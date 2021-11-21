define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function(ko, Component, customerData, $t) {
    'use strict';

    return Component.extend({
        /**
        * @return {String}
        */
         getGrandTotal: function () {
            if(Object.keys(customerData.get('cart')()).length > 0){
                var cartData = customerData.get('cart')();
                return cartData.advancecart.grand_total;
            }
            return '';
        }
    });
});