<?php

// ** スタッフマスター ** //
// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');
require_once('incl/password.php');

// ★ POST GET

class page extends core {
    
   private $koumokusu = 200; // 表示するfield数(従業員マスターfield数 + joinテーブルから選択したfield数)

    function initJs() {
        $this->clearJs();
        $today = date('Y-m-d');
        $this->js1 = <<<"__"
       // 検索条件初期値設定
        var json_data_stock_obj = {"jyouken1":"","maekakko1":"","koumoku1":"date","val1":"{$today}","enzan1":"=","atokakko1":"","jyouken2":"","maekakko2":"","koumoku2":"","val2":"","enzan2":"","atokakko2":"","jyouken3":"","maekakko3":"","koumoku3":"","val3":"","enzan3":"","atokakko3":"","jyouken4":"","maekakko4":"","koumoku4":"","val4":"","enzan4":"","atokakko4":"","order1":"jyuk02","orderopt1":"ASC","order2":"","orderopt2":"","order3":"","orderopt3":"","order4":"","orderopt4":""};
        $('#json_data_stock').val(JSON.stringify(json_data_stock_obj));
       $('#searchbtn2').trigger('click');
       
       // ヒントの表示
       var hintObj = {
       "":"　",
       "jyuk01":"適用開始：マスターの適用開始日。",
       "jyuk02":"適用終了：マスターの適用終了日。空が最新のマスター。",
       "jyuk03":"会社番号",
       "jyuk04":"会社名（略称。20文字以内。）",
       "jyuk05":"会社名カナ（略称。20文字以内。）",
       "jyuk06":"会社名（正式名称）",
       "jyuk07":"会社名カナ（正式名称）",
       "jyuk08":"備考",
       "jyuk09":"データの識別CD（unique）",
       "jyuk10":"種別(1～5:請求先　90～会社情報)",
       "jyuk11":"郵便番号",
       "jyuk12":"都道府県",
       "jyuk13":"市区町村",
       "jyuk14":"町域",
       "jyuk15":"アパート",
       "jyuk16":"電話番号",
       "jyuk17":"FAX番号",
       "jyuk18":"メールアドレス",
       "jyuk19":"担当部署",
       "jyuk20":"担当者名",
       "jyuk21":"請求日",
       "jyuk22":"",
       "jyuk23":"",
       "jyuk24":"",
       "jyuk25":"",
       "jyuk26":"",
       "jyuk27":"",
       "jyuk28":"",
       "jyuk29":"",
       "jyuk30":""
};
       $(document).on('change','#koumoku1',function(){1
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
        $this->addEventListener('', '', '', '');
    }

    // 新規モーダル表示(JS)
    function createbtn1ClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_c').remove();
            $('#modalParent1').append(data[0]["html"]);
            $('#modal_c').modal({backdrop:'static'});
            console.log(data);
            $(document).on('focus','#所属部署コードs',function(){
            	$('.tsArea1').TapSuggest({
                	tsInputElement : '#所属部署コードs',
                	tsArrayList : data[2],
                	tsRegExpAll : true
            	});
            });
            $(document).on('focus','#住民税＿納付先コードs',function(){
            	$('.tsArea3').TapSuggest({
                	tsInputElement : '#住民税＿納付先コードs',
                	tsArrayList : data[3],
                	tsRegExpAll : true
				});
            });
            $(document).on('focus','#ginko',function(){
            	$('.tsArea4').TapSuggest({
                	tsInputElement : '#ginko',
                	tsArrayList : data[4],
                	tsRegExpAll : true
				});
            });
            $(document).on('focus','#国籍s',function(){
            	$('.tsArea6').TapSuggest({
                	tsInputElement : '#国籍s',
                	tsArrayList : data[6],
                	tsRegExpAll : true
            	});
	   });

__;
        $this->addEventListener('#createbtn1', 'click', 'modalCallCr', 'ajax');
    }

    // 新規モーダル表示(PHP)
    function modalCallCr($data) {
        $modal = $this->readModalSource('modal_c');
        $sql = 'select sai02 from saiban where sai01 = "jyugyoin";';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $modal['saiban'] = $result['sai02'];
        }
        // 選択用の項目を取得
        $sql = 'select koum01,group_concat(koum03) as "k" from koumoku group by koum01 order by koum02;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $koumoku[$result['koum01']] = str_replace(',', '', $result['k']);
        }
        // 選択項目
        $blnk = "<option value=''></option>";
        $modal['拠点'] = $blnk.$koumoku['拠点'];
        $modal['応募者状態'] = $blnk.$koumoku['応募者状態'];
        $modal['性別'] = $blnk.$koumoku['性別'];
        $modal['緊急連絡先区分'] = $blnk.$koumoku['緊急連絡先区分'];
        $modal['最終学歴'] = $blnk.$koumoku['最終学歴'];
        $modal['学生区分'] = $blnk.$koumoku['学生区分'];
        $modal['障碍者区分'] = $blnk.$koumoku['障碍者区分'];
        $modal['職群'] = $blnk.$koumoku['職群'];
        $modal['号数'] = $blnk.$koumoku['番号'];
        $modal['通勤方法'] = $blnk.$koumoku['通勤方法'];
        $modal['利用乗物'] = $blnk.$koumoku['利用乗物'];
        $modal['管理権限'] = $blnk.$koumoku['管理権限'];
        $modal['稟議権限'] = $blnk.$koumoku['稟議権限'];
        $modal['権限'] = $blnk.$koumoku['権限'];
        $modal['社宅有無'] = $blnk.$koumoku['社宅有無'];
//        $modal[''] = $blnk.$koumoku[''];
        
