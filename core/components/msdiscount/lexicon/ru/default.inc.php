<?php

include_once 'setting.inc.php';

$_lang['msdiscount'] = 'Скидки и акции';
$_lang['msdiscount_menu_desc'] = 'Управление системой скидок';

$_lang['msd_sales'] = 'Скидки и акции';
$_lang['msd_sales_desc'] = 'Здесь вы создаёте акцию, указываете скидку и можете назначить группы товаров и пользователей, для которых она будет действовать.<br/>Если группы не указаны - скидка действует для всех.';
$_lang['msd_coupons'] = 'Скидочные купоны';
$_lang['msd_coupons_desc'] = 'Вы можете сгенерировать любое количество купонов с произвольной скидкой. Покупатели будут вводить код купона при оформлении заказа, чтобы получить скидку на всю корзину.';
$_lang['msd_users'] = 'Группы пользователей';
$_lang['msd_users_desc'] = 'Управление группами пользователей. Вы можете указать персональную скидку группы, которую получат все её пользователи.';
$_lang['msd_products'] = 'Группы товаров';
$_lang['msd_products_desc'] = 'Управление группами товаров. Вы можете указать персональную скидку группы, которая будет действовать на все товары, которые в неё входят.';


$_lang['msd_submit'] = 'Проверить';
$_lang['ms2_frontend_currency'] = 'руб.';
$_lang['msd_clear'] = 'Очистить';

$_lang['msd_btn_sale_create'] = 'Добавить акцию';
$_lang['msd_btn_group_create'] = 'Создать группу';
$_lang['msd_btn_coupon_create'] = 'Создать купоны';

$_lang['msd_action_update'] = 'Изменить';
$_lang['msd_action_disable'] = 'Отключить';
$_lang['msd_action_enable'] = 'Включить';
$_lang['msd_action_switch_mode'] = 'Переключить режим';
$_lang['msd_action_remove'] = 'Удалить';
$_lang['msd_action_remove_confirm'] = 'Вы уверены, что хотите удалить эту запись?';
$_lang['msd_action_download_coupons'] = 'Скачать купоны';
$_lang['msd_actions'] = 'Действия';

$_lang['msd_err_ae'] = 'Это поле должно быть уникально';
$_lang['msd_err_ns'] = 'Это поле обязательно для заполнения';
$_lang['msd_err_prefix'] = 'Префикс должен состоять из 5 символов группы A-Z0-9';
$_lang['msd_err_coupon_active'] = 'Этот код уже был активирован';
$_lang['msd_err_coupon_cart_empty'] = 'Для применения скидочного купона необходимо добавить товары в корзину!';
$_lang['msd_err_coupon_code'] = 'Неправильный код скидки';
$_lang['msd_err_coupon_empty_code'] = 'Вы не указали код скидки';
$_lang['msd_err_coupon_begins'] = 'Срок действия скидочного купона "[[+coupon]]" еще не наступил';
$_lang['msd_err_coupon_ends'] = 'Срок действия скидочного купона "[[+coupon]]" уже закончился';
$_lang['msd_err_download_ids'] = 'Не указаны id групп скидок';


$_lang['msd_err_copons_code_empty'] = "Не указан купон";
$_lang['msd_err_copons_code_not'] = "Указанный купон не найден";
$_lang['msd_err_copons_code_activeted'] = "Купон более не действителен";
$_lang['msd_err_copons_code_activeted_user'] = "Вы уже воспользовались этим купоном";
$_lang['msd_err_copons_code_access_die_user'] = "Вы не можете использовать данный купон";
$_lang['msd_err_copons_code_access_die_user_ur'] = "Только физическо лицо может применять купон";
$_lang['msd_err_copons_code_product'] = 'Внимание!! Введенный купон не будет действовать на выбранные товары.';
$_lang['msd_err_copons_code_product_data'] = 'Срок действия купона истек.';


