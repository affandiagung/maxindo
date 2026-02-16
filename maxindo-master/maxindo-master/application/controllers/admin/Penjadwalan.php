<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjadwalan extends CI_Controller {

	private $data = array();

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->data['projects'] = $this->db->where("PROJECTSTAGE >= ",4)->where("PROJECTSTAGE <>", 0)->get("projects")->result();
		$this->load->view("admin/penjadwalan", $this->data);
	}

}

/* End of file Penjadwalan.php */
/* Location: ./application/controllers/admin/Penjadwalan.php */