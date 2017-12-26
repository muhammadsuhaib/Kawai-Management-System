<?php
    session_start();

    // session check (1hour)
    if($_SESSION['id'] != "" and $_SESSION['time'] + 3600 > time()){
        // DB connection
        $dsn = 'mysql:dbname=main01db;host=localhost;charset=utf8mb4;';
        $user = 'webu';
        $password = 'KkoG95Vi';
        try{
            $pdo = new PDO($dsn, $user, $password);
        }catch (PDOException $e){
            die();
        }
    }else{
        session_destroy();
        echo "ログイン有効期限切れです。ログインし直して下さい。";
        die();
    }
?>
