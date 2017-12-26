<?php
// CLASS
class core {

    protected $db; // DB instance
    protected $js1; // before event
    protected $js2; // after event
    protected $js3; // ajax error
    protected $js4; // complete event 
    protected $err; // error
    public $pgname; // ----.php
    public $src;
    public $modal; // for modal customaize
    public $jsflg; // custom JS link
    public $cssflg; // custom CSS link

    // 初期設定
    function __construct() {
        $url = debug_backtrace();
        
        $os = 'l'; // wはテスト用
        
        if($os == 'w'){
            error_reporting(0);
            $this->pgname = explode('\\', $url[0]['file']);
        }elseif($os == 'l'){
            $this->pgname = explode('/', $url[0]['file']);
        }
        $this->pgname = end($this->pgname);

        session_start();
        $err = '';
        if ($_SESSION['従業員コード'] == "" or $_SESSION['ログイン時刻'] + 3600 * 10 < time()) { // session check (10hour)
            $err = 'エラー';
        }
        if ($this->pgname == 'login.php') { // login画面は例外
            $err = '例外';
        }
        if ($this->pgname == 'cert.php') { // cert認証ページは無条件でDB接続
            $err = '';
        }

        if ($err == '') {
            $_SESSION['time'] = time(); // 時間延長
            $dsn = 'mysql:dbname=main01db;host=localhost;port=3306;charset=utf8mb4;';
            $user = 'webu';
            $password = 'KkoG95Vi';
            try {
                $this->db = new PDO($dsn, $user, $password);
            } catch (PDOException $e) {
                echo "DBに接続できません。";
                die();
            }
        } elseif ($err == '例外') {
            session_destroy();
        } else {
            session_destroy();
            echo "ログイン有効期限切れです。ログインし直して下さい。";
            die();
        }
    }

    // メール送信(to:array cc:array bcc:array title:str content:str from:str)
    function sendMail($to, $cc, $bcc, $title, $content, $from) {
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        if (is_array($to)) { // 宛先処理
            foreach ($to as $val) {
                $to_str .= $val . ',';
            }
            $to_str = substr($to_str, 0, -1); // 1文字削除
        } else {
            $to_str = $to;
        }

        if (is_array($cc)) {// CC処理
            foreach ($cc as $val) {
                $cc_str .= $val . ',';
            }
            $cc_str = substr($cc_str, 0, -1); // 1文字削除
        } else {
            $cc_str = $cc;
        }
        $cc_str = 'Cc: ' . $cc_str . "\n";

        if (is_array($bcc)) { // BCC処理
            foreach ($bcc as $val) {
                $bcc_str .= $val . ',';
            }
            $bcc_str = substr($bcc_str, 0, -1); // 1文字削除
        } else {
            $bcc_str = $bcc;
        }
        $bcc_str = 'Bcc: ' . $bcc_str . "\n";

        if (!$from) { // from処理
            $from = "From:" . mb_encode_mimeheader("システム管理") . "<fujii@kawai-g.com>";
        }
        $header = $cc_str . $bcc_str . $from;
        if (mb_send_mail($to_str, $title, $content, $header)) { //送信先,タイトル,本文,追加ヘッダ,追加コマンドラインパラメータ
            return true;
        }
    }

    // カレンダーモジュールロード
    public function calendarLoad($id, $event) {
        $today = date('Y-m-d');
        echo <<<"__"
        $(document).ready(function()
        {
            $('$id').fullCalendar(
            {
                header: 
                {
                    left: 'prev,next today',center: 'title',right: 'month,agendaWeek,agendaDay'
                },
                lang: "ja", height: 600, timeFormat: 'H:mm',
                views: 
                {
                    basic:{columnFormat:'M/DD（ddd）'},
                    agenda:{columnFormat:'M/DD（ddd）'},
                    week:{columnFormat:'M/DD（ddd）'},
                    day:{columnFormat:'M/DD（ddd）'},
                },
                axisFormat: 'H:mm', // 時間軸に表示する時間の表示フォーマットを指定する
                defaultDate: '$today',
                editable: false, // 変更不可にする
                eventLimit: true, // allow "more" link when too many events
                eventLimitClick:'popover',
                eventClick: function(event)
                {
                    if(event.url)
                    {
                        $event return false;
                    }
                },
                events: []
            }
            );    
        }
        );
__;
    }

