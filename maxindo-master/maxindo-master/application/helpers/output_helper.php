<?php
if( !function_exists("output") ) {
	function output($dataList){
		header("Content-Type: application/json");
		echo json_encode($dataList);
	}

}