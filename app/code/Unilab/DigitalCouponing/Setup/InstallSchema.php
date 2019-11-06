<?php

namespace Unilab\DigitalCouponing\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('unilab_dc_asciiequivalents')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('unilab_dc_asciiequivalents')
			)
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					[
						'auto_increment' => true,
                        'nullable' => false,
                        'primary' => true,
					],
					'ID'
				)
				->addColumn(
					'ascii_equivalent',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					5,
					['nullable' => false],
					'ASCII'
				)
				->addColumn(
					'letter',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					5,
					['nullable' => false],
					'Letter'
				)
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('unilab_dc_remainderequivs')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('unilab_dc_remainderequivs')
			)
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					[
						'auto_increment' => true,
                        'nullable' => false,
                        'primary' => true,
					],
					'ID'
				)
				->addColumn(
					'remainder_equivalent',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					5,
					['nullable' => false],
					'Remainder'
				)
				->addColumn(
					'letter',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					5,
					['nullable' => false],
					'Letter'
				)
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('unilab_dc_usedcoupon')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('unilab_dc_usedcoupon')
			)
				->addColumn(
					'id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					[
						'auto_increment' => true,
                        'nullable' => false,
                        'primary' => true,
					],
					'ID'
				)
				->addColumn(
					'couponcode',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					10,
					['nullable' => false],
					'Coupon'
				)
				->addColumn(
					'sku',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					10,
					['nullable' => false],
					'Letter'
                )
                ->addColumn(
					'customeremail',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					55,
					['nullable' => false],
					'Customer Email'
                )
                ->addColumn(
					'orderid',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable' => false],
					'Order Id'
                )
                ->addColumn(
					'created_datetime',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created DateTime'
				)
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
        }

            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'dc_applied',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'default' => 0,
					'size' => 1,
                    'comment' => 'Coupon Applied'
                ]
                );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'dc_coupon',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'size' => 10,
                    'comment' => 'Coupon code'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'dc_applied',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'default' => 0,
					'size' => 1,
                    'comment' => 'Coupon Applied'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'dc_coupon',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'size' => 10,
                    'comment' => 'Coupon code'
                ]
            );
		$installer->endSetup();
	}
}