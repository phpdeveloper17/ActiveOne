<?php

namespace Unilab\Pricelist\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('wspi_pricelist')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('wspi_pricelist')
			)
				->addColumn(
					'price_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					60,
					['nullable' => true],
					'Price ID'
				)
				->addColumn(
					'name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255',
					['nullable' => true],
					'Name'
				)
				->addColumn(
					'company',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255',
					[],
					'Company'
				)
				->addColumn(
					'price_level_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255',
					[],
					'Price Level ID'
				)
				->addColumn(
					'from_date',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'50',
					['nullable' => true],
					'From Date'
				)
				
				->addColumn(
					'to_date',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'50',
					['nullable' => true],
                    'To Date'
				)
				->addColumn(
					'limited_days',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'100',
					[],
					'Limited Days'
				)
				->addColumn(
					'limited_time_from',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'100',
					[],
					'Limited Time From'
				)
				->addColumn(
					'limited_time_to',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'100',
					[],
					'Limited Time To'
				)
				->addColumn(
					'active',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'50',
					[],
					'Active'
				)
				->addColumn(
					'uploaded_by',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255',
					[],
					'Uploaded By'
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
                ->setComment('Pricelist Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}