<?php
// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');

class page extends core {
	
}
$p = new page();
//print_r($_SESSION);

$dir = $_SESSION['download_directory'];
$fname = $_SESSION['download_file'];

if($dir and $fname){
    $_SESSION['download_file'] = ''; // 初期化
    $_SESSION['download_directory'] = '';
    $fileName = $dir . $fname;
    header('Content-Type:application/octet-stream');
    header('Content-Disposition:attachment; filename='.$fname);
    header('Content-Transfer-Encoding: binary');
    header('Content-Length:'.filesize($fileName));
    readfile($fileName);
}else{
    exit();
}