<?php
// ** 従業員マスター ** //

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');
require_once('incl/password.php');

// ★ POST GET

class page extends core {
    private $koumokusu = 34; // 表示するfield数(従業員マスターfield数 + joinテーブルから選択したfield数)
    
    // JS初期処理
    function initJs(){
        $this->js1 = <<<"__"
        // テンプレサンプルデータ削除
        $("#tablebody tr").remove();
        $("#tablebody td").remove();
    
        // ボタン無効化解除と孫モーダル起動時のスクロール問題対処 
        $('#modalParent1').on('hidden.bs.modal',function(){
            $('[name=button1]').prop("disabled",false);
            $('body').addClass('modal-open');
	   });
        $('#modalParent2').on('hidden.bs.modal',function(){
		    $('[name=button2]').prop("disabled",false);
            $('body').addClass('modal-open');
	   });
        $('#modalLoaderParent').on('hidden.bs.modal',function(){
            $('body').addClass('modal-open');
	   });
       
       // パターン選択後の情報呼び出し
       $(document).on('change','#kpcombo',function(){
          
       });
        
       // 検索条件初期値設定
        var json_data_stock_obj = {"jyouken1":"","maekakko1":"","koumoku1":"jyu08","val1":"","enzan1":"=","atokakko1":"","jyouken2":"","maekakko2":"","koumoku2":"","val2":"","enzan2":"","atokakko2":"","jyouken3":"","maekakko3":"","koumoku3":"","val3":"","enzan3":"","atokakko3":"","jyouken4":"","maekakko4":"","koumoku4":"","val4":"","enzan4":"","atokakko4":"","order1":"jyu07","orderopt1":"DESC","order2":"","orderopt2":"","order3":"","orderopt3":"","order4":"","orderopt4":""};
        $('#json_data_stock').val(JSON.stringify(json_data_stock_obj));
       $('#searchbtn2').trigger('click');
       
       // ヒントの表示
       var hintObj = {
       "":"　",
       "jyu01":"従業員コード：現在利用なし。従業員番号が6桁突入したら、「従業員番号」の頭に付加予定。",
       "jyu02":"従業員番号：従業員の番号。数字0～999999まで。",
       "jyu03":"氏名（姓）：（50文字以内）",
       "jyu04":"氏名（名）：（50文字以内）",
       "jyu05":"生年月日：西暦8桁。ハイフンやスラッシュはなし。（例：19801101　→　1980年11月1日生まれ）",
       "jyu06":"性別：m.男 f.女",
       "jyu07":"入社日：西暦8桁。ハイフンやスラッシュはなし。（例：19801101　→　1980年11月1日入社）",
       "jyu08":"退職日：西暦8桁。ハイフンやスラッシュはなし。（例：19801101　→　1980年11月1日入社）",
       "jyu09":"パスワード：ログインするためのパスワード。全角文字も使用可。20文字以内。",
       "jyu10":"権限：システムにおける権限レベル。1:",
       "jyu11":"所属部門CD：所属部門のコード。",
       "bum02":"所属部門名：現在所属している部門名。",
       "jyu12":"役職：3桁。#は1～3の数値。　stf：スタッフ　ss# st# c# j# m# d#：各職位。",
       "jyu13":"単価：時給者の時間当たり単価。",
       "jyu14":"社会保険加入：0：未加入　1：加入",
       "jyu15":"管理領域：担当する部門またはグループコード。",
       "jyu16":"管理権限：管理領域の権限レベル。",
       "jyu17":"郵便番号：7桁。ハイフンやスラッシュはなし。（例：5250058　→　〒525-0058）",
       "jyu18":"都道府県：住所（都道府県名）",
       "jyu19":"市区町村：住所（市区町村）",
       "jyu20":"町域・番地：住所（町域・番地）",
       "jyu21":"アパート名：住所（アパート名など）",
       "jyu22":"電話番号：ハイフン区切り。（例：077-562-6278）",
       "jyu23":"FAX番号：ハイフン区切り。（例：077-567-2415）",
       "jyu24":"メールアドレス：",
       "jyu25":"契約タイプ：契約の形態。",
       "jyu26":"：",
       "jyu27":"：",
       "jyu28":"：",
       "jyu29":"：",
       "jyu30":"："
};
       $(document).on('change','#koumoku1',function(){
            var selectedText = $('#koumoku1 option:selected').text();
            var selectedVal = $('#koumoku1').val();
            $('#hint1').text(hintObj[selectedVal]);
	   });
       $(document).on('change','#koumoku2',function(){
            var selectedText = $('#koumoku2 option:selected').text();
            var selectedVal = $('#koumoku2').val();
            $('#hint2').text(hintObj[selectedVal]);
	   });
       $(document).on('change','#koumoku3',function(){
            var selectedText = $('#koumoku3 option:selected').text();
            var selectedVal = $('#koumoku3').val();
            $('#hint3').text(hintObj[selectedVal]);
	   });
       $(document).on('change','#koumoku4',function(){
            var selectedText = $('#koumoku4 option:selected').text();
            var selectedVal = $('#koumoku4').val();
            $('#hint4').text(hintObj[selectedVal]);
	   });
__;
        $this->addEventListener2();
    }
    
