<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Pricelist\Controller\Adminhtml\Promo;

use Magento\Backend\App\Action;

abstract class Widget extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Unilab_Pricelist::promo_catalog';
}
