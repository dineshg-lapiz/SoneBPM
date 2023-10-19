<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoices extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
        $this->load->model('credit_notes_model');
    }

    /* Get all invoices in case user go on index page */
    public function index($id = '')
    {
        $this->list_invoices($id);
    }

    /* List all invoices datatables */
    public function list_invoices($id = '')
    {
        if (!has_permission('invoices', '', 'view')
            && !has_permission('invoices', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('invoices');
        }

        close_setup_menu();

        $this->load->model('payment_modes_model');
        $data['payment_modes']        = $this->payment_modes_model->get('', [], true);
        $data['invoiceid']            = $id;
        $data['title']                = _l('invoices');
        $data['invoices_years']       = $this->invoices_model->get_invoices_years();
        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
        $data['invoices_statuses']    = $this->invoices_model->get_statuses();
        $data['bodyclass']            = 'invoices-total-manual';
        $this->load->view('admin/invoices/manage', $data);
    }

    /* List all recurring invoices */
    public function recurring($id = '')
    {
        if (!has_permission('invoices', '', 'view')
            && !has_permission('invoices', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            access_denied('invoices');
        }

        close_setup_menu();

        $data['invoiceid']            = $id;
        $data['title']                = _l('invoices_list_recurring');
        $data['invoices_years']       = $this->invoices_model->get_invoices_years();
        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
        $this->load->view('admin/invoices/recurring/list', $data);
    }

    public function table($clientid = '')
    {
        if (!has_permission('invoices', '', 'view')
            && !has_permission('invoices', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            ajax_access_denied();
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

        $this->app->get_table_data(($this->input->get('recurring') ? 'recurring_invoices' : 'invoices'), [
            'clientid' => $clientid,
            'data'     => $data,
        ]);
    }

    public function client_change_data($customer_id, $current_invoice = '')
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('projects_model');
            $data                     = [];
            $data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($customer_id);
            $data['client_currency']  = $this->clients_model->get_customer_default_currency($customer_id);

            $data['customer_has_projects'] = customer_has_projects($customer_id);
            $data['billable_tasks']        = $this->tasks_model->get_billable_tasks($customer_id);

            if ($current_invoice != '') {
                $this->db->select('status');
                $this->db->where('id', $current_invoice);
                $current_invoice_status = $this->db->get(db_prefix() . 'invoices')->row()->status;
            }

            $_data['invoices_to_merge'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != Invoices_model::STATUS_CANCELLED) ? $this->invoices_model->check_for_merge_invoice($customer_id, $current_invoice) : [];

            $data['merge_info'] = $this->load->view('admin/invoices/merge_invoice', $_data, true);

            $this->load->model('currencies_model');

            $__data['expenses_to_bill'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != Invoices_model::STATUS_CANCELLED) ? $this->invoices_model->get_expenses_to_bill($customer_id) : [];

            $data['expenses_bill_info'] = $this->load->view('admin/invoices/bill_expenses', $__data, true);
            echo json_encode($data);
        }
    }

    public function update_number_settings($id)
    {
        $response = [
            'success' => false,
            'message' => '',
        ];
        if (has_permission('invoices', '', 'edit')) {
            $affected_rows = 0;

            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', [
                'prefix' => $this->input->post('prefix'),
            ]);
            if ($this->db->affected_rows() > 0) {
                $affected_rows++;
            }

            if ($affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('invoice'));
            }
        }
        echo json_encode($response);
        die;
    }

    public function validate_invoice_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows('invoices', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
            'status !=' => Invoices_model::STATUS_DRAFT,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_invoice($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'invoice', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_invoice($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'invoice');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function pause_overdue_reminders($id)
    {
        if (has_permission('invoices', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 1]);
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function resume_overdue_reminders($id)
    {
        if (has_permission('invoices', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 0]);
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function mark_as_cancelled($id)
    {
        if (!has_permission('invoices', '', 'edit') && !has_permission('invoices', '', 'create')) {
            access_denied('invoices');
        }

        $success = $this->invoices_model->mark_as_cancelled($id);

        if ($success) {
            set_alert('success', _l('invoice_marked_as_cancelled_successfully'));
        }

        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function unmark_as_cancelled($id)
    {
        if (!has_permission('invoices', '', 'edit') && !has_permission('invoices', '', 'create')) {
            access_denied('invoices');
        }
        $success = $this->invoices_model->unmark_as_cancelled($id);
        if ($success) {
            set_alert('success', _l('invoice_unmarked_as_cancelled'));
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function copy($id)
    {
        if (!$id) {
            redirect(admin_url('invoices'));
        }
        if (!has_permission('invoices', '', 'create')) {
            access_denied('invoices');
        }
        $new_id = $this->invoices_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('invoice_copy_success'));
            redirect(admin_url('invoices/invoice/' . $new_id));
        } else {
            set_alert('success', _l('invoice_copy_fail'));
        }
        redirect(admin_url('invoices/invoice/' . $id));
    }

    public function get_merge_data($id)
    {
        $invoice = $this->invoices_model->get($id);
        $cf      = get_custom_fields('items');

        $i = 0;

        foreach ($invoice->items as $item) {
            $invoice->items[$i]['taxname']          = get_invoice_item_taxes($item['id']);
            $invoice->items[$i]['long_description'] = clear_textarea_breaks($item['long_description']);
            $this->db->where('item_id', $item['id']);
            $rel              = $this->db->get(db_prefix() . 'related_items')->result_array();
            $item_related_val = '';
            $rel_type         = '';
            foreach ($rel as $item_related) {
                $rel_type = $item_related['rel_type'];
                $item_related_val .= $item_related['rel_id'] . ',';
            }
            if ($item_related_val != '') {
                $item_related_val = substr($item_related_val, 0, -1);
            }
            $invoice->items[$i]['item_related_formatted_for_input'] = $item_related_val;
            $invoice->items[$i]['rel_type']                         = $rel_type;

            $invoice->items[$i]['custom_fields'] = [];

            foreach ($cf as $custom_field) {
                $custom_field['value']                 = get_custom_field_value($item['id'], $custom_field['id'], 'items');
                $invoice->items[$i]['custom_fields'][] = $custom_field;
            }
            $i++;
        }
        echo json_encode($invoice);
    }

    public function get_bill_expense_data($id)
    {
        $this->load->model('expenses_model');
        $expense = $this->expenses_model->get($id);

        $expense->qty              = 1;
        $expense->long_description = clear_textarea_breaks($expense->description);
        $expense->description      = $expense->name;
        $expense->rate             = $expense->amount;
        if ($expense->tax != 0) {
            $expense->taxname = [];
            array_push($expense->taxname, $expense->tax_name . '|' . $expense->taxrate);
        }
        if ($expense->tax2 != 0) {
            array_push($expense->taxname, $expense->tax_name2 . '|' . $expense->taxrate2);
        }
        echo json_encode($expense);
    }

    /* Add new invoice or update existing */
    public function invoice($id = '')
    {
        if ($this->input->post()) {
            $invoice_data = $this->input->post();
            if ($id == '') {
                if (!has_permission('invoices', '', 'create')) {
                    access_denied('invoices');
                }

                if (hooks()->apply_filters('validate_invoice_number', true)) {
                    $number = ltrim($invoice_data['number'], '0');
                    if (total_rows('invoices', [
                        'YEAR(date)' => date('Y', strtotime(to_sql_date($invoice_data['date']))),
                        'number'     => $number,
                        'status !='  => Invoices_model::STATUS_DRAFT,
                    ])) {
                        set_alert('warning', _l('invoice_number_exists'));

                        redirect(admin_url('invoices/invoice'));
                    }
                }
//print "<pre>";print_r($invoice_data);exit;
                $id = $this->invoices_model->add($invoice_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('invoice')));
                    $redUrl = admin_url('invoices/list_invoices/' . $id);

                    if (isset($invoice_data['save_and_record_payment'])) {
                        $this->session->set_userdata('record_payment', true);
                    } elseif (isset($invoice_data['save_and_send_later'])) {
                        $this->session->set_userdata('send_later', true);
                    }

                    redirect($redUrl);
                }
            } else {
                if (!has_permission('invoices', '', 'edit')) {
                    access_denied('invoices');
                }

                // If number not set, is draft
                if (hooks()->apply_filters('validate_invoice_number', true) && isset($invoice_data['number'])) {
                    $number = trim(ltrim($invoice_data['number'], '0'));
                    if (total_rows('invoices', [
                        'YEAR(date)' => date('Y', strtotime(to_sql_date($invoice_data['date']))),
                        'number'     => $number,
                        'status !='  => Invoices_model::STATUS_DRAFT,
                        'id !='      => $id,
                    ])) {
                        set_alert('warning', _l('invoice_number_exists'));

                        redirect(admin_url('invoices/invoice/' . $id));
                    }
                }
                $success = $this->invoices_model->update($invoice_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('invoice')));
                }

                redirect(admin_url('invoices/list_invoices/' . $id));
            }
        }
        if ($id == '') {
            $title                  = _l('create_new_invoice');
			/*$data = $this->session->userdata("data");
			print "<pre>";print_r($data);exit;*/
            $data['billable_tasks'] = [];
        } else {
            $invoice = $this->invoices_model->get($id);

            if (!$invoice || !user_can_view_invoice($id)) {
                blank_page(_l('invoice_not_found'));
            }

            $data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $invoice->id);
            $data['expenses_to_bill']  = $this->invoices_model->get_expenses_to_bill($invoice->clientid);

            $data['invoice']        = $invoice;
            $data['edit']           = true;
            $data['billable_tasks'] = $this->tasks_model->get_billable_tasks($invoice->clientid, !empty($invoice->project_id) ? $invoice->project_id : '');

            $title = _l('edit', _l('invoice_lowercase')) . ' - ' . format_invoice_number($invoice->id);
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['staff']     = $this->staff_model->get('', ['active' => 1]);
        $data['title']     = $title;
        $data['bodyclass'] = 'invoice';
		
		$this->load->model('template_list_model');
		$data['templates'] = $this->template_list_model->get_templates('invoice');
		
        $this->load->view('admin/invoices/invoice', $data);
    }
	
	public function so_to_invoice($id = ''){
		if ($this->input->post()) {
			$this->load->model('salesorder_model');
			$invoice = $this->salesorder_model->get($id);
			$invoice_data = $this->input->post();
			$invoice_data["items"] = array_values($invoice_data["items"]);
			
			
			$so_data = $invoice->items;
			/*print "<pre>";print_r($so_data);
			print "<pre>";print_r(($invoice_data["items"]));*/
			/*print $so_data[0]["id"];
			exit;*/
			$k=0;
			/*foreach ($invoice_data["items"] as $item) {
				print $item["itemid"]."==".$so_data[$k]["id"]." && ".$item["qty"].">".$so_data[$k]["qty"];
				if($item["itemid"]==$so_data[$k]["id"] && $item["qty"]>$so_data[$k]["qty"]){
					//print "sadsadsa";exit;
					set_alert('warning', _l('Invoice Quantity is more than Sale Order Quantity'));
					redirect(admin_url('invoices/so_to_invoice/'.$id));
				}elseif($item["itemid"]==$so_data[$k]["id"] && $item["qty"]<$so_data[$k]["qty"]){
					//print "sadsadsa";exit;
					$update_data["status"]="Partially Delivered";
					$this->salesorder_model->update($update_data, $id);
				}
				$k++;
			}*/
			foreach ($so_data as $item) {
				if(count($so_data)==count($invoice_data["items"])){
					/*print $item["id"]."==".$invoice_data["items"][$k]["itemid"]." && ".$item["qty"].">".$invoice_data["items"][$k]["qty"];
					exit;*/
					if($item["id"]==$invoice_data["items"][$k]["itemid"] && $invoice_data["items"][$k]["qty"]>$item["qty"]){
						//print "sadsadsa";exit;
						set_alert('warning', _l('Invoice Quantity is more than Sale Order Quantity'));
						redirect(admin_url('invoices/so_to_invoice/'.$id));
					}elseif($item["id"]==$invoice_data["items"][$k]["itemid"] && $invoice_data["items"][$k]["qty"]<$item["qty"]){
						//print "sadsadsa";exit;
						$update_data["status"]="Partially Delivered";
						//$this->salesorder_model->update($update_data, $id);
						$this->db->where('id', $id)->update('salesorder', $update_data);
						
						/*if($invoice_data["invoiceid"]!=""){
							$inv_item = $this->salesorder_model->get_invoice_item_by_rel($invoice->invoiceid);
							print "<pre>";print_r($inv_item);
							if(isset($inv_item) && $inv_item->qty<$item["qty"]){
								$item["qty"] = $item["qty"]-$inv_item->qty;
							}
						}
						$so_data_items[] = $item;*/
						
					}elseif($item["id"]==$invoice_data["items"][$k]["itemid"] && $invoice_data["items"][$k]["qty"]==$item["qty"]){
						//print "sadsadsa";exit;
						//$update_data["status"]="Fully Delivered";
						//$this->db->where('id', $id)->update('salesorder', $update_data);
					}
					$k++;
				}elseif(count($so_data)>count($invoice_data["items"])){
					$update_data["status"]="Partially Delivered";
					$this->db->where('id', $id)->update('salesorder', $update_data);
				}				
			}
			//exit;
            
            if ($id != '') {
                if (!has_permission('invoices', '', 'create')) {
                    access_denied('invoices');
                }

                if (hooks()->apply_filters('validate_invoice_number', true)) {
                    $number = ltrim($invoice_data['number'], '0');
                    if (total_rows('invoices', [
                        'YEAR(date)' => date('Y', strtotime(to_sql_date($invoice_data['date']))),
                        'number'     => $number,
                        'status !='  => Invoices_model::STATUS_DRAFT,
                    ])) {
                        set_alert('warning', _l('invoice_number_exists'));

                        redirect(admin_url('invoices/so_to_invoice/'.$id));
                    }
                }
				/*$invoice_data["newitems"] = $invoice_data["items"];
				unset($invoice_data["items"]);*/
//print "<pre>";print_r($invoice_data);exit;
                //$ins_id = $this->invoices_model->add($invoice_data);
				$this->load->model('invoices_model');
	            $ins_id = $this->invoices_model->add_new_invoice($invoice_data);
                if ($ins_id) {
                    set_alert('success', _l('added_successfully', _l('invoice')));
                    $redUrl = admin_url('invoices/list_invoices/' . $ins_id);
					
					$update_data["invoiceid"]= $invoice->invoiceid.",".$ins_id;
					$update_data["invoiced_date"]=date("Y-m-d h:i:s");
					$this->db->where('id', $id)->update('salesorder', $update_data);
					/*print $this->db->last_query();
					exit;*/
					
					$invoice_qty = $this->salesorder_model->get($id);
					$all_invoices = explode(",",$invoice_qty->invoiceid);
					$all_invoices = array_values($all_invoices);
					$tot_qty = 0;
					
					
					$so_data = $invoice_qty->items;
					//print "<pre>";print_r($so_data);
					$index=0;
					$status = [];
					foreach ($so_data as $item) {
						//print "<pre>";print_r($item);
						if($invoice_qty->invoiceid!=""){
							$exp_inv = explode(",",$invoice_qty->invoiceid);
							$exp_inv = array_filter(array_values($exp_inv));
							$inv_item_qty = [];
							//print "<pre>";print_r($exp_inv);
							foreach($exp_inv as $e_inv){
								$inv_item = (array)$this->salesorder_model->get_invoice_item_by_rel($e_inv);
								
								foreach ($inv_item as $k=>$itm) {
									$inv_item_qty[$k] += $itm["qty"];
									//$n++;
								}
								
							}
							/*print "<pre>";print_r($inv_item_qty);
							print $item["qty"];*/
								//exit;
							//print "<pre>";print_r($inv_item);
							//foreach ($inv_item_qty as $k=>$iiq) {
								//print $iiq."<".$item["qty"];
							//print $inv_item_qty[$index]."===".$item["qty"];
							if($inv_item_qty[$index]==$item["qty"]){
								$status[] = 1;
							}else{
								$status[] = 0;
							}
							//}
						}
						//print "<pre>";print_r($item);
						$index++;
					}
					/*print "<pre>";print_r($status);print count(array_unique($status)); exit;
					if(count(array_unique($status)) === 1){
						$update_data["status"]="Fully Delivered";
						$this->db->where('id', $id)->update('salesorder', $update_data);
					}*/
					
					
					//print "<pre>";print_r($status);
					$status_tot = count($status);
					$status_val_tot = array_sum($status);
					//print $status_tot."==".$status_val_tot;exit;
					if($status_tot==$status_val_tot){
						$update_data["status"]="Fully Delivered";
						$this->db->where('id', $id)->update('salesorder', $update_data);
					}
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					/*$so_tot_qty = (array)$this->salesorder_model->get_saleorder_item_by_rel($id);
					print "<pre>";print_r($so_tot_qty);
					$index = 0;
					foreach ($all_invoices as $so_inv) {
						$inv_qty = (array)$this->salesorder_model->get_invoice_item_by_rel($so_inv);
						//$tot_qty += $inv_qty->qty;
						$m=0;
						print "<pre>";print_r($inv_qty);
						//foreach($inv_qty as $iq){
							
						//}
						foreach ($inv_qty as $k=>$itm) {
							$inv_item_qty[] += $itm["qty"];
							//$n++;
						}
						$index++;
					}
					if($inv_item_qty[$index]<$item["qty"]){
							$item["qty"] = $item["qty"]-$inv_item_qty[$index];
						}
					print "<pre>";print_r($tot_qty);exit;*/
					
					/*if($so_tot_qty->qty==$tot_qty){
						$update_data["status"]="Fully Delivered";
						$this->db->where('id', $id)->update('salesorder', $update_data);
					}*/
					//print $tot_qty;exit;

                    if (isset($invoice_data['save_and_record_payment'])) {
                        $this->session->set_userdata('record_payment', true);
                    } elseif (isset($invoice_data['save_and_send_later'])) {
                        $this->session->set_userdata('send_later', true);
                    }

                    redirect($redUrl);
                }
			}
        }
		$this->load->model('salesorder_model');
		$invoice = $this->salesorder_model->get($id);
		
		//exit;
		$invoice->number = get_option('next_invoice_number');
		if (!$invoice || !user_can_view_invoice($id)) {
                blank_page(_l('invoice_not_found'));
            }
		$data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $invoice->id);
		$data['expenses_to_bill']  = $this->invoices_model->get_expenses_to_bill($invoice->clientid);
		
		$so_data = $invoice->items;
		//print "<pre>";print_r($so_data);
		$index=0;
		foreach ($so_data as $item) {
			//print "<pre>";print_r($item);
			if($invoice->invoiceid!=""){
				$exp_inv = explode(",",$invoice->invoiceid);
				$exp_inv = array_filter(array_values($exp_inv));
				$inv_item_qty = [];
				//print "<pre>";print_r($exp_inv);
				foreach($exp_inv as $e_inv){
					$inv_item = (array)$this->salesorder_model->get_invoice_item_by_rel($e_inv);
					
					foreach ($inv_item as $k=>$itm) {
						$inv_item_qty[$k] += $itm["qty"];
						//$n++;
					}
					
				}
				//print "<pre>";print_r($inv_item_qty);
					//exit;
				//print "<pre>";print_r($inv_item);
				//foreach ($inv_item_qty as $k=>$iiq) {
					//print $iiq."<".$item["qty"];
					if($inv_item_qty[$index]<$item["qty"]){
						$item["qty"] = $item["qty"]-$inv_item_qty[$index];
						$so_data_items[] = $item;
						//print "<pre>";print_r($item);
					}
				//}
			}else{
				$so_data_items[] = $item;
			}
			
			$index++;
		}
		/*print "<pre>";print_r($so_data_items);
		exit;*/
		$invoice->items = $so_data_items;
		
//print "<pre>";print_r($invoice);exit;
		$invoice->prefix = get_option('invoice_prefix');
		$data['invoice']        = $invoice;
		$data['edit']           = true;
		$data['billable_tasks'] = $this->tasks_model->get_billable_tasks($invoice->clientid, !empty($invoice->project_id) ? $invoice->project_id : '');

		$title = _l('edit', _l('invoice_lowercase')) . ' - ' . format_invoice_number($invoice->id);
		
		if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();
		
        $data['staff']     = $this->staff_model->get('', ['active' => 1]);
        $data['title']     = $title;
        $data['bodyclass'] = 'invoice';
		$data['convert'] = 'so_to_invoice';
		
       $this->load->view('admin/invoices/invoice', $data);
	}
    /* Get all invoice data used when user click on invoiec number in a datatable left side*/
    public function get_invoice_data_ajax($id)
    {
        if (!has_permission('invoices', '', 'view')
            && !has_permission('invoices', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die(_l('invoice_not_found'));
        }

        $invoice = $this->invoices_model->get($id);

        if (!$invoice || !user_can_view_invoice($id)) {
            echo _l('invoice_not_found');
            die;
        }

        $template_name = 'invoice_send_to_customer';

        if ($invoice->sent == 1) {
            $template_name = 'invoice_send_to_customer_already_sent';
        }

        $data = prepare_mail_preview_data($template_name, $invoice->clientid);
		
		$merge_fields = [];
        $merge_fields[] = [
            [
                'name' => 'Items Table',
                'key'  => '{invoice_items}',
            ],
        ];
        $merge_fields = array_merge($merge_fields, $this->app_merge_fields->get_flat('invoice', 'other', '{email_signature}'));

        // Check for recorded payments
        $this->load->model('payments_model');
        $data['invoices_to_merge']          = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $id);
        $data['members']                    = $this->staff_model->get('', ['active' => 1]);
        $data['payments']                   = $this->payments_model->get_invoice_payments($id);
        $data['activity']                   = $this->invoices_model->get_invoice_activity($id);
        $data['totalNotes']                 = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'invoice']);
        $data['invoice_recurring_invoices'] = $this->invoices_model->get_invoice_recurring_invoices($id);
		
		$data['proposal_merge_fields'] = $merge_fields;

        $data['applied_credits'] = $this->credit_notes_model->get_applied_invoice_credits($id);
        // This data is used only when credit can be applied to invoice
        if (credits_can_be_applied_to_invoice($invoice->status)) {
            $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($invoice->clientid);

            if ($data['credits_available'] > 0) {
                $data['open_credits'] = $this->credit_notes_model->get_open_credits($invoice->clientid);
            }

            $customer_currency = $this->clients_model->get_customer_default_currency($invoice->clientid);
            $this->load->model('currencies_model');

            if ($customer_currency != 0) {
                $data['customer_currency'] = $this->currencies_model->get($customer_currency);
            } else {
                $data['customer_currency'] = $this->currencies_model->get_base_currency();
            }
        }

        $data['invoice'] = $invoice;

        $data['record_payment'] = false;
        $data['send_later']     = false;

        if ($this->session->has_userdata('record_payment')) {
            $data['record_payment'] = true;
            $this->session->unset_userdata('record_payment');
        } elseif ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        $this->load->view('admin/invoices/invoice_preview_template', $data);
    }

    public function apply_credits($invoice_id)
    {
        $total_credits_applied = 0;
        foreach ($this->input->post('amount') as $credit_id => $amount) {
            $success = $this->credit_notes_model->apply_credits($credit_id, [
            'invoice_id' => $invoice_id,
            'amount'     => $amount,
        ]);
            if ($success) {
                $total_credits_applied++;
            }
        }

        if ($total_credits_applied > 0) {
            update_invoice_status($invoice_id, true);
            set_alert('success', _l('invoice_credits_applied'));
        }
        redirect(admin_url('invoices/list_invoices/' . $invoice_id));
    }

    public function get_invoices_total()
    {
        if ($this->input->post()) {
            load_invoices_total_template();
        }
    }

    /* Record new inoice payment view */
    public function record_invoice_payment_ajax($id)
    {
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $data['invoice']  = $this->invoices_model->get($id);
        $data['payments'] = $this->payments_model->get_invoice_payments($id);
        $this->load->view('admin/invoices/record_payment_template', $data);
    }

    /* This is where invoice payment record $_POST data is send */
    public function record_payment()
    {
        if (!has_permission('payments', '', 'create')) {
            access_denied('Record Payment');
        }
        if ($this->input->post()) {
            $this->load->model('payments_model');
            $id = $this->payments_model->process_payment($this->input->post(), '');
            if ($id) {
                set_alert('success', _l('invoice_payment_recorded'));
                redirect(admin_url('payments/payment/' . $id));
            } else {
                set_alert('danger', _l('invoice_payment_record_failed'));
            }
            redirect(admin_url('invoices/list_invoices/' . $this->input->post('invoiceid')));
        }
    }

    /* Send invoice to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_invoice($id);
        if (!$canView) {
            access_denied('Invoices');
        } else {
            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {
                access_denied('Invoices');
            }
        }

        try {
            $statementData = [];
            if ($this->input->post('attach_statement')) {
                $statementData['attach'] = true;
                $statementData['from']   = to_sql_date($this->input->post('statement_from'));
                $statementData['to']     = to_sql_date($this->input->post('statement_to'));
            }

            $success = $this->invoices_model->send_invoice_to_client(
                $id,
                '',
                $this->input->post('attach_pdf'),
                $this->input->post('cc'),
                false,
                $statementData
            );
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('invoice_sent_to_client_success'));
        } else {
            set_alert('danger', _l('invoice_sent_to_client_fail'));
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    /* Delete invoice payment*/
    public function delete_payment($id, $invoiceid)
    {
        if (!has_permission('payments', '', 'delete')) {
            access_denied('payments');
        }
        $this->load->model('payments_model');
        if (!$id) {
            redirect(admin_url('payments'));
        }
        $response = $this->payments_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
        }
        redirect(admin_url('invoices/list_invoices/' . $invoiceid));
    }

    /* Delete invoice */
    public function delete($id)
    {
        if (!has_permission('invoices', '', 'delete')) {
            access_denied('invoices');
        }
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }
        $success = $this->invoices_model->delete($id);

        if ($success) {
            set_alert('success', _l('deleted', _l('invoice')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_lowercase')));
        }
        if (strpos($_SERVER['HTTP_REFERER'], 'list_invoices') !== false) {
            redirect(admin_url('invoices/list_invoices'));
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->invoices_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Will send overdue notice to client */
    public function send_overdue_notice($id)
    {
        $canView = user_can_view_invoice($id);
        if (!$canView) {
            access_denied('Invoices');
        } else {
            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {
                access_denied('Invoices');
            }
        }

        $send = $this->invoices_model->send_invoice_overdue_notice($id);
        if ($send) {
            set_alert('success', _l('invoice_overdue_reminder_sent'));
        } else {
            set_alert('warning', _l('invoice_reminder_send_problem'));
        }
        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    /* Generates invoice PDF and senting to email of $send_to_email = true is passed */
    public function pdf($id)
    {
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }

        $canView = user_can_view_invoice($id);
        if (!$canView) {
            access_denied('Invoices');
        } else {
            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {
                access_denied('Invoices');
            }
        }

        $invoice        = $this->invoices_model->get($id);
        $invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);
        $invoice_number = format_invoice_number($invoice->id);
		
		if($invoice->template_id!=""){
			$this->load->model('template_list_model');
			$invoice->templates = (array)$this->template_list_model->get($invoice->template_id);
		}

        try {
            $pdf = invoice_pdf($invoice);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
    }
    
    public function custom_fields(){
$whereCF = ['show_on_pdf' => 1];
if (is_custom_fields_for_customers_portal()) {
$whereCF['show_on_client_portal'] = 1;
}
return get_custom_fields($this->type(), $whereCF);
}

public function type(){
 return 'invoice';
 }
	
public function printpdf_all(){
	$mpdfConfig='';
require 'vendor/autoload.php';
$mpdf =  new Mpdf\Mpdf();
$pdfids=$this->input->post('pdfs');
$news=explode(',',$pdfids);

$i=0; 

foreach($news as $id){
        if (!$id) {

            redirect(admin_url('invoices/list_invoices'));

        }
        $canView = user_can_view_invoice($id);

        if (!$canView) {

            access_denied('Invoices');

        } else {

            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {

                access_denied('Invoices');

            }

        }



        $invoice        = $this->invoices_model->get($id);

        $invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);
    //  print "<pre>";print_r($invoice); echo '<br>----------------------------';echo '<br>';//exit;
        $invoice_number = format_invoice_number($invoice->id);

		

		if($invoice->template_id!=""){

			$this->load->model('template_list_model');

			$invoice->templates = (array)$this->template_list_model->get($invoice->template_id);

		}
		

        try {
			$this->invoice_number=format_invoice_number($invoice->id);
			$this->clientid=$invoice->clientid;
			$this->load->model('payment_modes_model');
			$payment_modes = $this->payment_modes_model->get();

        // In case user want to include {invoice_number} or {client_id} in PDF offline mode description
        foreach ($payment_modes as $key => $mode) {
            if (isset($mode['description'])) {
                $payment_modes[$key]['description'] = str_replace('{invoice_number}', $this->invoice_number, $mode['description']);
                $payment_modes[$key]['description'] = str_replace('{client_id}', $this->clientid, $mode['description']);
            }
        }
			 
			 $items = get_items_table_data($invoice, 'invoice', 'pdf');
$font_size = 8;
$tblhtml = $items->table();
$html = <<<HTML
$tblhtml
HTML;

			 
			
$payment_modes=$payment_modes;//$invoice->get_payment_modes();
$status=$invoice->status;



$info_right_column='';
$info_right_column .= '<span style="font-weight:bold;font-size:18px;" font-family:freeserif">' . _l('invoice_pdf_heading') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $invoice_number . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>';
}

if ($status != Invoices_model::STATUS_PAID && $status != Invoices_model::STATUS_CANCELLED && get_option('show_pay_link_to_invoice_pdf') == 1
    && found_invoice_mode($payment_modes, $invoice->id, false)) {
    $info_right_column .= ' - <a style="color:#84c529;text-decoration:none;text-transform:uppercase;" href="' . site_url('invoice/' . $invoice->id . '/' . $invoice->hash) . '"><1b>' . _l('view_invoice_pdf_link_pay') . '</1b></a>';
}




$organization_info = '<div style="color:#424242; font-family:freeserif">';

$organization_info .= format_organization_info();

$organization_info .= '</div>';

$organization_info = hooks()->apply_filters('invoicepdf_organization_info', $organization_info, $invoice);


// Bill to
$invoice_info = '<table width="100%" style="font-size:14px;"><tr><td width="50%" ><div style="font-size:14px;"><b>' . _l('invoice_bill_to') . ':</b>';
$invoice_info .= '<div style="color:#424242;">';
$invoice_info .= format_customer_info($invoice, 'invoice', 'billing');
$invoice_info .= '</div></div></td><td width="50%"></td><td width="50%" style="float: inline-end;font-size:14px; text-align:left">';

// ship to to
if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) {
    $invoice_info .= '<div style=""><b>' . _l('ship_to') . ':</b>';
    $invoice_info .= '<div style="color:#424242;">';
    $invoice_info .= format_customer_info($invoice, 'invoice', 'shipping');
    $invoice_info .= '</div></div>';
}

$invoice_info .= '</td><tr><td width="50%"><div style="font-size:14px;">';
$invoice_info .= '<br />' . _l('invoice_data_date') . ' ' . _d($invoice->date) . '<br />';

$invoice_info = hooks()->apply_filters('invoice_pdf_header_after_date', $invoice_info, $invoice);

if (!empty($invoice->duedate)) {
    $invoice_info .= _l('invoice_data_duedate') . ' ' . _d($invoice->duedate) . '<br />';
    $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_due_date', $invoice_info, $invoice);
}

if ($invoice->sale_agent != 0 && get_option('show_sale_agent_on_invoices') == 1) {
    $invoice_info .= _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent) . '<br />';
    $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_sale_agent', $invoice_info, $invoice);
}

if ($invoice->project_id != 0 && get_option('show_project_on_invoice') == 1) {
    $invoice_info .= _l('project') . ': ' . get_project_name_by_id($invoice->project_id) . '<br />';
    $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_project_name', $invoice_info, $invoice);
}

$invoice_info = hooks()->apply_filters('invoice_pdf_header_before_custom_fields', $invoice_info, $invoice);
$swap =get_option('swap_pdf_info');
$pdf_custom_fields=$this->custom_fields();
foreach($pdf_custom_fields as $field) {
    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
    if ($value == '') {
        continue;
    }
    $invoice_info .= $field['name'] . ': ' . $value . '<br />';
}

$invoice_info .= '</td><td width="50%"></td><td width="50%" style="float: inline-end;">'.$organization_info.'</td></tr></table>';


$invoice_info      = hooks()->apply_filters('invoice_pdf_header_after_custom_fields', $invoice_info, $invoice);
$invoice_info      = hooks()->apply_filters('invoice_pdf_info', $invoice_info, $invoice);

//$invoice_info .='<br />';
$left_info  = $swap == '1' ? $invoice_info : $organization_info;
$right_info = '';//$swap == '1' ? $organization_info : $invoice_info;

//$invoice_info .= '<td width="50%"></td></tr></table>';
$items_html='';
$items_html .= '<b>'._l('invoice_note').'</b><br>';
$items_html .= $invoice->clientnote;
$items_html .= '<br><br><b>'._l('Terms & Conditions:').'</b><br>';
$items_html .= $invoice->terms;

$tbltotal = '';
$tbltotal .= '<table cellpadding="6" width="100%" style="font-size:' . ($font_size + 4) . 'px">';
$tbltotal .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->subtotal, $invoice->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($invoice)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_discount');
    if (is_sale_discount($invoice, 'percent')) {
        $tbltotal .= ' (' . app_format_number($invoice->discount_percent, true) . '%)';
    }
    $tbltotal .= '</strong>';
    $tbltotal .= '</td>';
    $tbltotal .= '<td align="right" width="15%">-' . app_format_money($invoice->discount_total, $invoice->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td>
</tr>';
}

if ((int) $invoice->adjustment != 0) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . _l('invoice_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->adjustment, $invoice->currency_name) . '</td>
</tr>';
}

$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($invoice->total, $invoice->currency_name) . '</td>
</tr>';

if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', [
        'field' => 'amount',
        'where' => [
            'invoiceid' => $invoice->id,
        ],
    ]), $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('applied_credits') . '</strong></td>
        <td align="right" width="15%">-' . app_format_money($credits_applied, $invoice->currency_name) . '</td>
    </tr>';
}

