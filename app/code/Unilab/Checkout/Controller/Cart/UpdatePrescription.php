<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Checkout\Controller\Cart;

class UpdatePresciprtion extends \Magento\Checkout\Controller\Cart
{
   
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        var_dump($this->getRequest()->getPost());
        die();
        return $this->_goBack();
    }
}
