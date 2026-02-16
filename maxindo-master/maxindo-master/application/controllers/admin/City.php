<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['table'] = "cities";
		$this->params['sql'] = "SELECT cities.CITYID, cities.NAME, provinces.NAME as PROVINCE 
		FROM cities
		LEFT JOIN provinces ON PROVINCE=PROVINCEID";
		// Check
		$check = $this->db->query($this->params['sql'])->num_rows();
		if($check == 0 ){
			$this->params['command'] .= ",generate";
		}
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
	}

	function generate(){
		$url = "http://180.250.123.54/api/json/ukpbj/kabupaten/bbbd145d89b8b1c7c6396e83f690e364";
		$data = $this->Mmasterdata->callAPI($url, "GET");
		$result = $data["data"];
		foreach($result as $r){
			$dataInsert = array(
				"CITYID" => $r["kbp_id"],
       	"NAME" => $r["kbp_nama"],
       	"PROVINCE" => $r["prp_id"],
      );
      $this->db->insert($this->params['table'], $dataInsert);
		}
		echo "<script>
			loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
		</script>";
		// echo "<pre>";print_r($data);echo "</pre>";
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'CITYID' => array(
				'type' => "primarykey",
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'PROVINCE' => array(
				'class' => "sorting",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'CITYID' => array(
				'type' => "primarykey",
				'validation' => "required",
				'class' => "col-md-6",
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'PROVINCE' => array(
				'validation' => "required",
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProvince())
			),
		);
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			
		</script>";
	}

	function add(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function delete(){
		$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('main-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
			</script>";
		}
	}

	function deleteall(){
		$post = $this->input->post();
		foreach($post as $key => $value){
			if($value == true){
				$id = explode("-", $key);
				$pk = $id[1];
				$val = $id[2];
				$this->db->delete($this->params['table'], array($pk => $val));
			}
		}
		echo "<script>
			loadcontent('engine-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
		</script>";
	}
	
}
?>