<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2023-10-17 10:32:10 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:32:10 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:32:11 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:32:11 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 10:32:14 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:32:14 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:32:14 --> Query error: Table 'zercoaru_sonebpmnew.tblinternal_delivery_not' doesn't exist - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS `tblgoods_transaction_detail`.`id` AS `tblgoods_transaction_detail.id`, goods_receipt_id, commodity_id, `tblgoods_transaction_detail`.`warehouse_id` AS `tblgoods_transaction_detail.warehouse_id`, tblwh_loss_adjustment.storage_location as sl, `tblgoods_transaction_detail`.`date_add` AS `tblgoods_transaction_detail.date_add`, old_quantity, quantity, lot_number, `tblgoods_transaction_detail`.`expiry_date` AS `tblgoods_transaction_detail.expiry_date`, `tblgoods_transaction_detail`.`serial_number` AS `tblgoods_transaction_detail.serial_number`, note, `tblgoods_transaction_detail`.`status` AS `tblgoods_transaction_detail.status` ,tblgoods_transaction_detail.id,tblgoods_transaction_detail.old_quantity,tblgoods_transaction_detail.from_stock_name,tblgoods_transaction_detail.to_stock_name,tblgoods_receipt.date_add as 1_date_add,tblgoods_delivery.date_add as 2_date_add,tblinternal_delivery_note.date_add as 4_date_add,tblwh_loss_adjustment.date_create as 3_date_add,tblgoods_transaction_detail.date_add as opening_stock_date_add
    FROM tblgoods_transaction_detail
    LEFT JOIN tblgoods_receipt ON tblgoods_receipt.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 1 LEFT JOIN tblgoods_delivery ON tblgoods_delivery.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 2 LEFT JOIN tblwh_loss_adjustment ON tblwh_loss_adjustment.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 3 LEFT JOIN tblinternal_delivery_not ON tblinternal_delivery_note.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 4
    
    
    
    ORDER BY tblgoods_transaction_detail.id DESC
    LIMIT 0, 25
    
ERROR - 2023-10-17 10:32:47 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:32:47 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:32:47 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:32:47 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 10:32:49 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:32:49 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:32:49 --> Query error: Table 'zercoaru_sonebpmnew.tblinternal_delivery_not' doesn't exist - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS `tblgoods_transaction_detail`.`id` AS `tblgoods_transaction_detail.id`, goods_receipt_id, commodity_id, `tblgoods_transaction_detail`.`warehouse_id` AS `tblgoods_transaction_detail.warehouse_id`, tblwh_loss_adjustment.storage_location as sl, `tblgoods_transaction_detail`.`date_add` AS `tblgoods_transaction_detail.date_add`, old_quantity, quantity, lot_number, `tblgoods_transaction_detail`.`expiry_date` AS `tblgoods_transaction_detail.expiry_date`, `tblgoods_transaction_detail`.`serial_number` AS `tblgoods_transaction_detail.serial_number`, note, `tblgoods_transaction_detail`.`status` AS `tblgoods_transaction_detail.status` ,tblgoods_transaction_detail.id,tblgoods_transaction_detail.old_quantity,tblgoods_transaction_detail.from_stock_name,tblgoods_transaction_detail.to_stock_name,tblgoods_receipt.date_add as 1_date_add,tblgoods_delivery.date_add as 2_date_add,tblinternal_delivery_note.date_add as 4_date_add,tblwh_loss_adjustment.date_create as 3_date_add,tblgoods_transaction_detail.date_add as opening_stock_date_add
    FROM tblgoods_transaction_detail
    LEFT JOIN tblgoods_receipt ON tblgoods_receipt.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 1 LEFT JOIN tblgoods_delivery ON tblgoods_delivery.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 2 LEFT JOIN tblwh_loss_adjustment ON tblwh_loss_adjustment.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 3 LEFT JOIN tblinternal_delivery_not ON tblinternal_delivery_note.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 4
    
    
    
    ORDER BY tblgoods_transaction_detail.id DESC
    LIMIT 0, 25
    
