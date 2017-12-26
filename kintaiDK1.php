<?php
// ** 勤怠入力(月給) ** //

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');

// ★ POST GET

class page extends core {

    // JS初期処理
    function initJs(){
        $this->clearJs();
        $this->js1 = <<<'__'
            // エンターキーでデータ送信
            $("#barcode").keydown(function(event){
            if(event.keyCode === 13){
                createData();
            }
            });
            setTimeout(function(){$('#barcode').focus();},0);
            
            // テンプレサンプルデータ削除
            $("#tablebody tr").remove();
            
            // データ読み込み
            readData();
__;
        $this->addEventListener2();
    }
    // フォーカスを常にバーコード入力枠に
    function focusLockJs(){
        $this->clearJs();
        $this->js1 = <<<'__'
        setTimeout(function(){$('#barcode').focus();},0);
__;
        $this->addEventListener2('#barcode','blur','','');
        $this->addEventListener2('body','click','','');
    }

    // 初回データ読み込み
    function readDataJs(){
        $this->clearJs();
        $this->js2 = <<<"__"
        var data = JSON.parse(json_data);
            $("#tablebody tr").remove();
            $('#tablebody').append(data[0]['html']);
            console.log(data);
__;
        $this->js3 = <<<"__"
            alert('エラー');
__;
        $this->js4 = <<<"__"
            $('#barcode').val('');
__;
        $this->addEventListener2('','wait','readData','ajax');
    }
    //
    function readData($data){
        $sql = "select * from kintai left join jyugyoin on jyu01=knt01 and jyu02=knt02 order by kintai.uday DESC,kintai.utim DESC limit 40;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $hiduke = substr($result['knt03'],0,4). '/' .substr($result['knt03'],4,2). '/' .substr($result['knt03'],6,2);
            $sji = substr($result['knt05'],0,2). ':' .substr($result['knt05'],2,2);
            $tji = substr($result['knt06'],0,2). ':' .substr($result['knt06'],2,2);
            $kyukei1 = $result['knt07'] + 0 .'h';
            $kyukei2 = $result['knt08'] + 0 .'h';
            if($tji == ':'){$tji = '';}
            if($sji != ''){$sji = 'OK';} // 時刻非表示
            if($tji != ''){$tji = 'OK';} // 時刻非表示
            if($kyukei1 == '0h' and $kyukei2 == '0h'){
                $kyukei = '';
            }elseif($kyukei2 != '0h'){
                $kyukei = $kyukei2.'(深夜)';
            }else{
                $kyukei = $kyukei1;
            }
            $res[0]['html'] .= "<tr><td>{$result['jyu03']} {$result['jyu04']}</td><td>$hiduke</td><td>$sji</td><td>$tji</td></tr>";
        }
        echo json_encode($res);
    }
    
    // バーコード読み込み → データ登録
    function createData2Js(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {'inf':$('#barcode').val(),'tim':$('#clock-01').html(),'day':$('#clock-02').html()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            if(data[0]['res']){
                $("#tablebody tr").remove();
                $('#tablebody').append(data[0]['html']);
                console.log(data);
            }else{
                alert('読み込みエラー');
            }
__;
        $this->js3 = <<<"__"
            alert('エラー');
__;
        $this->js4 = <<<"__"
            // フォームクリア
            $('#barcode').val('');
__;
        $this->addEventListener2('','wait','createData','ajax');
    }
    //
    function createData($data){
        $data['tim'] = str_replace('-** ','',$data['tim']);
        $data['tim'] = str_replace(' **-','',$data['tim']);
        $time = explode(':',$data['tim']);
        $wrk = explode('年',$data['day']);
        $nen = $wrk[0];
        $wrk = explode('月',$wrk[1]);
        $tuki = $wrk[0];
        $wrk = explode('日',$wrk[1]);
        $hi = $wrk[0];
        $eigyobi = $nen . $tuki . $hi;
        $inf = $data['inf']; // ログイン情報 cd,no,pass
        $info = explode('Z',$data['inf']);
        $jikan = $time[0].$time[1]; // 時間4桁
        $today = date('Ymd');
        $now = date('His');

		$chk = '';
        $par = array($info[0],$info[1],$info[2]);
        $sql = "select jyu02 from jyugyoin where jyu01=? and jyu02=? and jyu32=?;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $chk = $result['jyu02'];
        }
        if($chk){
            $st = '';
            $par = array($info[0],$info[1],$eigyobi);
            $sql = "select knt05 from kintai where knt01=? and knt02=? and knt03=?;"; // 営業日のデータ有無
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $st = $result['knt05'];
                $en = $result['knt06'];
            }
            if($st == ''){
                $par = array($info[0],$info[1],$eigyobi,$jikan,$today,$now,$_SESSION['id']); // $info[2] -> パスワード
                $sql = "insert into kintai (knt01,knt02,knt03,knt05,cday,ctim,cid) values (?,?,?,?,?,?,?);";
                $stmt = $this->db->prepare($sql);
                $res[0]['res'] = $stmt->execute($par);
            }else{
                $par = array($jikan,$today,$now,$_SESSION['id'],$info[0],$info[1],$eigyobi);
                $sql = "update kintai set knt06=?,uday=?,utim=?,uid=? where knt01=? and knt02=? and knt03=?;";
                $stmt = $this->db->prepare($sql);
                $res[0]['res'] = $stmt->execute($par);
            }
        }
        if($res[0]['res']){
            $sql = "select * from kintai left join jyugyoin on jyu01=knt01 and jyu02=knt02 order by kintai.uday DESC,kintai.utim DESC limit 40;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                // 更新レコード色付け
                if($result['knt01'] == $info[0] and $result['knt02'] == $info[1] and $result['knt03'] == $eigyobi){
                    $style = 'style="background-color:#77B8DA;"';
                }else{
                    $style = '';
                }
                $hiduke = substr($result['knt03'],0,4). '/' .substr($result['knt03'],4,2). '/' .substr($result['knt03'],6,2);
                $sji = substr($result['knt05'],0,2). ':' .substr($result['knt05'],2,2);
                $tji = substr($result['knt06'],0,2). ':' .substr($result['knt06'],2,2);
                $kyukei1 = $result['knt07'] + 0 .'h';
                $kyukei2 = $result['knt08'] + 0 .'h';
                if($tji == ':'){$tji = '';}
                if($kyukei1 == '0h' and $kyukei2 == '0h'){
                    $kyukei = '';
                }elseif($kyukei2 != '0h'){
                    $kyukei = $kyukei2.'(深夜)';
                }else{
                    $kyukei = $kyukei1;
                }
                $res[0]['html'] .= "<tr {$style}><td>{$result['jyu03']} {$result['jyu04']}</td><td>$hiduke</td><td>$sji</td><td>$tji</td></tr>";
            }
        }
        echo json_encode($res);
    }


}



// ** page info ** //
$p = new page();

if($_GET['linktype'] == 'js'){
    $p->jsFunctionExecute(); // function ---Js() を 実行しJSを出力($_GET['linktype'] == js)
}elseif($_GET['linktype'] == 'css'){
    $p->cssFunctionExecute(); // function ---Css() を 実行しCSSを出力($_GET['linktype'] == css)
}elseif($_POST['linktype'] == 'ajax'){
    $p->ajaxFunctionExecute(); // function ---ajax() を 実行しajaxデータを出力($_POST['linktype'] == ajax)
}else{
    $p->htmlFunctionExecute(); // 同名templateを読み込みHTMLソースを生成($this->src[key_name]が@key部分に該当)
    // header 及び standardComponents ロード(lockmodal hiddenfields)
    $params = array('title'=>'勤怠打刻');
    $p->src['header'] = headerReplace::createSrc($params);
    $p->src['standard'] = standardComponentsLoad::createSrc();

    // bootstrap ナビゲーション
    $params = array('autho'=>$_SESSION['autho'], 'active'=>'master', 'name'=>$_SESSION['name']);
    $p->src['nav'] = bootstrapNavigationReplace::createSrc($params);

    $p->show();
}
?>