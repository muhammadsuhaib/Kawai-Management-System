<?php
    // DB connection
    $dsn = 'mysql:dbname=main01db;host=localhost;charset=utf8mb4;';
    $user = 'webu';
    $password = 'KkoG95Vi';
    try{
        $pdo = new PDO($dsn, $user, $password);
    }catch (PDOException $e){
        die();
    }

    // POST
    $logid = $_POST['logid'];
    $logpass = $_POST['logpass'];

    // table select
    $sql = 'select * from test where test = ? and pass = ?';
    $par = array($logid,$logpass);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($par);
    while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
        $id = $result['test'];
        $name = $result['nam'];
        $authority = 100; //$result['auth'];
    }
    if($id != ""){
        session_start();
        $_SESSION['id'] = $id;
        $_SESSION['autho'] = $authority;
        $_SESSION['time'] = time();
        header('Location: garatest.php');
        exit;
    }else{
        header('Location: garatestlogin.php');
        exit;
    }

?>