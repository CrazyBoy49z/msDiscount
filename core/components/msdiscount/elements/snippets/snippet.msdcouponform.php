<?php
/** @var array $scriptProperties */
/** @var msDiscount $msDiscount */
$msDiscount = $modx->getService('msDiscount');
$tpl = !empty($tpl) ? $tpl : $scriptProperties['tpl'];
$tplOuter = !empty($tplOuter) ? $tplOuter : $scriptProperties['tplOuter'];

$config_js = preg_replace(array('/^\n/', '/\t{5}/'), '', '
    CouponConfig = {
        assetsUrl: "'.$msDiscount->config['assetsUrl'].'",
        actionUrl: "'.$msDiscount->config['actionUrl'].'",
        pageId: "'.$msDiscount->config['pageId'].'",
        btn_apply: "'.$modx->lexicon('msd_coupons_front_btn_apply').'",
        btn_cancel: "'.$modx->lexicon('msd_coupons_front_btn_cancel').'"
    };
');
$modx->regClientStartupScript("<script type=\"text/javascript\">\n".$config_js."\n</script>", true);
$modx->regClientScript($msDiscount->config['coupon_js']);
$msDiscount->statusCoupon();
$form = $modx->getChunk($tpl);
return $modx->getChunk($tplOuter, array('form' => $form));