<?php
// ** 請求書 ** //

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');
require_once("PHPExcel/Classes/PHPExcel.php");

class page extends core {
    // JS初期処理
    function initJs(){
        $this->clearJs();
        $ym = date('Ym');
        $this->js1 = <<<"__"

            // tabulatorは最終列にwidth:1で全角スペースカラムを作ること。入れないとセルheightが狭くなる。
            $("#tabu1").tabulator({
                height:450,
                fitColumns:true,
                tooltipsHeader:true,
                groupBy:"bumon",
                columns:[
//                    {width:80, title:"部門", field:"bumcd", align:"center", frozen:true, formatter:'html'},
//                    {width:50, title:"CD", field:"cd", align:"left", frozen:true},
                    {width:150, title:"氏名#cd", field:"shimei", align:"left", frozen:true,formatter:'html'},
                    {width:80, title:"1日", field:"01", align:"center",formatter:'html'},
                    {width:80, title:"2日", field:"02", align:"center"},
                    {width:80, title:"3日", field:"03", align:"center"},
                    {width:80, title:"4日", field:"04", align:"center"},
                    {width:80, title:"5日", field:"05", align:"center"},
                    {width:80, title:"6日", field:"06", align:"center"},
                    {width:80, title:"7日", field:"07", align:"center"},
                    {width:80, title:"8日", field:"08", align:"center"},
                    {width:80, title:"9日", field:"09", align:"center"},
                    {width:80, title:"10日", field:"10", align:"center"},
                    {width:80, title:"11日", field:"11", align:"center"},
                    {width:80, title:"12日", field:"12", align:"center"},
                    {width:80, title:"13日", field:"13", align:"center"},
                    {width:80, title:"14日", field:"14", align:"center"},
                    {width:80, title:"15日", field:"15", align:"center"},
                    {width:80, title:"16日", field:"16", align:"center"},
                    {width:80, title:"17日", field:"17", align:"center"},
                    {width:80, title:"18日", field:"18", align:"center"},
                    {width:80, title:"19日", field:"19", align:"center"},
                    {width:80, title:"20日", field:"20", align:"center"},
                    {width:80, title:"21日", field:"21", align:"center"},
                    {width:80, title:"22日", field:"22", align:"center"},
                    {width:80, title:"23日", field:"23", align:"center"},
                    {width:80, title:"24日", field:"24", align:"center"},
                    {width:80, title:"25日", field:"25", align:"center"},
                    {width:80, title:"26日", field:"26", align:"center"},
                    {width:80, title:"27日", field:"27", align:"center"},
                    {width:80, title:"28日", field:"28", align:"center"},
                    {width:80, title:"29日", field:"29", align:"center"},
                    {width:80, title:"30日", field:"30", align:"center"},
                    {width:80, title:"31日", field:"31", align:"center"},
                    {width:150, title:"部門", field:"bumon", align:"left"},
                    {width:1, title:"", field:"dm"}
                ],
                tooltips:function(cell){
                    return cell.getValue();
                },
				rowFormatter:function(row){
					var data = row.getData();
					var cell;
					var elem;
                    var strday;
                    for(var i = 1; i <= 31; i++){
                        strday = ('00'+i).slice(-2);
                        cell = row.getCell(strday);
                        elem = cell.getElement();
					   if(cell.getValue() == undefined || cell.getValue() == ''){
//				  		  console.log(cell);
						  elem.css({"background-color":"#ff7777"});
					   }
                    }
                },
			});

// コンボボックスのみ横キーキャンセル
$(document).on('keydown','select', function(e) {
    if(e.keyCode === 37 || e.keyCode === 39) {
        e.preventDefault();
    }
});
$(document).on("keypress", "input:not(.allow_submit)", function(event) {
    return event.which !== 13;
  });

__;
        $this->addEventListener();
    }
    
