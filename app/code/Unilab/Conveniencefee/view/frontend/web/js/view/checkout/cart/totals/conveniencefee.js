/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Unilab_Conveniencefee/js/view/checkout/summary/conveniencefee'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            /**
             * @override
             */
            isDisplayed: function () {
                return true;
            }
        });
    }
);