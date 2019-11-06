<?php
namespace Unilab\Prescription\Setup;

use Magento\Eav\Setup\EavSetup; 
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;

class UpgradeData implements UpgradeDataInterface
{
 	private $eavSetupFactory;
	private $attributeSetFactory;
	private $attributeSet;
    private $categorySetupFactory;
    protected $logger;
    protected $attributeSetRepository;
    protected $_attributeSetCollection;

   	public function __construct(
           EavSetupFactory $eavSetupFactory,
           AttributeSetFactory $attributeSetFactory,
           CategorySetupFactory $categorySetupFactory,
           \Psr\Log\LoggerInterface $logger,
           \Magento\Catalog\Api\AttributeSetRepositoryInterface $attributeSetRepository,
           CollectionFactory $attributeSetCollection
           )
    	{
        	$this->eavSetupFactory = $eavSetupFactory; 
        	$this->attributeSetFactory = $attributeSetFactory; 
            $this->categorySetupFactory = $categorySetupFactory; 
            $this->logger = $logger;
            $this->attributeSetRepository = $attributeSetRepository;
            $this->_attributeSetCollection = $attributeSetCollection;
    	} 
	
 	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
 	{
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '1.0.0') < 0) {

            

            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $attributeSetFactory = $this->attributeSetFactory->create();
            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
            $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);

            //$this->logger->log('600', print_r($attributeSetIds, true));
            // foreach($attributeSetIds as $asid) {
            //     if($asid > 4){ //if not default
            //         $this->attributeSetRepository->deleteById($asid);
            //     }
            // }

            $newAttributeSets = [
                array(
                    'attribute_set_name' => 'OTC', 
                    'entity_type_id' => $entityTypeId
                ),
                array(
                    'attribute_set_name' => 'OTC-NOSIZE', 
                    'entity_type_id' => $entityTypeId
                ),
                array(
                    'attribute_set_name' => 'RX', 
                    'entity_type_id' => $entityTypeId
                ),
                array(
                    'attribute_set_name' => 'RX-NOSIZE', 
                    'entity_type_id' => $entityTypeId
                )
            ];
            
            $attributeSets = [];

            foreach($newAttributeSets as $newAttributeSet){

                $attributeSets[] = array(
                    'name' => $newAttributeSet['attribute_set_name'],
                    'is_rx' => substr_count($newAttributeSet['attribute_set_name'], 'RX') > 0 ? true : false,
                    
                );
                
                $attributeSetFactory->setData($newAttributeSet);
                $attributeSetFactory->validate();
                $attributeSetFactory->save();
                $attributeSetFactory->initFromSkeleton($attributeSetId);
                $attributeSetFactory->save();
            }

            $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);

           

            foreach($attributeSetIds as $asid) {
                if($asid > 4){ 
                    $eavSetup->addAttributeGroup($entityTypeId, $asid, 'Product Attributes', null);
                }
                
            }
            
            foreach($attributeSets as $attributeSet){
                
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'unilab_rx',
                    [
                        'type' => 'int',
                        'label' => 'RX',
                        'input' => 'boolean',
                        'user_defined' => true,
                        'required' => false,
                        'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                        'attribute_set_id' => $attributeSet['name'],
                        'default' => $attributeSet['is_rx'],
                        'group' => 'Product Attributes',
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
                    ]
                );
                
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'unilab_dc',
                    [
                        'type' => 'int',
                        'label' => 'For Digital Couponing',
                        'input' => 'boolean',
                        'user_defined' => true,
                        'required' => false,
                        'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                        'attribute_set_id' => $attributeSet['name'],
                        'default' => $attributeSet['is_rx'],
                        'group' => 'Product Attributes',
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
                    ]
                );

                
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'unilab_moq',
                    [
                        'type' => 'varchar',
                        'label' => 'MOQ',
                        'input' => 'text',
                        'required' => true,
                        'user_defined' => true,
                        'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                        'attribute_set_id' => $attributeSet['name'],
                        'default' => 1,
                        'group' => 'Product Attributes',
                        'frontend_class' => 'validate-digits'
                    ]
                );
            }

            


            // $groupName = 'Product Attributes'; /* Label of your group*/
            
           

            // foreach($attributeSetIds as $asid) {
                

            //     // Add existing attribute to group
            //     $attributeId = $eavSetup->getAttributeId($entityTypeId, 'show_price');
            //     $eavSetup->addAttributeToGroup($entityTypeId, $asid, $attributeGroupId, $attributeId, null);
            // }

           
            
            
        }

		$setup->endSetup();
    }
    
    public function getAttributeSetId($attributeSetName)
    {
        $attributeSetCollection = $this->_attributeSetCollection->create()
        ->addFieldToSelect('attribute_set_id')
        ->addFieldToFilter('attribute_set_name', $attributeSetName)
        ->getFirstItem()
        ->toArray();

        $attributeSetId = (int) $attributeSetCollection['attribute_set_id'];
        // OR (see benchmark below for make your choice)
        // $attributeSetId = (int) implode($attributeSetCollection);

        return $attributeSetId;
    }
	
} ?>