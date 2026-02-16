<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

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
		$this->load->view("public/register", $this->data);
	}

	function doRegister(){
		$post = $this->input->post();
		if($post['PASSWORD'] != $post["CONFIRM"]){
			echo "<div class='kt-alert kt-alert--outline alert alert-danger'>Ulang Password Tidak Sesuai</div>";
		} else {
			// Check Email
			$check = $this->db->where("EMAIL", $post['EMAIL'])->or_where("MOBILE", $post['MOBILE'])->get("users")->result();
			if( count($check) > 0 ){
				echo "<div class='kt-alert kt-alert--outline alert alert-danger'>Email Atau No. HP Sudah digunakan</div>";
			} else {
				// Insert
				$dataInsert = array(
					"NIK" => $post['NIK'],
					"EMAIL" => $post['EMAIL'],
					"NAME" => $post['NAME'],
					"MOBILE" => $post['MOBILE']
				);
				$insert = $this->db->insert("farmers", $dataInsert);
				// $id = $this->db->insert_id();
				$vcode = md5($post['EMAIL']);
				$dataUpdate = array(
					"PASSWD" => md5($post['PASSWORD']),
					"VERIFICATIONCODE" => $vcode,
					"USERSTATUS" => 0,
				);
				$update = $this->db->update("users", $dataUpdate, array("EMAIL" => $post['EMAIL']));
				if($insert && $update ){
					$config = $this->Mmasterdata->getConfiguration();
					$msg = "
					<p>
				    Hallo ".$post['NAME'].", <br /><br />

				    Terima kasih telah mendaftarkan diri di ".$config->APP_NAME.". 
				    Berikut ini adalah detail pendaftaran Anda.
				    <br /><br />
				    Email : ".$post['EMAIL']."<br />
				    Password : ".$post['PASSWORD']."<br />

				    <br />
				    Silahkan klik tautan berikut ini untuk konfirmasi Pendaftaran Anda<br /><br />
				    <a style='text-decoration:none; color: #fff; background-color: #2B6698; font-size: 12pt; padding: 12px;' href='".site_url("confirmation/index/" . $vcode)."'> Konfirmasi Pendaftaran </a>
				  </p>
					";
					// $msg = $this->load->view("mail", $this->data, true);
					// print_r($msg);exit;
					$send = $this->Mmasterdata->sendEmail($post['EMAIL'],"Pendaftaran Petani", $msg);
					echo "<div class='kt-alert kt-alert--outline alert alert-success'>Pendaftaran Berhasil, Silahkan cek email Anda ... Halaman otomatis redirect</div>
						<script type='text/javascript'>
							setTimeout(\"window.location.href=\'".site_url("login")."\'\",3000);
						</script>
					";
				} else {

				}
			}
		}
	}
}
