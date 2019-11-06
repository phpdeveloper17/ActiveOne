<?php

namespace Unilab\Movshipping\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('unilab_mov_shipping')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('unilab_mov_shipping')
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
					'group_name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					100,
					['nullable' => true],
					'Group Name'
				)
				->addColumn(
					'listofcities',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'100M',
					['nullable' => false],
					'list of city within group'
				)
				->addColumn(
					'greaterequal_mov',
					\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'10,2',
					[],
					'Greater Equal MOV'
				)
				->addColumn(
					'lessthan_mov',
					\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
					'10,2',
					[],
					'lessthan MOV'
				)
                ->setComment('Mov Shipping Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}