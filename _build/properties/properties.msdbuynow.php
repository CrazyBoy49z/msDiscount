<?php

$properties = array();

$tmp = array(
	'sale' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'tpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.msProducts.discount.row',
	),
	'frontend_css' => array(
		'type' => 'textfield',
		'value' => '[[+assetsUrl]]css/web/main.css',
	),
	'frontend_js' => array(
		'type' => 'textfield',
		'value' => '[[+assetsUrl]]js/web/default.js',
	),
);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => PKG_NAME_LOWER . '_prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;
