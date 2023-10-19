<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'tbllocation.storage_location as storage_location',
	'tblwarehouse.warehouse_name as warehouse_name', 
];
$sIndexColumn = 'warehouse_id';
$sTable = db_prefix() . 'location';

$where = [];

$join= [];



$custom_fields = get_custom_fields('warehouse_name', [
    'show_on_table' => 1,
    ]);

$i = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $i . ' ON '.db_prefix().'warehouse.warehouse_id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="warehouse_name" AND ctable_' . $i . '.fieldid=' . $field['id']);
    //array_push($join, 'LEFT JOIN '.db_prefix().'location ON '.db_prefix().'warehouse.warehouse_i = location.warehouse_id');
	$i++;
}
array_push($join, 'LEFT JOIN '.db_prefix().'warehouse ON '.db_prefix().'warehouse.warehouse_id = '.db_prefix().'location.warehouse_id');
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse.warehouse_id','tblwarehouse.warehouse_name','tbllocation.storage_location']);

$output = $result['output'];
$rResult = $result['rResult'];



	foreach ($rResult as $aRow) {
		$row = [];
		for ($i = 0; $i < count($aColumns); $i++) {

			if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
	            $_data = $aRow[strafter($aColumns[$i], 'as ')];
	        } 

			if($aColumns[$i] == 'storage_location'){

				$_data = $aRow['storage_location']; 

			} elseif ($aColumns[$i] == 'warehouse_name') {

				$_data = $aRow['warehouse_name'];

			} 

			$row[] = $_data;

		}
		$output['aaData'][] = $row;
	}

