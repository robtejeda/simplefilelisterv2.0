<?php
	//CHANGE PATH ONCE IT'S ON LIVE SITE
	$path = $_SERVER['DOCUMENT_ROOT'].substr($_POST["path"],1);
	var_dump($path);

	if(file_exists($path)) {	

		if(unlink($path)) {

			echo '{"status":1,"msg":"File Successfuly Removed","file":"'.$path.'"}';
		} else {

			echo '{"status":0,"msg":"Failed to Remove the FIle","file":"'.$path.'"}';
		}
	} else {

		echo '{"status":0,"msg":"File Not Found","file":"'.$path.'"}';
	}
?>