    // カレンダーイベント自動整形
    public function calendarFormat($ary) {
        // $ary → [{cal01:val(cal01),cal02:val(cal02),...},{cal01:val(cal01),cal02:val(cal02),...},...]
        foreach ($ary as $obj) {
            if ($obj['cal04'] == '') { // 終了時間なし ＝ 終日イベント
                $cal['start'] = substr($obj['cal01'], 0, 4) . '-' . substr($obj['cal01'], 4, 2) . '-' . substr($obj['cal01'], 6, 2);
                $cal['end'] = '';
            } else {
                $cal['start'] = substr($obj['cal01'], 0, 4) . '-' . substr($obj['cal01'], 4, 2) . '-' . substr($obj['cal01'], 6, 2) . 'T' . substr($obj['cal03'], 0, 2) . ':' . substr($obj['cal03'], 2, 2);
                $cal['end'] = substr($obj['cal02'], 0, 4) . '-' . substr($obj['cal02'], 4, 2) . '-' . substr($obj['cal02'], 6, 2) . 'T' . substr($obj['cal04'], 0, 2) . ':' . substr($obj['cal04'], 2, 2);
            }
            $cal['title'] = $obj['cal06']; // 表示名
            $cal['color'] = $obj['cal10']; // 表示色
            $cal['url'] = '#'; // URL
            $cal['id'] = $obj['cal11']; // ID
            $res[] = $cal;
        }
        return $res;
    }

    // バリデーション
    public function validate($val, $type, $array, $title) {
        if(!$this->err) {
            if ($array[2] == true) { // 必須項目の入力チェック
                if ($val == '') {
                    $this->err = $title . 'が入力されていません。';
                }
            }
            if (!$this->err and $val != '') { // 文字数チェック
                if (mb_strlen($val) < $array[0] or mb_strlen($val) > $array[1]) {
                    $this->err = $title . 'の文字数エラーです。';
                }
            }
            if (!$this->err and $val != '') { // タイプチェック
                if ($type == 'date') { // 日付
                    if ($val > '2200-12-32' or $val < '1000-00-00') {
                        $this->err = $title . 'の範囲が不正です。';
                    }
                } elseif ($type == 'int') {
                    if (!ctype_digit($val)) {
                        $this->err = $title . 'の値が不正です。';
                    }
                } elseif ($type == 'time') {
                    if (!ctype_digit($val)) {
                        $this->err = $title . 'の値が不正です。';
                    } elseif (substr($val, 2, 2) >= 60) {
                        $this->err = $title . 'の範囲が不正です。';
                    }
                } elseif ($type == 'str') {
                    
                }
            }
        }
    }

    // 各種フォーマッタ
    public function format($str, $type) {
        if ($type == 'date-') { // yyyymmdd → yyyy-mm-dd
            if ($str != '') {
                $res = substr($str, 0, 4) . '-' . substr($str, 4, 2) . '-' . substr($str, 6, 2);
            } else {
                $res = '';
            }
        } elseif ($type == 'time:') { // mmss → mm:ss
            if ($str != '') {
                $res = substr($str, 0, 2) . ':' . substr($str, 2, 2);
            } else {
                $res = '';
            }
        } elseif ($type == 'date') { // yyyy-mm-dd → yyyymmdd
            if ($str != '') {
                $res = str_replace('-', '', $str);
            } else {
                $res = '';
            }
        } elseif ($type == 'time') { // mm:ss → mmss
            if ($str != '') {
                $res = str_replace(':', '', $str);
            } else {
                $res = '';
            }
        } elseif ($type == 'zip-') { // 郵便番号-付与
            if ($str != '') {
                $res = substr($str, 0, 3) . '-' . substr($str, 3, 4);
            } else {
                $res = '';
            }
        } elseif ($type == 'cd') { // 名称#cd　→　cd
            if ($str != '') {
                $ary = explode('#', $str);
                $res = $ary[1];
            } else {
                $res = '';
            }
        }
        return $res;
    }

    // $this->js 定義初期化
    public function clearJs() {
        $this->js1 = '';
        $this->js2 = '';
        $this->js3 = '';
        $this->js4 = '';
    }

    // カスタムJS出力
    public function customJsStart() {
        header('Content-type: application/javascript; charset=utf-8');
        echo "$(function(){" . $code;
    }

