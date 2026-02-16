<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gen_quotation extends CI_Controller{
	
	private $params = array();
	private $projectname = "";
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		if( isset($this->urisegments['valpk']) ){
		      $this->session->set_userdata('gen_quotation_where', $this->uri->uri_to_assoc(4) );
		}
		if( !$this->session->has_userdata("gen_quotation_where") ){
		      echo "Invalid parameters !";exit;
		}
		$this->browse();
	}
	

	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall,print_head";
		$this->params['name'] = $this->lang->line("gen_quotation");
		$this->params['maincontent'] = "gen_quotations";
		$this->params['table'] = "projectquotations";
		$this->params['sql'] = "SELECT PROJECTQUOTATIONID,
		projects.NAME PROJECT, 
		inventories.NAME INVENTORY, 
		FINALCOST, COST, DISCOUNT, DESCRIPTION FROM projectquotations
		LEFT JOIN projects ON PROJECTID = PROJECT
		LEFT JOIN inventories ON INVENTORYID = INVENTORY
		WHERE 1 
		";
		$this->params['query-total'] = "SELECT SUM(COST) as COST, SUM(DISCOUNT) as DISCOUNT, SUM(FINALCOST) as FINALCOST
		FROM projectquotations WHERE 1";

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
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'PROJECTQUOTATIONID' => array(
				'class' => "sorting",
				'type' => "primarykey",
				'hidden' => true,
			),
			'INVENTORY' => array(
				'class' => "sorting",
			),
			'DESCRIPTION' => array(
				'class' => "sorting",
				'sumtitle' => true
			),
			'COST' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true
			),
			'DISCOUNT' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true
			),	
			'FINALCOST' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTQUOTATIONID' => array(
				'class' => "col-md-6",
				'type' => "primarykey",
				'hidden' => true,
			),
			'INVENTORY' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInventory()),
			),
			'INVENTORYGROUP' => array(
				'class' => "col-md-6",
				'type' => "",
			),
			'COST' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'DISCOUNT' => array(
				'class' => "col-md-6",
				'type' => "number",
			),			
			'FINALCOST' => array(
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
		$where = $this->session->userdata()['gen_quotation_where']['pk']." = ".$this->session->userdata()['gen_quotation_where']['valpk'];
		$this->params['sql'] .=  " AND ".$where;
		$this->params['query-total'] .=  " AND ".$where;
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "PROYEK : " . $this->Mmasterdata->getProjectNameById($this->session->userdata()['gen_quotation_where']['valpk'])
		);
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
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "PROYEK : " . $this->Mmasterdata->getProjectNameById($this->session->userdata()['gen_quotation_where']['valpk'])
		);
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata()['gen_quotation_where']['valpk'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "PROYEK : " . $this->Mmasterdata->getProjectNameById($this->session->userdata()['gen_quotation_where']['valpk'])
		);
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata()['gen_quotation_where']['valpk'];
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
