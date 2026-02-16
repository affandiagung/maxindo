<?php
if( !function_exists("text2slug")){
	function text2slug( $text ){
			$text = str_replace(" ", "-", $text);
			$text = str_replace("/", "-", $text);
			$text = str_replace(":", "-", $text);
			$text = str_replace(",", "", $text);
			$text = str_replace(".", "", $text);
			$text = str_replace("!", "", $text);
			$text = str_replace("&", "", $text);
			$text = str_replace("%", "", $text);
			$text = str_replace("@", "", $text);
			$text = str_replace("$", "", $text);
			$text = str_replace("*", "", $text);
			$text = str_replace("=", "", $text);
			return strtolower($text);
	}
}

if( !function_exists("get_excerpt")){
	function get_excerpt( $text, $limit = 15){
			$arr = explode(" ", $text);
			$result = "";
			if (count($arr) < $limit) {
				$limit = count($arr);
			}
			for($i=0;$i<$limit;$i++){
				$result .= $arr[$i] . " ";
			}
			return strip_tags($result).'...';
	}
}
