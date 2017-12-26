<?php
// ** 日次収支報告 ** //

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');

// ★ POST GET
class page extends core {

    // 初期処理
    function initJs(){
        $this->clearJs();
        $this->js1 = <<<"EOT"
        $("#tablebody tr").remove();
        $("#tablebody td").remove();
EOT;
        $this->addEventListener('','','initJs','normal');
    }

    // nengetsu 初期処理 コンボボックスに年月挿入
    function nengetsuJs(){
        $this->clearJs();
        $this->js2 = <<<"EOT"
            var data = JSON.parse(json_data);
            $("#nengetsu > option").remove();
            for(var i = 0; i < data.length; i++){
                $("#nengetsu").append($("<option>").html(data[i]["disp"]).val(data[i]["val"]));
            }
EOT;
     $this->addEventListener('','','nengetsu','ajax');
    }
    // 年月取得（過去1年間）
    function nengetsu($data){
        $yyyymm = date('Ym') + 1;
        for($i = 0; $i < 13; $i++){
            $yyyymm = $yyyymm - 1;
            // 0月になったら年を-1年
            if(substr($yyyymm,4,2) == '00'){
                $yyyymm = $yyyymm - 100 + 12;
            }
            $wrk[$i]['disp'] = substr($yyyymm,0,4).'年'.substr($yyyymm,4,2).'月';
            $wrk[$i]['val'] = $yyyymm;
        }
        echo json_encode($wrk);
    }    

    // bumon 初期処理 コンボボックスに部門挿入
    function bumonJs(){
        $this->clearJs();
        $this->js2 = <<<"EOT"
            var data = JSON.parse(json_data);
            $("#bumon > option").remove();
            $("#bumon").append($("<option>").html('部門を選択').val(""));
            for(var i = 0; i < data.length; i++){
                $("#bumon").append($("<option>").html(data[i]["disp"]).val(data[i]["val"]));
            }
EOT;
    $this->addEventListener('','','bumon','ajax');
    }
    // 収支部門取得
    function bumon($data){
        $buary = array('1.CSB(請負)','2.Hライン(請負)','3.銅管加工(請負)','4.資材','5.派遣','6.バリア(請負)');
        for($i = 0; $i < count($buary); $i++){
            $wrk[$i]['disp'] = $buary[$i];
            $wrk[$i]['val'] = $i+1;
        }
        echo json_encode($wrk);
    }
    
    // nengetsu,bumon 変更時 データ取得
    function comboChangeJs(){
        $this->clearJs();
        $this->js1 = <<<"EOT"
            if($('#nengetsu').val() == '' || $('#bumon').val() == ''){
                return;
            }else{
                var obj = {"nengetsu":$('#nengetsu').val(),"bumon":$('#bumon').val()};
                params = JSON.stringify(obj);
            }
EOT;
        $this->js2 = <<<"EOT"
            var data = JSON.parse(json_data);
            $("#tablebody tr").remove();
            $("#tablebody td").remove();
            var str = '';
            for (var i = 0; i < data[0]['len']; i++){
                if(data[i]['uriagew'] == null){data[i]['uriagew'] = '-';}
                if(data[i]['shiharaiw'] == null){data[i]['shiharaiw'] = '-';}
                if(data[i]['keihiw'] == null){data[i]['keihiw'] = '-';}
                if(data[i]['arariw'] == null){data[i]['arariw'] = '-';}
                if(data[i]['araripw'] == null){data[i]['araripw'] = '-';}
                if(data[i]['riekiw'] == null){data[i]['riekiw'] = '-';}
                if(data[i]['riekipw'] == null){data[i]['riekipw'] = '-';}
                if(data[i]['shiharai01w'] == null){data[i]['shiharai01w'] = '-';}
                if(data[i]['shiharai02w'] == null){data[i]['shiharai02w'] = '-';}
                if(data[i]['shiharai03w'] == null){data[i]['shiharai03w'] = '-';}
                if(data[i]['shiharai04w'] == null){data[i]['shiharai04w'] = '-';}
                if(i == 1){
                str = '<tr>'
                str += '<td class="dispo-tdky2"><div class="dispo-bold">' + data[i]['date'] + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['uriagew']) + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['shiharaiw']) + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['arariw']) + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['araripw']) + '%</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['keihiw']) + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['riekiw']) + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['riekipw']) + '%</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['shiharai01w']) + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['shiharai02w']) + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['shiharai03w']) + '</div></td>';
                str += '<td class="dispo-tdky"><div>' + numberFormat(data[i]['shiharai04w']) + '</div></td>';
                str += '</tr>'
                }else{
                str = '<tr>'
                str += '<td><div class="dispo-bold">' + data[i]['date'] + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['uriagew']) + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['shiharaiw']) + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['arariw']) + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['araripw']) + '%</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['keihiw']) + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['riekiw']) + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['riekipw']) + '%</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['shiharai01w']) + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['shiharai02w']) + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['shiharai03w']) + '</div></td>';
                str += '<td class="dispo-alright"><div>' + numberFormat(data[i]['shiharai04w']) + '</div></td>';
                str += '</tr>'
                }
                $("#tablebody").append(str);
                FixedMidashi.remove();
                FixedMidashi.create();
            }
