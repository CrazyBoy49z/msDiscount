<?php
$xpdo_meta_map['msdCoupon']= array (
  'package' => 'msdiscount',
  'version' => '1.1',
  'table' => 'ms2d_coupons',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'group_id' => NULL,
    'code' => NULL,
    'createdon' => 'CURRENT_TIMESTAMP',
    'activatedon' => '0000-00-00 00:00:00',
    'active' => 1,
    'order_id' => 0,
  ),
  'fieldMeta' => 
  array (
    'group_id' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'int',
      'null' => false,
    ),
    'code' => 
    array (
      'dbtype' => 'char',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'index' => 'unique',
    ),
    'createdon' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
      'default' => 'CURRENT_TIMESTAMP',
    ),
    'activatedon' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
      'default' => '0000-00-00 00:00:00',
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => true,
      'default' => 1,
    ),
    'order_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => true,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'code' => 
    array (
      'alias' => 'code',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'code' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Group' => 
    array (
      'class' => 'msdCouponGroup',
      'local' => 'group_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Order' => 
    array (
      'class' => 'msOrder',
      'local' => 'order_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
