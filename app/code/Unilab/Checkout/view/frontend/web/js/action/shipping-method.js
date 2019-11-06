define(
    [
    'ko',
    'underscore',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Unilab_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/checkout-data',
    'Unilab_Checkout/js/action/set-billing-address',
    'Magento_Catalog/js/price-utils',
    'jquery',
	'Unilab_Checkout/js/action/billing-address',
	'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        ko, 
        _, 
        Component, 
        quote, 
        setShippingInfoAction, 
        shippingService, 
        rateRegistry, 
        customerAddressProcessor, 
        newAddressProcessor,
        selectShippingMethodAction,
        checkoutData,
        setBillingAddressAction,
        priceUtils,
        $,
		billingAction,
		fullScreenLoader
    ) {
       'use strict';
        
        var selectedShippingMethod = ko.observable();
        var defaultShipping = window.defaultShipping;
		

        return Component.extend({
            shippingRates: shippingService.getShippingRates(),
            shippingRateGroups: ko.observableArray([]),
            initialize: function () {

                this._super();
				this.methodSelected = ko.observable(null);
				if(billingAction().billingSet()) {
					var address = quote.shippingAddress();
                
                    // clearing cached rates to retrieve new ones
                    rateRegistry.set(address.getCacheKey(), null);

                    var type = quote.shippingAddress().getType();
                    if (type) {
                        customerAddressProcessor.getRates(address);
                    } else {
                        newAddressProcessor.getRates(address);
                    }
					
				}
				var self = this;
				this.methodSelected.subscribe(function(data) {
			   		if(data === false) {
						self.shippingRates({});
						$('.loading-mask').css('display','none');
					}
			   	});
                this.shippingRates.subscribe(function (rates) {
					if(rates) {
						var methodSelected = false;
						_.each(rates, function(rate) {
							var shippingMethod = rate['carrier_code'] + '_' + rate['carrier_code'];
							if(defaultShipping == shippingMethod) {
								selectedShippingMethod(shippingMethod);
								methodSelected = true;
								
							} 
						});
						self.methodSelected(methodSelected);
					}
                    
                }); 
               
            },
            rates: function() {
                return this.shippingRates;
            },

            selectedShippingMethod: function(methodData) {
				
                if (methodData['carrier_code'] + '_' + methodData['carrier_code'] == defaultShipping ) {
					
                    selectShippingMethodAction(methodData);
                    checkoutData.setSelectedShippingRate(methodData['carrier_code'] + '_' + methodData['carrier_code']);
                    setBillingAddressAction.setBillingAddress();
					setShippingInfoAction.saveShippingInformation();

                    $('.shipping_method').parent('li').css('display','none');
                    $('.shipping_method:checked').parent('li').css('display','block');
                    $('.shipping-info').text(methodData['carrier_title']+'-'+methodData['method_title']);
                    $('.shipping-info-value').text(this.getFormattedPrice(methodData['amount']));
					
                }
                return selectedShippingMethod;
            },
            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            }

       });
   }
);