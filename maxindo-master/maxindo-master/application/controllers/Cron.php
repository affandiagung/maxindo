<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

	private $error = array();
	private $data = array();
	private $logged = array();
	private $admin = array();

	function __construct(){
		parent::__construct();
		$this->logged = $this->session->userdata("login");
	}

	function index(){
		redirect(site_url("admin"));
		// $this->load->view("public/index", $desa);
	}

	function dbg(){
		exit;
		$this->db->set('PROJECTSTAGE',4)->update('projects');

		$project = $this->db->get('projects')->result();
		foreach ($project as $key => $value) {
			$r = rand(07,28);
			$this->db->set('SETUPDATE','2023-04-'.$r)->where('PROJECTID',$value->PROJECTID)->update('projects');
		}

	}

	function monitor(){
		//notifikasi untuk alokasi member h-3
		$bot_limit = date('Y-m-d', strtotime('+4 days'));
		$r = $this->db->query("SELECT 
		projects.*,
		'func' AS INVENTORY,
		'opleader' AS OPLEADER,
		employees.NAME ACCOUNT,
		employees.EMPLOYEEID,
		CONCAT(customers.NAME, IF( projects.DRYRENT = 1,'<br /><span class=\"badge badge-danger\">DRY RENT</span>', '')) AS CUSTOMERNAME,
		CONCAT(customercontacts.NAME,'<br />',customercontacts.PHONE) as CUSTOMERCONTACTNAME,
		CONCAT('<span class=\"badge badge-',CLASS,'\">',projectstages.NAME,'</span>') AS PROJECTSTAGENAME,
		projecttypes.NAME AS PROJECTTYPENAME,
		PROJECTDURATION, projectstages.NAME as STATUS,
		IF( DATEDIFF(DATE_ADD(projects.UPDATEAT, INTERVAL 24 HOUR),NOW()) > 0,'1','0' ) as BARU,
		IF( SETUPDATE <= NOW() AND DISPLACEDATE >= NOW() ,1,0) as ONGOING
		FROM projects
		LEFT JOIN employees ON employees.EMPLOYEEID = EMPLOYEE
		LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
		LEFT JOIN customercontacts ON projects.CUSTOMERCONTACT = customercontacts.CUSTOMERCONTACTID
		LEFT JOIN projectstages ON projects.PROJECTSTAGE = PROJECTSTAGEID
		LEFT JOIN projecttypes ON projects.PROJECTTYPE = PROJECTTYPEID
		WHERE projects.FINISH = 0 AND PROJECTSTAGE >= 4 AND DRYRENT = 0
		AND DATE(SETUPDATE) >= NOW() AND DATE(SETUPDATE) <= '".$bot_limit."'")->result();
		$no = 0;
		if (!empty($r)) {
			foreach ($r as $key => $value) {
				$no++;
				$check_member = $this->db->where('PROJECT',$value->PROJECTID)->get('projectmembers')->num_rows();
				if (!$check_member) {
					$setup = new DateTime($value->SETUPDATE);
					$curr = date('Y-m-d H:i:s');
					$now = new DateTime($curr);
					$diff = $now->diff($setup);
					$hours = $diff->h;
					$hours = $hours + ($diff->days*24);
					if ($hours<=72) {
						$message = "Kode order ".$value->PROJECTORDERCODE.", atas nama client ".$value->CUSTOMERNAME." sudah berada pada tahap deal, dan belum dialokasikan membernya";
						$target = $this->Mmasterdata->getConfigItem("OPERATIONALNUMBER");
					 	$this->Mmasterdata->sendWAblas( $target, $message);
					 	echo $value->PROJECTORDERCODE . " => Terkirim \n"; 
					}

				}
			}
			$this->Mmasterdata->sendWAblas( $target, "Silakan Login ke Aplikasi : " . site_url());
		}
		echo "Terkirim untuk " . $no . " project\n";
	}


	function notifclient(){
		//notifikasi untuk persiapan klien h-1
		$bot_limit = date('Y-m-d', strtotime('-1 days'));
		$r = $this->db->query("SELECT 
		projects.*,
		'func' AS INVENTORY,
		'opleader' AS OPLEADER,
		employees.NAME ACCOUNT,
		employees.EMPLOYEEID,
		CONCAT(customers.NAME, IF( projects.DRYRENT = 1,'<br /><span class=\"badge badge-danger\">DRY RENT</span>', '')) AS CUSTOMERNAME,
		CONCAT(customercontacts.NAME,'<br />',customercontacts.PHONE) as CUSTOMERCONTACTNAME, customercontacts.PHONE CUSTOMERCONTACTPHONE,
		CONCAT('<span class=\"badge badge-',CLASS,'\">',projectstages.NAME,'</span>') AS PROJECTSTAGENAME,
		projecttypes.NAME AS PROJECTTYPENAME,
		PROJECTDURATION, projectstages.NAME as STATUS,
		IF( DATEDIFF(DATE_ADD(projects.UPDATEAT, INTERVAL 24 HOUR),NOW()) > 0,'1','0' ) as BARU,
		IF( SETUPDATE <= NOW() AND DISPLACEDATE >= NOW() ,1,0) as ONGOING
		FROM projects
		LEFT JOIN employees ON employees.EMPLOYEEID = EMPLOYEE
		LEFT JOIN customers ON customers.CUSTOMERID = CUSTOMER
		LEFT JOIN customercontacts ON projects.CUSTOMERCONTACT = customercontacts.CUSTOMERCONTACTID
		LEFT JOIN projectstages ON projects.PROJECTSTAGE = PROJECTSTAGEID
		LEFT JOIN projecttypes ON projects.PROJECTTYPE = PROJECTTYPEID
		WHERE projects.FINISH = 0 AND PROJECTSTAGE = 4 
		AND DATE(SETUPDATE) >= '".$bot_limit."' AND DATE(SETUPDATE) <= NOW()")->result();
		if (!empty($r)) {
			foreach ($r as $key => $value) {
				$check_member = $this->db->where('PROJECT',$value->PROJECTID)->get('projectmembers')->num_rows();
				if (!$check_member) {
					$setup = new DateTime($value->SETUPDATE);
					$curr = date('Y-m-d h:i:s');
					$now = new DateTime($curr);
					$diff = $now->diff($setup);
					$hours = $diff->h;
					$hours = $hours + ($diff->days*24);
					if ($hours<=24) {
						$members = $this->Mmasterdata->getProjectMember($value->PROJECTID);
		$memberslist = "";
		foreach (@$members as $key => $value) {
			$no=$key+1;
			$memberslist.="
".$no.". ".$value['valuedt']." (".$value['projectunit'].")";
		}
						$message = "Informasi untuk order ".$value->PROJECTORDERCODE.", atas nama client ".$value->CUSTOMERNAME.",
dengan rincian :
lokasi : ".$value->PROJECTLOCATION." 
setup : ".$value->SETUPDATE."
bongkar : ".$value->CLEARDATE."

anggota lengkap : ".$memberslist."
";
						$target = $value->CUSTOMERCONTACTPHONE;
					 	$this->Mmasterdata->sendWAblas( $target, $message);
					}

				}
			}
		}
	}

}
