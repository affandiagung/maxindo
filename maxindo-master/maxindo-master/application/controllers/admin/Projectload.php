<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projectload extends CI_Controller {

	private $data = array();

	public function index(){
		$projectLoad = $this->db->select(
			"PROJECTID as id, CONCAT(PROJECTLOCATION,' - ',PROJECTROOM) as location, 
			DATE_FORMAT(SETUPDATE,'%Y-%m-%dT%H:%i:%s') as start, 
			DATE_FORMAT(DISPLACEDATE,'%Y-%m-%dT%H:%i:%s') as end, 
			CONCAT(PROJECTORDERCODE,' - ',customers.NAME) as title, 
			PROJECTNOTES as description, 
			'fc-event-solid-warning' as className"
		)
		->where("PROJECTSTAGE >=", 4)
		->join("customers", "CUSTOMER=CUSTOMERID", "LEFT")
		->get("projects")->result();
		$this->data['projectLoad'] = $projectLoad;
		$this->data['jsonProjectLoad'] = json_encode($projectLoad);
		$this->load->view('admin/projectload',$this->data);
	}

}

/* End of file Projectload.php */
/* Location: ./application/controllers/admin/Projectload.php */