ERROR - 2023-10-17 10:33:40 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:33:40 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:33:41 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:33:41 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 10:33:42 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:33:42 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:33:58 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:33:58 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:33:59 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:34:02 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:34:02 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:34:02 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:34:03 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:34:03 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:34:03 --> Could not find the language line "draft"
ERROR - 2023-10-17 10:34:03 --> Could not find the language line "draft"
ERROR - 2023-10-17 10:34:03 --> Could not find the language line "Adjusted"
ERROR - 2023-10-17 10:34:03 --> Could not find the language line "Adjusted"
ERROR - 2023-10-17 10:34:03 --> Could not find the language line "draft"
ERROR - 2023-10-17 10:34:38 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:34:38 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:34:38 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:34:45 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:34:45 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:34:49 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:34:49 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:34:56 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:34:56 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:34:56 --> Severity: Notice --> Undefined variable: slhtml D:\xampp7\htdocs\sonebpmnew\modules\warehouse\controllers\Warehouse.php 7360
ERROR - 2023-10-17 10:34:59 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:34:59 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:34:59 --> Severity: Notice --> Undefined variable: slhtml D:\xampp7\htdocs\sonebpmnew\modules\warehouse\controllers\Warehouse.php 7360
ERROR - 2023-10-17 10:35:05 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:35:05 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:35:06 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:35:06 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:35:06 --> Could not find the language line "data_must_number"
ERROR - 2023-10-17 10:47:21 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:47:21 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:47:21 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:47:21 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 10:47:23 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:47:23 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:49:01 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:49:01 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:49:03 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:49:03 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:54:25 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:54:25 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:54:26 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:54:26 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:54:26 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:54:30 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:54:30 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:54:30 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:54:30 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:54:31 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:54:33 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:54:33 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:54:38 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:54:38 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:54:38 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:54:38 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 10:54:38 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 10:54:40 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 10:54:40 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 11:00:42 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 11:00:42 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 11:00:42 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 11:00:42 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 11:01:33 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 11:01:33 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 11:01:35 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 11:01:35 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 11:01:35 --> Could not find the language line "lost, adjustment"
ERROR - 2023-10-17 11:01:37 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 11:01:37 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 11:01:40 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 11:01:40 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 11:01:41 --> Could not find the language line "lost, adjustment"
ERROR - 2023-10-17 11:01:46 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 11:01:46 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 11:01:48 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 11:01:48 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 11:01:48 --> Could not find the language line "lost, adjustment"
ERROR - 2023-10-17 14:17:21 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:17:21 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:17:21 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 14:17:22 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 14:17:24 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:17:24 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:17:24 --> Query error: Unknown column 'tblwh_loss_adjustment.storage_locatio' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS `tblgoods_transaction_detail`.`id` AS `tblgoods_transaction_detail.id`, goods_receipt_id, commodity_id, `tblgoods_transaction_detail`.`warehouse_id` AS `tblgoods_transaction_detail.warehouse_id`, tblwh_loss_adjustment.storage_locatio as sl, `tblgoods_transaction_detail`.`date_add` AS `tblgoods_transaction_detail.date_add`, old_quantity, quantity, lot_number, `tblgoods_transaction_detail`.`expiry_date` AS `tblgoods_transaction_detail.expiry_date`, `tblgoods_transaction_detail`.`serial_number` AS `tblgoods_transaction_detail.serial_number`, note, `tblgoods_transaction_detail`.`status` AS `tblgoods_transaction_detail.status` ,tblgoods_transaction_detail.id,tblgoods_transaction_detail.old_quantity,tblgoods_transaction_detail.from_stock_name,tblgoods_transaction_detail.to_stock_name,tblgoods_receipt.date_add as 1_date_add,tblgoods_delivery.date_add as 2_date_add,tblinternal_delivery_note.date_add as 4_date_add,tblwh_loss_adjustment.date_create as 3_date_add,tblgoods_transaction_detail.date_add as opening_stock_date_add
    FROM tblgoods_transaction_detail
    LEFT JOIN tblgoods_receipt ON tblgoods_receipt.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 1 LEFT JOIN tblgoods_delivery ON tblgoods_delivery.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 2 LEFT JOIN tblwh_loss_adjustment ON tblwh_loss_adjustment.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 3 LEFT JOIN tblinternal_delivery_note ON tblinternal_delivery_note.id = tblgoods_transaction_detail.goods_receipt_id AND  tblgoods_transaction_detail.status = 4
    
    
    
    ORDER BY tblgoods_transaction_detail.id DESC
    LIMIT 0, 25
    
ERROR - 2023-10-17 14:21:12 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:21:12 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:21:12 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 14:21:12 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 14:21:14 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:21:14 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:21:14 --> Could not find the language line "lost, adjustment"
ERROR - 2023-10-17 14:26:43 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:26:43 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:26:43 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 14:26:43 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 14:26:47 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:26:47 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:26:47 --> Severity: error --> Exception: syntax error, unexpected end of file D:\xampp7\htdocs\sonebpmnew\modules\warehouse\views\table_warehouse_history.php 358
ERROR - 2023-10-17 14:27:20 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:27:20 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:27:20 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 14:27:21 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 14:27:24 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:27:24 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:27:24 --> Severity: error --> Exception: syntax error, unexpected end of file D:\xampp7\htdocs\sonebpmnew\modules\warehouse\views\table_warehouse_history.php 358
ERROR - 2023-10-17 14:28:10 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:28:10 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:28:10 --> Severity: User Notice --> Hook after_render_top_search is <strong>deprecated</strong> since version 3.0.0! Use admin_navbar_start instead. D:\xampp7\htdocs\sonebpmnew\application\helpers\deprecated_helper.php 48
ERROR - 2023-10-17 14:28:10 --> Could not find the language line "Inventory history"
ERROR - 2023-10-17 14:28:14 --> Could not find the language line "Proforma Invoice"
ERROR - 2023-10-17 14:28:14 --> Could not find the language line "_warehouse_location"
ERROR - 2023-10-17 14:28:14 --> Severity: Notice --> Undefined variable: storage_location D:\xampp7\htdocs\sonebpmnew\modules\warehouse\views\table_warehouse_history.php 248
ERROR - 2023-10-17 14:28:14 --> Could not find the language line "lost, adjustment"
