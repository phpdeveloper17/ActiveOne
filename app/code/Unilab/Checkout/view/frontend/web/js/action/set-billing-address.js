define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/get-payment-information'
    ],
    function ($,
              quote,
              urlBuilder,
              storage,
              errorProcessor,
              customer,
              fullScreenLoader,
              getPaymentInformationAction
    ) {
        'use strict';

        return {
            setBillingAddress : function (messageContainer) {

                var serviceUrl,
                    payload;

                if (!customer.isLoggedIn()) {
                    serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/billing-address', {
                        cartId: quote.getQuoteId()
                    });
                    payload = {
                        cartId: quote.getQuoteId(),
                        address: quote.billingAddress()
                    };
                } else {
                    serviceUrl = urlBuilder.createUrl('/carts/mine/billing-address', {});
                    payload = {
                        cartId: quote.getQuoteId(),
                        address: quote.billingAddress()
                    };
                }
    
    
                return storage.post(
                    serviceUrl, JSON.stringify(payload)
                ).done(
                    function () {
                        var deferred = $.Deferred();
    
                        getPaymentInformationAction(deferred);
                        $.when(deferred).done(function () {
                            fullScreenLoader.stopLoader();
                        });
                        
                        $('#submit_btn').prop('disabled', false);
                        $('#confirm').prop('disabled', false);
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response, messageContainer);
                        fullScreenLoader.stopLoader();
                    }
                );
            }
        }
    }
);