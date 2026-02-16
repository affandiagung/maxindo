<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Database extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}
	
	public function index(){
		$this->load->view("admin/database");
	}

	function backup(){
		$this->load->dbutil();

		// Backup your entire database and assign it to a variable
		$prefs = array(
      'format'        => 'zip',                       // gzip, zip, txt
      'filename'      => "db_".$this->db->database."_".date("Y-m-d") . ".sql.zip", // File name - NEEDED ONLY WITH ZIP FILES
      'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
      'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
      'newline'       => "\n"                         // Newline character used in backup file
    );

		// $this->dbutil->backup($prefs);
		$backup = $this->dbutil->backup($prefs);

		// Load the file helper and write the file to your server
		$this->load->helper('file');
		write_file( FCPATH . "/backup/" . $prefs["filename"], $backup);

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download($prefs["filename"], $backup);
	}

}

/* End of file Database.php */
/* Location: ./application/controllers/admin/Database.php */