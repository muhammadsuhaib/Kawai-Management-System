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
    private $koumokusu = 30; // 表示するfield数(マスターfield数)
    
    // JS初期処理
    function initJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
        // テンプレサンプルデータ削除
        $("#tablebody tr").remove();
        $("#tablebody td").remove();

       // 検索条件初期値設定
        var json_data_stock_obj = {"jyouken1":"","maekakko1":"","koumoku1":"kais02","val1":"","enzan1":"=","atokakko1":"","jyouken2":"","maekakko2":"","koumoku2":"","val2":"","enzan2":"","atokakko2":"","jyouken3":"","maekakko3":"","koumoku3":"","val3":"","enzan3":"","atokakko3":"","jyouken4":"","maekakko4":"","koumoku4":"","val4":"","enzan4":"","atokakko4":"","order1":"kais02","orderopt1":"ASC","order2":"","orderopt2":"","order3":"","orderopt3":"","order4":"","orderopt4":""};
        $('#json_data_stock').val(JSON.stringify(json_data_stock_obj));
       $('#searchbtn2').trigger('click');
       
       // ヒントの表示
       var hintObj = {
       "":"　",
       "kais01":"適用開始：マスターの適用開始日。",
       "kais02":"適用終了：マスターの適用終了日。空が最新のマスター。",
       "kais03":"会社番号",
       "kais04":"会社名（略称。20文字以内。）",
       "kais05":"会社名カナ（略称。20文字以内。）",
       "kais06":"会社名（正式名称）",
       "kais07":"会社名カナ（正式名称）",
       "kais08":"備考",
       "kais09":"データの識別CD（unique）",
       "kais10":"種別(1～5:請求先　90～会社情報)",
       "kais11":"郵便番号",
       "kais12":"都道府県",
       "kais13":"市区町村",
       "kais14":"町域",
       "kais15":"アパート",
       "kais16":"電話番号",
       "kais17":"FAX番号",
       "kais18":"メールアドレス",
       "kais19":"担当部署",
       "kais20":"担当者名",
       "kais21":"請求日",
       "kais22":"",
       "kais23":"",
       "kais24":"uniqueCD",
       "kais25":"",
       "kais26":"",
       "kais27":"",
       "kais28":"",
       "kais29":"",
       "kais30":""
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
__;
        $this->addEventListener('#createbtn1','click','modalCallCr','ajax');
    }
    // 新規モーダル表示(PHP)
    function modalCallCr($data){
        $modal = $this->readModalSource('modal_c');
        $res[0]['html'] = implode($modal);
        echo json_encode($res);
        exit();
    }

    // 更新・削除モーダル表示(JS)
    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"kais24":event.getAttribute('uid')};
            //console.log(obj);
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_ud').remove();
            $('#modalParent1').append(data[0]["html"]);
            $('#kais10ud').val(data[1][10]);
            //console.log(data[1][10]);
            $('#modal_ud').modal({backdrop:'static'});
