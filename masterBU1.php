<?php
// ** 部門マスター ** //

// <!-- delimiter --><!-- @名前@ --><!-- delimiter -->はtmp_xxx.htmlと置き換える。
// standardにはmodalやhiddenのコンポーネントが入っている
// <!-- delimiter modal_xx -->はモーダルテンプレート。readModalSource()で読み込み。
// ||@名前@||が書き換え可能で、書き換えは$x['名前']=内容 とする。||@名前@||は "modal内で"ユニーク

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');

// ★ POST GET

class page extends core {
    private $koumokusu = 30; // 表示するfield数(従業員マスターfield数 + joinテーブルから選択したfield数)
    
    // JS初期処理
    function initJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
        // テンプレサンプルデータ削除
        $("#tablebody tr").remove();
        $("#tablebody td").remove();

       // 検索条件初期値設定
        var json_data_stock_obj = {"jyouken1":"","maekakko1":"","koumoku1":"bum03","val1":"0","enzan1":">=","atokakko1":"","jyouken2":"","maekakko2":"","koumoku2":"","val2":"","enzan2":"","atokakko2":"","jyouken3":"","maekakko3":"","koumoku3":"","val3":"","enzan3":"","atokakko3":"","jyouken4":"","maekakko4":"","koumoku4":"","val4":"","enzan4":"","atokakko4":"","order1":"bum03","orderopt1":"ASC","order2":"","orderopt2":"","order3":"","orderopt3":"","order4":"","orderopt4":""};
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
       "bum30":""
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
__;
        $this->addEventListener('','','','');
    }
    
    // 新規モーダル表示(JS)
    function createbtn1ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_c').remove();
            $('#modalParent1').append(data[0]["html"]);
            $('#modal_c').modal({backdrop:'static'});
            //console.log(data);
            $('.tsArea').TapSuggest({
                tsInputElement : '#bum26c',
                tsArrayList : data[2],
                tsRegExpAll : true
            });
            $('.tsArea2').TapSuggest({
                tsInputElement : '#bum27ud',
                tsArrayList : data[3],
                tsRegExpAll : true
            });
__;
        $this->addEventListener('#createbtn1','click','modalCallCr','ajax');
    }
    // 新規モーダル表示(PHP)
    function modalCallCr($data){
        $modal = $this->readModalSource('modal_c');
        $sql = 'select kais03,kais24,kais06,kais19,kais20,kais07,kais22,kais23 from kaisya;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kais03 = $result['kais03']; // 会社CD
            $kais24 = $result['kais24']; // uniqueID
            $kais06 = $result['kais06']; // 会社名（正式）
            $kais19 = $result['kais19']; // 部署
            $kais20 = $result['kais20']; // 担当者等
            $kais07 = $result['kais07']; // 会社名（正式）カナ
            $kais22 = $result['kais22']; // 部署カナ
            $kais23 = $result['kais23']; // 担当者カナ
            $disp = '[' . $kais24 . ']' . ' ' . $kais06 . ' ' . $kais19 . ' ' . $kais20;
            $ndisp = $kais07 . ' ' . $kais22 . ' ' . $kais23;
            $res[2][] = array($disp,$ndisp);
        }
        $sql = 'select sgrp01,sgrp02,sgrp03,sgrp04,sgrp05 from sgroup where sgrp05 = 1;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $freeg1 = "<option value=''></option>";
        $syozokug = "<option value=''></option>";
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            if($result['grp01'] == 'free'){
                $freeg1 .= "<option value='{$result['sgrp02']}'>{$result['sgrp04']}</option>";
            }else{
                $syozokug .= "<option value='{$result['sgrp02']}'>{$result['sgrp04']}</option>";
            }
        }
        $modal['syozokug'] = $syozokug;
        $modal['freeg_1'] = $freeg1;
        $res[0]['html'] = implode($modal);
        echo json_encode($res);
        exit();
    }

    // 更新・削除モーダル表示(JS)
    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"bum09":event.getAttribute('uid')};
            //console.log(obj);
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_ud').remove();
            $('#modalParent1').append(data[0]["html"]);
            $('#bum11ud').val(data[1][11]);
            $('#bum12ud').val(data[1][12]);
            $('#bum14ud').val(data[1][14]);
            $('#bum15ud').val(data[1][15]);
            $('#bum21ud').val(data[1][21]);
            $('#bum22ud').val(data[1][22]);
            $('#bum23ud').val(data[1][23]);
            $('#bum24ud').val(data[1][24]);
            $('#bum25ud').val(data[1][25]);
//            console.log(data[1][11]);
            $('#modal_ud').modal({backdrop:'static'});
            //console.log(data);
            $(document).on('focus','#bum26ud',function(){
                $('.tsArea').TapSuggest({
                    tsInputElement : '#bum26ud',
                    tsArrayList : data[2],
                    tsRegExpAll : true
                });
            });
            $(document).on('focus','#bum27ud',function(){
                $('.tsArea2').TapSuggest({
                    tsInputElement : '#bum27ud',
                    tsArrayList : data[3],
                    tsRegExpAll : true
                });
            });
