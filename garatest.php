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
    // ★ AJAX start
    $data = json_decode($_POST['dat'],true);
    
}else{
    // ★ HTML start
    $opt = array(''); // (J:JS,C:CSS)
    $src = $p->useTemplate('garatest.html',$opt);

    // HTML出力
    $p->show($src,$opt);
    $dbh = null;
}
?>