<?php
// ** 売上計画 ** //

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');
require_once('incl/password.php');

class page extends core {
    // JS初期処理
    function initJs(){
        $this->clearJs();
        $year = date('Y');
        $this->js1 = <<<"__"
        // テンプレサンプルデータ削除
        $("#tablebody tr").remove();
        $("#tablebody td").remove();

       // 検索条件初期値設定
        var json_data_stock_obj = {"jyouken1":"syu","maekakko1":"","koumoku1":"year","val1":"$year","enzan1":"=","atokakko1":"","jyouken2":"","maekakko2":"","koumoku2":"","val2":"","enzan2":"","atokakko2":"","jyouken3":"","maekakko3":"","koumoku3":"","val3":"","enzan3":"","atokakko3":"","jyouken4":"","maekakko4":"","koumoku4":"","val4":"","enzan4":"","atokakko4":"","order1":"bum03","orderopt1":"ASC","order2":"","orderopt2":"","order3":"","orderopt3":"","order4":"","orderopt4":""};
        $('#json_data_stock').val(JSON.stringify(json_data_stock_obj));
       $('#searchbtn2').trigger('click');
       
       // ヒントの表示
       var hintObj = {
       "":"　",
       "bum01":"適用開始：マスターの適用開始日。",
       "bum02":"適用終了：マスターの適用終了日。空が最新のマスター。",
       "bum03":"部門番号",
       "bum04":"部門名（略称。20文字以内。）",
       "bum05":"部門名カナ（略称。20文字以内。）",
       "bum06":"部門名（正式名称）",
       "bum07":"部門名カナ（正式名称）",
       "bum08":"業務の内容",
       "bum09":"データの識別CD（unique）",
       "bum10":"",
       "bum11":"勤怠パターン",
       "bum12":"残業計算パターン",
       "bum13":"カレンダー",
       "bum14":"週の起点とする曜日。法休の決定、残業計算に用いる。",
       "bum15":"何分単位で勤怠管理するか",
       "bum16":"有休利用時の労働時間数",
       "bum17":"請求単価",
       "bum18":"通常休憩時間",
       "bum19":"深夜休憩時間",
       "bum20":"締め日",
       "bum21":"管轄拠点",
       "bum22":"契約区分",
       "bum23":"大分類",
       "bum24":"中分類",
       "bum25":"小分類",
       "bum26":"取引先",
       "bum27":"",
       "bum28":"",
       "bum29":"",
       "bum30":"",
       "year":"表示年",
       "group":"集計単位　b：部門 s：収支 k：拠点 "
       
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
            var selectedVal = $('#koumoku4').val();
            $('#hint4').text(hintObj[selectedVal]);
	   });
       
       $(document).on('click','#ukeibtn',function(){
            var txt = $('#ukei00ud').val().replace(/\\r\\n|\\r/g, "\\n");
            var lines = txt.split('\\n');
//            console.log(lines);
            $('#ukei01ud').val(lines[0]);
            $('#ukei02ud').val(lines[1]);
            $('#ukei03ud').val(lines[2]);
            $('#ukei04ud').val(lines[3]);
            $('#ukei05ud').val(lines[4]);
            $('#ukei06ud').val(lines[5]);
            $('#ukei07ud').val(lines[6]);
            $('#ukei08ud').val(lines[7]);
            $('#ukei09ud').val(lines[8]);
            $('#ukei10ud').val(lines[9]);
            $('#ukei11ud').val(lines[10]);
            $('#ukei12ud').val(lines[11]);
	   });
__;
        $this->addEventListener('','','','');
    }
    
    // 更新・削除モーダル表示(JS)
    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"cd":event.value,"year":event.getAttribute('year')};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_ud').remove();
            $('#modalParent1').append(data[0]["html"]);
            $('#modal_ud').modal({backdrop:'static'});
            //console.log(data);
