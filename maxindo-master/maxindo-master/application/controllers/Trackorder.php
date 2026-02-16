<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trackorder extends CI_Controller {

	private $error = array();
	private $data = array();

	function __construct(){
		parent::__construct();
		$this->load->model("Mmasterdata");
	}

	function loadCaptcha(){
		$this->load->library('captcha');
		$captcha = $this->captcha->main();
		$this->session->set_userdata('security_code', $captcha['code']);
		echo '<img style="height:50px;" src="'.$captcha['image_src'].'" alt="captcha security code" />';
	}

	function index($ordercode = null){
		$this->data['order'] = null;
		if ($ordercode) {
			
			$result = $this->db->query("SELECT PROJECTID,EMPLOYEE,CUSTOMER,PROJECTLOCATION,PROJECTCOORDINATE,LEADDATE,PROJECTSTART,PROJECTEND,PROJECTSTAGE,PROJECTORDERCODE,
										projects.NAME NAME,
										employees.NAME EMPLOYEENAME,
										customers.NAME as CUSTOMERNAME,
										customers.ADDRESS as CUSTOMERADDRESS,
										projectstages.NAME as PROJECTSTAGENAME
										FROM projects
										LEFT JOIN employees ON employees.EMPLOYEEID = EMPLOYEE
										LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
										LEFT JOIN projectstages ON projects.PROJECTSTAGE = PROJECTSTAGEID
										WHERE 1 AND PROJECTORDERCODE = ".$this->db->escape($ordercode)." ")->result_array();
			$this->data['order'] = $result['0'];
		}
		$this->load->view("public/trackorder", $this->data);

	}

	function doTrackorder(){
			$security_code = $this->session->userdata("security_code");
			$ordercode = $this->input->post('ordercode');
			$result = $this->db->query("SELECT PROJECTID FROM projects WHERE PROJECTORDERCODE = ".$this->db->escape($ordercode)." ")->result_array();
			if( count($result) > 0){
				echo "success";
			}
			else {
				echo "failed";
			}

	}

}
