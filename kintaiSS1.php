<?php
// ** 勤怠修正・確定(時給) ** //

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');

// ★ POST GET

class page extends core {
    private $koumokusu = 26; // 表示するfield数(従業員マスターfield数 + joinテーブルから選択したfield数)
    
    // JS初期処理
    function initJs(){
        $date = new DateTime();
        $today = $date->format('Y-m-d');

        $this->js1 = <<<"__"
        // 本日を選択
        $('#cal1').val('$today');
        $('#cal1').trigger('change');
        
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
       
__;
        $this->addEventListener2('','','','');
    }
    
    // 年月日変更(JS)
    function dateChangeJs(){
        $this->clearJs();
        $this->js1 = <<<'__'
            var obj = {"date":$('#cal1').val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            //console.log(json_data);
            var data = JSON.parse(json_data||"null");
            $("#tablebody tr").remove();
            $("#tablebody td").remove();
            $("#tablebody").append(data[0]['html']);
            FixedMidashi.remove();
            FixedMidashi.create();
            $('#ptn').val('');
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#cal1','change','readData','ajax');
    }
    //
    function readData($data){
		$res[0]['html'] = '';
		$selectday = str_replace('-','',$data['date']);
		$par=array($selectday);
        $sql = "select * from kintai left join jyugyoin on jyu01=knt01 and jyu02=knt02 where knt03=? order by knt01,knt02;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        $i=1;
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $hiduke = substr($result['knt03'],0,4). '/' .substr($result['knt03'],4,2). '/' .substr($result['knt03'],6,2);
			$name = $result['jyu03']. ' ' .$result['jyu04'];
			$cd = $result['knt01'] . $result['knt02'];
            $kubun = $result['knt04'];
            $kubunK = $result['knt09'];
            $sji = substr($result['knt10'],0,2). ':' .substr($result['knt10'],2,2);
            $tji = substr($result['knt11'],0,2). ':' .substr($result['knt11'],2,2);
            $jsji = substr($result['knt05'],0,2). ':' .substr($result['knt05'],2,2);
            $jtji = substr($result['knt06'],0,2). ':' .substr($result['knt06'],2,2);
            $kyukei1 = $result['knt12'] + 0 .'h';
            $kyukei2 = $result['knt13'] + 0 .'h';
            if($tji == ':'){$tji = '';}
			if($sji == ':'){$sji = '';}
            if($jtji == ':'){$jtji = '';}
			if($jsji == ':'){$jsji = '';}
            if($kyukei1 == '0h' and $kyukei2 == '0h'){
                $kyukei = '';
            }elseif($kyukei2 != '0h'){
                $kyukei = $kyukei2.'(深夜)';
            }else{
                $kyukei = $kyukei1;
            }
//			<td><input type='text' class='form-control input-sm' value='$sji'></td>
            $yukyu = '';
            $furik = '';
            $furis = '';
            if($kubunK == '有休'){
                $yukyu = ' selected';
            }elseif($kubunK == '振休'){
                $furik = ' selected';
            }elseif($kubunK == '振出'){
                $furis = ' selected';
            }
            $res[0]['html'] .= <<<"__"
			<tr><td>$hiduke</td><td>$cd</td><td>$name</td>
            <td contenteditable='true' id='kubunk$i'>$kubunk</td>
			<td contenteditable='true' id='sji$i'>$sji</td>
			<td contenteditable='true' id='tji$i'>$tji</td>
			<td contenteditable='true' id='kyukei$i'>$kyukei</td>
			<td id='zangyou$i'>$zangyou</td><td id='kei$i'>$kei</td><td id='jsji$i'>$jsji</td><td id='jtji$i'>$jtji</td><td></td></tr>
__;
            $i++;
		}
        echo json_encode($res);
    }  
    

