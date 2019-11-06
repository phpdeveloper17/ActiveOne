<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Controller\Adminhtml\Group;

class Edit extends \Unilab\Customers\Controller\Adminhtml\Group
{
    /**
     * Edit customer group action. Forward to new action.
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('new');
    }
}
