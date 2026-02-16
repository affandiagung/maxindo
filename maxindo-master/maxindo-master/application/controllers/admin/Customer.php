<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller{
	
	private $params = array();
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall,kontak,dokumen";
		$this->params['maincontent'] = "customer";
		$this->params['name'] = $this->lang->line("customer");
		$this->params['table'] = "customers";
		$this->params['cmd']['kontak'] = array(
			'url' => "javascript:loadmodal(\"".site_url() . "/admin/customercontact/index/[urlparams]\")",
			'icon' => "fa fa-credit-card text-danger"
		);
		$this->params['cmd']['dokumen'] = array(
			'url' => "javascript:loadmodal(\"".site_url() . "/admin/customerdocument/index/[urlparams]\")",
			'icon' => "fa fa-file text-info"
		);
		$this->params['sql'] = "SELECT CUSTOMERID,
		customers.NAME,
		CONCAT(ADDRESS,'<br />',districts.NAME,'<br />', cities.NAME,'<br />', provinces.NAME) as ADDRESS,
		-- CONCAT('P : ', PHONE,'<br />E : ',EMAIL) as CONTACT,
		-- CONCAT(CONTACTNAME,'<br />',CONTACTMOBILE) as CP, 
		REGISTERDATE, customertypes.NAME CUSTOMERTYPE, CUSTOMERCOORDINATE,
		CREATEBY, UPDATEBY, CREATEAT, UPDATEAT
		FROM customers
		LEFT JOIN provinces ON PROVINCE=PROVINCEID
		LEFT JOIN cities ON CITY=CITYID
		LEFT JOIN districts ON DISTRICT=DISTRICTID
		LEFT JOIN customertypes ON CUSTOMERTYPE = CUSTOMERTYPEID
		";

		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "loadpopup(\"".site_url( $this->router->fetch_directory() . "customer_detail/index/[urlparams]" ) . "\"".");";
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
			'CUSTOMERID' => array(
				'class' => "sorting",
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => "sorting",
				'type' => "",
			),
			'ADDRESS' => array(
				'class' => "sorting",
				'type' => "",
			),
			'CUSTOMERTYPE' => array(
				'class' => "sorting",
				'type' => "",
			),
			'REGISTERDATE' => array(
				'class' => "sorting",
				'type' => "date",
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'CUSTOMERID' => array(
				'class' => "col-md-6",
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => "col-md-6",
				'type' => "",
				'validation' => "required",
			),
			'ADDRESS' => array(
				'class' => "col-md-6",
				'type' => "textarea",
			),
			'PROVINCE' => array(
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => array_merge(array(0 => array("keydt" => "", "valuedt" => "- Pilih Provinsi -")),$this->Mmasterdata->getProvince()),
			),
			'CITY' => array(
				'class' => "col-md-6",
				'type' => "dropdownquery",
				'sourcequery' => array(0 => array("keydt" => "", "valuedt" => "-")),
			),
			'DISTRICT' => array(
				'class' => "col-md-6",
				'type' => "dropdownquery",
				'sourcequery' => array(0 => array("keydt" => "", "valuedt" => "-")),
			),
			'PHONE' => array(
				'class' => "col-md-6",
				'type' => "",
			),
			'EMAIL' => array(
				'class' => "col-md-6",
				'type' => "email",
			),
			/*'CONTACTNAME' => array(
				'class' => "col-md-6",
			),
			'CONTACTMOBILE' => array(
				'class' => "col-md-6",
			),*/
			'CUSTOMERLOCATION' => array(
				'class' => "col-md-6",
			),
			'CUSTOMERCOORDINATE' => array(
				'class' => "col-md-6",
				'type' => "map",
				'address_id' => "CUSTOMERLOCATION"
			),
			'CUSTOMERTYPE' => array(
				'class' => "col-md-6",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getCustomertype()),
			),
			'REGISTERDATE' => array(
				'class' => "col-md-6",
				'type' => "date",
				'value' => date('Y-m-d'),
				'validation' => "required"
			),
			'CREATEBY' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'CREATEAT' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'UPDATEBY' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
			),
			'UPDATEAT' => array(
				'class' => "col-md-6",
				'type' => "text",
				'disabled' => true,
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

	function add(){
		if (!empty($_POST)) {
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		if (!empty($_POST)) {
			$_POST['UPDATEBY'] = $this->logged['USERID'];
		}
		$this->jsinclude();
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

	function jsinclude(){
		$config = $this->Mmasterdata->getConfiguration();
		$this->params['jsinclude'] = "<script type='text/javascript'>
		$(function(){
			$('#PROVINCE').change(function(){
				let target = site_url + 'welcome/getCity/';
				let datapost = {
					PROVINCE: $(this).val()
				}
				$.post(target, datapost, function( e ){
					$('#CITY').html( e );
					if( $('#CITY').attr('data-value') != '' ){
						$('#CITY').val( $('#CITY').attr('data-value') );
						$('#CITY').change();
					}
					$('#CITY').select2().trigger('change');
					});
					});

					$('#CITY').change(function(){
						let target = site_url + 'welcome/getDistrict/';
						let datapost = {
							CITY: $(this).val()
						}
						$.post(target, datapost, function( e ){
							$('#DISTRICT').html( e );
							if( $('#DISTRICT').attr('data-value') != '' ){
								$('#DISTRICT').val( $('#DISTRICT').attr('data-value') );
								$('#DISTRICT').change();
							}
							$('#DISTRICT').select2().trigger('change');
							});
							});

							$('#DISTRICT').change(function(){
								let target = site_url + 'welcome/getVillage/';
								let datapost = {
									DISTRICT: $(this).val()
								}
								$.post(target, datapost, function( e ){
									$('#VILLAGE').html( e );
									if( $('#VILLAGE').attr('data-value') != '' ){
										$('#VILLAGE').val( $('#VILLAGE').attr('data-value') );
									}
									$('#VILLAGE').select2().trigger('change');
									});
									});

									if( $('#PROVINCE').attr('data-value') !=''){
										$('#PROVINCE').change();
									}
									});
									</script>";
								}

								function getCity(){
									$kec = $this->uri->segment(4);
									$final = "<option value=''> - Pilih Kelurahan - </option>";
									$data = $this->db->select("VILLAGEID as keydt, NAME as valuedt")
									->where("DISTRICT", $this->input->post("DISTRICT"))
									->order_by('NAME','ASC')
									->get("villages")
									->result_array();
									foreach($data as $value){
										$final .= "<option value='".$value['keydt']."'>".$value['valuedt']."</option>";
									}
									echo $final;
								}

							}