EOT;
    $this->addEventListener('#nengetsu','change','comboChange','ajax');
    $this->addEventListener('#bumon','change','comboChange','ajax');
    }
    // 表示データ更新
    function comboChange($data){
        //$data = json_decode($_POST['dat'],true);
        $week = array('日','月','火','水','木','金','土'); // 0-6
//        $ymd = substr($data['nengetsu'],0,4).'-'.substr($data['nengetsu'],4,2).'-01';
        $ymd = $data['nengetsu'].'01';
        $dateobj = DateTime::createFromFormat('Ymd', $ymd); // DateTime Class
        $reki =  $dateobj->format('t'); // 歴日数
        // [0]目標 [1]合計 [2]1日 [3]2日 [4]3日 ・・・
        for($i = 2; $i < $reki + 2; $i++){
            $hi = $dateobj->format('m/d'); // 月日
            $nengappi = $dateobj->format('Ymd'); // 年月日
            $youbi = $dateobj->format('w'); // 曜日
            $hiduke = $hi.'('. $week[$youbi] .')';
            $wrk[$i]['date'] = $hiduke;
            $wrk[$i]['bumon'] = $data['bumon'];
            $wrk[$i]['hiduke'] = $nengappi;
            $dateobj->modify("+1 day");
        }
        $wrk[0]['len'] = $i;
        $wrk[0]['date'] = '着地予測';
        $wrk[0]['bumon'] = $data['bumon'];
        $wrk[1]['date'] = '合計';
        $wrk[1]['bumon'] = $data['bumon'];
        // 収支レコード取得
        $bu = $data['bumon'];
        $sql = 'select * from syushiw where bumonw = ? and hidukew >= ? and hidukew <= ?';
        $par = array($bu,$wrk[2]['hiduke'],$wrk[$i-1]['hiduke']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $hiduke = $result['hidukew'];
            $key = substr($hiduke,-2) + 1;
            if($result['hidukew'] != ''){
                // 売上集計（00:売上）
                // 支払集計（01：社員ST　02：スタッフSS　03：資材等　04：社会保険料）
                // 経費集計（00:経費）
                // 粗利（売上 - 支払）
                // 利益（粗利 - 経費）
                // 各テーブル、データが入ってない箇所は値を入れず - を表示
                $wrk[$key]['uriage00w'] = $result['uriage00w'];
                $wrk[$key]['shiharai01w'] = $result['shiharai01w'];
                $wrk[$key]['shiharai02w'] = $result['shiharai02w'];
                $wrk[$key]['shiharai03w'] = $result['shiharai03w'];
                $wrk[$key]['shiharai04w'] = $result['shiharai04w'];
                $wrk[$key]['keihi00w'] = $result['keihi00w'];
                $wrk[$key]['uriagew'] = $wrk[$key]['uriage00w'];
                $wrk[$key]['shiharaiw'] = $wrk[$key]['shiharai01w'] + $wrk[$key]['shiharai02w'] + $wrk[$key]['shiharai03w'] + $wrk[$key]['shiharai04w'];
                $wrk[$key]['keihiw'] = $wrk[$key]['keihi00w'];
                $wrk[$key]['arariw'] = $wrk[$key]['uriagew'] - $wrk[$key]['shiharaiw'];
                $wrk[$key]['araripw'] = round($wrk[$key]['arariw'] / $wrk[$key]['uriagew'] * 100,0);
                $wrk[$key]['riekiw'] = $wrk[$key]['uriagew'] - $wrk[$key]['shiharaiw'] - $wrk[$key]['keihiw'];
                $wrk[$key]['riekipw'] = round($wrk[$key]['riekiw'] / $wrk[$key]['uriagew'] * 100,0);

                $uriage00sum += $result['uriage00w'];
                $shiharai01sum += $result['shiharai01w'];
                $shiharai02sum += $result['shiharai02w'];
                $shiharai03sum += $result['shiharai03w'];
                $shiharai04sum += $result['shiharai04w'];
                $keihi00sum += $result['keihi00w'];
                $uriagesum += $wrk[$key]['uriagew'];
                $shiharaisum += $wrk[$key]['shiharaiw'];
                $keihisum += $wrk[$key]['keihiw'];
                $ararisum += $wrk[$key]['arariw'];
                $araripavr = round($ararisum / $uriagesum * 100,0);
                $riekisum += $wrk[$key]['riekiw'];
                $riekipavr = round($riekisum / $uriagesum * 100,0);
            }
            // 合計出力
            $wrk[1]['uriage00w'] = $uriage00sum;
            $wrk[1]['shiharai01w'] = $shiharai01sum;
            $wrk[1]['shiharai02w'] = $shiharai02sum;
            $wrk[1]['shiharai03w'] = $shiharai03sum;
            $wrk[1]['shiharai04w'] = $shiharai04sum;
            $wrk[1]['keihi00w'] = $keihi00sum;
            $wrk[1]['uriagew'] = $uriagesum;
            $wrk[1]['shiharaiw'] = $shiharaisum;
            $wrk[1]['keihiw'] = $keihisum;
            $wrk[1]['arariw'] = $ararisum;
            $wrk[1]['araripw'] = $araripavr;
            $wrk[1]['riekiw'] = $riekisum;
            $wrk[1]['riekipw'] = $riekipavr;
        }
        echo json_encode($wrk);
    }

    // koushin button1クリック時 データを呼び出し
   function koushinJs(){
    $this->clearJs();
       $this->js1 = <<<"EOT"
        FixedMidashi.remove();
        FixedMidashi.create();
EOT;
       $this->addEventListener('#button1','click','koushin','ajax');
   }
    // 更新
    function koushin($data){
        
    }
    

}

// ** page info ** //

$p = new page();
$cp = new customPage();

if($_GET['linktype'] == 'js'){
    $p->customJsStart();
    $methods = get_class_methods('page');
    foreach($methods as $val){
        if(substr($val,-2) == 'Js'){$p->$val();}
    }
    $p->customJsEnd();
}elseif($_GET['linktype'] == 'css'){
    $p->customCssStart();
    $p->customCssEnd();
}elseif($_POST['ajax'] == 'ajax'){
    $func = $_POST['func'];
    $data = json_decode($_POST['dat'],true);
    $p->$func($data);
}else{
    $src = $p->useTemplate(str_replace('.php','.html',$p->get_pgname()));

    $key = 'header';
    $params = array(
        "title"=>"収支",
        "customJs"=>1,
        "customCss"=>1
    );
    $src[$key] = $cp->customView($key,$params);
    
    // ナビゲーションの書き換え
    $key = 'nav';
    $params = array(
        "autho"=>$_SESSION['autho'],
        "active"=>"syushi"
    );
    $src[$key] = $cp->customView($key,$params);

    $p->show($src);
    $p->db = null;
}
?>