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
        $ym = date('Ym');
        $this->js1 = <<<"__"
        // テンプレサンプルデータ削除
        $("#tablebody tr").remove();
        $("#tablebody td").remove();

       // 検索条件初期値設定
        var json_data_stock_obj = {"jyouken1":"","maekakko1":"","koumoku1":"ym","val1":"$ym","enzan1":"=","atokakko1":"","jyouken2":"and","maekakko2":"","koumoku2":"bum22","val2":"ukeoi","enzan2":"=","atokakko2":"","jyouken3":"","maekakko3":"","koumoku3":"","val3":"","enzan3":"","atokakko3":"","jyouken4":"","maekakko4":"","koumoku4":"","val4":"","enzan4":"","atokakko4":"","order1":"bum03","orderopt1":"ASC","order2":"","orderopt2":"","order3":"","orderopt3":"","order4":"","orderopt4":""};
        $('#json_data_stock').val(JSON.stringify(json_data_stock_obj));

            $("#tabu1").tabulator({
                height:400,
                fitColumns:true,
                tooltipsHeader:true,
                columns:[
                    {width:80, title:"CD", field:"btn", align:"center", frozen:true, formatter:'html',tooltip:false},
                    {width:150, title:"名称", field:"name", frozen:true, align:"left"},
                    {width:100, title:"売上計画", align:"right", field:"keikaku", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"売上合計", align:"right", field:"goukei", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"月発生分", align:"right", field:"0", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"1日", field:"1", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"2日", field:"2", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"3日", field:"3", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"4日", field:"4", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"5日", field:"5", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"6日", field:"6", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"7日", field:"7", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"8日", field:"8", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"9日", field:"9", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"10日", field:"10", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"11日", field:"11", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"12日", field:"12", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"13日", field:"13", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"14日", field:"14", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"15日", field:"15", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"16日", field:"16", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"17日", field:"17", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"18日", field:"18", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"19日", field:"19", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"20日", field:"20", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"20日", field:"20", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"21日", field:"21", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"22日", field:"22", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"23日", field:"23", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"24日", field:"24", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"25日", field:"25", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"26日", field:"26", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"27日", field:"27", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"28日", field:"28", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"29日", field:"29", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"30日", field:"30", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"31日", field:"31", align:"right", formatter:'money', formatterParams:{precision:0}}
                ],
                tooltips:function(cell){
                    return cell.getValue();
                },
                rowFormatter:function(row){
                    // getElement:エレメント自体を取得（データ以外）　getData:データを取得　
//                    var data = row.getData();
//                    if(data.age >= 18){
//                        row.getElement().addClass("success");
//                    }
                },
//                rowClick:function(e, row){
//                    alert("Row " + row.getData().id + " Clicked!!!!");
//                },
            });

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
       
       $(document).on('click','#syushibtn',function(){
            var txt = $('#syushigkud').val().replace(/\\r\\n|\\r/g, "\\n");
            var lines = txt.split('\\n');
            var array = [];
            for(var i = 0; i < lines.length; i++){
                var wrk = lines[i].split(',');
                array = array.concat(wrk);
            }
            $('#syushi00ud').val(array[0]);
            $('#syushi00bud').val(array[1]);
            $('#syushi01ud').val(array[2]);
            $('#syushi01bud').val(array[3]);
            $('#syushi02ud').val(array[4]);
            $('#syushi02bud').val(array[5]);
            $('#syushi03ud').val(array[6]);
            $('#syushi03bud').val(array[7]);
            $('#syushi04ud').val(array[8]);
            $('#syushi04bud').val(array[9]);
            $('#syushi05ud').val(array[10]);
            $('#syushi05bud').val(array[11]);
            $('#syushi06ud').val(array[12]);
            $('#syushi06bud').val(array[13]);
            $('#syushi07ud').val(array[14]);
            $('#syushi07bud').val(array[15]);
            $('#syushi08ud').val(array[16]);
            $('#syushi08bud').val(array[17]);
            $('#syushi09ud').val(array[18]);
            $('#syushi09bud').val(array[19]);
            $('#syushi10ud').val(array[20]);
            $('#syushi10bud').val(array[21]);
            $('#syushi11ud').val(array[22]);
            $('#syushi11bud').val(array[23]);
            $('#syushi12ud').val(array[24]);
            $('#syushi12bud').val(array[25]);
            $('#syushi13ud').val(array[26]);
            $('#syushi13bud').val(array[27]);
            $('#syushi14ud').val(array[28]);
            $('#syushi14bud').val(array[29]);
            $('#syushi15ud').val(array[30]);
            $('#syushi15bud').val(array[31]);
            $('#syushi16ud').val(array[32]);
            $('#syushi16bud').val(array[33]);
            $('#syushi17ud').val(array[34]);
            $('#syushi17bud').val(array[35]);
            $('#syushi18ud').val(array[36]);
            $('#syushi18bud').val(array[37]);
            $('#syushi19ud').val(array[38]);
            $('#syushi19bud').val(array[39]);
            $('#syushi20ud').val(array[40]);
            $('#syushi20bud').val(array[41]);
            $('#syushi21ud').val(array[42]);
            $('#syushi21bud').val(array[43]);
            $('#syushi22ud').val(array[44]);
            $('#syushi22bud').val(array[45]);
            $('#syushi23ud').val(array[46]);
            $('#syushi23bud').val(array[47]);
            $('#syushi24ud').val(array[48]);
            $('#syushi24bud').val(array[49]);
            $('#syushi25ud').val(array[50]);
            $('#syushi25bud').val(array[51]);
            $('#syushi26ud').val(array[52]);
            $('#syushi26bud').val(array[53]);
            $('#syushi27ud').val(array[54]);
            $('#syushi27bud').val(array[55]);
            $('#syushi28ud').val(array[56]);
            $('#syushi28bud').val(array[57]);
            $('#syushi29ud').val(array[58]);
            $('#syushi29bud').val(array[59]);
            $('#syushi30ud').val(array[60]);
            $('#syushi30bud').val(array[61]);
            $('#syushi31ud').val(array[62]);
            $('#syushi31bud').val(array[63]);
        });