    public function customJsEnd() {
        echo "});";
    }

    // カスタムCSS出力
    public function customCssStart() {
        header('Content-Type: text/css; charset=utf-8');
    }

    public function customCssEnd() {
        echo "";
    }


    // ★templateファイルの読み込み → ソース分解
    public function useTemplate($path) {
        if (file_exists($path)) {
            $template = file_get_contents($path);
            $src = explode('<!-- delimiter del -->', $template);
            $html = '';
            foreach ($src as $key => $value) {
                if ($key % 2 == 0) { // 偶数番目を残す（<!-- delimiter del -->に挟まれた箇所は削除）
                    $html = $html . $value;
                }
            }
            $html = str_replace(' data-toggle="modal"', '', $html); // data-toggle="modal" 記述を削除(HTMLファイルのみでの開発に利用)
            $src = explode('<!-- delimiter -->', $html);
            foreach ($src as $key => $value) {
                if ($key % 2 == 1) { // 奇数番目（delimiterに挟まれた箇所）
                    $keystring = explode('@', $value);
                    $keyArray[] = $keystring[1]; // @で囲まれた文字列を配列キーに書き込み
                } else {
                    $keyArray[] = $key;
                }
            }
            $html = array_combine($keyArray, $src); // キーとソースの対応付け [0]src [1]src [nav]src ...
            $this->src = $html;
        } else {
            $this->src = 'template error';
        }
    }

    // ★HTML出力
    public function show() {
        if (is_array($this->src)) { // 分割ソース
            echo implode($this->src);
        } elseif ($this->src) { // 単品ソース
            echo $this->src;
        } else {
            echo 'error';
        }
    }

    // ★モーダルソース読み込み → 値の引き当て
    public function readModalSource($mdname) {
        unset($modal);
        $path = str_replace('.php', '.html', $this->pgname);
        if (file_exists($path)) {
            $template = file_get_contents($path);
            $srcwrk = explode('<!-- delimiter ' . $mdname . ' -->', $template);
            $srcwrk[1] = str_replace(' data-toggle="modal"', '', $srcwrk[1]); // data-toggle="modal" 記述を削除(HTMLファイルのみでの開発に利用)
            $src = explode('||', $srcwrk[1]); // delimiterに挟まれた箇所を読込
            foreach ($src as $key => $value) {
                if ($key % 2 == 1) { // 奇数番目（||に挟まれた箇所）
                    $keystring = explode('@', $value);
                    $keyArray[] = $keystring[1]; // @で囲まれた文字列を配列キーに書き込み
                } else {
                    $keyArray[] = $key;
                }
            }
            $modal = array_combine($keyArray, $src); // キーとソースの対応付け。[0]src [1]src [nav]src ...
        } else {
            $modal = 'read error';
        }
        return $modal;
    }

    // ★イベントリスナー
    public function addEventListener($selector, $event, $function, $type) {
        if ($type == 'ajax') { // 通常のajax
            $ajax = <<<"__"
            $.ajax({
            type:"POST",
            url:"{$this->pgname}",
            data:{linktype:"ajax",function:"$function",json:params}
            }).done(function(json_data) {
{$this->js2}
                })
              .fail(function(json_data, jqXHR, textStatus, errorThrown) {
                      console.log("XMLHttpRequest : " + jqXHR.status);
                      console.log("textStatus : " + textStatus);
                      console.log("errorThrown : " + errorThrown);
{$this->js3}
                 })
              .always(function(json_data) {
{$this->js4}
                $('#modalLoader').modal('hide');
                 })
__;
        } elseif ($type == 'ajax_f') { // ファイル送信ajax
            $ajax = <<<"__"
            fd.append('linktype', 'ajax_f');
            fd.append('function', '$function');
            $.ajax({
            type: 'POST',
            url:"{$this->pgname}",
            data:fd,
            processData: false,
            contentType: false,
            dataType: 'html'
            }).done(function(json_data) {
{$this->js2}
                })
              .fail(function(json_data, jqXHR, textStatus, errorThrown) {
                      console.log("XMLHttpRequest : " + jqXHR.status);
                      console.log("textStatus : " + textStatus);
                      console.log("errorThrown : " + errorThrown);
{$this->js3}
                 })
              .always(function(json_data) {
{$this->js4}
                 })
__;
        } else {
            $ajax = <<<"__"
            $('#modalLoader').modal('hide');
__;
        }

        if ($event == 'wait') {
            $code = <<<"__"
            function $function(event,params)
            {
                $('#modalLoader').modal({backdrop:'static'});
{$this->js1}
$ajax
            }
__;
        } elseif ($event != '') {
            $code = <<<"__"
            $(document).on('$event', '$selector', function(event,params)
            {
                $('#modalLoader').modal({backdrop:'static'});
{$this->js1}
$ajax
            });
__;
        } else {
            $code = <<<"__"
            $(document).ready(function(event,params)
            {
                $('#modalLoader').modal({backdrop:'static'});
{$this->js1}
$ajax
            });
__;
        }
        echo $code;
    }

