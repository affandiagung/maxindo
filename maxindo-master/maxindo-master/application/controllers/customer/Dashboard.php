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
				// 'controller' => "pelanggan",
				'total' => $this->db->get("customers")->num_rows()
			),
			'totalLead' => array(
				'icon' => 'fa fa-user',
				'nama' => 'Total Lead',
				// 'controller' => "lap_pemasukan",
				'total' => $this->db->where("LEADDATE >=", $tglAwal)->where("LEADDATE <=", $tglAkhir)->where("PROJECTSTAGE <",4)->where("PROJECTSTAGE <>",0)->get("projects")->num_rows()
			),
			'totalProject' => array(
				'icon' => 'fa fa-home',
				'nama' => 'Total Project',
				// 'controller' => "lap_pengeluaran",
				'total' => $this->db->where("DATE(PROJECTSTART) >=", $tglAwal)->where("DATE(PROJECTSTART) <=", $tglAkhir)->get("projects")->num_rows()
			),
			'totalProjectGagal' => array(
				'icon' => 'fa fa-home',
				'nama' => 'Total Project',
				// 'controller' => "lap_pengeluaran",
				'total' => $this->db->where("DATE(PROJECTSTART) >=", $tglAwal)->where("DATE(PROJECTSTART) <=", $tglAkhir)->where("PROJECTSTAGE",0)->get("projects")->num_rows()
			),
			'totalIncome' => array(
				'icon' => 'fas fa-dollar-sign',
				'nama' => 'Total Income',
				// 'controller' => "lap_tunggakan",
				'total' => $this->Mmasterdata->getTotalIncome( $tglAwal, $tglAkhir )
			)
		);
		$this->data['TGLAWAL'] = $tglAwal;
		$this->data['TGLAKHIR'] = $tglAkhir;
		$this->load->view('customer/dashboard',$this->data);
	}
}
