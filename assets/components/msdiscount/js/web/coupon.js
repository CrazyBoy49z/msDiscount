Coupon = {
    setup : function(elem){
        this.actcouponStatus = 'coupon/status';
        this.actCouponApply = 'coupon/apply';
        this.actCouponCancel = 'coupon/cancel';
        this.blk        = $('#msd_coupon');
        this.msCart     = $('#msCart');
        this.submit     = false;

        this.message    = elem.find('.help-block');
        this.input      = elem.find('input[name=coupon_code]');
        this.action     = elem.find('input[name=action]')
        this.btn        = elem.find('button');
        this.description = elem.find('#coupon_description');
        this.elem       = elem;
        this.$doc       = $(document);
        this.cl_error = 'has-error';
        this.cl_success = 'has-success';

        this.total_discount             = $('#total_discount');
        this.total_discount_all         = $('#total_discount_all');
        this.total_cost_full            = $('#total_cost_full');
        this.total_cost_old             = $('#total_cost_old');
        this.total_base_cost            = $('#total_base_cost');
        this.total_discount_sale        = $('#total_discount_sale');
        this.total_discount_bigsum      = $('#total_discount_bigsum');
        this.total_discount_coupone     = $('#total_discount_coupone');
        this.total_discount_bigsum_sum  = $('#total_discount_bigsum_sum');
        this.coupon_description         = $('#coupon_description');
    }

    ,initialize: function(selector) {

        if(!jQuery().ajaxForm) {
            document.write('<script src="'+CouponConfig.assetsUrl+'js/web/lib/jquery.form.min.js"><\/script>');
        }
        if(!jQuery().jGrowl) {
            document.write('<script src="'+CouponConfig.assetsUrl+'js/web/lib/jquery.jgrowl.min.js"><\/script>');
        }

        var elem = $(selector);
        if (!elem.length) {return false;}

        this.setup(elem);

        $(document).on('submit', selector, function(e) {
            e.preventDefault();
            Coupon.blk.removeClass(Coupon.cl_error);
            Coupon.blk.removeClass(Coupon.cl_success);
            var action      = Coupon.action.val();
            var coupon_code = Coupon.input.val();
            Coupon.submit = true;
            Coupon.elem.find('input, button, a').attr('disabled', true);
            Coupon.send({ action : action, coupon_code: coupon_code });
            return false;
        });


        /*
        *
        * response
        *
        * */
        Coupon.$doc.on('coupon.send.response',function(e,d,r){
            // default Coupon
            if(d.data.action == Coupon.actcouponStatus){
                Coupon.status(d.response.data.status);
                miniShop2.Cart.status(d.response.data.status);
            }

            // Coupon Apply
            if(d.data.action == Coupon.actCouponApply){
                if(Coupon.submit == true) {
                    Coupon.elem.find('input, button, a').attr('disabled', false);
                }

                if (d.response.success) {
                    Coupon.blk.addClass(Coupon.cl_success);
                    Coupon.message.html(d.response.message);
                    Coupon.editStatus(d.response);
                }
                else {
                    Coupon.blk.addClass(Coupon.cl_error);
                    Coupon.message.html(d.response.message);
                }

                if (d.response.data.refresh) {
                    document.location.href = d.response.data.refresh;
                }

            }
            // default Coupon
            if(d.data.action == Coupon.actCouponCancel){
                console.log(d.response);
                Coupon.editStatus(d.response);
            }
            // default Coupon
            if(d.data.action == Coupon.actcouponStatus){


                console.log(d.response);

            }
        });
    }
    ,editStatus: function() {
        Coupon.send({ action : Coupon.actcouponStatus});
    }
    ,resetStatus: function(data) {
        miniShop2.Cart.status(data);
    }
    ,status: function(response) {
        if(Coupon.submit == true){
            var action = Coupon.action.val();

            Coupon.message.html('');
            if(action == 'coupon/apply'){
                Coupon.btn.html(CouponConfig.btn_cancel);
                Coupon.action.val('coupon/cancel');
                Coupon.input.prop('disabled', true);
            }

            if(action == 'coupon/cancel'){
                Coupon.btn.html(CouponConfig.btn_apply);
                Coupon.input.prop('disabled', false);
                Coupon.btn.prop('disabled', false);
                Coupon.input.val('');
                Coupon.action.val('coupon/apply');
            }

            Coupon.submit = false;
        }
    }
    ,send : function(data){
        var e = $.Event('coupon.send.beforesend');
        var d = this.$doc.trigger(e,{data : data});
        $.post(CouponConfig.actionUrl,data,function(response){
            e = $.Event('coupon.send.response');
            Coupon.$doc.trigger(e,{ data : data, response : response});
        }, 'json');
    }
};
Coupon.initialize('#msdCouponeForm form');

miniShop2.Callbacks.Cart.change.ajax.done = function(res) {
    Coupon.editStatus(res);
}
miniShop2.Callbacks.Cart.clean.ajax.done = function(res) {
    Coupon.editStatus(res);
}
miniShop2.Callbacks.Cart.remove.ajax.done = function(res) {
    Coupon.editStatus(res);
    var action      = 'coupon/apply';
    var coupon_code = Coupon.input.val();
    Coupon.send({ action : action, coupon_code: coupon_code });

}
miniShop2.Callbacks.Cart.add.ajax.done = function(res) {
    Coupon.editStatus(res);
}
miniShop2.Cart.status = function(status) {
    if(status.key){
        return false;
    } else {

        if (status['total_count'] < 1) {
            location.reload();
        }
        else {
            var $cart = $(miniShop2.Cart.cart);
            var $miniCart = $(miniShop2.Cart.miniCart);
            if (status['total_count'] > 0 && !$miniCart.hasClass(miniShop2.Cart.miniCartNotEmptyClass)) {
                $miniCart.addClass(miniShop2.Cart.miniCartNotEmptyClass);
            }
            $(miniShop2.Cart.totalWeight).text(miniShop2.Utils.formatWeight(status['total_weight']));

            $(Coupon.total_discount).text(miniShop2.Utils.formatPrice(status['total_discount']));
            $(Coupon.total_discount_all).text(miniShop2.Utils.formatPrice(status['total_discount_all']));
            $(Coupon.total_discount_sale).text(miniShop2.Utils.formatPrice(status['total_discount_sale']));
            $(Coupon.total_cost_full).text(miniShop2.Utils.formatPrice(status['total_cost_full']));
            $(Coupon.total_cost_old).text(miniShop2.Utils.formatPrice(status['total_cost_old']));
            $(Coupon.total_base_cost).text(miniShop2.Utils.formatPrice(status['total_base_cost']));
            $(Coupon.total_discount_bigsum).text(miniShop2.Utils.formatPrice(status['total_discount_bigsum']));
            $(Coupon.total_discount_coupone).text(miniShop2.Utils.formatPrice(status['total_discount_coupone']));
            $(Coupon.total_discount_bigsum_sum).text(miniShop2.Utils.formatPrice(status['total_discount_bigsum_sum']));
            $(Coupon.coupon_description).text(status['coupon_description']);

            $(miniShop2.Cart.totalCost).text(miniShop2.Utils.formatPrice(status['total_cost']));
            if ($(miniShop2.Order.orderCost, miniShop2.Order.order).length) {
                miniShop2.Order.getcost();
            }
        }
    }

}


