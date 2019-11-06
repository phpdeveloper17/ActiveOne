<?php

namespace Unilab\Afptc\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('aw_afptc_rules')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('aw_afptc_rules')
			)
				->addColumn(
					'rule_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					[
						'auto_increment' => true,
						'nullable' => false,
						'primary' => true,
					],
					'Rule ID'
				)
				->addColumn(
					'name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255',
					['nullable' => true],
					'Name'
				)
				->addColumn(
					'description',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255M',
					[],
					'DESCRIPTION'
				)
				->addColumn(
					'status',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'2',
					[],
					'STATUS'
				)
				->addColumn(
					'store_ids',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'50',
					['nullable' => true],
					'STORE IDS'
				)
				
				->addColumn(
					'customer_groups',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255M',
					['nullable' => true],
                    'Customer Groups'
				)
				->addColumn(
					'discount',
					\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'10,2',
					[],
					'DISCOUNT'
				)
				->addColumn(
					'priority',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'10',
					[],
					'priority'
				)
				->addColumn(
					'simple_action',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'10',
					[],
					'simple_action'
				)
			
				->addColumn(
					'discount_step',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'5',
					[],
					'discount_step'
				)
				->addColumn(
					'n_product',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'5',
					['nullable' => false, 'default' => 0],
					'n_product'
				)
				->addColumn(
					'x_product',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'5',
					['nullable' => false, 'default' => 0],
					'x_product'
				)
				->addColumn(
					'x_autoincfreeprod',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'5',
					['nullable' => false, 'default' => 0],
					'x_autoincfreeprod'
				)
				->addColumn(
					'auto_incfreeprod',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'2',
					[],
					'auto_incfreeprod'
				)
				->addColumn(
					'show_popup',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'2',
					[],
					'show_popup'
				)
				->addColumn(
					'show_once',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'2',
					[],
					'show_once'
				)
				->addColumn(
					'free_shipping',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'2',
					[],
					'free_shipping'
				)
				->addColumn(
					'product_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'20',
					[],
					'product_id'
				)
				->addColumn(
					'conditions_serialized',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255M',
					[],
					'conditions_serialized'
				)
				->addColumn(
					'start_date',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'50',
					[],
					'start_date'
				)
				->addColumn(
					'coupon_code',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255',
					[],
					'coupon_code'
				)
				->addColumn(
					'y_qty',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'20',
					[],
					'y_qty'
				)
				->addColumn(
					'auto_increment',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'20',
					[],
					'auto_increment'
				)
				->addColumn(
					'two_promoitem',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'20',
					[],
					'two_promoitem'
				)
				->addColumn(
					'end_date',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'10',
					[],
					'end_date'
				)
				->addColumn(
					'stop_rules_processing',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'10',
					[],
					'stop_rules_processing'
				)
                ->setComment('Add Free Product To Cart Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}