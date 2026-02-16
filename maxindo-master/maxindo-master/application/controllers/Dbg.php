<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dbg extends CI_Controller {

	private $error = array();
	private $data = array();
	private $logged = array();
	private $admin = array();

	function __construct(){
		parent::__construct();
		$this->logged = $this->session->userdata("login");
	}

	function index(){
		exit;
		// $date = date('2023-02-08 23:48:32');
		// echo date_to_ID($date,0,1);
		$message = "Pesan Uji \nCoba";
		$this->Mmasterdata->sendWAblas("6285643448875", $message);
	}

	function updateduration(){
		$project = $this->db->get('projects')->result();
		foreach ($project as $key => $value) {
			$this->db->set('DURATION',$value->PROJECTDURATION)->where('PROJECT',$value->PROJECTID)->update('projectquotations');
		}
	}

}
