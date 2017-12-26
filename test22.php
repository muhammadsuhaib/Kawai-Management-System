<?php

$url = debug_backtrace();
$pgname = end(explode('/', $url[0]['file'])); // 呼び出し元PG名

// ** page info ** //

$p = new page();
$cp = new customPage();

if($_GET['linktype'] == 'js'){
    //$p->customJsStart();
    //$methods = get_class_methods($p);
    //foreach($methods as $val){
    //    if(substr($val,-2) == 'Js'){$p->$val();}
    //}
    //$p->customJsEnd();
    echo "<html>";
}elseif($_GET['linktype'] == 'css'){
    $p->customCssStart();
    $p->customCssEnd();
}elseif($_POST['ajax'] == 'ajax'){
    $func = $_POST['func'];
    $data = json_decode($_POST['dat'],true);
    $p->$func($data);
}else{
    $src = $p->useTemplate(str_replace('.php','.html',$pgname));

    $key = 'header';
    $params = array(
        "title"=>"ホーム",
        "customJs"=>1,
        "customCss"=>1
    );
    $src[$key] = $cp->customView($key,$params);
    
    // ナビゲーションの書き換え
    $key = 'nav';
    $params = array(
        "autho"=>$_SESSION['autho'],
        "active"=>"home"
    );
    $src[$key] = $cp->customView($key,$params);

    $p->show($src);
    $p->db = null;
}
?>