    // tabulator用データ作成
    public function tabulateData($tabuwrk) {
        $i = 1;
        foreach ($tabuwrk as &$ary) {
            $ary['id'] = $i;
            $i++;
        }
        unset($ary);
        return $tabuwrk;
    }

    // tabulator切替後は不要？ // テーブル用ソースの作成　<td><div>配列要素</div></td>
    public function createTableSource($array) {
        foreach ($array as $wrk) {
            if ($wrk['row-color'] != '') {
                $row_color = 'background-color:' . $wrk['row-color'] . ';';
            }
            if ($wrk['text-color'] != '') {
                $text_color = 'color:' . $wrk['text-color'] . ';';
            }
            $src .= "<tr style='{$row_color}{$text_color}'>";
            foreach ($wrk as $key => $val) {
                if ($key != 'row-color' and $key != 'text-color') {
                    $src .= "<td><div>$val</div></td>";
                }
            }
            $src .= '</tr>';
            $row_color = '';
            $text_color = '';
        }
        return $src;
    }
    // プログラム名取得
    public function get_pgname() {
        return $this->pgname;
    }

    // htmlspechalchars escape
    function h($s) {
        return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
    }

}


/* ----------- ページ出力　(coreクラス外) ----------- */

function loadResource($p,$data) {
    if ($_GET['linktype'] == 'js') {
        // function ---Js() を 実行しJSを出力
        error_reporting(0);
        header('Content-type: application/javascript; charset=utf-8');
        $methods = get_class_methods($p);
        foreach ($methods as $val) {
            if (substr($val, -2) == 'Js') {
                $p->{$val}();
            }
        }
    } elseif ($_GET['linktype'] == 'css') {
        // function ---Css() を 実行しCSSを出力
        error_reporting(0);
        header('Content-Type: text/css; charset=utf-8');
        $methods = get_class_methods($p);
        foreach ($methods as $val) {
            if (substr($val, -3) == 'Css') {
                $p->$val();
            }
        }
    } elseif ($_POST['linktype'] == 'ajax' or $_POST['linktype'] == 'ajax_f') {
        // function ---ajax() を 実行しajaxデータを出力
        $func = $_POST['function'];
        $data = json_decode($_POST['json'], true);
        $p->$func($data);
        $p->db = null;
    } else {
        // POST・GET値をセッション変数に格納（利用時は $_SESSION['post_dat']['名前'] で利用）
        // テンプレート読込（$this->src[key_name]が@key部分に該当）
        // 各部ソースの作成
        if (!empty($_POST)) {
            $_SESSION['post_dat'] = $_POST;
        }
        if (!empty($_GET)) {
            $_SESSION['get_dat'] = $_GET;
        }
        $methods = get_class_methods($p);
        foreach ($methods as $val) {
            if (substr($val, -2) == 'Js') {
                $p->jsflg = 1;
            } elseif (substr($val, -3) == 'Css') {
                $p->cssflg = 1;
            }
        }
        $p->useTemplate(str_replace('.php', '.html', $p->get_pgname()));
        if ($data['pr1']) {
            $p->src['header'] = headerReplace::createSrc($data['pr1']); // ヘッダ作成
        }
        if ($data['pr2']) {
            $p->src['standard'] = standardComponentsLoad::createSrc($data['pr2']); // modal等のDOM作成
        }
        if ($data['pr3']) {
            $p->src['nav'] = bootstrapNavigationReplace::createSrc($data['pr3']); // ナビメニュー作成
        }
        $p->show();
    }
}
