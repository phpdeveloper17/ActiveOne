<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\OfflinePayments\Model;

/**
 * Cash on delivery payment method model
 *
 * @method \Magento\Quote\Api\Data\PaymentMethodExtensionInterface getExtensionAttributes()
 *
 * @api
 * @since 100.0.2
 */
class Cashondelivery extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_CASHONDELIVERY_CODE = 'cashondelivery';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CASHONDELIVERY_CODE;

    /**
     * Cash On Delivery payment block paths
     *
     * @var string
     */

    protected $_canSaveCc 				= true;
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;		// Auth Only
    protected $_canCapture              = true;	    // Sale, Capture
    protected $_canCapturePartial       = true;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;     // Creation of a transaction from the admin panel
    protected $_canUseCheckout          = true;
	protected $_canFetchTransactionInfo = true;
    protected $_formBlockType = \Magento\OfflinePayments\Block\Form\Cashondelivery::class;

    /**
     * Info instructions block path
     *
     * @var string
     */
    protected $_infoBlockType = \Magento\Payment\Block\Info\Instructions::class;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }
}
