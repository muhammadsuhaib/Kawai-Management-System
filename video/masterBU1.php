<?php
// ** 日次収支報告 ** //

// CLASS INCLUDE
require_once('main.php');
require_once('certinc.php');
require_once('incl/htmlk.php');

// ★ POST GET

$p = new page;

if($_GET['linktype'] == 'js'){
    // customJsStart() ~ customJsEnd()までが$(function(){}内
    // $p->ajaxCall(セレクタ,イベント,関数名,タイプ（Ajaxかどうか）,前処理,後処理);
    // EOT行はスペース等が入るとエラー JS1はタブ*2 JS2はタブ*3
    $p->customJsStart();
        
    // listUpdate 初期処理 データ取得
    $jsString = '';
    $jsString .= "str += '<td><div>' + data[i]['kaisb02'] + '　' + data[i]['bum02'] + '</div></td>';";
    for($i=3; $i < 19; $i++){
        $jsString .= "str += '<td><div>' + data[i]['bum" . sprintf('%02d', $i) . "'] + '</div></td>';";
    }
    $js2 = <<<"EOT"
            var data = JSON.parse(json_data);
            $("#tablebody tr").remove();
            $("#tablebody td").remove();
            var str = '';
            for (var i = 0; i < data[0]['len']; i++){
                str += '<tr>'
                str += '<td><div><button name="button1" style="width:50px;" type="button" value="' + data[i]['kaisb01'] + '-' + data[i]['bum01'] + '">' + data[i]['kaisb01'] + '-' + data[i]['bum01'] + '</button></div></td>';
                $jsString
                str += '<td><div>' + data[i]['cday'] + '</div></td>';
                str += '<td><div>' + data[i]['ctim'] + '</div></td>';
                str += '<td><div>' + data[i]['cid'] + '</div></td>';
                str += '<td><div>' + data[i]['uday'] + '</div></td>';
                str += '<td><div>' + data[i]['utim'] + '</div></td>';
                str += '<td><div>' + data[i]['uid'] + '</div></td>';
                str += '</tr>'
            }
            $("#tablebody").append(str);
            FixedMidashi.remove();
            FixedMidashi.create();
EOT;
    echo $p->ajaxCall('','','listUpdate','ajax','',$js2,'');
 
    // button1 click 編集モーダル表示
    $js1 = <<<"EOT"
            $('[name=button1]').prop("disabled",true);
            var obj = {"bum01":event.target.value};
            params = JSON.stringify(obj);
EOT;
    $js2 = <<<"EOT"
            var data = JSON.parse(json_data);
            $('#modal1').remove();
            $('#modalParent1').append(data[0]['html']);
            $('#modal1').modal({backdrop:'static'});
EOT;
    echo $p->ajaxCall('[name=button1]','click','modalCall','ajax',$js1,$js2,'');

    // createbtn2 click 新規
    $objString = '{';
    for($i=1; $i < 19; $i++){
        $a=sprintf('%02d',$i);
        $objString .= '"bum'.$a.'":$("#bum'.$a.'").val(),';
    }
    $objString = substr($objString,0,-1);
    $objString .= '}';
    $js1 = <<<"EOT"
            $('[name=button2]').prop("disabled",true);
            var obj = $objString;
            params = JSON.stringify(obj);
EOT;
    $js2 = <<<"EOT"
            var data = JSON.parse(json_data);
                if(data[0]['res']){
                $('#modal1').modal('hide');
                listUpdate();
            }else{
                $('[name=button2]').prop("disabled",false);
                alert('重複するCDがあり登録できません');
            }
EOT;
    $fail =  <<<"EOT"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
EOT;
    echo $p->ajaxCall('#createbtn2','click','create','ajax',$js1,$js2,$fail);

    // upbtn1 click 更新
    $objString = '{';
    for($i=1; $i < 19; $i++){
        $a=sprintf('%02d',$i);
        $objString .= '"bum'.$a.'":$("#bum'.$a.'").val(),';
    }
    $objString = substr($objString,0,-1);
    $objString .= '}';
    $js1 = <<<"EOT"
            $('[name=button2]').prop("disabled",true);
            var obj = $objString;
            params = JSON.stringify(obj);
EOT;
    $js2 = <<<"EOT"
            var data = JSON.parse(json_data);
            $('#modal1').modal('hide');
            listUpdate();
EOT;
    $fail =  <<<"EOT"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
EOT;
    echo $p->ajaxCall('#upbtn1','click','update','ajax',$js1,$js2,$fail);
    
    // delbtn1 click 削除
    $js1 = <<<"EOT"
            var diag = '<div class="modal" id="modal2" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><div class=modal-title><b>確認</b></div></div><div class="modal-body"><div>削除します。宜しいですか？</div></div><div class="modal-footer"><button type="button" class="btn btn-default" name="button2" id="cancelbtn2">いいえ</button><button type="button" class="btn btn-danger" name="button2" id="delbtn2">はい</button></div></div></div></div>';
            $('#modal2').remove();
            $('#modalParent2').append(diag);
            $('#modal2').modal({backdrop:'static'});
EOT;
    echo $p->ajaxCall('#delbtn1','click','delready','normal',$js1,'','');

    // delbtn2 click 削除
    $js1 = <<<"EOT"
            $('[name=button2]').prop("disabled",true);
            var obj = {"bum01":$("#bum01").val()};
            params = JSON.stringify(obj);
EOT;
    $js2 = <<<"EOT"
            var data = JSON.parse(json_data);
            $('#modal2').modal('hide');
            $('#modal1').modal('hide');
            listUpdate();
EOT;
    $fail =  <<<"EOT"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
EOT;
    echo $p->ajaxCall('#delbtn2','click','deldat','ajax',$js1,$js2,$fail);

    // JS出力
    // テンプレサンプルデータ削除 / ボタン無効化解除と孫モーダル起動時のスクロール問題対処 / キャンセルボタン処理
    echo <<<"EOT"
    $("#tablebody tr").remove();
    $("#tablebody td").remove();
    
    $('#modalParent1').on('hidden.bs.modal',function(){
		$('[name=button1]').prop("disabled",false);
	});
    $('#modalParent2').on('hidden.bs.modal',function(){
		$('[name=button2]').prop("disabled",false);
        $('body').addClass('modal-open');
	});
    
    $(document).on('click','#cancelbtn1',function(){
		$('#modal1').modal('hide');
	});
    $(document).on('click','#cancelbtn2',function(){
		$('#modal2').modal('hide');
	});

EOT;
    $p->customJsEnd();


}elseif($_GET['linktype'] == 'css'){
    header('Content-Type: text/css; charset=utf-8');
// ★ CSS start
<<<"EOT"

EOT;

}elseif($_POST['ajax'] == 'ajax'){
    // ★ AJAX start
    $data = json_decode($_POST['dat'],true);
    
    // 部門情報(明細)を取得しモーダルHTMLを作成
    if($_POST['func'] == 'modalCall'){
        $sql = 'select * from bumon where kaisb01=? and bum01=?;';
        $par = array($data['kaisb01'],$data['bum01']);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrk[0]['kaisb01'] = ltrim($result['kaisbum01'], "0"); // 0padding clear
            $wrk[0]['kaisb02'] = $result['kaisb02'];
            $wrk[0]['bum01'] = ltrim($result['bum01'], "0"); // 0padding clear
            $wrk[0]['bum02'] = $result['bum02'];
            $wrk[0]['bum03'] = $result['bum03'];
            $wrk[0]['bum04'] = $result['bum04'];
            $wrk[0]['bum05'] = $result['bum05'];
            $wrk[0]['bum06'] = $result['bum06'];
            $wrk[0]['bum07'] = $result['bum07'];
            $wrk[0]['bum08'] = $result['bum08'];
            $wrk[0]['bum09'] = $result['bum09'];
            $wrk[0]['bum10'] = $result['bum10'];
            $wrk[0]['bum11'] = $result['bum11'];
            $wrk[0]['bum12'] = $result['bum12'];
            $wrk[0]['bum13'] = $result['bum13'];
            $wrk[0]['bum14'] = $result['bum14'];
            $wrk[0]['bum15'] = $result['bum15'];
            $wrk[0]['bum16'] = $result['bum16'];
            $wrk[0]['bum17'] = $result['bum17'];
            $wrk[0]['bum18'] = $result['bum18'];
            $wrk[0]['cid'] = $result['cid'];
            $wrk[0]['cday'] = $result['cday'];
            $wrk[0]['ctim'] = $result['ctim'];
            $wrk[0]['uid'] = $result['uid'];
            $wrk[0]['uday'] = $result['uday'];
            $wrk[0]['utim'] = $result['utim'];
        }
        if($wrk[0]['bum01'] == ''){
            $buDisabled = '';
            $delbtn = '';
            $kakbtn = '<button type="button" class="btn btn-success" name="button2" id="createbtn2">作成</button>';
        }else{
            $buDisabled = 'disabled';
            $delbtn = '<button type="button" class="btn btn-danger" name="button2" id="delbtn1">削除</button>';
            $kakbtn = '<button type="button" class="btn btn-primary" name="button2" id="upbtn1">更新</button>';
        }
        $jsString = '';
        $jsArray[0] = array('','コード','部門名','締め日','請求日','時間単位','割増発生','残業発生','休憩時間(始)','休憩時間(終)','郵便番号','都道府県名','市区町村名','町域名','アパート名等','電話番号','FAX番号','メールアドレス','契約タイプ');
        for($i = 1; $i < 19; $i++){
            $a = sprintf('%02d', $i);
            $jsString .= '<div class="form-group"><label class="col-sm-4 control-label">'. $jsArray[0][$i] .'</label><div class="col-sm-8"><input ';
            if($i == 1){$jsString .= $cdDisabled;} // CDは編集不可
            $jsString .= ' type="text" class="form-control" id="bum'. $a .'" value="'. $wrk[0]["bum$a"] .'"></div></div>';
        }
        $wrk[0]['html'] = <<<EOT
        <div class="modal" id="modal1" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title">部門マスター保守</h4>
                </div>
                <div class="modal-body">
                <form class="form-horizontal">
                $jsString
                </form>
                </div>
                <div class="modal-footer">
                $delbtn
                <button type="button" class="btn btn-default" name="button2" id="cancelbtn1">キャンセル</button>
                $kakbtn
                </div>
                </div>
                </div>
                </div>
EOT;
        echo json_encode($wrk);
    }

    // テーブル表示データ更新
    if($_POST['func'] == 'listUpdate'){
        // 部門情報（リスト）取得
        $i = 0;
        $sql = 'select * from bumon;';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrk[$i]['kaisb01'] = ltrim($result['kaisb01'], "0"); // 0padding clear
            $wrk[$i]['kaisb02'] = $result['kaisb02'];
            $wrk[$i]['bum01'] = ltrim($result['bum01'], "0"); // 0padding clear
            $wrk[$i]['bum02'] = $result['bum02'];
            $wrk[$i]['bum03'] = $result['bum03'];
            $wrk[$i]['bum04'] = $result['bum04'];
            $wrk[$i]['bum05'] = $result['bum05'];
            $wrk[$i]['bum06'] = $result['bum06'];
            $wrk[$i]['bum07'] = $result['bum07'];
            $wrk[$i]['bum08'] = $result['bum08'];
            $wrk[$i]['bum09'] = $result['bum09'];
            $wrk[$i]['bum10'] = $result['bum10'];
            $wrk[$i]['bum11'] = $result['bum11'];
            $wrk[$i]['bum12'] = $result['bum12'];
            $wrk[$i]['bum13'] = $result['bum13'];
            $wrk[$i]['bum14'] = $result['bum14'];
            $wrk[$i]['bum15'] = $result['bum15'];
            $wrk[$i]['bum16'] = $result['bum16'];
            $wrk[$i]['bum17'] = $result['bum17'];
            $wrk[$i]['bum18'] = $result['bum18'];
            $wrk[$i]['cday'] = substr($result['cday'],0,4) . '-' . substr($result['cday'],4,2) . '-' . substr($result['cday'],6,2);
            $wrk[$i]['ctim'] = substr($result['ctim'],0,2) . ':' . substr($result['ctim'],2,2) ;
            $wrk[$i]['cid'] = $result['cid'];
            $wrk[$i]['uday'] = substr($result['uday'],0,4) . '-' . substr($result['uday'],4,2) . '-' . substr($result['uday'],6,2);
            $wrk[$i]['utim'] = substr($result['utim'],0,2) . ':' . substr($result['utim'],2,2) ;
            $wrk[$i]['uid'] = $result['uid'];
            $wrk[$i] = str_replace(null,'',$wrk[$i]); // null値除去
            $i++;
        }
        $wrk[0]['len'] = $i;
        echo json_encode($wrk);
    }

    // データ作成
    if($_POST['func'] == 'create'){
        $err = '';
        if(preg_match("/^[a-zA-Z0-9]+$/", $data['bucd'])){
            $err = 'err';
        }
            $today = date('Ymd');
            $now = date('Hi');
            $wrkcd = '';
            $par = array($data['bucd']);
            $sql = "select bum01 from bumon where bum01=?;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($par);
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $wrkcd = $result['bum01'];
            }
            if($wrkcd == ''){
                $sql = "insert into bumon (bum01,bum02,bum03,cday,ctim,cid,uday,utim,uid) values (?,?,?,?,?,'20',?,?,'20');";
                $par = array($data['bucd'],$data['name'],$data['type'],$today,$now,$today,$now);
                $stmt = $pdo->prepare($sql);
                $wrk[0]['res'] = $stmt->execute($par);
            }else{
                $wrk[0]['res'] = false;
            }
            echo json_encode($wrk);
        }

    // データ更新
    if($_POST['func'] == 'update'){
            $today = date('Ymd');
            $now = date('Hi');
            $sql = "update bumon set bum02=?,uday=?,utim=? where bum01=?;";
            $par = array($data['name'],$today,$now,$data['bucd']);
            $stmt = $pdo->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        }

    // データ削除
    if($_POST['func'] == 'deldat'){
            $today = date('Ymd');
            $now = date('Hi');
            $sql = "delete from bumon where bum01=?;";
            $par = array($data['bucd']);
            $stmt = $pdo->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        }
    
}else{
    // ★ HTML start
    $opt = array('JS'); // (J:JS,C:CSS)
    $src = $p->useTemplate('masterBU1.html',$opt);

    // ソース差し替え
    $src['nav'] = customView($_SESSION['autho'],'nav','master');

    // HTML出力
    $p->show($src,$opt);
    $dbh = null;
}
?>