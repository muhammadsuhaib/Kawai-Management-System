<?php
// ** test ** //

// CLASS INCLUDE
require_once('main.php');
//require_once('certinc.php');

// ★ POST GET
$val = $_POST['text5'];

$p = new page;

if($_GET['linktype'] == 'js'){
}elseif($_GET['linktype'] == 'css'){
}elseif($_POST['ajax'] == 'ajax'){
}else{
    // ★ HTML start
    $opt = array(''); // (J:JS,C:CSS)
    $src = $p->useTemplate('garatest2.html',$opt);

    // ソース差し替え
    $src['table'] = '';
    $len = 5;
    for($i = 0; $i < $len; $i++){
        $src['table'] .= '<tr>';
        $src['table'] .= "<td style='font-size: x-small'>$i</td>";
        $src['table'] .= '</tr>';
    }
    
    // HTML出力
    $p->show($src,$opt);
    $dbh = null;
}
?>