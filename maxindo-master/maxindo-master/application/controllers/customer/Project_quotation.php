<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_quotation extends CI_Controller{
	
	private $params = array();
	private $projectname = "";
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		$this->browse();
	}
	

	function getparams(){
		$this->params['command'] = "browse,print_head";
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['maincontent'] = "project_quotation";
		$this->params['table'] = "projectquotations";
		$this->params['sql'] = "SELECT PROJECTQUOTATIONID,
		projects.NAME PROJECT, 
		inventories.NAME INVENTORY, 
		FINALCOST, COST, DISCOUNT, DESCRIPTION FROM projectquotations
		LEFT JOIN projects ON PROJECTID = PROJECT
		LEFT JOIN inventories ON INVENTORYID = INVENTORY
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

}
