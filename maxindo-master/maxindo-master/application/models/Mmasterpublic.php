<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mmasterpublic extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	function getLoggedUser( $userid ){
		return $this->db->get_where("users", array("USERID" => $userid) )->result();
	}

	function getSocialMedia(){
		return $this->db->get("socialmedias")->result();
	}

	function getPenduduk( $nik ){
		return $this->db->select("
			tweb_penduduk.*,  
			tweb_penduduk_sex.nama as sex,
			tweb_keluarga.no_kk as nokk,
			CONCAT(IFNULL(a.dusun,' - '),', RW ',IFNULL(a.rw,' '),', RT ',IFNULL(a.rt, ' ')) alamat,
			tweb_penduduk_agama.nama as agama,
			tweb_penduduk_pendidikan_kk.nama as pendidikan_kk,
			tweb_penduduk_pendidikan.nama as pendidikan,
			tweb_penduduk_pekerjaan.nama as pekerjaan,
			tweb_penduduk_kawin.nama as status_kawin,
			tweb_penduduk_warganegara.nama as warganegara,
			tweb_golongan_darah.nama as golongan_darah,
			tweb_status_dasar.nama as status_dasar,
			")
		->where("nik", $nik)
		->or_where("id_ktp", $nik)
		->join("tweb_wil_clusterdesa a", "a.id=tweb_penduduk.id_cluster", "left")
		->join("tweb_penduduk_sex", "tweb_penduduk_sex.id=tweb_penduduk.sex", "left")
		->join("tweb_keluarga", "tweb_penduduk.id_kk=tweb_keluarga.id", "left")
		->join("tweb_penduduk_agama", "tweb_penduduk_agama.id=tweb_penduduk.agama_id", "left")
		->join("tweb_penduduk_pendidikan_kk", "tweb_penduduk_pendidikan_kk.id=tweb_penduduk.pendidikan_kk_id", "left")
		->join("tweb_penduduk_pendidikan", "tweb_penduduk_pendidikan.id=tweb_penduduk.pendidikan_sedang_id", "left")
		->join("tweb_penduduk_pekerjaan", "tweb_penduduk_pekerjaan.id=tweb_penduduk.pekerjaan_id", "left")
		->join("tweb_penduduk_kawin", "tweb_penduduk_kawin.id=tweb_penduduk.status_kawin", "left")
		->join("tweb_penduduk_warganegara", "tweb_penduduk_warganegara.id=tweb_penduduk.warganegara_id", "left")
		->join("tweb_golongan_darah", "tweb_golongan_darah.id=tweb_penduduk.golongan_darah_id", "left")
		->join("tweb_status_dasar", "tweb_status_dasar.id=tweb_penduduk.status_dasar", "left")
		->get("tweb_penduduk")->row();
	}

	function w2pdf($input_path,$output_path){
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			chdir('C:\\Program Files\\LibreOffice\\program');
			$r = shell_exec(' soffice --headless --convert-to pdf '.$input_path.' --outdir '.$output_path.'');
		} else {
			$r = shell_exec('export HOME=/tmp && soffice --headless --convert-to pdf '.$input_path.' --outdir '.$output_path.'');
		}
		return $r;
	}
	
	function getInputer($nik){
		return $this->db->query("SELECT DISTINCT (SELECT nama FROM tweb_penduduk WHERE id = kel.nik_kepala) AS Kepala_Keluarga, u.id, u.alamat_sebelumnya, u.dokumen_pasport, u.tanggal_akhir_paspor, u.akta_perkawinan, u.tanggalperkawinan, u.akta_perceraian, u.tanggalperceraian,  u.tempatlahir, u.tanggallahir, u.akta_lahir, u.nik, u.ayah_nik, u.ibu_nik, v.nama as warganegara,
		CONCAT(u.tempatlahir,', ',DATE_FORMAT(u.tanggallahir, '%d-%c-%Y')) ttl,
		u.tempatlahir,u.tanggallahir,
		u.status,u.status_dasar,u.id_kk,u.nama,u.nama_ayah,u.nama_ibu,a.dusun,
		a.rw,a.rt,d.alamat alamat_field,d.no_kk AS no_kk,
		CONCAT(a.dusun,', RW ',a.rw,', RT ',a.rt) alamat,
		(SELECT DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(`tanggallahir`)), '%Y')+0 FROM tweb_penduduk WHERE id = u.id) AS umur,x.nama AS sex,
		sd.nama AS pendidikan_sedang,
		n.nama AS pendidikan,
		p.nama AS pekerjaan,
		k.nama AS kawin,
		g.nama AS agama,
		m.nama AS gol_darah,
		f.nama AS cacat,
		hub.nama AS hubungan FROM tweb_penduduk u LEFT JOIN tweb_keluarga d ON u.id_kk = d.id 
		LEFT JOIN tweb_wil_clusterdesa a ON u.id_cluster = a.id
		LEFT JOIN tweb_penduduk_pendidikan_kk n ON u.pendidikan_kk_id = n.id 
		LEFT JOIN tweb_penduduk_pendidikan sd ON u.pendidikan_sedang_id = sd.id 
		LEFT JOIN tweb_penduduk_pekerjaan p ON u.pekerjaan_id = p.id 
		LEFT JOIN tweb_penduduk_kawin k ON u.status_kawin = k.id 
		LEFT JOIN tweb_penduduk_sex x ON u.sex = x.id 
		LEFT JOIN tweb_penduduk_agama g ON u.agama_id = g.id 
		LEFT JOIN tweb_penduduk_warganegara v ON u.warganegara_id = v.id 
		LEFT JOIN tweb_golongan_darah m ON u.golongan_darah_id = m.id 
		LEFT JOIN tweb_cacat f ON u.cacat_id = f.id 
		LEFT JOIN tweb_penduduk_hubungan hub ON u.kk_level = hub.id 
		LEFT JOIN tweb_keluarga kel ON u.id_kk = kel.id 
		LEFT JOIN tweb_sakit_menahun j ON u.sakit_menahun_id = j.id
		 WHERE nik = '$nik'")->row();
	}

	function getFamilyUser( $idKK ){
		return $this->db->query("SELECT tweb_penduduk.nama as nama, tweb_penduduk.nik, tweb_penduduk_hubungan.nama as hubungan from tweb_penduduk left join tweb_penduduk_hubungan on tweb_penduduk.kk_level = tweb_penduduk_hubungan.id where tweb_penduduk.id_kk=".$idKK."")->result_array();
	}

	function getFamilybyNik( $nik){
		$return = $this->db->query("SELECT tweb_penduduk.nama as nama, tweb_penduduk.tanggallahir as tanggallahir, tweb_penduduk.tempatlahir as tempatlahir, if (tweb_penduduk.sex = 1, 'Laki-laki', 'Perempuan') as sex, tweb_penduduk.nik nik, tweb_penduduk_hubungan.nama as hubungan from tweb_penduduk left join tweb_penduduk_hubungan on tweb_penduduk.kk_level = tweb_penduduk_hubungan.id where tweb_penduduk.nik=".$nik."")->result_array();
		if ($return) {
			# code...
			return $return;
		}else{
			return null;
		}
	}
	function getbyNik( $nik){
		return $this->db->query("SELECT CONCAT(a.dusun,', RW ',a.rw,', RT ',a.rt)  alamat,
			tweb_penduduk_pekerjaan.nama as pekerjaan, tweb_penduduk_agama.nama as agama, tweb_penduduk.nama as nama, tweb_penduduk.tanggallahir as tanggallahir, tweb_penduduk.tempatlahir as tempatlahir, if (tweb_penduduk.sex = 1, 'Laki-laki', 'Perempuan') as sex, tweb_penduduk.nik nik, tweb_penduduk_hubungan.nama as hubungan 
			from tweb_penduduk
			 left join tweb_penduduk_hubungan on tweb_penduduk.kk_level = tweb_penduduk_hubungan.id 
			 left join tweb_penduduk_pekerjaan on tweb_penduduk.pekerjaan_id = tweb_penduduk_pekerjaan.id 
			 left join tweb_penduduk_agama on tweb_penduduk.agama_id = tweb_penduduk_agama.id 
			 left join tweb_wil_clusterdesa a on tweb_penduduk.id_cluster = a.id
			 WHERE  tweb_penduduk.nik='".$nik."'")->result_array();
	}

	function getSuratGroup(){	
		$warga = null !== $this->session->userdata("nik") ? 0 : 1;
		return $this->db->query("SELECT surat_template.WARGA, surat_group.NAMA NAMA, surat_group.SURAT_GROUPID FROM surat_group_detail 
	left JOIN surat_group on SURAT_GROUP=SURAT_GROUPID 
	left JOIN surat_template on SURAT_TEMPLATEID=surat_template 
	WHERE surat_template.KUNCI= 0 AND surat_template.WARGA=".$warga." GROUP BY surat_group.SURAT_GROUPID")->result();
		// return $this->db->get("surat_group")->result();	
	}
	function getSuratList($grup){
		$warga = null !== $this->session->userdata("nik") ? 0 : 1;
		return $this->db->query("SELECT surat_template.WARGA,  surat_group.NAMA GRUP,surat_template.SURAT_TEMPLATEID, surat_template.NAMA NAMA FROM surat_group_detail 
	left JOIN surat_group on SURAT_GROUP=SURAT_GROUPID 
	left JOIN surat_template on SURAT_TEMPLATEID=surat_template 
	WHERE surat_template.KUNCI= 0 AND surat_template.WARGA=".$warga." AND surat_group.SURAT_GROUPID = ".$grup."")->result();
	}


}
		