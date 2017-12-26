<?php
// ** 日次収支報告 ** //

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');

// ★ POST GET
class page extends core {
}

// ** page info ** //

$p = new page();
$cp = new customPage();

if($_GET['linktype'] == 'js'){
    $p->customJsStart();
    $methods = get_class_methods('page');
    foreach($methods as $val){
        if(substr($val,-2) == 'Js'){$p->$val();}
    }
    $p->customJsEnd();
}elseif($_GET['linktype'] == 'css'){
    $p->customCssStart();
    $p->customCssEnd();
}elseif($_POST['ajax'] == 'ajax'){
    $func = $_POST['func'];
    $data = json_decode($_POST['dat'],true);
    $p->$func($data);
}else{
    $src = $p->useTemplate(str_replace('.php','.html',$p->get_pgname()));

    $key = 'header';
    $params = array(
        "title"=>"マニュアル",
        "customJs"=>1,
        "customCss"=>1
    );
    $src[$key] = $cp->customView($key,$params);
    
    // ナビゲーションの書き換え
    $key = 'nav';
    $params = array(
        "autho"=>$_SESSION['autho'],
        "active"=>"manual"
    );
    $src[$key] = $cp->customView($key,$params);

    $p->show($src);
    $p->db = null;
}
?>