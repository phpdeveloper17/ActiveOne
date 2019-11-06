<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\DragonPay\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'dragonpay';

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
