<?php
namespace Unilab\Customers\Setup;


use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
// use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;



class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;
    private $attributeSetFactory;
    private $eavSetupFactory;
    protected $resourceConnection;
    
    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(CustomerSetupFactory $customerSetupFactory,EavSetupFactory $eavSetupFactory,AttributeSetFactory $attributeSetFactory,\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->resourceConnection = $resourceConnection;
    }
    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        

        $installer = $setup;
        $installer->startSetup();

        $insertArray = array(
            'employee_id' => array(
                "type" => "varchar",
                "label" => "Employee ID",
                "input" => "text",
                "sort_order" => 1000,
                "position" => 1000
            ),
            'date_hired' => array(
                "type" => "datetime",
                "label" => "Date Hired",
                "input" => "date",
                "sort_order" => 1001,
                "position" => 1001
            ),
            'contact_number' => array(
                "type" => "varchar",
                "label" => "Contact Number",
                "input" => "text",
                "sort_order" => 1002,
                "position" => 1002
            ),
            'price_level' => array(
                "type" => "int",
                "label" => "Price Level",
                "input" => "select",
                "sort_order" => 1003,
                "position" => 1003,
                "source" => 'Unilab\Customers\Model\Customer\Attribute\Source\PriceLevel'
            ),
            'date_registered' => array(
                "type" => "datetime",
                "label" => "Date Registered",
                "input" => "date",
                "sort_order" => 1004,
                "position" => 1004
            ),
            'civil_status' => array(
                "type" => "text",
                "label" => "Civil Status",
                "input" => "text",
                "sort_order" => 1005,
                "position" => 1005
            ),
            'accept_updated_privacy' => array(
                "type" => "int",
                "label" => "Accept Updated Terms",
                "input" => "select",
                "sort_order" => 1006,
                "position" => 1006,
                "source" => 'Unilab\Customers\Model\Customer\Attribute\Source\YesNo',
                // "options" => array(
                //     0 => 'No',
                //     1 => 'Yes'
                // )
            ),
            'employment_status' => array(
                "type" => "int",
                "label" => "Employment Status",
                "input" => "select",
                "sort_order" => 1007,
                "position" => 1007,
                "source" => 'Unilab\Customers\Model\Customer\Attribute\Source\EmploymentStatus',
                // "options" => array(
                //     0 => 'Resigned',
                //     1 => 'Active'
                // )
            ),
            'active' => array(
                "type" => "int",
                "label" => "Active",
                "input" => "select",
                "sort_order" => 1008,
                "position" => 1008,
                "source" => 'Unilab\Customers\Model\Customer\Attribute\Source\YesNo',
                // "options" => array(
                //     0 => 'No',
                //     1 => 'Yes'
                // )
            ),
            'agree_on_terms' => [
                "type" => "int",
                "label" => "Agree on Terms",
                "input" => "select",
                "sort_order" => 1009,
                "position" => 1009,
                "source" => 'Unilab\Customers\Model\Customer\Attribute\Source\YesNo',
            ]
        );

        foreach ($insertArray as $key => $data) {
            
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

                // $options = array();

                // if($key == 'price_level'){
                //     $connectdb = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

                //     $readresult = $connectdb->query("SELECT * FROM rra_pricelevelmaster");

                //     while ($items = $readresult->fetch() ) {
                //         $options[$items['id']] = $items['price_level_id'];
                //     }
                // }else{
                //     $addAttr['default'] = '0';
                //     $options = $data['options'];
                // }

                // $addAttr['option'] = array(
                //     'values' => $options
                // );

                
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


        $installer->endSetup();
    }
}