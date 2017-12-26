<?php
// CLASS INCLUDE
require_once('main.php');
require_once('certinc.php');

// ★ POST GET

$p = new page;

if($_GET['linktype'] == 'js'){
    // customJsStart() ~ customJsEnd()までが$(function(){})内
    //　$p->ajaxCall(処理タイミング,関数名,渡すデータ配列,success時処理);
    // EOT行はスペース等が入るとエラー JS1はタブ*2 JS2はタブ*3
    $p->customJsStart();

    $p->customJsEnd();

}elseif($_POST['ajax'] == 'ajax'){
    // ★ AJAX start
    

}else{
    // ★ HTML start
    $opt = array('JS'); // (J:JS,C:CSS)
    $src = $p->useTemplate('z.html',$opt);

    // HTMLソース差し替え

    // HTML出力
    $p->show($src,$opt);
    $dbh = null;
}
?>