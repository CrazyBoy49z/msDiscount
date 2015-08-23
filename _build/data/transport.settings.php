<?php
$settings = array();

$tmp = array(
    'msd2_discount_sum_cost' => array(
        'value' => true
        ,'xtype' => 'combo-boolean'
        ,'area' => 'msdiscount'
    )
    ,'msd2_save_user_active' => array(
         'value' => false
        ,'xtype' => 'combo-boolean'
        ,'area' => 'msdiscount'
    )
    ,'msd_coupons_code_one' => array(
         'value' => true
        ,'xtype' => 'combo-boolean'
        ,'area' => 'msdiscount'
    )
    ,'msd_coupons_prefix_length' => array(
        'value' => '12'
        ,'xtype' => 'textfield'
        ,'area' => 'msdiscount'
    )
);
foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => $k
            ,'namespace' => 'msdiscount'
        ), $v
    ),'',true,true);
    $settings[] = $setting;
}
return $settings;