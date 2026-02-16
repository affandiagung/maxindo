<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_inventory extends CI_Controller{
	
	private $params = array();
	private $logged = array();
	private $projectname = "";

	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->logged = $this->session->userdata("admin");
		$this->getparams();
	}
	
	function index(){
		$this->browse();
	}
	

	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall,generate";
		if( $this->logged['PRIVILEGE'] == "EMP" ){
			$this->params['command'] = "browse";
		}
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project_inventory";
		$this->params['table'] = "projectinventories";
		$this->params['sql'] = "SELECT PROJECTINVENTORYID,
		inventories.NAME as INVENTORY,
		projects.NAME as PROJECT,
		projectinventories.QTY,
		inventorydetails.BARCODE,
		inventories.IMAGE
		FROM projectinventories
		LEFT JOIN projects ON PROJECTID = PROJECT
		LEFT JOIN inventorydetails ON projectinventories.INVENTORYDETAIL = INVENTORYDETAILID
		LEFT JOIN inventories ON INVENTORYID = inventorydetails.INVENTORY
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."' 
		";
		$this->params['query-total'] = "SELECT SUM(COST) as COST, SUM(DISCOUNT) as DISCOUNT, SUM(FINALCOST) as FINALCOST
		FROM projectquotations WHERE PROJECT='".$this->session->userdata("PROJECTID")."'";

		$this->params['cmd'] = array(
			'print_head' => array(
				'url' => "#",
				'icon' => "fa fa-print"
			)
		);
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
	}

	function generate(){

	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'PROJECTINVENTORYID' => array(
				'class' => "sorting",
				'type' => "primarykey",
				'hidden' => true,
			),
			'IMAGE' => array(
				'class' => "sorting",
				'type' => "image",
				'width' => "100px"
			),
			'INVENTORY' => array(
				'class' => "sorting",
			),
			'BARCODE' => array(
				'class' => "sorting"
			),
			'QTY' => array(
				'class' => "sorting"
			),
			'COST' => array(
				'class' => "sorting",
				'type' => "number",
			),	
			'DESCRIPTION' => array(
				'class' => "sorting",
			),
		);
		if( $this->logged['PRIVILEGE'] == "EMP" ){
			unset($this->params['fieldselect']['#']);
			unset($this->params['fieldselect']['COST']);
		}
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTINVENTORYID' => array(
				'class' => "col-md-6",
				'type' => "primarykey",
				'hidden' => true,
			),
			// 'INVENTORY' => array(
			// 	'class' => "col-md-6 select2",
			// 	'validation' => "required",
			// 	'type' => "dropdownquery",
			// 	'sourcequery' => blankoption($this->Mmasterdata->getInventory()),
			// ),
			'INVENTORYDETAIL' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInventoryDetail()),
			),
			'QTY' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'COST' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'DESCRIPTION' => array(
				'class' => "col-md-6",
				'type' => "textarea",
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
			$('#DISCOUNT').blur(function(){
				let cost  =  eval( $('#COST').val() );
				let discount  =  eval( $('#DISCOUNT').val() );
				$('#FINALCOST').val(cost-discount);
			});
			$('#INVENTORY').change(function(){
				let target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getInventory")."';
				let datapost = {
					INVENTORYID: $(this).val()
				}
				$.post(target, datapost, function(e){
					$('#COST').val( e.RENTALPRICE );
					$('#DISCOUNT').val( 0 );
					$('#FINALCOST').val( e.RENTALPRICE );
				},'json');
			});
		</script>";
	}

	function getInventory(){
		$id = $this->input->post("INVENTORYID");
		$inv = $this->db->where("INVENTORYID", $id)->get("inventories")->row();
		echo json_encode($inv);
	}

	function add(){
		$this->jsinclude();
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['UPDATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
	function delete(){
		$delete=$this->db->delete($this->params['table'],array($this->urisegments['pk'] => urldecode($this->urisegments['valpk'])));
		if($delete){
			echo "<script>
				loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."');
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
			loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
		</script>";
	}
	
}
