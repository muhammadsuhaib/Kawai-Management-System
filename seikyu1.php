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
                columns:[
                    {width:30, title:"row_id", field:"id", align:"center", frozen:true},
                    {width:250, title:"請求先(部門)", field:"bumon", align:"left", frozen:true,formatter:'html'},
                    {width:80, title:"締日", field:"shimebi", align:"center"},
//                    {width:100, title:"売上計画", align:"right", field:"keikaku", formatter:'money', formatterParams:{precision:0}},
                    {width:150, title:"請求額(税抜)", align:"right", field:"zeinuki", formatter:'money', formatterParams:{precision:0}},
                    {width:100, title:"消費税", align:"right", field:"syouhizei", formatter:'money', formatterParams:{precision:0}},
                    {width:150, title:"請求額(税込)", field:"zeikomi", align:"right", align:"right", formatter:'money', formatterParams:{precision:0}},
                    {width:30, title:"F", field:"flg", align:"center"},
                    {title:"", field:"dm"}
                ],
                tooltips:function(cell){
                    return cell.getValue();
                },
				rowFormatter:function(row){
					var data = row.getData();
					var elem = row.getElement();
					var cell = row.getCell('shimebi');
					if(data.flg != "〇"){
						elem.css({"background-color":"#ffffff"});
//						console.log(cell);
						cell.setValue('');
					}else{
						elem.css({"background-color":"#a7ff9b"});
					}
				},
			});

__;
        $this->addEventListener('','','','');
    }
    
    // 検索実行(JS)
    function searchbtn1ClickJs(){
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"ymd":$("#ym_ud").val(),"shimebi":$("#shimebi_ud").val(),"hani":$("#hani_ud").val()};
            params = JSON.stringify(obj);
            //console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
			$("#tabu1").tabulator("setData", data[0]);
__;
        $this->addEventListener('#searchbtn1','click','searchData','ajax');
    }
    // 検索データ取得(PHP)
    function searchData($data){
        $ym = substr($this->format($data['ymd'],'date'),0,6); // 対象年月
        $shimebi = $data['shimebi']; // 締め日 kais21
        $hani = $data['hani']; // 条件絞り
        
        // 請求対象期間をワークテーブルに書き出し
        

        $ymdst = $ym.'00';
        $ymden = $ym.'31';
        $busql = "bum01<='$ymdst' and bum02>='$ymdst'";
		if($shimebi){
			$par = array($ym,$shimebi);
			$sql = "select * from kaisya left join bumon on bum26=kais24 left join syushi on bum03=syu03 and syu05=? where {$busql} and kais21=? order by cast(kais03 as SIGNED),kais24,bum03,syu02;"; // left join ukeikaku on ukei01='bum' and ukei02=bum03 and ukei03=?
		}else{
        	$par = array($ym);
			$sql = "select * from kaisya left join bumon on bum26=kais24 left join syushi on bum03=syu03 and syu05=? where {$busql} order by cast(kais03 as SIGNED),kais24,bum03,syu02;";
		}
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        $key = '';
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
			$res[] = $result;
        }
        $ritsu = 0.08;
		for($i=0;$i<=count($res)+1; $i++){
            if(!$key){
                $key = $res[$i]; // 初回は出力しない
            }else{
            // 部門集計
            if($key['bum03'] == $res[$i]['bum03']){ // keyが同じ=集計処理
                if($key['syu02'] == ''){
                    $zeinuki = 0;
                }else{
                    $zeinuki += $key['syu04']; // 税抜売上(合計)
                }
                $key = $res[$i]; // key更新
            }else{ // keyが違う=レコード出力して新しいkeyを集計
                $zeinuki += $key['syu04']; // 税抜売上(合計)
				
                if($key['bum28'] == '切上'){
                    $syouhizei = ceil($zeinuki * $ritsu);
                }elseif($key['bum28'] == '切捨'){
                    $syouhizei = floor($zeinuki * $ritsu);
                }else{ // ($key['bum28'] == '四捨五入')
                    $syouhizei = round($zeinuki * $ritsu);
                }
                $zeikomi = $zeinuki + $syouhizei;
                $bumon = "<a href='#' onclick='modalCallUd(this);' ym='$ym' val='{$key['bum03']}'>{$key['bum04']}</a>";
                $wrk[] = ['bum03'=>$key['bum03'],'bumon'=>$key['bum04'],'zeinuki'=>$zeinuki,'zeikomi'=>$zeikomi,'syouhizei'=>$syouhizei,'seikyucd'=>$key['kais03'],'seikyu'=>$key['kais04'],'hasu'=>$key['kais22'],'shimebi'=>$key['kais21']];//'keikaku'=>$key['ukei04'],
                
                $key = $res[$i]; // key更新
                
                $zeinuki = 0; // 税抜売上(合計)
            }
		}
		}
		$key = '';
		for($i=0;$i<=count($wrk)+1; $i++){
            if(!$key){ // 初回は出力しない
                $key = $wrk[$i];
            }else{				
            if($key['seikyucd'] == $wrk[$i]['seikyucd']){ // キーが同じ=請求先集計+部門出力
				$rec[] = ['bum03'=>$key['bum03'],'bumon'=>$key['bumon'],'zeinuki'=>$key['zeinuki'],'zeikomi'=>$key['zeikomi'],'syouhizei'=>$key['syouhizei'],'seikyucd'=>$key['seikyucd'],'seikyu'=>$key['seikyu'],'hasu'=>$key['hasu'],'shimebi'=>$key['shimebi']];//'keikaku'=>$key['keikaku'],
				//$goukei_k += $key['keikaku']; // 計画
				$goukei += $key['zeinuki']; // 売上
				$key = $wrk[$i];
			}else{ // キーが違う=部門と請求先を出力して集計値初期化
				$rec[] = ['bum03'=>$key['bum03'],'bumon'=>$key['bumon'],'zeinuki'=>$key['zeinuki'],'zeikomi'=>$key['zeikomi'],'syouhizei'=>$key['syouhizei'],'seikyucd'=>$key['seikyucd'],'seikyu'=>$key['seikyu'],'hasu'=>$key['hasu'],'shimebi'=>$key['shimebi']];//'keikaku'=>$key['keikaku'],
				//$goukei_k += $key['keikaku']; // 計画
				$goukei += $key['zeinuki']; // 売上
				
                if($key['hasu'] == '切上'){
                    $syouhizei = ceil($goukei * $ritsu);
                }elseif($key['hasu'] == '切捨'){
                    $syouhizei = floor($goukei * $ritsu);
                }else{ // ($key['bum28'] == '四捨五入')
                    $syouhizei = round($goukei * $ritsu);
                }
				$zeikomi = $goukei + $syouhizei;
				$rec[] = ['bum03'=>$key['seikyucd'],'bumon'=>$key['seikyu'],'zeinuki'=>$goukei,'zeikomi'=>$zeikomi,'syouhizei'=>$syouhizei,'seikyucd'=>$key['seikyucd'],'seikyu'=>$key['seikyu'],'hasu'=>$key['hasu'],'shimebi'=>$key['shimebi'],'flg'=>'〇'];//'keikaku'=>$goukei_k,
				
				$key = $wrk[$i];
				//$goukei_k = 0;
				$goukei = 0;
			}
			}
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
/*        $json[1]['sql'] = $sql;
        $json[3] = $wrk;
        $json[4] = $par;
*/		
        echo json_encode($json);
        exit();
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

$data['pr1'] = array('title' => '請求書発行'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
$data['pr3'] = array('active' => '売上・請求'); // ナビメニュー

loadResource($p,$data);