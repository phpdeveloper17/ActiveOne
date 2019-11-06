<?php

namespace Unilab\Prescription\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('unilab_prescription')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('unilab_prescription')
			)
				->addColumn(
					'prescription_id',
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
					'customer_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					['nullable' => true],
					'Customer Id'
				)
				->addColumn(
					'date_prescribed',
					\Magento\Framework\DB\Ddl\Table::TYPE_DATE,
					null,
					['nullable' => true],
					'Date Prescribed'
                )
                ->addColumn(
					'patient_name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255',
					['nullable' => true],
					'Patient Name'
				)
				->addColumn(
					'ptr_no',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'150',
					['nullable' => true],
					'PTR No'
				)
				->addColumn(
					'doctor',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'150',
					['nullable' => true],
					'doctor'
                )
                ->addColumn(
					'clinic',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'150',
					['nullable' => true],
					'clinic'
                )
                ->addColumn(
					'clinic_address',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'500',
					['nullable' => true],
					'clinic address'
                )
                ->addColumn(
					'contact_number',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'60',
					['nullable' => true],
					'contact number'
                )
                ->addColumn(
					'expiry_date',
					\Magento\Framework\DB\Ddl\Table::TYPE_DATE,
					null,
					['nullable' => true],
                    'expiry date'
				)
				->addColumn(
					'consumed',
					'boolean',
					null,
					['nullable' => true],
					'Consumed'
				)
                ->addColumn(
					'remarks',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'65535',
					['nullable' => true],
					'remarks'
                )
                ->addColumn(
					'scanned_rx',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'65535',
					['nullable' => true],
					'scanned rx'
                )
                ->addColumn(
					'original_filename',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'255',
					['nullable' => true],
					'original filename'
                )
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
					'Created Date'
				)->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
					'Updated Date')
                ->setComment('Prescription Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
			$installer->getConnection()->createTable($table);
		}
		$installer->endSetup();
	}
}