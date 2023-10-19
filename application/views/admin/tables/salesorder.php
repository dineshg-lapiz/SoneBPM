<?php

defined('BASEPATH') or exit('No direct script access allowed');

$project_id = $this->ci->input->post('project_id');

$aColumns = [
    db_prefix() . 'salesorder.id as id',
    'total',
    'total_tax',
	//format_salesorder_number('tblsalesorder.id'),
	'CONCAT("'.get_option("salesorder_prefix").'",LPAD(tblsalesorder.id, 6, "0")) as so_id',
	//'CONCAT("&#8377;", FORMAT(tblsalesorder.total, 2)) as formated_total',
	//'FORMAT(tblsalesorder.total, "C", "en-in") as en_in',
	//'CONCAT("€", FORMAT(tblsalesorder.total, 2, "en_IN")) as formated_total',
	//'CONCAT("",format(tblsalesorder.total,2,"en_IN"))  as formated_total',
	//"concat('',format(total,2)) as formated_total",
	//app_format_money('tblsalesorder.total', 'tblsalesorder.currency_name').' as formated_total',
	//'addedfrom',
    'YEAR(date) as year',
    'date',
    get_sql_select_client_company(),
    db_prefix() . 'projects.name as project_name',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'salesorder.id and rel_type="salesorder" ORDER by tag_order ASC) as tags',
    'duedate',
    db_prefix() . 'salesorder.status',
    ];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'salesorder';

$join = [
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'salesorder.clientid',
    'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'salesorder.currency',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'salesorder.project_id',
];

$custom_fields = get_table_custom_fields('salesorder');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);

    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'salesorder.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$where  = [];
$filter = [];

if ($this->ci->input->post('not_sent')) {
    array_push($filter, 'AND sent = 0 AND ' . db_prefix() . 'salesorder.status NOT IN(' . Salesorder_model::STATUS_PAID . ',' . Salesorder_model::STATUS_CANCELLED . ')');
}
if ($this->ci->input->post('not_have_payment')) {
    array_push($filter, 'AND ' . db_prefix() . 'salesorder.id NOT IN(SELECT invoiceid FROM ' . db_prefix() . 'invoicepaymentrecords) AND ' . db_prefix() . 'salesorder.status != ' . Salesorder_model::STATUS_CANCELLED);
}
if ($this->ci->input->post('recurring')) {
    array_push($filter, 'AND recurring > 0');
}

$statuses  = $this->ci->salesorder_model->get_statuses();
$statusIds = [];
foreach ($statuses as $status) {
    if ($this->ci->input->post('salesorder_' . $status)) {
        array_push($statusIds, $status);
    }
}
//print "<pre>";print_r($statusIds);
if (count($statusIds) > 0) {
    array_push($filter, 'AND ' . db_prefix() . 'salesorder.status IN ("' . implode('","', $statusIds) . '")'); // "','"
}
//print "<pre>";print_r($filter);

$agents    = $this->ci->salesorder_model->get_sale_agents();
$agentsIds = [];
foreach ($agents as $agent) {
    if ($this->ci->input->post('sale_agent_' . $agent['sale_agent'])) {
        array_push($agentsIds, $agent['sale_agent']);
    }
}
if (count($agentsIds) > 0) {
    array_push($filter, 'AND sale_agent IN (' . implode(', ', $agentsIds) . ')');
}

$modesIds = [];
foreach ($data['payment_modes'] as $mode) {
    if ($this->ci->input->post('invoice_payments_by_' . $mode['id'])) {
        array_push($modesIds, $mode['id']);
    }
}
if (count($modesIds) > 0) {
    array_push($where, 'AND ' . db_prefix() . 'salesorder.id IN (SELECT invoiceid FROM ' . db_prefix() . 'invoicepaymentrecords WHERE paymentmode IN ("' . implode('", "', $modesIds) . '"))');
}