    // 検索実行(JS)
    function searchbtn1ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"ymd":$("#ym_ud").val(),"shimebi":$("#shimebi_ud").val(),"hani":$("#hani_ud").val(),"kubun":$("#kubun_ud").val()};
            params = JSON.stringify(obj);
            //console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            console.log(data);
			$("#tabu1").tabulator("setData", data[0]);
__;
        $this->addEventListener('#searchbtn1','click','searchData','ajax');
    }
    // 検索データ取得(PHP)
    function searchData($data){
        $ym = substr($this->format($data['ymd'],'date'),0,6); // 対象年月
        $ymd = $this->format($data['ymd'],'date'); // 対象年月日
        $shimebi = $data['shimebi']; // 締め日 kais21
        $hani = $data['hani']; // 条件絞り
        $kubun = $data['kubun']; // 条件絞り
        
        $ymdst = $ym.'00';
        $ymden = $ym.'31';
        $busql = "bum01<='$ymdst' and bum02>='$ymdst'";
		if($shimebi){
			$par = array($shimebi);
			$sql = "select * from jyu_k left join jyu_c on jyuk01=jyuc01 and jyuc04='bumon'";
            $sql .= " left join bumon on jyuc05=bum03 and bum01<='$ymdst' and bum02>='$ymdst'"; // 請求先
			$sql .= " left join kaisya on bum26=kais24"; 
			$sql .= " left join kintai on jyuk01=knt01 and knt02>='$ymdst' and knt02<='$ymden'"; // 勤怠
			$sql .= " where jyuk03>='$ymdst' and jyuk02<='$ymden' and jyuk09='name' and kais21=?;"; // 終了日が指定月初日以上 = その月に在籍してた人
        }else{
			$par = array();
			$sql = "select * from jyu_k left join jyu_c on jyuk01=jyuc01 and jyuc04='bumon'";
            $sql .= " left join bumon on jyuc05=bum03 and bum01<='$ymdst' and bum02>='$ymdst'"; // 請求先
			$sql .= " left join kaisya on bum26=kais24"; 
			$sql .= " left join kintai on jyuk01=knt01 and knt02>='$ymdst' and knt02<='$ymden'"; // 勤怠
			$sql .= " where jyuk03>='$ymdst' and jyuk02<='$ymden' and jyuk09='name';";  // 終了日が指定月初日以上 = その月に在籍してた人
		}
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        $key = '';
        if($kubun == '予測'){
            $kntkey = 'knt04';
        }else{
            $kntkey = 'knt14';
        }
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$res[] = array(
                'cd'=>$result['jyuk01'],
                'bumcd'=>$result['bum03'],
                'shimei'=>$result['jyuk04'].' '.$result['jyuk05'].' #'.$result['jyuk01'],
                'bumon'=>$result['bum04'],
                'hi'=>$result['knt02'],
                'st'=>$result[$kntkey]
            );
        }
		foreach($res as $ary){
            if($ary['hi']){
                $rec[$ary['cd']]['cd'] = $ary['cd']; // CD
                $rec[$ary['cd']]['bumcd'] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$ary['bumcd'].'" ymd="'.$ymd.'">'.$ary['bumcd'].'</button>'; // 部門CD
                $rec[$ary['cd']]['shimei'] = '<a href=# onclick="modalCallUd(this);" value="'.$ary['cd'].'" ym="'.$ym.'">'.$ary['shimei']."</a>"; // 氏名
                $rec[$ary['cd']]['bumon'] = $ary['bumon']; // 部門
                $hi = substr($ary['hi'],6,2);
                $rec[$ary['cd']][$hi] = $ary['st']; // 日付
            }else{
                $rec[$ary['cd']]['cd'] = $ary['cd']; // CD
                $rec[$ary['cd']]['bumcd'] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$ary['bumcd'].'" ym="'.$ym.'">'.$ary['bumcd'].'</button>'; // 部門CD
                $rec[$ary['cd']]['shimei'] = '<a href=# onclick="modalCallUd(this);" value="'.$ary['cd'].'" ym="'.$ym.'">'.$ary['shimei']."</a>"; // 氏名
                $rec[$ary['cd']]['bumon'] = $ary['bumon']; // 部門
/*                for($i=1;$i<=31;$i++){
                    $a = sprintf('%02d',$i);
                    $rec[$ary['cd']][$a] = ''; // 社員CD,日付
                }
*/            }
        }
        unset($ary);
		foreach($rec as $ary){
            $tabuwrk[] = $ary;
        }
        unset($ary);
        $i = 1;
        foreach($tabuwrk as &$ary){
            $ary['id'] = $i;
            $i++;
        }
        unset($ary);
        
        $json[0] = $tabuwrk;
        $json[1]['sql'] = $sql;
        $json[3] = $wrk;
        $json[4] = $par;
		echo json_encode($json);
    }

    
    // 更新・削除モーダル表示(JS)
    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"cd":event.getAttribute('value'),"ym":event.getAttribute('ym')};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_ud').remove();
            $('#modalParent1').append(data["html"]);
            $('#modal_ud').modal({backdrop:'static'});
            var tableData = data;
            

