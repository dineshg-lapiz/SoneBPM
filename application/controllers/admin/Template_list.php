<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Template_list extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('template_list_model');
    }

    /* List all leads */
    public function index($id = '')
    {
        close_setup_menu();

        if (!is_staff_member()) {
            access_denied('Leads');
        }

        $data['switch_kanban'] = true;

        if ($this->session->userdata('leads_kanban_view') == 'true') {
            $data['switch_kanban'] = false;
            $data['bodyclass']     = 'kan-ban-body';
        }

        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        $data['summary']  = get_leads_summary();
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
		$data['companies']  = $this->leads_model->get_companies();
        $data['title']    = _l('leads');
        // in case accesed the url leads/index/ directly with id - used in search
        $data['leadid']   = $id;
        $data['isKanBan'] = $this->session->has_userdata('leads_kanban_view') &&
            $this->session->userdata('leads_kanban_view') == 'true';

        $this->load->view('admin/leads/manage_leads', $data);
    }

    public function table()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $this->app->get_table_data('leads');
    }

    // Sources
    /* Manage leads sources */
    public function templates()
    {
        if (!is_admin()) {
            access_denied('Leads Sources');
        }
        $data['sources'] = $this->template_list_model->get();
        $data['title']   = 'List of Templates';
		
		$proposal_merge_fields = [];
        $proposal_merge_fields[] = [
            [
                'name' => 'Items Table',
                'key'  => '{proposal_items}',
            ],
        ];

        $proposal_merge_fields = array_merge($proposal_merge_fields, $this->app_merge_fields->get_flat('proposals', 'other', '{email_signature}'));
		$data['proposal_merge_fields'] = $proposal_merge_fields;
		
		$invoice_merge_fields = [];
        $invoice_merge_fields[] = [
            [
                'name' => 'Items Table',
                'key'  => '{invoice_items}',
            ],
        ];

        $invoice_merge_fields = array_merge($invoice_merge_fields, $this->app_merge_fields->get_flat('invoice', 'other', '{email_signature}'));
		$data['invoice_merge_fields'] = $invoice_merge_fields;
		
		$estimate_merge_fields = [];
        $estimate_merge_fields[] = [
            [
                'name' => 'Items Table',
                'key'  => '{estimate_items}',
            ],
        ];

        $estimate_merge_fields = array_merge($estimate_merge_fields, $this->app_merge_fields->get_flat('estimate', 'other', '{email_signature}'));
		$data['estimate_merge_fields'] = $estimate_merge_fields;
		
		$saleorder_merge_fields = [];
        $saleorder_merge_fields[] = [
            [
                'name' => 'Items Table',
                'key'  => '{saleorder_items}',
            ],
        ];

        $saleorder_merge_fields = array_merge($saleorder_merge_fields, $this->app_merge_fields->get_flat('saleorder', 'other', '{email_signature}'));
		$data['saleorder_merge_fields'] = $saleorder_merge_fields;
		
        $this->load->view('admin/template_list/manage', $data);
    }

    /* Add or update leads sources */
    public function template()
    {
        //print "<pre>";print_r($this->input->post());exit;
		
        if ($this->input->post()) {
			$content = $this->input->post('content', false);
			$content = html_purify($content);
			$data['name']      = $this->input->post('name');
			$data['content']   = $content;
			$data['addedfrom'] = get_staff_user_id();
			$data['type']      = $this->input->post('rel_to');
			$data['id'] = $this->input->post('id');
			
            //$data = $this->input->post();
			
            if (!$this->input->post('id')) {
                $data['datecreated'] = date("Y-m-d h:i:s");
                $id = $this->template_list_model->add_template($data);
				if ($id) {
					set_alert('success', _l('added_successfully', _l('lead_source')));
				}
                
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->template_list_model->update($id, $data);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('lead_source')));
                }
            }
        }
    }
	
	public function set_default_template()
    {
		if($this->input->get('def_temp')=="1"){
			$data["default_template"] = "1";
			$this->template_list_model->update_as_default($this->input->get('temp_id'), $data, $this->input->get('temp_type'));
			$updated = $this->template_list_model->update_default_template_on_types($this->input->get('temp_id'),$this->input->get('temp_type'));
			if($updated){
				/*redirect(admin_url('template_list/templates'));
				set_alert('success', _l('updated_successfully', _l('Default Template')));	*/	
				echo "success";
				die;	
			}
		}
		if($this->input->get('def_temp')=="0"){
			$data["default_template"] = "0";
			$this->template_list_model->update_as_default($this->input->get('temp_id'), $data);
			$updated = $this->template_list_model->update_default_template_on_types('',$this->input->get('temp_type'));
			if($updated){
				/*redirect(admin_url('template_list/templates'));
				set_alert('success', _l('updated_successfully', _l('Default Template')));*/			
				echo "success";
				die;
			}
		}
		
	}
	
	public function get_template()
    {
       
		$template = $this->template_list_model->get($this->input->get('id'));

		echo json_encode($template);
		die;
        
        $this->load->view('admin/includes/templates', $data);
    }

    /* Delete leads source */
    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('Delete Template');
        }
        if (!$id) {
            redirect(admin_url('template_list/templates'));
        }
        $response = $this->template_list_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('template')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('template')));
        }
        redirect(admin_url('template_list/templates'));
    }

}
