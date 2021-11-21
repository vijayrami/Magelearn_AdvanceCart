define([
    'ko',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function(ko, Component, customerData, $t) {
    'use strict';

    var cartData = customerData.get('cart')();
    return Component.extend({
        /**
        * @return {Boolean}
        */
        isVisible: function(){
            if(Object.keys(customerData.get('cart')()).length > 0){
                var cartData = customerData.get('cart')();
                return ko.observable(Boolean(cartData.advancecart.totals_enable));
            }
            return ko.observable(false);
        },
        /**
        * @return array
        */
        getTotals: function () {
            if(Object.keys(customerData.get('cart')()).length > 0){
                var cartData = customerData.get('cart')();
                return cartData.advancecart.totals;
            }
            return {};
        },
        /**
        * @return {String}
        */
        getTotalsTitle: function () {
            if(Object.keys(customerData.get('cart')()).length > 0){
                var cartData = customerData.get('cart')();
                return $t(cartData.advancecart.totals_title);
            }
            return '';
        }
    });
});