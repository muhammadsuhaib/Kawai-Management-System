<?php
// ** test ** //

// CLASS INCLUDE
require_once('main.php');
//require_once('certinc.php');

// ★ POST GET
$p = new page;

if($_GET['linktype'] == 'js'){
}elseif($_GET['linktype'] == 'css'){
}elseif($_POST['ajax'] == 'ajax'){
}else{
    // ★ HTML start
    $opt = array(''); // (J:JS,C:CSS)
    $src = $p->useTemplate('garatestlogin.html',$opt);

    // HTML出力
    $p->show($src,$opt);
    $dbh = null;
}
?>