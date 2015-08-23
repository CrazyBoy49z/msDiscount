<div id="msdCouponeForm" class="row">
    <form action="[[~[[*id]]]]" class="msd__coupone__form clearfix span10 col-md-10">
        <input type="hidden" value="[[+coupon_action]]" name="action">
        <h2>[[%msd_coupons_front_form_title]]</h2>
        <fieldset>

            <div class="form-group [[+coupon_class]]" id="msd_coupon">
                <label for="exampleInputEmail1"></label>
                <input type="text" class="form-control" [[+coupon_disabled:ne=``:then=`disabled`]] id="msd_coupon_input" name="coupon_code" value="[[+coupon_code]]" placeholder="[[%msd_coupons_front_place_copone]]">
                <div class="help-block error_coupon">[[+coupon_msg]]</div>
            </div>

            <button type="submit" class="btn btn-default">[[+coupon_btn]]</button>
            <br><br>
            <div id="coupon_description">
                [[+coupon_description]]
            </div>
        </fieldset>
    </form>


</div>