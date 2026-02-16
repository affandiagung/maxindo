<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cekstok extends CI_Controller {

	private $data = array();

	public function __construct(){
		parent::__construct();
		//Do your magic here
	}

	public function index(){
		$this->data['TGLINVAWAL'] = date("Y-m-d H:i");
		$this->data['TGLINVAKHIR'] = date("Y-m-d H:i", strtotime( date("Y-m-d H:i") . " + 7 days" ) );
		$this->data['INVENTORY'] =  blankoption($this->Mmasterdata->getInventory());
		$this->load->view("admin/cekstok", $this->data);
	}

	function loadCalendar(){
		$data = array();
		$post = $this->input->post();
		$awal = $post['TGLINVAWAL'];
		$akhir = $post['TGLINVAKHIR'];
		$inventories = array();
		$inventoriesBook = array();
		$availabilities = array();
		$booked = array();
		$id = 1;
		$idBook = 1;
		$idMaster = 1;
		if( isset($post['INVENTORY']) ){
			$seq = 1;
			foreach($post['INVENTORY'] as $inv){
				$inventory = $this->db->where("INVENTORYID", $inv)->get("inventories")->row();
				array_push($inventories, array(
					'id' => $inventory->INVENTORYID,
					'content' => $inventory->NAME,
					'order' => $seq
				));

				// Hitung yang sudah deal dari Inventory Calendar
				$used = 0;
				$totItem = $inventory->TOTITEM;
				$sisa = $totItem;

				// Cari Tanggal yang berubah
				$mulaiPinjam = $this->db->select("USEDCOUNT, STARTDATE as TANGGAL, 'START' as STATUS")->where("
			    INVENTORY = '".$inv."' AND (
				    ('".$awal."' <= STARTDATE AND '".$akhir."' <= ENDDATE AND '".$awal."' <= ENDDATE) OR
				    ('".$awal."' <= STARTDATE AND '".$akhir."' >= ENDDATE) OR
				    ('".$awal."' >= STARTDATE AND '".$akhir."' >= ENDDATE AND '".$awal."' <= ENDDATE) OR
				    ('".$awal."' >= STARTDATE AND '".$akhir."' <= ENDDATE)
			    )
				")
				->join("projects", "PROJECT=PROJECTID","LEFT")
				->get("inventorycalendars")->result_array();

				$selesaiPinjam = $this->db->select("USEDCOUNT, ENDDATE as TANGGAL, 'END' as STATUS")->where("
			    INVENTORY = '".$inv."' AND (
				    ('".$awal."' <= STARTDATE AND '".$akhir."' <= ENDDATE AND '".$awal."' <= ENDDATE) OR
				    ('".$awal."' <= STARTDATE AND '".$akhir."' >= ENDDATE) OR
				    ('".$awal."' >= STARTDATE AND '".$akhir."' >= ENDDATE AND '".$awal."' <= ENDDATE) OR
				    ('".$awal."' >= STARTDATE AND '".$akhir."' <= ENDDATE)
			    )
				")
				->join("projects", "PROJECT=PROJECTID","LEFT")
				->get("inventorycalendars")->result_array();
				$penggunaan = array_merge($mulaiPinjam, $selesaiPinjam);
				usort($penggunaan, function ($a, $b) {
				    return $a["TANGGAL"] <=> $b["TANGGAL"];
				});
				// dd($penggunaan);
				// Kelompokkan berdasarkan tanggal
				$finalPenggunaan = array();
				$index = 0;
				$lastData = array(
					"USEDCOUNT" => 0,
					"TANGGAL" => "",
					"STATUS" => ""
				);
				foreach($penggunaan as $pgn){
					if( $lastData["TANGGAL"] == $pgn["TANGGAL"] ){
						if( $lastData['STATUS'] == "START" && $pgn['STATUS'] == "START" ){
							$finalPenggunaan[$index - 1]['USEDCOUNT'] = $finalPenggunaan[$index - 1]['USEDCOUNT'] + $pgn['USEDCOUNT'];
						}
						if( $lastData['STATUS'] == "START" && $pgn['STATUS'] == "END"){
							$finalPenggunaan[$index - 1]['USEDCOUNT'] = $finalPenggunaan[$index - 1]['USEDCOUNT'] + $pgn['USEDCOUNT'];
						}
						if( $lastData['STATUS'] == "END" && $pgn['STATUS'] == "START"){
							$finalPenggunaan[$index - 1]['USEDCOUNT'] = $finalPenggunaan[$index - 1]['USEDCOUNT'] - $pgn['USEDCOUNT'];
						}
						if( $lastData['STATUS'] == "END" && $pgn['STATUS'] == "END"){
							$finalPenggunaan[$index - 1]['USEDCOUNT'] = $finalPenggunaan[$index - 1]['USEDCOUNT'] + $pgn['USEDCOUNT'];
						}
					} else {
						$finalPenggunaan[$index] = $pgn;
						$index++;
					}
					$lastData = $pgn;
				}

				$lastDate = $awal;
				$lastGuna = $used;
				$color = "primary";
				$first = true;
				$lastPenggunaan = array();
				if( count($finalPenggunaan) > 0 ){
					// dd($finalPenggunaan);
					foreach( $finalPenggunaan as $guna ){
						if( $first == true && $guna["TANGGAL"] > $awal ){
							$color = $this->getWarnaSisa( $sisa, $inventory->TOTITEM );
							array_push($availabilities, array(
								"id" => $id,
								"group" => $inventory->INVENTORYID,
								"start" => $awal,
								"end" => $guna["TANGGAL"],
								"content" => $sisa,
								"progress" => "100%",
								"color" => $color,
							));
							$id++;
						}

						if( $first == true ){
							$lastPenggunaan = $guna;
						} else {
							if($lastPenggunaan["STATUS"] == "START"){
								$sisa = $sisa - $lastPenggunaan["USEDCOUNT"];
							} else {
								$sisa = $sisa + $lastPenggunaan["USEDCOUNT"];
							}
							$color = $this->getWarnaSisa( $sisa, $inventory->TOTITEM );
							$finalDate = $guna["TANGGAL"] > $akhir ? $akhir : $guna["TANGGAL"];

							array_push($availabilities, array(
								"id" => $id,
								"group" => $inventory->INVENTORYID,
								"start" => $lastPenggunaan['TANGGAL'],
								"end" => $finalDate,
								"content" => $sisa,
								"progress" => "100%",
								"color" => $color,
							));
							$lastPenggunaan = $guna;
							$id++;
						}
						$first = false;
					}
					if( $lastPenggunaan["TANGGAL"] < $akhir ){
						if($lastPenggunaan["STATUS"] == "START"){
							$sisa = $sisa - $lastPenggunaan["USEDCOUNT"];
						} else {
							$sisa = $sisa + $lastPenggunaan["USEDCOUNT"];
						}
						$color = $this->getWarnaSisa( $sisa, $inventory->TOTITEM );
						array_push($availabilities, array(
							"id" => $id,
							"group" => $inventory->INVENTORYID,
							"start" => $lastPenggunaan["TANGGAL"],
							"end" => $akhir,
							"content" => $sisa,
							"progress" => "100%",
							"color" => $color,
						));
						$lastPenggunaan = $guna;
						$id++;
					}
				} else {
					array_push($availabilities, array(
						"id" => $id,
						"group" => $inventory->INVENTORYID,
						"start" => $awal,
						"end" => $akhir,
						"content" => $sisa,
						"progress" => "100%",
						"color" => "primary",
					));
					$id++;
				}
				
				$seq++;
			}

			// Hitung yang belum deal dari Quotation
			foreach($post['INVENTORY'] as $inv){
				$inventory = $this->db->where("INVENTORYID", $inv)->get("inventories")->row();
				array_push($inventoriesBook, array(
					'id' => $inventory->INVENTORYID,
					'content' => $inventory->NAME,
					'order' => $seq
				));

				$used = 0;
				$totItem = $inventory->TOTITEM;
				$sisa = $totItem;

				// Cari Tanggal yang berubah
				$mulaiPinjam = $this->db->select("QTY as USEDCOUNT, SETUPDATE as TANGGAL, 'START' as STATUS")->where("
			    projects.PROJECTSTAGE <> 0 AND INVENTORY = '".$inv."' AND (
				    ('".$awal."' <= SETUPDATE AND '".$akhir."' <= DISPLACEDATE AND '".$awal."' <= DISPLACEDATE) OR
				    ('".$awal."' <= SETUPDATE AND '".$akhir."' >= DISPLACEDATE) OR
				    ('".$awal."' >= SETUPDATE AND '".$akhir."' >= DISPLACEDATE AND '".$awal."' <= DISPLACEDATE) OR
				    ('".$awal."' >= SETUPDATE AND '".$akhir."' <= DISPLACEDATE)
			    )
				")
				->join("projects", "PROJECT=PROJECTID","LEFT")
				->get("projectquotations")->result_array();

				$selesaiPinjam = $this->db->select("QTY as USEDCOUNT, DISPLACEDATE as TANGGAL, 'END' as STATUS")->where("
			    INVENTORY = '".$inv."' AND (
				    ('".$awal."' <= SETUPDATE AND '".$akhir."' <= DISPLACEDATE AND '".$awal."' <= DISPLACEDATE) OR
				    ('".$awal."' <= SETUPDATE AND '".$akhir."' >= DISPLACEDATE) OR
				    ('".$awal."' >= SETUPDATE AND '".$akhir."' >= DISPLACEDATE AND '".$awal."' <= DISPLACEDATE) OR
				    ('".$awal."' >= SETUPDATE AND '".$akhir."' <= DISPLACEDATE)
			    )
				")
				->join("projects", "PROJECT=PROJECTID","LEFT")
				->get("projectquotations")->result_array();

				$penggunaan = array_merge($mulaiPinjam, $selesaiPinjam);
				usort($penggunaan, function ($a, $b) {
				    return $a["TANGGAL"] <=> $b["TANGGAL"];
				});
				// dd($penggunaan);

				// Kelompokkan berdasarkan tanggal
				$finalPenggunaan = array();
				$index = 0;
				$lastData = array(
					"USEDCOUNT" => 0,
					"TANGGAL" => "",
					"STATUS" => ""
				);
				foreach($penggunaan as $pgn){
					if( $lastData["TANGGAL"] == $pgn["TANGGAL"] ){
						if( $lastData['STATUS'] == "START" && $pgn['STATUS'] == "START" ){
							$finalPenggunaan[$index - 1]['USEDCOUNT'] = $finalPenggunaan[$index - 1]['USEDCOUNT'] + $pgn['USEDCOUNT'];
						}
						if( $lastData['STATUS'] == "START" && $pgn['STATUS'] == "END"){
							$finalPenggunaan[$index - 1]['USEDCOUNT'] = $finalPenggunaan[$index - 1]['USEDCOUNT'] + $pgn['USEDCOUNT'];
						}
						if( $lastData['STATUS'] == "END" && $pgn['STATUS'] == "START"){
							$finalPenggunaan[$index - 1]['USEDCOUNT'] = $finalPenggunaan[$index - 1]['USEDCOUNT'] - $pgn['USEDCOUNT'];
						}
						if( $lastData['STATUS'] == "END" && $pgn['STATUS'] == "END"){
							$finalPenggunaan[$index - 1]['USEDCOUNT'] = $finalPenggunaan[$index - 1]['USEDCOUNT'] + $pgn['USEDCOUNT'];
						}
					} else {
						$finalPenggunaan[$index] = $pgn;
						$index++;
					}
					$lastData = $pgn;
				}

				$lastDate = $awal;
				$lastGuna = $used;
				$color = "primary";
				$first = true;
				$lastPenggunaan = array();
				if( count($finalPenggunaan) > 0 ){
					// dd($finalPenggunaan);
					foreach( $finalPenggunaan as $guna ){
						if( $first == true && $guna["TANGGAL"] > $awal ){
							$color = $this->getWarnaSisa( $sisa, $inventory->TOTITEM );
							array_push($booked, array(
								"id" => $idBook,
								"group" => $inventory->INVENTORYID,
								"start" => $awal,
								"end" => $guna["TANGGAL"],
								"content" => $sisa,
								"progress" => "100%",
								"color" => $color,
							));
							$idBook++;
						}

						if( $first == true ){
							$lastPenggunaan = $guna;
						} else {
							if($lastPenggunaan["STATUS"] == "START"){
								$sisa = $sisa - $lastPenggunaan["USEDCOUNT"];
							} else {
								$sisa = $sisa + $lastPenggunaan["USEDCOUNT"];
							}
							$sisa = ($sisa > $totItem) ? $totItem : $sisa;
							$color = $this->getWarnaSisa( $sisa, $inventory->TOTITEM );
							$finalDate = $guna["TANGGAL"] > $akhir ? $akhir : $guna["TANGGAL"];

							array_push($booked, array(
								"id" => $idBook,
								"group" => $inventory->INVENTORYID,
								"start" => $lastPenggunaan['TANGGAL'],
								"end" => $finalDate,
								"content" => $sisa,
								"progress" => "100%",
								"color" => $color,
							));
							$lastPenggunaan = $guna;
							$idBook++;
						}
						$first = false;
					}
					if( $lastPenggunaan["TANGGAL"] < $akhir ){
						if($lastPenggunaan["STATUS"] == "START"){
							$sisa = $sisa - $lastPenggunaan["USEDCOUNT"];
						} else {
							$sisa = $sisa + $lastPenggunaan["USEDCOUNT"];
						}
						$sisa = ($sisa > $totItem) ? $totItem : $sisa;
						$color = $this->getWarnaSisa( $sisa, $inventory->TOTITEM );
						array_push($booked, array(
							"id" => $idBook,
							"group" => $inventory->INVENTORYID,
							"start" => $lastPenggunaan["TANGGAL"],
							"end" => $akhir,
							"content" => $sisa,
							"progress" => "100%",
							"color" => $color,
						));
						$lastPenggunaan = $guna;
						$idBook++;
					}
				} else {
					array_push($booked, array(
						"id" => $idBook,
						"group" => $inventory->INVENTORYID,
						"start" => $awal,
						"end" => $akhir,
						"content" => $sisa,
						"progress" => "100%",
						"color" => "primary",
					));
					$idBook++;
				}
				
				$seq++;
			}
			$data['inventory'] = json_encode($inventories);
			$data['bookedinv'] = json_encode($inventoriesBook);
			$data['availability'] = json_encode($availabilities);
			$data['booked'] = json_encode($booked);
			// $this->session->set_userdata("availabilities");
			// dd($data['inventory']);
			// dd($data['availability']);
			$data['TGLINVAWAL'] = $awal;
			$data['TGLINVAKHIR'] = $akhir;
			$this->load->view("admin/inventoryCalendar", $data);
		} else {
			echo "<div class='alert alert-danger'>Pilih Inventory terlebih dahulu</alert>";
		}

	}

	function getWarnaSisa($sisa, $total){
		if( $sisa == $total ){
			return "primary";
		} elseif( $sisa < $total && $sisa > 0 ){
			return "warning";
		} else {
			return "danger";
		}
	}

	public function detail( $inventoryid, $sisa, $start, $end  ){
		$data = array();
		$data['start'] = urldecode($start);
		$data['end'] = urldecode($end);
		// Check Total Inventory
		$data['inventory'] = $this->db->where("INVENTORYID", $inventoryid)->get("inventories")->row();
		$data['totItem'] = $data['inventory']->TOTITEM;
		if( $sisa == $data['totItem'] ){
			echo "Inventory <strong>".$data['inventory']->NAME."</strong> Tidak dipakai dimanapun untuk tanggal <strong>" . datetime_to_ID($data['start']) . "</strong> s/d <strong>" . datetime_to_ID($data['end']) . "</strong>";
		} else {
			// Check Inventory
			$data['usage'] = $this->db->select("
                 inventorycalendars.INVENTORYCALENDARID, 
                 inventorycalendars.STARTDATE,
                 inventorycalendars.ENDDATE,
                 inventorycalendars.USEDCOUNT,
                 IF( inventories.INVENTORYTYPE = '1', (inventorycalendars.USEDCOUNT / 4), 0 ) as SQM,
                 projects.PROJECTLOCATION,
                 projects.PROJECTROOM,
                 customers.NAME as CUSTOMER,
                 employees.NAME as EMPLOYEE,
                 employees.MOBILE,
                 IFNULL(projectstages.NAME,'') as PROJECTSTAGE
               ")->where("INVENTORY", $inventoryid)
							->where("STARTDATE <=", $data['start'])
							->where("ENDDATE >=", $data['end'])
							->join("projects", "PROJECT=PROJECTID", "LEFT")
							->join("employees", "EMPLOYEE=EMPLOYEEID", "LEFT")
							->join("customers", "CUSTOMER=CUSTOMERID", "LEFT")
							->join("projectstages", "PROJECTSTAGE=PROJECTSTAGEID", "LEFT")
							->join("inventories", "INVENTORY=INVENTORYID", "LEFT")
							->get("inventorycalendars")->result();

			$this->load->view("admin/inventoryusage", $data);
			// echo "Detail : " . $inventoryid . " Start : " . $start . " End : " . $end;;
		}
	}

	public function detailbooked( $inventoryid, $sisa, $start, $end  ){
		$data = array();
		$data['start'] = urldecode($start);
		$data['end'] = urldecode($end);
		// Check Total Inventory
		$data['inventory'] = $this->db->where("INVENTORYID", $inventoryid)->get("inventories")->row();
		$data['totItem'] = $data['inventory']->TOTITEM;
		if( $sisa == $data['totItem'] ){
			echo "Inventory <strong>".$data['inventory']->NAME."</strong> Tidak dibooking dimanapun untuk tanggal <strong>" . datetime_to_ID($data['start']) . "</strong> s/d <strong>" . datetime_to_ID($data['end']) . "</strong>";
		} else {
			// Check Inventory
			$data['usage'] = $this->db->select("
                 projectquotations.PROJECTQUOTATIONID, 
                 projects.SETUPDATE,
                 projects.DISPLACEDATE,
                 projectquotations.QTY as USEDCOUNT,
                 IF( inventories.INVENTORYTYPE = '1', (projectquotations.QTY / 4), 0 ) as SQM,
                 projects.PROJECTLOCATION,
                 projects.PROJECTROOM,
                 customers.NAME as CUSTOMER,
                 employees.NAME as EMPLOYEE,
                 employees.MOBILE,
                 IFNULL(projectstages.NAME,'') as PROJECTSTAGE
               ")->where("INVENTORY", $inventoryid)
							->where("SETUPDATE <=", $data['start'])
							->where("DISPLACEDATE >=", $data['end'])
							->join("projects", "PROJECT=PROJECTID", "LEFT")
							->join("employees", "EMPLOYEE=EMPLOYEEID", "LEFT")
							->join("customers", "CUSTOMER=CUSTOMERID", "LEFT")
							->join("projectstages", "PROJECTSTAGE=PROJECTSTAGEID", "LEFT")
							->join("inventories", "INVENTORY=INVENTORYID", "LEFT")
							->get("projectquotations")->result();

			$this->load->view("admin/inventoryusage", $data);
			// echo "Detail : " . $inventoryid . " Start : " . $start . " End : " . $end;;
		}
	}

}

/* End of file Cekstok.php */
/* Location: ./application/controllers/admin/Cekstok.php */