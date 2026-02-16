<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	private $data = array();
	private $logged = array();

	public function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->logged = $this->session->userdata("admin");
	}
	// day(DATETIME) day, month(DATETIME)-1 month, year(DATETIME) year , 
	
	function chart(){
		$d= $this->db->query('SELECT DATETIME tanggal, COUNT(distinct NIK) AS penduduk, count(SURAT_TEMPLATEID) surat
			from transaksi_surat
			GROUP BY DATETIME')->result_array();
		 // header('Content-Type: application/json');
		dd(array_keys($d[0]));

		// echo json_encode($d);
	}

	public function index(){
		$this->data["logged"] = $this->logged;
		$post = $this->input->post();
		$tglAwal = date("Y-m-01");
		$tglAkhir = date("Y-m-t");
		if( isset($post['TGLAWAL']) ){
			$tglAwal = $post['TGLAWAL'];
		}
		if( isset($post['TGLAKHIR']) ){
			$tglAkhir = $post['TGLAKHIR'];
		}
		$this->data['dashboard'] = array(
			'totalCustomer' => array(
				'icon' => 'fa fa-users',
				'nama' => 'Total Customer',
				'controller' => "customer",
				'total' => $this->db->where("REGISTERDATE >=", $tglAwal)->where("REGISTERDATE <=", $tglAkhir)->get("customers")->num_rows()
			),
			'totalLead' => array(
				'icon' => 'fa fa-user',
				'nama' => 'Total Lead',
				'controller' => "project/filter/PROJECTSTAGE/1",
				'total' => $this->db->where("LEADDATE >=", $tglAwal)->where("LEADDATE <=", $tglAkhir)->where("PROJECTSTAGE <",4)->where("PROJECTSTAGE <>",0)->get("projects")->num_rows()
			),
			'totalProject' => array(
				'icon' => 'fa fa-home',
				'nama' => 'Total Project',
				'controller' => "project",
				'total' => $this->db->where("DATE(PROJECTSTART) >=", $tglAwal)->where("DATE(PROJECTSTART) <=", $tglAkhir)->get("projects")->num_rows()
			),
			'totalProjectGagal' => array(
				'icon' => 'fa fa-home',
				'nama' => 'Total Cancel',
				'controller' => "project/filter/PROJECTSTAGE/0",
				'total' => $this->db->where("PROJECTSTAGE",0)->get("projects")->num_rows()
			),
			'totalIncome' => array(
				'icon' => 'fas fa-dollar-sign',
				'nama' => 'Total Income',
				'controller' => "lap_pencapaian_bulanan",
				'total' => $this->Mmasterdata->getTotalIncome( $tglAwal, $tglAkhir )
			)
		);
		if( $this->logged['PRIVILEGE'] == "EMP" ){
			unset($this->data['dashboard']['totalIncome']);
		}
		$this->data['TGLAWAL'] = $tglAwal;
		$this->data['TGLAKHIR'] = $tglAkhir;

		$this->data['TGLINVAWAL'] = date("Y-m-d H:i");
		$this->data['TGLINVAKHIR'] = date("Y-m-d H:i", strtotime( date("Y-m-d H:i") . " + 7 days" ) );
		$this->load->view('admin/dashboard',$this->data);
	}

	function checkAvailability(){

		$post = $this->input->post();
		$tglAwal = date("Y-m-01 00:00");
		$tglAkhir = date("Y-m-t 00:00");
		if( isset($post['TGLAWAL']) ){
			$tglAwal = $post['TGLAWAL'];
		}
		if( isset($post['TGLAKHIR']) ){
			$tglAkhir = $post['TGLAKHIR'];
		}
		$this->data['TGLAWAL'] = $tglAwal;
		$this->data['TGLAKHIR'] = $tglAkhir;
		$this->data['INVENTORY'] =  blankoption($this->Mmasterdata->getInventory());
		if( !empty($post) ){
			if ($post['INVENTORY']=="") {
				echo '<h4 class="text-warning">Mohon Pilih inventory</h4>';
			}
			else{
				$this->getInventoryCalendar($post['INVENTORY'],$post['TGLAWAL'],$post['TGLAKHIR']);
			}
		}	
		else{
			$this->load->view('admin/checkAvailability',$this->data);
		}

	}

	function getInventoryCalendar($inv=null,$startDate=null,$endDate=null){
		$usedInventory = $this->db->select("IFNULL(SUM(USEDCOUNT),0) as USEDCOUNT")->where("
	    INVENTORY = '".$inv."' AND (
		    ('".$startDate."' <= STARTDATE AND '".$endDate."' <= ENDDATE AND '".$startDate."' <= ENDDATE) OR
		    ('".$startDate."' <= STARTDATE AND '".$endDate."' >= ENDDATE) OR
		    ('".$startDate."' >= STARTDATE AND '".$endDate."' >= ENDDATE AND '".$startDate."' <= ENDDATE) OR
		    ('".$startDate."' >= STARTDATE AND '".$endDate."' <= ENDDATE)
	    )
		")->get("inventorycalendars");
		$used = 0;
		if( $usedInventory->num_rows() > 0 ){
			$row = $usedInventory->row();
			$used = $row->USEDCOUNT;
		}
		$inventory = $this->db->where("INVENTORYID", $inv)->get("inventories")->row();
		$total = $inventory->TOTITEM;
		$availability = $total-$used;
		$availability_sqm = $availability/4;
		// $inventory_name = $inventory->INVENTORYNAME;
		$clr = 'success';
		if ($availability<1){
			$clr = "danger";
		}
		echo "<h4 class='text-".$clr."'>Jumlah Tersedia : ". number_format($availability,2,",",".")." / ".number_format($availability_sqm,2,",",".")." m<sup>2</h4>";
	}

	function debug(){
		$this->load->view('admin/debug');
	}


}
