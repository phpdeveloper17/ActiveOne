<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Model\Adminhtml\Source;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class PaymentAction
 */
class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Authorize')
            ]
        ];
    }
}
