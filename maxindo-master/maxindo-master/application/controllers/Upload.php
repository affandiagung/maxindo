<?php
	class Upload extends CI_Controller{
		
		function uploadfile(){
			$error = "";
			$msg = "";
			$this->urisegments = $this->uri->uri_to_assoc(3);
			$fileElementName = $this->urisegments['fileName'];
			if(!empty($_FILES[$fileElementName]['error']))
			{
				switch($_FILES[$fileElementName]['error'])
				{

					case '1':
						$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2':
						$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3':
						$error = 'The uploaded file was only partially uploaded';
						break;
					case '4':
						$error = 'No file was uploaded.';
						break;
					case '6':
						$error = 'Missing a temporary folder';
						break;
					case '7':
						$error = 'Failed to write file to disk';
						break;
					case '8':
						$error = 'File upload stopped by extension';
						break;
					case '999':
					default:
						$error = 'No error code avaiable';
				}
			} elseif (empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none') {
				$error = 'Tidak ada file terupload';
			} else {
				$path = $_FILES[$fileElementName]['name'];
				$ext = pathinfo($path, PATHINFO_EXTENSION);
				$finfo = new finfo(FILEINFO_MIME_TYPE);
		    if (false === $ext = array_search(
		        $finfo->file($_FILES[$fileElementName]['tmp_name']),
		        array(
		            'jpg' => 'image/jpeg',
		            'jpeg' => 'image/jpeg',
		            'png' => 'image/png',
		            'gif' => 'image/gif',
		            'pdf' => 'application/pdf',
		            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		            'doc' => 'application/msword',
		            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		            'xls' => 'application/vnd.ms-excel',
		            'mp4' => 'video/mp4',
		            'avi' => 'video/x-msvideo',
		        ),
		        true
		    )){
		    	$error = "Invalid Extension";
		    } else {
					$imagename = md5(microtime() . $_FILES[$fileElementName]['name']) . "." . $ext;
					move_uploaded_file($_FILES[$fileElementName]['tmp_name'],"./uploads/". $imagename);
					$msg .=  $imagename;
		    } 
				// $msg .= " File Name: " . $_FILES[$fileElementName]['name'] . ", ";
				// $msg .= " File Size: " . @filesize($_FILES[$fileElementName]['tmp_name']);	
			}	
				
			echo "{";
			echo				"error: '" . $error . "',\n";
			echo				"msg: '" . $msg . "'\n";
			echo "}";
		}

		function tinyMCEUpload(){
			$storeFolder			= 'uploads';   //2
			$fileElementName 	= "file";
			$location = "";
			$msg = "";
			$error = "";
			if (!empty($_FILES)) {
		    $path = $_FILES[$fileElementName]['name'];
				$ext = pathinfo($path, PATHINFO_EXTENSION);
				$finfo = new finfo(FILEINFO_MIME_TYPE);
		    if (false === $ext = array_search(
		        $finfo->file($_FILES[$fileElementName]['tmp_name']),
		        array(
		            'jpg' => 'image/jpeg',
		            'jpeg' => 'image/jpeg',
		            'png' => 'image/png',
		            'gif' => 'image/gif',
		            'pdf' => 'application/pdf',
		            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		            'doc' => 'application/msword',
		            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		            'xls' => 'application/vnd.ms-excel',
		            'mp4' => 'video/mp4',
		            'avi' => 'video/x-msvideo',
		        ),
		        true
		    )){
		    	http_response_code(501);
		    	$error = "Invalid Extension !";
		    } else {
					$imagename = md5(microtime() . $_FILES[$fileElementName]['name']) . "." . $ext;
					move_uploaded_file($_FILES[$fileElementName]['tmp_name'],"./uploads/". $imagename);
					$msg .=  $imagename;
		    } 
		    // move_uploaded_file($tempFile,$targetFile); //6
			}
			header('Content-type: application/json');
	    $result = array(
	    	// "filename" => $_FILES[$fileElementName]['name'],
	    	// "error" => $error,
	    	"location" => base_url("uploads") . "/" . $msg
	    );
	    echo json_encode($result);
		}

		function dropZone(){
			$ds         			= DIRECTORY_SEPARATOR;  //1
			$storeFolder			= 'uploads';   //2
			$fileElementName 	= "file";
			$error = "";
			$msg = "";
			if (!empty($_FILES)) {
		    $path = $_FILES[$fileElementName]['name'];
				$ext = pathinfo($path, PATHINFO_EXTENSION);
				$finfo = new finfo(FILEINFO_MIME_TYPE);
		    if (false === $ext = array_search(
		        $finfo->file($_FILES[$fileElementName]['tmp_name']),
		        array(
		            'jpg' => 'image/jpeg',
		            'jpeg' => 'image/jpeg',
		            'png' => 'image/png',
		            'gif' => 'image/gif',
		            'pdf' => 'application/pdf',
		            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		            'doc' => 'application/msword',
		            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		            'xls' => 'application/vnd.ms-excel',
		            'mp4' => 'video/mp4',
		            'avi' => 'video/x-msvideo',
		        ),
		        true
		    )){
		    	http_response_code(501);
		    	$error = "Invalid Extension !";
		    } else {
		    	// $tempFile = $_FILES['file']['tmp_name']; //3             
			    // $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
			    // $targetFile =  $targetPath. $_FILES['file']['name'];  //5

					$imagename = md5(microtime() . $_FILES[$fileElementName]['name']) . "." . $ext;
					move_uploaded_file($_FILES[$fileElementName]['tmp_name'],"./uploads/". $imagename);
					$msg .=  $imagename;
		    } 
		    // move_uploaded_file($tempFile,$targetFile); //6
			}
			header('Content-type: application/json');
	    $result = array(
	    	"filename" => $_FILES[$fileElementName]['name'],
	    	"error" => $error,
	    	"msg" => $msg
	    );
	    echo json_encode($result);
	  //   echo "{";
			// echo				"filename: '" . $_FILES[$fileElementName]['name'] . "',\n";
			// echo				"error: '" . $error . "',\n";
			// echo				"msg: '" . $msg . "'\n";
			// echo "}";
		}

	}