<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Province extends CI_Controller{
	
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
		$this->params['name'] = $this->lang->line("province");
		$this->params['table'] = "provinces";
		$this->params['sql'] = "SELECT PROVINCEID, provinces.NAME
		FROM provinces";
		$this->params['order'] = "PROVINCEID ASC";
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
		$url = "http://180.250.123.54/api/json/ukpbj/propinsi/75f3fc26af67449b0fb02d3fc5b56a26";
		$data = $this->Mmasterdata->callAPI($url, "GET");
		$result = $data["data"];
		foreach($result as $r){
			$dataInsert = array(
				"PROVINCEID" => $r["prp_id"],
       	"NAME" => $r["prp_nama"],
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
			'PROVINCEID' => array(
				'type' => "primarykey",
				'width' => "100px",
				'class' => "sorting",
			),
			'NAME' => array(
				'class' => "sorting",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROVINCEID' => array(
				'type' => "primarykey",
				'validation' => "required",
				'class' => "col-md-6",
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
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