//            $('#kubun01ud').val(data[1]['01']); $('#kubun02ud').val(data[1]['02']); $('#kubun03ud').val(data[1]['03']);
//            $('#kubun04ud').val(data[1]['04']); $('#kubun05ud').val(data[1]['05']); $('#kubun06ud').val(data[1]['06']);
//            $('#kubun07ud').val(data[1]['07']); $('#kubun08ud').val(data[1]['08']); $('#kubun09ud').val(data[1]['09']);
//            $('#kubun10ud').val(data[1]['10']);
//            $('#kubun11ud').val(data[1]['11']); $('#kubun12ud').val(data[1]['12']); $('#kubun13ud').val(data[1]['13']);
//            $('#kubun14ud').val(data[1]['14']); $('#kubun15ud').val(data[1]['15']); $('#kubun16ud').val(data[1]['16']);
//            $('#kubun17ud').val(data[1]['17']); $('#kubun18ud').val(data[1]['18']); $('#kubun19ud').val(data[1]['19']);
//            $('#kubun10ud').val(data[1]['10']);
//            $('#kubun21ud').val(data[1]['21']); $('#kubun22ud').val(data[1]['22']); $('#kubun23ud').val(data[1]['23']);
//            $('#kubun24ud').val(data[1]['24']); $('#kubun25ud').val(data[1]['25']); $('#kubun26ud').val(data[1]['26']);
//            $('#kubun27ud').val(data[1]['27']); $('#kubun28ud').val(data[1]['28']); $('#kubun29ud').val(data[1]['29']);
//            $('#kubun30ud').val(data[1]['30']);
//            $('#kubun31ud').val(data[1]['31']);

            // tabulator用combobox定義
            var selectEditor = function(cell, onRendered, success, cancel, editorParams){
                var editor = $('<select class="form-control"><option value=""></option>data[1]["opt"]</select>');
                editor.css({
                    "padding":"3px",
                    "width":"100%",
                    "box-sizing":"border-box",
                });
                editor.val(cell.getValue());
                onRendered(function(){
                    editor.focus();
                });
                editor.on("change blur", function(e){
                    success(editor.val());
                });
                return editor;
            };
            $("#tabu2").tabulator({
                height:450,
                fitColumns:true,
                tooltipsHeader:true,
                columns:[
                    {width:20, title:"変更", field:"change", align:"center",frozen:true},
                    {width:200, title:"氏名#cd", field:"shimei", align:"left",editor:'input' ,frozen:true},
                    {width:90, title:"ヘルプ", field:"help", align:"left"},
                    {width:120, title:"勤務区分", field:"kubun", align:"left",editor:selectEditor},
                    {width:100, title:"出勤時刻", field:"kaishi", align:"left",editor:'input', formatter:function(cell, formatterParams){
                        if(cell.getValue()){
                            var tim = cell.getValue();
                            return tim.substr(0,2) + ':' + tim.substr(2,2)
                        }else{
                            return false;
                        }
                        }
                    },
                    {width:100, title:"退勤時刻", field:"syuryo", align:"left",editor:'input', formatter:function(cell, formatterParams){
                        if(cell.getValue()){
                            var tim = cell.getValue();
                            return tim.substr(0,2) + ':' + tim.substr(2,2)
                        }else{
                            return false;
                        }
                        }
                    },
                    {width:100, title:"休憩時間", field:"kyukei", align:"left",editor:'input', formatter:function(cell, formatterParams){
                        if(cell.getValue()){
                            var tim = cell.getValue();
                            return tim.substr(0,2) + ':' + tim.substr(2,2)
                        }else{
                            return false;
                        }
                        }
                    },
                    {width:100, title:"深夜休憩", field:"shinya", align:"left",editor:'input', formatter:function(cell, formatterParams){
                        if(cell.getValue()){
                            var tim = cell.getValue();
                            return tim.substr(0,2) + ':' + tim.substr(2,2)
                        }else{
                            return false;
                        }
                        }
                    },
                    {width:150, title:"部門", field:"bumon", align:"left"},
                    {width:1, title:"", field:"dm"}
                ],
                tooltips:function(cell){
                    return cell.getValue();
                },
				rowFormatter:function(row){
					var data = row.getData();
					if(data.seikyu == "〇"){
						row.getElement().css({"background-color":"#c2a6ff"});
					}
				},
                cellEdited:function(cell){
                    var row = cell.getRow();
                    var oldv = cell.getOldValue();
                    var newv = cell.getValue();
                    if(oldv != newv && (oldv != null && newv != '')){
                        row.update({'change':'●'});
                    }
//                    console.log(cell.getOldValue());
//                    console.log(cell.getValue());
                },
            });

			$("#tabu2").tabulator("setData", tableData);
            console.log(tableData);

