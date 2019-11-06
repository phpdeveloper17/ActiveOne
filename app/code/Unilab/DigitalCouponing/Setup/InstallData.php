<?php
/**
 * @author Reyson Auino
 * @copyright Copyright (c) 2018 Unilab, Inc. (https://www.unilab.com.ph/)
 * @package Unilab_DigitalCouponing
 */

namespace Unilab\DigitalCouponing\Setup;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
    /**
     * Resource Config
     *
     * @var Config
     */
    protected $resourceConfig;

    /**
     * Eav Setup Factory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * AddDefaultShippingMethodsService constructor
     *
     * @param Config $resourceConfig
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(Config $resourceConfig, EavSetupFactory $eavSetupFactory)
    {
        $this->resourceConfig = $resourceConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void

     * @throws CouldNotSaveException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        try {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'unilab_dc',
                [
                    'type' => 'int',/* Data type in which formate your value save in database*/
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Digital Couponing', /* lablel of your attribute*/
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                                    /* Source of your select type custom attribute options*/
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                                        /*Scope of your attribute */
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false
                ]
            );
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could create product attribute: "%1"', $e->getMessage()), $e);
        }

        $installer->endSetup();
    }
}
