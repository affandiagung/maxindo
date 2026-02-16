<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adminlogin extends CI_Controller {

	private $error = array();
	private $data = array();

	function __construct(){
		parent::__construct();
		$this->load->model("Mmasterdata");
	}

	function loadCaptcha(){
		$this->load->library('captcha');
		$captcha = $this->captcha->main();
		$this->session->set_userdata('security_code', $captcha['code']);
		echo '<img style="height:50px;" src="'.$captcha['image_src'].'" alt="captcha security code" />';
	}

	function index(){
		$logged = $this->session->userdata("admin");
		if( !isset($logged['USERID']) ){
			$this->load->view("admin/login", $this->data);
		}
		else {
			$basedir = $this->Mmasterdata->getHomePage( $logged['PRIVILEGE'] );
			redirect($basedir);
		}
	}

	function doLogin(){
		$security_code = $this->session->userdata("security_code");
		$post = $this->input->post();
		// if( $this->input->post("security_code") != $security_code){
		// 		echo "<div class='alert alert-danger'>Kode Captcha salah.</div>";
		// 		echo "<script type='text/javascript'>
		// 		        $('#captcha_container').load(site_url + 'login/loadCaptcha');
		// 					</script>";
		// } else {
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$this->db->where("(EMAIL = ".$this->db->escape($username)." OR USERID=".$this->db->escape($username).")");
			$this->db->where('PASSWD',md5($password));

			$result = $this->db->select("users.*, privileges.HOMEDIR")->join("privileges", "PRIVILEGE=PRIVILEGEID", "LEFT")->get('users')->result_array();
			if( count($result) > 0){
				unset($result[0]['PASSWD']);
				if($result[0]['PRIVILEGE'] == "MBR"){
					$this->session->set_userdata('login', $result[0]);
				} else {
					$this->session->set_userdata('admin', $result[0]);
				}
				$basedir = $this->Mmasterdata->getHomePage( $result[0]['PRIVILEGE'] );
				echo "success";
			}
			else {
				echo "failed";
			}

		// }
	}
}
