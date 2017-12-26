<?php
// ** 稟議起案 ** //

// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');
require_once('incl/password.php');

class page extends core {
	
    // JS初期処理
    function initJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
        // テンプレサンプルデータ削除
        $("#tablebody tr").remove();
        $("#tablebody td").remove();

       // 検索条件初期値設定
        var json_data_stock_obj = {"jyouken1":"","maekakko1":"","koumoku1":"ring01","val1":"0","enzan1":">=","atokakko1":"","jyouken2":"","maekakko2":"","koumoku2":"","val2":"","enzan2":"","atokakko2":"","jyouken3":"","maekakko3":"","koumoku3":"","val3":"","enzan3":"","atokakko3":"","jyouken4":"","maekakko4":"","koumoku4":"","val4":"","enzan4":"","atokakko4":"","order1":"ring01","orderopt1":"DESC","order2":"","orderopt2":"","order3":"","orderopt3":"","order4":"","orderopt4":""};
        $('#json_data_stock').val(JSON.stringify(json_data_stock_obj));
       $('#searchbtn2').trigger('click');
       
       // ヒントの表示
       var hintObj = {
       "":"　",
       "ring01":"稟議番号",
       "ring02":"起案日（yyyymmdd）",
       "ring03":"承認ルートCD",
       "ring04":"起案者CD",
       "ring05":"タイトル",
       "ring06":"内容",
       "ring07":"支払区分",
       "ring08":"金額",
       "ring09":"承認希望時期",
       "ring10":"",
       "ring11":"",
       "ring12":"",
       "ring13":"",
       "ring14":"",
       "ring15":"承認者CD",
       "ring16":"未承認者CD",
       "ring17":"現在承認者CD",
       "ring18":"現在承認者名",
       "ring19":"承認URLパスワード",
       "ring20":"コメント"
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
		
        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function () {
            FixedMidashi.create();
        });
        
__;
        $this->addEventListener('','','','');
    }
    
    // a新規モーダル表示(JS)
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
    // a新規モーダル表示(PHP)
    function modalCallCr($data){
        $modal = $this->readModalSource('modal_c');
        $res[0]['html'] = implode($modal);
        echo json_encode($res);
        exit();
    }

    // a更新・削除モーダル表示(JS)
    function button1nmClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"ring01":event.getAttribute('value')};
            //console.log(obj);
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            $('#modal_ud').remove();
            $('#modalParent1').append(data[0]["html"]);
            $('#ring03ud').val(data[1][3]);
            $('#ring07ud').val(data[1][7]);
            $('#modal_ud').modal({backdrop:'static'});