__;
        $this->addEventListener('','wait','modalCallUd','ajax');
    }
    // 更新・削除モーダル表示(PHP)
    function modalCallUd($data){
        $ymst = $data['year'].'01';
        $ymen = $data['year'].'12';
        $modal = $this->readModalSource('modal_ud');
        $sql = 'select * from ukeikaku where ukei03>=? and ukei03<=? and ukei02=? and ukei01="bum";';
        $par = array($ymst,$ymen,$data['cd']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        // 初期処理
        $modal['ukei01ud'] = '';
        $modal['ukei02ud'] = '';
        $modal['ukei03ud'] = '';
        $modal['ukei04ud'] = '';
        $modal['ukei05ud'] = '';
        $modal['ukei06ud'] = '';
        $modal['ukei07ud'] = '';
        $modal['ukei08ud'] = '';
        $modal['ukei09ud'] = '';
        $modal['ukei10ud'] = '';
        $modal['ukei11ud'] = '';
        $modal['ukei12ud'] = '';
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $month = substr($result["ukei03"],4,2);
            if($month == '01'){
                $modal['ukei01ud'] = $result["ukei04"];
            }elseif($month == '02'){
                $modal['ukei02ud'] = $result["ukei04"];
            }elseif($month == '03'){
                $modal['ukei03ud'] = $result["ukei04"];
            }elseif($month == '04'){
                $modal['ukei04ud'] = $result["ukei04"];
            }elseif($month == '05'){
                $modal['ukei05ud'] = $result["ukei04"];
            }elseif($month == '06'){
                $modal['ukei06ud'] = $result["ukei04"];
            }elseif($month == '07'){
                $modal['ukei07ud'] = $result["ukei04"];
            }elseif($month == '08'){
                $modal['ukei08ud'] = $result["ukei04"];
            }elseif($month == '09'){
                $modal['ukei09ud'] = $result["ukei04"];
            }elseif($month == '10'){
                $modal['ukei10ud'] = $result["ukei04"];
            }elseif($month == '11'){
                $modal['ukei11ud'] = $result["ukei04"];
            }elseif($month == '12'){
                $modal['ukei12ud'] = $result["ukei04"];
            }
        }
        $modal['cd_ud'] = $data['cd']; // 部門CD
        $modal['year_ud'] = $data['year']; // 対象年
        if($month == ''){
            $modal['ukei00ud'] = '';
        }else{
            $modal['ukei00ud'] = $modal['ukei01ud']."\r\n".$modal['ukei01ud']."\r\n".$modal['ukei02ud']."\r\n".$modal['ukei03ud']."\r\n".$modal['ukei04ud']."\r\n".$modal['ukei05ud']."\r\n".$modal['ukei06ud']."\r\n".$modal['ukei07ud']."\r\n".$modal['ukei08ud']."\r\n".$modal['ukei09ud']."\r\n".$modal['ukei10ud']."\r\n".$modal['ukei11ud']."\r\n".$modal['ukei12ud'];
        }
        $res[0]['html'] = implode("", $modal);
        echo json_encode($res);
        exit();
    }

    // 更新+新規(JS)
    function upbtn1ClickJs(){
        $this->js1 = <<<"__"
            var obj = {
            'ukei01':$('#ukei01ud').val(),'ukei02':$('#ukei02ud').val(),
            'ukei03':$('#ukei03ud').val(),'ukei04':$('#ukei04ud').val(),
            'ukei05':$('#ukei05ud').val(),'ukei06':$('#ukei06ud').val(),
            'ukei07':$('#ukei07ud').val(),'ukei08':$('#ukei08ud').val(),
            'ukei09':$('#ukei09ud').val(),'ukei10':$('#ukei10ud').val(),
            'ukei11':$('#ukei11ud').val(),'ukei12':$('#ukei12ud').val(),
            'cd_ud':$('#cd_ud').val(),'year_ud':$('#year_ud').val(),
            'lab01':$('#lab01ud').text(),'lab02':$('#lab02ud').text(),
            'lab03':$('#lab03ud').text(),'lab04':$('#lab04ud').text(),
            'lab05':$('#lab05ud').text(),'lab06':$('#lab06ud').text(),
            'lab07':$('#lab07ud').text(),'lab08':$('#lab08ud').text(),
            'lab09':$('#lab09ud').text(),'lab10':$('#lab10ud').text(),
            'lab11':$('#lab11ud').text(),'lab12':$('#lab12ud').text(),
            'lab_cd':$('#lab_cd_ud').text(),'lab_year':$('#lab_year_ud').text()
            };
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#modal_ud').modal('hide');
                $('#searchbtn2').trigger('click');
            }else{
                $('[name=button2]').prop("disabled",false);
                $('#modal_n').remove();
                $('#modalParent3').append(data[0]["html"]);
                $('#modal_n').modal({backdrop:'static'});
            }
