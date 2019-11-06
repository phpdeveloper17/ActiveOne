<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Sales\Controller\Adminhtml\Order;

class Index extends \Unilab\Sales\Controller\Adminhtml\Order
{
    /**
     * Orders grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Orders'));
        $this->_eventTrigger('adminhtml_sales_orders_view');
        return $resultPage;
    }
}
