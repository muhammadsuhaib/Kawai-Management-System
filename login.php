<?php
// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');

/*
main.php ... coreクラス定義。coreクラスはpageクラスの親クラス。pageクラスでよく利用する機能はcoreクラスにくくり出すこと。
             pageオブジェクトにはそのページ固有のコードを書く。pageに定義したfunctionで「--Js」又は「--Css」で終わるものは、
             それぞれ「ファイル名.php?linktype=js」「ファイル名.php?linktype=css」で自動リンクされる。HTML表示時に
             読み込まれるのはPHPと同名のhtmlファイル。
                【流れ】PHP実行 → HTML表示 → カスタムjsとカスタムcssリンクが再度PHPにアクセス → Js,Cssで終わるfunctionを内包
             constructorでセッションチェック(10h)とDB接続許可（cert.phpのみ無条件接続）も実行。

個別.php ... カスタムjsでPOST値・GET値を使うため、$_POST及び$_GETはSESSION変数に格納している。
                【値の取得】$_SESSION['post_dat']['名前']　$_SESSION['get_dat']['名前']

個別.html ... テンプレートファイル(HTML)。以下のタグは特殊。
                <!-- delimiter del --> ... 囲まれた部分は削除される。
                <!-- delimiter --><!-- @header@ --> ... カスタムヘッダに置き換え
                <!-- delimiter --><!-- @standard@ --> ... サイト共通利用のDOMに置き換え
                <!-- delimiter --><!-- @nav@ --> ... bootstrapナビゲーションに置き換え
                <!-- delimiter modal_xx --> ... モーダルテンプレートとして参照される（<!-- delimiter del -->と併用のこと）
                
htmlk.php ... サイト（システム）全体で共通利用するクラスを定義。必ず利用するモーダルのdom追加やヘッダ書き換え等に利用。
            　今のところstatic functionを定義したクラスのみ。

cert.php ... ログイン認証（パスワードは暗号化）と権限貸し出し（session変数にログインした権限情報格納）
password.php ... passwordをhashするAPI。（PHP5.5 以降は標準で利用できるため必要ない）

main.js ... 共通利用するJSを定義（htmlk.phpのスクリプトに統合でもよいか？）

main.css ... 共通利用するCSSを定義（htmlk.phpのスタイルに統合でもよいか？）

*/

class page extends core{ 
}

$p = new page();

$data['pr1'] = array('title' => 'ログイン'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
//$pr3 = array('active' => 'マスター', 'name' => $_SESSION['姓'] . '　' . $_SESSION['名']); // ナビメニュー

loadResource($p,$data);