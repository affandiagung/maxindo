<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

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
		if( !isset($logged['EMAIL']) ){
			$this->load->view("public/login", $this->data);
		}
		else {
			$basedir = $this->Mmasterdata->getHomePage( $logged['PRIVILEGE'] );
			redirect($basedir);
		}
	}

	function doLogin(){
		$security_code = $this->session->userdata("security_code");
		$post = $this->input->post();
		if( count($post) > 0 ){
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$this->db->where("(EMAIL = ".$this->db->escape($username)." OR USERID=".$this->db->escape($username).")");
			$this->db->where('PASSWD',md5($password));
			// $this->db->where('USERSTATUS',1);

			$result = $this->db->select("users.*, privileges.KUNCILEVEL")->join("privileges", "PRIVILEGE=PRIVILEGEID", "LEFT")->get('users')->result_array();
			if( count($result) > 0){
				unset($result[0]['PASSWD']);
				if( $result[0]['USERSTATUS'] == "1"){
					$this->session->set_userdata('admin', $result[0]);
					$basedir = $this->Mmasterdata->getHomePage( $result[0]['PRIVILEGE'] );
					echo "<div class='kt-alert kt-alert--outline alert alert-success'>Login Berhasil ... Halaman otomatis redirect</div>
						<script type='text/javascript'>
							setTimeout(\"window.location.href=\'".$basedir."\'\",3000);
						</script>
					";
				} else {
					echo "<div class='kt-alert kt-alert--outline alert alert-danger'>User belum aktif, silahkan cek email Anda.</div>";
					echo "<script type='text/javascript'>
					        $('#captcha_container').load(site_url + 'login/loadCaptcha');
								</script>";
				}
				// }
			}
			else {
				echo "<div class='kt-alert kt-alert--outline alert alert-danger'>Username / password salah.</div>";
				echo "<script type='text/javascript'>
				        $('#captcha_container').load(site_url + 'login/loadCaptcha');
							</script>";
			}

		} else {
			echo "<script type='text/javascript'>
			        window.location.href= '".site_url()."/logout/';
						</script>";
		}
	}
}
