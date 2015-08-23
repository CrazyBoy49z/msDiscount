<?php
$xpdo_meta_map['msdCouponGroup']= array (
  'package' => 'msdiscount',
  'version' => '1.1',
  'table' => 'ms2d_coupons_group',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'description' => '',
    'discount' => NULL,
    'begins' => '0000-00-00 00:00:00',
    'ends' => '0000-00-00 00:00:00',
    'coupons' => 0,
    'activated' => 0,
    'prefix' => '',
    'disposable' => 0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' =>
    array (
      'dbtype' => 'text',
      'phptype' => 'text',
      'null' => true,
      'default' => '',
    ),
    'discount' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
    ),
    'begins' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
      'default' => '0000-00-00 00:00:00',
    ),
    'ends' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
      'default' => '0000-00-00 00:00:00',
    ),
    'coupons' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'int',
      'null' => true,
      'default' => 0,
    ),
    'activated' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'int',
      'null' => true,
      'default' => 0,
    ),
    'prefix' => 
    array (
      'dbtype' => 'char',
      'precision' => '5',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'disposable' =>
      array (
          'dbtype' => 'tinyint',
          'precision' => '1',
          'phptype' => 'integer',
          'null' => true,
          'default' => 0,
      ),
  ),
  'composites' => 
  array (
    'Coupons' => 
    array (
      'class' => 'msdCoupon',
      'local' => 'id',
      'foreign' => 'group_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
