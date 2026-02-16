<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_pencapaian_bulanan extends CI_Controller{
	
	private $params = array();
	
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		$this->browse();
	}
	
	function getparams(){
		$this->params['command'] = "browse,export,search";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['table'] = "projects";
		$this->params['sql'] = "SELECT PROJECTID,
		employees.NAME as EMPLOYEE,
		FINALAMOUNT, TOTAMOUNT, DISCOUNTAMOUNT,
		PROJECTSTAGE,
		SUM(IF(PROJECTSTAGE<> 0,FINALAMOUNT,0)) as LEAD,
		SUM(IF(PROJECTSTAGE>=4,FINALAMOUNT,0)) as DEAL,
		SUM(IF(PROJECTSTAGE=0,FINALAMOUNT,0)) as CANCEL,
		(SUM(IF(PROJECTSTAGE>=4,FINALAMOUNT,0)) / SUM(IF(PROJECTSTAGE<> 0,FINALAMOUNT,0))) * 100 as PERSENDEAL,
		(SUM(IF(PROJECTSTAGE=0,FINALAMOUNT,0)) / SUM(IF(PROJECTSTAGE<> 0,FINALAMOUNT,0))) * 100 as PERSENCANCEL,
		PROJECTLOCATION
		FROM projects
		LEFT JOIN employees ON EMPLOYEE=EMPLOYEEID
		LEFT JOIN projectstages ON PROJECTSTAGE=PROJECTSTAGEID
		WHERE 1
		";

		$this->params['query-total'] = "SELECT SUM(IF(PROJECTSTAGE<> 0,FINALAMOUNT,0)) as LEAD,
		SUM(IF(PROJECTSTAGE>=4,FINALAMOUNT,0)) as DEAL,
		SUM(IF(PROJECTSTAGE=0,FINALAMOUNT,0)) as CANCEL
		FROM projects
		LEFT JOIN employees ON EMPLOYEE=EMPLOYEEID
		LEFT JOIN projectstages ON PROJECTSTAGE=PROJECTSTAGEID
		WHERE 1";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();

		$this->params['search'] = array(
			'TGLAWAL' => array(
				'type' => "date",
				'value' => date("Y-m-1")
			),
			'TGLAKHIR' => array(
				'type' => "date",
				'value' => date("Y-m-d")
			)
		);
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'PROJECTID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'EMPLOYEE' => array(
				'class' => 'sorting',
				'sumtitle' => true
			),
			'LEAD' => array(
				'class' => 'sorting table-primary',
				'type' => "number",
				'width' => "200px",
				'sum' => true
			),
			'DEAL' => array(
				'class' => 'sorting table-success',
				'type' => "number",
				'width' => "200px",
				'sum' => true
			),
			'CANCEL' => array(
				'class' => 'sorting table-danger',
				'type' => "number",
				'width' => "200px",
				'sum' => true
			),
			'PERSENDEAL' => array(
				'class' => 'sorting',
				'type' => "number",
				'width' => "200px",
				'rtag' => " %"
			),
			'PERSENCANCEL' => array(
				'class' => 'sorting',
				'type' => "number",
				'width' => "200px",
				'rtag' => " %"
			),
		);
	}
	
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$awal = date("Y-m-1");
		$akhir = date("Y-m-d");
		if( count($_POST) > 0 ){
			if( isset($_POST['TGLAWAL']) && isset($_POST['TGLAKHIR']) ){
				$awal = $_POST['TGLAWAL'];
				$akhir = $_POST['TGLAKHIR'];
			}
		}
		$_POST["p_search"] = " AND LEADDATE >= '".$awal."' AND LEADDATE <= '".$akhir."' GROUP BY EMPLOYEEID";
		$this->params['order'] = "EMPLOYEE";
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			
		</script>";
	}
	
}
