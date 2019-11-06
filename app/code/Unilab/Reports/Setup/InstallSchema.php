<?php

namespace Unilab\Reports\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('cleansql_report')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('cleansql_report')
			)
				->addColumn(
					'report_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					[
						'auto_increment' => true,
                        'nullable' => false,
                        'primary' => true,
					],
					'Report ID'
				)
				->addColumn(
					'sql_query',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'100M',
					['nullable' => true],
					'SQL Queries'
				)
				->addColumn(
					'title',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => false],
					'Title'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					50,
					['nullable' => false],
					'Created At'
				)
                ->setComment('For Unilab Custom Reports')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}