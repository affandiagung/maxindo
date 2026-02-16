<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_popup extends CI_Controller{
	
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
		$this->params['maincontent'] = "vendor_popup";
		$this->params['command'] = "browse,add,edit";
		$this->params['name']=$this->lang->line( $this->router->fetch_class() );
		$this->params['table']="vendors";
		$this->params['sql'] = "SELECT VENDORID,vendors.NAME,cities.NAME AS CITY,ADDRESS, PKP, PPH, PHONE,EMAIL,BUSINESSFIELD
		FROM vendors
		LEFT JOIN cities ON CITYID = CITY
		WHERE 1
		";
		$this->getfieldselect();
		$this->getfieldedit();
		
		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "setVendor(\"".$this->session->userdata("trigger")."\",\"[id]\");";

	}
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'#' => array(

			),
			'VENDORID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'NAME' => array(
				'class' => "sorting",
			),
			'BUSINESSFIELD' => array(
				'class' => "sorting",
				'type' => "function",
				'model' => "Mmasterdata",
				'func' => "showVendorBusinessFiled"
			),
			'PKP' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo(),
			),
			'PPH' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo(),
			),
			'CITY' => array(
				'class' => "sorting",
			),
			'ADDRESS' => array(
				'class' => "sorting",
			),
			'CONTACTNAME1' => array(
				'class' => "sorting",
			),
			'CONTACTPHONE1' => array(
				'class' => "sorting",
			),
			'CONTACTEMAIL1' => array(
				'class' => "sorting",
				'type' => "email",
			),
			/*'PHONE' => array(
				'class' => "sorting",
			),
			'EMAIL' => array(
				'class' => "sorting",
			),*/
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'VENDORID' => array(
				'type' => "primarykey",
				'hidden' => true
			),
			'GENERALINFO'  => array(
				'type' => "separator",
			),
			'NAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
				'maxlength' => "100",
			),
			'BUSINESSFIELD' => array(
				'validation' => "required",
				'multiple' => true,
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => $this->Mmasterdata->getBusinessField(),
			),
			'PKP' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => blankarray($this->Mmasterdata->getYesNo()),
			),
			'PPH' => array(
				'validation' => "required",
				'class' => "col-md-3",
				'type' => "dropdownarray",
				'sourcearray' => blankarray($this->Mmasterdata->getYesNo()),
			),
			'CITY' => array(
				// 'validation' => "required",
				'class' => "col-md-6 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getCity())
			),
			'ADDRESS' => array(
				// 'validation' => "required",
				'class' => "col-md-6",
				'type' => "textarea",
			),
			/*'PHONE' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'EMAIL' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),*/
			/*'CONTACTNAME' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),
			'CONTACTMOBILE' => array(
				'validation' => "required",
				'class' => "col-md-6",
			),*/
			'BANKINFO' => array(
				'type' => "separator",
			),
			'BANKNUMBER1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNAME1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'ACCOUNTNAME1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNUMBER2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNAME2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'ACCOUNTNAME2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNUMBER3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'BANKNAME3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'ACCOUNTNAME3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTINFO' => array(
				'type' => "separator",
			),
			'CONTACTNAME1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTPHONE1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTEMAIL1' => array(
				'validation' => "",
				'class' => "col-md-6",
				'type' => "email",
			),
			'CONTACTJOBPOSITION1' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTNAME2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTPHONE2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTEMAIL2' => array(
				'validation' => "",
				'class' => "col-md-6",
				'type' => "email",
			),
			'CONTACTJOBPOSITION2' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTNAME3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTPHONE3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
			'CONTACTEMAIL3' => array(
				'validation' => "",
				'class' => "col-md-6",
				'type' => "email",
			),
			'CONTACTJOBPOSITION3' => array(
				'validation' => "",
				'class' => "col-md-6",
			),
		);
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		if( count($_POST) > 0 ){
			if( $_POST['BUSINESSFIELD'] != "" ){
				$_POST['p_search'] = " AND FIND_IN_SET('".$_POST['BUSINESSFIELD']."', BUSINESSFIELD)";
			}
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function add(){
		$this->jsinclude();
		if (count($_POST)>0) {
			@$_POST['BUSINESSFIELD'] = implode(',',@$_POST['BUSINESSFIELD']);
		}
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}
	
	function edit(){
		$this->jsinclude();
		if (count($_POST)>0) {
			@$_POST['BUSINESSFIELD'] = implode(',',@$_POST['BUSINESSFIELD']);
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
						dropdownParent: $('#modalContainer')
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
						dropdownParent: $('#modalContainer')
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
						dropdownParent: $('#modalContainer')
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