$_lang['msd_sales_id'] = 'Id';
$_lang['msd_sales_main'] = 'Основные свойства';
$_lang['msd_sales_usergroups'] = 'Пользователи';
$_lang['msd_sales_productgroups'] = 'Товары';
$_lang['msd_sales_create'] = 'Новая акция';
$_lang['msd_sales_update'] = 'Редактирование ';
$_lang['msd_sales_discount'] = 'Скидка';
$_lang['msd_sales_name'] = 'Название';
$_lang['msd_sales_description'] = 'Описание';
$_lang['msd_sales_begins'] = 'Начинается';
$_lang['msd_sales_ends'] = 'Заканчивается';
$_lang['msd_sales_active'] = 'Включена';
$_lang['msd_sales_resource'] = 'Связана со страницей';
$_lang['msd_sales_image'] = 'Изображение';

$_lang['msd_coupons_id'] = 'Id';
$_lang['msd_coupons_main'] = 'Группа купонов';
$_lang['msd_coupons_create'] = 'Генерация купонов';
$_lang['msd_coupons_view'] = 'Просмотр купонов';
$_lang['msd_coupons_update'] = 'Редактирование купонов';
$_lang['msd_coupons_name'] = 'Группа купонов';
$_lang['msd_coupons_number'] = 'Количество';
$_lang['msd_coupons_disposable'] = 'Уникальный';
$_lang['msd_coupons_disposable_desc'] = 'Все купоны будут иметь уникальный код в виде: PREFI-P9MD-MGR6-3QLQ';
$_lang['msd_coupons_code'] = 'Код';
$_lang['msd_coupons_discount'] = 'Скидка';
$_lang['msd_coupons_begins'] = 'Начинаются';
$_lang['msd_coupons_ends'] = 'Заканчиваются';
$_lang['msd_coupons_active'] = 'Активен';
$_lang['msd_coupons_activated'] = 'Активировано';
$_lang['msd_coupons_coupons'] = 'Купоны';
$_lang['msd_coupons_group_users'] = 'Группы пользователей';
$_lang['msd_coupons_group_products'] = 'Группы ресурсов';
$_lang['msd_coupons_order'] = 'Заказ';
$_lang['msd_coupons_createdon'] = 'Дата создания';
$_lang['msd_coupons_activatedon'] = 'Дата активации';
$_lang['msd_coupons_prefix'] = 'Префикс';
$_lang['msd_coupons_description'] = 'Описание';
$_lang['msd_coupons_form_desc'] = 'Укажите название для группы, срок действия, номинал и количество скидочных купонов.<br/>Группе можно указать уникальный префикс из 5 символов, чтобы отличать её купоны от других.<br/> Внимание: если вы не укажите группу пользователей которые смогут воспользоватся купоном, то купоном смогут воспользовать Гости (не авторизированные пользователь).';

$_lang['msd_coupons_front_form_title'] = 'Скидочный купон';
$_lang['msd_coupons_front_title'] = 'Купон';
$_lang['msd_coupons_front_cancel'] = 'отменить купон';
$_lang['msd_coupons_front_text'] = 'У вас есть купон?';
$_lang['msd_coupons_front_btn_apply'] = 'Применить';
$_lang['msd_coupons_front_btn_cancel'] = 'Отмена';
$_lang['msd_coupons_front_place_copone'] = 'XXXXXXXXXX';
$_lang['msd_coupons_front_help'] = 'Введите купон';
$_lang['msd_coupons_front_active_coupone'] = 'Примененный Купон';


$_lang['msd_group_id'] = 'Id';
$_lang['msd_group_name'] = 'Название';
$_lang['msd_group_discount'] = 'Скидка';
$_lang['msd_group_joinsum'] = 'Требуется сумма';
$_lang['msd_group_rates'] = 'Скидка на сумму от';

