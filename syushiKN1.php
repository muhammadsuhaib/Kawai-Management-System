<?php
// ** 収支計画入力 ** //

// CLASS INCLUDE
require_once('main.php');
 require_once('incl/htmlk.php');

// ★ POST GET

$p = new page;

if($_GET['linktype'] == 'js'){
    // customJsStart() ~ customJsEnd()までが$(function(){})内
    //　$p->ajaxCall(処理タイミング,関数名,渡すデータ配列,success時処理);
    // EOT行はスペース等が入るとエラー JS1はタブ*2 JS2はタブ*3
    $p->customJsStart();
    //
    // textarea change 確認用テーブル更新
    $js1 = <<<'EOT'
        $('#torikomi').change(function(){
            $('#tablebody tr').remove();
            $('#tablebody td').remove();
            var str = $('#torikomi').val();
            var ary = str.split('\n');
            var obj = {};
            var td1 = '<td class="dispo-alright"><div>';
            var td2 = '</div></td>';
            var htmlstr;
            for(var i = 0; i < ary.length; i++){
                obj[i] = ary[i].split('\t');
                if(parseInt(obj[i][0]) > 20000101){
                    htmlstr = '<tr><td class="dispo-alright"><div>'+ obj[i][0] + td2;
                    htmlstr += td1 + obj[i][2] + td2;
                    htmlstr += td1 + obj[i][3] + td2;
                    htmlstr += td1 + obj[i][4] + td2;
                    htmlstr += td1 + obj[i][5] + td2;
                    htmlstr += td1 + obj[i][6] + td2;
                    htmlstr += td1 + obj[i][7] + td2;
                    htmlstr += '</tr>';
                    $('#tablebody').append(htmlstr);
                }
            }
        });
EOT;
    echo $js1;
    // datelist　初期処理　本日日付をセット
    $js2 = <<<'EOT'
    var data = JSON.parse(json_data);
    $('#kakuteibi').val(data[0]['hiduke']);
EOT;
    echo $p->ajaxCall('','','datelist','ajax','',$js2);
    // soushin ボタンクリック データを更新
    $js1 = <<<'EOT'
            var str = $('#torikomi').val();
            var ary = str.split('\n');
            var obj = {};
            var sendobj = {};
            var j = 0;
            for(var i = 0; i < ary.length; i++){
                obj[i] = ary[i].split('\t');
                if(parseInt(obj[i][0]) > 20000101){
                    sendobj[j] = obj[i];
                    j++;
                }
            }
            params = JSON.stringify(sendobj);
EOT;
    $js2 = <<<'EOT'
            var data = JSON.parse(json_data);
            console.log(data);
            if(data['res'] == false){
                alert('データ更新に失敗しました。システム管理者までご連絡下さい。');
            }
EOT;
    echo $p->ajaxCall('#button1','click','soushin','ajax',$js1,$js2);
    //
    $p->customJsEnd();

}elseif($_POST['ajax'] == 'ajax'){
    // ★ AJAX start
    //
    // datelist　初期処理　本日日付をセット　
    if($_POST['func'] == 'datelist'){
        $dat[0]['hiduke'] = date('Y-m-d');
        echo json_encode($dat);
    }
    // soushin　ボタンクリック　データを送信　
    if($_POST['func'] == 'soushin'){
        $data = json_decode($_POST['dat'],true);
        // パラメータ格納
        foreach($data as $ary){
            $cnt = 0;
            foreach($ary as $val){
                if($cnt != 1){ // key列は除外
                    $par[] = $val;
                }
                $cnt++;
            }
            $i = 0;
        }
        $sql = 'insert into syushiw';
        $sql .= ' (hidukew,bumonw,uriage00w,shiharai01w,shiharai02w,shiharai03w,shiharai04w) VALUES ';
        for($i=0; $i < count($data); $i++){
            $sql .= '(?,?,?,?,?,?,?),';
        }
        $sql = substr($sql, 0, -1); // カンマ削除
        $sql .= ' ON DUPLICATE KEY UPDATE';
        $sql .= ' uriage00w = values(uriage00w),shiharai01w = values(shiharai01w),shiharai02w = values(shiharai02w),shiharai03w = values(shiharai03w),shiharai04w = values(shiharai04w);';
        $stmt = $pdo->prepare($sql);
        $result['res'] = $stmt->execute($par);
        echo json_encode($result);
    }

}else{
    // ★ HTML start
    $opt = array('JS'); // (J:JS,C:CSS)
    $src = $p->useTemplate('syushiKN1.html',$opt);

    // HTMLソース差し替え
    $src['nav'] = customView($_SESSION['autho'],'nav','syushi');
    
    // HTML出力
    $p->show($src,$opt);

    $dbh = null;
}
?>