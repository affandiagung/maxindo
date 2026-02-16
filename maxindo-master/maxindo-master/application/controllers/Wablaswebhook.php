<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wablaswebhook extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	public function index(){
		header("Content-Type: text/plain");
		$content = json_decode(file_get_contents('php://input'), true);

		/*$id = $content['id'];
		$pushName = $content['pushName'];
		$isGroup = $content['isGroup'];
		if ($isGroup == true) {
	    $subjectGroup = $content['group']['subject'];
	    $ownerGroup = $content['group']['owner'];
	    $decriptionGroup = $content['group']['desc'];
	    $partisipanGroup = $content['group']['participants'];
		}
		$message = $content['message'];
		$phone = $content['phone'];
		$messageType = $content['messageType'];
		$file = $content['file'];
		$mimeType = $content['mimeType'];
		$deviceId = $content['deviceId'];
		$sender = $content['sender'];
		$timestamp = $content['timestamp'];*/

		$message = $content['message'];
		$balasan = explode("<~", $message);
		// Check apabila itu adalah pesan balasan dari tombol
		if( isset($balasan[1]) ){
			// echo "Ini adalah pesan balasan";
			// Ambil Kode Pesanan
			preg_match('#\[(.*?)\]#', $message, $match);
			if( isset($match[1]) ){
				$kode = $match[1];
				$project = $this->Mmasterdata->getProjectDetailbyKode( $kode );
				if( trim(strtolower($balasan[1])) == "setujui" ){
					// Update Project jadi setujui
					$this->db->update("projects", array("APPROVALSTATUS" => 1), array("PROJECTORDERCODE" => $kode));
					echo "Anda telah menyetujui Quotation Project [".$kode."]";
					$target = $project->MOBILE;
					// $target = "082225566148";
					$phone = preg_replace('/\D+/', '', $target);
					$balasan = "Quotation Project dengan kode [".$kode."] DISETUJUI oleh Manager Marketing";
					$this->Mmasterdata->sendWAblas( $phone, $balasan);
				} elseif( trim(strtolower($balasan[1])) == "tolak"){
					$this->db->update("projects", array("APPROVALSTATUS" => 2), array("PROJECTORDERCODE" => $kode));
					echo "Masukkan alasan Anda menolak Quotation Project [".$kode."] dengan reply pesan ini :";
				} else {
					// Berarti memasukkan alasan menolak
					$this->db->update("projects", array("REJECTREASON" => trim($balasan[1])), array("PROJECTORDERCODE" => $kode));
					echo "Alasan penolakan berhasil disimpan";
					// Kirim Notifikasi ke Marketing
					$target = $project->MOBILE;
					// $target = "082225566148";
					$phone = preg_replace('/\D+/', '', $target);
					$alasan = "Quotation Project dengan kode [".$kode."] Ditolak manager Marketing dengan alasan : " . $balasan[1];
					$this->Mmasterdata->sendWAblas( $phone, $alasan);
				}
			} else {
				// echo "Project Tidak Valid";
			}
		} else {
			// Kemungkinan alasan Menolak

			// echo "Format Tidak Valid";
		}

		// echo $message;
	}

}

/* End of file Wablaswebhook.php */
/* Location: ./application/controllers/Wablaswebhook.php */