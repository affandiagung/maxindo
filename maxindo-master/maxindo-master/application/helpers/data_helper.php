<?php
if( !function_exists("blankoption")){
	function blankoption( $data, $caption = "-" ){
			return array_merge(array(0 => array("keydt" => "", "valuedt" => $caption)), $data);
	}
	function blankarray( $data, $caption = "-"){
			return array_merge(array("" => $caption), $data);
	}
}
