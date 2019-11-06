
define(
    [
    'jquery',
    'Magento_Checkout/js/model/payment/renderer-list',
    ],
    function ($, renderList) {
       'use strict'; 

       return {
            setPaymentMethod: function (payment) {
                renderList.push(
                    {
                        type: payment['code'],
                        component: payment['component']
                    }
                );
           },

          
       };
   }
);