        // 部門候補取得
        $today = date('Ymd');
        $sql = 'select * from bumon where bum01 <= ? and bum02 >= ?;';
        $par = array($today, $today);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $disp = $result['bum06'] . '.#' . $result['bum03'];
            $res[2][] = array($disp, $result['bum04'] . ' ' . $result['bum05'] . ' ' . $result['bum06'] . ' ' . $result['bum07']);
        }
        // 国籍用データ取得
        $sql = 'select * from country;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $disp = $r['cntry02'] . '.#' . $r['cntry01'];
            $res[6][] = array($disp, $r['cntry03']);
        }
        // 管理領域用データ取得
        $sql = 'select grp01,grp02,grp03,grp04,grp05 from bgroup order by grp05,grp07;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $kouho = "<option value=''></option>";
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($result['grp05'] < 10) {
                $result['grp04'] = '★' . $result['grp04'];
            }
            $kouho .= "<option value='{$result['grp02']}'>{$result['grp04']}</option>";
        }
        $modal['jyuc05opt'] = $kouho;
        // 住民税納付先
        $sql = 'select * from tkdantai;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $kana = mb_convert_kana($result['danta04'] . ' ' . $result['danta05'], "KV"); // 半カナ→全カナ
            $disp = $result['danta02'] . ' ' . $result['danta03'] . '.#' . $result['danta01'];
            $res[3][] = array($disp, $kana);
        }
        // 銀行
        $sql = 'select * from ginkou;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $kana = mb_convert_kana($result['gink03'], "KV"); // 半カナ→全カナ
            $disp = $result['gink04'] . '銀行.#' . $result['gink01'];
            $res[4][] = array($disp, $kana);
        }

        $res[0]['html'] = implode($modal);
        echo json_encode($res);
    }

    // 新規(JS)
    function createbtn2ClickJs() {
        $this->clearJs();
        $sql = 'select koum04,koum03 from koumoku where koum01="従業員" order by koum02;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
//            $kcd = $result['koum04']; // コード
            $knm = $result['koum03']; // 名前（key）
            $kary[$knm] = $knm;
        }
        $this->js1 = <<<"__"
            //$('[name=button2]').prop("disabled",true);
			// 部門
			var ary = $('#所属部署コードs').val().split('.#');
			if(ary.length == 2)
			{
				$('#所属部署コードs').val(ary[1]);
			}
			else
			{
				$('#所属部署コードs').val('');
			}
			// 住民税
			ary = $('#住民税＿納付先コードs').val().split('.#');
			if(ary.length == 2)
			{
				$('#住民税＿納付先コードs').val(ary[1]);
			}
			else
			{
				$('#住民税＿納付先コードs').val('');
			}
            var obj = {
__;
        // ex. {'拠点lab':$('#拠点lab').val(),'拠点':$('#拠点').val(),...}
        foreach ($kary as $key => $val) {
            $this->js1 .= "'" . $val . "lab':$('#" . $val . "lab').val(),";
            $this->js1 .= "'" . $val . "':$('#" . $val . "').val(),";
        }
        $this->js1 = substr($this->js1, 0, -1) . "}; params = JSON.stringify(obj); console.log(obj);";

        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            console.log(data);
            if(data[0]['res'])
			{
                $('#modal_c').modal('hide');
                $('#searchbtn2').trigger('click');
            }
			else
			{
                $('[name=button2]').prop("disabled",false);
                alert('エラーが発生しました。データを登録できません。');
            }
__;
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#createbtn2', 'click', 'createData', 'ajax');
    }

    // 新規(PHP)
    function createData($data) {
        // 住所全角変換
        $data['都道府県'] = mb_convert_kana($data['都道府県'], "ASKV");
        $data['市区町村'] = mb_convert_kana($data['市区町村'], "ASKV");
        $data['町域'] = mb_convert_kana($data['町域'], "ASKV");
        $data['アパート名など'] = mb_convert_kana($data['アパート名など'], "ASKV");


        /*        $err = '';
          if(preg_match("/^[a-zA-Z0-9]+$/", $data['jyu01'])){
          $err = 'err';
          }
          if(preg_match("/^[a-zA-Z0-9]+$/", $data['jyu02'])){
          $err = 'err';
          }
         */
        // 従業員番号がブランクなら採番
        $sql = "select sai02 from saiban where sai01='jyugyoin';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bango = $result["sai02"];
        }
        if ($bango) {
            $sql = "update saiban set sai02 = (sai02 + 1) where sai01='jyugyoin';";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $wrkcd = '';

        // 同一従業員番号がないかチェック
        $par = array($bango);
        $sql = "select jyuk01 from jyu_k where jyuk01=?;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $wrkcd = $result['jyuk01'];
        }
        if ($wrkcd == '') {
            $cd = $bango; // 従業員番号
            $st = $data['入社日']; // 期間開始（入社日）
            $en = '2200-01-01'; // 期間終了
            if (!$data['パスワード']) {
                $data['パスワード'] = '';
            } else {
                $pass = password_hash($data['パスワード'], PASSWORD_DEFAULT); // パスワードはハッシュ化
                $data['パスワード'] = $pass;
            }
            // 
            $par = array($cd, $st, $en, $today, '', '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']);
            $sql = "insert into jyu_k values ";
            $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);

            if ($wrk[0]['res']) {
                $par = array();
                $jyukary = array('従業員コード', '入社日', '退職日', '登録日', '契約日', '終了日');
                $j = 0;
                foreach ($data as $key => $val) {
                    // jyu_kテーブルでない項目 and ラベルでないデータ
                    if (!in_array($key, $jyukary) and substr($key, -3, 3) != 'lab') {
                        array_push($par, $cd, $today, $en, $key, $val, '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']);
                        $j++;
                    }
                }
                $sql = "insert into jyu_i values ";
                // jyu_iの項目数
                for ($i = 0; $i < $j; $i++) {
                    $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
                }
                $sql = substr($sql, 0, -1);
                $stmt = $this->db->prepare($sql);
                $wrk[0]['res'] = $stmt->execute($par);

                if ($wrk[0]['res']) {
                    $par3 = array(
                        $cd, $st, $en, '年調', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                        '', $now, $_SESSION['id'], $now, $_SESSION['id']
                    );
                    $sql = "insert into nencho values "; // 空レコード登録
                    $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $this->db->prepare($sql);
                    $wrk[0]['res'] = $stmt->execute($par3);
                }
            }
        } else {
            $wrk[0]['res'] = false;
        }
        echo json_encode($wrk);
    }

    // 検索用モーダル表示(JS)
    function searchbtn1ClickJs() {
        $this->clearJs();
        $this->js1 = " params = $('#json_data_stock').val();";
        for ($i = 1; $i <= 4; $i++) {
            $zenkai .= <<<"__"
            $('#jyouken$i').val(zdata['jyouken$i']);
            $('#maekakko$i').val(zdata['maekakko$i']);
            $('#koumoku$i').val(zdata['koumoku$i']);
            $('#val$i').val(zdata['val$i']);
            $('#enzan$i').val(zdata['enzan$i']);
            $('#atokakko$i').val(zdata['atokakko$i']);
__;
        }
        for ($i = 1; $i <= 4; $i++) {
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
        $this->addEventListener('#searchbtn1', 'click', 'searchModalCall', 'ajax');
    }

    // 検索用モーダル表示(PHP)
    function searchModalCall($data) {
        $modal = $this->readModalSource('modal_search'); // $modal[] = modalソース配列
        // コンボボックス処理
        $wrk = array('' => 'パターンから選択');
        $modal['combo0'] = '<OPTION value="">未選択</OPTION>';
        $sql = "select * from kensakupt where kns01=? and kns05='jyugyouin' order by kns02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $kns02 = $result['kns02'];
            $kns03 = $result['kns03'];
            $modal['combo0'] .= "<OPTION value='$kns02'>$kns03</OPTION>";
        }
        // 選択項目
        $sql = 'select koum03 from koumoku where koum01="従業員" order by koum01;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $wrk[$result['koum03']] = $result['koum03'];
        }
        $wrk[''] = '未選択';
        foreach ($wrk as $key => $val) {
            $combo1 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo1_1'] = "<OPTION value='date'>日付</OPTION>";
        $modal['combo1_2'] = $modal['combo1_3'] = $modal['combo1_4'] = $modal['combo1_n1'] = $modal['combo1_n2'] = $modal['combo1_n3'] = $modal['combo1_n4'] = $combo1;

        // 比較演算子
        $wrk = array('' => '未選択', '==' => 'に等しい', '!=' => 'に等しくない', '>=' => '以上', '<=' => '以下', 'not in' => 'に含まれない', 'in' => 'に含まれる', 'between' => 'の間(以上/以下)', 'not between' => 'の間にない', 'like' => 'の文字を含む');
        foreach ($wrk as $key => $val) {
            $combo2 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo2_1'] = $modal['combo2_2'] = $modal['combo2_3'] = $modal['combo2_4'] = $combo2;

        // AND OR
        $wrk = array('' => '', 'and' => 'かつ', 'or' => '又は');
        foreach ($wrk as $key => $val) {
            $combo3 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo3_1'] = $modal['combo3_2'] = $modal['combo3_3'] = $modal['combo3_4'] = $combo3;

        // カッコ（前）
        $wrk = array('' => '', '(' => '(');
        foreach ($wrk as $key => $val) {
            $combo4 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo4_1'] = $modal['combo4_2'] = $modal['combo4_3'] = $modal['combo4_4'] = $combo4;

        // カッコ（後）
        $wrk = array('' => '', ')' => ')');
        foreach ($wrk as $key => $val) {
            $combo5 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo5_1'] = $modal['combo5_2'] = $modal['combo5_3'] = $modal['combo5_4'] = $combo5;

        // 並び順
        $wrk = array('' => '', 'ASC' => '昇順', 'DESC' => '降順');
        foreach ($wrk as $key => $val) {
            $combo6 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo6_n1'] = $modal['combo6_n2'] = $modal['combo6_n3'] = $modal['combo6_n4'] = $combo6;

        $res[0]['html'] = implode($modal);
        echo json_encode($res);
    }

    // 検索(JS)
    function searchbtn2ClickJs() {
        $this->clearJs();
        $numArray = array();
        for ($i = 0; $i < $this->koumokusu; $i++) {
            $jsString .= "str += '<td><div>' + data[i]['" . $numArray[$i] . "'] + '</div></td>';";
        }
        // 検索PT記録用obj作成
        $this->js1 = " var obj = {";
        for ($i = 1; $i <= 4; $i++) {
            $this->js1 .= "'jyouken$i':$('#jyouken$i').val(),";
            $this->js1 .= "'maekakko$i':$('#maekakko$i').val(),";
            $this->js1 .= "'koumoku$i':$('#koumoku$i').val(),";
            $this->js1 .= "'val$i':$('#val$i').val(),";
            $this->js1 .= "'enzan$i':$('#enzan$i').val(),";
            $this->js1 .= "'atokakko$i':$('#atokakko$i').val(),";
        }
        for ($i = 1; $i <= 4; $i++) {
            $this->js1 .= "'order$i':$('#order$i').val(),";
            $this->js1 .= "'orderopt$i':$('#orderopt$i').val(),";
        }
        $this->js1 = substr($this->js1, 0, -1);
        $this->js1 .= '};';
        $this->js1 .= <<<"__"
        params = JSON.stringify(obj);
        $('#json_data_stock').val(params);
        $('#modalLoader1').modal({backdrop:'static'});
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||null);
            console.log(data);
			$("#tabu1").tabulator("setData", data[0]);
__;
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->js4 = "$('#modal_search').modal('hide'); $('#modalLoader1').modal('hide');";

        $this->addEventListener('#searchbtn2', 'click', 'searchData', 'ajax');
    }

    // 検索データ取得(PHP)
    function searchData($data) {
        $orderby = '';
        for ($i = 1; $i <= 4; $i++) {
            if ($data["order$i"]) {
                $orderby .= $data["order$i"] . ' ' . $data["orderopt$i"] . ',';
            }
        }
        if ($orderby != '') {
            $orderby = 'order by ' . substr($orderby, 0, -1); // 余分カンマ削除
        }

        // 条件式の組み立て
        for ($i = 2; $i <= 4; $i++) {
            if ($data["jyouken$i"] != '' or $i == 1) {
                if ($i != 2) {
                    $sqlwhere .= ' ' . $data["jyouken$i"] . ' ';
                }
                if ($data["enzan$i"] == 'in') {
                    $val = explode(',', $data["val$i"]);
                    $sqlwhere .= $data["maekakko$i"] . 'in_array($data["koumoku$i"],array(';
                    foreach ($val as $sval) {
                        $sqlwhere .= "'$sval',";
                    }
                    $sqlwhere = substr($sqlwhere, 0, -1);
                    $sqlwhere .= '))' . $data["atokakko$i"];
                } elseif ($data["enzan$i"] == 'not in') {
                    $val = explode(',', $data["val$i"]);
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' (';
                    foreach ($val as $sval) {
                        $sqlwhere .= "'$sval',";
                    }
                    $sqlwhere = substr($sqlwhere, 0, -1);
                    $sqlwhere .= ')' . $data["atokakko$i"];
                } elseif ($data["enzan$i"] == 'between') {
                    $val = explode(',', $data["val$i"]);
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val[0]'" . ' and ' . "'$val[1]'" . $data["atokakko$i"];
                } elseif ($data["enzan$i"] == 'not between') {
                    $val = explode(',', $data["val$i"]);
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val[0]'" . ' and ' . "'$val[1]'" . $data["atokakko$i"];
                } elseif ($data["enzan$i"] == 'like') {
                    $val = '%' . $data["val$i"] . '%';
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val'" . $data["atokakko$i"];
                } else {
                    $val = $data["val$i"];
                    $sqlwhere .= $data["maekakko$i"] . $data["koumoku$i"] . ' ' . $data["enzan$i"] . ' ' . "'$val'" . $data["atokakko$i"];
                }
            }
        }
        $par = array($data["val1"], $data["val1"], $data["val1"], $data["val1"]); // 日付
        $sql = "select jyuk01,jyuk02,jyuk03,jyuk04,jyuk05,jyuk06,group_concat(jyui04) as 'key',group_concat(jyui05) as 'val' from jyu_k";
        $sql .= " left join jyu_i on jyuk01=jyui01 and jyui02<='{$data['val1']}' and jyui03>='{$data['val1']}'";
        $sql .= " where jyuk02<='{$data['val1']}' and jyuk03>='{$data['val1']}' group by jyuk01 $orderby;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $jyukkey = array('従業員コード', '入社日', '退職日', '登録日', '契約日', '終了日');
            $jyukval = array($result['jyuk01'], $result['jyuk02'], $result['jyuk03'], $result['jyuk04'], $result['jyuk05'], $result['jyuk06']);
            $jyuikey = explode(',', $result['key']);
            $jyuival = explode(',', $result['val']);

            $jyukey = $jyukkey + $jyuikey;
            $jyuval = $jyukval + $jyuival;
//                $wrk[$i]['nenrei'] = (int) ((date('Ymd')-str_replace('-','',$result['jyuk10']))/10000) . '歳'; // 年齢計算式
            $tabuwrk = array_combine($jyukey, $jyuval); // tabuwrk[従業員CD]={拠点:大阪,姓:山田,名:太郎}
            $tabuwrk['ダミー'] = '　';
            $tabuwrk['姓名'] = $tabuwrk['姓'] . ' ' . $tabuwrk['名']; // 姓名
            $tabuwrk['住所'] = $tabuwrk['都道府県'] . ' ' . $tabuwrk['市区町村'] . ' ' . $tabuwrk['町域'] . ' ' . $tabuwrk['アパート名など']; // 住所
            $tabuwrk['btn'] = '<button style="width:60px;" type="submit" name="act" value="' . $tabuwrk['従業員コード'] . '">' . $tabuwrk['従業員コード'] . '</button><input type="hidden" name="day" value="' . date("Y-m-d") . '">'; // 選択ボタン
            if ($tabuwrk['生年月日'] != '') {
                $tabuwrk['年齢'] = (int) ((date('Ymd') - str_replace('-', '', $tabuwrk['生年月日'])) / 10000) . '歳'; // 年齢計算式
            }

            /*            if($data['enzan2'] == '')
              {
              if($tabuwrk[$data['koumoku2']] == $data['val2'])
              {
              $tabudata[] = $tabuwrk;
              }
              }
             */
            //$wrk[$i]['jyuk0102'] = $this->format($result['jyuk01'],'date-').'～'.$this->format($result['jyuk02'],'date-'); // 適用期間
            $tabudata[] = $tabuwrk;
        }
        $tabudata = $this->tabulateData($tabudata);

//        $res[0]['html'] = '';
//        $res[0]['sql'] = $sql;
        $res[0] = $tabudata;
        echo json_encode($res);
    }

    // パターン保存ボタンクリック
    function patcrtbtn1ClickJs() {
        $this->clearJs();
        // 検索PT記録用obj作成
        $jsString = '';
        for ($i = 1; $i <= 4; $i++) {
            $jsString .= "jyouken$i:$('#jyouken$i').val(),";
            $jsString .= "maekakko$i:$('#maekakko$i').val(),";
            $jsString .= "koumoku$i:$('#koumoku$i').val(),";
            $jsString .= "val$i:$('#val$i').val(),";
            $jsString .= "enzan$i:$('#enzan$i').val(),";
            $jsString .= "atokakko$i:$('#atokakko$i').val(),";
        }
        for ($i = 1; $i <= 4; $i++) {
            $jsString .= "order$i:$('#order$i').val(),";
            $jsString .= "orderopt$i:$('#orderopt$i').val(),";
        }
        $jsString = substr($jsString, 0, -1);

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
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#patcrtbtn1', 'click', 'createPattern', 'ajax');
    }

    // 新規パターン保存(PHP)
    function createPattern($data) {
        $err = '';
        if ($data['ptnm']) {
            $today = date('Ymd');
            $now = date('Hi');
            $wrkcd = '';
            $par = array($_SESSION['id']);
            $sql = "select max(knsk02)+1 as ptcd from kensakuptka where knsk01=? group by knsk01;";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $wrkcd = $result['ptcd'];
            }
            if ($wrkcd == '') {
                $wrkcd = 1;
            }
            $par = array();
            $sql = "insert into kensakuptka (";
            for ($i = 1; $i <= 4; $i++) {
                $a = sprintf('%02d', $i);
                $sql .= "knsk" . $a . ",";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for ($i = 1; $i <= 4; $i++) {
                $sql .= "?,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",?,?,?,?,?,?);";
            array_push($par, $_SESSION['id'], $wrkcd, $data["ptnm"], $data["ptdef"], $today, $now, $_SESSION['id'], $today, $now, $_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            $modal = $this->readModalSource('modal_n');
            if ($wrk[0]['res']) {
                $modal['body'] = '<div>正常に保存されました</div>';
            } else {
                $modal['body'] = '<div>エラーが発生しました</div>';
            }
        } else {
            $wrk[0]['res'] = false;
            $modal['body'] = '<div>パターン名を入力して下さい</div>';
        }
        $wrk[0]['html'] = implode($modal);

        echo json_encode($wrk);
    }

    // パターン取得・更新
    function patternListUpdateJs() {
        $this->clearJs();
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#kpcombo option').remove();
            $('#kpcombo').append(data[0]["html"]);
__;
        $this->addEventListener('', 'wait', 'patternListUpdate', 'ajax');
    }

    //
    function patternListUpdate($data) {
        $wrk[0]['html'] = "<OPTION value=''>未選択</OPTION>";
        $sql = "select * from kensakuptka where knsk01=? order by knsk02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $knsk02 = $result['knsk02'];
            $knsk03 = $result['knsk03'];
            $wrk[0]['html'] .= "<OPTION value='$knsk02'>$knsk03</OPTION>";
        }
        echo json_encode($wrk);
    }

    // パターン変更
    function kpcomboSelectJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"knsk02":$('#kpcombo').val()};
            params = JSON.stringify(obj);
__;
        for ($i = 1; $i < 5; $i++) {
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
        $this->addEventListener('#kpcombo', 'change', 'patternRead', 'ajax');
    }

    //
    function patternRead($data) {
        $sql = "select * from kensakuptka where knsk01=? and knsk02=?;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id'], $data['knsk02']);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $knsk04 = $result['knsk04'];
        }
        $wrk[0]['str'] = $knsk04;
        echo json_encode($wrk);
    }

    // パターン削除前(JS)
    function patdelbtn1ClickJs() {
        $this->clearJs();

        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
			$('#modal_pd').remove();
			$('#modalParent2').append(data[0]['html']);
			$('#modal_pd').modal({backdrop:'static'});            
__;
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#patdelbtn1', 'click', 'patdelmodal', 'ajax');
    }

    // 
    function patdelmodal($data) {
        $modal = $this->readModalSource('modal_pd');
        $res[0]['html'] = $modal;
        echo json_encode($res);
    }

    // パターン削除(JS)
    function patdelbtn2ClickJs() {
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
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#patdelbtn2', 'click', 'patdeleteData', 'ajax');
    }

    // パターン削除(PHP)
    function patdeleteData($data) {
        $today = date('Ymd');
        $now = date('Hi');
        $sql = "delete from kensakuptka where (knsk01=? and knsk02=?);";
        $par = array($_SESSION['id'], $data['knsk02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
    }

    // 郵便番号から住所を取得(JS)
    function 郵便番号KeyupJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        if(event.keyCode != 13){
            return false;
        }else if($('#郵便番号').val().length >= 7 && $('#郵便番号').val().length <= 8 && $('#都道府県').val() == ''){
            var obj = {"zip":$('#郵便番号').val()};
            params = JSON.stringify(obj);
        }
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
				$('#都道府県').val(data[0]['ken_name']);
            	$('#市区町村').val(data[0]['city_name']);
            	$('#町域').val(data[0]['town_name']);
//          	console.log(data);
__;
        $this->addEventListener('#郵便番号', 'keyup', 'getZipData', 'ajax');
    }

    function getZipData($data) {
        if (strlen($data['zip']) == 7) {
            $data['zip'] = substr($data['zip'], 0, 3) . '-' . substr($data['zip'], 3, 4);
        }
        $sql = "select * from ad_address where zip = ? and delete_flg != 1 limit 1";
        $par = array($data['zip']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
    }

    // 銀行名から銀行コードを取得(JS)
    function ginkoKeyupJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        	var ary = $('#ginko').val().split('.#');
			if(ary.length == 2){
				$('#口座＿銀行コード').val(ary[1]);
			}else{
				$('#口座＿銀行コード').val('');
			}
            if(event.keyCode != 13){
                return false;
            }else if($('#口座＿銀行コード').val().length = 4){
                var obj = {"gink01":$('#口座＿銀行コード').val()};
                params = JSON.stringify(obj);
            }
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
        	$('.tsArea5').TapSuggest({
            	tsInputElement : '#shiten',
            	tsArrayList : data[5],
            	tsRegExpAll : true
            });
            console.log(data[5]);
__;
        $this->addEventListener('#ginko', 'keyup', 'getBranchOfficeData', 'ajax');
    }

    function getBranchOfficeData($data) {
        if (strlen($data['gink01']) == 4) {
            $sql = "select * from shiten where gsit01 = ?";
            $par = array($data['gink01']);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $kana = mb_convert_kana($result['gsit03'], "KV"); // 半カナ→全カナ
                $disp = $result['gsit04'] . '支店.#' . $result['gsit02'];
                $res[5][] = array($disp, $kana);
            }
        }
        echo json_encode($res);
    }

    // 支店名から支店コード確定(JS)
    function shitenKeyupJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        	var ary = $('#shiten').val().split('.#');
			if(ary.length == 2){
				$('#口座＿支店コード').val(ary[1]);
			}else{
				$('#口座＿支店コード').val('');
			}
__;
        $this->js2 = <<<"__"
__;
        $this->addEventListener('#shiten', 'keyup', '', '');
    }

    // 部門名から部門コード確定(JS)
    function 所属部署コードsKeyupJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        	var ary = $('#所属部署コードs').val().split('.#');
			if(ary.length == 2){
				$('#所属部署コード').val(ary[1]);
			}else{
				$('#所属部署コード').val('');
			}
__;
        $this->js2 = <<<"__"
__;
        $this->addEventListener('#所属部署コードs', 'keyup', '', '');
    }
    // 部門名から部門コード確定(JS)
    function 国籍sKeyupJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        	var ary = $('#国籍s').val().split('.#');
			if(ary.length == 2){
				$('#国籍').val(ary[1]);
			}else{
				$('#国籍').val('');
			}
__;
        $this->js2 = <<<"__"
__;
        $this->addEventListener('#国籍s', 'keyup', '', '');
    }
    
}

$p = new page();

$data['pr1'] = array('title' => '従業員登録'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
$data['pr3'] = array('active' => 'マスター'); // ナビメニュー

//var_dump($p);
loadResource($p,$data);