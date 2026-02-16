<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;


class Inventory_detail extends CI_Controller{
	
	private $params = array();
	private $projectname = "";
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		if( isset($this->urisegments['valpk']) ){
		      $this->session->set_userdata('inventory_detail_where', $this->uri->uri_to_assoc(4) );
		}
		if( !$this->session->has_userdata("inventory_detail_where") ){
		      echo "Invalid parameters !";exit;
		}
		$this->browse();
	}
	

	function getparams(){
		$this->params['command'] = "browse,add,edit,delete,deleteall,cetak_all";
		$this->params['name'] = $this->lang->line("inventorydetail");
		$this->params['maincontent'] = "inventorydetails";
		$this->params['simpleform'] = true;
		$this->params['table'] = "inventorydetails";
		$this->params['sql'] = "SELECT INVENTORYDETAILID,
		inventorydetails.QTY,
		inventorydetails.OWNER,
		inventorydetails.PURCHASEDATE,
		inventorydetails.WARRANTY,
		inventories.NAME INVENTORY, 
		inventorydetails.BARCODE, 
		inventoryconditions.NAME as INVENTORYCONDITION,
		inventorydetails.NOTES,
		CONCAT('Beli : ',IFNULL(inventorydetails.PURCHASEPRICE,'-'),'<br>Jual : ',IFNULL(inventorydetails.SELLINGPRICE,'-'),'<br> Rental : ',IFNULL(inventorydetails.RENTALPRICE,'-')) AS PRICE,
		BUYDATE, suppliers.NAME SUPPLIER FROM inventorydetails
		LEFT JOIN inventories ON INVENTORYID = INVENTORY
		LEFT JOIN inventoryconditions ON INVENTORYCONDITION=INVENTORYCONDITIONID
		LEFT JOIN suppliers ON SUPPLIERID = SUPPLIER
		WHERE 1 
		";
		$this->params['cmd'] = array(
			'cetak_all' => array(
				'icon' => "fa fa-print",
				'url' => "javascript:printall(\"inventorydetails-content\",\"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() )."/cetak_all/\",$(\".checkbox-data--inventorydetails-content\"),\"print\");"
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
			'INVENTORYDETAILID' => array(
				'class' => "sorting",
				'type' => "primarykey",
				'hidden' => true,
			),
			'BARCODE' => array(
				'class' => "sorting",
			),
			'PRICE' => array(
				'class' => "sorting",
				'type' => "",
			),
			"QTY" => array(
				'class' => "sorting",
				'type' => "",
			),
			'PURCHASEDATE' => array(
				'class' => "sorting",
				'type' => "date",
			),
			'INVENTORYCONDITION' => array(
				'class' => "sorting",
			),
			'OWNER' => array(
				'class' => "sorting",
			),
			'NOTES' => array(

			)
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'INVENTORYDETAILID' => array(
				'class' => "col-md-6",
				'type' => "primarykey",
				'hidden' => true,
			),
			'BARCODE' => array(
				'class' => "col-md-6",
			),
			'PURCHASEPRICE' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'RENTALPRICE' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'SELLINGPRICE' => array(
				'class' => "col-md-6",
				'type' => "number",
			),
			'PURCHASEDATE' => array(
				'class' => "col-md-6",
				'type' => "date",
				'value' => date('Y-m-d'),
			),
			'SUPPLIER' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getSupplier()),
			),
			'INVENTORYCONDITION' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInventoryCondition()),
			),
			'NOTES' => array(
				'class' => "col-md-6",
				'type' => "textarea",
			),
		);
	}
	
	function getData(){
		$where = $this->session->userdata()['inventory_detail_where']['pk']." = ".$this->session->userdata()['inventory_detail_where']['valpk'];
		$this->params['sql'] .=  " AND ".$where;
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "Inventory : " . $this->Mmasterdata->getInventoryNameById($this->session->userdata()['inventory_detail_where']['valpk'])
		);
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			
		</script>";
	}

	function add(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "Inventory : " . $this->Mmasterdata->getInventoryNameById($this->session->userdata()['inventory_detail_where']['valpk'])
		);
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['INVENTORY'] = $this->session->userdata()['inventory_detail_where']['valpk'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->params['alert'] = array(
			'type' => "primary",
			'message' => "Inventory : " . $this->Mmasterdata->getInventoryNameById($this->session->userdata()['inventory_detail_where']['valpk'])
		);
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['INVENTORY'] = $this->session->userdata()['inventory_detail_where']['valpk'];
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

	function cetak_all(){
		$post = $this->input->post();
		echo "<div class='row'>";
		foreach($post as $key => $value){
			if($value == true){
				$id = explode("-", $key);
				$pk = $id[1];
				$val = $id[2];
				$inventory_detail = $this->db->where("INVENTORYDETAILID", $val)->get("inventorydetails")->row();
				$barcode = new BarcodeGenerator();
				$barcode->setText($inventory_detail->BARCODE);
				$barcode->setType(BarcodeGenerator::Code128);
				$barcode->setScale(2);
				$barcode->setThickness(25);
				$barcode->setFontSize(10);
				$code = $barcode->generate();

				echo "<div class='col-md-3 border border-dark text-center p-3'><img src='data:image/png;base64,".$code."' /></div>";
			}
		}
		echo "</div>";
	}
	
}
