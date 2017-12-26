<?php
// ** スタッフマスター ** //
// 定数（項目名）
define("KOUMOKU_MEI01","No");
define("KOUMOKU_MEI02","氏名(姓)");
define("KOUMOKU_MEI03","氏名(名)");
define("KOUMOKU_MEI04","氏名カナ(姓)");
define("KOUMOKU_MEI05","氏名カナ(名)");
define("KOUMOKU_MEI06","性別");
define("KOUMOKU_MEI07","生年月日");
define("KOUMOKU_MEI08","住所");
define("KOUMOKU_MEI09","勤務可能日");
define("KOUMOKU_MEI10","通勤手段");
define("KOUMOKU_MEI11","通勤時間");
define("KOUMOKU_MEI12","健康状態");
define("KOUMOKU_MEI13","作業着(上)");
define("KOUMOKU_MEI14","作業着(下)");
define("KOUMOKU_MEI15","作業靴");
define("KOUMOKU_MEI16","最終学歴");
define("KOUMOKU_MEI17","保有資格");
define("KOUMOKU_MEI18","職務経歴");
define("KOUMOKU_MEI19","希望勤務地");
define("KOUMOKU_MEI20","採用区分");
define("KOUMOKU_MEI21","不採用理由");
define("KOUMOKU_MEI22","");
define("KOUMOKU_MEI23","");
define("KOUMOKU_MEI24","");
define("KOUMOKU_MEI25","");
define("KOUMOKU_MEI26","");
define("KOUMOKU_MEI27","");
define("KOUMOKU_MEI28","");
define("KOUMOKU_MEI29","");
define("KOUMOKU_MEI30","");
define("KOUMOKU_MEI31","");
define("KOUMOKU_MEI32","");
define("KOUMOKU_MEI33","");
define("KOUMOKU_MEI34","");
define("KOUMOKU_MEI35","");
define("KOUMOKU_MEI36","");
define("KOUMOKU_MEI37","");
define("KOUMOKU_MEI38","");
define("KOUMOKU_MEI39","");
define("KOUMOKU_MEI40","");
define("KOUMOKU_MEI41","");
define("KOUMOKU_MEI42","");
define("KOUMOKU_MEI43","");
define("KOUMOKU_MEI44","");
define("KOUMOKU_MEI45","");
define("KOUMOKU_MEI46","");
define("KOUMOKU_MEI47","");
define("KOUMOKU_MEI48","");
define("KOUMOKU_MEI49","");
define("KOUMOKU_MEI50","");

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');
require_once('incl/password.php');
require_once("PHPExcel/Classes/PHPExcel.php");

// ★ POST GET

class page extends core {
    private $koumokusu = 34; // 表示するfield数(従業員マスターfield数 + joinテーブルから選択したfield数)
    


    // コピー準備(JS)
    function cpybtn1ClickJs(){
        $this->clearJs();
        $str = modalControl::appendSrc('modal2','data[0]["html"]','modalParent2');
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $str
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('#cpybtn1','click','cpymodal','ajax');
    }
    // 
    function cpymodal($data){
        $id = 'modal2';
        $header = '<div class=modal-title><b>確認</b></div>';
        $body = '<div>このデータを従業員マスターに移行します。宜しいですか？</div>';
        $footer = <<<"__"
        <button type="button" class="btn btn-default" name="button2" id="cancelbtn2" data-dismiss="modal">いいえ</button>
        <button type="button" class="btn btn-danger" name="button2" id="cpybtn2">はい</button>
__;
        $params = array('id'=>$id,'size'=>'modal-sm','header'=>$header,'body'=>$body,'footer'=>$footer);
        $wrk[0]['html'] = modalControl::createSrc($params);
        echo json_encode($wrk);
    }

	// コピー(JS)
    function cpybtn2ClickJs(){
        $this->clearJs();
        for($i=1; $i <= $this->koumokusu; $i++){
            $a = sprintf('%02d',$i);
            $wrkstr .= '"stf' . $a . '":$("#stf' . $a . '").val(),';
        }
		$wrkstr = substr($wrkstr,0,-1);
        $this->js1 = <<<"__"
            $('[name=button2]').prop("disabled",true);
            var obj = {
			$wrkstr
			};
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
        $this->addEventListener2('#cpybtn2','click','copyData','ajax');
    }
    // コピー(PHP)
    function copyData($data){
        $today = date('Ymd');
        $now = date('Hi');
		// 従業員マスター内の同一人物重複チェック
		
		// コピー
        $sql = "select sai02 from saiban where sai01='jyugyoin';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $bango = $result["sai02"];
        }
        if($bango){
            //$data['stf01'] = $bango; // 採番した番号
            $sql = "update saiban set sai02 = (sai02 + 1) where sai01='jyugyoin';";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
			
            $par = array();
            $sql = "insert into jyugyoin (";
            for($i=1; $i <= 34; $i++){ // 従業員マスター項目数
                $a = sprintf('%02d',$i);
                $sql .= "jyu".$a.",";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for($i=1; $i <= 34; $i++){
                $sql .= "?,";
            }
            $sql = substr($sql,0,-1);
            $sql .= ",?,?,?,?,?,?);";
			/*
            for($i=1; $i <= $this->koumokusu; $i++){
                $a = sprintf('%02d',$i);
                $par[] = $data["stf$a"];
            }
			*/
			$par = array(
				"",$bango,$data['stf02'],$data['stf03'],"","","","","","",
				"","","","","","","","","","",
				"","","","","","","","","","",
				"","","",""
			);
            array_push($par,$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
			//echo json_encode($wrk);
        }
		
		// 削除
        $sql = "delete from stuff where stf01=?;";
        $par = array($data['stf01']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
    }


    
    // エクセルダウンロード(JS)
    function excelbtnClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
		window.open('exceldown-7djq4thd51js0ahde.php','_blank');
__;
        $this->js2 = <<<"__"
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener2('','wait','excelDownload','');
	}
    // エクセルダウンロード(PHP)
    function excelDownload($data){
		// exceldown-7djq4thd51js0ahde.php にアクセス
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
    $params = array('title'=>'スタッフエントリー');
    $p->src['header'] = headerReplace::createSrc($params);
    $p->src['standard'] = standardComponentsLoad::createSrc();

    // bootstrap ナビゲーション
    $params = array('autho'=>$_SESSION['autho'], 'active'=>'order', 'name'=>$_SESSION['name']);
    $p->src['nav'] = bootstrapNavigationReplace::createSrc($params);

    $p->show();
}
?>