$years     = $this->ci->salesorder_model->get_invoices_years();
$yearArray = [];
foreach ($years as $year) {
    if ($this->ci->input->post('year_' . $year['year'])) {
        array_push($yearArray, $year['year']);
    }
}
if (count($yearArray) > 0) {
    array_push($where, 'AND YEAR(date) IN (' . implode(', ', $yearArray) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if ($clientid != '') {
    array_push($where, 'AND ' . db_prefix() . 'salesorder.clientid=' . $this->ci->db->escape_str($clientid));
}

if ($project_id) {
    array_push($where, 'AND project_id=' . $this->ci->db->escape_str($project_id));
}

if (!has_permission('salesorder', '', 'view')) {
    $userWhere = 'AND ' . get_salesorder_where_sql_for_staff(get_staff_user_id());
    array_push($where, $userWhere);
}

$this->ci->load->model('staff_model');
$this->ci->load->model('roles_model');
$staff_members = $this->ci->staff_model->get_staff_members(get_staff_user_id()); 
$staff = $this->ci->staff_model->get(get_staff_user_id());
$role         = (array)$this->ci->roles_model->get($staff->role);
if (has_permission('salesorder', '', 'view_members') || (isset($role["permissions"]["salesorder"]) && in_array("view_members",$role["permissions"]["salesorder"]))) { 
	if(isset($staff_members)){
		foreach ($staff_members as $member) {		
			array_push($where, ' OR (' . db_prefix() . 'salesorder.addedfrom=' . $member['staffid'] . ')');
		}
	}
}

if (has_permission('salesorder', '', 'view_members')) { //print "here";
   /* $userWhere = 'AND ' . get_salesorder_where_sql_for_staff(get_staff_user_id());
    array_push($where, $userWhere);*/
}

$aColumns = hooks()->apply_filters('invoices_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'salesorder.id',
	//format_salesorder_number(db_prefix() . 'salesorder.id'),
	//str_pad(db_prefix() . 'salesorder.id', get_option('number_padding_prefixes'), '0', STR_PAD_LEFT).' as so_id',
	db_prefix() . 'salesorder.addedfrom',
    db_prefix() . 'salesorder.clientid',
    db_prefix() . 'currencies.name as currency_name',
    'project_id',
    'hash',
    'recurring',
    'deleted_customer_name',
    ]);
$output  = $result['output'];
$rResult = $result['rResult'];


/*for($m=0;$m<count($rResult);$m++){
	$rResult[$m]['id'] = format_salesorder_number($rResult[$m]['id']);
	$rResult[$m]['total'] = app_format_money($rResult[$m]['total'], $rResult[$m]['currency_name']);
	$rResult[$m]['total_tax'] = app_format_money($rResult[$m]['total_tax'], $rResult[$m]['currency_name']);
}*/
//print "<pre>";print_r($rResult);exit;

foreach ($rResult as $aRow) {
    $row = [];

    $numberOutput = '';

    // If is from client area table
    if (is_numeric($clientid) || $project_id) {
        $numberOutput = '<a href="' . admin_url('salesorder/list_salesorder/' . $aRow['id']) . '" target="_blank">' . ($aRow['so_id']) . '</a>';
    } else {
        $numberOutput = '<a href="' . admin_url('salesorder/list_salesorder/' . $aRow['id']) . '" onclick="init_salesorder(' . $aRow['id'] . '); return false;">'. ($aRow['so_id']) . '</a>';
    }

    if ($aRow['recurring'] > 0) {
        $numberOutput .= '<br /><span class="label label-primary inline-block tw-mt-1"> ' . _l('invoice_recurring_indicator') . '</span>';
    }

    $numberOutput .= '<div class="row-options">';

    $numberOutput .= '<a href="' . site_url('salesorder/' . $aRow['id'] . '/' . $aRow['hash']) . '" target="_blank">' . _l('view') . '</a>';
    if (has_permission('salesorder', '', 'edit')) {
        $numberOutput .= ' | <a href="' . admin_url('salesorder/invoice/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }
    $numberOutput .= '</div>';

    $row[] = $numberOutput;

    $row[] = app_format_money($aRow['total'], $aRow['currency_name']);
	//$row[] = $aRow['formated_total'];

    $row[] = app_format_money($aRow['total_tax'], $aRow['currency_name']);
	
	//$row[] = $aRow['addedfrom'];
	$staff = get_staff($aRow['addedfrom']);
    $row[] = $staff->username;

    $row[] = $aRow['year'];

    $row[] = _d($aRow['date']);

    if (empty($aRow['deleted_customer_name'])) {
        $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
    } else {
        $row[] = $aRow['deleted_customer_name'];
    }

    $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . $aRow['project_name'] . '</a>';
    ;

    $row[] = render_tags($aRow['tags']);

    $row[] = _d($aRow['duedate']);

    $row[] = format_salesorder_status($aRow[db_prefix() . 'salesorder.status']);

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('invoices_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}