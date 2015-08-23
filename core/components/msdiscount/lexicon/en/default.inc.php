<?php

include_once 'setting.inc.php';

$_lang['msdiscount'] = 'Discounts';
$_lang['msdiscount_menu_desc'] = 'Management of discount system';

$_lang['msd_sales'] = 'Discounts';
$_lang['msd_sales_desc'] = 'Here you create a share, specify a discount and can assign groups of goods and users for whom it will work.<br/>If you group is not specified - the discount is valid for all.';
$_lang['msd_coupons'] = 'Discount coupons';
$_lang['msd_coupons_desc'] = 'Here you can generate any number of coupons with arbitrary discount. Customers will can enter coupon code to receive discount for the whole shopping cart.';
$_lang['msd_users'] = 'Users groups';
$_lang['msd_users_desc'] = 'Manage user groups. You can specify a personal discount group, which will be received by all its users.';
$_lang['msd_products'] = 'Products groups';
$_lang['msd_products_desc'] = 'Manage groups of goods. You can specify a personal discount group, which will act on all the goods that it includes.';


$_lang['msd_submit'] = 'Test';
$_lang['msd_clear'] = 'Clear';

$_lang['msd_btn_sale_create'] = 'Add sale';
$_lang['msd_btn_group_create'] = 'Create group';
$_lang['msd_btn_coupon_create'] = 'Create coupons';

$_lang['msd_action_update'] = 'Update';
$_lang['msd_action_disable'] = 'Disable';
$_lang['msd_action_enable'] = 'Enable';
$_lang['msd_action_switch_mode'] = 'Switch mode';
$_lang['msd_action_remove'] = 'Remove';
$_lang['msd_action_remove_confirm'] = 'Are you sure you want to remove this record?';
$_lang['msd_action_download_coupons'] = 'Download coupons';
$_lang['msd_actions'] = 'Actions';

$_lang['msd_err_ae'] = 'This value must be unique';
$_lang['msd_err_ns'] = 'This field is required';
$_lang['msd_err_prefix'] = 'The prefix must consist of 5 characters of group A-Z0-9';
$_lang['msd_err_coupon_active'] = 'This code was already activated';
$_lang['msd_err_coupon_code'] = 'Wrong discount code';
$_lang['msd_err_coupon_begins'] = 'The validity of this discount has not yet arrived';
$_lang['msd_err_coupon_ends'] = 'The validity of this discount has ended';
$_lang['msd_err_download_ids'] = 'Not specified ids of discount groups';

$_lang['msd_sales_id'] = 'Id';
$_lang['msd_sales_main'] = 'Main';
$_lang['msd_sales_usergroups'] = 'Users';
$_lang['msd_sales_productgroups'] = 'Products';
$_lang['msd_sales_create'] = 'New sale';
$_lang['msd_sales_update'] = 'Update ';
$_lang['msd_sales_discount'] = 'Discount';
$_lang['msd_sales_name'] = 'Title';
$_lang['msd_sales_description'] = 'Description';
$_lang['msd_sales_begins'] = 'Begins';
$_lang['msd_sales_ends'] = 'Ends';
$_lang['msd_sales_active'] = 'Active';
$_lang['msd_sales_resource'] = 'Linked with page';
$_lang['msd_sales_image'] = 'Image';

$_lang['msd_coupons_id'] = 'Id';
$_lang['msd_coupons_main'] = 'Coupons group';
$_lang['msd_coupons_create'] = 'Create coupons';
$_lang['msd_coupons_view'] = 'View coupons';
$_lang['msd_coupons_update'] = 'Edit coupons';
$_lang['msd_coupons_name'] = 'Coupons group';
$_lang['msd_coupons_number'] = 'The number';
$_lang['msd_coupons_code'] = 'Code';
$_lang['msd_coupons_discount'] = 'Discount';
$_lang['msd_coupons_begins'] = 'Begins';
$_lang['msd_coupons_ends'] = 'Ends';
$_lang['msd_coupons_active'] = 'Active';
$_lang['msd_coupons_activated'] = 'Activated';
$_lang['msd_coupons_coupons'] = 'Coupons';
$_lang['msd_coupons_order'] = 'Order';
$_lang['msd_coupons_createdon'] = 'Created On';
$_lang['msd_coupons_activatedon'] = 'Activated On';
$_lang['msd_coupons_prefix'] = 'Prefix';
$_lang['msd_coupons_description'] = 'Description';
$_lang['msd_coupons_form_desc'] = 'Specify name of the group, period of validity, discount and the number of coupons.<br/>You can specify a unique prefix from 5 characters to distinguish it coupons from others.';