__;
        $this->addEventListener('','wait','modalCallUd','ajax');
    }
    // 更新・削除モーダル表示(PHP)
    function modalCallUd($data){
        $modal = $this->readModalSource('modal_ud');
        $sql = 'select * from kaisya where kais24=?;';
        $par = array($data['kais24']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $modal['kais01ud'] = $this->format($result["kais01"],'date-');
            $modal['kais02ud'] = $this->format($result["kais02"],'date-');
            $modal['kais03ud'] = $result['kais03'];
            $modal['kais04ud'] = $result['kais04'];
            $modal['kais05ud'] = $result['kais05'];
            $modal['kais06ud'] = $result['kais06'];
            $modal['kais07ud'] = $result['kais07'];
            $modal['kais08ud'] = $result['kais08'];
            $modal['kais09ud'] = $result['kais09'];
            $res[1][10] = $result['kais10']; // combobox
            $modal['kais11ud'] = $result['kais11'];
            $modal['kais12ud'] = $result['kais12'];
            $modal['kais13ud'] = $result['kais13'];
            $modal['kais14ud'] = $result['kais14'];
            $modal['kais15ud'] = $result['kais15'];
            $modal['kais16ud'] = $result['kais16'];
            $modal['kais17ud'] = $result['kais17'];
            $modal['kais18ud'] = $result['kais18'];
            $modal['kais19ud'] = $result['kais19'];
            $modal['kais20ud'] = $result['kais20'];
            $modal['kais21ud'] = $result['kais21'];
            $modal['kais22ud'] = $result['kais22'];
            $modal['kais23ud'] = $result['kais23'];
            $modal['kais24ud'] = '';//$result['kais24'];
            $modal['kais25ud'] = $result['kais25'];
            $modal['kais26ud'] = $result['kais26'];
            $modal['kais27ud'] = $result['kais27'];
            $modal['kais28ud'] = $result['kais28'];
            $modal['kais29ud'] = $result['kais29'];
            $modal['kais30ud'] = $result['kais30'];
        }
        $res[0]['html'] = implode("", $modal);
        echo json_encode($res);
        exit();
    }

 
    // 新規(JS)
    function createbtn2ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {
            'kais01':$('#kais01c').val(),'kais02':$('#kais02c').val(),
            'kais03':$('#kais03c').val(),'kais04':$('#kais04c').val(),
            'kais05':$('#kais05c').val(),'kais06':$('#kais06c').val(),
            'kais07':$('#kais07c').val(),'kais08':$('#kais08c').val(),
            'kais09':$('#kais09c').val(),'kais10':$('#kais10c').val(),
            'kais11':$('#kais11c').val(),'kais12':$('#kais12c').val(),
            'kais13':$('#kais13c').val(),'kais14':$('#kais14c').val(),
            'kais15':$('#kais15c').val(),'kais16':$('#kais16c').val(),
            'kais17':$('#kais17c').val(),'kais18':$('#kais18c').val(),
            'kais19':$('#kais19c').val(),'kais20':$('#kais20c').val(),
            'kais21':$('#kais21c').val(),'kais22':$('#kais22c').val(),
            'kais23':$('#kais23c').val(),'kais24':$('#kais24c').val(),
            'kais25':$('#kais25c').val(),'kais26':$('#kais26c').val(),
            'kais27':$('#kais27c').val(),'kais28':$('#kais28c').val(),
            'kais29':$('#kais29c').val(),'kais30':$('#kais30c').val(),
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
        $data['kais01'] = str_replace('-','',$data['kais01']);
        $data['kais02'] = str_replace('-','',$data['kais02']);
        $data['kais24'] = $data['kais03'].'-'.$data['kais10']; // uniqueCD = 会社CD-種別

        // エラーチェック
        if(!$this->err){$this->validate($data['kais01'] ,'date', array(0,8,false),  $data['lab01']);} // 適用開始日
        if(!$this->err){$this->validate($data['kais02'] ,'date', array(0,8,false), $data['lab02']);} // 適用終了日
        if(!$this->err){$this->validate($data['kais03'] ,'str',  array(0,0,false), $data['lab03']);} // 会社No.
        if(!$this->err){$this->validate($data['kais04'] ,'str',  array(1,10,true), $data['lab04']);} // 会社名
        if(!$this->err){$this->validate($data['kais05'] ,'str',  array(1,10,true), $data['lab05']);} // 会社名カナ
        if(!$this->err){$this->validate($data['kais06'] ,'str',  array(1,50,true), $data['lab06']);} // 会社名（正式）
        if(!$this->err){$this->validate($data['kais07'] ,'str',  array(1,50,true), $data['lab07']);} // 会社名カナ（正式）
        if(!$this->err){$this->validate($data['kais08'] ,'str',  array(1,100,false),$data['lab08']);} // 備考
        if(!$this->err){$this->validate($data['kais09'] ,'str',  array(0,20,false), $data['lab09']);} // 識別ID
        if(!$this->err){$this->validate($data['kais10'] ,'str',  array(1,2,false), $data['lab10']);} // 種別
        if(!$this->err){$this->validate($data['kais11'] ,'str',  array(7,8,false),  $data['lab11']);} // 郵便番号
        if(!$this->err){$this->validate($data['kais12'] ,'str',  array(0,25,false),  $data['lab12']);} // 都道府県
        if(!$this->err){$this->validate($data['kais13'] ,'str',  array(0,25,false), $data['lab13']);} // 市区町村
        if(!$this->err){$this->validate($data['kais14'] ,'str',  array(0,25,false),  $data['lab14']);} // 町域
        if(!$this->err){$this->validate($data['kais15'] ,'str',  array(0,25,false),  $data['lab15']);} // アパート
        if(!$this->err){$this->validate($data['kais16'] ,'str',  array(0,15,false),  $data['lab16']);} // 電話番号
        if(!$this->err){$this->validate($data['kais17'] ,'str',  array(0,15,false), $data['lab17']);} // FAX番号
        if(!$this->err){$this->validate($data['kais18'] ,'str',  array(0,50,false), $data['lab18']);} // メールアドレス
        if(!$this->err){$this->validate($data['kais19'] ,'str',  array(0,25,false), $data['lab19']);} // 担当部署
        if(!$this->err){$this->validate($data['kais20'] ,'str',  array(0,25,false), $data['lab20']);} // 担当者名
        if(!$this->err){$this->validate($data['kais21'] ,'str',  array(0,2,false),  $data['lab21']);} // 請求日
        if(!$this->err){$this->validate($data['kais22'] ,'str',  array(0,25,false), $data['lab22']);} // 担当部署カナ
        if(!$this->err){$this->validate($data['kais23'] ,'str',  array(0,25,false), $data['lab23']);} // 担当者名カナ
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        $today = date('Ymd');
        $now = date('Hi');
        // 採番
        $data['kais03'] = '';
        $sql = "select sai02 from saiban where sai01='kaisya';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data['kais03'] = $result['sai02'] + 1;
        }
        if($data['kais03'] != ''){
            $par = array();
            $sql = "insert into kaisya (";
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "kais".$a.",";
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
                $par[] = $data["kais$a"];
            }
            array_push($par,$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);

            if($wrk[0]['res']){
                $sql2 = "update saiban set sai02 = ? where sai01 = 'kaisya';";
                $par2 = array($data['kais03']);
                $stmt = $this->db->prepare($sql2);
                $wrk[0]['res'] = $stmt->execute($par2);
            }
        }else{
            $wrk[0]['res'] = false;
        }
        echo json_encode($wrk);
        exit();
    }
    
    // 更新(JS)
    function upbtn1ClickJs(){
        $this->js1 = <<<"__"
            var obj = {
            'kais01':$('#kais01ud').val(),'kais02':$('#kais02ud').val(),
            'kais03':$('#kais03ud').val(),'kais04':$('#kais04ud').val(),
            'kais05':$('#kais05ud').val(),'kais06':$('#kais06ud').val(),
            'kais07':$('#kais07ud').val(),'kais08':$('#kais08ud').val(),
            'kais09':$('#kais09ud').val(),'kais10':$('#kais10ud').val(),
            'kais11':$('#kais11ud').val(),'kais12':$('#kais12ud').val(),
            'kais13':$('#kais13ud').val(),'kais14':$('#kais14ud').val(),
            'kais15':$('#kais15ud').val(),'kais16':$('#kais16ud').val(),
            'kais17':$('#kais17ud').val(),'kais18':$('#kais18ud').val(),
            'kais19':$('#kais19ud').val(),'kais20':$('#kais20ud').val(),
            'kais21':$('#kais21ud').val(),'kais22':$('#kais22ud').val(),
            'kais23':$('#kais23ud').val(),'kais24':$('#kais24ud').val(),
            'kais25':$('#kais25ud').val(),'kais26':$('#kais26ud').val(),
            'kais27':$('#kais27ud').val(),'kais28':$('#kais28ud').val(),
            'kais29':$('#kais29ud').val(),'kais30':$('#kais30ud').val(),
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
        $data['kais01'] = str_replace('-','',$data['kais01']);
        $data['kais02'] = str_replace('-','',$data['kais02']);
        $data['kais24'] = $data['kais03'].'-'.$data['kais10']; // uniqueCD = 会社CD-種別

        // エラーチェック
        if(!$this->err){$this->validate($data['kais01'] ,'date', array(0,8,false),  $data['lab01']);} // 適用開始日
        if(!$this->err){$this->validate($data['kais02'] ,'date', array(0,8,false), $data['lab02']);} // 適用終了日
        if(!$this->err){$this->validate($data['kais03'] ,'str',  array(1,20,true), $data['lab03']);} // 会社No.
        if(!$this->err){$this->validate($data['kais04'] ,'str',  array(1,10,true), $data['lab04']);} // 会社名
        if(!$this->err){$this->validate($data['kais05'] ,'str',  array(1,10,true), $data['lab05']);} // 会社名カナ
        if(!$this->err){$this->validate($data['kais06'] ,'str',  array(1,50,true), $data['lab06']);} // 会社名（正式）
        if(!$this->err){$this->validate($data['kais07'] ,'str',  array(1,50,true), $data['lab07']);} // 会社名カナ（正式）
        if(!$this->err){$this->validate($data['kais08'] ,'str',  array(0,100,false),$data['lab08']);} // 備考
        if(!$this->err){$this->validate($data['kais09'] ,'str',  array(1,20,true), $data['lab09']);} // 識別ID
        if(!$this->err){$this->validate($data['kais10'] ,'str',  array(0,2,false), $data['lab10']);} // 種別
        if(!$this->err){$this->validate($data['kais11'] ,'str',  array(0,8,false),  $data['lab11']);} // 郵便番号
        if(!$this->err){$this->validate($data['kais12'] ,'str',  array(0,25,false),  $data['lab12']);} // 都道府県
        if(!$this->err){$this->validate($data['kais13'] ,'str',  array(0,25,false), $data['lab13']);} // 市区町村
        if(!$this->err){$this->validate($data['kais14'] ,'str',  array(0,25,false),  $data['lab14']);} // 町域
        if(!$this->err){$this->validate($data['kais15'] ,'str',  array(0,25,false),  $data['lab15']);} // アパート
        if(!$this->err){$this->validate($data['kais16'] ,'str',  array(0,15,false),  $data['lab16']);} // 電話番号
        if(!$this->err){$this->validate($data['kais17'] ,'str',  array(0,15,false), $data['lab17']);} // FAX番号
        if(!$this->err){$this->validate($data['kais18'] ,'str',  array(0,50,false), $data['lab18']);} // メールアドレス
        if(!$this->err){$this->validate($data['kais19'] ,'str',  array(0,25,false), $data['lab19']);} // 担当部署
        if(!$this->err){$this->validate($data['kais20'] ,'str',  array(0,25,false), $data['lab20']);} // 担当者名
        if(!$this->err){$this->validate($data['kais21'] ,'str',  array(0,2,false),  $data['lab21']);} // 請求日
        if(!$this->err){$this->validate($data['kais22'] ,'str',  array(0,25,false), $data['lab22']);} // 担当部署カナ
        if(!$this->err){$this->validate($data['kais23'] ,'str',  array(0,25,false), $data['lab23']);} // 担当者名カナ

        // 種別の二重登録チェック
        $par = array($data['kais09'],$data['kais03'],$data['kais10']); // 識別ID , 会社No , 種別
        $sql = "select kais10 from kaisya where kais09!=? and kais03=? and kais10=?;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            if($result['kais10']){
                $this->err = '種別が既に存在しています。';
            }
        }
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        $par = array();
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "update kaisya set ";
        for($i=1; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            $sql .= "kais".$a."=?,";
        }
        $sql = substr($sql,0,-1);
        $sql .= ",uday=?,utim=?,uid=? where kais09=?;";
        for($i=1; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            $par[] = $data["kais$a"];
        }
        array_push($par,$today,$now,$_SESSION['id'],$data["kais09"]);
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
            'kais01':$('#kais01ud').val(),'kais02':$('#kais02ud').val(),
            'kais03':$('#kais03ud').val(),'kais04':$('#kais04ud').val(),
            'kais05':$('#kais05ud').val(),'kais06':$('#kais06ud').val(),
            'kais07':$('#kais07ud').val(),'kais08':$('#kais08ud').val(),
            'kais09':$('#kais09ud').val(),'kais10':$('#kais10ud').val(),
            'kais11':$('#kais11ud').val(),'kais12':$('#kais12ud').val(),
            'kais13':$('#kais13ud').val(),'kais14':$('#kais14ud').val(),
            'kais15':$('#kais15ud').val(),'kais16':$('#kais16ud').val(),
            'kais17':$('#kais17ud').val(),'kais18':$('#kais18ud').val(),
            'kais19':$('#kais19ud').val(),'kais20':$('#kais20ud').val(),
            'kais21':$('#kais21ud').val(),'kais22':$('#kais22ud').val(),
            'kais23':$('#kais23ud').val(),'kais24':$('#kais24ud').val(),
            'kais25':$('#kais25ud').val(),'kais26':$('#kais26ud').val(),
            'kais27':$('#kais27ud').val(),'kais28':$('#kais28ud').val(),
            'kais29':$('#kais29ud').val(),'kais30':$('#kais30ud').val(),
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
            //console.log(obj);
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
        $data['kais01'] = str_replace('-','',$data['kais01']);
        $data['kais02'] = str_replace('-','',$data['kais02']);
		$data['kais09'] = ''; // autoincriment
        $data['kais24'] = $data['kais03'].'-'.$data['kais10']; // uniqueCD = 会社CD-種別

        // エラーチェック
        if(!$this->err){$this->validate($data['kais01'] ,'date', array(0,8,false),  $data['lab01']);} // 適用開始日
        if(!$this->err){$this->validate($data['kais02'] ,'date', array(0,8,false), $data['lab02']);} // 適用終了日
        if(!$this->err){$this->validate($data['kais03'] ,'str',  array(1,20,true), $data['lab03']);} // 会社No.
        if(!$this->err){$this->validate($data['kais04'] ,'str',  array(1,10,true), $data['lab04']);} // 会社名
        if(!$this->err){$this->validate($data['kais05'] ,'str',  array(1,10,true), $data['lab05']);} // 会社名カナ
        if(!$this->err){$this->validate($data['kais06'] ,'str',  array(1,50,true), $data['lab06']);} // 会社名（正式）
        if(!$this->err){$this->validate($data['kais07'] ,'str',  array(1,50,true), $data['lab07']);} // 会社名カナ（正式）
        if(!$this->err){$this->validate($data['kais08'] ,'str',  array(0,100,false),$data['lab08']);} // 備考
        if(!$this->err){$this->validate($data['kais09'] ,'str',  array(0,0,false), $data['lab09']);} // 識別ID
        if(!$this->err){$this->validate($data['kais10'] ,'str',  array(0,2,false), $data['lab10']);} // 種別
        if(!$this->err){$this->validate($data['kais11'] ,'str',  array(0,8,false),  $data['lab11']);} // 郵便番号
        if(!$this->err){$this->validate($data['kais12'] ,'str',  array(0,25,false),  $data['lab12']);} // 都道府県
        if(!$this->err){$this->validate($data['kais13'] ,'str',  array(0,25,false), $data['lab13']);} // 市区町村
        if(!$this->err){$this->validate($data['kais14'] ,'str',  array(0,25,false),  $data['lab14']);} // 町域
        if(!$this->err){$this->validate($data['kais15'] ,'str',  array(0,25,false),  $data['lab15']);} // アパート
        if(!$this->err){$this->validate($data['kais16'] ,'str',  array(0,15,false),  $data['lab16']);} // 電話番号
        if(!$this->err){$this->validate($data['kais17'] ,'str',  array(0,15,false), $data['lab17']);} // FAX番号
        if(!$this->err){$this->validate($data['kais18'] ,'str',  array(0,50,false), $data['lab18']);} // メールアドレス
        if(!$this->err){$this->validate($data['kais19'] ,'str',  array(0,25,false), $data['lab19']);} // 担当部署
        if(!$this->err){$this->validate($data['kais20'] ,'str',  array(0,25,false), $data['lab20']);} // 担当者名
        if(!$this->err){$this->validate($data['kais21'] ,'str',  array(0,2,false),  $data['lab21']);} // 請求日
        if(!$this->err){$this->validate($data['kais22'] ,'str',  array(0,25,false), $data['lab22']);} // 担当部署カナ
        if(!$this->err){$this->validate($data['kais23'] ,'str',  array(0,25,false), $data['lab23']);} // 担当者名カナ

        // 種別の二重登録チェック
        $par = array($data['kais03'],$data['kais10']); // 会社No , 種別
        $sql = "select kais10 from kaisya where kais03=? and kais10=?;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            if($result['kais10']){
                $this->err = '種別が既に存在しています。';
            }
        }
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        $today = date('Ymd');
        $now = date('Hi');
        // 重複期間の存在チェック (始点～終点の間に他の始点・終点がある場合はエラー)
        $st = '';
        /*
        $par = array($data['kais03'],$data['kais01'],$data['kais02'],$data['kais01'],$data['kais02']);
        $sql = "select kais01,kais02 from kaisya where kais03 = ? and ((kais01 >= ? and kais01 <= ?) or (kais02 >= ? and kais02 <= ?)) limit 1;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $st = $result['kais01'];
            $en = $result['kais02'];
        }
        */
        if($st == ''){
            $par = array();
            $sql = "insert into kaisya (";
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "kais".$a.",";
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
                $par[] = $data["kais$a"];
            }
            array_push($par,$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
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
            var obj = {"kais09":$("#kais09ud").val()};
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
        $sql = "delete from kaisya where kais09=?;";
        $par = array($data['kais09']);
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
        $sql = "select * from kensakuptka where knsk01=? order by knsk02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $knsk02 = $result['knsk02'];
            $knsk03 = $result['knsk03'];
            $modal['combo0'] .= "<OPTION value='$knsk02'>$knsk03</OPTION>";
        }
        $wrk = array(''=>'未選択','kais01'=>'適用開始日','kais02'=>'適用終了日','kais03'=>'会社No.','kais04'=>'会社名','kais05'=>'会社名カナ','kais06'=>'会社名（正式）','kais07'=>'会社名カナ（正式）','kais08'=>'備考','kais09'=>'識別番号','kais10'=>'種別','kais11'=>'郵便番号','kais12'=>'都道府県','kais13'=>'市区町村','kais14'=>'町域','kais15'=>'アパート','kais16'=>'電話番号','kais17'=>'FAX番号','kais18'=>'メールアドレス','kais19'=>'担当部署','kais20'=>'担当者名','kais21'=>'請求日');

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
        $numArray = array();
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
            //console.log(data);
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
        $sql = "select * from kaisya where $sqlwhere $orderby;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrk[$i]['kais03'] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$result['kais24'].'" uid="'.$result['kais24'].'">'.$result['kais24'].'</button>'; // 選択ボタン
            //$wrk[$i]['kais0102'] = $this->format($result['kais01'],'date-').'～'.$this->format($result['kais02'],'date-'); // 適用期間
            if($result['kais10'] == 1){
                $wrk[$i]['kais10'] = '請求先';
            }elseif($result['kais10'] >= 2 and $result['kais10'] <= 5){
                $wrk[$i]['kais10'] = '請求先' . $result['kais10'];
            }elseif($result['kais10'] == 90){
                $wrk[$i]['kais10'] = '会社情報';
            }

            $wrk[$i]['kais04'] = $result['kais04'];
            $wrk[$i]['kais05'] = $result['kais05'];
            $wrk[$i]['kais06'] = $result['kais06'];
            $wrk[$i]['kais07'] = $result['kais07'];
            $wrk[$i]['kais08'] = $result['kais08'];
            $wrk[$i]['kais09'] = $result['kais09'];

            $wrk[$i]['kais11'] = $result['kais11'];
            $wrk[$i]['kais12'] = $result['kais12'];
            $wrk[$i]['kais13'] = $result['kais13'];
            $wrk[$i]['kais14'] = $result['kais14'];
            $wrk[$i]['kais15'] = $result['kais15'];
            $wrk[$i]['kais16'] = $result['kais16'];
            $wrk[$i]['kais17'] = $result['kais17'];
            $wrk[$i]['kais18'] = $result['kais18'];
            $wrk[$i]['kais19'] = $result['kais19'];
            $wrk[$i]['kais20'] = $result['kais20'];
            $wrk[$i]['kais21'] = $result['kais21'];
            $wrk[$i]['kais22'] = $result['kais22'];
            $wrk[$i]['kais23'] = $result['kais23'];
            $wrk[$i]['kais24'] = $result['kais24'];
            $wrk[$i]['kais25'] = $result['kais25'];
            $wrk[$i]['kais26'] = $result['kais26'];
            $wrk[$i]['kais27'] = $result['kais27'];
            $wrk[$i]['kais28'] = $result['kais28'];
            $wrk[$i]['kais29'] = $result['kais29'];
            $wrk[$i]['kais30'] = $result['kais30'];
            $wrk[$i]['cday'] = $this->format($result['cday'],'date-');
            $wrk[$i]['ctim'] = $this->format($result['ctim'],'time:');
            $wrk[$i]['cid']  = $result['cid'];
            $wrk[$i]['uday'] = $this->format($result['uday'],'date-');
            $wrk[$i]['utim'] = $this->format($result['utim'],'time:');
            $wrk[$i]['uid']  = $result['uid'];
            $wrk[$i] = str_replace(null,'',$wrk[$i]); // null値除去
            $i++;
        }
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
            $sql = "select max(knsk02)+1 as ptcd from kensakuptka where knsk01=? group by knsk01;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $wrkcd = $result['ptcd'];
            }
            if($wrkcd == ''){$wrkcd = 1;}
            $par = array();
            $sql = "insert into kensakuptka (";
            for($i=1; $i <= 4; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "knsk".$a.",";
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
        $sql = "select * from kensakuptka where knsk01=? order by knsk02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $knsk02 = $result['knsk02'];
            $knsk03 = $result['knsk03'];
            $wrk[0]['html'] .= "<OPTION value='$knsk02'>$knsk03</OPTION>";
        }
        echo json_encode($wrk);
        exit();
    }
    
    // パターン変更
    function kpcomboSelectJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"knsk02":$('#kpcombo').val()};
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
        $sql = "select * from kensakuptka where knsk01=? and knsk02=?;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id'],$data['knsk02']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $knsk04 = $result['knsk04'];
        }
        $wrk[0]['str'] = $knsk04;
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
            var obj = {"knsk02":$("#kpcombo").val()};
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
        $sql = "delete from kensakuptka where (knsk01=? and knsk02=?);";
        $par = array($_SESSION['id'],$data['knsk02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }

    // 郵便番号から住所を取得(JS)
    function kais11udKeyupJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
        if(event.keyCode != 13){
            return false;
        }else if($('#kais11ud').val().length >= 7 && $('#kais11ud').val().length <= 8){
            var obj = {"zip":$('#kais11ud').val()};
            params = JSON.stringify(obj);
        }
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            $('#kais12ud').val(data[0]['ken_name']);
            $('#kais13ud').val(data[0]['city_name']);
            $('#kais14ud').val(data[0]['town_name']);
//            console.log(data);
__;
        $this->addEventListener('#kais11ud','keyup','getZipData','ajax');
    }
    function kais11cKeyupJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
        if(event.keyCode != 13){
            return false;
        }else if($('#kais11c').val().length >= 7 && $('#kais11c').val().length <= 8){
            var obj = {"zip":$('#kais11c').val()};
            params = JSON.stringify(obj);
        }
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            $('#kais12c').val(data[0]['ken_name']);
            $('#kais13c').val(data[0]['city_name']);
            $('#kais14c').val(data[0]['town_name']);
//            console.log(data);
__;
        $this->addEventListener('#kais11c','keyup','getZipData','ajax');
    }
    function getZipData($data){
        if(strlen($data['zip']) == 7){
            $data['zip'] = substr($data['zip'],0,3) . '-' . substr($data['zip'],3,4);
        }
        $sql = "select * from ad_address where zip = ? and delete_flg != 1 limit 1";
        $par = array($data['zip']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $res[0]['ken_name'] = $result['ken_name'];
            $res[0]['ken_furi'] = $result['ken_furi'];
            $res[0]['city_name'] = $result['city_name'];
            $res[0]['city_furi'] = $result['city_furi'];
            $res[0]['town_name'] = $result['town_name'];
            $res[0]['town_furi'] = $result['town_furi'];
            $town_memo = $result['town_memo']; // 同じ町域が複数レコードある場合、この項目で区別します。
            //１．town_nameが空欄となるとき、以下の内容で区別します。
            //“（該当なし）”　他に町域の記載がないときにはここに含まれる
            //“（直番地）”　　市区町村名のあとに番地が続く
            //“（全域）”　　　一市区町村に対し、一つの郵便番号のみ
            $block_name = $result['block_name'];
            $block_furi = $result['block_furi'];
        }
        echo json_encode($res);
        exit();
    }
    
}

// ** page info ** //
$p = new page();

$data['pr1'] = array('title' => '会社マスター保守'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
$data['pr3'] = array('active' => 'マスター'); // ナビメニュー

//var_dump($p);
loadResource($p,$data);