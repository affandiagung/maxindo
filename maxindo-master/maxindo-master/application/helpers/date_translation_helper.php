<?php
if (!function_exists("date_to_ID")) {
  function date_to_ID($sourcedate = "", $day = false, $time = false){
    $date = date("Y-m-d", strtotime($sourcedate));
    if( $date == "" ){
      return "";
    }
    $bulan = array (1 => 'Januari',
            				2 => 'Februari',
            				3 => 'Maret',
            				4 => 'April',
            				5 => 'Mei',
            				6 => 'Juni',
            				7 => 'Juli',
            				8 =>'Agustus',
            				9 => 'September',
            				10 => 'Oktober',
            				11 =>'November',
            				12 => 'Desember'
			);
	$split = explode('-', $date);
	$ID_date =  $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
  $hari = array ( 1 => 'Senin',
          				2 => 'Selasa',
          				3 => 'Rabu',
          				4 => 'Kamis',
          				5 => 'Jumat',
          				6 => 'Sabtu',
          				7 => 'Minggu'
			);

	if ($day) {
		$num = date('N', strtotime($sourcedate));
    $time_str = "";
    if ($time) {
        $time_str = " ".date("H:i", strtotime($sourcedate));
    }
		return $hari[$num] . ', ' . $ID_date.$time_str;
	}
  if ($time) {
    $day_str = "";
    if ($day) {
        $day_str = $hari[$num] . ', ';
    }
    return $day_str.$ID_date." ".date("H:i", strtotime($sourcedate));
  }
	return $ID_date;
  }
}

if (!function_exists("datetime_to_ID")) {
  function datetime_to_ID($datetime = "", $day = false){
    $mydatetime = date("Y-m-d", strtotime($datetime));
    if( $datetime == "" ){
      return "";
    }
    $bulan = array (1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 =>'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 =>'November',
                    12 => 'Desember'
      );
    $split = explode('-', $mydatetime);
    $ID_date =  $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
    $hari = array ( 1 => 'Senin',
                  2 => 'Selasa',
                  3 => 'Rabu',
                  4 => 'Kamis',
                  5 => 'Jumat',
                  6 => 'Sabtu',
                  7 => 'Minggu'
      );

  if ($day) {
    $num = date('N', strtotime($datetime));
    return $hari[$num] . ', ' . $ID_date;
  }
  return $ID_date . " - " . date("H:i:s", strtotime($datetime));
  }
}

if (!function_exists("date_to_DAY")) {
  function date_to_DAY($date = ""){
    $date = date("Y-m-d", strtotime($date));
    if( $date == "" ){
      return "";
    }

  $split = explode('-', $date);
  $hari = array ( 1 => 'Senin',
                  2 => 'Selasa',
                  3 => 'Rabu',
                  4 => 'Kamis',
                  5 => 'Jumat',
                  6 => 'Sabtu',
                  7 => 'Minggu'
      );

    $num = date('N', strtotime($date));
    return $hari[$num];
  }
}



if(!function_exists("ex_month")){
  function ex_month($month){
    $months = array (
      1 => 'Januari',
      2 => 'Februari',
      3 => 'Maret',
      4 => 'April',
      5 => 'Mei',
      6 => 'Juni',
      7 => 'Juli',
      8 => 'Agustus',
      9 => 'September',
      10 => 'Oktober',
      11 => 'November',
      12 => 'Desember'
    );
    $ex = explode("-", $month);
    $_value = "";
    if( count($ex) > 1 ){
      if( strlen($ex[0]) == "4" ){
        $_value = $months[intval($ex[1])] . " " . $ex[0];
      } else {
        $_value = $months[intval($ex[0])] . " " . $ex[1];
      }
    }
    return $_value;
  }
}
