/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'Unilab_Checkout/js/view/default',
	'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            // template: 'Magento_OfflinePayments/payment/cashondelivery'
        },

        /**
         * Returns payment method instructions.
         *
         * @return {*}
         */
        getCode: function() {
            return 'cashondelivery';
        },
		isAvailable: function() {
			if(quote.shippingMethod()) {
				if(quote.shippingMethod().carrier_code == 'minimumordervalue') {
					return true;
				} else {
					return false;
				}
			}
		},
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions[this.item.method];
        }
    });
});