__;
        $this->addEventListener('','','','');
    }
    
    // 更新・削除モーダル表示(JS)
    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"cd":event.value,"ym":event.getAttribute('ym')};
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
        $modal = $this->readModalSource('modal_ud');
        $modal["ym_ud"] = $data['ym'];
        // 日付・曜日表示
        $ymdst = $data['ym'].'00';
        $ymden = $data['ym'].'31';
        $y = substr($ymdst,0,4);
        $m = substr($ymdst,4,2);
        $d = '01';
        $date = $y . '-' . $m .  '-' . $d;
        $datetime = new DateTime($date);
        $week = array("日", "月", "火", "水", "木", "金", "土");
        $nissu = $datetime->format('t');
        // 月発生分
        $modal["lab00ud"] = '月発生分';
        $modal["lab00bud"] = '備考';
        for($i=1; $i<=31; $i++){
            $youbi = $week[$datetime->format('w')];
            $a = sprintf('%02d',$i);
            if($i <= $nissu){
                $modal["lab{$a}ud"] = $datetime->format("m/d（{$youbi}）");
                $modal["lab{$a}bud"] = '備考';
            }else{
                $modal["lab{$a}ud"] = '-';
                $modal["lab{$a}bud"] = '-';
            }
            $datetime->modify('+1 days');
        }
        // 収支レコード取得
        $sql = 'select * from syushi where syu02>=? and syu02<=? and syu03=? and syu01="通常";';
        $par = array($ymdst,$ymden,$data['cd']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $day = substr($result["syu02"],6,2);
            $modal["syushi{$day}ud"]  = $result["syu04"];
            $modal["syushi{$day}bud"] = $result["syu12"];
        }
        $modal['cd_ud'] = $data['cd']; // 部門CD
        if($day == ''){
            $modal['syushigkud'] = '';
            for($i=0;$i<=31;$i++){
                $day = sprintf('%02d',$i);
                $modal["syushi{$day}ud"]  = $result["syu04"];
                $modal["syushi{$day}bud"] = $result["syu12"];
            }
        }else{
            $modal['syushigkud'] = $modal['syushi00ud'].",".$modal['syushi00bud']."\r\n".$modal['syushi01ud'].",".$modal['syushi01bud']."\r\n".$modal['syushi02ud'].",".$modal['syushi02bud']."\r\n".$modal['syushi03ud'].",".$modal['syushi03bud']."\r\n".$modal['syushi04ud'].",".$modal['syushi04bud']."\r\n".$modal['syushi05ud'].",".$modal['syushi05bud']."\r\n".$modal['syushi06ud'].",".$modal['syushi06bud']."\r\n".$modal['syushi07ud'].",".$modal['syushi07bud']."\r\n".$modal['syushi08ud'].",".$modal['syushi08bud']."\r\n".$modal['syushi09ud'].",".$modal['syushi09bud']."\r\n".$modal['syushi10ud'].",".$modal['syushi10bud']."\r\n".$modal['syushi11ud'].",".$modal['syushi11bud']."\r\n".$modal['syushi12ud'].",".$modal['syushi12bud']."\r\n".$modal['syushi13ud'].",".$modal['syushi13bud']."\r\n".$modal['syushi14ud'].",".$modal['syushi14bud']."\r\n".$modal['syushi15ud'].",".$modal['syushi15bud']."\r\n".$modal['syushi16ud'].",".$modal['syushi16bud']."\r\n".$modal['syushi17ud'].",".$modal['syushi17bud']."\r\n".$modal['syushi18ud'].",".$modal['syushi18bud']."\r\n".$modal['syushi19ud'].",".$modal['syushi19bud']."\r\n".$modal['syushi20ud'].",".$modal['syushi20bud']."\r\n".$modal['syushi21ud'].",".$modal['syushi21bud']."\r\n".$modal['syushi22ud'].",".$modal['syushi22bud']."\r\n".$modal['syushi23ud'].",".$modal['syushi23bud']."\r\n".$modal['syushi24ud'].",".$modal['syushi24bud']."\r\n".$modal['syushi25ud'].",".$modal['syushi25bud']."\r\n".$modal['syushi26ud'].",".$modal['syushi26bud']."\r\n".$modal['syushi27ud'].",".$modal['syushi27bud']."\r\n".$modal['syushi28ud'].",".$modal['syushi28bud']."\r\n".$modal['syushi29ud'].",".$modal['syushi29bud']."\r\n".$modal['syushi30ud'].",".$modal['syushi30bud']."\r\n".$modal['syushi31ud'].",".$modal['syushi31bud']."\r\n";
        }
        $res[0]['html'] = implode("", $modal);
        echo json_encode($res);
        exit();
    }

    // 更新+新規(JS)
    function upbtn1ClickJs(){
        $this->js1 = <<<"__"
            var obj = {
            'syushi01':$('#syushi01ud').val(),'syushi02':$('#syushi02ud').val(),
            'syushi03':$('#syushi03ud').val(),'syushi04':$('#syushi04ud').val(),
            'syushi05':$('#syushi05ud').val(),'syushi06':$('#syushi06ud').val(),
            'syushi07':$('#syushi07ud').val(),'syushi08':$('#syushi08ud').val(),
            'syushi09':$('#syushi09ud').val(),'syushi10':$('#syushi10ud').val(),
            'syushi11':$('#syushi11ud').val(),'syushi12':$('#syushi12ud').val(),
            'syushi13':$('#syushi13ud').val(),'syushi14':$('#syushi14ud').val(),
            'syushi15':$('#syushi15ud').val(),'syushi16':$('#syushi16ud').val(),
            'syushi17':$('#syushi17ud').val(),'syushi18':$('#syushi18ud').val(),
            'syushi19':$('#syushi19ud').val(),'syushi20':$('#syushi20ud').val(),
            'syushi21':$('#syushi21ud').val(),'syushi22':$('#syushi22ud').val(),
            'syushi23':$('#syushi23ud').val(),'syushi24':$('#syushi24ud').val(),
            'syushi25':$('#syushi25ud').val(),'syushi26':$('#syushi26ud').val(),
            'syushi27':$('#syushi27ud').val(),'syushi28':$('#syushi28ud').val(),
            'syushi29':$('#syushi29ud').val(),'syushi30':$('#syushi30ud').val(),
            'syushi31':$('#syushi31ud').val(),'syushi00':$('#syushi00ud').val(),
            'cd_ud':$('#cd_ud').val(),'ym_ud':$('#ym_ud').val(),
            'lab01':$('#lab01ud').val(),'lab02':$('#lab02ud').val(),
            'lab03':$('#lab03ud').val(),'lab04':$('#lab04ud').val(),
            'lab05':$('#lab05ud').val(),'lab06':$('#lab06ud').val(),
            'lab07':$('#lab07ud').val(),'lab08':$('#lab08ud').val(),
            'lab09':$('#lab09ud').val(),'lab10':$('#lab10ud').val(),
            'lab11':$('#lab11ud').val(),'lab12':$('#lab12ud').val(),
            'lab13':$('#lab13ud').val(),'lab14':$('#lab14ud').val(),
            'lab15':$('#lab15ud').val(),'lab16':$('#lab16ud').val(),
            'lab17':$('#lab17ud').val(),'lab18':$('#lab18ud').val(),
            'lab19':$('#lab19ud').val(),'lab20':$('#lab20ud').val(),
            'lab21':$('#lab21ud').val(),'lab22':$('#lab22ud').val(),
            'lab23':$('#lab23ud').val(),'lab24':$('#lab24ud').val(),
            'lab25':$('#lab25ud').val(),'lab26':$('#lab26ud').val(),
            'lab27':$('#lab27ud').val(),'lab28':$('#lab28ud').val(),
            'lab29':$('#lab29ud').val(),'lab30':$('#lab30ud').val(),
            'lab31':$('#lab31ud').val(),'lab00':$('#lab00ud').val(),
            'lab_cd':$('#lab_cd_ud').val(),'lab_ym':$('#lab_ym_ud').val()
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
        $this->addEventListener('#upbtn1','click','updateData','ajax');
    }
    // 更新+新規(PHP)
    function updateData($data){
        $this->err = '';
        // エラーチェック
        if(!$this->err){$this->validate($data['cd_ud']   ,'str', array(1,20,true), $data['lab_cd']);} // cd
        if(!$this->err){$this->validate($data['ym_ud'] ,'int', array(6,6,true), $data['lab_ym']);} // 対象年月
        if(!$this->err){$this->validate($data['syushi00'] ,'int',  array(0,9,false), $data['lab00']);} // 0日(月売上)
        if(!$this->err){$this->validate($data['syushi01'] ,'int',  array(0,9,false), $data['lab01']);} // 1日
        if(!$this->err){$this->validate($data['syushi02'] ,'int',  array(0,9,false), $data['lab02']);} // 2日
        if(!$this->err){$this->validate($data['syushi03'] ,'int',  array(0,9,false), $data['lab03']);} // 3日
        if(!$this->err){$this->validate($data['syushi04'] ,'int',  array(0,9,false), $data['lab04']);} // 4日
        if(!$this->err){$this->validate($data['syushi05'] ,'int',  array(0,9,false), $data['lab05']);} // 5日
        if(!$this->err){$this->validate($data['syushi06'] ,'int',  array(0,9,false), $data['lab06']);} // 6日
        if(!$this->err){$this->validate($data['syushi07'] ,'int',  array(0,9,false), $data['lab07']);} // 7日
        if(!$this->err){$this->validate($data['syushi08'] ,'int',  array(0,9,false), $data['lab08']);} // 8日
        if(!$this->err){$this->validate($data['syushi09'] ,'int',  array(0,9,false), $data['lab09']);} // 9日
        if(!$this->err){$this->validate($data['syushi10'] ,'int',  array(0,9,false), $data['lab10']);} // 10日
        if(!$this->err){$this->validate($data['syushi11'] ,'int',  array(0,9,false), $data['lab11']);} // 11日
        if(!$this->err){$this->validate($data['syushi12'] ,'int',  array(0,9,false), $data['lab12']);} // 12日
        if(!$this->err){$this->validate($data['syushi13'] ,'int',  array(0,9,false), $data['lab13']);} // 13日
        if(!$this->err){$this->validate($data['syushi14'] ,'int',  array(0,9,false), $data['lab14']);} // 14日
        if(!$this->err){$this->validate($data['syushi15'] ,'int',  array(0,9,false), $data['lab15']);} // 15日
        if(!$this->err){$this->validate($data['syushi16'] ,'int',  array(0,9,false), $data['lab16']);} // 16日
        if(!$this->err){$this->validate($data['syushi17'] ,'int',  array(0,9,false), $data['lab17']);} // 17日
        if(!$this->err){$this->validate($data['syushi18'] ,'int',  array(0,9,false), $data['lab18']);} // 18日
        if(!$this->err){$this->validate($data['syushi19'] ,'int',  array(0,9,false), $data['lab19']);} // 19日
        if(!$this->err){$this->validate($data['syushi20'] ,'int',  array(0,9,false), $data['lab20']);} // 20日
        if(!$this->err){$this->validate($data['syushi21'] ,'int',  array(0,9,false), $data['lab21']);} // 21日
        if(!$this->err){$this->validate($data['syushi22'] ,'int',  array(0,9,false), $data['lab22']);} // 22日
        if(!$this->err){$this->validate($data['syushi23'] ,'int',  array(0,9,false), $data['lab23']);} // 23日
        if(!$this->err){$this->validate($data['syushi24'] ,'int',  array(0,9,false), $data['lab24']);} // 24日
        if(!$this->err){$this->validate($data['syushi25'] ,'int',  array(0,9,false), $data['lab25']);} // 25日
        if(!$this->err){$this->validate($data['syushi26'] ,'int',  array(0,9,false), $data['lab26']);} // 26日
        if(!$this->err){$this->validate($data['syushi27'] ,'int',  array(0,9,false), $data['lab27']);} // 27日
        if(!$this->err){$this->validate($data['syushi28'] ,'int',  array(0,9,false), $data['lab28']);} // 28日
        if(!$this->err){$this->validate($data['syushi29'] ,'int',  array(0,9,false), $data['lab29']);} // 29日
        if(!$this->err){$this->validate($data['syushi30'] ,'int',  array(0,9,false), $data['lab30']);} // 30日
        if(!$this->err){$this->validate($data['syushi31'] ,'int',  array(0,9,false), $data['lab31']);} // 31日
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        // 金額・収支配列生成
        for($i=0; $i <= 31; $i++){
            $a = sprintf('%02d',$i);
            $ymd[$i] = $data['ym_ud'].$a;
            $kin[$i] = $data['syushi'.$a];
        }
        $today = date('Ymd');
        $now = date('Hi');
        $par = array();
        $sql = "insert into syushi (syu01,syu02,syu03,syu04,syu05,syu06,syu07,syu08,syu09,syu10,syu11,syu12,syu13,syu14,syu15,syu16,syu17,syu18,syu19,syu20,cday,ctim,cid,uday,utim,uid) values ";
        for($i=0; $i <= 31; $i++){
            $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
        }
        $sql = substr($sql,0,-1);
        $sql .= 'ON DUPLICATE KEY UPDATE syu04 = VALUES(syu04),uday = VALUES(uday),utim = VALUES(utim),uid = VALUES(uid)';
        for($i=0; $i <= 31; $i++){
            array_push($par,'通常',$ymd[$i],$data['cd_ud'],$kin[$i],'','','','','','','','','','','','','','','','',$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
        }
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        $wrk[0]['sql'] = $sql;
        $wrk[0]['par'] = $par;
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

        $wrk = array(''=>'未選択','bum01'=>'適用開始日','bum02'=>'適用終了日','bum03'=>'部門No.','bum04'=>'部門名','bum05'=>'部門名カナ','bum06'=>'部門名（正式）','bum07'=>'部門名カナ（正式）','bum08'=>'業務内容','bum09'=>'','bum10'=>'','bum11'=>'勤怠パターン','bum12'=>'残業計算タイプ','bum13'=>'カレンダーCD','bum14'=>'週の起点','bum15'=>'時間集計単位','bum16'=>'有休時間数','bum17'=>'請求単価','bum18'=>'','bum19'=>'','bum20'=>'','bum21'=>'','bum22'=>'派遣・請負区分','ym'=>'表示年月');

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
            // 条件１は式固定
            $this->js1 .= "'jyouken1':$('#jyouken1').val(),";
            $this->js1 .= "'maekakko1':'',";
            $this->js1 .= "'koumoku1':'ym',";
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
            console.log(data[1]);
            $("#tabu1").tabulator("setData", data[0]);
            $("#tabu1").tabulator("redraw");
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
                $sqlwhere .= $data["jyouken$i"] . ' ';
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
        $ym = $data['val1'];
        $ymdst = $ym.'00';
        $ymden = $ym.'31';
        $busql = "bum01<='$ymdst' and bum02>='$ymdst'";
        // 会社
        $par = array($ym);
        $sql = "select * from bumon left join ukeikaku on ukei01='bum' and ukei02=bum03 and ukei03=? left join syushi on bum03=syu03 and syu02>= $ymdst and syu02 <= $ymden where {$busql} $sqlwhere order by cast(bum03 as SIGNED),ukei03;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $bucd = $result['bum03']; // 部門CD
            $month = substr($ym2,4,2);
            $wrk[$bucd]['btn'] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$result['bum03'].'" ym="'.$ym.'">'.$result['bum03'].'</button>'; // 選択ボタン
            $wrk[$bucd]['name'] = $result['bum04']; // 名

            // 売上計画
            $wrk[$bucd]['keikaku'] = $result['ukei04'];
//            $wrk[$bucd]['yosoku'] = '予測';
            $hi = substr($result['syu02'],6,2)+0; // 日付に配置
            if($result['syu02'] == ''){
                for($i=0;$i<=31;$i++){
                    $wrk[$bucd][$i] = '';
                }
            }else{
                // 売上実績
                $wrk[$bucd][$hi] = $result['syu04'];
                $wrk[$bucd]['goukei'] += $wrk[$bucd][$hi];
            }
        }
        // 整形
        foreach($wrk as $key => $ary){
            $ary['bu'] = $key;
            $tabuwrk[] = $ary;
        }
        unset($ary);
        $i = 1;
        foreach($tabuwrk as &$ary){
            $ary['recid'] = $i;
            $i++;
        }
        unset($ary);
        
        $res[0] = $tabuwrk;
        $res[1]['sql'] = $sql;
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

$data['pr1'] = array('title' => '売上実績【請負】'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
$data['pr3'] = array('active' => '売上・請求'); // ナビメニュー

loadResource($p,$data);