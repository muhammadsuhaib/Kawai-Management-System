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
        $sql = 'select * from koumoku where koum01="勤怠";';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $opt='';
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$opt .= $result['koum03'];
        }
        $this->js1 = <<<"__"
// コンボボックスのみ横キーキャンセル
$(document).on('keydown','select', function(e) {
    if(e.keyCode === 37 || e.keyCode === 39) {
        e.preventDefault();
    }
});
$(document).on("keypress", "input:not(.allow_submit)", function(event) {
    return event.which !== 13;
  });
var selectEditor = function(cell, onRendered, success, cancel, editorParams){
        //cell - the cell component for the editable cell
        //onRendered - function to call when the editor has been rendered
        //success - function to call to pass the succesfully updated value to Tabulator
        //cancel - function to call to abort the edit and return to a normal cell
        //editorParams - editorParams object set in column defintion
    //create and style editor
    var editor = $('<select class="form-control"><option value=""></option>$opt</select>');
    editor.css({
        "padding":"3px",
        "width":"100%",
        "box-sizing":"border-box",
    });

    //Set value of editor to the current value of the cell
    editor.val(cell.getValue());
    //set focus on the select box when the editor is selected (timeout allows for editor to be added to DOM)
    onRendered(function(){
      editor.focus();
    });
    //when the value has been set, trigger the cell to update
    editor.on("change blur", function(e){
        success(editor.val());
    });
    //return the editor element
    return editor;
};
        $(document).ready(function(){
			// tabulatorは最終列にwidth:1で全角スペースカラムを作ること。入れないとセルheightが狭くなる。
            $("#tabu1").tabulator({
                height:450,
                fitColumns:true,
                tooltipsHeader:true,
                groupBy:"bumon",
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
                //data - the updated table data
//                    console.log(cell.getRow().getData().id);
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
			var tableData = [
				{id:1,dm:'　',shimei:'female', help:'',kubun:'',kaishi:'',syuryo:'',kyukei:'',shinya:''},
    			{id:2,shimei:'male', help:'',kubun:'',kaishi:'',syuryo:'',kyukei:'',shinya:''},
    			{id:3,shimei:'female', help:'',kubun:'',kaishi:'',syuryo:'',kyukei:'',shinya:''}
			]
			$("#tabu1").tabulator("setData", tableData);
		});


__;
        $this->addEventListener();
    }
    // 日付変更(JS)
    function ymd_udInputChangeJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"ymd":$("#ymd_ud").val(),"bumon":$("#bumon_ud").val()};
            params = JSON.stringify(obj);
            //console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
			$(document).on('focus','#bumon',function(){
            	$('.tsArea1').TapSuggest({
                	tsInputElement : '#bumon',
                	tsArrayList : data[2],
                	tsRegExpAll : true
            	});
	   		});
__;
        $this->addEventListener('#ymd_ud','change','getBumon','ajax');
    }
        
    // 部門候補の取得
    function getBumon ($data){
		$today = date('Ymd');
        $sql = 'select * from bumon where bum01 <= ? and bum02 >= ?;';
		$par = array($today,$today);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$disp = $result['bum06'] . '.#' . $result['bum03'];
			$res[2][] = array($disp,$result['bum04'].' '.$result['bum05'].' '.$result['bum06'].' '.$result['bum07']);
		}
        echo json_encode($res);
    }

    // 検索実行(JS)
    function searchbtn1ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"ymd":$("#ymd_ud").val(),"kubun":$("#kubun_ud").val()};
            params = JSON.stringify(obj);
            console.log(obj);
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
        $ymd = $this->format($data['ymd'],'date'); // 対象年月日
        $bumon = $this->format($data['bumon'],'cd');
        if($data['kubun'] == '予測'){
            $par = array($bumon,$bumon);
            $sql = "select * from jyu_k left join jyu_c on jyuk01=jyuc01 and jyuc04='bumon'";
            $sql .= " left join bumon on jyuc05=bum03 and bum01<='$ymd' and bum02>='$ymd'"; // 請求先 
            $sql .= " left join kaisya on bum26=kais24"; // 部門 
            $sql .= " left join kintai on jyuk01=knt01 and knt02>='$ymd' and knt02<='$ymd' and knt09=bum03"; // 勤怠
            $sql .= " where jyuk02<='$ymd' and jyuk03>='$ymd' and jyuk09='name';";
        }else{
            $par = array($bumon,$bumon);
            $sql = "select * from jyu_k left join jyu_c on jyuk01=jyuc01 and jyuc04='bumon'";
            $sql .= " left join bumon on jyuc05=bum03 and bum01<='$ymd' and bum02>='$ymd'"; // 請求先 
            $sql .= " left join kaisya on bum26=kais24"; // 部門 
            $sql .= " left join kintai on jyuk01=knt01 and knt02>='$ymd' and knt02<='$ymd' and knt19=bum03"; // 勤怠
            $sql .= " where jyuk02<='$ymd' and jyuk03>='$ymd' and jyuk09='name';";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        $key = '';
        if($data['kubun'] == '予測'){
            $kntkey = 'knt04';
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $res[] = array(
                'cd'=>$result['jyuk01'], // 従業員cd
                'shimei'=>$result['jyuk04'].' '.$result['jyuk05'].' #'.$result['jyuk01'],
                'kubun'=>$result['knt04'],
                'kaishi'=>$result['knt05'],
                'syuryo'=>$result['knt06'],
                'kyukei'=>$result['knt07'],
                'shinya'=>$result['knt08'],
                'bumon'=>$result['bum04']
                );
            }
        }else{
            $kntkey = 'knt14';
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $res[] = array(
                'cd'=>$result['jyuk01'], // 従業員cd
                'shimei'=>$result['jyuk04'].' '.$result['jyuk05'].' #'.$result['jyuk01'],
                'help'=>'', // helpデータはhelpテーブルのみ
                'kubun'=>$result['knt14'],
                'kaishi'=>$result['knt15'],
                'syuryo'=>$result['knt16'],
                'kyukei'=>$result['knt17'],
                'shinya'=>$result['knt18'],
                'bumon'=>$result['bum04']
                );
            }
        }
		foreach($res as $ary){
            $rec[$ary['cd']]['cd'] = $ary['cd']; // CD
            $rec[$ary['cd']]['change'] = ''; // 
            $rec[$ary['cd']]['shimei'] = $ary['shimei']; // 
            $rec[$ary['cd']]['help'] = $ary['help']; // 
            $rec[$ary['cd']]['kubun'] = $ary['kubun']; // 
            $rec[$ary['cd']]['kaishi'] = $ary['kaishi']; // 
            $rec[$ary['cd']]['syuryo'] = $ary['syuryo']; // 
            $rec[$ary['cd']]['kyukei'] = $ary['kyukei']; // 
            $rec[$ary['cd']]['shinya'] = $ary['shinya']; // 
            $rec[$ary['cd']]['bumon'] = $ary['bumon']; // 
            $rec[$ary['cd']]['dm'] = '　'; // 
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
        $json[3] = $data['kubun'];
        $json[4] = $par;
		echo json_encode($json);
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
    $params = array('title'=>'請求書発行');
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