    // 新規・更新・削除モーダル表示(JS)
/*    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var jyuArray = event.target.value.split('-');
            var obj = {"jyu01":"","jyu02":jyuArray[1]};
            params = JSON.stringify(obj);
__;
        $str = modalControl::appendSrc('modal1','data[0]["html"]',"modalParent1");
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $str
__;
        $this->addEventListener2('[name=button1]','click','modalCall','ajax');
    }
*/
    // 新規・更新・削除モーダル表示(JS)
    function button1nmClickJs($val){
        $this->clearJs();
        $this->js1 = <<<"__"
            var jyuArray = event.value.split('-');
            var obj = {"jyu01":"","jyu02":jyuArray[1]};
            params = JSON.stringify(obj);
__;
        $str = modalControl::appendSrc('modal1','data[0]["html"]',"modalParent1");
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $str
__;
        $this->addEventListener2('','wait','modalCall','ajax');
    }
    // 新規・更新・削除モーダル表示(PHP)
    function modalCall($data){
        $sql = 'select * from jyugyoin where jyu01=? and jyu02=?;';
        $par = array($data['jyu01'],$data['jyu02']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            for($i = 1; $i <= $this->koumokusu; $i++){
                $j = sprintf('%02d',$i);
                $wrk[0]["jyu$j"] = $result["jyu$j"];
            }
            if($wrk[0]['jyu05'] != ''){
                $wrk[0]['jyu05'] = substr($wrk[0]['jyu05'],0,4).'-'.substr($wrk[0]['jyu05'],4,2).'-'.substr($wrk[0]['jyu05'],6,2);
            }
            if($wrk[0]['jyu07'] != ''){
                $wrk[0]['jyu07'] = substr($wrk[0]['jyu07'],0,4).'-'.substr($wrk[0]['jyu07'],4,2).'-'.substr($wrk[0]['jyu07'],6,2);
            }
            if($wrk[0]['jyu08'] != ''){
                $wrk[0]['jyu08'] = substr($wrk[0]['jyu08'],0,4).'-'.substr($wrk[0]['jyu08'],4,2).'-'.substr($wrk[0]['jyu08'],6,2);
            }
            if($wrk[0]['jyu09'] != ''){ // パスワード(表示しない)
                $wrk[0]['jyu09'] = '';
            }
            if($wrk[0]['jyu17'] != ''){
                $wrk[0]['jyu17'] = substr($wrk[0]['jyu08'],0,4).'-'.substr($wrk[0]['jyu08'],4,3); // 郵便番号
            }
            $wrk[0]['cid']  = $result['cid'];
            $wrk[0]['cday'] = $result['cday'];
            $wrk[0]['ctim'] = $result['ctim'];
            $wrk[0]['uid']  = $result['uid'];
            $wrk[0]['uday'] = $result['uday'];
            $wrk[0]['utim'] = $result['utim'];
        }
        // modal HTML (id,header,body,footer)
        $id = 'modal1';

        $header = <<<"__"
        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
        <h4 class="modal-title">従業員マスター保守</h4>
__;

        $body = <<<"__"
        <ul class="nav nav-tabs">
        <li class="nav-item active"><a href="#tab1" class="nav-link navbar-default active" data-toggle="tab">契約情報</a></li>
        <li class="nav-item"><a href="#tab2" class="nav-link navbar-default" data-toggle="tab">基本情報</a></li>
        <li class="nav-item"><a href="#tab3" class="nav-link navbar-default" data-toggle="tab">他情報</a></li>
        </ul>
        <div class="tab-content">
        <div id="tab1" class="tab-pane active" style="padding-top:10px;">
        <!--Tab1の内容-->
__;
        $mj1 = '<div class="form-group"><label class="col-sm-';
        $mj2 = ' control-label">';
        $mj3 = '</label><div class="col-sm-';
        $mj4 = '"><input type="text" class="form-control" id="';
        $mj5 = '" value="';
        $mj6 = '"></div><p class="form-control-static col-sm-';
        $mj7 = '">';
        $mj8 = '</p></div>';
        
        if($wrk[0]["jyu02"] != ''){
            $mj4a = '"><input type="text" readonly="true" class="form-control" id="';
        }else{
            $mj4a = '"><input type="text" class="form-control" id="';
        }
        $mj4b = '"><input type="text" readonly="true" class="form-control" id="'; // システム権限用
        
        $body .= <<<"__"
            {$mj1}4{$mj2}従業員番号{$mj3}3{$mj4a}jyu02{$mj5}{$wrk[0]["jyu02"]}{$mj6}5{$mj7}{$mj8}
            {$mj1}4{$mj2}姓（漢字）{$mj3}8{$mj4}jyu03{$mj5}{$wrk[0]["jyu03"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}名（漢字）{$mj3}8{$mj4}jyu04{$mj5}{$wrk[0]["jyu04"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}姓（カナ）{$mj3}8{$mj4}jyu27{$mj5}{$wrk[0]["jyu27"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}名（カナ）{$mj3}8{$mj4}jyu28{$mj5}{$wrk[0]["jyu28"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}入社日{$mj3}3{$mj4}jyu07{$mj5}{$wrk[0]["jyu07"]}{$mj6}5{$mj7}数字のみ8桁又は-付きで入力{$mj8}
            {$mj1}4{$mj2}退職日{$mj3}3{$mj4}jyu08{$mj5}{$wrk[0]["jyu08"]}{$mj6}5{$mj7}数字のみ8桁又は-付きで入力{$mj8}
            {$mj1}4{$mj2}契約タイプ{$mj3}8{$mj4}jyu25{$mj5}{$wrk[0]["jyu25"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}所属部門{$mj3}8{$mj4}jyu11{$mj5}{$wrk[0]["jyu11"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}時給単価{$mj3}3{$mj4}jyu13{$mj5}{$wrk[0]["jyu13"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}手当単価{$mj3}3{$mj4}jyu34{$mj5}{$wrk[0]["jyu34"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}社会保険加入{$mj3}2{$mj4}jyu14{$mj5}{$wrk[0]["jyu14"]}{$mj6}6{$mj7}あり　なし{$mj8}
            {$mj1}4{$mj2}雇用保険加入{$mj3}2{$mj4}jyu29{$mj5}{$wrk[0]["jyu29"]}{$mj6}6{$mj7}あり　なし{$mj8}
            {$mj1}4{$mj2}通勤方法{$mj3}3{$mj4}jyu30{$mj5}{$wrk[0]["jyu30"]}{$mj6}5{$mj7}送迎　徒歩　車　バイク　電車　自転車{$mj8}
            {$mj1}4{$mj2}職群{$mj3}2{$mj4}jyu12{$mj5}{$wrk[0]["jyu12"]}{$mj6}6{$mj7}stf：スタッフ　ss1：SS1　ss2：SS2　st1：ST1社員　st2：ST2社員　c：C職　m：M職　j：J職　d：D職{$mj8}
            {$mj1}4{$mj2}区分１{$mj3}4{$mj4}jyu33{$mj5}{$wrk[0]["jyu33"]}{$mj6}4{$mj7}セクションで自由利用可能な区分{$mj8}

            </div><div id="tab2" class="tab-pane" style="padding-top:10px;"><!--Tab2の内容-->
            {$mj1}4{$mj2}生年月日{$mj3}3{$mj4}jyu05{$mj5}{$wrk[0]["jyu05"]}{$mj6}5{$mj7}数字のみ8桁又は-付きで入力{$mj8}
            {$mj1}4{$mj2}性別{$mj3}2{$mj4}jyu06{$mj5}{$wrk[0]["jyu06"]}{$mj6}5{$mj7}m：男性　f：女性{$mj8}
            {$mj1}4{$mj2}郵便番号{$mj3}3{$mj4}jyu17{$mj5}{$wrk[0]["jyu17"]}{$mj6}5{$mj7}数字のみ7桁又は-付きで入力{$mj8}
            {$mj1}4{$mj2}都道府県{$mj3}3{$mj4}jyu18{$mj5}{$wrk[0]["jyu18"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}市区町村{$mj3}8{$mj4}jyu19{$mj5}{$wrk[0]["jyu19"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}町域・番地{$mj3}8{$mj4}jyu20{$mj5}{$wrk[0]["jyu20"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}アパート名{$mj3}8{$mj4}jyu21{$mj5}{$wrk[0]["jyu21"]}{$mj6}0{$mj7}{$mj8}
            {$mj1}4{$mj2}携帯電話{$mj3}4{$mj4}jyu22{$mj5}{$wrk[0]["jyu22"]}{$mj6}4{$mj7}-付きで入力{$mj8}
            {$mj1}4{$mj2}固定電話{$mj3}4{$mj4}jyu23{$mj5}{$wrk[0]["jyu23"]}{$mj6}4{$mj7}-付きで入力{$mj8}
            {$mj1}4{$mj2}メールアドレス{$mj3}8{$mj4}jyu24{$mj5}{$wrk[0]["jyu24"]}{$mj6}{$mj7}{$mj8}

            </div><div id="tab3" class="tab-pane" style="padding-top:10px;"><!--Tab3の内容-->
            {$mj1}4{$mj2}権限{$mj3}2{$mj4}jyu10{$mj5}{$wrk[0]["jyu10"]}{$mj6}{$mj7}{$mj8}
            {$mj1}4{$mj2}管理領域{$mj3}4{$mj4}jyu15{$mj5}{$wrk[0]["jyu15"]}{$mj6}{$mj7}{$mj8}
            {$mj1}4{$mj2}管理権限{$mj3}2{$mj4}jyu16{$mj5}{$wrk[0]["jyu16"]}{$mj6}{$mj7}{$mj8}
            {$mj1}4{$mj2}システム権限{$mj3}2{$mj4b}jyu31{$mj5}{$wrk[0]["jyu31"]}{$mj6}{$mj7}{$mj8}
            {$mj1}4{$mj2}勤怠打刻パスワード{$mj3}3{$mj4}jyu32{$mj5}{$wrk[0]["jyu32"]}{$mj6}5{$mj7}数字のみ8桁{$mj8}
            {$mj1}4{$mj2}パスワード{$mj3}8{$mj4}jyu09{$mj5}{$wrk[0]["jyu09"]}{$mj6}{$mj7}{$mj8}
__;
        $body .= '</div></div>';

        if($wrk[0]['jyu01'] == '' and $wrk[0]['jyu02'] == ''){
            $footer = <<<"__"
            <button type="button" class="btn btn-default" name="button2" id="cancelbtn1" data-dismiss="modal">キャンセル</button>
            <button type="button" class="btn btn-success" name="button2" id="createbtn2">作成</button>
__;
        }else{
            $footer = <<<"__"
            <button type="button" class="btn btn-danger" name="button2" id="delbtn1">削除</button>
            <button type="button" class="btn btn-default" name="button2" id="cancelbtn1" data-dismiss="modal">キャンセル</button>
            <button type="button" class="btn btn-primary" name="button2" id="upbtn1">更新</button>
__;
        }
        $params = array('id'=>$id,'header'=>$header,'body'=>$body,'footer'=>$footer);
        $wrk[0]['html'] = modalControl::createSrc($params);

        echo json_encode($wrk);
    }

    // 郵便番号change(JS)
    function jyu17KeypressJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"yubin":$('#jyu17').val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<'__'
            var data = JSON.parse(json_data||"null");
            if(data['jusyo1'] != ''){
                setTimeout(function(){$('#jyu18').focus();},0);
            }
__;
        $this->addEventListener2('#jyu17','change','yubin','ajax');
    }
    //
    function yubin($data){
        //$data['yubin'];
        
        $wrk['jyusyo1'] = '';
        echo json_encode($wrk);
    }
    
    // 新規(JS)
    function createbtn2ClickJs(){
        $this->clearJs();
        $objString = '{';
        for($i=1; $i <= $this->koumokusu; $i++){
            $a=sprintf('%02d',$i);
            if($i == 1){
                $objString .= '"jyu'.$a.'":"",';
            }else{
                $objString .= '"jyu'.$a.'":$("#jyu'.$a.'").val(),';
            }
        }
        $objString = substr($objString,0,-1);
        $objString .= '}';
        $this->js1 = <<<"__"
            
            //$('[name=button2]').prop("disabled",true);
            var obj = $objString;
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            if(data[0]['res']){
                $('#modal1').modal('hide');
                $('#searchbtn2').trigger('click');
            }else{
                $('[name=button2]').prop("disabled",false);
                alert('重複するCDがあり登録できません');
            }
__;
        $this->js3 =  <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#createbtn2','click','createData','ajax');
    }
    // 新規(PHP)
    function createData($data){
        $data['jyu05'] = str_replace('-','',$data['jyu05']);
        $data['jyu07'] = str_replace('-','',$data['jyu07']);
        $data['jyu08'] = str_replace('-','',$data['jyu08']);
        $data['jyu17'] = str_replace('-','',$data['jyu17']); // 郵便番号
        $err = '';
        if(preg_match("/^[a-zA-Z0-9]+$/", $data['jyu01'])){
            $err = 'err';
        }
        if(preg_match("/^[a-zA-Z0-9]+$/", $data['jyu02'])){
            $err = 'err';
        }
        // 従業員番号がブランクなら採番
        if(!$data['jyu02']){
            $sql = "select sai02 from saiban where sai01='jyugyoin';";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $bango = $result["sai02"];
            }
            if($bango){
                $data['jyu02'] = $bango;
                $sql = "update saiban set sai02 = (sai02 + 1) where sai01='jyugyoin';";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
            }
        }
        $today = date('Ymd');
        $now = date('Hi');
        $wrkcd = '';
        $par = array($data['jyu01'],$data['jyu02']);
        $sql = "select jyu02 from jyugyoin where (jyu01=? and jyu02=?);";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrkcd = $result['jyu02'];
        }
        if($wrkcd == ''){
            $par = array();
            $sql = "insert into jyugyoin (";
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "jyu".$a.",";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for($i=1; $i <= $this->koumokusu; $i++){
                $sql .= "?,";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",?,?,?,?,?,?);";
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                if($i == 9 and $data["jyu$a"] != ''){ // パスワード指定があればハッシュして更新
                    $par[] = password_hash($data["jyu$a"], PASSWORD_DEFAULT);                
                }else{
                    $par[] = $data["jyu$a"];
                }
            }
            array_push($par,$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
        }else{
            $wrk[0]['res'] = false;
        }
        echo json_encode($wrk);
    }
    
    // 更新(JS)
    function upbtn1ClickJs(){
        $this->clearJs();
        $objString = '{';
        for($i=1; $i <= $this->koumokusu; $i++){
            $a=sprintf('%02d',$i);
            if($i == 1){
                $objString .= '"jyu'.$a.'":"",';
            }else{
                $objString .= '"jyu'.$a.'":$("#jyu'.$a.'").val(),';
            }
        }
        $objString = substr($objString,0,-1);
        $objString .= '}';
        $this->js1 = <<<"__"
            //$('[name=button2]').prop("disabled",true);
            var obj = $objString;
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
        console.log(json_data);
            var data = JSON.parse(json_data);
            $('#modal1').modal('hide');
            $('#searchbtn2').trigger('click');
__;
        $this->js3 =  <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#upbtn1','click','updateData','ajax');
    }
    // 更新(PHP)
    function updateData($data){
        $data['jyu05'] = str_replace('-','',$data['jyu05']);
        $data['jyu07'] = str_replace('-','',$data['jyu07']);
        $data['jyu08'] = str_replace('-','',$data['jyu08']);
        $data['jyu17'] = str_replace('-','',$data['jyu17']); // 郵便番号
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "update jyugyoin set ";
        for($i=3; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            if($i == 9 and $data["jyu$a"] == ''){ // パスワードは入力ない場合更新しない
                
            }else{
                $sql .= "jyu".$a."=?,";
            }
        }
        $sql = substr($sql,0,-1);
        $sql .= ",uday=?,utim=?,uid=? where (jyu01=? and jyu02=?);";
        for($i=3; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            if($i == 9 and $data["jyu$a"] == ''){ // パスワード入力ない場合は更新なし

            }elseif($i == 9 and $data["jyu$a"] != ''){ // パスワード指定があればハッシュして更新
                $par[] = password_hash($data["jyu$a"], PASSWORD_DEFAULT);                
            }else{
                $par[] = $data["jyu$a"];
            }
        }
        array_push($par,$today,$now,$_SESSION['id'],$data["jyu01"],$data["jyu02"]);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($par);
    }
    
    // 削除準備(JS)
    function delbtn1ClickJs(){
        $this->clearJs();
        $str = modalControl::appendSrc('modal2','data[0]["html"]','modalParent2');
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $str
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#delbtn1','click','delmodal','ajax');
    }
    // 
    function delmodal($data){
        $id = 'modal2';
        $header = '<div class=modal-title><b>確認</b></div>';
        $body = '<div>削除します。宜しいですか？</div>';
        $footer = <<<"__"
        <button type="button" class="btn btn-default" name="button2" id="cancelbtn2" data-dismiss="modal">いいえ</button>
        <button type="button" class="btn btn-danger" name="button2" id="delbtn2">はい</button>
__;
        $params = array('id'=>$id,'size'=>'modal-sm','header'=>$header,'body'=>$body,'footer'=>$footer);
        $wrk[0]['html'] = modalControl::createSrc($params);
        echo json_encode($wrk);
    }

    // 削除(JS)
    function delbtn2ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            $('[name=button2]').prop("disabled",true);
            var obj = {"jyu01":"","jyu02":$("#jyu02").val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            $('#modal2').modal('hide');
            $('#modal1').modal('hide');
            $('#searchbtn2').trigger('click');
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#delbtn2','click','deleteData','ajax');
    }
    // 削除(PHP)
    function deleteData($data){
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "delete from jyugyoin where (jyu01=? and jyu02=?);";
        $par = array($data['jyu01'],$data['jyu02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
    }

    // 検索用モーダル表示(JS)
    function searchbtn1ClickJs(){
        $this->clearJs();
        $this->js1 = " params = $('#json_data_stock').val();";
         for($i=1; $i <= 4; $i++){
            $zenkai .= <<<"__"
            $('#jyouken$i').val(zdata['jyouken$i']);
            $('#maekakko$i').val(zdata['maekakko$i']);
            $('#koumoku$i').val(zdata['koumoku$i']);
            $('#val$i').val(zdata['val$i']);
            $('#enzan$i').val(zdata['enzan$i']);
            $('#atokakko$i').val(zdata['atokakko$i']);
__;
        }
        for($i=1; $i <= 4; $i++){
            $zenkai .= <<<"__"
            $('#order$i').val(zdata['order$i']);
            $('#orderopt$i').val(zdata['orderopt$i']);
__;
        }
        $str = modalControl::appendSrc('searchModal1','data[0]["html"]','modalParent1');
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $str

            // 前回選択値の適用
            var zdata = JSON.parse($('#json_data_stock').val()||"null");
            if(zdata){
                $zenkai
            }
__;
        $this->addEventListener2('#searchbtn1','click','searchModalCall','ajax');
    }
    // 検索用モーダル表示(PHP)
    function searchModalCall($data){
        $header = <<<"__"
            <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            <h4 class="modal-title">従業員検索</h4>
__;
        //[01]CD,[02]No,[03]姓,[04]名,[05]生年月日,[06]性別,[07]入社日,[08]退職日,[09]パスワード,[10]権限
        //[11]所属部門CD,[bum02]所属部門名,[12]役職,[13]単価,[14]社会保険加入,[15]管理領域,[16]管理権限,[17]郵便番号,[18]都道府県,[19]市区町村,[20]町域・番地
        //[21]アパート名,[22]電話番号,[23]FAX番号,[24]メールアドレス,[25]契約タイプ);
        $wrk = array(''=>'パターンから選択');
        $comboboxStr = '<OPTION value="">未選択</OPTION>';
        $sql = "select * from kensakupt where kns01=? order by kns02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns02 = $result['kns02'];
            $kns03 = $result['kns03'];
            $comboboxStr .= "<OPTION value='$kns02'>$kns03</OPTION>";
        }
        $wrk = array(''=>'未選択','jyu01'=>'従業員コード','jyu02'=>'従業員番号','jyu03'=>'氏名（姓）','jyu04'=>'氏名（名）','jyu05'=>'生年月日','jyu06'=>'性別','jyu07'=>'入社日','jyu08'=>'退職日','jyu09'=>'パスワード','jyu10'=>'権限','jyu11'=>'所属部門CD','bum02'=>'所属部門','jyu12'=>'役職','jyu13'=>'単価','jyu14'=>'社会保険加入','jyu15'=>'管理領域','jyu16'=>'管理権限','jyu17'=>'郵便番号','jyu18'=>'住所（都道府県）','jyu19'=>'住所（市区町村）','jyu20'=>'住所（町域・番地）','jyu21'=>'住所（アパート）','jyu22'=>'電話番号','jyu23'=>'FAX番号','jyu24'=>'メールアドレス','jyu25'=>'契約タイプ');
        $comboboxStr1 = '';
        foreach($wrk as $key => $val){
            $comboboxStr1 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $wrk = array(''=>'未選択','='=>'に等しい','!='=>'に等しくない','>='=>'以上','<='=>'以下','not in'=>'に含まれない','in'=>'に含まれる','between'=>'の間(以上/以下)','not between'=>'の間にない','like'=>'の文字を含む');
        $comboboxStr2 = '';
        foreach($wrk as $key => $val){
            $comboboxStr2 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $wrk = array(''=>'','and'=>'かつ','or'=>'又は');
        $comboboxStr3 = '';
        foreach($wrk as $key => $val){
            $comboboxStr3 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $wrk = array(''=>'','('=>'(');
        $comboboxStr4 = '';
        foreach($wrk as $key => $val){
            $comboboxStr4 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $wrk = array(''=>'',')'=>')');
        $comboboxStr5 = '';
        foreach($wrk as $key => $val){
            $comboboxStr5 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $wrk = array(''=>'','ASC'=>'昇順','DESC'=>'降順');
        $comboboxStr6 = '';
        foreach($wrk as $key => $val){
            $comboboxStr6 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $body .= <<<"__"
        <div class='form-group form-group-sm' style='padding: 10px;'>
        <div class="col-sm-5">
        <label class="control-label">検索パターン</label>
        <SELECT id='kpcombo' class='form-control'>
        $comboboxStr
        </SELECT>
        </div>
        <div class="col-sm-4">
        <div>　　</div>
        <button type="button" class="btn btn-danger" name="button2" id="patdelbtn1">選択パターンを削除</button>
        <div>　　</div>
        </div>
        <div class="col-sm-3">
        <div>　　</div>
        <button type="button" class="btn btn-default" name="button2" id="cancelbtn3" data-dismiss="modal">キャンセル</button>
        <span>　　</span>
        <button type="button" class="btn btn-primary" name="button2" id="searchbtn2">検索</button>
        </div>
        </div>
__;
        for($i = 1; $i <= 4; $i++){
            $body .= <<<"__"
            <div id='formbox$i' class='form-group form-group-sm' style='border: 1px solid #90d4ff; padding: 10px; margin:10px; border-radius:5px;'>
            <div class="col-sm-10">
            <div><label class='control-label' style='color:#0000FF;'>■検索条件$i</label></div>
            <label class='control-label' id='hint$i' style='color:#6fbdff; width:100%; text-align:left;'>　</label>
            </div>
            <div class="col-sm-2">
            <label class="control-label">AND OR</label>
            <SELECT id='jyouken$i' class='form-control'>
            $comboboxStr3
            </SELECT>
            </div>
            <div class="col-sm-2">
            <label class="control-label">括弧(前)</label>
            <SELECT id='maekakko$i' class='form-control'>
            $comboboxStr4
            </SELECT>
            </div>
            <div class="col-sm-3">
            <label class="control-label">検索項目</label>
            <SELECT id='koumoku$i' class='form-control'>
            $comboboxStr1
            </SELECT>
            </div>
            <div class="col-sm-3">
            <label class="control-label">条件に使う値</label>
            <input type='text' class='form-control' id='val$i' value='' data-toggle='tooltip' title='複数値を入力する場合はカンマで区切って下さい'>
            </div>
            <div class="col-sm-2">
            <label class="control-label">検索条件</label>
            <SELECT id='enzan$i' class='form-control'>
            $comboboxStr2
            </SELECT>
            </div>
            <div class="col-sm-2">
            <label class="control-label">括弧(後)</label>
            <SELECT id='atokakko$i' class='form-control'>
            $comboboxStr5
            </SELECT>
            </div>
            </div>
__;
        }
        // 並び順
        $body .= <<<"__"
            <div id='formbox$i' class='form-group form-group-sm' style='border: 1px solid #cccccc; padding: 10px; margin:10px; border-radius:5px;'>
            <div class="col-sm-2">
            <div><label class='control-label' style='color:#e66000;'>■並び順</label></div>
            <label class='control-label'>　</label>
            </div>
            <div class="col-sm-3">
            <label class="control-label">並び替え項目１</label>
            <SELECT id='order1' class='form-control'>
            $comboboxStr1
            </SELECT>
            </div>
            <div class="col-sm-2">
            <label class="control-label">昇降</label>
            <SELECT id='orderopt1' class='form-control'>
            $comboboxStr6
            </SELECT>
            </div>
            <div class="col-sm-3">
            <label class="control-label">並び替え項目２</label>
            <SELECT id='order2' class='form-control'>
            $comboboxStr1
            </SELECT>
            </div>
            <div class="col-sm-2">
            <label class="control-label">昇降</label>
            <SELECT id='orderopt2' class='form-control'>
            $comboboxStr6
            </SELECT>
            </div>
            <div class="col-sm-3 col-sm-offset-2">
            <label class="control-label">並び替え項目３</label>
            <SELECT id='order3' class='form-control'>
            $comboboxStr1
            </SELECT>
            </div>
            <div class="col-sm-2">
            <label class="control-label">昇降</label>
            <SELECT id='orderopt3' class='form-control'>
            $comboboxStr6
            </SELECT>
            </div>
            <div class="col-sm-3">
            <label class="control-label">並び替え項目４</label>
            <SELECT id='order4' class='form-control'>
            $comboboxStr1
            </SELECT>
            </div>
            <div class="col-sm-2">
            <label class="control-label">昇降</label>
            <SELECT id='orderopt4' class='form-control'>
            $comboboxStr6
            </SELECT>
            </div>
            </div>
__;
        $footer = <<<"__"
                <div class='form-group form-group-sm' style='padding:10px;'>
                <div class="col-sm-4" style="text-align:left;">
                    <label class="control-label">パターン保存名</label>
                    <input type='text' class='form-control' id='ptnm' value=''>
                </div>
                <div class="col-sm-2">
                <div>　　</div>
                <button type="button" class="btn btn-success" name="button2" id="patcrtbtn1">パターンを保存</button>
                </div>
                </div>
__;
        $params = array('id'=>'searchModal1','size'=>'modal-lg','header'=>$header,'body'=>$body,'footer'=>$footer);
        $wrk[0]['html'] = modalControl::createSrc($params);

        echo json_encode($wrk);
    }
    
    // 検索(JS)
    function searchbtn2ClickJs(){
        $this->clearJs();
        //[01]CD,[02]No,[03]姓,[04]名,[05]生年月日,[06]性別,[07]入社日,[08]退職日,[09]パスワード,[10]権限
        //[11]所属部門,[12]役職,[13]単価,[14]社会保険加入,[15]管理領域,[16]管理権限,[17]郵便番号,[18]都道府県,[19]市区町村,[20]町域・番地
        //[21]アパート名,[22]携帯電話番号,[23]固定電話番号,[24]メールアドレス,[25]契約タイプ,[26],[27],[28],[29]雇用保険加入,
        //[30]通勤方法,[32]勤怠打刻パス,[33]区分１,[34]手当単価
        $numArray = array('jyu07','jyu08','jyu25','jyu11','bum02','jyu12','jyu33','jyu13','jyu34','jyu14','jyu29','jyu05','jyu06','jyu17','jyu18','jyu19','jyu20','jyu21','jyu22','jyu23','jyu24','jyu10','jyu15','jyu16','jyu09','jyu32','jyu30');
        for($i=0; $i < 27; $i++){
            $jsString .= "str += '<td><div>' + data[i]['" . $numArray[$i] . "'] + '</div></td>';";
        }
        // 検索PT記録用obj作成
        $this->js1 = " var obj = {";
        for($i=1; $i <= 4; $i++){
            $this->js1 .= "'jyouken$i':$('#jyouken$i').val(),";
            $this->js1 .= "'maekakko$i':$('#maekakko$i').val(),";
            $this->js1 .= "'koumoku$i':$('#koumoku$i').val(),";
            $this->js1 .= "'val$i':$('#val$i').val(),";
            $this->js1 .= "'enzan$i':$('#enzan$i').val(),";
            $this->js1 .= "'atokakko$i':$('#atokakko$i').val(),";
        }
        for($i=1; $i <= 4; $i++){
            $this->js1 .= "'order$i':$('#order$i').val(),";
            $this->js1 .= "'orderopt$i':$('#orderopt$i').val(),";
        }
        $this->js1 = substr($this->js1,0,-1);
        $this->js1 .= '};';
        $this->js1 .= <<<"__"
        params = JSON.stringify(obj);
        $('#json_data_stock').val(params);
        $('#modalLoader2').modal({backdrop:'static'});
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||null);
            $("#tablebody tr").remove();
            $("#tablebody td").remove();
            var str = '';
            for (var i = 0; i < data[0]['len']; i++){
                str += '<tr>'
                str += '<td><div><button onclick="modalCall(this);" style="width:50px;" type="button" value="' + data[i]['jyu01'] + '-' + data[i]['jyu02'] + '">' + data[i]['jyu01'] + data[i]['jyu02'] + '</button></div></td>';
                str += '<td><div>' + data[i]['jyu03'] + ' ' +data[i]['jyu04'] + '</div></td>';
                str += '<td><div>' + data[i]['jyu27'] + ' ' +data[i]['jyu28'] + '</div></td>';
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
            //FixedMidashi.syncValue(button);
__;
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->js4 = "$('#searchModal1').modal('hide'); $('#modalLoader2').modal('hide');";

        $this->addEventListener2('#searchbtn2','click','searchData','ajax');
    }
    // 検索データ取得(PHP)
    function searchData($data){
        // 条件式の組み立て
        for($i=1; $i <= 4; $i++){
            if($data["jyouken$i"] != '' or $i == 1){
                if($i != 1){
                    $sqlwhere .= ' ' . $data["jyouken$i"] . ' ';
                }
                if($data["enzan$i"] == 'in' or $data["enzan$i"] == 'not in'){
                    $val = explode(',',$data["val$i"]);
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' (';
                    foreach($val as $sval){
                        $sqlwhere .= "'$sval',";
                    }
                    $sqlwhere = substr($sqlwhere,0,-1);
                    $sqlwhere .= ')' . $data["atokakko$i"];
                }elseif ($data["enzan$i"] == 'between' or $data["enzan$i"] == 'not between'){
                    $val = explode(',',$data["val$i"]);
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val[0]'" . ' and ' . "'$val[1]'" . $data["atokakko$i"];
                }elseif ($data["enzan$i"] == 'like'){
                    $val = '%' . $data["val$i"] . '%';
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val'" . $data["atokakko$i"];
                }else{
                    $val = $data["val$i"];
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val'" . $data["atokakko$i"];
                }
            }
        }
        $orderby = '';
        for($i=1; $i <= 4; $i++){
            if($data["order$i"]){
                $orderby .= $data["order$i"] . ' ' . $data["orderopt$i"] . ',';
            }
        }
        if($orderby != ''){
            $orderby = 'order by ' . substr($orderby,0,-1); // 余分カンマ削除
        }
        $i = 0;
        $sql = "select *,jyugyoin.cday,jyugyoin.ctim,jyugyoin.cid,jyugyoin.uday,jyugyoin.utim,jyugyoin.uid from jyugyoin left join bumon on jyu26 = bum01 and jyu11 = bum02 where $sqlwhere $orderby;"; // 所属部門CD,所属部門No
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrk[$i]['jyu01'] = $result['jyu01'];
            $wrk[$i]['jyu02'] = $result['jyu02'];
            $wrk[$i]['jyu03'] = $result['jyu03'];
            $wrk[$i]['jyu04'] = $result['jyu04'];
            if($result['jyu05']){
                $wrk[$i]['jyu05'] = substr($result['jyu05'],0,4) . '-' . substr($result['jyu05'],4,2) . '-' . substr($result['jyu05'],6,2);
            }else{
                $wrk[$i]['jyu05'] = '';
            }
            if($result['jyu06'] == 'f'){
                $wrk[$i]['jyu06'] = '女';
            }elseif($result['jyu06'] == 'm'){
                $wrk[$i]['jyu06'] = '男';
            }else{
                $wrk[$i]['jyu06'] = '';
            }
            if($result['jyu07']){
                $wrk[$i]['jyu07'] = substr($result['jyu07'],0,4) . '-' . substr($result['jyu07'],4,2) . '-' . substr($result['jyu07'],6,2);
            }else{
                $wrk[$i]['jyu07'] = '';
            }
            if($result['jyu08']){
                $wrk[$i]['jyu08'] = substr($result['jyu08'],0,4) . '-' . substr($result['jyu08'],4,2) . '-' . substr($result['jyu08'],6,2);
            }else{
                $wrk[$i]['jyu08'] = '';
            }            
            if($result['jyu09'] != ''){
                $wrk[$i]['jyu09'] = '***';
            }else{
                $wrk[$i]['jyu09'] = '';
            }
            $wrk[$i]['jyu10'] = $result['jyu10'];
            $wrk[$i]['jyu11'] = $result['jyu11'];
            $wrk[$i]['bum02'] = $result['bum02'];
            $wrk[$i]['jyu12'] = $result['jyu12'];
            $wrk[$i]['jyu13'] = $result['jyu13'];
            $wrk[$i]['jyu14'] = $result['jyu14'];
            $wrk[$i]['jyu15'] = $result['jyu15'];
            $wrk[$i]['jyu16'] = $result['jyu16'];
            if($result['jyu17']){
                $wrk[$i]['jyu17'] = substr($result['jyu17'],0,3) . '-' . substr($result['jyu17'],3,4);
            }else{
                $wrk[$i]['jyu17'] = '';
            }
            $wrk[$i]['jyu18'] = $result['jyu18'];
            $wrk[$i]['jyu19'] = $result['jyu19'];
            $wrk[$i]['jyu20'] = $result['jyu20'];
            $wrk[$i]['jyu21'] = $result['jyu21'];
            $wrk[$i]['jyu22'] = $result['jyu22'];
            $wrk[$i]['jyu23'] = $result['jyu23'];
            $wrk[$i]['jyu24'] = $result['jyu24'];
            $wrk[$i]['jyu25'] = $result['jyu25'];
            $wrk[$i]['jyu26'] = $result['jyu26'];

            $wrk[$i]['jyu27'] = $result['jyu27'];
            $wrk[$i]['jyu28'] = $result['jyu28'];
            $wrk[$i]['jyu29'] = $result['jyu29'];
            $wrk[$i]['jyu30'] = $result['jyu30'];
            $wrk[$i]['jyu31'] = $result['jyu31'];
            $wrk[$i]['jyu32'] = $result['jyu32'];
            $wrk[$i]['jyu33'] = $result['jyu33'];
            $wrk[$i]['jyu34'] = $result['jyu34'];
            
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
    
    // パターン保存ボタンクリック
    function patcrtbtn1ClickJs(){
        $this->clearJs();
        // 検索PT記録用obj作成
        $jsString = '';
        for($i=1; $i <= 4; $i++){
            $jsString .= "jyouken$i:$('#jyouken$i').val(),";
            $jsString .= "maekakko$i:$('#maekakko$i').val(),";
            $jsString .= "koumoku$i:$('#koumoku$i').val(),";
            $jsString .= "val$i:$('#val$i').val(),";
            $jsString .= "enzan$i:$('#enzan$i').val(),";
            $jsString .= "atokakko$i:$('#atokakko$i').val(),";
        }
        for($i=1; $i <= 4; $i++){
            $jsString .= "order$i:$('#order$i').val(),";
            $jsString .= "orderopt$i:$('#orderopt$i').val(),";
        }
        $jsString = substr($jsString,0,-1);

        $this->js1 = <<<"__"
            var obj = {{$jsString}};
            var kensakustr = JSON.stringify(obj);
            params = {ptnm:$('#ptnm').val(),ptdef:kensakustr};
            params = JSON.stringify(params);
__;
        $str = modalControl::appendSrc('modal2','data[0]["html"]','modalParent2');
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            if(data[0]['res']){
                $('#ptnm').val('');
            }
            $str
            patternListUpdate();
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#patcrtbtn1','click','createPattern','ajax');
    }
    // 新規パターン保存(PHP)
    function createPattern($data){
        $err = '';
        if($data['ptnm']) {
            $today = date('Ymd');
            $now = date('Hi');
            $wrkcd = '';
            $par = array($_SESSION['id']);
            $sql = "select max(kns02)+1 as ptcd from kensakupt where kns01=? group by kns01;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $wrkcd = $result['ptcd'];
            }
            if($wrkcd == ''){$wrkcd = 1;}
            $par = array();
            $sql = "insert into kensakupt (";
            for($i=1; $i <= 4; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "kns".$a.",";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for($i=1; $i <= 4; $i++){
                $sql .= "?,";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",?,?,?,?,?,?);";
            array_push($par,$_SESSION['id'],$wrkcd,$data["ptnm"],$data["ptdef"],$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            if($wrk[0]['res']){
                // JsModalのHTML
                $header = '<h4>通知<h4>';
                $body = '<div>正常に保存されました</div>';
                $footer = '<button type="button" class="btn btn-success" name="button2" data-dismiss="modal">OK</button>';
                
            }else{
                $header = '<h4>エラー<h4>';
                $body = '<div>エラーが発生しました。</div>';
                $footer = '<button type="button" class="btn btn-danger" name="button2" data-dismiss="modal">OK</button>';
            }
        }else{
            $wrk[0]['res'] = false;
            // JsModalのHTML
            $header = '<h4>エラー<h4>';
            $body = '<div>保存名を入力して下さい</div>';
            $footer = '<button type="button" class="btn btn-danger" name="button2" data-dismiss="modal">OK</button>';
        }
        $params = array('id'=>'modal2','size'=>'modal-sm','header'=>$header,'body'=>$body,'footer'=>$footer);
        $wrk[0]['html'] = modalControl::createSrc($params);
        
        echo json_encode($wrk);
    }
    // パターン取得・更新
    function patternListUpdateJs(){
        $this->clearJs();
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#kpcombo option').remove();
            $('#kpcombo').append(data[0]["html"]);
__;
        $this->addEventListener2('','wait','patternListUpdate','ajax');
    }
    //
    function patternListUpdate($data){
        $wrk[0]['html'] = "<OPTION value=''>未選択</OPTION>";
        $sql = "select * from kensakupt where kns01=? order by kns02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns02 = $result['kns02'];
            $kns03 = $result['kns03'];
            $wrk[0]['html'] .= "<OPTION value='$kns02'>$kns03</OPTION>";
        }
        echo json_encode($wrk);
    }
    
    // パターン変更
    function kpcomboSelectJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"kns02":$('#kpcombo').val()};
            params = JSON.stringify(obj);
__;
        for($i=0; $i < 5; $i++){
            $jsString .= <<<"__"
            $('#jyouken$i').val(json['jyouken$i']);
            $('#maekakko$i').val(json['maekakko$i']);
            $('#koumoku$i').val(json['koumoku$i']);
            $('#val$i').val(json['val$i']);
            $('#enzan$i').val(json['enzan$i']);
            $('#atokakko$i').val(json['atokakko$i']);
__;
        }
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            var json = JSON.parse(data[0]['str']);
            $jsString
__;
        $this->addEventListener2('#kpcombo','change','patternRead','ajax');
    }
    //
    function patternRead($data){
        $sql = "select * from kensakupt where kns01=? and kns02=?;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id'],$data['kns02']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns04 = $result['kns04'];
        }
        $wrk[0]['str'] = $kns04;
        echo json_encode($wrk);
    }


    // パターン削除前(JS)
    function patdelbtn1ClickJs(){
        $this->clearJs();
        $str = modalControl::appendSrc('modal2','data[0]["html"]','modalParent2');
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $str
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#patdelbtn1','click','patdelmodal','ajax');
    }
    // 
    function patdelmodal($data){
        $id = 'modal2';
        $header = '<div class=modal-title><b>確認</b></div>';
        $body = '<div>削除します。宜しいですか？</div>';
        $footer = <<<"__"
        <button type="button" class="btn btn-default" name="button2" data-dismiss="modal">いいえ</button>
        <button type="button" class="btn btn-danger" name="button2" id="patdelbtn2">はい</button>
__;
        $params = array('id'=>$id,'size'=>'modal-sm','header'=>$header,'body'=>$body,'footer'=>$footer);
        $wrk[0]['html'] = modalControl::createSrc($params);
        echo json_encode($wrk);
    }

    // パターン削除(JS)
    function patdelbtn2ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"kns02":$("#kpcombo").val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            $('#modal2').modal('hide');
            patternListUpdate();
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#patdelbtn2','click','patdeleteData','ajax');
    }
    // パターン削除(PHP)
    function patdeleteData($data){
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "delete from kensakupt where (kns01=? and kns02=?);";
        $par = array($_SESSION['id'],$data['kns02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
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
    $params = array('title'=>'従業員マスター保守');
    $p->src['header'] = headerReplace::createSrc($params);
    $p->src['standard'] = standardComponentsLoad::createSrc();

    // bootstrap ナビゲーション
    $params = array('autho'=>$_SESSION['autho'], 'active'=>'master', 'name'=>$_SESSION['name']);
    $p->src['nav'] = bootstrapNavigationReplace::createSrc($params);

    $p->show();
}
?>