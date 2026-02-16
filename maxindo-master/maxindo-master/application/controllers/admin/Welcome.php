<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	private $data = array();
	private $logged = array();

	public function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->logged = $this->session->userdata("admin");
	}

	public function index(){
		if( !isset($this->logged['USERID']) ){
			redirect(site_url("adminlogin"));
		}
		else {
			// Check Foto
			$this->data['imgprofile'] = base_url("assets/admin") . "/img/avatars/male.png";
			$this->data['module'] = "default";
			$users = $this->db->where("USERID", $this->logged['USERID'])->get("users")->result();
			$this->data['user'] = $users[0];
			if( $users[0]->PHOTO != "" ){
				$this->data['imgprofile'] = base_url("uploads/" . $users[0]->PHOTO);
			}
			$this->load->view('admin/index',$this->data);
		}
	}

	function getNotif(){
		// Get Unprocess
		$result = array();
		// if( $this->logged['PRIVILEGE'] == "KAB" ){
		// 	$this->db->where("farmers.CITY", $this->logged['IDREF']);
		// 	$result = $this->db->where("CITYAPPROVAL", 0)->join("farmers", "FARMER=FARMERID", "LEFT")->get("fieldcertificates")->result_array();
		// }	elseif($this->logged['PRIVILEGE'] == "ADM"){
		// 	$result = $this->db->where("PROVINCEAPPROVAL", 0)->get("fieldcertificates")->result_array();
		// }
		// $notif['pendingNotif'] = count($result) . " pending";
		// if( count($result) > 0 ){
		// 	$notif['badge_administrasi'] = count($result);
		// }
		// echo json_encode($notif);
	}

	function getDistrict(){
		$post = $this->input->post();
		$city = $post['CITY'];
		$districts = $this->db->where("CITY", $city)->get("districts")->result();
		$result = "<option value=''>- Pilih Kecamatan -</option>";
		foreach($districts as $dis){
			$result .= "<option value='".$dis->DISTRICTID."'>".$dis->NAME."</option>";
		}
		echo $result;
	}

	function getVillage(){
		$post = $this->input->post();
		$district = $post['DISTRICT'];
		$villages = $this->db->where("DISTRICT", $district)->get("villages")->result();
		$result = "<option value=''>- Pilih Desa -</option>";
		foreach($villages as $vil){
			$result .= "<option value='".$vil->VILLAGEID."'>".$vil->NAME."</option>";
		}
		echo $result;
	}

	function topContent(){
    	
  }

  function countdown_timer(){
    	
  }
}
