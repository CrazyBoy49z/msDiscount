<?php
$xpdo_meta_map['msdUserGroup']= array (
  'package' => 'msdiscount',
  'version' => '1.1',
  'table' => 'ms2d_user_groups',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'discount' => '0',
    'joinsum' => 0,
    'rates' => 0,
  ),
  'fieldMeta' => 
  array (
    'discount' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => true,
      'default' => '0',
    ),
    'joinsum' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '10,2',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
    'rates' =>
    array (
      'dbtype' => 'decimal',
      'precision' => '10,2',
      'phptype' => 'float',
      'null' => true,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'joinsum' => 
    array (
      'alias' => 'joinsum',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'joinsum' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),'rates' =>
    array (
      'alias' => 'rates',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' =>
      array (
        'rates' =>
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Members' => 
    array (
      'class' => 'msdSaleMember',
      'local' => 'id',
      'foreign' => 'group_id',
      'owner' => 'local',
      'cardinality' => 'many',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'type' => 'users',
        ),
      ),
    ),
  ),
);
