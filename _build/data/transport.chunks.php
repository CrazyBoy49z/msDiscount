<?php

$chunks = array();

$tmp = array(
	'tpl.msProducts.discount.row' => array(
		'file' => 'msd_product_row',
		'description' => '',
	),
	'tpl.msProduct.discount' => array(
		'file' => 'msd_discount',
		'description' => '',
	),
	'tpl.msdCoupon.Form' => array(
		'file' => 'msd_coupon_form',
		'description' => '',
	),
	'msdDiscountCoupon' => array(
		'file' => 'msd_discount_outer',
		'description' => '',
	),
);

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'id' => 0,
		'name' => $k,
		'description' => @$v['description'],
		'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v['file'].'.tpl'),
		'static' => BUILD_CHUNK_STATIC,
		'source' => 1,
		'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/chunks/chunk.'.$v['file'].'.tpl',
	),'',true,true);

	$chunks[] = $chunk;
}

unset($tmp);
return $chunks;
