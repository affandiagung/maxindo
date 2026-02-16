<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller{
	
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
		$this->params['command'] = "browse,add,edit,delete,deleteall,employee_contract,employee_file,search,export";
		$this->params['name'] = $this->lang->line("employee");
		$this->params['table'] = "employees";
		$this->params['sql'] = "SELECT 
		EMPLOYEEID,
		EMPLOYEECODE,
		employees.NAME as EMPLOYEENAME,
		CONCAT('<strong>',EMPLOYEECODE,'</strong>','<br />',
		       employees.NAME) as NAME,
		BIRTHDATE,
		MOBILE, EMAIL,
		CONCAT('M : ',MOBILE,'<br />E : ', EMAIL) as CONTACT,
		BIRTHPLACE, PHOTO, 
		units.NAME AS UNIT, 
		jobpositions.NAME AS JOBPOSITION, 
		IF(NPWP <> '',1,0) as NPWP, 
		IF(BPJSTK <> '',1,0) as BPJSTK, 
		IF(BPJSKES <> '',1,0) as BPJSKES, 
		employeestatuses.NAME AS EMPLOYEESTATUS
		FROM employees
		LEFT JOIN units on units.UNITID = employees.UNIT
		LEFT JOIN jobpositions ON jobpositions.JOBPOSITIONID = employees.JOBPOSITION
		LEFT JOIN employeestatuses ON EMPLOYEESTATUSID = EMPLOYEESTATUS
		WHERE 1
		";
		$this->params['order'] = "EMPLOYEEID DESC";
		$this->params['rowselect'] = false;
		$this->params['rowcallback'] = "javascript:loadmodal(\"".site_url( $this->router->fetch_directory() . "employee_contract/index/[urlparams]" ) . "\"".");";
		$this->params['cmd'] = array(
			'employee_contract' => array(
				'url' => "javascript:loadmodal(\"".site_url( $this->router->fetch_directory() . "employee_contract/index/[urlparams]" ) . "\"".");",
				'icon' => "la la-list",
			),
			'employee_file' => array(
				'url' => "javascript:loadmodal(\"".site_url( $this->router->fetch_directory() . "employee_file/index/[urlparams]" ) . "\"".");",
				'icon' => "la la-list",
			),
			'cetak_all' => array(
				'icon' => "fa fa-print",
				'url' => "javascript:printall(\"engine-content\",\"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() )."/cetak_all/\",$(\".checkbox-data--engine-content\"),\"print\");"
			)
		);


		$this->params['search']=array(

			'EMPLOYEESTATUS' => array(
				'class' => " select2 col-md-6",
				'type' => "dropdownquery",
				'sourcequery' => array_merge(array(0 => array('keydt' => "", 'valuedt' => '- Semua -')),$this->Mmasterdata->getEmployeeStatus())
			),

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
			'EMPLOYEEID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'PHOTO' => array(
				'class' => "sorting",
				'type' => "image",
				'width' => "75px"
			),
			'EMPLOYEECODE' => array(
				'class' => "sorting",
			),
			'EMPLOYEENAME' => array(
				'class' => "sorting",
			),
			'MOBILE' => array(
				'class' => "sorting",
			),
			'EMAIL' => array(
				'class' => "sorting",
			),
			'UNIT' => array(
				'class' => "sorting",
			),
			'JOBPOSITION' => array(
				'class' => "sorting",
			),
			'EMPLOYEESTATUS' => array(
				'class' => "sorting",
			),
			'NPWP' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo()
			),
			'BPJSTK' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo()
			),
			'BPJSKES' => array(
				'class' => "sorting",
				'type' => "dropdownarray",
				'sourcearray' => $this->Mmasterdata->getYesNo()
			),

		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'EMPLOYEEID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'PRIVATEDATA' => array(
				'type' => "separator",
			),
			'NAME' => array(
				'class' => "col-md-6",
				'type' => "",
				'validation' =>	 "required",
				'help' => "Sesuai KTP",
			),
			'EMPLOYEECODE' => array(
				'class' => "col-md-3",
				'type' => "",
			),
			'PHOTO' => array(
				'class' => "col-md-3",
				'type' => "file",
			),
			'BIRTHDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
			),
			'BIRTHPLACE' => array(
				'class' => "col-md-3",
				'type' => "",
			),
			'EMPLOYEESTATUS' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployeeStatus()),
			),
			'ADDRESS' => array(
				'class' => "col-md-6",
				'type' => "textarea",
			),
			'PROVINCE' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'sourcequery' => array_merge(array(0 => array("keydt" => "", "valuedt" => "- Pilih Provinsi -")),$this->Mmasterdata->getProvince()),
			),
			'CITY' => array(
				'class' => "col-md-3",
				'type' => "dropdownquery",
				'sourcequery' => array(0 => array("keydt" => "", "valuedt" => "-")),
			),
			'DISTRICT' => array(
				'class' => "col-md-3",
				'type' => "dropdownquery",
				'sourcequery' => array(0 => array("keydt" => "", "valuedt" => "-")),
			),
			'TELP' => array(
				'class' => "col-md-3",
				'type' => "",
			),
			'MOBILE' => array(
				'class' => "col-md-3",
				'type' => "",
			),
			'IDCARDNUMBER' => array(
				'class' => "col-md-3",
				'type' => "",
			),
			'EMAIL' => array(
				'class' => "col-md-3",
				'type' => "email",
			),
			'BANKNUMBER' => array(
				'class' => "col-md-3",
				'type' => "",
			),
			'BANKNAME' => array(
				'class' => "col-md-3",
				'type' => "",
			),
			'UNIT' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getUnit()),
			),
			'JOBPOSITION' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getJobPosition()),
			),
			'JOINDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
				'value' => date('Y-m-d'),
			),
			'ADDITIONALDATA' => array(
				'type' => "separator",
			),
			'PARENTNAME' => array(
				'class' => "col-md-6",
				'type' => "",
			),
			'PARENTADDRESS' => array(
				'class' => "col-md-6",
				'type' => "textarea",
			),
			'RELATIVE' => array(
				'type' => "separator",
			),
			'RELATIVENAME' => array(
				'class' => "col-md-6",
				'type' => "",
			),
			'RELATIVEPHONENUMBER' => array(
				'class' => "col-md-3",
				'type' => "",
			),
			'RELATION' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getRelation()),
			),
			'SUPPORTINGDOCUMENT' =>  array(
				'type' => "separator",
			),
			'IDCARD' => array(
				'class' => "col-md-3",
				'type' => "file",
			),
			'FAMILYCARD' => array(
				'class' => "col-md-3",
				'type' => "file",
			),
			'NPWP' => array(
				'class' => "col-md-6",
			),
			'NPWPFILE' => array(
				'class' => "col-md-3",
				'type' => "file",
			),
			'BPJSTK' => array(
				'class' => "col-md-6",
			),
			'BPJSTKTYPE' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInsurance()),
			),
			'BPJSKES' => array(
				'class' => "col-md-6",
			),
			'BPJSKESTYPE' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInsurance()),
			),
			'INSURANCE' => array(
				'class' => "col-md-3 select2",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getInsurance()),
			),
			'OTHERINSURANCE' => array(
				'class' => "col-md-3",
				'type' => "textarea",
				'placeholder' => "Isi jika ada ansuransi lain"
			),
			/*'DATEINFORMATION' =>  array(
				'type' => "separator",
			),
			'CONTRACTSTARTDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
			),
			'CONTRACTENDDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
			),
			'PROBATIONALSTARTDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
			),
			'PROBATIONALENDDATE' => array(
				'class' => "col-md-3",
				'type' => "date",
			),*/
			'OTHER' =>  array(
				'type' => "separator",
			),
			'NOTES' => array(
				'class' => "col-md-3",
				'type' => "textarea",
			),
			'LASTEDUCATIONGRADE' => array(
				'type' => "",
				'class' => "col-md-3",
			),
		);
}

function getData(){
	$this->load->library("Engine",$this->params);
	echo $this->engine->getData();
}

function browse(){
	$filter = false;
	$where = "";
	if(isset($_POST['EMPLOYEESTATUS']) && $_POST['EMPLOYEESTATUS'] != ""){
		$filter = true;
		$where .= " EMPLOYEESTATUS = ".$this->db->escape($_POST['EMPLOYEESTATUS'])." AND";	
	}
	$s =  substr($where,0,strlen($where)-4);
	if($filter){
		$where = " AND " . substr($where,0,strlen($where)-4);
		$_POST['p_search'] = $where;        
	}
	$this->load->library("Engine",$this->params);
	echo $this->engine->browse();
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

	function add(){
		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->add();
	}

	function edit(){
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

}
