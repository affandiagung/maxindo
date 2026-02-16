<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_popup extends CI_Controller{
	
	private $params = array();
	private $province = "";
	private $visi = array();
	
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
		$this->urisegments=$this->uri->uri_to_assoc(4);
		if( isset($this->urisegments['trigger']) ){
			$this->session->set_userdata("trigger", $this->urisegments['trigger']);
		}
		$this->params['simpleform'] = true;
		$this->params['maincontent'] = "customer_popup";
		$this->params['command'] = "browse,add,edit";
		$this->params['name']=$this->lang->line( $this->router->fetch_class() );
		$this->params['table']="customers";
		$this->params['sql'] = "SELECT CUSTOMERID, customers.NAME, customers.ADDRESS, PHONE, EMAIL, provinces.NAME as PROVINCE, cities.NAME as CITY, districts.NAME as DISTRICT,
		customers.CREATEBY, customers.UPDATEBY, customers.CREATEAT, customers.UPDATEAT
		FROM customers
		LEFT JOIN provinces ON PROVINCE=PROVINCEID
		LEFT JOIN cities ON CITY=CITYID
		LEFT JOIN districts ON DISTRICT=DISTRICTID
		";
		$this->getfieldselect();
		$this->getfieldedit();
		
		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "setCustomer(\"".$this->session->userdata("trigger")."\",\"[id]\");";

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
			),
			'ADDRESS' => array(
				'class' => "sorting",
				'type' => "textarea",
			),
			'PROVINCE' => array(
				'class' => "sorting",
			),
			'CITY' => array(
				'class' => "sorting",
			),
			'DISTRICT' => array(
				'class' => "sorting",
			),
			'PHONE' => array(
				'class' => "sorting",
			),
			'EMAIL' => array(
				'class' => "sorting",
				'type' => "email",
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
			'REGISTERDATE' => array(
				'class' => "col-md-6",
				'type' => "date",
				'value' => date('Y-m-d'),
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
		$this->jsinclude();
		if (!empty($_POST)) {
			$_POST['CREATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->jsinclude();
		if (!empty($_POST)) {
			$_POST['UPDATEBY'] = $this->logged['USERID'];
		}
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
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
					$('#CITY').select2({
						dropdownParent: $('#modalInputContainer')
					}).trigger('change');
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
					$('#DISTRICT').select2({
					dropdownParent: $('#modalInputContainer')
				}).trigger('change');
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
					$('#VILLAGE').select2({
				dropdownParent: $('#modalInputContainer')
			}).trigger('change');
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