__;
        $this->addEventListener('','wait','modalCallUd','ajax');
    }
    // 更新・削除モーダル表示(PHP)
    function modalCallUd($data){
        $modal = $this->readModalSource('modal_ud');
        
        // 日付・曜日表示
        $st = $data['ym'].'01';
        $en = $data['ym'].'31';
        $y = substr($st,0,4);
        $m = substr($st,4,2);
        $d = '01';
        $date = $y . '-' . $m .  '-' . $d;
        $datetime = new DateTime($date);
        $week = array("日", "月", "火", "水", "木", "金", "土");
        $nissu = $datetime->format('t'); // 暦日
        // $dat['日付']['項目']
        // dat[20170901][20170901,st,en,kyu,sinya,syuku]
        for($i=1; $i<=31; $i++){
            $youbi = $week[$datetime->format('w')];
            $a = sprintf('%02d',$i);
            if($i <= $nissu){
                $dat[$a] = $datetime->format("m/d（{$youbi}）");
            }else{
                $dat[$a] = '-';
            }
            $datetime->modify('+1 days');
        }
        
        // 値の初期化
/*        for($i=1;$i<=31;$i++){
            $a = sprintf('%02d',$i);
            $dat[$a] = array('change'=>'','shimei'=>,'kubun'=>,'kaishi'=>,'syuryo'=>,'kyukei'=>,'shinya'=>,);
        }
*/
        // 祝日処理
        // データの終点が基準始点以上、データの始点が基準終点以下
        $par = array($en,$st);
		$sql = "select cal01,cal02 from calendar where cal05='kyu' and cal07='' and cal01<=? and cal02>=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            for($i=$result['cal01'];$i<=$result['cal02'];$i++){
                if($i < $st){ // 基準始点より小さければ
                    $i += $st-$i; // 基準始点までとばす
                }
                if($i > $en){ // 基準終点より大きければ
                    $i += $i-$en; // 基準終点までとばす
                }
                $hi = substr($i,6,2);
                if($hi < $nissu){ // 暦日より大きい日付をカット
//                    $modal["lab{$hi}ud"] = '【祝】'.$modal["lab{$hi}ud"];
                    $rec[$hi]['syuku'] = '祝日';
                }
            }
        }
        
        // 勤怠区分候補取得
        $par = array();
        $sql = 'select koum03 from koumoku where koum01="勤怠" order by koum02;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			 $json['opt'] .= $result['koum03'];
        }
        
        $par = array($data['cd'],$st,$en);
		$sql = "select * from kintai where knt01=? and knt02>=? and knt02<=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $a = substr($result['knt02'],6,2);
            $rec[$a]['kubun'] = $result['knt04'];
            $rec[$a]["kaishi"] = $this->format($result['knt05'],'time:');
            $rec[$a]["syuryo"] = $this->format($result['knt06'],'time:');
            $rec[$a]["kyukei"] = $this->format($result['knt07'],'time:');
            $rec[$a]["shinya"] = $this->format($result['knt08'],'time:');
        }
		foreach($rec as $ary){
            $tabuwrk[] = $ary;
        }
        unset($ary);
        $i = 1;
        foreach($tabuwrk as &$ary){
            $ary['id'] = $i;
            $i++;
        }
        unset($ary);
        $json[0] = $tabuwrk;

        $json['html'] = implode("", $modal);
        echo json_encode($res);
    }

    // 請求書発行(JS)
    function seikyubtnClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = $("#tabu1").tabulator("getData", true); // return currently filtered data
            console.log(obj);
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
			window.open('exceldown-7djq4thd51js0ahde.php','_blank');
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#seikyubtn','click','excelDownload','ajax');
	}
    // 請求書発行(PHP)
    function excelDownload($data){
		// 必要値をsession変数に格納
		$_SESSION['wrk'] = $data;
		// exceldown-7djq4thd51js0ahde.php にアクセス
	}

}


