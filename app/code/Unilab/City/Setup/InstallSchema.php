<?php

namespace Unilab\City\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('unilab_cities')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('unilab_cities')
			)
				->addColumn(
					'city_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'City ID'
                )
                ->addColumn(
					'name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'City Name'
                )
                ->addColumn(
					'country_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					2,
					['nullable => false'],
					'Country ID'
                )
                ->addColumn(
					'region_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					6,
					['nullable => false'],
					'Region ID'
                )
                ->addColumn(
					'region_code',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					2,
					['nullable => false'],
					'Region Code'
				)
				->setComment('Unilab Cities');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}