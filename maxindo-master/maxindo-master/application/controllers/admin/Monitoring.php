<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring extends CI_Controller{
	
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
		$this->params['command'] = "browse";
		$this->params['name'] = $this->lang->line("monitoring");
		$this->params['table'] = "projects";
		$this->params['maincontent'] = "monitoring";
		$this->params['sql'] = "
		SELECT 
		projects.*,
		'func' AS INVENTORY,
		'opleader' AS OPLEADER,
		employees.NAME ACCOUNT,
		employees.EMPLOYEEID,
		CONCAT(customers.NAME, '<br />', IF( projects.DRYRENT = 1,'<span class=\"badge badge-danger\">DRY RENT</span>', '')) AS CUSTOMERNAME,
		CONCAT(customercontacts.NAME,'<br />',customercontacts.PHONE) as CUSTOMERCONTACTNAME,
		CONCAT('<span class=\"badge badge-',CLASS,'\">',projectstages.NAME,'</span>') AS PROJECTSTAGENAME,
		projecttypes.NAME AS PROJECTTYPENAME,
		projectstages.NAME as STATUS,
		IF( DATEDIFF(DATE_ADD(projects.UPDATEAT, INTERVAL 24 HOUR),NOW()) > 0,'1','0' ) as BARU,
		IF( SETUPDATE <= NOW() AND DISPLACEDATE >= NOW() ,1,0) as ONGOING
		FROM projects
		LEFT JOIN employees ON employees.EMPLOYEEID = EMPLOYEE
		LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
		LEFT JOIN customercontacts ON projects.CUSTOMERCONTACT = customercontacts.CUSTOMERCONTACTID
		LEFT JOIN projectstages ON projects.PROJECTSTAGE = PROJECTSTAGEID
		LEFT JOIN projecttypes ON projects.PROJECTTYPE = PROJECTTYPEID
		WHERE projects.FINISH = 0 AND PROJECTSTAGE >= 4
		AND DISPLACEDATE >= NOW()
		";
		$this->params['order'] = "ONGOING, SETUPDATE ASC";
		$this->urisegments = $this->uri->uri_to_assoc(4);
		$this->getfieldselect();
		$this->getfieldedit();

		$this->params['rowselect'] = true;
		$this->params['rowcallback'] = "loadpopup(\"".site_url( $this->router->fetch_directory() . "monitoring_detail/index/[urlparams]" ) . "\"".");";
	
		$this->params['customrowcallback'] = "
			if( data.BARU == '1' ){
				$(row).find('td').addClass('table-green');
			}
		";
	}
	
	function getfieldselect(){
		$this->params['fieldselect']=array(
			'SEQ' => array(
				
			),
			'PROJECTID' => array(
				'class' => 'sorting',
				'type' => "primarykey",
				'hidden' => true,
			),
			'SETUPDATE' => array(
				'class' => 'sorting',
				'type' => "datetime",
			),
			'DISPLACEDATE' => array(
				'class' => 'sorting',
				'type' => "datetime",
			),
			'PROJECTDURATION' => array(
				'class' => 'sorting',
				'rtag' => " hari",
			),
			'PROJECTLOCATION' => array(
				'class' => 'sorting',
				'type' => "function",
				'model' => "Mmasterdata",
				'func' => "splitLokasi",
				'params' => "PROJECTLOCATION"
			),
			// 'INVENTORY' => array(
			// 	'class' => 'sorting input',
			// 	'type' => "function",
			// 	'model' => "Mmasterdata",
			// 	'func' => "showInventoryLimit",
			// ),
			'CUSTOMERNAME' => array(
				'class' => 'sorting',
			),
			'ACCOUNT' => array(
				'class' => 'sorting',
			),
			'OPLEADER' => array(
				'class' => 'sorting',
				'type' => "function",
				'model' => "Mmasterdata",
				'func' => "getOpLeader",
			),
			/*'SQM' => array(
				'class' => 'sorting',
				'type' => "function",
				'func' => "getTotalSQM",
				'model' => "Mmasterdata",
				'rtag' => " sqm"
			),*/
			'STATUS' => array(
				'class' => 'sorting',
			),
			'BARU' => array(
				'class' => 'sorting',
				'hidden' => true
			),
		);
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'INVENTORY' => array(
				'class' => 'col-md-6',
				'type' => "primarykey",
				'hidden' => true,
			),
			'NAME' => array(
				'class' => 'col-md-6',
				'type' => "",
			),
			'DESCRIPTION' => array(
				'class' => 'col-md-6',
				'type' => "textarea",
			),
		);
	}
	
	function getData(){
		$this->load->library("Engine",$this->params);
		echo $this->engine->getData();
	}
	
	function browse(){
		$this->jsinclude();
		$this->load->library("Engine",$this->params);
		echo $this->engine->browse();
	}

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			function autorefresh(){
				clearTimeout(timer);
				$('#".$this->params['maincontent']."-content').load( '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() )."' );
			}
			var timer = setTimeout('autorefresh()', 600000);

			function inventoryDetail(args){
				loadpopup(\"".site_url( $this->router->fetch_directory() . "monitoring/inventoryDetail/" ) . "\"+args".");
			}
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

	function inventoryDetail($id=null){
		echo $this->Mmasterdata->showMonitorInventory($id);
	}
	
}