// ** page info ** //
$p = new page();

if($_GET['linktype'] == 'js'){
    $p->jsFunctionExecute(); // function ---Js() を 実行しJSを出力($_GET['linktype'] == js)
}
elseif($_GET['linktype'] == 'css'){
    $p->cssFunctionExecute(); // function ---Css() を 実行しCSSを出力($_GET['linktype'] == css)
}
elseif($_POST['linktype'] == 'ajax' or $_POST['linktype'] == 'ajax_f'){
    $p->ajaxFunctionExecute(); // function ---ajax() を 実行しajaxデータを出力($_POST['linktype'] == ajax_f)
}
else{
    // HTMLからリンクされるカスタムJSでPOST値・GET値を使うため、$_POST・$_GETをSESSION変数に格納
    // 利用時は $_SESSION['post_dat']['名前'] で利用
    if(!empty($_POST)){
        $_SESSION['post_dat'] = $_POST;
    }
    if(!empty($_GET)){
        $_SESSION['get_dat'] = $_GET;
    }
    $p->htmlFunctionExecute(); // 同名templateを読み込みHTMLソースを生成($this->src[key_name]が@key部分に該当)
    // header 及び standardComponents ロード(lockmodal hiddenfields)
    $params = array('title'=>'勤怠予定');
    $p->src['header'] = headerReplace::createSrc($params);
    $p->src['standard'] = standardComponentsLoad::createSrc();

    // bootstrap ナビゲーション
	foreach($_SESSION as $key => $val){
		$sessstr .= "[$key]" . $val . " , ";
	}
    $params = array('autho'=>$_SESSION['autho'], 'active'=>'order', 'name'=>$_SESSION['name'],'sess'=>$sessstr);
    $p->src['nav'] = bootstrapNavigationReplace::createSrc($params);

    $p->show();
}
?>