<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilab\Reports\Setup;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Init
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /*
         * Report Event Types default data
         */
        $data = [
            [
			'report_id'         => 1,
            'sql_query'         => "SELECT MONTHNAME(STR_TO_DATE(MONTH(created_at), '%m')) 'Month',COUNT(entity_id)  'Total Orders', SUM(base_subtotal_incl_tax) 'Total Amount' FROM sales_order GROUP BY MONTH(created_at) ORDER BY created_at DESC",
			'title'             => "Monthly Summary Report",
			'created_at'        => date('Y-m-d h:i:s')
            ],

            [
                'report_id'         => 2,
                'sql_query'         => "SELECT sales_order_address.country_id AS Country, sales_order_address.city AS City, Count(sales_order.entity_id) AS `Total Orders`, CONCAT('₱ ',FORMAT(SUM(base_subtotal_incl_tax),2)) AS `Total Amount` FROM sales_order LEFT JOIN sales_order_address ON sales_order.billing_address_id = sales_order_address.entity_id GROUP BY sales_order_address.city ORDER BY sales_order_address.city",
                'title'             => "Country and City",
                'created_at'        => date('Y-m-d h:i:s')
            ],
            [
                'report_id'         => 3,
                'sql_query'         => "SELECT CONCAT((SELECT value from customer_entity_varchar where attribute_id=5 AND entity_id = sales_order.customer_id),' ',(SELECT value from customer_entity_varchar where attribute_id=7 AND entity_id = sales_order.customer_id)) AS Customer, Count(sales_order.entity_id) 'Total Orders', Sum(sales_order.base_subtotal_incl_tax) 'Total Amount' FROM sales_order GROUP BY sales_order.customer_id ORDER BY Customer",
                'title'             => "Customer's Name",
                'created_at'        => date('Y-m-d h:i:s')
            ],
            [
                'report_id'         => 4,
                'sql_query'         => "SELECT sales_order_item.sku AS `Item Code`,sales_order_item.`name` AS `Item Name`,SUM(FORMAT(sales_order_item.qty_ordered,2)) AS Quantity,CONCAT('₱ ',FORMAT(SUM(sales_order_item.qty_ordered * sales_order_item.base_price ),2)) AS `Total Amount` FROM sales_order INNER JOIN sales_order_item ON sales_order.entity_id = sales_order_item.order_id GROUP BY sku ORDER BY sku ASC",
                'title'             => "Products",
                'created_at'        => date('Y-m-d h:i:s')
            ],
            [
                'report_id'         => 5,
                'sql_query'         => "SELECT CASE(sales_order.customer_gender) WHEN 1 THEN 'Male' WHEN 2 THEN 'Female' END AS 'Gender', COUNT(sales_order.entity_id) AS `Count of Order Number`, SUM(base_subtotal) AS `Sum of Order Amount` FROM sales_order GROUP BY sales_order.customer_gender ORDER BY created_at DESC",
                'title'             => "Gender",
                'created_at'        => date('Y-m-d h:i:s')
            ],
            [
			'report_id'         => 6,
            'sql_query'         => "SELECT CASE sales_order.store_id WHEN 1 THEN 'ActiveoneRX' END AS `Store Name`,sales_order.entity_id AS `Order ID`, sales_order.increment_id AS `Order Number`, CONCAT('₱ ',FORMAT(grand_total,2)) AS `Order Total`, CONCAT('₱ ',FORMAT((base_subtotal + base_tax_amount) ,2)) AS `Order Amount`, CONCAT('₱ ',FORMAT(sales_order.base_shipping_amount,2)) AS `Order Shipping`, rra_tender_type.tender_name AS `Tender Type`, CASE WHEN sales_order.`status` IN ('processing' , 'complete', 'in_transit') THEN 'Paid' ELSE 'Unpaid' END 'Payment Status', DATE_FORMAT(created_at,'%d-%M') AS `Date Ordered`, DATE_FORMAT(updated_at,'%d-%M') AS `Date Updated`, DATE_FORMAT(created_at,'%M') AS `Month`, sales_order.unilab_waybill_number AS `Waybill Number`, customer_group.customer_group_code AS `Company`, CONCAT(customer_firstname,' ',customer_lastname) AS `Shopper's Name`, IF(sales_order.customer_gender = 1, 'Male', 'Female') AS Gender, sales_order_address.country_id AS Country, sales_order_address.street AS `Address`, sales_order_address.city AS City, DATE_FORMAT(created_at,'%Y') AS `Year` FROM sales_order INNER JOIN customer_group ON customer_group.customer_group_id = sales_order.customer_group_id INNER JOIN sales_order_address ON sales_order.entity_id = sales_order_address.parent_id INNER JOIN customer_entity_varchar ON sales_order.customer_id = customer_entity_varchar.entity_id INNER JOIN sales_order_payment ON sales_order.entity_id = sales_order_payment.parent_id INNER JOIN rra_tender_type ON sales_order_payment.method = rra_tender_type.paymentmethod_code GROUP BY increment_id ORDER BY created_at ASC",
			'title'             => "Order Summary",
			'created_at'        => date('Y-m-d h:i:s')
            ],
            [
			'report_id'         => 7,
            'sql_query'         => "SELECT CASE(sales_order.store_id) WHEN 1 THEN 'ActiveoneRX' END `Store Name`, sales_order_item.order_id, sales_order_item.sku AS `Item Code`, sales_order_item.`name` AS `Item Name`, IF(sales_order_item.prescription_id IS NULL, 'Non-Rx', 'Rx') AS Type, CONCAT(FORMAT(qty_ordered,2)) AS Quantity, CONCAT('₱ ',FORMAT(price_incl_tax,2)) AS `Item Price`, CONCAT('₱ ',FORMAT(row_total_incl_tax,2)) AS `Order Amount`, sales_order.increment_id AS `Order Number`, CONCAT('₱ ',FORMAT(base_shipping_amount,2)) AS `Shipping Rate`, CASE WHEN sales_order.`status` IN ('processing' , 'complete', 'in_transit') THEN 'Paid' ELSE 'Unpaid' END 'Payment Status', DATE_FORMAT(sales_order_item.created_at,'%m/%d/%Y') AS `Order Date`, DATE_FORMAT(sales_order_item.created_at,'%M') AS `Month`, CONCAT(sales_order.customer_firstname,' ',sales_order.customer_lastname) AS `Shopper's Name`, IF(sales_order.customer_gender = 1, 'Male', 'Female') AS Gender, sales_order_address.country_id AS Country, sales_order_address.street AS Address, sales_order_address.city AS City, DATE_FORMAT(sales_order_item.created_at,'%Y') AS `Year`,customer_group.customer_group_code AS Company FROM sales_order_item INNER JOIN sales_order ON sales_order_item.order_id = sales_order.entity_id LEFT JOIN sales_order_address ON sales_order_address.entity_id = sales_order.billing_address_id LEFT JOIN customer_group ON customer_group.customer_group_id = sales_order.customer_group_id GROUP BY item_id ORDER BY order_id DESC",
			'title'             => "Order Details",
			'created_at'        => date('Y-m-d h:i:s')
            ]
        ];
        foreach ($data as $row) {
            $setup->getConnection()
                ->insertForce($setup->getTable('cleansql_report'), $row);
        }
       
        /**
         * Prepare database after data upgrade
         */
        $setup->endSetup();

    
    }
}