__;
        $this->addEventListener('','wait','modalCallUd','ajax');
    }
    // 更新・削除モーダル表示(PHP)
    function modalCallUd($data){
        $modal = $this->readModalSource('modal_ud');
        $sql = 'select * from bumon where bum09=?;';
        $par = array($data['bum09']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $modal['bum01ud'] = $result["bum01"];
            $modal['bum02ud'] = $result["bum02"];
            $modal['bum03ud'] = $result['bum03'];
            $modal['bum04ud'] = $result['bum04'];
            $modal['bum05ud'] = $result['bum05'];
            $modal['bum06ud'] = $result['bum06'];
            $modal['bum07ud'] = $result['bum07'];
            $modal['bum08ud'] = $result['bum08'];
            $modal['bum09ud'] = $result['bum09'];
            $modal['bum10ud'] = $result['bum10'];
            $res[1][11] = $result['bum11'];
            $res[1][12] = $result['bum12'];
            $modal['bum13ud'] = $result['bum13'];
            $res[1][14] = $result['bum14'];
            $res[1][15] = $result['bum15'];
            $modal['bum16ud'] = $result['bum16'];
            $modal['bum17ud'] = $result['bum17'];
            $modal['bum18ud'] = $result['bum18'];
            $modal['bum19ud'] = $result['bum19'];
            $modal['bum20ud'] = $result['bum20'];
            $res[1][21] = $result['bum21'];
            $res[1][22] = $result['bum22'];
            $res[1][23] = $result['bum23'];
            $res[1][24] = $result['bum24'];
            $res[1][25] = $result['bum25'];
            $bum26 = $result['bum26'];
            $bum27 = $result['bum27'];
            $bum26ex = explode('-',$bum26); // bum26[0]:請求先CD , bum26[1]:1～5(請求先1～5)
            $bum27ex = explode('-',$bum27); // bum27[0]:請求先CD , bum27[1]:90(会社)
            $modal['bum28ud'] = $result['bum28'];
            $modal['bum29ud'] = $result['bum29'];
            $modal['bum30ud'] = $result['bum30'];
            
        }
        // 請求先と会社
        $modal['bum27ud'] = '';
        $modal['bum26ud'] = '';
        $sql = 'select kais03,kais06,kais10 from kaisya where (kais03=? and kais10=?) or (kais03=? and kais10=?);';
        $par = array($bum26ex[0],$bum26ex[1],$bum27ex[0],$bum27ex[1]);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kais03 = $result['kais03']; // 会社CD
            $kais06 = $result['kais06']; // 会社名（正式）
            $kais10 = $result['kais10']; // 種別(1:請求先 90:会社情報)
            if($kais10 == 90){
                $modal['bum27ud'] = '['.$bum27.']'.$kais06;
            }else{
                $modal['bum26ud'] = '['.$bum26.']'.$kais06;
            }
        }
        $sql = 'select kais03,kais24,kais06,kais19,kais20,kais07,kais22,kais23,kais10 from kaisya;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kais03 = $result['kais03']; // 会社CD
            $kais24 = $result['kais24']; // uniqueID
            $kais06 = $result['kais06']; // 会社名（正式）
            $kais19 = $result['kais19']; // 部署
            $kais20 = $result['kais20']; // 担当者等
            $kais07 = $result['kais07']; // 会社名（正式）カナ
            $kais22 = $result['kais22']; // 部署カナ
            $kais23 = $result['kais23']; // 担当者カナ
            $kais10 = $result['kais10']; // 種別(1:請求先 90:会社情報)
            $disp = '[' . $kais24 . ']' . ' ' . $kais06 . ' ' . $kais19 . ' ' . $kais20;
            $ndisp = $kais07 . ' ' . $kais22 . ' ' . $kais23;
            if($kais10 == 1){
                $res[2][] = array($disp,$ndisp);
            }elseif($kais10 == 90){
                $res[3][] = array($disp,$ndisp);
            }
        }
        $sql = 'select sgrp01,sgrp02,sgrp03,sgrp04,sgrp05 from sgroup where sgrp05 = 1;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $freeg1 = "<option value=''></option>";
        $syozokug = "<option value=''></option>";
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            if($result['grp01'] == 'free'){
                $freeg1 .= "<option value='{$result['sgrp02']}'>{$result['sgrp04']}</option>";
            }else{
                $syozokug .= "<option value='{$result['sgrp02']}'>{$result['sgrp04']}</option>";
            }
        }
        $modal['syozokug'] = $syozokug;
        $modal['freeg_1'] = $freeg1;
        $res[0]['html'] = implode("", $modal);
        echo json_encode($res);
        exit();
    }

 
    // 新規(JS)
    function createbtn2ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {
            'bum01':$('#bum01c').val(),'bum02':$('#bum02c').val(),
            'bum03':$('#bum03c').val(),'bum04':$('#bum04c').val(),
            'bum05':$('#bum05c').val(),'bum06':$('#bum06c').val(),
            'bum07':$('#bum07c').val(),'bum08':$('#bum08c').val(),
            'bum09':$('#bum09c').val(),'bum10':$('#bum10c').val(),
            'bum11':$('#bum11c').val(),'bum12':$('#bum12c').val(),
            'bum13':$('#bum13c').val(),'bum14':$('#bum14c').val(),
            'bum15':$('#bum15c').val(),'bum16':$('#bum16c').val(),
            'bum17':$('#bum17c').val(),'bum18':$('#bum18c').val(),
            'bum19':$('#bum19c').val(),'bum20':$('#bum20c').val(),
            'bum21':$('#bum21c').val(),'bum22':$('#bum22c').val(),
            'bum23':$('#bum23c').val(),'bum24':$('#bum24c').val(),
            'bum25':$('#bum25c').val(),'bum26':$('#bum26c').val(),
            'bum27':$('#bum27c').val(),'bum28':$('#bum28c').val(),
            'bum29':$('#bum29c').val(),'bum30':$('#bum30c').val(),
            'lab01':$('#lab01c').text(),'lab02':$('#lab02c').text(),
            'lab03':$('#lab03c').text(),'lab04':$('#lab04c').text(),
            'lab05':$('#lab05c').text(),'lab06':$('#lab06c').text(),
            'lab07':$('#lab07c').text(),'lab08':$('#lab08c').text(),
            'lab09':$('#lab09c').text(),'lab10':$('#lab10c').text(),
            'lab11':$('#lab11c').text(),'lab12':$('#lab12c').text(),
            'lab13':$('#lab13c').text(),'lab14':$('#lab14c').text(),
            'lab15':$('#lab15c').text(),'lab16':$('#lab16c').text(),
            'lab17':$('#lab17c').text(),'lab18':$('#lab18c').text(),
            'lab19':$('#lab19c').text(),'lab20':$('#lab20c').text(),
            'lab21':$('#lab21c').text(),'lab22':$('#lab22c').text(),
            'lab23':$('#lab23c').text(),'lab24':$('#lab24c').text(),
            'lab25':$('#lab25c').text(),'lab26':$('#lab26c').text(),
            'lab27':$('#lab27c').text(),'lab28':$('#lab28c').text(),
            'lab29':$('#lab29c').text(),'lab30':$('#lab30c').text()
            };
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#modal_c').modal('hide');
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
        $this->addEventListener('#createbtn2','click','createData','ajax');
    }
    // 新規(PHP)
    function createData($data){
        $this->err = '';
        // 受信データの加工
        $data['bum16'] = str_replace(':','',$data['bum16']);
        $bum26w = explode(']',$data['bum26']);
        $data['bum26'] = str_replace('[','',$bum26w[0]); // 番号
        $bum27w = explode(']',$data['bum27']);
        $data['bum27'] = str_replace('[','',$bum27w[0]); // 番号
        if(!$data['bum02']){
            $data['bum02'] = '2200-01-01';
        }

        // エラーチェック
        $this->validate($data['bum01'], 'date', array(0, 10, false), $data['lab01']); // 開始
        $this->validate($data['bum02'], 'date', array(1, 10, true), $data['lab02']); // 終了
        $this->validate($data['bum03'] ,'str',  array(0,10,false), $data['lab03']); // 部門No.
        $this->validate($data['bum04'] ,'str',  array(1,10,true), $data['lab04']); // 部門名
        $this->validate($data['bum05'] ,'str',  array(1,10,true), $data['lab05']); // 部門名カナ
        $this->validate($data['bum06'] ,'str',  array(1,50,true), $data['lab06']); // 部門名（正式）
        $this->validate($data['bum07'] ,'str',  array(1,50,true), $data['lab07']); // 部門名カナ（正式）
        $this->validate($data['bum08'] ,'str',  array(1,100,true),$data['lab08']); // 事業内容
        $this->validate($data['bum09'] ,'str',  array(0,20,false), $data['lab09']); // 識別ID
        $this->validate($data['bum10'] ,'str',  array(0,0,false), $data['lab10']); // -
        $this->validate($data['bum11'] ,'str',  array(1,1,true),  $data['lab11']); // 勤怠パターン
        $this->validate($data['bum12'] ,'str',  array(2,2,true),  $data['lab12']); // 残業計算タイプ
        $this->validate($data['bum13'] ,'str',  array(1,2,false), $data['lab13']); // カレンダーCD
        $this->validate($data['bum14'] ,'str',  array(3,3,true),  $data['lab14']); // 週の起点
        $this->validate($data['bum15'] ,'int',  array(1,2,true),  $data['lab15']); // 時間集計単位
        $this->validate($data['bum16'] ,'time', array(4,4,true),  $data['lab16']); // 有休時間数
        $this->validate($data['bum17'] ,'int',  array(0,5,false), $data['lab17']); // 請求単価
        $this->validate($data['bum18'] ,'str',  array(0,0,false), $data['lab18']); // -
        $this->validate($data['bum19'] ,'str',  array(0,0,false), $data['lab19']); // -
        $this->validate($data['bum20'] ,'str',  array(0,0,false), $data['lab20']); // -
        $this->validate($data['bum21'] ,'str',  array(1,5,true), $data['lab21']); // 管轄拠点
        $this->validate($data['bum22'] ,'str',  array(1,5,true), $data['lab22']); // 契約区分
        $this->validate($data['bum23'] ,'str',  array(1,20,true), $data['lab23']); // 所属グループ
        $this->validate($data['bum24'] ,'str',  array(0,10,false), $data['lab24']); // ﾌﾘｰｸﾞﾙｰﾌﾟ1
        $this->validate($data['bum25'] ,'str',  array(0,10,false), $data['lab25']); // ﾌﾘｰｸﾞﾙｰﾌﾟ2
        $this->validate($data['bum26'] ,'str',  array(1,10,true), $data['lab26']); // 請求先
        $this->validate($data['bum27'] ,'str',  array(0,10,false), $data['lab27']); // 会社
        $this->validate($data['bum28'] ,'str',  array(0,5,false), $data['lab28']); // -
        $this->validate($data['bum29'] ,'str',  array(0,5,false), $data['lab29']); // -
        $this->validate($data['bum30'] ,'str',  array(0,5,false), $data['lab30']); // -
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        $now = date('Y-m-d H:i:s');
        // 採番
        $data['bum03'] = '';
        $sql = "select sai02 from saiban where sai01='bumon';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data['bum03'] = $result['sai02'] + 1;
        }
        if($data['bum03'] != ''){
            $par = array();
            $sql = "insert into bumon (";
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "bum".$a.",";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",crdt,crid,updt,upid) values (";
            for($i=1; $i <= $this->koumokusu; $i++){
                $sql .= "?,";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",?,?,?,?);";
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                $par[] = $data["bum$a"];
            }
            array_push($par,$now,$_SESSION['id'],$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);

            if($wrk[0]['res']){
                $sql2 = "update saiban set sai02 = ? where sai01 = 'bumon';";
                $par2 = array($data['bum03']);
                $stmt = $this->db->prepare($sql2);
                $wrk[0]['res'] = $stmt->execute($par2);
            }
        }else{
            $wrk[0]['res'] = false;
        }
        echo json_encode($wrk);
    }
    
    // 更新(JS)
    function upbtn1ClickJs(){
        $this->js1 = <<<"__"
            var obj = {
            'bum01':$('#bum01ud').val(),'bum02':$('#bum02ud').val(),
            'bum03':$('#bum03ud').val(),'bum04':$('#bum04ud').val(),
            'bum05':$('#bum05ud').val(),'bum06':$('#bum06ud').val(),
            'bum07':$('#bum07ud').val(),'bum08':$('#bum08ud').val(),
            'bum09':$('#bum09ud').val(),'bum10':$('#bum10ud').val(),
            'bum11':$('#bum11ud').val(),'bum12':$('#bum12ud').val(),
            'bum13':$('#bum13ud').val(),'bum14':$('#bum14ud').val(),
            'bum15':$('#bum15ud').val(),'bum16':$('#bum16ud').val(),
            'bum17':$('#bum17ud').val(),'bum18':$('#bum18ud').val(),
            'bum19':$('#bum19ud').val(),'bum20':$('#bum20ud').val(),
            'bum21':$('#bum21ud').val(),'bum22':$('#bum22ud').val(),
            'bum23':$('#bum23ud').val(),'bum24':$('#bum24ud').val(),
            'bum25':$('#bum25ud').val(),'bum26':$('#bum26ud').val(),
            'bum27':$('#bum27ud').val(),'bum28':$('#bum28ud').val(),
            'bum29':$('#bum29ud').val(),'bum30':$('#bum30ud').val(),
            'lab01':$('#lab01ud').text(),'lab02':$('#lab02ud').text(),
            'lab03':$('#lab03ud').text(),'lab04':$('#lab04ud').text(),
            'lab05':$('#lab05ud').text(),'lab06':$('#lab06ud').text(),
            'lab07':$('#lab07ud').text(),'lab08':$('#lab08ud').text(),
            'lab09':$('#lab09ud').text(),'lab10':$('#lab10ud').text(),
            'lab11':$('#lab11ud').text(),'lab12':$('#lab12ud').text(),
            'lab13':$('#lab13ud').text(),'lab14':$('#lab14ud').text(),
            'lab15':$('#lab15ud').text(),'lab16':$('#lab16ud').text(),
            'lab17':$('#lab17ud').text(),'lab18':$('#lab18ud').text(),
            'lab19':$('#lab19ud').text(),'lab20':$('#lab20ud').text(),
            'lab21':$('#lab21ud').text(),'lab22':$('#lab22ud').text(),
            'lab23':$('#lab23ud').text(),'lab24':$('#lab24ud').text(),
            'lab25':$('#lab25ud').text(),'lab26':$('#lab26ud').text(),
            'lab27':$('#lab27ud').text(),'lab28':$('#lab28ud').text(),
            'lab29':$('#lab29ud').text(),'lab30':$('#lab30ud').text()
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
    // 更新(PHP)
    function updateData($data){
        $this->err = '';
        // 受信データの加工
        $data['bum16'] = str_replace(':','',$data['bum16']);
        $bum26w = explode(']',$data['bum26']);
        $data['bum26'] = str_replace('[','',$bum26w[0]); // 番号
        $bum27w = explode(']',$data['bum27']);
        $data['bum27'] = str_replace('[','',$bum27w[0]); // 番号
        if(!$data['bum02']){
            $data['bum02'] = '2200-01-01';
        }
        // エラーチェック
        $this->validate($data['bum01'], 'date', array(0, 10, false), $data['lab01']); // 開始
        $this->validate($data['bum02'], 'date', array(1, 10, true), $data['lab02']); // 終了
        $this->validate($data['bum03'] ,'str',  array(0,10,false), $data['lab03']); // 部門No.
        $this->validate($data['bum04'] ,'str',  array(1,10,true), $data['lab04']); // 部門名
        $this->validate($data['bum05'] ,'str',  array(1,10,true), $data['lab05']); // 部門名カナ
        $this->validate($data['bum06'] ,'str',  array(1,50,true), $data['lab06']); // 部門名（正式）
        $this->validate($data['bum07'] ,'str',  array(1,50,true), $data['lab07']); // 部門名カナ（正式）
        $this->validate($data['bum08'] ,'str',  array(1,100,true),$data['lab08']); // 事業内容
        $this->validate($data['bum09'] ,'str',  array(0,20,false), $data['lab09']); // 識別ID
        $this->validate($data['bum10'] ,'str',  array(0,0,false), $data['lab10']); // -
        $this->validate($data['bum11'] ,'str',  array(1,1,true),  $data['lab11']); // 勤怠パターン
        $this->validate($data['bum12'] ,'str',  array(2,2,true),  $data['lab12']); // 残業計算タイプ
        $this->validate($data['bum13'] ,'str',  array(1,2,false), $data['lab13']); // カレンダーCD
        $this->validate($data['bum14'] ,'str',  array(3,3,true),  $data['lab14']); // 週の起点
        $this->validate($data['bum15'] ,'int',  array(1,2,true),  $data['lab15']); // 時間集計単位
        $this->validate($data['bum16'] ,'time', array(4,4,true),  $data['lab16']); // 有休時間数
        $this->validate($data['bum17'] ,'int',  array(0,5,false), $data['lab17']); // 請求単価
        $this->validate($data['bum18'] ,'str',  array(0,0,false), $data['lab18']); // -
        $this->validate($data['bum19'] ,'str',  array(0,0,false), $data['lab19']); // -
        $this->validate($data['bum20'] ,'str',  array(0,0,false), $data['lab20']); // -
        $this->validate($data['bum21'] ,'str',  array(1,5,true), $data['lab21']); // 管轄拠点
        $this->validate($data['bum22'] ,'str',  array(1,5,true), $data['lab22']); // 契約区分
        $this->validate($data['bum23'] ,'str',  array(1,20,true), $data['lab23']); // 所属グループ
        $this->validate($data['bum24'] ,'str',  array(0,10,false), $data['lab24']); // ﾌﾘｰｸﾞﾙｰﾌﾟ1
        $this->validate($data['bum25'] ,'str',  array(0,10,false), $data['lab25']); // ﾌﾘｰｸﾞﾙｰﾌﾟ2
        $this->validate($data['bum26'] ,'str',  array(1,10,true), $data['lab26']); // 請求先
        $this->validate($data['bum27'] ,'str',  array(0,10,false), $data['lab27']); // 会社
        $this->validate($data['bum28'] ,'str',  array(0,5,false), $data['lab28']); // -
        $this->validate($data['bum29'] ,'str',  array(0,5,false), $data['lab29']); // -
        $this->validate($data['bum30'] ,'str',  array(0,5,false), $data['lab30']); // -
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        $now = date('Y-m-d H:i:s');
        $sql = "update bumon set ";
        for($i=1; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            $sql .= "bum".$a."=?,";
        }
        $sql = substr($sql,0,-1);
        $sql .= ",updt=?,upid=? where bum09=?;";
        for($i=1; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            $par[] = $data["bum$a"];
        }
        array_push($par,$now,$_SESSION['id'],$data["bum09"]);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }

 
    // 新期間を追加(JS)
    function createbtn3ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {
            'bum01':$('#bum01ud').val(),'bum02':$('#bum02ud').val(),
            'bum03':$('#bum03ud').val(),'bum04':$('#bum04ud').val(),
            'bum05':$('#bum05ud').val(),'bum06':$('#bum06ud').val(),
            'bum07':$('#bum07ud').val(),'bum08':$('#bum08ud').val(),
            'bum09':$('#bum09ud').val(),'bum10':$('#bum10ud').val(),
            'bum11':$('#bum11ud').val(),'bum12':$('#bum12ud').val(),
            'bum13':$('#bum13ud').val(),'bum14':$('#bum14ud').val(),
            'bum15':$('#bum15ud').val(),'bum16':$('#bum16ud').val(),
            'bum17':$('#bum17ud').val(),'bum18':$('#bum18ud').val(),
            'bum19':$('#bum19ud').val(),'bum20':$('#bum20ud').val(),
            'bum21':$('#bum21ud').val(),'bum22':$('#bum22ud').val(),
            'bum23':$('#bum23ud').val(),'bum24':$('#bum24ud').val(),
            'bum25':$('#bum25ud').val(),'bum26':$('#bum26ud').val(),
            'bum27':$('#bum27ud').val(),'bum28':$('#bum28ud').val(),
            'bum29':$('#bum29ud').val(),'bum30':$('#bum30ud').val(),
            'lab01':$('#lab01ud').text(),'lab02':$('#lab02ud').text(),
            'lab03':$('#lab03ud').text(),'lab04':$('#lab04ud').text(),
            'lab05':$('#lab05ud').text(),'lab06':$('#lab06ud').text(),
            'lab07':$('#lab07ud').text(),'lab08':$('#lab08ud').text(),
            'lab09':$('#lab09ud').text(),'lab10':$('#lab10ud').text(),
            'lab11':$('#lab11ud').text(),'lab12':$('#lab12ud').text(),
            'lab13':$('#lab13ud').text(),'lab14':$('#lab14ud').text(),
            'lab15':$('#lab15ud').text(),'lab16':$('#lab16ud').text(),
            'lab17':$('#lab17ud').text(),'lab18':$('#lab18ud').text(),
            'lab19':$('#lab19ud').text(),'lab20':$('#lab20ud').text(),
            'lab21':$('#lab21ud').text(),'lab22':$('#lab22ud').text(),
            'lab23':$('#lab23ud').text(),'lab23':$('#lab24ud').text(),
            'lab25':$('#lab25ud').text(),'lab26':$('#lab26ud').text(),
            'lab27':$('#lab27ud').text(),'lab28':$('#lab28ud').text(),
            'lab29':$('#lab29ud').text(),'lab30':$('#lab30ud').text()
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
        $this->addEventListener('#createbtn3','click','createRangeData','ajax');
    }
    // 新期間を追加(PHP)
    function createRangeData($data){
        $this->err = '';
        // 受信データの加工
        $data['bum16'] = str_replace(':','',$data['bum16']);
		$data['bum09'] = '';
        $bum26w = explode(']',$data['bum26']);
        $data['bum26'] = str_replace('[','',$bum26w[0]); // 番号
        if(!$data['bum02']){
            $data['bum02'] = '2200-01-01';
        }
        // エラーチェック
        $this->validate($data['bum01'], 'date', array(0, 10, false), $data['lab01']); // 開始
        $this->validate($data['bum02'], 'date', array(1, 10, true), $data['lab02']); // 終了
        $this->validate($data['bum03'] ,'str',  array(0,10,false), $data['lab03']); // 部門No.
        $this->validate($data['bum04'] ,'str',  array(1,10,true), $data['lab04']); // 部門名
        $this->validate($data['bum05'] ,'str',  array(1,10,true), $data['lab05']); // 部門名カナ
        $this->validate($data['bum06'] ,'str',  array(1,50,true), $data['lab06']); // 部門名（正式）
        $this->validate($data['bum07'] ,'str',  array(1,50,true), $data['lab07']); // 部門名カナ（正式）
        $this->validate($data['bum08'] ,'str',  array(1,100,true),$data['lab08']); // 事業内容
        $this->validate($data['bum09'] ,'str',  array(0,20,false), $data['lab09']); // 識別ID
        $this->validate($data['bum10'] ,'str',  array(0,0,false), $data['lab10']); // -
        $this->validate($data['bum11'] ,'str',  array(1,1,true),  $data['lab11']); // 勤怠パターン
        $this->validate($data['bum12'] ,'str',  array(2,2,true),  $data['lab12']); // 残業計算タイプ
        $this->validate($data['bum13'] ,'str',  array(1,2,false), $data['lab13']); // カレンダーCD
        $this->validate($data['bum14'] ,'str',  array(3,3,true),  $data['lab14']); // 週の起点
        $this->validate($data['bum15'] ,'int',  array(1,2,true),  $data['lab15']); // 時間集計単位
        $this->validate($data['bum16'] ,'time', array(4,4,true),  $data['lab16']); // 有休時間数
        $this->validate($data['bum17'] ,'int',  array(0,5,false), $data['lab17']); // 請求単価
        $this->validate($data['bum18'] ,'str',  array(0,0,false), $data['lab18']); // -
        $this->validate($data['bum19'] ,'str',  array(0,0,false), $data['lab19']); // -
        $this->validate($data['bum20'] ,'str',  array(0,0,false), $data['lab20']); // -
        $this->validate($data['bum21'] ,'str',  array(1,5,true), $data['lab21']); // 管轄拠点
        $this->validate($data['bum22'] ,'str',  array(1,5,true), $data['lab22']); // 契約区分
        $this->validate($data['bum23'] ,'str',  array(1,20,true), $data['lab23']); // 所属グループ
        $this->validate($data['bum24'] ,'str',  array(0,10,false), $data['lab24']); // ﾌﾘｰｸﾞﾙｰﾌﾟ1
        $this->validate($data['bum25'] ,'str',  array(0,10,false), $data['lab25']); // ﾌﾘｰｸﾞﾙｰﾌﾟ2
        $this->validate($data['bum26'] ,'str',  array(1,10,true), $data['lab26']); // 請求先
        $this->validate($data['bum27'] ,'str',  array(0,10,false), $data['lab27']); // 会社
        $this->validate($data['bum28'] ,'str',  array(0,5,false), $data['lab28']); // -
        $this->validate($data['bum29'] ,'str',  array(0,5,false), $data['lab29']); // -
        $this->validate($data['bum30'] ,'str',  array(0,5,false), $data['lab30']); // -
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        $now = date('Y-m-d H:i:s');
        // 重複期間の存在チェック (始点～終点の間に他の始点・終点がある場合はエラー)
        $st = '';
        $par = array($data['bum03'],$data['bum01'],$data['bum02'],$data['bum01'],$data['bum02']);
        $sql = "select bum01,bum02 from bumon where bum03 = ? and ((bum01 >= ? and bum01 <= ?) or (bum02 >= ? and bum02 <= ?)) limit 1;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $st = $result['bum01'];
            $en = $result['bum02'];
        }
        if($st == ''){
            $par = array();
            $sql = "insert into bumon (";
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "bum".$a.",";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",crdt,crid,updt,upid) values (";
            for($i=1; $i <= $this->koumokusu; $i++){
                $sql .= "?,";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",?,?,?,?);";
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                $par[] = $data["bum$a"];
            }
            array_push($par,$now,$_SESSION['id'],$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
        }else{
            $wrk[0]['res'] = $st . ' - ' . $en;
        }
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
            var obj = {"bum09":$("#bum09ud").val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
			$('#searchbtn2').trigger('click');
            $('#searchbtn2').trigger('click');
			$('#modal_d').modal('hide');
            $('#modal_ud').modal('hide');
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
        $sql = "delete from bumon where bum09=?;";
        $par = array($data['bum09']);
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
        $sql = "select * from kensakuptbu where knsb01=? order by knsb02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $knsb02 = $result['knsb02'];
            $knsb03 = $result['knsb03'];
            $modal['combo0'] .= "<OPTION value='$knsb02'>$knsb03</OPTION>";
        }
        $wrk = array(''=>'未選択','bum01'=>'適用開始日','bum02'=>'適用終了日','bum03'=>'部門No.','bum04'=>'部門名','bum05'=>'部門名カナ','bum06'=>'部門名（正式）','bum07'=>'部門名カナ（正式）','bum08'=>'業務内容','bum09'=>'','bum10'=>'','bum11'=>'勤怠パターン','bum12'=>'残業計算タイプ','bum13'=>'カレンダーCD','bum14'=>'週の起点','bum15'=>'時間集計単位','bum16'=>'有休時間数','bum17'=>'請求単価','bum18'=>'','bum19'=>'','bum20'=>'');

        foreach($wrk as $key => $val){
            $combo1 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo1_1'] = $modal['combo1_2'] = $modal['combo1_3'] = $modal['combo1_4'] = $modal['combo1_n1'] = $modal['combo1_n2'] = $modal['combo1_n3'] = $modal['combo1_n4'] = $combo1;
        $wrk = array(''=>'未選択','='=>'に等しい','!='=>'に等しくない','>='=>'以上','<='=>'以下','not in'=>'に含まれない','in'=>'に含まれる','between'=>'の間(以上/以下)','not between'=>'の間にない','like'=>'の文字を含む');
        foreach($wrk as $key => $val){
            $combo2 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo2_1'] = $modal['combo2_2'] = $modal['combo2_3'] = $modal['combo2_4'] = $combo2;
        
        $wrk = array(''=>'','and'=>'かつ','or'=>'又は');
        foreach($wrk as $key => $val){
            $combo3 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo3_1'] = $modal['combo3_2'] = $modal['combo3_3'] = $modal['combo3_4'] = $combo3;

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
        $numArray = array('bum07','bum08','bum25','bum11','bum02','bum12','bum13','bum14','bum05','bum06','bum17','bum18','bum19','bum20','bum21','bum22','bum23','bum24','bum10','bum15','bum16','bum09');
        for($i=0; $i < $this->koumokusu; $i++){
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
        $('#modalLoader1').modal({backdrop:'static'});
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||null);
            console.log(data);
            $("#tabu1").tabulator("setData", data[0]["html"]);
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
        $i = 0;
        $sql = "select bum01,bum02,bum03,bum04,bum05,bum06,bum07,bum08,bum09,bum10,bum11,bum12,bum13,bum14,bum15,bum16,bum17,bum18,bum19,bum20,";
        $sql .= "crdt,crid,updt,upid,a1.nm as crnm,a2.nm as upnm from bumon";
        $sql .= " left join namae as a1 on bumon.crid=a1.cd and a1.st<=bumon.crdt and a1.en>=bumon.crdt";
        $sql .= " left join namae as a2 on bumon.crid=a2.cd and a2.st<=bumon.crdt and a2.en>=bumon.crdt";
        $sql .= " where $sqlwhere $orderby;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $tabuwrk['ボタン'] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$result['bum03'].'" uid="'.$result['bum09'].'">'.$result['bum03'].'</button>'; // 選択ボタン
            $tabuwrk['部門名'] = $result['bum04'];
            $tabuwrk['開始'] = $result['bum01'];
            $tabuwrk['終了'] = $result['bum02'];
            $tabuwrk['部門名カナ'] = $result['bum05'];
            $tabuwrk['部門名正式'] = $result['bum06'];
            $tabuwrk['部門名カナ正式'] = $result['bum07'];
            $tabuwrk['業務内容'] = $result['bum08'];
            $tabuwrk['識別コード'] = $result['bum09'];
            //$tabuwrk[''] = $result['bum10'];
            $tabuwrk['勤怠パターン'] = $result['bum11'];
            $tabuwrk['残業計算パターン'] = $result['bum12'];
            $tabuwrk['カレンダー'] = $result['bum13'];
            $tabuwrk['週の起点'] = $result['bum14'];
            $tabuwrk['時間集計単位'] = $result['bum15'];
            $tabuwrk['有休時間数'] = $result['bum16'];
            $tabuwrk['請求単価'] = $result['bum17'];
            //$tabuwrk['bum18'] = $result['bum18'];
            //$tabuwrk['bum19'] = $result['bum19'];
            //$tabuwrk['bum20'] = $result['bum20'];
            $tabuwrk['作成日時']  = $result['crdt'];
            $tabuwrk['作成者']  = $result['crnm'];
            $tabuwrk['更新日時']  = $result['updt'];
            $tabuwrk['更新者']  = $result['upnm'];
            $tabuwrk['ダミー'] = '　';
            $tabuwrk = str_replace(null,'',$tabuwrk); // null値除去
            $tabudata[] = $tabuwrk;
        }
        $tabudata = $this->tabulateData($tabudata);
        
        $res[0]['html'] = $tabudata; //$this->createTableSource($wrk);
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
            $sql = "select max(knsb02)+1 as ptcd from kensakuptbu where knsb01=? group by knsb01;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $wrkcd = $result['ptcd'];
            }
            if($wrkcd == ''){$wrkcd = 1;}
            $par = array();
            $sql = "insert into kensakuptbu (";
            for($i=1; $i <= 4; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "knsb".$a.",";
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
        $sql = "select * from kensakuptbu where knsb01=? order by knsb02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $knsb02 = $result['knsb02'];
            $knsb03 = $result['knsb03'];
            $wrk[0]['html'] .= "<OPTION value='$knsb02'>$knsb03</OPTION>";
        }
        echo json_encode($wrk);
        exit();
    }
    
    // パターン変更
    function kpcomboSelectJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"knsb02":$('#kpcombo').val()};
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
    //
    function patternRead($data){
        $sql = "select * from kensakuptbu where knsb01=? and knsb02=?;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id'],$data['knsb02']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $knsb04 = $result['knsb04'];
        }
        $wrk[0]['str'] = $knsb04;
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
            var obj = {"knsb02":$("#kpcombo").val()};
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
        $sql = "delete from kensakuptbu where (knsb01=? and knsb02=?);";
        $par = array($_SESSION['id'],$data['knsb02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }
}

// ** page info ** //

$p = new page();

$data['pr1'] = array('title' => '部門マスター保守'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
$data['pr3'] = array('active'=>'master'); // ナビメニュー

loadResource($p,$data);