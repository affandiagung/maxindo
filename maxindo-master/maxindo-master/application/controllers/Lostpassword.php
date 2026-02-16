<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Postmark\PostmarkClient;

class Lostpassword extends CI_Controller {

	private $error = array();
	private $data = array();

	function __construct(){
		parent::__construct();
		$this->load->model("Mmasterdata");
		$this->data['message'] = "";
	}

	function index(){
		$post = $this->input->post();
		$config = $this->Mmasterdata->getConfiguration();
		if( count($post) > 0 ){
			$check = $this->db->get_where("users", array("EMAIL" => $post['email']))->result();
			if( count($check) > 0){ // Found
				$user = $check[0];
				$newpasswd = substr(md5(microtime()),4,6);
				// Send Email
				// $client = new PostmarkClient( $this->config->item("postmarkapp_token") );
				// echo "<pre>"; print_r($user); echo "</pre>";
				// exit;
				$msg = "
				<p>
					Hallo ".$user->NAME.", <br /><br />

					Anda telah meminta sistem melakukan reset password pada aplikasi ".$config['APP_NAME'].". 
					Berikut Informasi Login terbaru Anda : <br />
					Email : ".$user->EMAIL."<br />
					Password : ".$newpasswd."<br />
					<br />
	
					Silakan login menggunakan informasi tersebut, Anda dapat mengganti Password Anda sewaktu-waktu.<br /><br />
					Login ke aplikasi melalui : <a href='".site_url("login")."'> Login ".$config['APP_NAME']." </a>
				</p>
				";
				$sendResult = $this->Mmasterdata->sendEmail(
				  $user->EMAIL,
				  $config->APP_NAME." - Reset Password",
				  $msg
				);
				if( !$sendResult ){
					$this->data['message'] = "<div class='kt-alert kt-alert--outline alert alert-danger'>Password Gagal di-reset, Silahkan coba beberapa saat lagi.</div>";
				} else {
					// Update Password
					$this->db->update("users", array('PASSWD' => md5($newpasswd)), array('EMAIL' => $user->EMAIL));
					$this->data['message'] = "<div class='kt-alert kt-alert--outline alert alert-success'>Password Berhasil di Reset, silahkan cek email Anda.</div>";
				}
			} else {
				$this->data['message'] = "<div class='kt-alert kt-alert--outline alert alert-danger'>Email Tidak ditemukan</div>";
			}
			echo $this->data['message'];
		} else {
			$this->load->view("public/lostpassword");
		}
	}
}
