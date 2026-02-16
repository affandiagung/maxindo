<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	private $error = array();
	private $data = array();
	private $logged = array();
	private $admin = array();

	function __construct(){
		parent::__construct();
		$this->logged = $this->session->userdata("login");
	}

	function index(){
		redirect(site_url("admin"));
		// $this->load->view("public/index", $desa);
	}


	function error404(){
		$this->error['heading'] = "Halaman sedang dalam pengembangan";
		$this->error['message'] = "<p>Pastikan Anda melakukan navigasi melalui menu</p>";
		echo "
		<div class='card'>
		<div class='card-header bg-danger'>
		<div class='card-title text-white'>Error 404</div>
		</div>
		<div class='card-body bg-light-danger'>
		<h1 class='text-danger'>Halaman sedang dalam pengembangan</h1>
		</div>
		</div>";
		// $this->load->view("errors/html/error_404");
	}

	function sendMessage(){
		$post = $this->input->post();
		if(count($post) > 0){
			$insert = $this->db->insert( "messages", $post );
			if( $insert ){
				echo "<div class='alert alert-success'>Pesan berhasil dikirimkan</div>";
			} else {
				echo "<div class='alert alert-danger'>Pesan Gagal dikirimkan</div>";
			}
		}
	}
	function verification( $code ){
		$data = $this->db->where("VERIFICATIONCODE", $code)->get("users")->row();
		if( isset($data->USERSTATUS) && $data->USERSTATUS == 1 ){
			echo "Link sudah kedaluarsa";
		} else {
			$this->db->update("users", array("USERSTATUS" => 1), array("VERIFICATIONCODE" => $code));
			echo "<h3>Selamat anda berhasil verifikasi</h3>";
		}
		echo "<script>setTimeout('window.location.href=\"".site_url("adminlogin")."\";',2500);</script>";
	}

	function getCity(){
		$province = $this->input->post("PROVINCE");
		$cities = $this->db->where("PROVINCE", $province)->get("cities")->result();
		$result = "<option value='' selected>-Pilih Kota-</option>";
		foreach($cities as $city){
			$result .= "<option value='".$city->CITYID."'>".$city->NAME."</option>";
		}
		echo $result;
	}

	function getDistrict(){
		$city = $this->input->post("CITY");
		$districts = $this->db->where("CITY", $city)->get("districts")->result();
		$result = "<option value='' selected>-Pilih Kecamatan-</option>";
		foreach($districts as $district){
			$result .= "<option value='".$district->DISTRICTID."'>".$district->NAME."</option>";
		}
		echo $result;
	}

	function getVillage(){
		$district = $this->input->post("DISTRICT");
		$villages = $this->db->where("DISTRICT", $district)->get("villages")->result();
		$result = "<option value='' selected>-Pilih Desa-</option>";
		foreach($villages as $village){
			$result .= "<option value='".$village->VILLAGEID."'>".$village->NAME."</option>";
		}
		echo $result;
	}
}