$_lang['msd_members_group'] = 'Группа';
$_lang['msd_members_relation'] = 'Режим';
$_lang['msd_members_select'] = 'Выберите группу';
$_lang['msd_members_relation_in'] = 'Включает';
$_lang['msd_members_relation_out'] = 'Исключает';

$_lang['msd_check'] = 'Проверка';
$_lang['msd_check_desc'] = 'Здесь вы можете проверить, как будет насчитываться скидка.';
$_lang['msd_check_product'] = 'Товар';
$_lang['msd_check_user'] = 'Пользователь';
$_lang['msd_check_date'] = 'Дата';
$_lang['msd_check_log'] = 'Лог';
$_lang['msd_check_select'] = 'Выберите из списка';
$_lang['msd_check_err_pid'] = 'Вы должны указать товар для проверки';
$_lang['msd_check_err_product'] = 'Не могу найти указанный товар';



// Debug messages
$_lang['msd_dbg_initial_price'] = 'Начальная цена товара (id=[[+product_id]]): <b>[[+price]]</b>.';

$_lang['msd_dbg_get_users'] = 'Получены группы пользователя (id=[[+user_id]]): <b>[[+count]]</b> <small>шт.</small>';
$_lang['msd_dbg_get_products'] = 'Получены группы товара (id=[[+product_id]]): <b>[[+count]]</b> <small>шт.</small>';
$_lang['msd_dbg_get_sales'] = "Получены активные акции: <b>[[+count]]</b> <small>шт.</small>\n";

$_lang['msd_dbg_sale_start'] = 'Пытаемся применить акцию "[[+name]]"';
$_lang['msd_dbg_sale_all'] = '	У "[[+name]]" нет требований к группам';

$_lang['msd_dbg_sale_group_both'] = '	И товар и пользователь удовлетворяют правилам акции, поэтому: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_group_users'] = '	Пользователь входит в требуемые группы "[[+name]]", поэтому: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_group_products'] = '	Товар входит в требуемые группы "[[+name]]", поэтому: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_personal_users'] = '	У пользователя есть персональная скидка: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_personal_products'] = '	На товар установлена персональная скидка: <b>[[+discount]]</b>.';
$_lang['msd_dbg_sale_group_no'] = '	Пользователь и\или товар не входят в список требуемых групп "[[+name]]".';
$_lang['msd_dbg_sale_users_exclude'] = '	Правила "[[+name]]" исключают группу пользователя (id = [[+group_id]]).';
$_lang['msd_dbg_sale_products_exclude'] = '	Правила "[[+name]]" исключают группу товара (id = [[+group_id]]).';
$_lang['msd_dbg_sale_end'] = "Проверены все активные акции.\n";

$_lang['msd_dbg_personal_users'] = 'Персональная скидка пользователя от группы (id = [[+group_id]]): <b>[[+discount]]</b>.';
$_lang['msd_dbg_personal_products'] = 'Скидка на все товары из группы (id = [[+group_id]]): <b>[[+discount]]</b>.';

$_lang['msd_dbg_discount_percent_to_abs'] = 'Считаем процент [[+percent]] от цены товара [[+price]] - выходит <b>[[+discount]]</b>.';
$_lang['msd_dbg_discount_abs_vs_percent'] = 'Сравниваем абсолютную скидку [[+absolute]] с посчитанным процентом [[+percent]].';
$_lang['msd_dbg_discount_total'] = "Итоговая скидка <b>[[+discount]]</b>\nИтоговая цена товара <b>[[+price]]</b>.";
$_lang['msd_dbg_discount_less'] = "	Пропускаем скидку <b>[[+discount]]</b>, так как она меньше или равна текущей.";
$_lang['msd_dbg_discount_no'] = 'Скидки нет, возвращаем начальную цену <b>[[+price]]</b>.';
$_lang['msd_dbg_time'] = "\nЗатрачено времени: <b>[[+time]]</b> <small>сек.</small>";

$_lang['msd_err_no_sales'] = "Нет товаров, подходящих под условия акций.";