__;
        $this->addEventListener('','wait','modalCallUd','ajax');
    }
    // a更新・削除モーダル表示(PHP)
    function modalCallUd($data){
        $modal = $this->readModalSource('modal_ud');
        $sql = 'select * from ringi where ring01=?;';
        $par = array($data['ring01']);
    	$stmt = $this->db->prepare($sql);
    	$stmt->execute($par);
    	while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $modal['ring01ud'] = $this->h($result['ring01']); // 稟議番号
            $modal['ring02ud'] = $this->format($result['ring02'],'date-'); // 起案日
        	$res[1][3]         = $this->h($result['ring03']); // 承認ルート
		$modal['ring04ud'] = $this->h($result['ring04']); // 起案者CD
		$modal['ring05ud'] = $this->h($result['ring05']); // タイトル
            $modal['ring06ud'] = $this->h($result['ring06']); // 内容
            // 改行コードのみエスケープしたのを戻す
            $modal['ring06ud'] = str_replace('&amp;#13;','&#13;',$modal['ring06ud']);
			$res[1][7]         = $this->h($result['ring07']);
            $modal['ring08ud'] = $this->h($result['ring08']);
            $modal['ring09ud'] = $this->format($result['ring09'],'date-'); // 承認希望時期
            //$modal['ring10ud'] = $result['ring10'];
            //$modal['ring11ud'] = $result['ring11'];
            //$modal['ring12ud'] = $result['ring12'];
        	//$modal['ring13ud'] = $result['ring13'];
			//$modal['ring14ud'] = $result['ring14'];
            $syowrk = json_decode($result['ring15'],true); // 承認者情報 (JSONenc)
            $wrk = '';
            for($i=0; $i < count($syowrk); $i++){
                $wrk .= ' → 【';
                $wrk .= $this->h($syowrk[$i]['name']);
                $wrk .= '】';
            }
			$modal['ring15ud'] = $wrk;
            
            $syowrk = json_decode($result['ring16'],true); // 未承認者情報 (JSONenc)
            $wrk = '';
            for($i=0; $i < count($syowrk); $i++){
                $wrk .= ' → 【';
                $wrk .= $this->h($syowrk[$i]['name']);
                $wrk .= '】';
            }
            $modal['ring16ud'] = $wrk;

			$modal['ring17ud'] = $result['ring17'];
			$modal['ring18ud'] = $result['ring18'];
//            $modal['ring19ud'] = $this->h($result['ring19']); // 承認URLパスワード
            $modal['ring20ud'] = $this->h($result['ring20']); // コメント(1000)
            // 改行コードのみエスケープしたのを戻す
            $modal['ring20ud'] = str_replace('&amp;#13;','&#13;',$modal['ring20ud']);
			if($result['ring17']){
				$modal['teisyutsu'] = '提出取消';
				$modal['delbtn_sts'] = 'disabled';
				$modal['upbtn1_sts'] = 'disabled';
			}else{
				$modal['teisyutsu'] = '提出';
			}
		}
        // 添付ファイルの一覧取得
        $tlist = '';
        $dir = "/var/www/doc/rin/{$data['ring01']}/";
        $handle = opendir($dir) or exit('NG');
        $wrkary = array();
        while ($fileName = readdir($handle)) {
            if(is_file($dir . $fileName)){
                $wrkary[] = $fileName;
            }
        }
        closedir($handle);
        foreach ($wrkary as $key=>$value) {
            $tlist .= '<a href="#" onclick="fileDownload(this);" val="'. htmlspecialchars($value) .'">' . htmlspecialchars($value) . "</a><br>";
        }
        
        $modal['ring00ud'] = $tlist; // 添付ファイルリンク

        $res[0]['html'] = implode("", $modal);
        $res[0]['ka'] = $syowrk;
		echo json_encode($res);
    }
    // aファイルダウンロード(JS)
    function fileDownloadJs(){
        $this->js1 = <<<"__"
            var obj = {'fname':event.getAttribute('val'),'ring01':$('#ring01ud').val()};
            params = JSON.stringify(obj);
//			console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            window.open('fdl-nfaujfaoqpdkfnzxkjfe62jdfa.php', '_blank');
//            console.log(data);
__;
        $this->js3 =  <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener('','wait','fileDownload','ajax');
    }
    // aファイルダウンロード(PHP)
    function fileDownload($data){
        $dir = "/var/www/doc/rin/{$data['ring01']}/";
        $_SESSION['download_file'] = $data['fname'];
        $_SESSION['download_directory'] = $dir;
        exit();
    }

    // a更新(JS)
    function upbtn1ClickJs(){
        $this->js1 = <<<"__"
		var fd = new FormData($('#formud1').get(0));
        	//fd.append('name', 'value');
        	fd.append('ring01', $('#ring01ud').val());
        	fd.append('ring02', $('#ring02ud').val());
        	fd.append('ring03', $('#ring03ud').val());
        	fd.append('ring04', $('#ring04ud').val());
        	fd.append('ring05', $('#ring05ud').val());
        	fd.append('ring06', $('#ring06ud').val());
        	fd.append('ring07', $('#ring07ud').val());
        	fd.append('ring08', $('#ring08ud').val());
        	fd.append('ring09', $('#ring09ud').val());
        	fd.append('ring10', $('#ring10ud').val());
        	fd.append('ring11', $('#ring11ud').val());
        	fd.append('ring12', $('#ring12ud').val());
        	fd.append('ring13', $('#ring13ud').val());
        	fd.append('ring14', $('#ring14ud').val());
        	fd.append('ring15', $('#ring15ud').val());
        	fd.append('ring16', $('#ring16ud').val());
        	fd.append('ring17', $('#ring17ud').val());
        	fd.append('ring18', $('#ring18ud').val());
        	fd.append('ring19', $('#ring19ud').val());
        	fd.append('ring20', $('#ring20ud').val());
		fd.append('fileupud', $('#fileupud').val());
        	fd.append('lab01', $('#lab01ud').val());
        	fd.append('lab02', $('#lab02ud').val());
        	fd.append('lab03', $('#lab03ud').val());
        	fd.append('lab04', $('#lab04ud').val());
        	fd.append('lab05', $('#lab05ud').val());
        	fd.append('lab06', $('#lab06ud').val());
        	fd.append('lab07', $('#lab07ud').val());
        	fd.append('lab08', $('#lab08ud').val());
        	fd.append('lab09', $('#lab09ud').val());
        	fd.append('lab10', $('#lab10ud').val());
        	fd.append('lab11', $('#lab11ud').val());
        	fd.append('lab12', $('#lab12ud').val());
        	fd.append('lab13', $('#lab13ud').val());
        	fd.append('lab14', $('#lab14ud').val());
        	fd.append('lab15', $('#lab15ud').val());
        	fd.append('lab16', $('#lab16ud').val());
        	fd.append('lab17', $('#lab17ud').val());
        	fd.append('lab18', $('#lab18ud').val());
        	fd.append('lab19', $('#lab19ud').val());
        	fd.append('lab20', $('#lab20ud').val());
		console.log($('#fileupud').val());
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
        $this->addEventListener('#upbtn1','click','updateData','ajax_f');
    }
    // a更新(PHP)
    function updateData($data) {
        $today = date('Ymd');
        $now = date('Hi');

        $data = $_POST;
        $data['ring02'] = str_replace('-', '', $data['ring02']);
        $data['ring09'] = str_replace('-', '', $data['ring09']);

        // エラーチェック
        $this->err = '';

        //if(!$this->err){$this->validate($data['ring01'] ,'str',  array(0,20,false), $data['lab01']);} // 稟議番号(自動)
        //if(!$this->err){$this->validate($data['ring02'] ,'date', array(0,8,false), $data['lab02']);} // 起案日
        //if(!$this->err){$this->validate($data['ring04'] ,'str',  array(1,10,true), $data['lab04']);} // 起案者CD
        if (!$this->err) { // 承認ルート
            $this->validate($data['ring03'], 'str', array(1, 2, true), $data['lab03']);
        }
        if (!$this->err) { // タイトル
            $this->validate($data['ring05'], 'str', array(1, 100, true), $data['lab05']);
        }
        if (!$this->err) { // 内容
            $this->validate($data['ring06'], 'str', array(1, 500, true), $data['lab06']);
        }
        if (!$this->err) { // 支払種別
            $this->validate($data['ring07'], 'str', array(1, 10, true), $data['lab07']);
        }
        if (!$this->err) { // 金額
            $this->validate($data['ring08'], 'str', array(1, 10, true), $data['lab08']);
        }
        if (!$this->err) { // 承認希望時期
            $this->validate($data['ring09'], 'date', array(0, 8, false), $data['lab09']);
        }
//        if(!$this->err){$this->validate($data['ring10'] ,'str',  array(0,0,false), $data['lab10']);} // 
//        if(!$this->err){$this->validate($data['ring11'] ,'str',  array(0,0,false),  $data['lab11']);} // 
//        if(!$this->err){$this->validate($data['ring12'] ,'str',  array(0,0,false),  $data['lab12']);} // 
//        if(!$this->err){$this->validate($data['ring13'] ,'str',  array(0,0,false), $data['lab13']);} // 
//        if(!$this->err){$this->validate($data['ring14'] ,'str',  array(0,0,false),  $data['lab14']);} // 
//        if(!$this->err){$this->validate($data['ring15'] ,'str',  array(3,300,true),  $data['lab15']);} // 承認者
//        if(!$this->err){$this->validate($data['ring16'] ,'str',  array(3,300,true),  $data['lab16']);} // 未承認者
//        if(!$this->err){$this->validate($data['ring17'] ,'str',  array(0,10,false), $data['lab17']);} // 
//        if(!$this->err){$this->validate($data['ring18'] ,'str',  array(0,50,false), $data['lab18']);} // 
//        if(!$this->err){$this->validate($data['ring19'] ,'str',  array(0,100,false), $data['lab19']);} // 承認URLパスワード 
//        if(!$this->err){$this->validate($data['ring20'] ,'str',  array(0,500,false), $data['lab20']);} // コメント（蓄積）		
        // 稟議状態の確認
        $sql = "select ring17 from ringi where ring01=?;";
        $par = array($data['ring01']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($result['ring17'] != '') { // 提出済み
                $this->err = '提出済みデータは更新できません。変更が必要であれば取消を実行して下さい。';
            }
        }

        // 添付ファイルアップロードしなおし
        if (!$this->err and $data['fileupud']) {
            $res = true;
            $dir = "/var/www/doc/rin/{$data['ring01']}/";
            $handle = opendir($dir) or exit('NG');
            $wrkary = array();
            while ($fileName = readdir($handle)) {
                if (is_file($dir . $fileName)) {
                    $wrkary[] = $fileName;
                }
            }
            closedir($handle);
            foreach ($wrkary as $key => $value) {
                $res = unlink($dir . $value); // ファイル全削除
            }

            if ($res and isset($_FILES["fileud1"]["tmp_name"])) {
                $dir = "/var/www/doc/rin/{$data['ring01']}";
                if (!file_exists($dir)) {
                    mkdir($dir); // 稟議番号フォルダ作成
                    chmod($dir, 0777); // パーミッション変更
                }
                $wrk[0]['res'] = true;
                for ($i = 0; $i < count($_FILES["fileud1"]["tmp_name"]); $i++) {
                    $file_tmp = $_FILES["fileud1"]["tmp_name"][$i]; // 一時アップロード先ファイルパス
                    $file_save = $dir . "/" . $_FILES["fileud1"]["name"][$i]; // 正式保存先ファイルパス
                    $result = @move_uploaded_file($file_tmp, $file_save); // ファイル移動
                    if ($result == false) {
                        $this->err = $data['fileud'] . 'アップロードに失敗しました';
                    }
                }
            } else {
                $this->err = '以前の添付データを削除できません。システム管理者に連絡して下さい。';
            }
        }

        if ($this->err) {
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>' . $this->err . '</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }

        // 稟議承認者情報取得
        $sql = "select jyuk01,jyuk04,jyuk05,jyuc05 from jyu_c left join jyu_k";
        $sql .= " on jyuk01 = jyuc01 and jyuk09 = 'name' and jyuk02 <= ? and jyuk03 >= ?";
        $sql .= " where jyuc04='kengen1' and jyuc02 <= ? and jyuc03 >= ? and (jyuc05 = ? or jyuc05 = ?);";
        $stmt = $this->db->prepare($sql);
        $wcd = explode('[', $_SESSION['拠点']);
        $kyocd = substr($wcd[1], 0, -1); // 拠点CD
        $par = array($today, $today, $today, $today, $kyocd, 'L0-1'); // L0-1=全社(社長)
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($result["jyuc05"] == $kyocd) {
                $id_1 = $result["jyuk01"];
                $name_1 = $result["jyuk04"] . ' ' . $result["jyuk05"];
            } else {
                $id_2 = $result["jyuk01"];
                $name_2 = $result["jyuk04"] . ' ' . $result["jyuk05"];
            }
        }
        // 承認者メール取得
        $sql = "select jyuc01,jyuc05 from jyu_c";
        $sql .= " where jyuc04='kmail' and jyuc02 <= ? and jyuc03 >= ? and (jyuc01 = ? or jyuc01 = ?);";
        $stmt = $this->db->prepare($sql);
        $wcd = explode('[', $_SESSION['拠点']);
        $kyocd = substr($wcd[1], 0, -1); // 拠点CD
        $par = array($today, $today, $id_1, $id_2);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($result["jyuc01"] == $id_1) {
                $mail_1 = $result["jyuc05"];
            } else {
                $mail_2 = $result["jyuc05"];
            }
        }

        if ($data['ring03'] == 'l1') {
            $syowrk['id'] = $id_1;
            $syowrk['name'] = $name_1;
            $syowrk['mail'] = $mail_1;
            $syinfo[] = $syowrk;
        } elseif ($data['ring03'] == 'l2') {
            $syowrk['id'] = $id_2;
            $syowrk['name'] = $name_2;
            $syowrk['mail'] = $mail_2;
            $syinfo[] = $syowrk;
        } elseif ($data['ring03'] == 'l3') {
            $syowrk['id'] = $id_1;
            $syowrk['name'] = $name_1;
            $syowrk['mail'] = $mail_1;
            $syinfo[] = $syowrk;
            $syowrk['id'] = $id_2;
            $syowrk['name'] = $name_2;
            $syowrk['mail'] = $mail_2;
            $syinfo[] = $syowrk;
        }
        $syouninsya = json_encode($syinfo, JSON_UNESCAPED_UNICODE);

        // 更新日に合わせて強制的に上書する項目
        $data['ring15'] = $syouninsya;
        $data['ring16'] = $syouninsya;
        $data['ring02'] = $today;


        // update実行
        if (!$this->err) {
            $par = array();
            $today = date('Ymd');
            $now = date('Hi');
            $sql = "update ringi set ";
            for ($i = 1; $i <= 20; $i++) {
                if ($i == 2 or $i == 3 or $i == 5 or $i == 6 or $i == 7 or $i == 8 or $i == 9 or $i == 15 or $i == 16) {
                    $a = sprintf('%02d', $i);
                    $sql .= "ring" . $a . "=?,";
                }
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",uday=?,utim=?,uid=? where ring01=?;";
            for ($i = 1; $i <= 20; $i++) {
                if ($i == 2 or $i == 3 or $i == 5 or $i == 6 or $i == 7 or $i == 8 or $i == 9 or $i == 15 or $i == 16) {
                    $a = sprintf('%02d', $i);
                    $par[] = $data["ring$a"];
                }
            }
            array_push($par, $today, $now, $_SESSION['従業員コード'], $data['ring01']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        }
        exit();
    }

    // a提出・取消準備(JS)
    function createbtn3ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {'kubun':$('#ring17ud').val()};
            params = JSON.stringify(obj);
            //console.log(params);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
			$('#modal_cl').remove();
			$('#modalParent2').append(data[0]['html']);
			$('#modal_cl').modal({backdrop:'static'});
__;
        $this->js3 =  <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#createbtn3','click','createLockDataModal','ajax');
    }
    // 
    function createLockDataModal($data){
		$modal = $this->readModalSource('modal_cl');
        if($data['kubun'] != ''){
            $modal['kakunin'] = "提出した稟議を取り消しますか？<br>（承認完了者と現在の承認者に通知メールが送信されます）"; // 提出取消処理
        }else{
            $modal['kakunin'] = "提出しますか？<br>（内容変更した場合、更新ボタンを押していなければ変更は破棄されます。承認者に通知メールが送信されます。）"; // 提出処理
        }
        $res[0]['html'] = implode($modal);
        
        echo json_encode($res);
        exit();
    }
    // a提出・取消(JS)
    function createbtn4ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {'ring01':$('#ring01ud').val(),'ring17':$('#ring17ud').val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#modal_ud').modal('hide');
                $('#modal_cl').modal('hide');
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
        $this->addEventListener('#createbtn4','click','createLockData','ajax');
    }
    // a提出・取消(PHP)
    function createLockData($data){
		$this->err = '';
        // 稟議状態の確認
		$sql = "select * from ringi where ring01=?;";
        $par = array($data['ring01']);
		$stmt = $this->db->prepare($sql);
		$stmt->execute($par);
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $ring01 = $this->h($result['ring01']); // 稟議番号
            $ring02 = $this->format($result['ring02'],'date-'); // 起案日
        	$ring03 = $this->h($result['ring03']); // 承認ルート
			$ring04 = $this->h($result['ring04']); // 起案者CD
			$ring05 = $this->h($result['ring05']); // タイトル
            $ring06 = $this->h($result['ring06']); // 内容
			$ring07 = $this->h($result['ring07']); // 金額種別
            $ring08 = $this->h($result['ring08']); // 金額
            $ring09 = $this->format($result['ring09'],'date-'); // 承認希望時期
            //$ring10 = $result['ring10'];
            //$ring11 = $result['ring11'];
            //$ring12 = $result['ring12'];
        	//$ring13 = $result['ring13'];
			//$ring14 = $result['ring14'];
			$ring15 = $result['ring15']; // 承認ルート
			$ring16 = $result['ring16']; // 未承認者
			$ring17 = $result['ring17'];
			$ring18 = $this->h($result['ring18']);
//            $ring19 = $this->h($result['ring19']); // 承認URLパスワード
            $ring20 = $this->h($result['ring20']); // コメント(1000)

            if($ring17){
				$teisyutsu = '提出取消';
			}else{
				$teisyutsu = '提出';
			}
            // 改行コードのみエスケープしたのを戻す
            $ring06 = str_replace('&amp;#13;','&#13;',$ring06);
            $ring20 = str_replace('&amp;#13;','&#13;',$ring20);
            
            $ring15jsn = json_decode($ring15,true); // 承認者情報 (JSONenc)
            $wrkstr = '';
            for($i=0; $i < count($ring15jsn); $i++){
                $wrkstr .= ' → 【';
                $wrkstr .= $this->h($ring15jsn[$i]['name']);
                $wrkstr .= '】';
            }
            $ring15disp = $wrkstr;
            
            $ring16jsn = json_decode($ring16,true); // 未承認者情報 (JSONenc)
            $wrkstr = '';
            for($i=0; $i < count($ring16jsn); $i++){
                $wrkstr .= ' → 【';
                $wrkstr .= $this->h($ring16jsn[$i]['name']);
                $wrkstr .= '】';
            }
            $ring16disp = $wrkstr;
        }
        
		// エラーチェック
        if($ring17 != $data['ring17']){
            $this->err = '編集中、他者によりデータが変更されました。始めからやり直して下さい。';
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
		if($data['ring17']){ // 提出済みの場合　→　提出取消
            $par = array($ring15,$today,$now,$_SESSION['id'],$ring01);
            $sql = "update ringi set ring16=?,ring17='',ring18='',uday=?,utim=?,uid=? where ring01=?";
		}else{
            $par = array($ring15jsn[0]['id'],$ring15jsn[0]['name'],$today,$now,$_SESSION['id'],$ring01);
            $sql = "update ringi set ring17=?,ring18=?,uday=?,utim=?,uid=? where ring01=?";
		}
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
		if($wrk[0]['res']){
            if(!$ring17){ // 未提出の場合、現在承認者を設定
                $ring17 = $ring15jsn[0]['id'];
                $ring18 = $ring15jsn[0]['name'];
            }
            if($ring07 == '1'){
                $ring07disp = '円';
            }elseif($ring07 == 'm'){
                $ring07disp = '円／月';
            }elseif($ring07 == 'd'){
                $ring07disp = '円／日';
            }elseif($ring07 == 'w'){
                $ring07disp = '円／週';
            }
            // メール送信先の判定（承認済み+現在承認者）
            $array15=array();
            $array16=array();
            foreach($ring15jsn as $val){
                $array15[] = $val['mail'];
                if($val['id'] == $ring17){
                    $to[] = $val['mail']; // 現在の承認者
                }
            }
            foreach($ring16jsn as $val){
                $array16[] = $val['mail'];
            }
            $syouninzumi = array_diff($array15, $array16);
            foreach($syouninzumi as $val){
                $to[] = $val; // 承認完了者
            }
/////////////////////////////////////////////////
            $toss = $to;
            foreach($toss as $val){$todum .= $val . ', ';}
            $to = 'fujii@kawai-g.com';
/////////////////////////////////////////////////
            $cc = '';//array('fujii@kawai-g.com','adgjmptw0a0a@gmail.com');
            $bcc = '';//'fujii@kawai-g.com';
		}
        if($data['ring17']){ // 提出取消　→　承認した人と現在の承認者にメール
            $title = "【通知】稟議No.{$ring01} 取り消し";
            $content = <<<"__"
to : {$todum}
下記稟議が取り消されましたのでお知らせ致します。
-----------------------------------------
No.{$ring01}　　　起案日：{$ring02}　　　承認希望時期：{$ring09}
承認ルート：{$ring15disp}
未承認　　：{$ring16disp}
タイトル　：{$ring05}
金額　　　：{$ring08}{$ring07disp}
{$ring06}
*** コメント ***************
{$ring20}
-----------------------------------------
__;
        }else{ // 提出　→　現在の承認者にメール
            $title = "【依頼】稟議No.{$ring01} 承認処理";
            $content = <<<"__"
to : {$todum}
下記稟議が起案されました。ご確認をお願い致します。
ポータルURL：https://ik1-302-11462.vs.sakura.ne.jp/bootstrap/docs/examples/test2/login.php

-----------------------------------------
No.{$ring01}　　　起案日：{$ring02}　　　承認希望時期：{$ring09}
承認ルート：{$ring15disp}
未承認　　：{$ring16disp}
タイトル　：{$ring05}
金額　　　：{$ring08}{$ring07disp}
{$ring06}
-----------------------------------------
__;
        }
        $wrk[0]['mail'] = $this->sendMail($to, $cc, $bcc, $title, $content);
        $wrk[0]['ary1'] = $array1; // 承認者
        $wrk[0]['ary2'] = $array2; // 未承認の人
        $wrk[0]['sa'] = $sa; // 承認した人
        $wrk[0]['toss'] = $toss; // 承認した人
        
        echo json_encode($wrk);
        exit();
    }
    
    // a削除準備(JS)
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
    // a削除(JS)
    function delbtn2ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {'ring01':$('#ring01ud').val()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
				$('#searchbtn2').trigger('click');
				$('#modal_d').modal('hide');
            	$('#modal_ud').modal('hide');
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
    // a削除(PHP)
    function deleteData($data){
        // 稟議状態の確認
		$sql = "select ring17 from ringi where ring01=?;";
        $par = array($data['ring01']);
		$stmt = $this->db->prepare($sql);
		$stmt->execute($par);
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            if($result['ring17'] != ''){ // 提出済み
				$this->err = '提出済みデータは削除できません。削除が必要であれば取消を実行して下さい。';
			}
        }
        // 添付ファイル削除
        if(!$this->err){
			$res = true;
        	$dir = "/var/www/doc/rin/{$data['ring01']}/";
        	$handle = opendir($dir) or exit('NG');
        	$wrkary = array();
        	while ($fileName = readdir($handle)) {
            	if(is_file($dir . $fileName)){
                	$wrkary[] = $fileName;
            	}
        	}
        	closedir($handle);
        	foreach ($wrkary as $key=>$value) {
				$res = unlink($dir.$value); // ファイル全削除
			}
			if($res){
				rmdir(substr($dir,0,-1));
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
        $sql = "delete from ringi where ring01=?;";
        $par = array($data['ring01']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }

    // a新規作成(保存)+ファイルアップロード(JS)
    function createbtn2ClickJsJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
        var fd = new FormData($('#formc1').get(0));
        //fd.append('name', 'value');
        fd.append('ring03', $('#ring03c').val());
        fd.append('ring05', $('#ring05c').val());
        fd.append('ring06', $('#ring06c').val());
        fd.append('ring07', $('#ring07c').val());
        fd.append('ring08', $('#ring08c').val());
        fd.append('ring09', $('#ring09c').val());
        fd.append('lab03', $('#lab03c').val());
        fd.append('lab05', $('#lab05c').val());
        fd.append('lab06', $('#lab06c').val());
        fd.append('lab07', $('#lab07c').val());
        fd.append('lab08', $('#lab08c').val());
        fd.append('lab09', $('#lab09c').val());        
__;
        $this->js2 = <<<"__"
            //alert(data);
            var data = JSON.parse(json_data||"null");
            console.log(data);
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
            console.log(data);
            $('[name=button2]').prop("disabled",false);
            //alert('エラーが発生しました。');
__;
        $this->addEventListener('#createbtn2','click','createDataUp','ajax_f');
    }
    // a新規作成(保存)+ファイルアップロード(PHP)
    function createDataUp($data){
        $this->err = '';
        $today = date('Ymd');
        $now = date('Hi');

		// 受信データの加工
        $data = $_POST;
        $data['ring09'] = str_replace('-','',$data['ring09']);
        
        // 所属拠点長
        $sql = "select jyuk01,jyuk04,jyuk05,jyuc05 from jyu_c left join jyu_k";
        $sql .= " on jyuk01 = jyuc01 and jyuk09 = 'name' and jyuk02 <= ? and jyuk03 >= ?";
        $sql .= " where jyuc04='kengen1' and jyuc02 <= ? and jyuc03 >= ? and (jyuc05 = ? or jyuc05 = ?);";
        $stmt = $this->db->prepare($sql);
		$wcd = explode('[',$_SESSION['拠点']);
		$kyocd = substr($wcd[1],0,-1); // 拠点CD
		$par = array($today,$today,$today,$today,$kyocd,'L0-1'); // L0-1=全社(社長)
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			if($result["jyuc05"] == $kyocd){
				$id_1   = $result["jyuk01"];
            	$name_1 = $result["jyuk04"] . ' ' .$result["jyuk05"];
			}else{
				$id_2   = $result["jyuk01"];
            	$name_2 = $result["jyuk04"] . ' ' .$result["jyuk05"];
			}
        }
        // 承認者メール取得
        $sql = "select jyuc01,jyuc05 from jyu_c";
        $sql .= " where jyuc04='kmail' and jyuc02 <= ? and jyuc03 >= ? and (jyuc01 = ? or jyuc01 = ?);";
        $stmt = $this->db->prepare($sql);
		$wcd = explode('[',$_SESSION['拠点']);
		$kyocd = substr($wcd[1],0,-1); // 拠点CD
		$par = array($today,$today,$id_1,$id_2);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			if($result["jyuc01"] == $id_1){
            	$mail_1 = $result["jyuc05"];
			}else{
            	$mail_2 = $result["jyuc05"];
			}
        }
        
        if($data['ring03'] == 'l1'){
            $syowrk['id'] = $id_1;
            $syowrk['name'] = $name_1;
            $syowrk['mail'] = $mail_1;
            $syinfo[] = $syowrk;
        }elseif($data['ring03'] == 'l2'){
            $syowrk['id'] = $id_2;
            $syowrk['name'] = $name_2;
            $syowrk['mail'] = $mail_2;
            $syinfo[] = $syowrk;
        }elseif($data['ring03'] == 'l3'){
            $syowrk['id'] = $id_1;
            $syowrk['name'] = $name_1;
            $syowrk['mail'] = $mail_1;
            $syinfo[] = $syowrk;
            $syowrk['id'] = $id_2;
            $syowrk['name'] = $name_2;
            $syowrk['mail'] = $mail_2;
            $syinfo[] = $syowrk;
        }
        $syouninsya = json_encode($syinfo,JSON_UNESCAPED_UNICODE);
        
        $data['ring01'] = ''; // 稟議番号
        $data['ring02'] = date('Ymd'); // 起案日
        //$data['ring03'] = ''; // 起案種別
        $data['ring04'] = $_SESSION['id']; // 起案者CD
        //$data['ring05'] = ''; // タイトル
        $data['ring06'] = str_replace("\r\n",'&#13;',$data['ring06']); // 内容
        //$data['ring07'] = ''; // 支払種別
        //$data['ring08'] = ''; // 金額
        //$data['ring09'] = ''; // 承認希望時期
        $data['ring10'] = ''; // 
        $data['ring11'] = ''; // 
        $data['ring12'] = ''; // 
        $data['ring13'] = ''; // 
        $data['ring14'] = ''; // 
        $data['ring15'] = $syouninsya; // 承認者
        $data['ring16'] = $syouninsya; // 未承認者
        $data['ring17'] = '';//$genzaicd; // 現在の承認者CD
        $data['ring18'] = '';//$genzainm; // 現在の承認者名
        $data['ring19'] = md5(uniqid(rand(), true)); // 承認URLパスワード 
        $data['ring20'] = ''; // コメント(500)


        // エラーチェック
        //if(!$this->err){$this->validate($data['ring01'] ,'str',  array(0,20,false), $data['lab01']);} // 稟議番号(自動)
        //if(!$this->err){$this->validate($data['ring02'] ,'date', array(0,8,false), $data['lab02']);} // 起案日
        if(!$this->err){$this->validate($data['ring03'] ,'str',  array(1,2,true), $data['lab03']);} // 起案種別
//        if(!$this->err){$this->validate($data['ring04'] ,'str',  array(1,10,true), $data['lab04']);} // 起案者CD
        if(!$this->err){$this->validate($data['ring05'] ,'str',  array(1,100,true), $data['lab05']);} // タイトル
        if(!$this->err){$this->validate($data['ring06'] ,'str',  array(1,500,true), $data['lab06']);} // 内容
        if(!$this->err){$this->validate($data['ring07'] ,'str',  array(1,10,true), $data['lab07']);} // 支払種別
        if(!$this->err){$this->validate($data['ring08'] ,'str',  array(1,10,true),$data['lab08']);} // 金額
        if(!$this->err){$this->validate($data['ring09'] ,'date', array(0,8,false), $data['lab09']);} // 承認希望時期
//        if(!$this->err){$this->validate($data['ring10'] ,'str',  array(0,0,false), $data['lab10']);} // 
//        if(!$this->err){$this->validate($data['ring11'] ,'str',  array(0,0,false),  $data['lab11']);} // 
//        if(!$this->err){$this->validate($data['ring12'] ,'str',  array(0,0,false),  $data['lab12']);} // 
//        if(!$this->err){$this->validate($data['ring13'] ,'str',  array(0,0,false), $data['lab13']);} // 
//        if(!$this->err){$this->validate($data['ring14'] ,'str',  array(0,0,false),  $data['lab14']);} // 
        if(!$this->err){$this->validate($data['ring15'] ,'str',  array(3,300,true),  $data['lab15']);} // 承認者
        if(!$this->err){$this->validate($data['ring16'] ,'str',  array(3,300,true),  $data['lab16']);} // 未承認者
//        if(!$this->err){$this->validate($data['ring17'] ,'str',  array(0,10,false), $data['lab17']);} // 
//        if(!$this->err){$this->validate($data['ring18'] ,'str',  array(0,50,false), $data['lab18']);} // 
        if(!$this->err){$this->validate($data['ring19'] ,'str',  array(0,100,false), $data['lab19']);} // 承認URLパスワード 
        if(!$this->err){$this->validate($data['ring20'] ,'str',  array(0,500,false), $data['lab20']);} // コメント（蓄積）
        if(!isset($_FILES["filec1"]["tmp_name"])){$this->err = "添付ファイルがありません。";}
        
        if($this->err){
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>'.$this->err.'</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
        
        // 採番
        $sql = "select sai02 from saiban where sai01='ringi';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $bango = $result["sai02"];
        }
        if($bango){
            // 採番マスタ更新
            $data['ring01'] = $bango;
            $sql = "update saiban set sai02 = (sai02 + 1) where sai01='ringi';";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            // データ作成
            $par = array();
            $sql = "insert into ringi (";
            for($i=1; $i <= 20; $i++){
                $a = sprintf('%02d',$i);
                $sql .= "ring".$a.",";
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
                $par[] = $data["ring$a"];
            }
            array_push($par,$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute($par);
            
            // 添付ファイルアップロード
            if($res){
                $dir = "/var/www/doc/rin/{$data['ring01']}";
                if(!file_exists($dir)){
                    mkdir($dir); // 稟議番号フォルダ作成
                    chmod($dir, 0777); // パーミッション変更
                }
                $wrk[0]['res'] = true;
                for($i=0; $i < count($_FILES["filec1"]["tmp_name"]); $i++){
                    $file_tmp  = $_FILES["filec1"]["tmp_name"][$i]; // 一時アップロード先ファイルパス
                    $file_save = $dir . "/" . $_FILES["filec1"]["name"][$i]; // 正式保存先ファイルパス
                    $result = @move_uploaded_file($file_tmp, $file_save); // ファイル移動
                    if($result == false){
                        $modal = $this->readModalSource('modal_n');
                        $modal['body'] = '<div>アップロードに失敗しました</div>';
                        $wrk[0]['html'] = implode($modal);
                        $wrk[0]['res'] = false;
                    }
                }
            }else{
                $modal = $this->readModalSource('modal_n');
                $modal['body'] = '<div>新規データを書き込みできませんでした。システム管理者に連絡して下さい。</div>';
                $wrk[0]['html'] = implode($modal);
                $wrk[0]['res'] = false;
            }
        }
        echo json_encode($wrk);
        exit();
    }
    
	// a検索用モーダル表示(JS)
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
    // a検索用モーダル表示(PHP)
    function searchModalCall($data){
        $modal = $this->readModalSource('modal_search'); // $modal[] = modalソース配列
        
        // コンボボックス処理
        $wrk = array(''=>'パターンから選択');
        $modal['combo0'] = '<OPTION value="">未選択</OPTION>';
        $sql = "select * from kensakupt where kns01=? and kns05='ringi' order by kns02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['id']);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $kns02 = $result['kns02'];
            $kns03 = $result['kns03'];
            $modal['combo0'] .= "<OPTION value='$kns02'>$kns03</OPTION>";
        }
        $wrk = array(''=>'未選択','ring01'=>'稟議番号','ring02'=>'起案日','ring03'=>'承認ルート','ring04'=>'起案者CD','ring05'=>'タイトル','ring06'=>'内容','ring07'=>'支払区分','ring08'=>'金額','ring09'=>'希望承認時期','ring10'=>'','ring11'=>'','ring12'=>'','ring13'=>'','ring14'=>'','ring15'=>'承認者CD','ring16'=>'未承認者CD','ring17'=>'現在承認者CD','ring18'=>'現在承認者名','ring19'=>'','ring20'=>'コメント');

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
    // a検索(JS)
    function searchbtn2ClickJs(){
        $this->clearJs();
        $numArray = array();
        for($i=0; $i < 20; $i++){
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
    // a検索データ取得(PHP)
    function searchData($data){
        // 条件式の組み立て
        for($i=1; $i <= 4; $i++){
            if($data["jyouken$i"] != '' or $i == 1){
                if($i != 1){
                    $sqlwhere .= ' ' . $data["jyouken$i"] . ' ';
                }else{
                    $sqlwhere .= ' and ';
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
        $today = date('Y-m-d');
        $par = array($today,$today,$today,$today,$_SESSION['従業員コード']);
        $sql = "select *,ringi.cday as cday,ringi.ctim as ctim,ringi.cid as cid,ringi.uday as uday,ringi.utim as utim,ringi.uid as uid,tbl1.nm as cname,tbl2.nm as uname from ringi";
        $sql .= " left join namae as tbl1 on tbl1.st<=? and tbl1.en>=? and tbl1.cd=ringi.cid";
        $sql .= " left join namae as tbl2 on tbl2.st<=? and tbl2.en>=? and tbl2.cd=ringi.uid";
        $sql .= " where ring04 = ? $sqlwhere $orderby;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $wrk[$i]['button'] = '<button onclick="modalCallUd(this);" style="width:50px;" type="button" value="'.$result['ring01'].'">詳細</button>'; // 選択ボタン
            $wrk[$i]['ring01'] = $result['ring01'];
            if($result['ring17'] == ''){
                $jyoukyou = '';
            }else{
                $jyoukyou = '承認待ち';
            }
            if($result['cname'] == ''){
                $result['cname'] = $result['cid'];
            }
            if($result['cname'] == ''){
                $result['uname'] = $result['uid'];
            }
            $wrk[$i]['joukyo'] = $jyoukyou;
            $wrk[$i]['ring05'] = $result['ring05'];
            $wrk[$i]['ring08'] = $result['ring08'];
            $wrk[$i]['cday'] = $this->format($result['cday'],'date-');
            $wrk[$i]['ctim'] = $this->format($result['ctim'],'time:');
            $wrk[$i]['cid']  = $result['cname'];
            $wrk[$i]['uday'] = $this->format($result['uday'],'date-');
            $wrk[$i]['utim'] = $this->format($result['utim'],'time:');
            $wrk[$i]['uid']  = $result['uname'];
            $wrk[$i] = str_replace(null,'',$wrk[$i]); // null値除去
            $test[] = $result;
            $i++;
        }
        $res[0]['html'] = $this->createTableSource($wrk);
        $res[0]['sql'] = $test;
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
            $sql = "select max(kns02)+1 as ptcd from kensakupt where kns01=? and kns05='ringi' group by kns01;";
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
            array_push($par,$_SESSION['id'],$wrkcd,$data["ptnm"],$data["ptdef"],'ringi',$today,$now,$_SESSION['id'],$today,$now,$_SESSION['id']);
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
        $sql = "select * from kensakupt where kns01=? and kns05='ringi' order by kns02 DESC;";
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
        $sql = "select * from kensakupt where kns01=? and kns02=? and kns05='ringi';";
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
        $sql = "delete from kensakupt where (kns01=? and kns02=?) and kns05='ringi';";
        $par = array($_SESSION['id'],$data['kns02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
        exit();
    }

}



// ** page info ** //
$p = new page();

$data['pr1'] = array('title' => '稟議起案'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
$data['pr3'] = array('active' => '申請・承認'); // ナビメニュー

loadResource($p,$data);