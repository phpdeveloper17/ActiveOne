<?php
/**
 * BpiSecurepay View XML.
 * @category  Unilab
 * @package   BpiSecurepay
 * @author    Kristian Claridad
 */
namespace Unilab\BpiSecurepay\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
// use Unilab\BpiSecurepay\Gateway\Http\Client\ClientMock;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'bpisecurepay';

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'transactionResults' => [
                        __('Success'),
                        __('Fraud')
                        // ClientMock::SUCCESS => __('Success'),
                        // ClientMock::FAILURE => __('Fraud')
                    ]
                ]
            ]
        ];
    }
}
