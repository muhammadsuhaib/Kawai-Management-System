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
    private $koumokusu = 20; // 表示するfield数(従業員マスターfield数 + joinテーブルから選択したfield数)
    
    // JS初期処理
    function initJs(){
        $this->clearJs();
        $this->js1 = <<<"__"

       // 検索条件初期値設定
        var json_data_stock_obj = {"jyouken1":"","maekakko1":"","koumoku1":"cal01","val1":"0","enzan1":">=","atokakko1":"","jyouken2":"","maekakko2":"","koumoku2":"","val2":"","enzan2":"","atokakko2":"","jyouken3":"","maekakko3":"","koumoku3":"","val3":"","enzan3":"","atokakko3":"","jyouken4":"","maekakko4":"","koumoku4":"","val4":"","enzan4":"","atokakko4":"","order1":"bum03","orderopt1":"ASC","order2":"","orderopt2":"","order3":"","orderopt3":"","order4":"","orderopt4":""};
        $('#json_data_stock').val(JSON.stringify(json_data_stock_obj));
       $('#searchbtn2').trigger('click');
       
       // ヒントの表示
       var hintObj = {
       "":"　",
       "cal01":"開始日（8桁 ex.20170101）",
       "cal02":"終了日（8桁 ex.20170101）",
       "cal03":"開始時間（4桁 ex.1800）",
       "cal04":"終了時間（4桁 ex.2210）",
       "cal05":"種別（kai:会議　gyo:行事　kyu:休日）",
       "cal06":"表示名（15文字以内）",
       "cal07":"部門CD",
       "cal08":"補足CD",
       "cal09":"詳細",
       "cal10":"表示色（red,blue,green,black,purple）",
       "cal11":"カレンダーID（unique）",
       "cal12":"",
       "cal13":"",
       "cal14":"",
       "cal15":"",
       "cal16":"",
       "cal17":"",
       "cal18":"",
       "cal19":"",
       "cal20":""
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
       // カレンダーロード
        $event = 'modalCallUd(event);';
        $this->calendarLoad('#calendar',$event);
//        echo 'calendarGet();';

        $this->addEventListener('','','','');
    }

    // aカレンダーイベント取得(JS)
    function calendarGetJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#calendar').fullCalendar('removeEvents'); // イベントクリア          
            var events = data['caldt'];
            $('#calendar').fullCalendar('addEventSource', events);
            $('#calendar').fullCalendar('refetchEvents');
            console.log(data['caldt']);
__;
        $this->addEventListener('','wait','calendarGet','ajax');
    }
    // aカレンダーイベント取得
    function calendarGet($data){
        $sql = 'select * from calendar;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrk[] = $result;
        }
        $res['caldt'] = $this->calendarFormat($wrk);
        echo json_encode($res);
        exit();
    }
    
    // s新規モーダル表示(JS)
    function createbtn1ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_c').remove();
            $('#modalParent1').append(data[0]["html"]);
            // 部門データ
			$(document).on('focus','#cal07c',function(){
            	$('.tsArea1').TapSuggest({
                	tsInputElement : '#cal07c',
                	tsArrayList : data[2],
                	tsRegExpAll : true
            	});
	   		});
            //            console.log(data);
            $('#modal_c').modal({backdrop:'static'});
__;
        $this->addEventListener('#createbtn1','click','modalCallCr','ajax');
    }
    // s新規モーダル表示(PHP)
    function modalCallCr($data){
        $modal = $this->readModalSource('modal_c');
		// 部門候補取得
		$today = date('Ymd');
        $sql = 'select * from bumon where bum01 <= ? and bum02 >= ?;';
		$par = array($today,$today);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$disp = '[' . $result['bum03'] . ']' . ' ' . $result['bum06'];
			$res[2][] = array($disp,$result['bum04'].' '.$result['bum05'].' '.$result['bum06'].' '.$result['bum07']);
		}
        $res[0]['html'] = implode("", $modal);
        echo json_encode($res);
        exit();
    }

    // a更新・削除モーダル表示(JS)
    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"id":event.id,"start":event.start._i};
            console.log(obj);
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_ud').remove();
            $('#modalParent1').append(data[0]["html"]);
            $('#cal05ud').val(data[1][5]);
            $('#cal10ud').val(data[1][10]);
            // 部門データ
			$(document).on('focus','#cal07ud',function(){
            	$('.tsArea1').TapSuggest({
                	tsInputElement : '#cal07ud',
                	tsArrayList : data[2],
                	tsRegExpAll : true
            	});
	   		});
            //            console.log(data);
            $('#modal_ud').modal({backdrop:'static'});
