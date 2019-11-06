<?php
namespace Unilab\Customers\Setup;
 
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
 
 
class UpgradeData implements UpgradeDataInterface {
 
    public function __construct(CustomerSetupFactory $customerSetupFactory,EavSetupFactory $eavSetupFactory,AttributeSetFactory $attributeSetFactory,\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->resourceConnection = $resourceConnection;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        
        $installer = $setup;

        $installer->startSetup();
 
        if (version_compare($context->getVersion(), '1.0.1') < 0)  {
            
            $dataArray = [
                'agree_on_terms' => [
                    "type" => "int",
                    "label" => "Agree on Terms",
                    "input" => "select",
                    "sort_order" => 1009,
                    "position" => 1009,
                    "source" => 'Unilab\Customers\Model\Customer\Attribute\Source\YesNo',
                ]
            ];

            foreach ($dataArray as $key => $data) {
            
                $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
                // $eavSetup->removeAttribute(Customer::ENTITY, $key);
    
                $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
    
                $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
                $attributeSetId = $customerEntity->getDefaultAttributeSetId();
    
                /** @var $attributeSet AttributeSet */
                $attributeSet = $this->attributeSetFactory->create();
                $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
    
                $addAttr = array(
                    'type' => $data['type'],
                    'label' => $data['label'],
                    'input' => $data['input'],
                    'required' => false,
                    'visible' => true,
                    'user_defined' => true,
                    'sort_order' => $data['sort_order'],
                    'position' => $data['position'],
                    'system' => 0
                );
    
                if($data['input'] == 'select'){
    
                    $addAttr['source'] = $data['source'];
                    
                }
    
                $customerSetup->addAttribute(Customer::ENTITY, $key, $addAttr);
    
                $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $key)
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer'],
                    'is_used_in_grid' => 1,
                    'is_visible_in_grid' => 1,
                    'is_filterable_in_grid' => 1,
                    'is_searchable_in_grid' => 1,
    
                ]);
    
                $attribute->save();
            }
    
        }

        $installer->endSetup();
    }
}