<?php
// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');
require_once("PHPExcel/Classes/PHPExcel.php");

class page extends core {
	
}
$p = new page();

// POST値取得
$d = $_SESSION['wrk'];

// キャッシュメモリ設定（デフォルト:1MB → 256MB）
// ※キャッシュを有効にした場合、列の挿入(insertNewColumnBefore)・削除(removeColumn)、
//   行の挿入(insertNewRowBefore)・削除(removeRow)が正常に動作しないため注意すること！！

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array('memoryCacheSize' => '256MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);


// Excelファイルの新規作成
/*$Excel = new PHPExcel();
*/

// テンプレート読み込み
$filepath = "/var/www/doc/請求書1.xls";
$Reader = PHPExcel_IOFactory::createReader('Excel5');
$Excel = $Reader->load($filepath);

for($i=0;$i<count($d);$i++){
    if($d[$i]['flg'] == '〇'){
        // シートコピー
        $newSheet = $Excel->getSheetByName("tmp")->copy();
        $newSheet->setTitle($d[$i]['bumon']);
        $Excel->addSheet( $newSheet );

        // シート切替
        $Sheet = $Excel->getSheetByName($d[$i]['bumon']);

        // 文字列を挿入
        $Sheet->setCellValue('A18', $d[$i]['bumon']);
        $Sheet->setCellValue('C26', $d[$i]['zeinuki']);
        $Sheet->setCellValue('F26', $d[$i]['syouhizei']);
        $Sheet->setCellValue('I26', $d[$i]['zeikomi']);
    }
}

// Excelファイルのダウンロード
$Writer = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=" . "TestDownload.xls");
header("Content-Transfer-Encoding: binary");
$Writer->save('php://output');


// メモリの開放
$Excel->disconnectWorksheets();
unset($Writer);
unset($Sheet);
unset($Excel);
	
?>