    // パターン適用(JS)
    function patternApplyJs(){
        $this->clearJs();
        $this->js1 = <<<'__'
            var obj = {"date":$('#cal1').val(),"pattern":$('#ptn').val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $("#tablebody tr").remove();
            $("#tablebody td").remove();
            $("#tablebody").append(data[0]['html']);
            FixedMidashi.remove();
            FixedMidashi.create();
            $('#keisan').trigger('click'); // 計算処理
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#ptn','change','patternApply','ajax');
    }
    //
    function patternApply($data){
		$res[0]['html'] = '';
		$selectday = str_replace('-','',$data['date']);
		$par=array($selectday);
        $sql = "select * from kintai left join jyugyoin on jyu01=knt01 and jyu02=knt02 where knt03=? order by knt01,knt02;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        $i=1;
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $hiduke = substr($result['knt03'],0,4). '/' .substr($result['knt03'],4,2). '/' .substr($result['knt03'],6,2);
			$name = $result['jyu03']. ' ' .$result['jyu04'];
			$cd = $result['knt01'] . $result['knt02'];
            $kubun = $result['knt04'];
            $kubunK = $result['knt09'];
            $sji = substr($result['knt10'],0,2). ':' .substr($result['knt10'],2,2);
            $tji = substr($result['knt11'],0,2). ':' .substr($result['knt11'],2,2);
            $jsji = substr($result['knt05'],0,2). ':' .substr($result['knt05'],2,2);
            $jtji = substr($result['knt06'],0,2). ':' .substr($result['knt06'],2,2);
            $kyukei1 = $result['knt12'] + 0 .'h';
            $kyukei2 = $result['knt13'] + 0 .'h';
			if($sji == ':'){$sji = '';}
            if($tji == ':'){$tji = '';}
			if($jsji == ':'){$jsji = '';}
            if($jtji == ':'){$jtji = '';}
            if($kyukei1 == '0h' and $kyukei2 == '0h'){
                $kyukei = '';
            }elseif($kyukei2 != '0h'){
                $kyukei = $kyukei2.'(深夜)';
            }else{
                $kyukei = $kyukei1;
            }
            
            // パターン適用
            
            // バリア通常パターン
            if($data['pattern'] == 'baria_normal'){
                // 出勤=>7:00-8:00に来た人は8:00出勤 8:00-8:30に来た人は8:30出勤 それ以外は処理しない
                if($jsji != ''){
                    $sji = '';
                    $sji_w = substr($jsji,0,2).substr($jsji,3,2);
                    $sji_w = (int)$sji_w;
                    if($sji_w <= (int)'0700'){$sji = '';}
                    if($sji_w > (int)'0700' and $sji_w <= (int)'0800'){$sji = "08:00";}
                    if($sji_w > (int)'0800' and $sji_w <= (int)'0830'){$sji = "08:30";}
                }
                // 退勤=>16:45-17:15に帰った人は17:00退勤 8:00-8:30に来た人は8:30出勤 それ以外は処理しない
                if($jtji != ''){
                    $tji = '';
                    $tji_w = substr($jtji,0,2).substr($jtji,3,2);
                    if($tji_w >= 1645 and $tji_w <= 1715){$tji = "17:00";}
                    if($tji_w > 1715 or $tji_w < 1645){
                        if(substr($jtji,3,2) > 00 and substr($jtji,3,2) <= 15){$tji = substr($jtji,0,2).':00';}
                        if(substr($jtji,3,2) > 15 and substr($jtji,3,2) <= 30){$tji = substr($jtji,0,2).':15';}
                        if(substr($jtji,3,2) > 30 and substr($jtji,3,2) <= 45){$tji = substr($jtji,0,2).':30';}
                        if(substr($jtji,3,2) > 45 and substr($jtji,3,2) <= 60){$tji = substr($jtji,0,2).':45';}
                    }
                }
            }
            

            $yukyu = '';
            $furik = '';
            $furis = '';
            if($kubunK == '有休'){
                $yukyu = ' selected';
            }elseif($kubunK == '振休'){
                $furik = ' selected';
            }elseif($kubunK == '振出'){
                $furis = ' selected';
            }
            
            $res[0]['html'] .= <<<"__"
			<tr><td>$hiduke</td><td>$cd</td><td>$name</td>
            <td contenteditable='true' id='kubunk$i'>$kubunk</td>
			<td contenteditable='true' id='sji$i'>$sji</td>
			<td contenteditable='true' id='tji$i'>$tji</td>
			<td contenteditable='true' id='kyukei$i'>$kyukei</td>
			<td id='zangyou$i'>$zangyou</td><td id='kei$i'>$kei</td><td id='jsji$i'>$jsji</td><td id='jtji$i'>$jtji</td><td></td></tr>
__;
            $i++;
		}
        echo json_encode($res);
    }  

    
    // 再計算(JS)
    function keisanClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
        var data = [];
        var tr = $("table tbody tr");//全行を取得
        for( var i=0,l=tr.length;i<l; i++ ){
            var cells = tr.eq(i).children(); //1行目から順にth、td問わず列を取得
            for( var j=0,m=cells.length;j<m;j++ ){
                if( typeof data[i] == "undefined" )
                data[i] = [];
                data[i][j] = cells.eq(j).text(); //i行目j列の文字列を取得
            }
            // data[i][4] data[i][5]
            var wrkstr = data[i][4].replace( /:/g , "" );
            var st_ji = wrkstr.slice(0,2);
            var st_fn = wrkstr.slice(2);
            wrkstr = data[i][5].replace( /:/g , "" );
            var en_ji = wrkstr.slice(0,2);
            var en_fn = wrkstr.slice(2);
            
            // 総労働時間計算
            var st_jiw = st_ji;
            var st_fnw = st_fn;
            var en_jiw = en_ji;
            var en_fnw = en_fn;
            var a; // ワーク
            var total_fn=0; // 休憩引き前の総労働時間（分）
            var kyukei=0; // 休憩時間
            var souroudou=0; // 総労働時間
            var zangyou=0; // 残業時間
            var kyusyutu=0; // 休出時間
            a = 60 - Number(st_fn); // 最初の切り良い時間までの分を足す
            st_fnw = 0; // 足したので分を0にする
            st_jiw++; // 足したので時間を1増やす
            a = a + Number(en_fn); // 退勤時間の半端な分を足す
            a = a + Number(en_jiw - st_jiw) * 60; // (退勤時 - 出勤時)の分を足す
            total_fn = a;
            
            // 休憩時間・総労働時間計算
            if(total_fn > 360){ // 6h超の勤務は休憩1時間
                kyukei=1;
                souroudou = total_fn - 60;
            }else{
                kyukei=0;
                souroudou = total_fn;
            }
            
            $('#kei'+(i+1)).text(souroudou);
            $('#zangyou'+(i+1)).text(zangyou);
            $('#kyukei'+(i+1)).text(kyukei);
            console.log(a);
        }

//params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            $('#modal1').modal('hide');
            $('#searchbtn2').trigger('click');
__;
        $this->addEventListener2('#keisan','click','','');
    }

    
    // 更新(JS)
    function upbtn1ClickJs(){
        $this->clearJs();
        $objString = '{';
        for($i=1; $i <= 2; $i++){
            $a=sprintf('%02d',$i);
            $objString .= '"jyu'.$a.'":$("#jyu'.$a.'").val(),';
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
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "update jyugyoin set ";
        for($i=3; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            $sql .= "jyu".$a."=?,";
        }
        $sql = substr($sql,0,-1);
        $sql .= ",uday=?,utim=?,uid=? where (jyu01=? and jyu02=?);";
        for($i=3; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            $par[] = $data["jyu$a"];
        }
        array_push($par,$today,$now,$_SESSION['id'],$data["jyu01"],$data["jyu02"]);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
    }
    
    
    
    
    
    // 新規・更新・削除モーダル表示(JS)
    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var jyuArray = event.target.value.split('-');
            var obj = {"jyu01":jyuArray[0],"jyu02":jyuArray[1]};
            params = JSON.stringify(obj);
__;
        $str = modalControl::appendSrc('modal1','data[0]["html"]',"modalParent1");
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $str
__;
        $this->addEventListener2('[name=button1]','click','modalCall','ajax');
    }
    // 新規・更新・削除モーダル表示(PHP)
    function modalCall($data){
        $sql = 'select * from jyugyoin where jyu01=? and jyu02=?;';
        $par = array($data['jyu01'],$data['jyu02']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            for($i = 1; $i <= 25; $i++){
                $j = sprintf('%02d',$i);
                $wrk[0]["jyu$j"] = $result["jyu$j"];
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
        <div class="tab-content" style="padding-bottom:10px;">
        <div id="tab1" class="tab-pane active">
        <!--Tab1の内容-->
__;
        $subArray = array('[01]CD','[02]No','[03]姓','[04]名','[07]入社日','[08]退職日','[25]契約タイプ','[11]所属部門','[12]役職','[13]単価','[14]社会保険加入');
        $numArray = array('01','02','03','04','07','08','25','11','12','13','14');
        for($i = 0; $i < count($subArray); $i++){
            $body .= '<div class="form-group"><label class="col-sm-4 control-label">'. substr($subArray[$i],4) .'</label><div class="col-sm-8"><input ';
            $body .= ' type="text" class="form-control" id="jyu'. $numArray[$i] .'" value="'. $wrk[0]["jyu$numArray[$i]"] .'"></div></div>';
        }
        $body .= '</div><div id="tab2" class="tab-pane"><!--Tab2の内容-->';
        $subArray = array('[05]生年月日','[06]性別','[17]郵便番号','[18]都道府県','[19]市区町村','[20]町域・番地','[21]アパート名','[22]電話番号','[23]FAX番号','[24]メールアドレス');
        $numArray = array('05','06','17','18','19','20','21','22','23','24');
        for($i = 0; $i < count($subArray); $i++){
            $body .= '<div class="form-group"><label class="col-sm-4 control-label">'. substr($subArray[$i],4) .'</label><div class="col-sm-8"><input ';
            if($numArray[$i] == '01' or $numArray[$i] == '02'){$body .= $editDisabled;}
            $body .= ' type="text" class="form-control" id="jyu'. $numArray[$i] .'" value="'. $wrk[0]["jyu$numArray[$i]"] .'"></div></div>';
        }
        $body .= '</div><div id="tab3" class="tab-pane"><!--Tab3の内容-->';
        $subArray = array('[10]権限','[15]管理領域','[16]管理権限','[09]パスワード');
        $numArray = array('10','15','16','09');
        for($i = 0; $i < count($subArray); $i++){
            $body .= '<div class="form-group"><label class="col-sm-4 control-label">'. substr($subArray[$i],4) .'</label><div class="col-sm-8"><input ';
            $body .= ' type="text" class="form-control" id="jyu'. $numArray[$i] .'" value="'. $wrk[0]["jyu$numArray[$i]"] .'"></div></div>';
        }
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
    
    // 新規(JS)
    function createbtn2ClickJs(){
        $this->clearJs();
        $objString = '{';
        for($i=1; $i <= $this->koumokusu; $i++){
            $a=sprintf('%02d',$i);
            $objString .= '"jyu'.$a.'":$("#jyu'.$a.'").val(),';
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
        $err = '';
        if(preg_match("/^[a-zA-Z0-9]+$/", $data['jyu01'])){
            $err = 'err';
        }
        if(preg_match("/^[a-zA-Z0-9]+$/", $data['jyu02'])){
            $err = 'err';
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
                $par[] = $data["jyu$a"];
            }
            array_push($par,$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
        }else{
            $wrk[0]['res'] = false;
        }
        echo json_encode($wrk);
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
            var obj = {"jyu01":$("#jyu01").val(),"jyu02":$("#jyu02").val()};
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
        //[21]アパート名,[22]電話番号,[23]FAX番号,[24]メールアドレス,[25]契約タイプ);
        $numArray = array('jyu07','jyu08','jyu25','jyu11','bum02','jyu12','jyu13','jyu14','jyu05','jyu06','jyu17','jyu18','jyu19','jyu20','jyu21','jyu22','jyu23','jyu24','jyu10','jyu15','jyu16','jyu09');
        for($i=0; $i < $this->koumokusu - 4; $i++){
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
                str += '<td><div><button name="button1" style="width:50px;" type="button" value="' + data[i]['jyu01'] + '-' + data[i]['jyu02'] + '">' + data[i]['jyu01'] + data[i]['jyu02'] + '</button></div></td>';
                str += '<td><div>' + data[i]['jyu03'] + ' ' +data[i]['jyu04'] + '</div></td>';
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
        $sql = "select * from jyugyoin left join bumon on jyu26 = bum01 and jyu11 = bum02 where $sqlwhere $orderby;"; // 所属部門CD,所属部門No
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
            $wrk[$i]['jyu09'] = $result['jyu09'];
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
    $params = array('title'=>'勤怠修正・確定');
    $p->src['header'] = headerReplace::createSrc($params);
    $p->src['standard'] = standardComponentsLoad::createSrc();

    // bootstrap ナビゲーション
    $params = array('autho'=>$_SESSION['autho'], 'active'=>'master', 'name'=>$_SESSION['name']);
    $p->src['nav'] = bootstrapNavigationReplace::createSrc($params);

    $p->show();
}
?>