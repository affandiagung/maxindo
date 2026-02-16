<?php
if( !function_exists("image_check")){
	function image_check( $image ){
    $foto = (file_exists("./uploads/".$image)) ? "uploads/".$image : "uploads/noimage.png";
    if( $foto != "uploads/noimage.png" ){
      $foto = ($image != "") ? "uploads/".$image : "uploads/noimage.png";
    }
    return $foto;
	}
}

if( !function_exists("imageload")){
	function imageload( $image, $name, $uploaddir ){
		if($image != "" && file_exists(FCPATH . "/".$uploaddir."/" . $image) ){
			return "<img style='max-width:100%;' src='".$uploaddir."/".$image."' class='img-rounded marginless' alt='".$name."'>";
		} else {
			return "<span class='font-size-h3 symbol-label'><span class='symbol-label'>".strtoupper(substr($name,0,1))."</span></span>";
		}
	}
}
