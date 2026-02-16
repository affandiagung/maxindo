<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

class Inventory extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall,inventory_detail,cetak_all,import,disable_all,enable_all";
		$this->params['name'] = $this->lang->line("inventory");
		$this->params['table'] = "inventories";
		$this->params['sql'] = "SELECT 
		INVENTORYID, 
		CONCAT(IFNUll(BRAND,'-'), '<br />', inventories.NAME) as NAME, 
		TIPE,
		inventorytypes.NAME INVENTORYTYPE, PACKAGEDESCRIPTION,
		TRXSTATUS,
		IMAGE, SPECIFICATION, TOTITEM, RENTALPRICE, SELLINGPRICE, SUBRENTCOST 
		FROM inventories
		LEFT JOIN inventorytypes ON inventorytypes.INVENTORYTYPEID = inventories.INVENTORYTYPE
		";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();
		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "javascript:loadmodal(\"".site_url( $this->router->fetch_directory() . "inventory_detail/index/[urlparams]" ) . "\"".");";
		$this->params['cmd'] = array(
			'inventory_detail' => array(
				'url' => "javascript:loadmodal(\"".site_url( $this->router->fetch_directory() . "inventory_detail/index/[urlparams]" ) . "\"".");",
				'icon' => "fa fa-list text-warning",
			),
			'cetak_all' => array(
				'icon' => "fa fa-print",
				'url' => "javascript:printall(\"engine-content\",\"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() )."/cetak_all/\",$(\".checkbox-data--engine-content\"),\"print\");"
			),
			'disable_all' => array(
				'icon' => "fa fa-times text-danger",
				'url' => "javascript:updateall(\"engine-content\",\"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() )."/disable_all/\",$(\".checkbox-data--engine-content\"));"
			),
			'enable_all' => array(
				'icon' => "fa fa-check text-success",
				'url' => "javascript:updateall(\"engine-content\",\"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() )."/enable_all/\",$(\".checkbox-data--engine-content\"));"
			)
		);
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'INVENTORYID' => array(
				'type' => "primarykey",
				'width' => "100px",
				'hidden' => true,
			),
			'IMAGE' => array(
				'class' => "sorting",
				'type' => "image",
				'width' => "100px"
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'TIPE' => array(
				'class' => "sorting",
			),
			'SPECIFICATION' => array(
				'class' => "sorting",
			),
			'INVENTORYTYPE' => array(
				'class' => "sorting",
			),
			'TOTITEM' => array(
				'class' => "sorting",
			),
			'TRXSTATUS' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo()
			),
			/*'PURCHASEPRICE' => array(
				'type' => "number",
				'ltag' => "Rp. ",
				'class' => "sorting",
			),*/
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'INVENTORYID' => array(
				'hidden' => true,
				'type' => "primarykey"
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'TIPE' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			/*'BARCODE' => array(
				'class' => "col-md-6",
			),*/
			'IMAGE' => array(
				'validation' => "",
				'class' => "col-md-3",
				'type' => "file",
			),
			/*'INVENTORYSTATUS' => array(
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInventoryStatus()),
				'class' => "col-md-6 select2",
				'validation' => "required",
			),*/
			'INVENTORYTYPE' => array(
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInventoryType()),
				'class' => "col-md-6 select2",
				'validation' => "required",
			),
			'DETAIL' => array(
				'type' => "separator",
			),
			'BRAND' => array(
				'class' => "col-md-6",
			),
			'SPECIFICATION' => array(
				'validation' => "",
				'class' => "col-md-6",
				'type' => "textarea",
			),
			'PACKAGEDESCRIPTION' => array(
				'validation' => "",
				'class' => "col-md-6",
				'type' => "textarea",
			),
			'COLOR' => array(
				'class' => "col-md-6",
			),
			'DIMENSION' => array(
				'class' => "col-md-6",
			),
			'QTYPERM' => array(
				'class' => "col-md-6",
				'type' => "number"
			),
			'TOTITEM' => array(
				'class' => "col-md-6",
			),
			'UNITTYPE' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'validation' => "required",
				'sourcequery' => blankoption($this->Mmasterdata->getUnitType())
			),
			'PRICE' =>  array(
				'type' => "separator",
			),
			'PURCHASEPRICE' => array(
				'type' => "number",
				'ltag' => "Rp. ",
				'class' => "col-md-6",
			),
			'ADDITIONALDATA' => array(
				'type' =>  "separator",
			),
			/*'DESCRIPTION' => array(
				'type' => "textarea",
				'class' => "col-md-6",
			),*/
			'TRXSTATUS' => array(
				'class' => "col-md-3 select2",
				'vaclidation' => "required",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo()
			),
			'NOTES' => array(
				'type' => "textarea",
				'class' => "col-md-6",
			),		
		);
	}

	function import(){
		// $truncate = $this->input->post("truncate");
		$truncate = 1;
		/*if ($truncate) {
			$this->db->query("TRUNCATE inventories");
			$this->db->query("TRUNCATE inventorydetails");
			$this->db->query("TRUNCATE inventorytypes");
		}*/
		$files = $_FILES['importFile'];
		$result =
		array(
			'status' => false,
			'message' => "No Imported File"
		);
		if(
			$files['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
			$files['type'] == "application/vnd.ms-excel" ||
			$files['type'] == "application/wps-office.xlsx" ||
			$files['type'] == "application/wps-office.xls"
		){
			$inputFileName = $files['tmp_name'];
			$object = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
			$data = array();
			$c = 1;
			$inventory_id = NULL;
			$datainsert = array();
			$datainsertdetail = array();
			foreach($object->getWorksheetIterator() as $worksheet){
				$highestRow = $worksheet->getHighestRow();
				$inventory = array();
				$inventorydetail = array();

				$name = "";
				for($row=2; $row<=$highestRow; $row++){
					// $n = $worksheet->getCellByColumnAndRow(1, $row)->getCalculatedValue();

					$inventory_id = "";
					$inventory['NAME'] = $worksheet->getCellByColumnAndRow(2, $row)->getValue() . " " . $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$inventory['TIPE'] = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$inventory['BRAND'] = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$inventory['SPECIFICATION'] = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$inventory['DIMENSION'] = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$inventorydetail['QTY'] = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$inventorydetail['BARCODE'] = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$UNIX_DATE = ( (int) $worksheet->getCellByColumnAndRow(11, $row)->getValue() - 25569) * 86400;
					$inventorydetail['PURCHASEDATE'] = gmdate("Y-m-d", $UNIX_DATE);
					$inventorydetail['WARRANTY'] = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
					$inventorydetail['OWNER'] = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
					$inventorydetail['INVENTORYCONDITION'] = $worksheet->getCellByColumnAndRow(14, $row)->getValue() == "Ok" ? "B" : "R";
					if( $name != $inventory['NAME']){
						// Insert ke Inventory
						$name = $inventory['NAME'];

						// Check Category
						$category = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
						$categoryId = "";
						if ($category == "") {
							$category = "Undefined*";
						}
						$cat = $this->db->where("NAME", $category)->get("inventorytypes");
						if( $cat->num_rows() == 0  ){
							$this->db->insert("inventorytypes", array("NAME" => $category));
							$categoryId = $this->db->insert_id();
						} else {
							$data = $cat->row();
							$categoryId = $data->INVENTORYTYPEID;
						}
						$inventory['INVENTORYTYPE'] = $categoryId;
						
						// Download Image
						$image = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
						// $inventory['IMAGE'] = $image;
						if( $image != "" ){
							$image_ex = explode("/",$image);
							$idx = 5;
							foreach($image_ex as $k => $v){
								if($v == "d"){
									$idx = $k + 1;
								}
							}
							$image_id = $image_ex[$idx];
							$inventory['IMAGE'] = $image_id . ".jpg";
							if( !file_exists( FCPATH . "/uploads/".$inventory['IMAGE']) ){
								$download = "wget --no-check-certificate 'https://docs.google.com/uc?export=download&id=".$image_id."' -O ". FCPATH . "/uploads/".$inventory['IMAGE'];
								exec($download);
							}
						}
						$this->db->insert("inventories", $inventory);
						$inventory_id = $this->db->insert_id();

						// Insert ke Inventory Detail
						$inventorydetail['INVENTORY'] = $inventory_id;
						$this->db->insert("inventorydetails", $inventorydetail);
					} else { // hanya masukkan detail saja
						$this->db->insert("inventorydetails", $inventorydetail);
					}
				}
			}
			$result['status'] = true;
			$result['message'] = "Data berhasil di Import";
		} else {
			$result['status'] = false;
			$result['message'] = "Jenis File Salah. file Anda : " . $files['type'];
		}
		// header("Content-Type: application/json");
		echo json_encode($result);
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
		echo "<script type='text/javascript'>

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

	function enable_all(){
		$post = $this->input->post();
		foreach($post as $key => $value){
			if($value == true){
				$id = explode("-", $key);
				$pk = $id[1];
				$val = $id[2];
				$this->db->update($this->params['table'], array("TRXSTATUS" => 1), array($pk => $val));
			}
		}
		echo "<script>
		loadcontent('engine-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
		</script>";
	}

	function disable_all(){
		$post = $this->input->post();
		foreach($post as $key => $value){
			if($value == true){
				$id = explode("-", $key);
				$pk = $id[1];
				$val = $id[2];
				$this->db->update($this->params['table'], array("TRXSTATUS" => 0), array($pk => $val));
			}
		}
		echo "<script>
		loadcontent('engine-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
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
				$inventory = $this->db->where("INVENTORYID", $val)->get("inventories")->row();
				$barcode = new BarcodeGenerator();
				$barcode->setText($inventory->BARCODE);
				$barcode->setType(BarcodeGenerator::Code128);
				$barcode->setScale(2);
				$barcode->setThickness(25);
				$barcode->setFontSize(10);
				$code = $barcode->generate();

				echo "<div class='col-md-3 border border-dark text-center p-3'><img src='data:image/png;base64,".$code."' /><br />".$inventory->NAME."</div>";
			}
		}
		echo "</div>";
	}

}
