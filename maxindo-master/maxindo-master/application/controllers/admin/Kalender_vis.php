<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kalender_vis extends CI_Controller {

	private $data = array();
	private $logged = array();

	public function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->logged = $this->session->userdata("admin");
	}


	public function index(){
		$this->load->view('admin/tl',$this->data);
	}




}
