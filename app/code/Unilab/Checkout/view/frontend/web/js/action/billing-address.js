define(
    [
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Checkout/js/checkout-data',
    ],
    function (ko, $, Component, addressList, fullScreenLoader, customerData, quote, selectPaymentMethod, checkoutData) {
       'use strict';

       var countryData = customerData.get('directory-data');
 
       return Component.extend({
		   
            initialize: function () {
                
               	fullScreenLoader.startLoader();
                
                this._super();
                this.customerAddresses = ko.observableArray(addressList());
				this.selectedAddress = ko.observable(null);
				this.billingSet = ko.observable(false)
                
                var defaultBillingAddress = ko.utils.arrayFirst(this.customerAddresses(), function(item) {
                    return item.isDefaultBilling() === true;
                });
				var self = this;
				
				this.selectedAddress.subscribe(function(address) {
                    quote.billingAddress(address);
                    quote.shippingAddress(address);
					self.billingSet(true);
					
                });
				
                if(defaultBillingAddress) {
					this.selectedAddress(defaultBillingAddress);
				}
                else {
                    this.selectedAddress(this.customerAddresses()[0]);
				}
                
                var paymentData = {
                    method: window.paymentMethod,
                    po_number: null,
                    additional_data: null
                };
                selectPaymentMethod(paymentData);
                checkoutData.setSelectedPaymentMethod(paymentData);
				
				$(document).ajaxStop(function() {
				  	fullScreenLoader.stopLoader();
				});

                

            },

            getCountryName: function(countryId) {
                return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
            }
          
       });
   }
);