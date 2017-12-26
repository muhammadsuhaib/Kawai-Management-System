<?php

// CLASS INCLUDE
require_once('main.php');
require_once('incl/password.php');

class page extends core {

    function __construct() {
        parent::__construct();
        $today = date('Ymd');
        // 退職してない従業員
        $sql = 'select * from jyu_k';
        $sql .= ' left join jyu_i on jyuk01=jyui01 and jyui02<=? and jyui03>=? and jyui04="パスワード"';
        $sql .= ' where jyuk01=? and jyuk02<=? and jyuk03>=?';
        $par = array($today, $today, $_POST['logid'], $today, $today);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $jyui04 = $result['jyui04'];
            $passwordHash = $result['jyui05'];
            $id = $result['jyui01'];
        }
        // パスワード登録されていてIDパスが合っている
        if ($passwordHash and password_verify($_POST['logpass'], $passwordHash)) {
//        if($aaa = 1)                
            $sql = 'select group_concat(jyui04) as "key",group_concat(jyui05) as "val" from jyu_i where jyui01=? and jyui02 <= ? and jyui03 >= ?;';
            $par = array($id, $today, $today);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $key = $result['key'];
                $val = $result['val'];
            }
            $keyary = explode(',', $key);
            $valary = explode(',', $val);
            // 配列数チェック必要？
            for ($i = 0; $i < count($keyary); $i++) {
                $_SESSION[$keyary[$i]] = $valary[$i]; // sesion変数に書き込み
            }
            $_SESSION['従業員コード'] = $id;
            $_SESSION['ログイン時刻'] = time();
            
            // 小口現金システム連携用
            if($_SESSION['管理権限']=='事務責任者'){
                $role='checker';
            }elseif($_SESSION['管理権限']=='経理責任者'){
                $role='accounting';
            }else{
                $role='employee';
            }
            if($_SESSION['拠点']=='滋賀'){
                $point='shiga';
            }elseif($_SESSION['拠点']=='大阪'){
                $point='osaka';
            }else{
                $point='';
            }
            $_SESSION['petty_cash'] = '{"id":"'.$id.'","role":"'.$role.'","point":"'.$point.'"}';
            // 暗号化（openssl）
            $enckey = 'X{g)q1?@8VR_rBl9qj|W}A(v$%xE)Kq}F85V/V>kgJB%ni2Lf6';
            $_SESSION['petty_cash'] = openssl_encrypt($_SESSION['petty_cash'], 'AES-256-CBC', $enckey);
            $_SESSION['petty_cash'] = urlencode($_SESSION['petty_cash']);
//            $original_txt = openssl_decrypt($_SESSION['petty_cash'], 'AES-256-CBC', $enckey);
//            $original_json = json_decode($original_txt);
//            var_dump($original_json);
        }
    }
}

$p = new page();

if ($_SESSION['従業員コード'] != "") {
    header('Location: home.php');
    exit;
} else {
    session_destroy();
    header('Location: login.php');
    exit;
}