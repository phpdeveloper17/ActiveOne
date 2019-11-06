/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
     [
     'jquery',
     'Unilab_Checkout/js/view/default',
     // 'Unilab_BpiSecurepay/js/action/set-payment-method',
     'Magento_Checkout/js/model/full-screen-loader',
     'mage/url',
     ],
     function ($, Component, fullScreenLoader,url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Unilab_BpiSecurepay/payment/bpisecurepayform',
                transactionResult: ''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
            },

            getCode: function() {
                return 'bpisecurepay';
            },
            continueTobpisecurepay: function (lastOrderId) {
                fullScreenLoader.startLoader();
                this.redirectAfterPlaceOrder = false;
                this.placeOrder();// save order first then redirect to 3rd party payment gateway
                return false;
            },
            // /** Redirect to Genericclass */
            afterPlaceOrder: function (lastOrderId) {
                fullScreenLoader.startLoader();
                $.mage.cookies.set('lastOrderId', lastOrderId);
                $.mage.redirect(url.build('bpisecurepay/payment/redirect?ts='+ (new Date()).getTime()));
            }
           
        });
    }
);