if (get_option('show_amount_due_on_invoice') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) {
    $tbltotal .= '<tr style="background-color:#f0f0f0;">
       <td align="right" width="85%"><strong>' . _l('invoice_amount_due') . '</strong></td>
       <td align="right" width="15%">' . app_format_money($invoice->total_left_to_pay, $invoice->currency_name) . '</td>
   </tr>';
}

$tbltotal .= '</table>';





$signatureImage = get_option('signature_image');

 $Signature='<div style=margin:10px;>
Authorized Signature<br>
<img src="'.APP_BASE_URL.'uploads/company/'.$signatureImage.'">
</div>';

$info_left_column='';
$info_left_column .= pdf_logo_url();
$header= '<table width="100%"><tr>  
<td width="5%"><span style="width:200px;height:100px">'.$info_left_column.'</span></td>
<td width="95%"></td>
</tr></table>';
$image='<div style="height:200px;width:280px;">'.$info_left_column.'</div>';
//echo $left_info;
//exit();
 //print_r(pdf_logo_url());
$mpdf->AddPage();
$mpdf->WriteHTML($image);
//$mpdf->WriteHTML($info_right_column);
$mpdf->WriteHTML($left_info);
$mpdf->WriteHTML($right_info);

$mpdf->WriteHTML($tblhtml); //Table
$mpdf->WriteHTML($tbltotal);
//$mpdf->WriteHTML('<br>');
//$mpdf->WriteHTML('<div style="font-family:dejavusanscondensed">&#x20b9;</div>');
//$mpdf->SetXY(107, 30);
//$mpdf->WriteCell(1, 1, 'Note', '', '', 'R');
$mpdf->WriteHTML($items_html);
//$mpdf->WriteHTML('<br>');
$mpdf->WriteHTML($Signature);

//  $pdf = invoice_pdf($invoice);
 
        } catch (Exception $e) {

            $message = $e->getMessage();

            echo $message;

            if (strpos($message, 'Unable to get the size of the image') !== false) {

                show_pdf_unable_to_get_image_size_error();

            }

            die;

        }
		
}
$mpdf->Output();
exit();
      /*  $type = 'D';



        if ($this->input->get('output_type')) {

            $type = $this->input->get('output_type');

        }



        if ($this->input->get('print')) {

            $type = 'I';

        }



        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
*/
	}

    public function mark_as_sent($id)
    {
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }
        if (!user_can_view_invoice($id)) {
            access_denied('Invoice Mark As Sent');
        }

        $success = $this->invoices_model->set_invoice_sent($id, true);

        if ($success) {
            set_alert('success', _l('invoice_marked_as_sent'));
        } else {
            set_alert('warning', _l('invoice_marked_as_sent_failed'));
        }

        redirect(admin_url('invoices/list_invoices/' . $id));
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('invoice_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }
}