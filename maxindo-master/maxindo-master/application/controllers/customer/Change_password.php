<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_password extends CI_Controller{
	
	private $params = array();
	
	function __construct(){
		parent::__construct();
		$this->load->model('Mmasterdata');
		$this->getparams();
	}
	
	function index(){
		$this->edit();
	}
	
	function getparams(){
		$this->params['command'] = "browse";
		$this->params['name']=$this->lang->line("change_password");
		$this->params['table']="users";
		$this->params['sqlupdate']="SELECT '".$_SESSION['admin']['USERID']."' as USERID, '' as OLDPASSWD,'' as NEWPASSWD, '' as CONFPASSWD FROM users";
		$this->urisegments=$this->uri->uri_to_assoc(4);
		$this->getfieldedit();
	}
	
	function getfieldedit(){
		$this->params['fieldadd']=array(
			'USERID' => array(
				'hidden' => true,
				'class' => "col-md-2",
				'maxlength' => "25",
				'type' => "primarykey"
			),
			'OLDPASSWD' => array(
				'is_required' => "required",
				'class' => "col-md-3",
				'maxlength' => "50",
				'value' => "",
				'type' => "password"
			),
			'NEWPASSWD' => array(
				'is_required' => "required",
				'class' => "col-md-3",
				'maxlength' => "50",
				'value' => "",
				'type' => "password"
			),
			'CONFPASSWD' => array(
				'is_required' => "required",
				'class' => "col-md-3",
				'maxlength' => "50",
				'value' => "",
				'type' => "password"
			),
		);
	}
	
	
	function browse(){
		$this->edit();
	}
	
	function edit(){
		$this->params['urisegments']['pk'] = 'USERID';
		$this->params['urisegments']['valpk'] = $_SESSION['admin']['USERID'];
		
		if(count($_POST)>0){
			$this->db->where('USERID',$_SESSION['admin']['USERID']);
			$password = $this->db->get('users')->result_array();
			$password = $password[0]['PASSWD'];
			if(md5($this->input->post('OLDPASSWD')) == $password){
				if( $this->input->post('NEWPASSWD') ==  $this->input->post('CONFPASSWD')){
					$upd=$this->db->update('users',array('PASSWD' => md5($this->input->post('NEWPASSWD'))),array('USERID' => $_SESSION['admin']['USERID']));
					$this->params['alert']['type'] = "success";
					$this->params['alert']['message'] = "Password Telah Berhasil Diubah";
					// echo "<div class='alert alert-success'><h2>Sukses</h2><p>Password Telah Berhasil Diubah</p></div>";
				}
				else {
					$this->params['alert']['type'] = "danger";
					$this->params['alert']['message'] = "Password Baru dan Ulangi Password Tidak Sama";
					
					// echo "<div class='alert alert-danger'><h2>Gagal</h2><p>Password Baru dan Ulangi Password Tidak Sama</p></div>";
				}
			}
			else {
				$this->params['alert']['type'] = "danger";
				$this->params['alert']['message'] = "Password Lama Salah";
					
				// echo "<div class='alert alert-danger'><h2>Gagal</h2><p>Password Lama Salah</p></div>";
			}
			$_POST = array();
		}
		$this->load->library("engine",$this->params);
		echo $this->engine->edit();
	}
	
}
?>