__;
        $this->addEventListener('','wait','modalCallUd','ajax');
    }
    // a更新・削除モーダル表示(PHP)
    function modalCallUd($data){
        $data['start'] = str_replace('-','',$data['start']);
        
        $modal = $this->readModalSource('modal_ud');
        $sql = 'select * from calendar left join bumon on bum03 = cal07 and bum01 <= ? and bum02 >= ? where cal11=?;';
        $par = array($data['start'],$data['start'],$data['id']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $modal['cal01ud'] = $this->format($result["cal01"],'date-');
            $modal['cal02ud'] = $this->format($result["cal02"],'date-');
            $modal['cal03ud'] = $this->format($result["cal03"],'time:');
            $modal['cal04ud'] = $this->format($result["cal04"],'time:');
            $res[1][5] = $result["cal05"]; // 種別
            $modal['cal06ud'] = $result["cal06"]; // 表示名
            $modal['cal07ud'] = '['.$result["cal07"].']'.$result["bum06"]; // 部門
            $modal['cal08ud'] = $result["cal08"]; // 補足CD
            $modal['cal09ud'] = $result["cal09"]; // 詳細
            $res[1][10] = $result["cal10"]; // 色
            $modal['cal11ud'] = $result["cal11"]; // ID
        }
		// 部門候補取得
		$today = date('Ymd');
        $sql = 'select * from bumon where bum01 <= ? and bum02 >= ?;';
		$par = array($today,$today);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$disp = '[' . $result['bum03'] . ']' . ' ' . $result['bum06'];
			$res[2][] = array($disp,$result['bum04'].' '.$result['bum05'].' '.$result['bum06'].' '.$result['bum07']);
		}
        $res[0]['html'] = implode("", $modal);
        echo json_encode($res);
        exit();
    }

    // s新規(JS)
    function createbtn2ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {
            'cal01':$('#cal01c').val(),'cal02':$('#cal02c').val(),
            'cal03':$('#cal03c').val(),'cal04':$('#cal04c').val(),
            'cal05':$('#cal05c').val(),'cal06':$('#cal06c').val(),
            'cal07':$('#cal07c').val(),'cal08':$('#cal08c').val(),
            'cal09':$('#cal09c').val(),'cal10':$('#cal10c').val(),
            'cal11':$('#cal11c').val(),'cal12':$('#cal12c').val(),
            'cal13':$('#cal13c').val(),'cal14':$('#cal14c').val(),
            'cal15':$('#cal15c').val(),'cal16':$('#cal16c').val(),
            'cal17':$('#cal17c').val(),'cal18':$('#cal18c').val(),
            'cal19':$('#cal19c').val(),'cal20':$('#cal20c').val(),
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
    // s新規(PHP)
    function createData($data){
        $this->err = '';
        // 受信データの加工
        $data['cal01'] = str_replace('-','',$data['cal01']);
        $data['cal02'] = str_replace('-','',$data['cal02']);
        $data['cal03'] = str_replace(':','',$data['cal03']);
        $data['cal04'] = str_replace(':','',$data['cal04']);
        $cal07w = explode(']',$data['cal07']);
        $data['cal07'] = str_replace('[','',$cal07w[0]); // 番号
        
        // エラーチェック
        if(!$this->err){$this->validate($data['cal01'] ,'date', array(8,8,true), $data['lab01']);} // 開始日
        if(!$this->err){$this->validate($data['cal02'] ,'date', array(8,8,true), $data['lab02']);} // 終了日
        if(!$this->err){$this->validate($data['cal03'] ,'str',  array(0,4,false), $data['lab03']);} // 開始時間
        if(!$this->err){$this->validate($data['cal04'] ,'str',  array(0,4,false), $data['lab04']);} // 終了時間
        if(!$this->err){$this->validate($data['cal05'] ,'str',  array(1,5,true), $data['lab05']);} // 種別
        if(!$this->err){$this->validate($data['cal06'] ,'str',  array(1,15,true), $data['lab06']);} // 表示名(15)
        if(!$this->err){$this->validate($data['cal07'] ,'str',  array(1,10,false), $data['lab07']);} // 部門CD
        if(!$this->err){$this->validate($data['cal08'] ,'str',  array(0,10,false), $data['lab08']);} // 補足CD
        if(!$this->err){$this->validate($data['cal09'] ,'str',  array(0,250,false),$data['lab09']);} // 備考
        if(!$this->err){$this->validate($data['cal10'] ,'str',  array(0,10,false), $data['lab10']);} // 表示色
//        if(!$this->err){$this->validate($data['cal11'] ,'str',  array(1,20,true),  $data['lab11']);} // uniqueID(autoinc)
/*        if(!$this->err){$this->validate($data['cal12'] ,'str',  array(2,2,true),  $data['lab12']);} // 
        if(!$this->err){$this->validate($data['cal13'] ,'str',  array(1,2,false), $data['lab13']);} // 
        if(!$this->err){$this->validate($data['cal14'] ,'str',  array(3,3,true),  $data['lab14']);} // 
        if(!$this->err){$this->validate($data['cal15'] ,'str',  array(1,2,true),  $data['lab15']);} // 
        if(!$this->err){$this->validate($data['cal16'] ,'str', array(4,4,true),  $data['lab16']);} // 
        if(!$this->err){$this->validate($data['cal17'] ,'str',  array(0,5,false), $data['lab17']);} // 
        if(!$this->err){$this->validate($data['cal18'] ,'str',  array(0,0,false), $data['lab18']);} // 
        if(!$this->err){$this->validate($data['cal19'] ,'str',  array(0,0,false), $data['lab19']);} // 
        if(!$this->err){$this->validate($data['cal20'] ,'str',  array(0,0,false), $data['lab20']);} // 
*/        
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
        if(!$this->err){
            $par = array();
            $sql = "insert into calendar (";
            for($i=1; $i <= 20; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "cal".$a.",";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for($i=1; $i <= 20; $i++){
                $sql .= "?,";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",?,?,?,?,?,?);";
            for($i=1; $i <= 20; $i++){
                $a = sprintf('%02d',$i);
                $par[] = $data["cal$a"];
            }
            array_push($par,$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
        }else{
            $wrk[0]['res'] = false;
        }
        echo json_encode($wrk);
        exit();
    }
    
    // a更新(JS)
    function upbtn1ClickJs(){
        $this->js1 = <<<"__"
            var obj = {
            'cal01':$('#cal01ud').val(),'cal02':$('#cal02ud').val(),
            'cal03':$('#cal03ud').val(),'cal04':$('#cal04ud').val(),
            'cal05':$('#cal05ud').val(),'cal06':$('#cal06ud').val(),
            'cal07':$('#cal07ud').val(),'cal08':$('#cal08ud').val(),
            'cal09':$('#cal09ud').val(),'cal10':$('#cal10ud').val(),
            'cal11':$('#cal11ud').val(),'cal12':$('#cal12ud').val(),
            'cal13':$('#cal13ud').val(),'cal14':$('#cal14ud').val(),
            'cal15':$('#cal15ud').val(),'cal16':$('#cal16ud').val(),
            'cal17':$('#cal17ud').val(),'cal18':$('#cal18ud').val(),
            'cal19':$('#cal19ud').val(),'cal20':$('#cal20ud').val(),
            'lab01':$('#lab01ud').text(),'lab02':$('#lab02ud').text(),
            'lab03':$('#lab03ud').text(),'lab04':$('#lab04ud').text(),
            'lab05':$('#lab05ud').text(),'lab06':$('#lab06ud').text(),
            'lab07':$('#lab07ud').text(),'lab08':$('#lab08ud').text(),
            'lab09':$('#lab09ud').text(),'lab10':$('#lab10ud').text(),
            'lab11':$('#lab11ud').text(),'lab12':$('#lab12ud').text(),
            'lab13':$('#lab13ud').text(),'lab14':$('#lab14ud').text(),
            'lab15':$('#lab15ud').text(),'lab16':$('#lab16ud').text(),
            'lab17':$('#lab17ud').text(),'lab18':$('#lab18ud').text(),
            'lab19':$('#lab19ud').text(),'lab20':$('#lab20ud').text()
            };
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#modal_ud').modal('hide');
                calendarGet();
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
    // a更新(PHP)
    function updateData($data){
        $this->err = '';
        // 受信データの加工
        $data['cal01'] = str_replace('-','',$data['cal01']);
        $data['cal02'] = str_replace('-','',$data['cal02']);
        $data['cal03'] = str_replace(':','',$data['cal03']);
        $data['cal04'] = str_replace(':','',$data['cal04']);
        $cal07w = explode(']',$data['cal07']);
        $data['cal07'] = str_replace('[','',$cal07w[0]); // 番号
        
        // エラーチェック
        if(!$this->err){$this->validate($data['cal01'] ,'date', array(8,8,true), $data['lab01']);} // 開始日
        if(!$this->err){$this->validate($data['cal02'] ,'date', array(8,8,true), $data['lab02']);} // 終了日
        if(!$this->err){$this->validate($data['cal03'] ,'str',  array(0,4,false), $data['lab03']);} // 開始時間
        if(!$this->err){$this->validate($data['cal04'] ,'str',  array(0,4,false), $data['lab04']);} // 終了時間
        if(!$this->err){$this->validate($data['cal05'] ,'str',  array(1,5,true), $data['lab05']);} // 種別
        if(!$this->err){$this->validate($data['cal06'] ,'str',  array(1,15,true), $data['lab06']);} // 表示名(15)
        if(!$this->err){$this->validate($data['cal07'] ,'str',  array(1,10,false), $data['lab07']);} // 部門CD
        if(!$this->err){$this->validate($data['cal08'] ,'str',  array(0,10,false), $data['lab08']);} // 補足CD
        if(!$this->err){$this->validate($data['cal09'] ,'str',  array(0,250,false),$data['lab09']);} // 備考
        if(!$this->err){$this->validate($data['cal10'] ,'str',  array(0,10,false), $data['lab10']);} // 表示色
        if(!$this->err){$this->validate($data['cal11'] ,'str',  array(1,20,true),  $data['lab11']);} // uniqueID(autoinc)
/*        if(!$this->err){$this->validate($data['cal12'] ,'str',  array(2,2,true),  $data['lab12']);} // 
        if(!$this->err){$this->validate($data['cal13'] ,'str',  array(1,2,false), $data['lab13']);} // 
        if(!$this->err){$this->validate($data['cal14'] ,'str',  array(3,3,true),  $data['lab14']);} // 
        if(!$this->err){$this->validate($data['cal15'] ,'str',  array(1,2,true),  $data['lab15']);} // 
        if(!$this->err){$this->validate($data['cal16'] ,'str', array(4,4,true),  $data['lab16']);} // 
        if(!$this->err){$this->validate($data['cal17'] ,'str',  array(0,5,false), $data['lab17']);} // 
        if(!$this->err){$this->validate($data['cal18'] ,'str',  array(0,0,false), $data['lab18']);} // 
        if(!$this->err){$this->validate($data['cal19'] ,'str',  array(0,0,false), $data['lab19']);} // 
        if(!$this->err){$this->validate($data['cal20'] ,'str',  array(0,0,false), $data['lab20']);} // 
*/        
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
        $sql = "update calendar set ";
        for($i=1; $i <= 20; $i++){
            $a = sprintf('%02d',$i);
            $sql .= "cal".$a."=?,";
        }
        $sql = substr($sql,0,-1);
        $sql .= ",uday=?,utim=?,uid=? where cal11=?;";
        for($i=1; $i <= 20; $i++){
            $a = sprintf('%02d',$i);
            $par[] = $data["cal$a"];
        }
        array_push($par,$today,$now,$_SESSION['id'],$data["cal11"]);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }

 
    // s新期間を追加(JS)
    function createbtn3ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {
            'cal01':$('#cal01ud').val(),'cal02':$('#cal02ud').val(),
            'cal03':$('#cal03ud').val(),'cal04':$('#cal04ud').val(),
            'cal05':$('#cal05ud').val(),'cal06':$('#cal06ud').val(),
            'cal07':$('#cal07ud').val(),'cal08':$('#cal08ud').val(),
            'cal09':$('#cal09ud').val(),'cal10':$('#cal10ud').val(),
            'cal11':$('#cal11ud').val(),'cal12':$('#cal12ud').val(),
            'cal13':$('#cal13ud').val(),'cal14':$('#cal14ud').val(),
            'cal15':$('#cal15ud').val(),'cal16':$('#cal16ud').val(),
            'cal17':$('#cal17ud').val(),'cal18':$('#cal18ud').val(),
            'cal19':$('#cal19ud').val(),'cal20':$('#cal20ud').val(),
            'lab01':$('#lab01ud').text(),'lab02':$('#lab02ud').text(),
            'lab03':$('#lab03ud').text(),'lab04':$('#lab04ud').text(),
            'lab05':$('#lab05ud').text(),'lab06':$('#lab06ud').text(),
            'lab07':$('#lab07ud').text(),'lab08':$('#lab08ud').text(),
            'lab09':$('#lab09ud').text(),'lab10':$('#lab10ud').text(),
            'lab11':$('#lab11ud').text(),'lab12':$('#lab12ud').text(),
            'lab13':$('#lab13ud').text(),'lab14':$('#lab14ud').text(),
            'lab15':$('#lab15ud').text(),'lab16':$('#lab16ud').text(),
            'lab17':$('#lab17ud').text(),'lab18':$('#lab18ud').text(),
            'lab19':$('#lab19ud').text(),'lab20':$('#lab20ud').text()
            };
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#modal_ud').modal('hide');
                calendarGet();
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
    // s新期間を追加(PHP)
    function createRangeData($data){
        $this->err = '';
        // 受信データの加工
        $data['cal11'] = '';
        $data['cal01'] = str_replace('-','',$data['cal01']);
        $data['cal02'] = str_replace('-','',$data['cal02']);
        $data['cal03'] = str_replace(':','',$data['cal03']);
        $data['cal04'] = str_replace(':','',$data['cal04']);
        $cal07w = explode(']',$data['cal07']);
        $data['cal07'] = str_replace('[','',$cal07w[0]); // 番号
        
        // エラーチェック
        if(!$this->err){$this->validate($data['cal01'] ,'date', array(8,8,true), $data['lab01']);} // 開始日
        if(!$this->err){$this->validate($data['cal02'] ,'date', array(8,8,true), $data['lab02']);} // 終了日
        if(!$this->err){$this->validate($data['cal03'] ,'str',  array(0,4,false), $data['lab03']);} // 開始時間
        if(!$this->err){$this->validate($data['cal04'] ,'str',  array(0,4,false), $data['lab04']);} // 終了時間
        if(!$this->err){$this->validate($data['cal05'] ,'str',  array(1,5,true), $data['lab05']);} // 種別
        if(!$this->err){$this->validate($data['cal06'] ,'str',  array(1,15,true), $data['lab06']);} // 表示名(15)
        if(!$this->err){$this->validate($data['cal07'] ,'str',  array(1,10,false), $data['lab07']);} // 部門CD
        if(!$this->err){$this->validate($data['cal08'] ,'str',  array(0,10,false), $data['lab08']);} // 補足CD
        if(!$this->err){$this->validate($data['cal09'] ,'str',  array(0,250,false),$data['lab09']);} // 備考
        if(!$this->err){$this->validate($data['cal10'] ,'str',  array(0,10,false), $data['lab10']);} // 表示色
//        if(!$this->err){$this->validate($data['cal11'] ,'str',  array(1,20,true),  $data['lab11']);} // uniqueID(autoinc)
/*        if(!$this->err){$this->validate($data['cal12'] ,'str',  array(2,2,true),  $data['lab12']);} // 
        if(!$this->err){$this->validate($data['cal13'] ,'str',  array(1,2,false), $data['lab13']);} // 
        if(!$this->err){$this->validate($data['cal14'] ,'str',  array(3,3,true),  $data['lab14']);} // 
        if(!$this->err){$this->validate($data['cal15'] ,'str',  array(1,2,true),  $data['lab15']);} // 
        if(!$this->err){$this->validate($data['cal16'] ,'str', array(4,4,true),  $data['lab16']);} // 
        if(!$this->err){$this->validate($data['cal17'] ,'str',  array(0,5,false), $data['lab17']);} // 
        if(!$this->err){$this->validate($data['cal18'] ,'str',  array(0,0,false), $data['lab18']);} // 
        if(!$this->err){$this->validate($data['cal19'] ,'str',  array(0,0,false), $data['lab19']);} // 
        if(!$this->err){$this->validate($data['cal20'] ,'str',  array(0,0,false), $data['lab20']);} // 
*/        
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
        $st = '';
        if($st == ''){
            $par = array();
            $sql = "insert into calendar (";
            for($i=1; $i <= 20; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "cal".$a.",";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for($i=1; $i <= 20; $i++){
                $sql .= "?,";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",?,?,?,?,?,?);";
            for($i=1; $i <= 20; $i++){
                $a = sprintf('%02d',$i);
                $par[] = $data["cal$a"];
            }
            array_push($par,$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
        }else{
            $wrk[0]['res'] = false;
        }
        echo json_encode($wrk);
        exit();
    }
    
    // s削除準備(JS)
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
    // s
    function delmodal($data){
		$modal = $this->readModalSource('modal_d');
        $res[0]['html'] = $modal;
        echo json_encode($res);
        exit();
    }

    // s削除(JS)
    function delbtn2ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {'cal11':$('#cal11ud').val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            if(data[0]['res']){
                $('#modal_d').modal('hide');
                $('#modal_ud').modal('hide');
                calendarGet();
            }else{
                $('[name=button2]').prop("disabled",false);
                $('#modal_n').remove();
                $('#modalParent3').append(data[0]["html"]);
                $('#modal_n').modal({backdrop:'static'});
            }
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#delbtn2','click','deleteData','ajax');
    }
    // s削除(PHP)
    function deleteData($data){
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "delete from calendar where cal11=?;";
        $par = array($data['cal11']);
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
        $sql = "select * from kensakupt where kns01=? and kns05='calendar' order by kns02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns02 = $result['kns02'];
            $kns03 = $result['kns03'];
            $modal['combo0'] .= "<OPTION value='$kns02'>$kns03</OPTION>";
        }
        $wrk = array(''=>'未選択','cal01'=>'開始日','cal02'=>'終了日','cal03'=>'開始時間','cal04'=>'終了時間','cal05'=>'種別CD','cal06'=>'表示名','cal07'=>'部門CD','cal08'=>'補足CD','cal09'=>'詳細','cal10'=>'表示色','cal11'=>'ID','cal12'=>'','cal13'=>'','cal14'=>'','cal15'=>'','cal16'=>'','cal17'=>'','cal18'=>'','cal19'=>'','cal20'=>'');

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
            var data = JSON.parse(json_data||"null");
            $('#calendar').fullCalendar('removeEvents'); // イベントクリア          
            var events = data['caldt'];
            $('#calendar').fullCalendar('addEventSource', events);
            $('#calendar').fullCalendar('refetchEvents');
            console.log(data);
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
        
        $sql = "select * from calendar where $sqlwhere $orderby;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrk[] = $result;
        }
        $res['caldt'] = $this->calendarFormat($wrk);
        $res['sql'] = $sql;
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
            $sql = "select max(kns02)+1 as ptcd from kensakupt where kns01=? and kns05='calendar' group by kns01;";
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
            array_push($par,$_SESSION['id'],$wrkcd,$data["ptnm"],$data["ptdef"],'calendar',$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
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
        $sql = "select * from kensakupt where kns01=? and kns05='calendar' order by kns02 DESC;";
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
    
    // パターン変更
    function kpcomboSelectJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"kns02":$('#kpcombo').val()};
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
        $sql = "select * from kensakupt where kns01=? and kns02=? and kns05='calendar';";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id'],$data['kns02']);
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
        $sql = "delete from kensakupt where (kns01=? and kns02=?) and kns05='calendar';";
        $par = array($_SESSION['id'],$data['kns02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }

    // カレンダー用CSS出力
    function calendarCss(){
        echo <<<"__"
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
        .fc-sun, .fc-sat {
            color: #FF0000;
        }
__;
    }
}

// ** page info ** //
$p = new page();

$data['pr1'] = array('title' => 'カレンダー登録'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
$data['pr3'] = array('active' => 'マスター'); // ナビメニュー

//var_dump($p);
loadResource($p,$data);