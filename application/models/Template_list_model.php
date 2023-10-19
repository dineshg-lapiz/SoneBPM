<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Template_list_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
	public function add_template($data)
    {
        $this->db->insert(db_prefix() . 'templates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Template Added [TemplateID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
        }

        return $insert_id;
    }
	public function get($id = '', $where = [])
    {
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $template = $this->db->get(db_prefix() . 'templates')->row();
            return $template;
        }

        $this->db->order_by('name', 'asc');
        $templates = $this->db->get(db_prefix() . 'templates')->result_array();
        return $templates;
    }
	public function get_templates($type)
    {
        $this->db->where('type', $type);
        //$this->db->order_by('name', 'asc');
        $templates = $this->db->get(db_prefix() . 'templates')->result_array();
        return $templates;
    }
	public function update($id, $data)
    {
        $data = hooks()->apply_filters('before_template_deleted', $data, $id);
        $name = $this->get($id)->name;

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'templates', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Template updated [Name: ' . $name . ']');
            hooks()->do_action('after_template_updated', $id);
            return true;
        }
        return false;
    }
	public function delete($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'templates');
        if ($this->db->affected_rows() > 0) {
            
            log_activity('Template Deleted [TemplateID: ' . $id . ']');

            return true;
        }

        return false;
    }
    public function update_default_template_on_types($id, $type)
    {
		if($type=="invoice"){
			//$this->db->where('addedfrom', get_staff_user_id());
			$this->db->update(db_prefix() . 'invoices', [
                'template_id' => $id,
            ]);
			return true;
		}else if($type=="estimate"){
			//$this->db->where('addedfrom', get_staff_user_id());
			$this->db->update(db_prefix() . 'estimates', [
                'template_id' => $id,
            ]);
			return true;
		}else if($type=="saleorder"){
			//$this->db->where('addedfrom', get_staff_user_id());
			$this->db->update(db_prefix() . 'salesorder', [
                'template_id' => $id,
            ]);
			return true;
		}else if($type=="proposals"){
			//$this->db->where('addedfrom', get_staff_user_id());
			$this->db->update(db_prefix() . 'proposals', [
                'template_id' => $id,
            ]);
			return true;
		}
		return false;
		
		
    }
	public function update_as_default($id, $data, $type)
    {
		$this->db->where('type', $type);
		$this->db->update(db_prefix() . 'templates', [
                            'default_template' => '0',
                        ]);
		
		$this->db->where('id', $id);
        $this->db->update(db_prefix() . 'templates', $data);
		if ($this->db->affected_rows() > 0) {
			return true;
		}
		return false;
    }
    
}
