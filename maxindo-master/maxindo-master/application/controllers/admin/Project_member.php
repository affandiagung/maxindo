<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_member extends CI_Controller{
	
	private $params = array();
	private $logged = array();
	private $projectname = "";

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
		$this->params['command'] = "browse,add,edit,delete,deleteall";
		$this->params['maincontent'] = "project_member";
		if( $this->logged['PRIVILEGE'] == "EMP" || $this->logged['PRIVILEGE'] == "MRK"){
			$this->params['command'] = "browse";
		}
		if( $this->logged['PRIVILEGE'] == "ADM" ){
			$this->params['command'] .= ",sendnotifmember_head";
			$this->params['cmd']['sendnotifmember_head'] = array(
				'icon' => "fab fa-whatsapp",
				'url' => "javascript:loadcontent(\"".$this->params['maincontent']."-content\",\"".site_url("admin/Project_member/sendNotifmember/")."\")"
			);
		}
		$this->params['name'] = $this->lang->line( $this->router->fetch_class() );
		$this->params['table'] = "projectmembers";
		$this->params['sql'] = "SELECT PROJECTMEMBERID,
		employees.NAME as EMPLOYEE,
		projectunits.NAME as PROJECTUNIT,
		jobpositions.NAME as JOBPOSITION,
		projectmembers.NOTES, FEE,
		projectmembers.CREATEBY, projectmembers.UPDATEBY, projectmembers.CREATEAT, projectmembers.UPDATEAT
		FROM projectmembers
		LEFT JOIN employees ON projectmembers.EMPLOYEE=EMPLOYEEID
		LEFT JOIN projectunits ON projectmembers.PROJECTUNIT=PROJECTUNITID
		LEFT JOIN jobpositions ON projectmembers.JOBPOSITION=JOBPOSITIONID
		WHERE PROJECT='".$this->session->userdata("PROJECTID")."' 
		";
		$this->params['query-total'] = "SELECT SUM(FEE) as FEE
		FROM projectmembers WHERE PROJECT='".$this->session->userdata("PROJECTID")."'";
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
			'PROJECTMEMBERID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'PROJECTUNIT' => array(
				'class' => "sorting",
				'width' => "200px"
			),
			'EMPLOYEE' => array(
				'class' => "sorting",
				'width' => "250px"
			),
			'NOTES' => array(
				'class' => "sorting",
			),
			'FEE' => array(
				'class' => "sorting",
				'type' => "number",
				'sum' => true,
				'width' => "100px"
			),	
		);
		if( $this->logged['PRIVILEGE'] == "EMP" ){
			unset($this->params['fieldselect']['#']);
			unset($this->params['fieldselect']['FEE']);
		}
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'PROJECTMEMBERID' => array(
				'type' => "primarykey",
				'hidden' => true,
			),
			'PROJECTUNIT' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getProjectUnit()),
			),
			'EMPLOYEE' => array(
				'class' => "col-md-6 select2",
				'validation' => "required",
				'type' => "dropdownquery",
				'sourcequery' => blankoption($this->Mmasterdata->getEmployee()),
			),
			'FEE' => array(
				'class' => "col-md-3",
				'type' => "number",
				'validation' => 'required',
				'value' => 0
			),			
			'NOTES' => array(
				'class' => "col-md-6",
				'type' => "textarea",
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

	function jsinclude(){
		$this->params['jsinclude'] = "<script type='text/javascript'>
			$('#EMPLOYEE').change(function(){
				let target = '".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/getEmployee")."';
				let datapost = {
					EMPLOYEEID: $(this).val()
				}
				$.post(target, datapost, function(e){
					
				},'json');
			});
		</script>";
	}

	// function getEmployee(){
	// 	$id = $this->input->post("EMPLOYEEID");
	// 	$emp = $this->db->where("EMPLOYEEID", $id)->get("employees")->row();
	// 	echo json_encode($emp);
	// }

	function add(){
		$this->jsinclude();
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['CREATEBY'] = $this->logged['USERID'];
			$project = $this->db->where("PROJECTID", $_POST['PROJECT'])->get("projects")->row();
			// Check Tabrakan
			$check = $this->db->where("
				  EMPLOYEE = '".$_POST['EMPLOYEE']."' AND PROJECT <> '".$_POST['PROJECT']."' AND
				  (('".$project->SETUPDATE."' <= STARTDATE AND '".$project->CLEARDATE."' <= STARTDATE) OR
				  ('".$project->SETUPDATE."' <= ENDDATE AND '".$project->CLEARDATE."' >= ENDDATE) OR
				  ('".$project->SETUPDATE."' <= STARTDATE AND '".$project->CLEARDATE."' >= ENDDATE) OR
				  ('".$project->SETUPDATE."' >= STARTDATE AND '".$project->CLEARDATE."' <= ENDDATE))
				")
				->get("employeecalendars")->num_rows();
			if( $check > 0 ){
				echo "<script>
					swal.fire({
		        icon: 'error',
		        title: 'Jadwal Tabrakan',
		        text: 'Karyawan ini telah dialokasikan di project lain',
		        showConfirmButton: !1,
		        timer: 1500
		      });
		      loadcontent(\"".$this->params['maincontent']."-content\", \"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/add")."\");
				</script>";
			} else {
				$this->load->library("Engine",$this->params);
				echo $this->engine->add();
			}
		} else {
			$this->load->library("Engine",$this->params);
			echo $this->engine->add();
		}
	}
	
	function edit(){
		$this->getfieldedit();
		if (count($_POST)>0){
			$_POST['PROJECT'] = $this->session->userdata("PROJECTID");
			$_POST['UPDATEBY'] = $this->logged['USERID'];
			$project = $this->db->where("PROJECTID", $_POST['PROJECT'])->get("projects")->row();
			// Check Tabrakan
			$check = $this->db->where("
				  EMPLOYEE = '".$_POST['EMPLOYEE']."' AND PROJECT <> '".$_POST['PROJECT']."' AND
				  (('".$project->SETUPDATE."' <= STARTDATE AND '".$project->CLEARDATE."' <= STARTDATE) OR
				  ('".$project->SETUPDATE."' <= ENDDATE AND '".$project->CLEARDATE."' >= ENDDATE) OR
				  ('".$project->SETUPDATE."' <= STARTDATE AND '".$project->CLEARDATE."' >= ENDDATE) OR
				  ('".$project->SETUPDATE."' >= STARTDATE AND '".$project->CLEARDATE."' <= ENDDATE))
				")
				->get("employeecalendars")->num_rows();
			if( $check > 0 ){
				echo "<script>
					swal.fire({
		        icon: 'error',
		        title: 'Jadwal Tabrakan',
		        text: 'Karyawan ini telah dialokasikan di project lain',
		        showConfirmButton: !1,
		        timer: 1500
		      });
		      loadcontent(\"".$this->params['maincontent']."-content\", \"".site_url( $this->router->fetch_directory() . $this->router->fetch_class() . "/edit/pk/".$this->urisegments['pk']."/valpk/".$this->urisegments['valpk'])."\");
				</script>";
			} else {
				$this->load->library("Engine",$this->params);
				echo $this->engine->edit();
			}
		} else {
			$this->load->library("Engine",$this->params);
			echo $this->engine->edit();
		}
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

	function sendnotifmember(){
		$projectID = $this->session->userdata("PROJECTID");
		$project = $this->Mmasterdata->getProjectDetail( $projectID );
		$members = $this->Mmasterdata->getProjectMember($projectID);

		if (count($members)<1) {
			echo "<script>
				swal.fire({
					icon: 'warning',
					title: 'Isikan data member terlebih dahulu.',
					showConfirmButton: !1,
					timer: 3000
					}).then(function(){
					loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
					});
			</script>";
			exit;
		}
		$memberslist = "";
		foreach ($members as $key => $value) {
			$no=$key+1;
			$memberslist.="
".$no.". ".$value['valuedt']." (".$value['projectunit'].")";
		}
		foreach ($members as $key => $value) {
			$message = "Halo ".$value['valuedt'] .", anda ditugaskan sebagai ".$value['projectunit']." dengan rincian :
kode : ".$project->PROJECTORDERCODE." 
lokasi : ".$project->PROJECTLOCATION." 
client : ".$project->CUSTOMERNAME."
waktu : ".$project->SETUPDATE." s/d ".$project->CLEARDATE."

anggota lengkap : ".$memberslist."
";
			$target = $value['whatsapp'];
	 		$this->Mmasterdata->sendWAblas( $target, $message);
			
		}


		echo "<script>
				swal.fire({
					icon: 'success',
					title: 'Notifikasi WA Berhasil Dikirimkan ke semua member',
					showConfirmButton: !1,
					timer: 1500
					}).then(function(){
					loadcontent('".$this->params['maincontent']."-content','".site_url($this->router->fetch_directory().$this->router->fetch_class())."/browse/');
					});
			</script>";
	}

	
}