__;
        $this->js3 =  <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#upbtn1','click','updateData','ajax');
    }
    // 更新+新規(PHP)
    function updateData($data){
        $this->err = '';

        // エラーチェック
        if(!$this->err){$this->validate($data['cd_ud']   ,'str', array(1,20,true), $data['lab_cd']);} // cd
        if(!$this->err){$this->validate($data['year_ud'] ,'int', array(4,4,true), $data['lab_year']);} // 対象年
        if(!$this->err){$this->validate($data['ukei01'] ,'int',  array(0,9,true), $data['lab01']);} // 1月
        if(!$this->err){$this->validate($data['ukei02'] ,'int',  array(0,9,true), $data['lab02']);} // 2月
        if(!$this->err){$this->validate($data['ukei03'] ,'int',  array(0,9,true), $data['lab03']);} // 3月
        if(!$this->err){$this->validate($data['ukei04'] ,'int',  array(0,9,true), $data['lab04']);} // 4月
        if(!$this->err){$this->validate($data['ukei05'] ,'int',  array(0,9,true), $data['lab05']);} // 5月
        if(!$this->err){$this->validate($data['ukei06'] ,'int',  array(0,9,true), $data['lab06']);} // 6月
        if(!$this->err){$this->validate($data['ukei07'] ,'int',  array(0,9,true), $data['lab07']);} // 7月
        if(!$this->err){$this->validate($data['ukei08'] ,'int',  array(0,9,true), $data['lab08']);} // 8月
        if(!$this->err){$this->validate($data['ukei09'] ,'int',  array(0,9,false), $data['lab09']);} // 9月
        if(!$this->err){$this->validate($data['ukei10'] ,'int',  array(0,9,false), $data['lab10']);} // 10月
        if(!$this->err){$this->validate($data['ukei11'] ,'int',  array(0,9,true),  $data['lab11']);} // 11月
        if(!$this->err){$this->validate($data['ukei12'] ,'int',  array(0,9,true),  $data['lab12']);} // 12月
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        // 年月生成
        for($i=1; $i <= 12; $i++){
            $a = sprintf('%02d',$i);
            $ym[$i] = $data['year_ud'].$a;
        }
        // 金額配列生成
        for($i=1; $i <= 12; $i++){
            $a = sprintf('%02d',$i);
            $kin[$i] = $data['ukei'.$a];
        }
        $today = date('Ymd');
        $now = date('Hi');
        $par = array();
        $sql = "insert into ukeikaku (ukei01,ukei02,ukei03,ukei04,ukei05,ukei06,ukei07,ukei08,ukei09,ukei10,cday,ctim,cid,uday,utim,uid) values ";
        for($i=1; $i <= 12; $i++){
            $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
        }
        $sql = substr($sql,0,-1);
        $sql .= 'ON DUPLICATE KEY UPDATE ukei04 = VALUES(ukei04),uday = VALUES(uday),utim = VALUES(utim),uid = VALUES(uid)';
        for($i=1; $i <= 12; $i++){
            array_push($par,'bum',$data['cd_ud'],$ym[$i],$kin[$i],'','','','','','',$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
        }
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }
    
    // 削除準備(JS)
    function delbtn1ClickJs(){
        $this->clearJs();
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
			$('#modal_d').remove();
			$('#modalParent2').append(data[0]['html']);
			$('#modal_d').modal({backdrop:'static'});
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#delbtn1','click','delmodal','ajax');
    }
    // 
    function delmodal($data){
		$modal = $this->readModalSource('modal_d');
        $res[0]['html'] = $modal;
        echo json_encode($res);
        exit();
    }

    // 削除(JS)
    function delbtn2ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"cd":$("#cd_ud").val(),"year":$("#year_ud").val()};
            params = JSON.stringify(obj);
            //console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            $('#searchbtn2').trigger('click');
			$('#modal_d').modal('hide');
            $('#modal_ud').modal('hide');
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#delbtn2','click','deleteData','ajax');
    }
    // 削除(PHP)
    function deleteData($data){
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "delete from ukeikaku where ukei02=? and ukei03>=? and ukei03<=?;";
        $par = array($data['cd'],$data['year'].'01',$data['year'].'12');
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
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
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
			$('#modal_search').remove();
			$('#modalParent1').append(data[0]['html']);
			$('#modal_search').modal({backdrop:'static'});

            // 前回選択値の適用
            var zdata = JSON.parse($('#json_data_stock').val()||"null");
            if(zdata){
                $zenkai
            }
__;
        $this->addEventListener('#searchbtn1','click','searchModalCall','ajax');
    }
    // 検索用モーダル表示(PHP)
    function searchModalCall($data){
        $modal = $this->readModalSource('modal_search'); // $modal[] = modalソース配列
        
        // コンボボックス処理
        $wrk = array(''=>'パターンから選択');
        $modal['combo0'] = '<OPTION value="">未選択</OPTION>';
        $sql = "select * from kensakupt where kns01=? and kns05='ukeikaku' order by kns02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns02 = $result['kns02'];
            $kns03 = $result['kns03'];
            $modal['combo0'] .= "<OPTION value='$kns02'>$kns03</OPTION>";
        }
        $wrk = array(''=>'パターンから選択');
        $modal['combo0k'] = '<OPTION value="">未選択</OPTION>';
        $sql = "select * from kensakupt where kns01=? and kns05='ukeikaku' order by kns02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array('all');
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns02 = $result['kns02'];
            $kns03 = $result['kns03'];
            $modal['combo0k'] .= "<OPTION value='$kns02'>$kns03</OPTION>";
        }

        $wrk = array(''=>'未選択','bum01'=>'適用開始日','bum02'=>'適用終了日','bum03'=>'部門No.','bum04'=>'部門名','bum05'=>'部門名カナ','bum06'=>'部門名（正式）','bum07'=>'部門名カナ（正式）','bum08'=>'業務内容','bum09'=>'','bum10'=>'','bum11'=>'勤怠パターン','bum12'=>'残業計算タイプ','bum13'=>'カレンダーCD','bum14'=>'週の起点','bum15'=>'時間集計単位','bum16'=>'有休時間数','bum17'=>'請求単価','bum18'=>'','bum19'=>'','bum20'=>'','year'=>'表示年','group'=>'集計単位');

        foreach($wrk as $key => $val){
            $combo1 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo1_1'] = $modal['combo1_2'] = $modal['combo1_3'] = $modal['combo1_4'] = $modal['combo1_n1'] = $modal['combo1_n2'] = $modal['combo1_n3'] = $modal['combo1_n4'] = $combo1;
        $wrk = array(''=>'未選択','='=>'に等しい','!='=>'に等しくない','>='=>'以上','<='=>'以下','not in'=>'に含まれない','in'=>'に含まれる','between'=>'の間(以上/以下)','not between'=>'の間にない','like'=>'の文字を含む');
        foreach($wrk as $key => $val){
            $combo2 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo2_1'] = $modal['combo2_2'] = $modal['combo2_3'] = $modal['combo2_4'] = $combo2;
        //$modal['combo2_1'] = "<OPTION value=''>から</OPTION>";
        
        $wrk = array(''=>'','and'=>'かつ','or'=>'又は');
        foreach($wrk as $key => $val){
            $combo3 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo3_2'] = $modal['combo3_3'] = $modal['combo3_4'] = $combo3;
        $modal['combo3_1'] = "<OPTION value='bum'>部門単位</OPTION><OPTION value='kai'>会社単位</OPTION><OPTION value='syu'>収支単位</OPTION>";

        $wrk = array(''=>'','('=>'(');
        foreach($wrk as $key => $val){
            $combo4 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo4_1'] = $modal['combo4_2'] = $modal['combo4_3'] = $modal['combo4_4'] = $combo4;
        
        $wrk = array(''=>'',')'=>')');
        foreach($wrk as $key => $val){
            $combo5 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo5_1'] = $modal['combo5_2'] = $modal['combo5_3'] = $modal['combo5_4'] = $combo5;

        $wrk = array(''=>'','ASC'=>'昇順','DESC'=>'降順');
        foreach($wrk as $key => $val){
            $combo6 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo6_n1'] = $modal['combo6_n2'] = $modal['combo6_n3'] = $modal['combo6_n4'] = $combo6;
        
        $res[0]['html'] = implode($modal);
        echo json_encode($res);
        exit();
    }   
    
    // 検索(JS)
    function searchbtn2ClickJs(){
        $this->clearJs();

        // 検索PT記録用obj作成
        $this->js1 = " var obj = {";
            // 条件１は式固定
            $this->js1 .= "'jyouken1':$('#jyouken1').val(),";
            $this->js1 .= "'maekakko1':'',";
            $this->js1 .= "'koumoku1':'year',";
            $this->js1 .= "'val1':$('#val1').val(),";
            $this->js1 .= "'enzan1':'=',";
            $this->js1 .= "'atokakko1':'',";
        for($i=2; $i <= 4; $i++){
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
        $('#modalLoader1').modal({backdrop:'static'});
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            console.log(data);
            $("#tablebody tr").remove();
            $("#tablebody td").remove();
            $("#tablebody").append(data[0]['html']);
            FixedMidashi.remove();
            FixedMidashi.create();
__;
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->js4 = "$('#modal_search').modal('hide'); $('#modalLoader1').modal('hide');";

        $this->addEventListener('#searchbtn2','click','searchData','ajax');
    }
    // 検索データ取得(PHP)
    function searchData($data){
        // 条件式の組み立て
        for($i=2; $i <= 4; $i++){
            if($data["jyouken$i"] != ''){
/*                if($i != 2){
                    $sqlwhere .= ' ' . $data["jyouken$i"] . ' ';
                }
*/                if($data["enzan$i"] == 'in' or $data["enzan$i"] == 'not in'){
                    $val = explode(',',$data["val$i"]);
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' (';
                    foreach($val as $sval){
                        $sqlwhere .= "'$sval',";
                    }
                    $sqlwhere = substr($sqlwhere,0,-1);
                    $sqlwhere .= ')' . $data["atokakko$i"];
                }elseif ($data["enzan$i"] == 'between' or $data["enzan$i"] == 'not between'){
                    $val = explode(',',$data["val$i"]);
                    $sqlwhere .= $data["maekakko$i"] . 'cast(' . $data["koumoku$i"] . ' as SIGNED) ' . $data["enzan$i"] . ' ' . $val[0] . ' and ' . $val[1] . $data["atokakko$i"];
                }elseif ($data["enzan$i"] == 'like'){
                    $val = '%' . $data["val$i"] . '%';
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val'" . $data["atokakko$i"];
                }elseif ($data["enzan$i"] == '>=' or $data["enzan$i"] == '<='){
                    $val = $data["val$i"];
                    $sqlwhere .= $data["maekakko$i"] . 'cast(' . $data["koumoku$i"] . ' as SIGNED) ' . $data["enzan$i"] . ' ' . $val . $data["atokakko$i"];
                }else{
                    $val = $data["val$i"];
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val'" . $data["atokakko$i"];
                }
            }
        }
        $orderby = '';
        for($i=1; $i <= 4; $i++){
            if($data["order$i"]){
                $orderby .= $data["order$i"] . '+0 ' . $data["orderopt$i"] . ',';
            }
        }
        if($orderby != ''){
            $orderby = 'order by ' . substr($orderby,0,-1); // 余分カンマ削除
        }
        $year = $data['val1'];
        $ymdst = $data['val1'].'0101';
        $ymden = $data['val1'].'0102';
        $ymst = $data['val1'].'01';
        $ymen = $data['val1'].'12';
        $busql = "bum01<='$ymdst' and bum02>='$ymden'";
        // 会社
        if($data["jyouken1"] == 'kai'){
            // 会社ごとに集計したレコードを取得
            $flg = 0; // 初回ループ
            $sql = "select * from bumon left join kaisya on kais24=bum27 left join ukeikaku on ukei01='bum' and ukei02=bum03 and ukei03>=$ymst and ukei03<=$ymen where {$busql} order by cast(kais24 as SIGNED) DESC,cast(bum03 as SIGNED) ASC,ukei03 ASC;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $bucd = $result['bum03']; // 部門CD
                $ym = $result['ukei03'];
                $month = substr($ym,4,2);

                // ループ初期処理
                if($flg == 0){
                    $key = $result['kais24'];
                    $kaisya = $result['kais04'];
                    $flg = 1;
                }
                if($key == $result['kais24']){
                    // 金額加算(同一会社)
                    $ary[$month] += $result['ukei04'];
                }else{
                    // 集計用レコード出力
                    $wrk[$key][1] = ''; // 選択ボタン
                    $wrk[$key][2] = $kaisya; // 名
                    $wrk[$key][11] = $ary['01'];
                    $wrk[$key][12] = $ary['02'];
                    $wrk[$key][13] = $ary['03'];
                    $wrk[$key][14] = $ary['04'];
                    $wrk[$key][15] = $ary['05'];
                    $wrk[$key][16] = $ary['06'];
                    $wrk[$key][17] = $ary['07'];
                    $wrk[$key][18] = $ary['08'];
                    $wrk[$key][19] = $ary['09'];
                    $wrk[$key][20] = $ary['10'];
                    $wrk[$key][21] = $ary['11'];
                    $wrk[$key][22] = $ary['12'];
                    $wrk[$key]['row-color'] = '#d8d3ff';
                    $ary = array();
                    // key更新
                    $key = $result['kais24'];
                    $kaisya = $result['kais04'];
                }
                $wrk[$bucd][1] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$result['bum03'].'" year="'.$year.'">'.$result['bum03'].'</button>'; // 選択ボタン
                $wrk[$bucd][2] = $result['bum04']; // 名
                                
                // 金額
                if($month == '01'){
                    $wrk[$bucd][11] = $result['ukei04'];
                }elseif($month == '02'){
                    $wrk[$bucd][12] = $result['ukei04'];
                }elseif($month == '03'){
                    $wrk[$bucd][13] = $result['ukei04'];
                }elseif($month == '04'){
                    $wrk[$bucd][14] = $result['ukei04'];
                }elseif($month == '05'){
                    $wrk[$bucd][15] = $result['ukei04'];
                }elseif($month == '06'){
                    $wrk[$bucd][16] = $result['ukei04'];
                }elseif($month == '07'){
                    $wrk[$bucd][17] = $result['ukei04'];
                }elseif($month == '08'){
                    $wrk[$bucd][18] = $result['ukei04'];
                }elseif($month == '09'){
                    $wrk[$bucd][19] = $result['ukei04'];
                }elseif($month == '10'){
                    $wrk[$bucd][20] = $result['ukei04'];
                }elseif($month == '11'){
                    $wrk[$bucd][21] = $result['ukei04'];
                }elseif($month == '12'){
                    $wrk[$bucd][22] = $result['ukei04'];
                }else{ // 売上計画データなし
                    $wrk[$bucd][11] = '';
                    $wrk[$bucd][12] = '';
                    $wrk[$bucd][13] = '';
                    $wrk[$bucd][14] = '';
                    $wrk[$bucd][15] = '';
                    $wrk[$bucd][16] = '';
                    $wrk[$bucd][17] = '';
                    $wrk[$bucd][18] = '';
                    $wrk[$bucd][19] = '';
                    $wrk[$bucd][20] = '';
                    $wrk[$bucd][21] = '';
                    $wrk[$bucd][22] = '';
                }

            }
/*            // 会社別レコードが存在する場合はそちらを優先
            $sql = "select * from ukeikaku where ukei01='kai' and ukei03>=$ymst and ukei03<=$ymen order by ukei02,ukei03;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $kaicd = $result['ukei02'];
                $ym = $result['ukei03'];
                $month = substr($ym,4,2);
                // 金額
                if($month == '01'){$wrk[$kaicd][11] = $result['ukei04'];}
                elseif($month == '02'){$wrk[$kaicd][12] = $result['ukei04'];}
                elseif($month == '03'){$wrk[$kaicd][13] = $result['ukei04'];}
                elseif($month == '04'){$wrk[$kaicd][14] = $result['ukei04'];}
                elseif($month == '05'){$wrk[$kaicd][15] = $result['ukei04'];}
                elseif($month == '06'){$wrk[$kaicd][16] = $result['ukei04'];}
                elseif($month == '07'){$wrk[$kaicd][17] = $result['ukei04'];}
                elseif($month == '08'){$wrk[$kaicd][18] = $result['ukei04'];}
                elseif($month == '09'){$wrk[$kaicd][19] = $result['ukei04'];}
                elseif($month == '10'){$wrk[$kaicd][20] = $result['ukei04'];}
                elseif($month == '11'){$wrk[$kaicd][21] = $result['ukei04'];}
                elseif($month == '12'){$wrk[$kaicd][22] = $result['ukei04'];}
                if($flg[$kaicd] != 1){
                    $wrk[$kaicd][30] = '会社'; // 部門金額の積上か会社の金額データか
                    $flg[$kaicd] = 1;
                }
            }
            */
            
        }elseif($data["jyouken1"] == 'syu'){
            // 部門レコードを取得
            $L02ary = array();
            $L03ary = array();
            $L04ary = array();
            $L10ary = array();
            $sql = "select * from sgroup left join bumon on sgrp02=bum23 and {$busql} left join ukeikaku on ukei01='bum' and ukei02=bum03 and ukei03>=$ymst and ukei03<=$ymen where sgrp01='収支' order by sgrp06,cast(bum03 as SIGNED),ukei03;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                if($result['sgrp05'] > 1){ // 集計レコード
                    $bucd = $result['sgrp02']; // 部門CDに収支CDを設定
                }else{
                    $bucd = $result['bum03'];
                }
                $ym = $result['ukei03'];
                $month = substr($ym,4,2);
//                $wrk[$kaicd]['name'] = $result['kais04']; 
//                $wrk[$kaicd][$ym] = $result['kin']; // array[会社][年月]=金額
                // テーブル組み立て
//                $wrk[$kaicd][1] = $result['kais03']; // 会社CD
                $wrk[$bucd][1] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$result['bum03'].'" year="'.$year.'">'.$result['bum03'].'</button>'; // 選択ボタン
                $wrk[$bucd][2] = $result['bum04']; // 名
                // 金額
                if($month == '01'){
                    $wrk[$bucd][11] = $result['ukei04'];
                }elseif($month == '02'){
                    $wrk[$bucd][12] = $result['ukei04'];
                }elseif($month == '03'){
                    $wrk[$bucd][13] = $result['ukei04'];
                }elseif($month == '04'){
                    $wrk[$bucd][14] = $result['ukei04'];
                }elseif($month == '05'){
                    $wrk[$bucd][15] = $result['ukei04'];
                }elseif($month == '06'){
                    $wrk[$bucd][16] = $result['ukei04'];
                }elseif($month == '07'){
                    $wrk[$bucd][17] = $result['ukei04'];
                }elseif($month == '08'){
                    $wrk[$bucd][18] = $result['ukei04'];
                }elseif($month == '09'){
                    $wrk[$bucd][19] = $result['ukei04'];
                }elseif($month == '10'){
                    $wrk[$bucd][20] = $result['ukei04'];
                }elseif($month == '11'){
                    $wrk[$bucd][21] = $result['ukei04'];
                }elseif($month == '12'){
                    $wrk[$bucd][22] = $result['ukei04'];
                }elseif($result['sgrp05'] > 1){ // 集計レコード
                    $bucd = $result['sgrp02']; // 部門CDに収支CDを設定
                    $wrk[$bucd][1] = ''; //
                    $wrk[$bucd][2] = $result['sgrp04']; //
                }else{ // 売上計画データなし
                    $wrk[$bucd][11] = '';
                    $wrk[$bucd][12] = '';
                    $wrk[$bucd][13] = '';
                    $wrk[$bucd][14] = '';
                    $wrk[$bucd][15] = '';
                    $wrk[$bucd][16] = '';
                    $wrk[$bucd][17] = '';
                    $wrk[$bucd][18] = '';
                    $wrk[$bucd][19] = '';
                    $wrk[$bucd][20] = '';
                    $wrk[$bucd][21] = '';
                    $wrk[$bucd][22] = '';
                }
                // 金額加算
                $L02ary[$month] += $result['ukei04'];
                $L03ary[$month] += $result['ukei04'];
                $L04ary[$month] += $result['ukei04'];
                $L10ary[$month] += $result['ukei04'];
                // 集計用レコード
                if($result['sgrp05'] == 2){
                    $wrk[$bucd][11] = $L02ary['01'];
                    $wrk[$bucd][12] = $L02ary['02'];
                    $wrk[$bucd][13] = $L02ary['03'];
                    $wrk[$bucd][14] = $L02ary['04'];
                    $wrk[$bucd][15] = $L02ary['05'];
                    $wrk[$bucd][16] = $L02ary['06'];
                    $wrk[$bucd][17] = $L02ary['07'];
                    $wrk[$bucd][18] = $L02ary['08'];
                    $wrk[$bucd][19] = $L02ary['09'];
                    $wrk[$bucd][20] = $L02ary['10'];
                    $wrk[$bucd][21] = $L02ary['11'];
                    $wrk[$bucd][22] = $L02ary['12'];
                    $wrk[$bucd]['row-color'] = '#d8d3ff';
                    $L02ary = array();
                }elseif($result['sgrp05'] == 3){
                    $wrk[$bucd][11] = $L03ary['01'];
                    $wrk[$bucd][12] = $L03ary['02'];
                    $wrk[$bucd][13] = $L03ary['03'];
                    $wrk[$bucd][14] = $L03ary['04'];
                    $wrk[$bucd][15] = $L03ary['05'];
                    $wrk[$bucd][16] = $L03ary['06'];
                    $wrk[$bucd][17] = $L03ary['07'];
                    $wrk[$bucd][18] = $L03ary['08'];
                    $wrk[$bucd][19] = $L03ary['09'];
                    $wrk[$bucd][20] = $L03ary['10'];
                    $wrk[$bucd][21] = $L03ary['11'];
                    $wrk[$bucd][22] = $L03ary['12'];
                    $wrk[$bucd]['row-color'] = '#9082ff';
                    $L03ary = array();
                }elseif($result['sgrp05'] == 4){
                    $wrk[$bucd][11] = $L04ary['01'];
                    $wrk[$bucd][12] = $L04ary['02'];
                    $wrk[$bucd][13] = $L04ary['03'];
                    $wrk[$bucd][14] = $L04ary['04'];
                    $wrk[$bucd][15] = $L04ary['05'];
                    $wrk[$bucd][16] = $L04ary['06'];
                    $wrk[$bucd][17] = $L04ary['07'];
                    $wrk[$bucd][18] = $L04ary['08'];
                    $wrk[$bucd][19] = $L04ary['09'];
                    $wrk[$bucd][20] = $L04ary['10'];
                    $wrk[$bucd][21] = $L04ary['11'];
                    $wrk[$bucd][22] = $L04ary['12'];
                    $wrk[$bucd]['row-color'] = '#6d5aff';
                    $L04ary = array();
                }elseif($result['sgrp05'] == 10){
                    $wrk[$bucd][11] = $L10ary['01'];
                    $wrk[$bucd][12] = $L10ary['02'];
                    $wrk[$bucd][13] = $L10ary['03'];
                    $wrk[$bucd][14] = $L10ary['04'];
                    $wrk[$bucd][15] = $L10ary['05'];
                    $wrk[$bucd][16] = $L10ary['06'];
                    $wrk[$bucd][17] = $L10ary['07'];
                    $wrk[$bucd][18] = $L10ary['08'];
                    $wrk[$bucd][19] = $L10ary['09'];
                    $wrk[$bucd][20] = $L10ary['10'];
                    $wrk[$bucd][21] = $L10ary['11'];
                    $wrk[$bucd][22] = $L10ary['12'];
                    $wrk[$bucd]['row-color'] = '#3a22ff';
                    $wrk[$bucd]['text-color'] = '#ffffff';
                    $L10ary = array();
                }
            }
            // 収支別レコードが存在する場合はそちらを優先
/*            $sql = "select * from ukeikaku where ukei01='kai' and ukei03>=$ymst and ukei03<=$ymen order by ukei02,ukei03;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $bucd = $result['ukei02'];
                $ym = $result['ukei03'];
                $month = substr($ym,4,2);
                // 金額
                if($month == '01'){$wrk[$bucd][11] = $result['ukei04'];}
                elseif($month == '02'){$wrk[$bucd][12] = $result['ukei04'];}
                elseif($month == '03'){$wrk[$bucd][13] = $result['ukei04'];}
                elseif($month == '04'){$wrk[$bucd][14] = $result['ukei04'];}
                elseif($month == '05'){$wrk[$bucd][15] = $result['ukei04'];}
                elseif($month == '06'){$wrk[$bucd][16] = $result['ukei04'];}
                elseif($month == '07'){$wrk[$bucd][17] = $result['ukei04'];}
                elseif($month == '08'){$wrk[$bucd][18] = $result['ukei04'];}
                elseif($month == '09'){$wrk[$bucd][19] = $result['ukei04'];}
                elseif($month == '10'){$wrk[$bucd][20] = $result['ukei04'];}
                elseif($month == '11'){$wrk[$bucd][21] = $result['ukei04'];}
                elseif($month == '12'){$wrk[$bucd][22] = $result['ukei04'];}
                if($flg[$kaicd] != 1){
                    $wrk[$kaicd][30] = '会社'; // 部門金額の積上か会社の金額データか
                    $flg[$kaicd] = 1;
                }
            }
            */
            
        }else{
            $sql = "select * from bumon left join ukeikaku on ukei01='bum' and ukei02=bum03 where {$busql};";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $bucd = $result['bum03'];
                $ym = $result['ukei03'];
                $month = substr($ym,4,2);
                // テーブル組み立て
//                $wrk[$bucd][1] = $result['bum03']; // 会社CD
                $wrk[$bucd][1] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$result['bum03'].'" year="'.$year.'">'.$result['bum03'].'</button>'; // 選択ボタン
                $wrk[$bucd][2] = $result['bum04']; // 会社名
                // 金額
                if($month == '01'){$wrk[$bucd][11] = $result['ukei04'];}
                elseif($month == '02'){$wrk[$bucd][12] = $result['ukei04'];}
                elseif($month == '03'){$wrk[$bucd][13] = $result['ukei04'];}
                elseif($month == '04'){$wrk[$bucd][14] = $result['ukei04'];}
                elseif($month == '05'){$wrk[$bucd][15] = $result['ukei04'];}
                elseif($month == '06'){$wrk[$bucd][16] = $result['ukei04'];}
                elseif($month == '07'){$wrk[$bucd][17] = $result['ukei04'];}
                elseif($month == '08'){$wrk[$bucd][18] = $result['ukei04'];}
                elseif($month == '09'){$wrk[$bucd][19] = $result['ukei04'];}
                elseif($month == '10'){$wrk[$bucd][20] = $result['ukei04'];}
                elseif($month == '11'){$wrk[$bucd][21] = $result['ukei04'];}
                elseif($month == '12'){$wrk[$bucd][22] = $result['ukei04'];}
                else{ // レコード無し
                    $wrk[$bucd][11] = '';
                    $wrk[$bucd][12] = '';
                    $wrk[$bucd][13] = '';
                    $wrk[$bucd][14] = '';
                    $wrk[$bucd][15] = '';
                    $wrk[$bucd][16] = '';
                    $wrk[$bucd][17] = '';
                    $wrk[$bucd][18] = '';
                    $wrk[$bucd][19] = '';
                    $wrk[$bucd][20] = '';
                    $wrk[$bucd][21] = '';
                    $wrk[$bucd][22] = '';
                }
            }
        }
        // キー順でソート
        foreach($wrk as &$ary){
            ksort($ary);
        }
        unset($ary);
/*        $i = 0;

        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrk[$i]['bum03'] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$result['bum03'].'" uid="'.$result['bum09'].'">'.$result['bum03'].'</button>'; // 選択ボタン
            $wrk[$i]['bum0102'] = $this->format($result['bum01'],'date-').'～'.$this->format($result['bum02'],'date-'); // 適用期間
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
            $wrk[$i]['bum19'] = $result['bum19'];
            $wrk[$i]['bum20'] = $result['bum20'];
            $wrk[$i]['cday'] = $this->format($result['cday'],'date-');
            $wrk[$i]['ctim'] = $this->format($result['ctim'],'time:');
            $wrk[$i]['cid']  = $result['cid'];
            $wrk[$i]['uday'] = $this->format($result['uday'],'date-');
            $wrk[$i]['utim'] = $this->format($result['utim'],'time:');
            $wrk[$i]['uid']  = $result['uid'];
            $wrk[$i] = str_replace(null,'',$wrk[$i]); // null値除去
            $i++;
        }
*/
    $res[0]['html'] = $this->createTableSource($wrk);
        $res[0]['sql'] = $sql;
        echo json_encode($res);
        exit();
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
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            if(data[0]['res']){
                $('#ptnm').val('');
            }
            $('#modal_n').remove();
			$('#modalParent3').append(data[0]['html']);
			$('#modal_n').modal({backdrop:'static'});
            patternListUpdate();
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#patcrtbtn1','click','createPattern','ajax');
    }
    // 新規パターン保存(PHP)
    function createPattern($data){
        $err = '';
        if($data['ptnm']) {
            $today = date('Ymd');
            $now = date('Hi');
            $wrkcd = '';
            $par = array($_SESSION['id']);
            $sql = "select max(kns02)+1 as ptcd from kensakupt where kns01=? and kns05='ukeikaku' group by kns01;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $wrkcd = $result['ptcd'];
            }
            if($wrkcd == ''){$wrkcd = 1;}
            $par = array();
            $sql = "insert into kensakupt (";
            for($i=1; $i <= 5; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "kns".$a.",";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for($i=1; $i <= 5; $i++){
                $sql .= "?,";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",?,?,?,?,?,?);";
            array_push($par,$_SESSION['id'],$wrkcd,$data["ptnm"],$data["ptdef"],'ukeikaku',$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
			$modal = $this->readModalSource('modal_n');
            if($wrk[0]['res']){
				$modal['body'] = '<div>正常に保存されました</div>';
            }else{
				$modal['body'] = '<div>エラーが発生しました</div>';
            }
        }else{
            $wrk[0]['res'] = false;
			$modal['body'] = '<div>パターン名を入力して下さい</div>';
        }
        $wrk[0]['html'] = implode($modal);
        
        echo json_encode($wrk);
        exit();
    }

	// パターン取得・更新
    function patternListUpdateJs(){
        $this->clearJs();
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#kpcombo option').remove();
            $('#kpcombo').append(data[0]["html"]);
__;
        $this->addEventListener('','wait','patternListUpdate','ajax');
    }
    // 
    function patternListUpdate($data){
        $wrk[0]['html'] = "<OPTION value=''>未選択</OPTION>";
        $sql = "select * from kensakupt where kns01=? and kns05='ukeikaku' order by kns02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns02 = $result['kns02'];
            $kns03 = $result['kns03'];
            $wrk[0]['html'] .= "<OPTION value='$kns02'>$kns03</OPTION>";
        }
        echo json_encode($wrk);
        exit();
    }
    
    // パターン(カスタム)変更
    function kpcomboSelectJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"kns02":$('#kpcombo').val(),kubun:'cus'};
            params = JSON.stringify(obj);
__;
        for($i=1; $i < 5; $i++){
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
			//console.log(data);
			//console.log(json);
            $jsString
__;
        $this->addEventListener('#kpcombo','change','patternRead','ajax');
    }
    // パターン(共通)変更
    function kpcomboKySelectJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"kns02":$('#kpcombok').val(),kubun:'ky'};
            params = JSON.stringify(obj);
__;
        for($i=1; $i < 5; $i++){
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
			//console.log(data);
			//console.log(json);
            $jsString
__;
        $this->addEventListener('#kpcombok','change','patternRead','ajax');
    }
    //
    function patternRead($data){
        $sql = "select * from kensakupt where kns01=? and kns02=? and kns05='ukeikaku';";
        $stmt = $this->db->prepare($sql);
        if($data['kubun'] == 'cus'){ // カスタム
            $par = array($_SESSION['id'],$data['kns02']);
        }elseif($data['kubun'] == 'ky'){ // 共通
            $par = array('all',$data['kns02']);
        }
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns04 = $result['kns04'];
        }
        $wrk[0]['str'] = $kns04;
        echo json_encode($wrk);
        exit();
    }
    
    // パターン削除前(JS)
    function patdelbtn1ClickJs(){
        $this->clearJs();
 
		$this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
			$('#modal_pd').remove();
			$('#modalParent2').append(data[0]['html']);
			$('#modal_pd').modal({backdrop:'static'});            
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#patdelbtn1','click','patdelmodal','ajax');
    }
    // 
    function patdelmodal($data){
		$modal = $this->readModalSource('modal_pd');
        $res[0]['html'] = $modal;
        echo json_encode($res);
        exit();
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
            $('#modal_pd').modal('hide');
            patternListUpdate();
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#patdelbtn2','click','patdeleteData','ajax');
    }
    // パターン削除(PHP)
    function patdeleteData($data){
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "delete from kensakupt where (kns01=? and kns02=?) and kns05='ukeikaku';";
        $par = array($_SESSION['id'],$data['kns02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }

}


// ** page info ** //

$p = new page();

$data['pr1'] = array('title' => '売上計画'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
$data['pr3'] = array('active' => '売上・請求'); // ナビメニュー

loadResource($p,$data);