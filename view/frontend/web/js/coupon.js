define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function (ko, $, Component, customerData, $t) {
    'use strict';

    var guestUrl    = 'guest-carts/:cartId/coupons/:couponCode',
        customerUrl = 'carts/mine/coupons/:couponCode';

    var cartData = customerData.get('cart')();

    return Component.extend({
        /**
        * @return {Boolean}
        */
        isVisible: function(){
            if(Object.keys(customerData.get('cart')()).length > 0){
                var cartData = customerData.get('cart')();
                return ko.observable(Boolean(cartData.advancecart.coupon_enable));
            }
            return ko.observable(false);
        },
        couponCode: ko.observable(),
        isApplied: ko.observable(),
        quoteId: ko.observable(),
        isLoggedIn: ko.observable(),
        apiUrl: ko.observable(),
        errorMsg: ko.observable(),
        successMsg: ko.observable(),
        hideMsgTimeout: null,

        initialize: function () {
            this._super();
            this.initAdvanceCartData(customerData.get('cart')());
            return this;
        },

        initObservable: function () {
            var self = this;

            this._super();

            customerData.get('cart').subscribe(function (cartData) {
                self.initAdvanceCartData(cartData);
            });

            return this;
        },

        initAdvanceCartData: function (cartData) {
            if (cartData.hasOwnProperty('advancecart')) {
                this.couponCode(cartData.advancecart.coupon_code);
                this.isApplied(!!cartData.advancecart.coupon_code);
                this.isLoggedIn(cartData.advancecart.isLoggedIn);
                this.quoteId(cartData.advancecart.quoteId);
                this.apiUrl(cartData.advancecart.apiUrl);
            }
        },

        handleMsg: function (type) {
            $('#slidingcart-coupon-form .message-' + type).show();

            this.hideMsgTimeout = setTimeout(function () {
                $('#slidingcart-coupon-form .message-' + type).hide('blind', {}, 500);
            }, 3000);
        },

        apply: function () {
            var self  = this,
                field = $('#slidingcart-coupon-code');

            clearTimeout(this.hideMsgTimeout);

            if (!this.couponCode()) {
                field.focus().trigger('focusin');
                field.css('border-color', '#ed8380');

                return;
            }

            field.css('border-color', '');

            $.ajax({
                method: 'put',
                contentType: 'application/json',
                showLoader: true,
                url: this.buildUrl(this.quoteId(), this.couponCode()),
                data: JSON.stringify({
                    quoteId: this.quoteId(),
                    couponCode: this.couponCode()
                }),
                success: function () {
                    var cartData = customerData.get('cart')();

                    cartData.couponCode = self.couponCode();
                    customerData.set('cart', cartData);

                    self.handleSuccessApply();
                    self.handleMsg('success');
                },
                error: function (response) {
                    self.handleErrorResponse(response);
                    self.handleMsg('error');
                }
            });
        },

        cancel: function () {
            var self = this;

            clearTimeout(this.hideMsgTimeout);

            $.ajax({
                method: 'delete',
                contentType: 'application/json',
                showLoader: true,
                url: this.buildUrl(this.quoteId(), ''),
                data: JSON.stringify({quoteId: this.quoteId()}),
                success: function () {
                    var cartData = customerData.get('cart')();

                    cartData.couponCode = '';
                    customerData.set('cart', cartData);

                    self.handleSuccessCancel();
                    self.handleMsg('success');
                },
                error: function (response) {
                    self.handleErrorResponse(response);
                    self.handleMsg('error');
                }
            });
        },

        handleSuccessApply: function () {
            customerData.reload(['cart'], false);

            this.successMsg($t('Your coupon was successfully applied.'));
        },

        handleSuccessCancel: function () {
            customerData.reload(['cart'], false);

            this.successMsg($t('Your coupon was successfully removed.'));
        },

        handleErrorResponse: function (response) {
            this.errorMsg(response.responseJSON ? response.responseJSON.message : '');
        },

        buildUrl: function (cartId, couponCode) {
            var url = guestUrl;

            if (this.isLoggedIn()) {
                url = customerUrl;
            }

            return this.apiUrl() + url.replace(':cartId', cartId).replace(':couponCode', couponCode);
        }
    });
});