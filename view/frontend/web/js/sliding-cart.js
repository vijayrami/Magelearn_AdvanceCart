define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function(ko, Component, customerData, $t) {
    'use strict';

    return Component.extend({
        /**
        * @return {Boolean}
        */
         isTotalsEnabled: function () {
            if(Object.keys(customerData.get('cart')()).length > 0){
                var cartData = customerData.get('cart')();
                return ko.observable(Boolean(cartData.advancecart.totals_enable));
            }
            return ko.observable(false);
        }
    });
});