$_lang['msd_group_id'] = 'Id';
$_lang['msd_group_name'] = 'Name';
$_lang['msd_group_discount'] = 'Discount';
$_lang['msd_group_joinsum'] = 'Required amount';

$_lang['msd_members_group'] = 'Group';
$_lang['msd_members_relation'] = 'Mode';
$_lang['msd_members_select'] = 'Select group';
$_lang['msd_members_relation_in'] = 'Includes';
$_lang['msd_members_relation_out'] = 'Excludes';

$_lang['msd_check'] = 'Check';
$_lang['msd_check_desc'] = 'Here you can check how there will be a discount.';
$_lang['msd_check_product'] = 'Product';
$_lang['msd_check_user'] = 'User';
$_lang['msd_check_date'] = 'Date';
$_lang['msd_check_log'] = 'Log';
$_lang['msd_check_select'] = 'Select from list';
$_lang['msd_check_err_pid'] = 'You must specify the goods to test';
$_lang['msd_check_err_product'] = 'Cannot find the specified goods';



// Debug messages
$_lang['msd_dbg_initial_price'] = 'Initial price of the product (id=[[+product_id]]): <b>[[+price]]</b>.';

$_lang['msd_dbg_get_users'] = 'Received user groups (id=[[+user_id]]): <b>[[+count]]</b> <small>PCs</small>';
$_lang['msd_dbg_get_products'] = 'Received product groups (id=[[+product_id]]): <b>[[+count]]</b> <small>PCs</small>';
$_lang['msd_dbg_get_sales'] = "Received active sales: <b>[[+count]]</b> <small>PCs</small>\n";

$_lang['msd_dbg_sale_start'] = 'Try to apply the action "[[+name]]"';
$_lang['msd_dbg_sale_all'] = 'The "[[+name]]" no requirements to the groups';

$_lang['msd_dbg_sale_group_both'] = '	The product and users groups satisfy the rules of the action, so: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_group_users'] = '	User is required groups "[[+name]]", so: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_group_products'] = '	This product is included in the required groups "[[+name]]", so: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_personal_users'] = '	The user has a personal discount: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_personal_products'] = '	An item is personal discount: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_group_no'] = '	The user and / or the goods are not included in the list of the required groups "[[+name]]".';
$_lang['msd_dbg_sale_users_exclude'] = '	The rules "[[+name]]" exclude group user id = [[+group_id]]).';
$_lang['msd_dbg_sale_products_exclude'] = '	The rules "[[+name]]" exclude groups of goods (id = [[+group_id]]).';
$_lang['msd_dbg_sale_end'] = "Checked all active promotion.\n";

$_lang['msd_dbg_personal_users'] = 'Personal discount user group (id = [[+group_id]]): <b>[[+discount]]</b>.';
$_lang['msd_dbg_personal_products'] = 'Discount on all items of the group (id = [[+group_id]]): <b>[[+discount]]</b>.';

$_lang['msd_dbg_discount_percent_to_abs'] = 'Consider the percentage of [[+percent]] product prices [[+price]] - goes <b>[[+discount]]</b>.';
$_lang['msd_dbg_discount_abs_vs_percent'] = 'Compare absolute discount [[+absolute]] посчитанным percentage [[+percent]].';
$_lang['msd_dbg_discount_total'] = "The resulting discount <b>[[+discount]]</b>\nTotal product price <b>[[+price]]</b>.";
$_lang['msd_dbg_discount_less'] = "Skip discount <b>[[+discount]]</b> because it is smaller than the current.";
$_lang['msd_dbg_discount_no'] = 'No discounts, return the original price of the <b>[[+price]]</b>.';
$_lang['msd_dbg_time'] = "\nSpent time: <b>[[+time]]</b> <small>s.</small>";

$_lang['msd_err_no_sales'] = "There is no suitable products in current actions.";