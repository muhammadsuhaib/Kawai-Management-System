<?php

// ** スタッフマスター ** //
// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');
require_once('incl/password.php');
require_once("PHPExcel/Classes/PHPExcel.php");

class page extends core {

    private $koumokusu = 200;

    // JS初期処理
    function initJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function () 
        {
            FixedMidashi.create();
        });
        $('#sdate').val('{$_SESSION['post_dat']['day']}');
        $('#syid').text('{$_SESSION['post_dat']['act']}');
        $('#sdate').trigger('change');
__;
        $this->addEventListener();
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
__;
        $this->addEventListener('#createbtn1', 'click', 'modalCallCr', 'ajax');
    }

    // 新規モーダル表示(PHP)
    function modalCallCr($data) {
        $modal = $this->readModalSource('modal_c');
        $res[0]['html'] = implode($modal);
        echo json_encode($res);
    }

    // 更新・削除モーダル表示(JS)
    function button1nmClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"syu":event.syu,"cd":event.cd,"st":event.st,"en":event.en,"table":event.table,"modsyu":event.modsyu,"crdt":event.crdt};
            console.log(obj);
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_ud').remove();
            $('#modalParent1').append(data[0]["html"]);
            console.log(data);
            // 0:html,table 1:選択 2:候補 
            if(data[0]['table'] == 'jyu_i'){
                if(data[2]) // 候補
                {
                    $('.tsArea').TapSuggest({
                	tsInputElement : '#jyui05s',
                	tsArrayList : data[2],
                	tsRegExpAll : true
                    });	
                }
                if(data[1]) // 選択
                {
                    $('#jyui05').val(data[1][5]);
                }
            }else if(data[0]['table'] == 'nenchou'){
                if(data[1]) // 選択
                {
                    $('#nenc05').val(data[1][5]);
                    $('#nenc06').val(data[1][6]); $('#nenc07').val(data[1][7]); $('#nenc08').val(data[1][8]); $('#nenc09').val(data[1][9]); $('#nenc10').val(data[1][10]);
                    $('#nenc11').val(data[1][11]); $('#nenc12').val(data[1][12]); $('#nenc13').val(data[1][13]); $('#nenc14').val(data[1][14]); $('#nenc15').val(data[1][15]);
                    $('#nenc16').val(data[1][16]); $('#nenc17').val(data[1][17]); $('#nenc18').val(data[1][18]); $('#nenc19').val(data[1][19]); $('#nenc20').val(data[1][20]);
                    $('#nenc21').val(data[1][21]); $('#nenc22').val(data[1][22]); $('#nenc23').val(data[1][23]); $('#nenc24').val(data[1][24]); $('#nenc25').val(data[1][25]);
                    $('#nenc26').val(data[1][26]); $('#nenc27').val(data[1][27]); $('#nenc28').val(data[1][28]); $('#nenc29').val(data[1][29]); $('#nenc30').val(data[1][30]);
                }
            }else if(data[0]['table'] == 'shinzoku'){
                $('#shin10').val(data[1][10]);
                $('#shin11').val(data[1][11]);
                $('#shin12').val(data[1][12]);
            }else if(data[0]['table'] == 'jyu_s'){
                $('#jyus05').val(data[1][5]);
            }
            $('#modal_ud').modal({backdrop:'static'});
__;
        $this->addEventListener('', 'wait', 'modalCallUd', 'ajax');
    }

    // 更新・削除モーダル表示(PHP)
    function modalCallUd($data) {
        // $data['syu']：種別　$data['cd']：従業員CD　$data['start']：開始日　$data['end']：終了日,$data['table']:テーブル,$data['modsyu']：モーダル種別（基本　カレンダー　選択　候補）
        // 選択用の項目を取得
        $sql = 'select koum01,group_concat(koum03) as "k" from koumoku group by koum01 order by koum02;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $koumoku[$result['koum01']] = str_replace(',', '', $result['k']);
        }

        if ($data['table'] == 'jyu_k') {
            $modal = $this->readModalSource('modal_jyuk');
            $sql = 'select * from jyu_k where jyuk01=?;';
            $par = array($data['cd']);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $modal['jyuk01'] = $result['jyuk01'];
                $modal['jyuk02'] = $result["jyuk02"];
                $modal['jyuk03'] = $result["jyuk03"];
                $modal['jyuk04'] = $result["jyuk04"];
                $modal['jyuk05'] = $result["jyuk05"];
                $modal['jyuk06'] = $result["jyuk06"];
                $modal['jyuk07'] = $result["jyuk07"];
                $modal['jyuk08'] = $result['jyuk08'];
                $modal['jyuk09'] = $result['jyuk09'];
                $modal['jyuk10'] = $result['jyuk10'];
                $modal['jyuk_hid'] = $modal['jyuk01'];
                if ($modal['jyuk02'] == '0000-00-00') {
                    $modal['jyuk02'] = '';
                }
                if ($modal['jyuk03'] == '2200-01-01') { // 退職日
                    $modal['jyuk03'] = '';
                }
                if ($modal['jyuk04'] == '0000-00-00') {
                    $modal['jyuk04'] = '';
                }
                if ($modal['jyuk05'] == '0000-00-00') {
                    $modal['jyuk05'] = '';
                }
                if ($modal['jyuk06'] == '0000-00-00') {
                    $modal['jyuk06'] = '';
                }
                if ($modal['jyuk07'] == '0000-00-00') {
                    $modal['jyuk07'] = '';
                }
                if ($modal['jyuk08'] == '0000-00-00') {
                    $modal['jyuk08'] = '';
                }
            }
        } elseif ($data['table'] == 'jyu_i') {
            // jyu_i
            if ($data['modsyu'] == '基本') {
                $modal = $this->readModalSource('modal_基本');
            } elseif ($data['modsyu'] == 'カレンダー') {
                $modal = $this->readModalSource('modal_カレンダー');
            } elseif ($data['modsyu'] == '選択') {
                $modal = $this->readModalSource('modal_選択');
            } elseif ($data['modsyu'] == '選択と入力') {
                $modal = $this->readModalSource('modal_選択と入力');
            } elseif ($data['modsyu'] == '候補') {
                $modal = $this->readModalSource('modal_候補');
            }

            $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
            $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $modal['jyui01'] = $r['jyui01'];
                $modal['jyui02'] = $r["jyui02"];
                $modal['jyui03'] = $r["jyui03"];
                $modal['jyui04'] = $r["jyui04"];
                if ($data['modsyu'] == '選択') {
                    $res[1][5] = $r['jyui05'];
                } else {
                    $modal['jyui05'] = $r["jyui05"];
                    if ($modal['jyui04'] == 'パスワード') {
                        $modal['jyui05'] = '';
                    }
                }
                $modal['label'] = $r["jyui04"];
                $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
            }

            // 候補表示式 ----------------------------------
            if ($data['syu'] == '所属部署コード') {
                $sql = 'select * from bumon where bum01 <= ? and bum02 >= ?;';
                $par = array($modal['jyui02'], $modal['jyui03']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                $modal['jyui05s'] = $modal['jyui05'];
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $disp = $r['bum06'] . '.#' . $r['bum03'];
                    $res[2][] = array($disp, $r['bum04'] . ' ' . $r['bum05'] . ' ' . $r['bum06'] . ' ' . $r['bum07']);
                    if ($modal['jyui05'] == $r['bum03']) {
                        $modal['jyui05s'] = $disp;
                    }
                }
            } elseif ($data['syu'] == '国籍') {
                $sql = 'select * from country;';
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $modal['jyui05s'] = $modal['jyui05'];
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $disp = $r['cntry02'] . '.#' . $r['cntry01'];
                    $res[2][] = array($disp, $r['cntry03']);
                    if ($modal['jyui05'] == $r['cntry01']) {
                        $modal['jyui05s'] = $disp;
                    }
                }
            } elseif ($data['syu'] == '管理領域') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $res[1][5] = $result['jyui05'];
                    $modal['jyui06'] = $result['jyui06'];
                    $modal['jyui07'] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $modal['jyui09'] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"];
                }
                $sql = 'select grp01,grp02,grp03,grp04,grp05 from bgroup order by grp05,grp07;';
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $kouho = "<option value=''></option>";
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($result['grp05'] < 10) {
                        $result['grp04'] = '★' . $result['grp04'];
                    } else {
                        $result['grp04'] = $result['grp04'];
                    }
                    $kouho .= "<option value='{$result['grp02']}'>{$result['grp04']}</option>";
                }
                $modal['jyui05opt'] = $kouho;
            }
            // 選択式 ----------------------------------
            elseif ($data['syu'] == '管理領域コード') {
                // 管理領域用データ取得
                $sql = 'select grp01,grp02,grp03,grp04,grp05 from bgroup order by grp05,grp07;';
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $kouho = "<option value=''></option>";
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($r['grp05'] < 10) {
                        $r['grp04'] = '★' . $r['grp04'];
                    }
                    $kouho .= "<option value='{$r['grp02']}'>{$r['grp04']}</option>";
                }
                $modal['jyui05opt'] = $kouho;
            } elseif ($data['syu'] == '性別') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['性別'];
            } elseif ($data['syu'] == '応募者＿状態') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['応募者状態'];
            } elseif ($data['syu'] == '障碍者区分') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['障碍者区分'];
            } elseif ($data['syu'] == '職群') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['職群'];
            } elseif ($data['syu'] == '号数') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['番号'];
            } elseif ($data['syu'] == '拠点') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['拠点'];
            } elseif ($data['syu'] == '管理権限') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['管理権限'];
            } elseif ($data['syu'] == '稟議権限') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['稟議権限'];
            } elseif ($data['syu'] == '権限') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['権限'];
            } elseif ($data['syu'] == '通勤方法') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['通勤方法'];
            } elseif ($data['syu'] == '利用乗物') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['利用乗物'];
            } elseif ($data['syu'] == '社宅有無') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['社宅有無'];
            } elseif ($data['syu'] == '振込日') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['振込日'];
            } elseif ($data['syu'] == '課税区分') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['課税区分'];
            } elseif ($data['syu'] == '保険＿労災') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['保険＿労災'];
            } elseif ($data['syu'] == '保険＿雇用') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['保険＿雇用'];
            } elseif ($data['syu'] == '保険＿健康') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['保険＿厚生'];
            } elseif ($data['syu'] == '保険＿厚生') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['保険＿介護'];
            } elseif ($data['syu'] == '保険＿介護') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku[''];
            } elseif ($data['syu'] == '') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku[''];
            } elseif ($data['syu'] == '') {
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku[''];

                // 特殊 ----------------------------------
            } elseif ($data['syu'] == '姓名') {
                $modal = $this->readModalSource('modal_' . $data['syu']);
                $sql = 'select * from jyu_i where jyui01=? and jyui02=? and jyui03=? and (jyui04=? or jyui04=? or jyui04=? or jyui04=?);';
                $par = array($data['cd'], $data['st'], $data['en'], '姓', '名', '姓カナ', '名カナ');
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $r['jyui01'];
                    $modal['jyui02'] = $r["jyui02"];
                    $modal['jyui03'] = $r["jyui03"];
                    $modal['jyui04'] = $data['syu'];
                    if ($r['jyui04'] == '姓') {
                        $modal['jyui05'] = $r["jyui05"];
                    } elseif ($r['jyui04'] == '名') {
                        $modal['jyui06'] = $r["jyui05"];
                    } elseif ($r['jyui04'] == '姓カナ') {
                        $modal['jyui07'] = $r["jyui05"];
                    } elseif ($r['jyui04'] == '名カナ') {
                        $modal['jyui08'] = $r["jyui05"];
                    }
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
            } elseif ($data['syu'] == '住所') {
                $modal = $this->readModalSource('modal_' . $data['syu']);
                $sql = 'select * from jyu_i where jyui01=? and jyui02=? and jyui03=? and (jyui04=? or jyui04=? or jyui04=? or jyui04=? or jyui04=?);';
                $par = array($data['cd'], $data['st'], $data['en'], '郵便番号', '都道府県', '市区町村', '町域', 'アパート名など');
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $r['jyui01'];
                    $modal['jyui02'] = $r["jyui02"];
                    $modal['jyui03'] = $r["jyui03"];
                    $modal['jyui04'] = $data['syu'];
                    if ($r['jyui04'] == '郵便番号') {
                        $modal['jyui05'] = $r["jyui05"];
                    } elseif ($r['jyui04'] == '都道府県') {
                        $modal['jyui06'] = $r["jyui05"];
                    } elseif ($r['jyui04'] == '市区町村') {
                        $modal['jyui07'] = $r["jyui05"];
                    } elseif ($r['jyui04'] == '町域') {
                        $modal['jyui08'] = $r["jyui05"];
                    } elseif ($r['jyui04'] == 'アパート名など') {
                        $modal['jyui09'] = $r["jyui05"];
                    }
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
            } elseif ($data['syu'] == '最終学歴') {
                $sql = 'select * from jyu_i where jyui01=? and jyui02=? and jyui03=? and (jyui04=? or jyui04=?);';
                $par = array($data['cd'], $data['st'], $data['en'], '学歴＿区分', '学歴＿学校名');
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $r['jyui01'];
                    $modal['jyui02'] = $r["jyui02"];
                    $modal['jyui03'] = $r["jyui03"];
                    $modal['jyui04'] = $data['syu'];
                    if ($r['jyui04'] == '学歴＿区分') {
                        $res[1][5] = $r["jyui05"];
                        $modal['label05'] = $r["jyui04"];
                    } elseif ($r['jyui04'] == '学歴＿学校名') {
                        $modal['jyui06'] = $r["jyui05"];
                        $modal['label06'] = $r["jyui04"];
                    }
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
                $kouho = "<option value=''></option>";
                $kouho .= $koumoku['最終学歴'];
                $modal['jyui05opt'] = $kouho;
            } elseif ($data['syu'] == '学生区分') {
                $sql = 'select * from jyu_i where jyui01=? and jyui02=? and jyui03=? and (jyui04=? or jyui04=?);';
                $par = array($data['cd'], $data['st'], $data['en'], '学生＿区分', '学生＿学校名');
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $r['jyui01'];
                    $modal['jyui02'] = $r["jyui02"];
                    $modal['jyui03'] = $r["jyui03"];
                    $modal['jyui04'] = $data['syu'];
                    if ($r['jyui04'] == '学生＿区分') {
                        $res[1][5] = $r["jyui05"];
                        $modal['label05'] = $r["jyui04"];
                    } elseif ($r['jyui04'] == '学生＿学校名') {
                        $modal['jyui06'] = $r["jyui05"];
                        $modal['label06'] = $r["jyui04"];
                    }
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
                $kouho = "<option value=''></option>";
                $kouho .= $koumoku['学生区分'];
                $modal['jyui05opt'] = $kouho;
            } elseif ($data['syu'] == '緊急連絡先') {
                $sql = 'select * from jyu_i where jyui01=? and jyui02=? and jyui03=? and (jyui04=? or jyui04=?);';
                $par = array($data['cd'], $data['st'], $data['en'], '緊急連絡先区分', '緊急連絡先番号');
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $r['jyui01'];
                    $modal['jyui02'] = $r["jyui02"];
                    $modal['jyui03'] = $r["jyui03"];
                    $modal['jyui04'] = $data['syu'];
                    if ($r['jyui04'] == '緊急連絡先区分') {
                        $res[1][5] = $r["jyui05"];
                        $modal['label05'] = $r["jyui04"];
                    } elseif ($r['jyui04'] == '緊急連絡先番号') {
                        $modal['jyui06'] = $r["jyui05"];
                        $modal['label06'] = $r["jyui04"];
                    }
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
                $modal['jyui05opt'] = "<option value=''></option>" . $koumoku['緊急連絡先区分'];
            } elseif ($data['syu'] == 't_seikyu' or $data['syu'] == 't_shiharai') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $modal['jyui05'] = $result['jyui05'];
                    $modal['jyui06'] = $result['jyui06'];
                    $modal['jyui07'] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $modal['jyui09'] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
            } elseif ($data['syu'] == 'kouza') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $modal['jyui05'] = $result['jyui05'];
                    $modal['jyui06'] = $result['jyui06'];
                    $modal['jyui07'] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $res[1][9] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
                // 銀行
                $sql = 'select * from ginkou;';
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $kana = mb_convert_kana($result['gink03'], "KV"); // 半カナ→全カナ
                    $disp = $result['gink04'] . '銀行.#' . $result['gink01'];
                    $res[2][] = array($disp, $kana);
                }
            } elseif ($data['syu'] == 'zeiku' or $data['syu'] == 'rousai' or $data['syu'] == 'koyou' or $data['syu'] == 'kenkou' or $data['syu'] == 'kousei' or $data['syu'] == 'kaigo') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $res[1][5] = $result['jyui05'];
                    $modal['jyui06'] = $result['jyui06'];
                    $modal['jyui07'] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $modal['jyui09'] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
            } elseif ($data['syu'] == 'jyumin') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $modal['jyui05'] = $result['jyui05'];
                    $modal['jyui06'] = $result['jyui06'];
                    $modal['jyui07'] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $modal['jyui09'] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
                // 住民税納付先
                $sql = 'select * from tkdantai;';
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $kana = mb_convert_kana($result['danta04'] . ' ' . $result['danta05'], "KV"); // 半カナ→全カナ
                    $disp = $result['danta02'] . ' ' . $result['danta03'] . '.#' . $result['danta01'];
                    $res[2][] = array($disp, $kana);
                }
            } elseif ($data['syu'] == 't_tsukin' or $data['syu'] == 't_seikyuk') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $modal['jyui05'] = $result['jyui05'];
                    $modal['jyui06'] = $result['jyui06'];
                    $res[1][7] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $modal['jyui09'] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
            } elseif ($data['syu'] == 't_teate1' or $data['syu'] == 't_teate2' or $data['syu'] == 't_teate3' or $data['syu'] == 't_teate4' or $data['syu'] == '固定支給1' or $data['syu'] == '固定支給2' or $data['syu'] == '固定支給3' or $data['syu'] == '固定支給4' or $data['syu'] == '固定支給5' or $data['syu'] == '固定支給6' or $data['syu'] == '固定支給7' or $data['syu'] == '固定支給8' or $data['syu'] == '固定支給9' or $data['syu'] == 'ks_10' or $data['syu'] == '固定控除1' or $data['syu'] == '固定控除2' or $data['syu'] == '固定控除3' or $data['syu'] == '固定控除4' or $data['syu'] == '固定控除5' or $data['syu'] == '固定控除6' or $data['syu'] == '固定控除7' or $data['syu'] == '固定控除8' or $data['syu'] == '固定控除9' or $data['syu'] == 'kk_10') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $modal['jyui05'] = $result['jyui05'];
                    $res[1][6] = $result['jyui06'];
                    $modal['jyui07'] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $modal['jyui09'] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
            } elseif ($data['syu'] == 'syotei') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $modal['jyui05'] = $result['jyui05'];
                    $modal['jyui06'] = $result['jyui06'];
                    $modal['jyui07'] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $modal['jyui09'] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
            } elseif ($data['syu'] == 'youbi') {
                $sql = 'select * from jyu_i where jyui04=? and jyui01=? and jyui02=? and jyui03=?;';
                $par = array($data['syu'], $data['cd'], $data['st'], $data['en']);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($par);
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $modal['jyui01'] = $result['jyui01'];
                    $modal['jyui02'] = $result["jyui02"];
                    $modal['jyui03'] = $result["jyui03"];
                    $modal['jyui04'] = $result['jyui04'];
                    $modal['jyui05'] = $result['jyui05'];
                    $modal['jyui06'] = $result['jyui06'];
                    $modal['jyui07'] = $result['jyui07'];
                    $modal['jyui08'] = $result['jyui08'];
                    $modal['jyui09'] = $result['jyui09'];
                    $modal['jyui10'] = $result['jyui10'];
                    $modal['jyui_hid'] = $modal['jyui01'] . ',' . $modal['jyui04'] . ',' . $r["crdt"] . ',' . $r["jyui02"] . ',' . $r["jyui03"];
                }
            }
            if($modal['jyui03'] != '2200-01-01'){
                $modal['button'] = 'style="display:none;"';
                $modal['jyui02ds'] = ' disabled';
                $modal['jyui03ds'] = ' disabled';
            }else{
                $modal['jyui03ds'] = ' disabled';
            }
        } elseif ($data['table'] == 'nenchou') {
            $modal = $this->readModalSource('modal_年調');
            $sql = 'select * from nenchou where nenc01=? and nenc02=? and nenc03=?;';
            $par = array($data['cd'], $data['st'], $data['en']);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $modal['nenc01'] = $result['nenc01'];
                $modal['nenc02'] = $result['nenc02'];
                $modal['nenc03'] = $result['nenc03'];
                $modal['nenc04'] = $result['nenc04'];
                $res[1][5] = $result['nenc05'];
                $res[1][6] = $result['nenc06'];
                $res[1][7] = $result['nenc07'];
                $res[1][8] = $result['nenc08'];
                $res[1][9] = $result['nenc09'];
                $res[1][10] = $result['nenc10'];
                $res[1][11] = $result['nenc11'];
                $res[1][12] = $result['nenc12'];
                $res[1][13] = $result['nenc13'];
                $res[1][14] = $result['nenc14'];
                $res[1][15] = $result['nenc15'];
                $res[1][16] = $result['nenc16'];
                $res[1][17] = $result['nenc17'];
                $res[1][18] = $result['nenc18'];
                $res[1][19] = $result['nenc19'];
                $res[1][20] = $result['nenc20'];
                $res[1][21] = $result['nenc21'];
                $res[1][22] = $result['nenc22'];
                $res[1][23] = $result['nenc23'];
                $res[1][24] = $result['nenc24'];
                $res[1][25] = $result['nenc25'];
                $res[1][26] = $result['nenc26'];
                $res[1][27] = $result['nenc27'];
                $res[1][28] = $result['nenc28'];
                $res[1][29] = $result['nenc29'];
                $res[1][30] = $result['nenc30'];
                $modal['nenc31'] = $result['nenc31'];
                $modal['nenc_hid'] = $modal['nenc01'] . ',' . $modal['nenc02'] . ',' . $modal['nenc03'] . ',' . $modal['nenc04'];
            }
            if($modal['nenc03'] != '2200-01-01'){
                $modal['button'] = 'style="display:none;"';
                $modal['nenc02ds'] = 'disabled';
                $modal['nenc03ds'] = 'disabled';
            }else{
                $modal['nenc03ds'] = 'disabled';
            }
        } elseif ($data['table'] == 'shinzoku') {
            $modal = $this->readModalSource('modal_扶養親族');
            $modal['shin01'] = $data['cd'];
            $modal['shin02'] = '';
            $modal['shin03'] = '2200-01-01';
            $modal['shin04'] = '';
            $modal['shin05'] = '';
            $modal['shin06'] = '';
            $modal['shin07'] = '';
            $modal['shin08'] = '';
            $modal['shin09'] = ''; // 生年月日
            $modal['shin10opt'] = "<option value=''></option>" . $koumoku['性別'];
            $modal['shin11opt'] = "<option value=''></option>" . $koumoku['扶養親族＿配偶者'];
            $modal['shin12opt'] = "<option value=''></option>" . $koumoku['扶養親族＿非居住者'];
            $modal['shin13opt'] = "<option value=''></option>" . $koumoku[''];
            $modal['shin14opt'] = "<option value=''></option>" . $koumoku[''];
            $modal['shin15opt'] = "<option value=''></option>" . $koumoku[''];
            $res[1][10] = ''; // 性別
            $res[1][11] = ''; // 配偶者
            $res[1][12] = ''; // 非居住者
            $res[1][13] = ''; // 
            $res[1][14] = ''; // 
            $res[1][15] = ''; // 
            $modal['shin_hid'] = $modal['shin01'] . ',' . $modal['shin04'] . ',' . $r["crdt"] . ',' . $r["shin02"] . ',' . $r["shin03"];
            
            $sql = 'select * from shinzoku where shin01=? and crdt=?;';
            $par = array($data['cd'], $data['crdt']);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $modal['shin01'] = $r['shin01'];
                $modal['shin02'] = $r['shin02'];
                $modal['shin03'] = $r['shin03'];
                $modal['shin04'] = $r['shin04'];
                $modal['shin05'] = $r['shin05'];
                $modal['shin06'] = $r['shin06'];
                $modal['shin07'] = $r['shin07'];
                $modal['shin08'] = $r['shin08'];
                $modal['shin09'] = $r['shin09'];
                $res[1][10] = $r['shin10']; // 性別
                $res[1][11] = $r['shin11']; // 配偶者
                $res[1][12] = $r['shin12']; // 非居住者
                $res[1][13] = $r['shin13']; // 
                $res[1][14] = $r['shin14']; // 
                $res[1][15] = $r['shin15']; // 
                $modal['shin_hid'] = $modal['shin01'] . ',' . $modal['shin04'] . ',' . $r["crdt"] . ',' . $r["shin02"] . ',' . $r["shin03"];
            }
                if($modal['shin03'] != '2200-01-01'){
                    $modal['button'] = 'style="display:none;"';
                    $modal['shin02ds'] = 'disabled';
                    $modal['shin03ds'] = 'disabled';
                }else{
                    $modal['shin03ds'] = 'disabled';
                }

        } elseif ($data['table'] == 'jyu_s') {
            if($data['modsyu'] == 'チェック'){
                $modal = $this->readModalSource('modal_jyusCHK');
            }else{
                $modal = $this->readModalSource('modal_jyus');
            }
            // 初期化
            $modal['jyus01'] = $data['cd'];
            $modal['jyus02'] = '';
            $modal['jyus03'] = '';
            $modal['jyus04'] = $data['syu'];
            $modal['jyus04t'] = $data['syu']; // タイトル
            $res[1][5] = '';
            $modal['jyus05opt'] = '';
            if($data['modsyu'] == 'チェック'){
                $modal['jyus05opt'] = $koumoku['チェック'];
            }
            $modal['jyus06'] = '';
            $modal['jyus07'] = '';
            $modal['jyus08'] = '';
            $modal['jyus09'] = '';
            $modal['jyus10'] = '';
            $modal['jyus_hid'] = $modal['jyus01'] . ',' . $modal['jyus04'] . ',"","",""';
            
            $sql = 'select * from jyu_s where jyus01=? and jyus04=?;';
            $par = array($data['cd'],$data['syu']);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($par);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $modal['jyus01'] = $r['jyus01'];
                $modal['jyus02'] = $r["jyus02"];
                $modal['jyus03'] = $r["jyus03"];
                $modal['jyus04'] = $r["jyus04"];
                $modal['jyus04t'] = $r["jyus04"]; // タイトル
                $res[1][5] = $r["jyus05"];
                $modal['jyus06'] = $r["jyus06"];
                $modal['jyus07'] = $r["jyus07"];
                $modal['jyus08'] = $r['jyus08'];
                $modal['jyus09'] = $r['jyus09'];
                $modal['jyus10'] = $r['jyus10'];
                $modal['jyus_hid'] = $modal['jyus01'] . ',' . $modal['jyus04'] . ',' . $r["crdt"] . ',' . $r["jyus02"] . ',' . $r["jyus03"];
                if ($modal['jyus02'] == '0000-00-00') {
                    $modal['jyus02'] = '';
                }
                if ($modal['jyus03'] == '0000-00-00') {
                    $modal['jyus03'] = '';
                }
            }
        } 
        $res[0]['html'] = implode("", $modal);
        $res[0]['table'] = $data['table'];
        echo json_encode($res);
    }

    // 更新(JS)
    function upbtn1ClickJs() {
        $this->js1 = <<<"__"
        if($('#jyuk01')[0]){
            var obj = {
            'jyuk01':$('#jyuk01').val(),'jyuk02':$('#jyuk02').val(),
            'jyuk03':$('#jyuk03').val(),'jyuk04':$('#jyuk04').val(),
            'jyuk05':$('#jyuk05').val(),'jyuk06':$('#jyuk06').val(),
            'jyuk07':$('#jyuk07').val(),'jyuk08':$('#jyuk08').val(),
            'jyuk09':$('#jyuk09').val(),'jyuk10':$('#jyuk10').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'jyuk_hid':$('#jyuk_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}else if($('#jyui01')[0]){
            var obj = {
            'jyui01':$('#jyui01').val(),'jyui02':$('#jyui02').val(),
            'jyui03':$('#jyui03').val(),'jyui04':$('#jyui04').val(),
            'jyui05':$('#jyui05').val(),'jyui06':$('#jyui06').val(),
            'jyui07':$('#jyui07').val(),'jyui08':$('#jyui08').val(),
            'jyui09':$('#jyui09').val(),'jyui10':$('#jyui10').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'jyui_hid':$('#jyui_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}else if($('#nenc01')[0]){
            var obj = {
            'nenc01':$('#nenc01').val(),'nenc02':$('#nenc02').val(),
            'nenc03':$('#nenc03').val(),'nenc04':$('#nenc04').val(),
            'nenc05':$('#nenc05').val(),'nenc06':$('#nenc06').val(),
            'nenc07':$('#nenc07').val(),'nenc08':$('#nenc08').val(),
            'nenc09':$('#nenc09').val(),'nenc10':$('#nenc10').val(),
            'nenc11':$('#nenc11').val(),'nenc12':$('#nenc12').val(),
            'nenc13':$('#nenc13').val(),'nenc14':$('#nenc14').val(),
            'nenc15':$('#nenc15').val(),'nenc16':$('#nenc16').val(),
            'nenc17':$('#nenc17').val(),'nenc18':$('#nenc18').val(),
            'nenc19':$('#nenc19').val(),'nenc20':$('#nenc20').val(),           
            'nenc21':$('#nenc21').val(),'nenc22':$('#nenc22').val(),
            'nenc23':$('#nenc23').val(),'nenc24':$('#nenc24').val(),
            'nenc25':$('#nenc25').val(),'nenc26':$('#nenc26').val(),
            'nenc27':$('#nenc27').val(),'nenc28':$('#nenc28').val(),
            'nenc29':$('#nenc29').val(),'nenc30':$('#nenc30').val(),
            'nenc31':$('#nenc31').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'lab11':$('#lab11').text(),'lab12':$('#lab12').text(),
            'lab13':$('#lab13').text(),'lab14':$('#lab14').text(),
            'lab15':$('#lab15').text(),'lab16':$('#lab16').text(),
            'lab17':$('#lab17').text(),'lab18':$('#lab18').text(),
            'lab19':$('#lab19').text(),'lab20':$('#lab20').text(),
            'lab21':$('#lab21').text(),'lab22':$('#lab22').text(),
            'lab23':$('#lab23').text(),'lab24':$('#lab24').text(),
            'lab25':$('#lab25').text(),'lab26':$('#lab26').text(),
            'lab27':$('#lab27').text(),'lab28':$('#lab28').text(),
            'lab29':$('#lab29').text(),'lab30':$('#lab30').text(),
            'lab30':$('#lab30').text(),
            'nenc_hid':$('#nenc_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}else if($('#shin01')[0]){
            var obj = {
            'shin01':$('#shin01').val(),'shin02':$('#shin02').val(),
            'shin03':$('#shin03').val(),'shin04':$('#shin04').val(),
            'shin05':$('#shin05').val(),'shin06':$('#shin06').val(),
            'shin07':$('#shin07').val(),'shin08':$('#shin08').val(),
            'shin09':$('#shin09').val(),'shin10':$('#shin10').val(),
            'shin11':$('#shin11').val(),'shin12':$('#shin12').val(),
            'shin13':$('#shin13').val(),'shin14':$('#shin14').val(),
            'shin15':$('#shin15').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'lab11':$('#lab11').text(),'lab12':$('#lab12').text(),
            'lab13':$('#lab13').text(),'lab14':$('#lab14').text(),
            'lab15':$('#lab15').text(),
            'shin_hid':$('#shin_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}else if($('#jyus01')[0]){
            var obj = {
            'jyus01':$('#jyus01').val(),'jyus02':$('#jyus02').val(),
            'jyus03':$('#jyus03').val(),'jyus04':$('#jyus04').val(),
            'jyus05':$('#jyus05').val(),'jyus06':$('#jyus06').val(),
            'jyus07':$('#jyus07').val(),'jyus08':$('#jyus08').val(),
            'jyus09':$('#jyus09').val(),'jyus10':$('#jyus10').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'jyus_hid':$('#jyus_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}
        params = JSON.stringify(obj);
	console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#modal_ud').modal('hide');
                $('#sdate').trigger('change');
            }else{
                $('[name=button2]').prop("disabled",false);
                $('#modal_n').remove();
                $('#modalParent3').append(data[0]["html"]);
                $('#modal_n').modal({backdrop:'static'});
            }
__;
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#upbtn1', 'click', 'updateData', 'ajax');
    }

    // 新規・更新処理時のエラーチェック
    function validateData($data) {
        // エラーチェック
        if ($data['jyuk01']) { // 基本テーブル
            $this->validate($data['jyuk01'], 'str', array(1, 10, true), $data['lab01']);
            $this->validate($data['jyuk02'], 'date', array(0, 10, false), $data['lab02']);
            $this->validate($data['jyuk03'], 'date', array(1, 10, true), $data['lab03']);
            $this->validate($data['jyuk04'], 'date', array(0, 10, false), $data['lab04']);
            $this->validate($data['jyuk05'], 'date', array(0, 10, false), $data['lab05']);
            $this->validate($data['jyuk06'], 'date', array(0, 10, false), $data['lab06']);
            $this->validate($data['jyuk07'], 'date', array(0, 10, false), $data['lab07']);
            $this->validate($data['jyuk08'], 'str', array(0, 10, false), $data['lab08']);
            $this->validate($data['jyuk09'], 'str', array(0, 10, false), $data['lab09']);
            $this->validate($data['jyuk10'], 'str', array(0, 10, false), $data['lab10']);
            $this->validate($data['jyuk_hid'], 'str', array(1, 50, true), $data['lab_hid']);
        } elseif ($data['jyui01']) { // 詳細情報テーブル(共通)
            
            $this->validate($data['jyui01'], 'str', array(1, 10, true), $data['lab01']);
            $this->validate($data['jyui02'], 'date', array(0, 10, true), $data['lab02']);
            $this->validate($data['jyui03'], 'date', array(0, 10, true), $data['lab03']);
            $this->validate($data['jyui04'], 'str', array(1, 10, true), $data['lab04']);
            $this->validate($data['jyui_hid'], 'str', array(1, 50, true), $data['lab_hid']);
        } elseif ($data['nenc01']) { // 年調テーブル
            $this->validate($data['nenc01'], 'str', array(1, 10, true), $data['lab01']);
            $this->validate($data['nenc02'], 'date', array(10, 10, true), $data['lab02']);
            $this->validate($data['nenc03'], 'date', array(10, 10, true), $data['lab03']);
            $this->validate($data['nenc04'], 'str', array(0, 10, true), $data['lab04']);
            $this->validate($data['nenc05'], 'str', array(0, 1,false), $data['lab05']);
            $this->validate($data['nenc06'], 'str', array(0, 1,false), $data['lab06']);
            $this->validate($data['nenc07'], 'str', array(0, 1,false), $data['lab07']);
            $this->validate($data['nenc08'], 'str', array(0, 1,false), $data['lab08']);
            $this->validate($data['nenc09'], 'str', array(0, 1,false), $data['lab09']);
            $this->validate($data['nenc10'], 'str', array(0, 1,false), $data['lab10']);
            $this->validate($data['nenc11'], 'str', array(0, 1,false), $data['lab11']);
            $this->validate($data['nenc12'], 'str', array(0, 1,false), $data['lab12']);
            $this->validate($data['nenc13'], 'str', array(0, 1,false), $data['lab13']);
            $this->validate($data['nenc14'], 'str', array(0, 1,false), $data['lab14']);
            $this->validate($data['nenc15'], 'str', array(0, 1,false), $data['lab15']);
            $this->validate($data['nenc16'], 'str', array(0, 1,false), $data['lab16']);
            $this->validate($data['nenc17'], 'str', array(0, 1,false), $data['lab17']);
            $this->validate($data['nenc18'], 'str', array(0, 1,false), $data['lab18']);
            $this->validate($data['nenc19'], 'str', array(0, 1,false), $data['lab19']);
            $this->validate($data['nenc20'], 'str', array(0, 1,false), $data['lab20']);
            $this->validate($data['nenc21'], 'str', array(0, 1,false), $data['lab21']);
            $this->validate($data['nenc22'], 'str', array(0, 1,false), $data['lab22']);
            $this->validate($data['nenc23'], 'str', array(0, 1,false), $data['lab23']);
            $this->validate($data['nenc24'], 'str', array(0, 1,false), $data['lab24']);
            $this->validate($data['nenc25'], 'str', array(0, 1,false), $data['lab25']);
            $this->validate($data['nenc26'], 'str', array(0, 1,false), $data['lab26']);
            $this->validate($data['nenc27'], 'str', array(0, 1,false), $data['lab27']);
            $this->validate($data['nenc28'], 'str', array(0, 1,false), $data['lab28']);
            $this->validate($data['nenc29'], 'str', array(0, 1,false), $data['lab29']);
            $this->validate($data['nenc30'], 'str', array(0, 1,false), $data['lab30']);
            $this->validate($data['nenc31'], 'str', array(0, 2,false), $data['lab31']);
            $this->validate($data['nenc_hid'], 'str', array(1, 100, true), $data['lab_hid']);
        } elseif ($data['shin01']) { // 扶養親族テーブル
            $this->validate($data['shin01'], 'str', array(1, 10, true), $data['lab01']);
            $this->validate($data['shin02'], 'date', array(10, 10, true), $data['lab02']);
            $this->validate($data['shin03'], 'date', array(10, 10, true), $data['lab03']);
            $this->validate($data['shin04'], 'str', array(0, 10, true), $data['lab04']);
            $this->validate($data['shin05'], 'str', array(0, 30, false), $data['lab05']);
            $this->validate($data['shin06'], 'str', array(0, 30, false), $data['lab06']);
            $this->validate($data['shin07'], 'str', array(0, 30, false), $data['lab07']);
            $this->validate($data['shin08'], 'str', array(0, 30, false), $data['lab08']);
            $this->validate($data['shin09'], 'date', array(0, 10, false), $data['lab09']);
            $this->validate($data['shin10'], 'str', array(0, 5, false), $data['lab10']);
            $this->validate($data['shin11'], 'str', array(0, 10, false), $data['lab11']);
            $this->validate($data['shin12'], 'str', array(0, 10, false), $data['lab12']);
            $this->validate($data['shin13'], 'str', array(0, 10, false), $data['lab13']);
            $this->validate($data['shin14'], 'str', array(0, 10, false), $data['lab14']);
            $this->validate($data['shin15'], 'str', array(0, 10, false), $data['lab15']);
        } elseif ($data['jyus01']) { // jyus
            $this->validate($data['jyus01'], 'str', array(1, 10, true), $data['lab01']);
            $this->validate($data['jyus02'], 'date', array(0, 10, false), $data['lab02']);
            $this->validate($data['jyus03'], 'date', array(0, 10, false), $data['lab03']);
            $this->validate($data['jyus04'], 'str', array(1, 10, true), $data['lab04']);
            $this->validate($data['jyus05'], 'str', array(0, 30, false), $data['lab05']);
            $this->validate($data['jyus06'], 'str', array(0, 30, false), $data['lab06']);
            $this->validate($data['jyus07'], 'str', array(0, 30, false), $data['lab07']);
            $this->validate($data['jyus08'], 'str', array(0, 30, false), $data['lab08']);
            $this->validate($data['jyus09'], 'str', array(0, 30, false), $data['lab09']);
            $this->validate($data['jyus10'], 'str', array(0, 30, false), $data['lab10']);
            $this->validate($data['jyus_hid'], 'str', array(1, 50, true), $data['lab_hid']);
        }
        if (!$this->err) {
            if ($data['jyui04'] == 'パスワード') {
                if ($data['jyui05']) { // パスワードハッシュ化
                    $data['jyui05'] = password_hash($data['jyui05'], PASSWORD_DEFAULT); // パスワードはハッシュ化
                } else {
                    $data['jyui05'] = '';
                }
                $this->validate($data['jyui05'], 'str', array(0, 200, false), $data['lab05']); // パスワード（ハッシュ化）
            } elseif ($data['jyui04'] == '姓名') {
                $this->validate($data['jyui05'], 'str', array(0, 30, false), $data['lab05']);
                $this->validate($data['jyui06'], 'str', array(0, 30, false), $data['lab06']);
                $this->validate($data['jyui07'], 'str', array(0, 30, false), $data['lab07']);
                $this->validate($data['jyui08'], 'str', array(0, 30, false), $data['lab08']);
            } elseif ($data['jyui04'] == '住所') {
                $data['jyui05'] = str_replace('-', '', $data['jyui05']);
                $this->validate($data['jyui05'], 'int', array(0, 7, false), $data['lab05']);
                $data['jyui05'] = $this->format($data['jyui05'], 'zip-');
                $data['jyui06'] = mb_convert_kana($data['jyui06'], "ASKV");
                $data['jyui07'] = mb_convert_kana($data['jyui07'], "ASKV");
                $data['jyui08'] = mb_convert_kana($data['jyui08'], "ASKV");
                $data['jyui09'] = mb_convert_kana($data['jyui09'], "ASKV");
                $this->validate($data['jyui06'], 'str', array(0, 30, false), $data['lab06']);
                $this->validate($data['jyui07'], 'str', array(0, 30, false), $data['lab07']);
                $this->validate($data['jyui08'], 'str', array(0, 50, false), $data['lab08']);
                $this->validate($data['jyui09'], 'str', array(0, 50, false), $data['lab09']);
            } elseif ($data['jyui04'] == '国籍') {
                $this->validate($data['jyui05'], 'str', array(1, 5, true), $data['lab05']);
            } elseif ($data['jyui04'] == '最終学歴') {
                $this->validate($data['jyui05'], 'str', array(1, 15, false), $data['lab05']);
                $this->validate($data['jyui06'], 'str', array(1, 50, false), $data['lab06']);
            } elseif ($data['jyui04'] == '学生区分') {
                $this->validate($data['jyui05'], 'str', array(1, 5, false), $data['lab05']);
                $this->validate($data['jyui06'], 'str', array(1, 50, false), $data['lab06']);
            } elseif ($data['jyui04'] == '障碍者') {
                $this->validate($data['jyui05'], 'str', array(0, 5, false), $data['lab05']);
            } elseif ($data['jyui04'] == '携帯電話番号' or $data['jyui04'] == '自宅電話番号' or $data['jyui04'] == 'kmphone') {
                $this->validate($data['jyui05'], 'str', array(1, 15, true), $data['lab05']);
            } elseif ($data['jyui04'] == '個人メールアドレス') {
                $this->validate($data['jyui05'], 'str', array(1, 30, true), $data['lab05']);
            } elseif ($data['jyui04'] == '緊急連絡先') {
                $this->validate($data['jyui05'], 'str', array(1, 15, false), $data['lab05']);
                $this->validate($data['jyui06'], 'str', array(1, 20, false), $data['lab06']);
            } elseif ($data['jyui04'] == '所属部署') {
                $this->validate($data['jyui05'], 'str', array(1, 8, false), $data['lab05']);
            } elseif ($data['jyui04'] == '職群') {
                $this->validate($data['jyui05'], 'str', array(1, 8, false), $data['lab05']);
            } elseif ($data['jyui04'] == '管理領域') {
                $this->validate($data['jyui05'], 'str', array(1, 10, false), $data['lab05']);
            } elseif ($data['jyui04'] == '号数') {
                $this->validate($data['jyui05'], 'str', array(1, 8, false), $data['lab05']);
            } elseif ($data['jyui04'] == '拠点') {
                $this->validate($data['jyui05'], 'str', array(1, 8, false), $data['lab05']);
            } elseif ($data['jyui04'] == '管理権限') {
                $this->validate($data['jyui05'], 'str', array(1, 8, false), $data['lab05']);
            } elseif ($data['jyui04'] == '稟議権限' or $data['jyui04'] == '権限') {
                $this->validate($data['jyui05'], 'str', array(1, 8, false), $data['lab05']);
            } elseif ($data['jyui04'] == '通勤方法' or $data['jyui04'] == 'norimono') {
                $this->validate($data['jyui05'], 'str', array(1, 8, false), $data['lab05']);
            } elseif ($data['jyui04'] == '社宅有無') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 't_seikyu' or $data['jyui04'] == 't_shiharai') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 'furikomi') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 'kouza') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 'zeiku') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 'rousai' or $data['jyui04'] == 'koyou' or $data['jyui04'] == 'kaigo') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 'kenkou' or $data['jyui04'] == 'kousei') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 'jyumin') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 't_tsukin' or $data['jyui04'] == 't_seikyuk') {
                $this->validate($data['jyui05'], 'str', array(0, 8, false), $data['lab05']);
            } elseif ($data['jyui04'] == 't_teate1' or $data['jyui04'] == 't_teate2' or $data['jyui04'] == 't_teate3' or $data['jyui04'] == 't_teate4') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 'syotei') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif ($data['jyui04'] == 'youbi') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            } elseif (substr($data['jyui04'], 0, 3) == 'ks_' or substr($data['jyui04'], 0, 3) == 'kk_') {
                $this->validate($data['jyui05'], 'str', array(1, 8, true), $data['lab05']);
            }
        }
        if (!$this->err) {
            if ($data['jyuk01']) {
                $st = $data['jyuk02'];
                $en = $data['jyuk03'];
            } elseif ($data['jyui01']) { // jyui
                $st = $data['jyui02'];
                $en = $data['jyui03'];
            } elseif ($data['nenc01']) { // nenc
                $st = $data['nenc02'];
                $en = $data['nenc03'];
            } elseif ($data['jyus01']) { // jyus
                $st = $data['jyus02'];
                $en = $data['jyus03'];
            } elseif ($data['shin01']) { // shin
                $st = $data['shin02'];
                $en = $data['shin03'];
            }
            if ($st and $en and $st > $en) {
                $this->err = '期間が逆転しています。';
            }
        }
        // エラー表示
        $this->notice();
    }

    // 警告など
    function notice() {
        if ($this->err) {
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>' . $this->err . '</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }
    }

    // 更新(PHP)
    function updateData($data) {
        if($data['jyuk01']){
            if(!$data['jyuk03']){
                $data['jyuk03'] = '2200-01-01';
            }
        }elseif($data['jyui01']){
            $parwrk = explode(',', $data['jyui_hid']);   // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            if(!$data['jyui03'] and $parwrk[4] == '2200-01-01'){
                $data['jyui03'] = $parwrk[4];
            }
        }elseif($data['shin01']){
            $parwrk = explode(',', $data['shin_hid']);   // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            if(!$data['shin03'] and $parwrk[4] == '2200-01-01'){
                $data['shin03'] = $parwrk[4];
            }
        }
        $this->validateData($data); // エラーチェック+$this->errorを更新

        /*
          if (!$this->err and $data['jyuk01']) { // jyuk
          $parwrk = $data['jyuk01'];   // 元のキー情報（社員CD）
          } elseif (!$this->err and $data['jyui01']) { // jyui

          // 期間の重複チェック(比較する開始日付 <= 対象の終了日付 AND 比較する終了日付 >= 対象の開始日付 かつ 呼び出し元データでないもの)

          $sql = "select jyui01 from jyu_i where jyui01=? and jyui03>=? and jyui02<=? and jyui04=? and crdt!=?;";
          $stmt = $this->db->prepare($sql);
          $stmt->execute($par);
          while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
          if ($result['jyui01']) {
          $this->err = '登録期間が重複します。';
          }
          }
          } elseif (!$this->err and $data['nenc01']) { // nenc
          // 期間の重複チェック(比較する開始日付 <= 対象の終了日付 AND 比較する終了日付 >= 対象の開始日付 かつ元々のデータでないもの)
          $parwrk = explode(',', $data['nenc_hid']);   // 元のキー情報（社員CD　開始日　終了日　種別CD）
          $par = array($data['nenc01'], $data['nenc02'], $data['nenc03'], $parwrk[1], $parwrk[2]); // 社員CD　開始日　終了日 元の開始日　元の終了日
          $sql = "select nenc01 from nenchou where nenc01=? and nenc03>=? and nenc02<=? and (nenc02!=? and nenc03!=?);";
          $stmt = $this->db->prepare($sql);
          $stmt->execute($par);
          while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
          if ($result['nenc01']) {
          $this->err = '登録期間が重複します。';
          }
          }
          } elseif (!$this->err and $data['jyus01']) { // jyus
          // 期間の重複チェック(比較する開始日付 <= 対象の終了日付 AND 比較する終了日付 >= 対象の開始日付 かつ元々のデータでないもの)
          $parwrk = explode(',', $data['jyus_hid']);   // 元のキー情報（社員CD　開始日　終了日　種別CD）
          $par = array($data['jyus01'], $data['jyus02'], $data['jyus03'], $data['jyus04'], $data['jyus05'], $parwrk[1], $parwrk[2]); // 社員CD　開始日　終了日　種別CD 項目CD 元の開始日　元の終了日
          $sql = "select jyus01 from jyu_i where jyus01=? and jyus03>=? and jyus02<=? and jyus04=? and jyus05=? and (jyus02!=? and jyus03!=?);";
          $stmt = $this->db->prepare($sql);
          $stmt->execute($par);
          while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
          if ($result['jyus01']) {
          $this->err = '登録期間が重複します。';
          }
          }
          }
         */


        // update実行
        if (!$this->err and $data['jyuk01']) { // jyuk
            $par = array();
            $now = date('Y-m-d H:i:s');
            $sql = "update jyu_k set ";
            for ($i = 1; $i <= 10; $i++) {
                $a = sprintf('%02d', $i);
                $sql .= "jyuk" . $a . "=?,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",updt=?,upid=? where jyuk01=?;";
            for ($i = 1; $i <= 10; $i++) {
                $a = sprintf('%02d', $i);
                $par[] = $data["jyuk$a"];
            }
            array_push($par, $now, $_SESSION['従業員コード'], $data["jyuk01"]);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        } elseif (!$this->err and $data['jyui01']) { // jyui
            $parwrk = explode(',', $data['jyui_hid']);   // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            $now = date('Y-m-d H:i:s');

            $sql = "insert into jyu_i (jyui01,jyui02,jyui03,jyui04,jyui05,jyui06,jyui07,jyui08,jyui09,jyui10,crdt,crid,updt,upid) values ";
            if ($data['jyui04'] == '姓名') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '姓', $data['jyui05'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '名', $data['jyui06'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '姓カナ', $data['jyui07'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '名カナ', $data['jyui08'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } elseif ($data['jyui04'] == '住所') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '郵便番号', $data['jyui05'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '都道府県', $data['jyui06'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '市区町村', $data['jyui07'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '町域', $data['jyui08'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], 'アパート名など', $data['jyui09'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } elseif ($data['jyui04'] == '最終学歴') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '学歴＿区分', $data['jyui05'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '学歴＿学校名', $data['jyui06'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } elseif ($data['jyui04'] == '学生区分') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '学生＿区分', $data['jyui05'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '学生＿学校名', $data['jyui06'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } elseif ($data['jyui04'] == '緊急連絡先') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '緊急連絡先区分', $data['jyui05'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '緊急連絡先番号', $data['jyui06'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } else {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], $data['jyui04'], $data['jyui05'], '', '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            }
            $sql .= "on duplicate key update jyui02=values(jyui02),jyui03=values(jyui03),jyui05=values(jyui05),updt=values(updt),upid=values(upid);";

            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
//            $wrk[0]['sql'] = $sql;
//            $wrk[0]['par'] = $par;
            echo json_encode($wrk);
        } elseif (!$this->err and $data['nenc01']) { // nenc
            $parwrk = explode(',', $data['nenc_hid']);   // 元のキー情報（社員CD　開始日　終了日　種別CD）
            $par = array();
            $now = date('Y-m-d H:i:s');
            $sql = "update nenchou set ";
            for ($i = 1; $i <= 31; $i++) {
                $a = sprintf('%02d', $i);
                $sql .= "nenc" . $a . "=?,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",updt=?,upid=? where nenc01=? and nenc02=? and nenc03=? and nenc04=?;";
            for ($i = 1; $i <= 31; $i++) {
                $a = sprintf('%02d', $i);
                $par[] = $data["nenc$a"];
            }
            array_push($par, $now, $_SESSION['従業員コード'], $parwrk[0], $parwrk[1], $parwrk[2], $parwrk[3]);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        } elseif (!$this->err and $data['jyus01']) { // jyus
            $parwrk = explode(',', $data['jyus_hid']);   // 元のキー情報（社員CD　開始日　終了日　種別CD）
            $now = date('Y-m-d H:i:s');
            if($parwrk[2] == ''){
                $parwrk[2] = $now;
            }
            $par = array(
                    $data['jyus01'], $data['jyus02'], $data['jyus03'], $data['jyus04'], $data['jyus05'], $data['jyus06'], '', '', '', '', $parwrk[2], $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            $sql = "insert into jyu_s (jyus01,jyus02,jyus03,jyus04,jyus05,jyus06,jyus07,jyus08,jyus09,jyus10,crdt,crid,updt,upid) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $sql .= "on duplicate key update jyus02=values(jyus02),jyus03=values(jyus03),jyus05=values(jyus05),jyus06=values(jyus06),updt=values(updt),upid=values(upid);";
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        } elseif (!$this->err and $data['shin01']) { // shin
            $parwrk = explode(',', $data['shin_hid']);  // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            $par = array();
            $now = date('Y-m-d H:i:s');
            $sql = "update shinzoku set ";
            for ($i = 1; $i <= 15; $i++) {
                $a = sprintf('%02d', $i);
                $sql .= "shin" . $a . "=?,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",updt=?,upid=? where shin01=? and crdt=?;";
            for ($i = 1; $i <= 15; $i++) {
                $a = sprintf('%02d', $i);
                $par[] = $data["shin$a"];
            }
            array_push($par, $now, $_SESSION['従業員コード'],$parwrk[0],$parwrk[2]);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        }
    }

    // 新期間を追加(JS)
    function createbtn3ClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
	if($('#jyuk01')[0]){
            var obj = {
            'jyuk01':$('#jyuk01').val(),'jyuk02':$('#jyuk02').val(),
            'jyuk03':$('#jyuk03').val(),'jyuk04':$('#jyuk04').val(),
            'jyuk05':$('#jyuk05').val(),'jyuk06':$('#jyuk06').val(),
            'jyuk07':$('#jyuk07').val(),'jyuk08':$('#jyuk08').val(),
            'jyuk09':$('#jyuk09').val(),'jyuk10':$('#jyuk10').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'jyuk_hid':$('#jyuk_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}else if($('#jyui01')[0]){
            var obj = {
            'jyui01':$('#jyui01').val(),'jyui02':$('#jyui02').val(),
            'jyui03':$('#jyui03').val(),'jyui04':$('#jyui04').val(),
            'jyui05':$('#jyui05').val(),'jyui06':$('#jyui06').val(),
            'jyui07':$('#jyui07').val(),'jyui08':$('#jyui08').val(),
            'jyui09':$('#jyui09').val(),'jyui10':$('#jyui10').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'jyui_hid':$('#jyui_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}else if($('#nenc01')[0]){
            var obj = {
            'nenc01':$('#nenc01').val(),'nenc02':$('#nenc02').val(),
            'nenc03':$('#nenc03').val(),'nenc04':$('#nenc04').val(),
            'nenc05':$('#nenc05').val(),'nenc06':$('#nenc06').val(),
            'nenc07':$('#nenc07').val(),'nenc08':$('#nenc08').val(),
            'nenc09':$('#nenc09').val(),'nenc10':$('#nenc10').val(),
            'nenc11':$('#nenc11').val(),'nenc12':$('#nenc12').val(),
            'nenc13':$('#nenc13').val(),'nenc14':$('#nenc14').val(),
            'nenc15':$('#nenc15').val(),'nenc16':$('#nenc16').val(),
            'nenc17':$('#nenc17').val(),'nenc18':$('#nenc18').val(),
            'nenc19':$('#nenc19').val(),'nenc20':$('#nenc20').val(),           
            'nenc21':$('#nenc21').val(),'nenc22':$('#nenc22').val(),
            'nenc23':$('#nenc23').val(),'nenc24':$('#nenc24').val(),
            'nenc25':$('#nenc25').val(),'nenc26':$('#nenc26').val(),
            'nenc27':$('#nenc27').val(),'nenc28':$('#nenc28').val(),
            'nenc29':$('#nenc29').val(),'nenc30':$('#nenc30').val(),
            'nenc31':$('#nenc31').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'lab11':$('#lab11').text(),'lab12':$('#lab12').text(),
            'lab13':$('#lab13').text(),'lab14':$('#lab14').text(),
            'lab15':$('#lab15').text(),'lab16':$('#lab16').text(),
            'lab17':$('#lab17').text(),'lab18':$('#lab18').text(),
            'lab19':$('#lab19').text(),'lab20':$('#lab20').text(),
            'lab21':$('#lab21').text(),'lab22':$('#lab22').text(),
            'lab23':$('#lab23').text(),'lab24':$('#lab24').text(),
            'lab25':$('#lab25').text(),'lab26':$('#lab26').text(),
            'lab27':$('#lab27').text(),'lab28':$('#lab28').text(),
            'lab29':$('#lab29').text(),'lab30':$('#lab30').text(),
            'lab30':$('#lab30').text(),
            'nenc_hid':$('#nenc_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}else if($('#shin01')[0]){
            var obj = {
            'shin01':$('#shin01').val(),'shin02':$('#shin02').val(),
            'shin03':$('#shin03').val(),'shin04':$('#shin04').val(),
            'shin05':$('#shin05').val(),'shin06':$('#shin06').val(),
            'shin07':$('#shin07').val(),'shin08':$('#shin08').val(),
            'shin09':$('#shin09').val(),'shin10':$('#shin10').val(),
            'shin11':$('#shin11').val(),'shin12':$('#shin12').val(),
            'shin13':$('#shin13').val(),'shin14':$('#shin14').val(),
            'shin15':$('#shin15').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'lab11':$('#lab11').text(),'lab12':$('#lab12').text(),
            'lab13':$('#lab13').text(),'lab14':$('#lab14').text(),
            'lab15':$('#lab15').text(),
            'shin_hid':$('#shin_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}
        params = JSON.stringify(obj);
        //console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#modal_ud').modal('hide');
                $('#sdate').trigger('change');
            }else{
                $('[name=button2]').prop("disabled",false);
                $('#modal_n').remove();
                $('#modalParent3').append(data[0]["html"]);
                $('#modal_n').modal({backdrop:'static'});
            }
__;
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#createbtn3', 'click', 'createRangeData', 'ajax');
    }

    // 新期間を追加(PHP)
    function createRangeData($data) {
        if($data['jyui01']){
            $parwrk = explode(',', $data['jyui_hid']);   // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            if($parwrk[4] == '2200-01-01'){
                $data['jyui03'] = $parwrk[4];
            }else{
                $this->err = '新期間登録は最新のデータから行って下さい。'; // 選択レコードのjyui03は必ず2200-01-01
            }
            
            if(!$this->err and $data['jyui02'] <= $parwrk[3]){ // 新しい開始日が元データの開始日以下はエラー
                $this->err = '元データの対象となる期間がありません。 ' . $data['jyui02'] . ' ' . $parwrk[3];
            }
        }elseif($data['shin01']){
            $parwrk = explode(',', $data['shin_hid']);   // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            if($parwrk[4] == '2200-01-01'){
                $data['shin03'] = $parwrk[4];
            }else{
                $this->err = '新期間登録は最新のデータから行って下さい。'; // 選択レコードのshin03は必ず2200-01-01
            }
            
            if(!$this->err and $data['shin02'] <= $parwrk[3]){ // 新しい開始日が元データの開始日以下はエラー
                $this->err = '元データの対象となる期間がありません。 ' . $data['shin02'] . ' ' . $parwrk[3];
            }
        }

        $this->validateData($data); // エラーチェック+$this->errを更新
        
        // insert実行
        if (!$this->err and $data['jyui01']) { // jyui
            $parwrk = explode(',', $data['jyui_hid']);   // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            $now = date('Y-m-d H:i:s');
            $en = strtotime($data['jyui02']);
            $en = strtotime( "-1 day", $en);
            $en = date("Y-m-d",$en);
            $sql = "insert into jyu_i (jyui01,jyui02,jyui03,jyui04,jyui05,jyui06,jyui07,jyui08,jyui09,jyui10,crdt,crid,updt,upid) values ";
            if ($data['jyui04'] == '姓名') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    // 既存データの更新（終了日の設定）
                    $data['jyui01'], '', $en, '姓', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '名', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '姓カナ', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '名カナ', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    // 新規データの挿入
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '姓', $data['jyui05'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '名', $data['jyui06'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '姓カナ', $data['jyui07'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '名カナ', $data['jyui08'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } elseif ($data['jyui04'] == '住所') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    // 既存データの更新（終了日の設定）
                    $data['jyui01'], '', $en, '郵便番号', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '都道府県', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '市区町村', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '町域', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, 'アパート名など', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    // 新規データの挿入
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '郵便番号', $data['jyui05'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '都道府県', $data['jyui06'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '市区町村', $data['jyui07'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '町域', $data['jyui08'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], 'アパート名など', $data['jyui09'], '', '', '', '', '',$now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } elseif ($data['jyui04'] == '最終学歴') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    // 既存データの更新（終了日の設定）
                    $data['jyui01'], '', $en, '学歴＿区分', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '学歴＿学校名', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    // 新規データの挿入
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '学歴＿区分', $data['jyui05'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '学歴＿学校名', $data['jyui06'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } elseif ($data['jyui04'] == '学生区分') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    // 既存データの更新（終了日の設定）
                    $data['jyui01'], '', $en, '学生＿区分', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '学生＿学校名', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    // 新規データの挿入
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '学生＿区分', $data['jyui05'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '学生＿学校名', $data['jyui06'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } elseif ($data['jyui04'] == '緊急連絡先') {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    // 既存データの更新（終了日の設定）
                    $data['jyui01'], '', $en, '緊急連絡先区分', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    $data['jyui01'], '', $en, '緊急連絡先番号', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    // 新規データの挿入
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '緊急連絡先区分', $data['jyui05'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード'],
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], '緊急連絡先番号', $data['jyui06'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            } else {
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
                $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $par = array(
                    // 既存データの更新（終了日の設定）
                    $data['jyui01'], '', $en, $data['jyui04'], '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    // 新規データの挿入
                    $data['jyui01'], $data['jyui02'], $data['jyui03'], $data['jyui04'], $data['jyui05'], '', '', '', '', '', $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            }
            $sql .= "on duplicate key update jyui03=values(jyui03),updt=values(updt),upid=values(upid);";

            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        }elseif (!$this->err and $data['shin01']) { // shin
            $parwrk = explode(',', $data['shin_hid']);   // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            $now = date('Y-m-d H:i:s');
            $en = strtotime($data['shin02']);
            $en = strtotime( "-1 day", $en);
            $en = date("Y-m-d",$en);
            $sql = "insert into shinzoku (shin01,shin02,shin03,shin04,shin05,shin06,shin07,shin08,shin09,shin10,shin11,shin12,shin13,shin14,shin15,crdt,crid,updt,upid) values ";
            $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?),";
            $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
            $par = array(
                    // 既存データの更新（終了日の設定）
                    $data['shin01'], '', $en, $data['shin04'], '', '', '', '', '', '', '', '', '', '', '', $parwrk[2], '', $now, $_SESSION['従業員コード'],
                    // 新規データの挿入
                    $data['shin01'], $data['shin02'], $data['shin03'], $data['shin04'], $data['shin05'], $data['shin06'], $data['shin07'], $data['shin08'], $data['shin09'], $data['shin10'], $data['shin11'], $data['shin12'], $data['shin13'], $data['shin14'], $data['shin15'], $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']
                );
            $sql .= "on duplicate key update shin03=values(shin03),updt=values(updt),upid=values(upid);";

            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        }
    }

    // 新規データを追加(JS)
    function createbtn4ClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
	if($('#shin01')[0]){
            var obj = {
            'shin01':$('#shin01').val(),'shin02':$('#shin02').val(),
            'shin03':$('#shin03').val(),'shin04':$('#shin04').val(),
            'shin05':$('#shin05').val(),'shin06':$('#shin06').val(),
            'shin07':$('#shin07').val(),'shin08':$('#shin08').val(),
            'shin09':$('#shin09').val(),'shin10':$('#shin10').val(),
            'shin11':$('#shin11').val(),'shin12':$('#shin12').val(),
            'shin13':$('#shin13').val(),'shin14':$('#shin14').val(),
            'shin15':$('#shin15').val(),
            'lab01':$('#lab01').text(),'lab02':$('#lab02').text(),
            'lab03':$('#lab03').text(),'lab04':$('#lab04').text(),
            'lab05':$('#lab05').text(),'lab06':$('#lab06').text(),
            'lab07':$('#lab07').text(),'lab08':$('#lab08').text(),
            'lab09':$('#lab09').text(),'lab10':$('#lab10').text(),
            'lab11':$('#lab11').text(),'lab12':$('#lab12').text(),
            'lab13':$('#lab13').text(),'lab14':$('#lab14').text(),
            'lab15':$('#lab15').text(),
            'shin_hid':$('#shin_hid').val(),'lab_hid':$('#lab_hid').text()
            };
	}else if($('#jyus01')[0]){

	}
        params = JSON.stringify(obj);
        //console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#modal_ud').modal('hide');
                $('#sdate').trigger('change');
            }else{
                $('[name=button2]').prop("disabled",false);
                $('#modal_n').remove();
                $('#modalParent3').append(data[0]["html"]);
                $('#modal_n').modal({backdrop:'static'});
            }
__;
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#createbtn4', 'click', 'createNewData', 'ajax');
    }

    // 新規データを追加(PHP)
    function createNewData($data) {
        if (!$this->err and $data['shin01']) { // shin
            $parwrk = explode(',', $data['shin_hid']);   // 元のキー情報（社員CD　種別CD データ作成日時 元の開始日 元の終了日）
            if($parwrk[4] == ''){
               $parwrk[4] = '2200-01-01';
            }
            if($parwrk[4] == '2200-01-01'){
                $data['shin03'] = $parwrk[4];
            }else{
                $this->err = '新規登録は最新のデータから行って下さい。'; // 選択レコードのshin03は必ず2200-01-01
            }
            
            $this->validateData($data); // エラーチェック+$this->errを更新
            
            $now = date('Y-m-d H:i:s');
            $sql = "insert into shinzoku (shin01,shin02,shin03,shin04,shin05,shin06,shin07,shin08,shin09,shin10,shin11,shin12,shin13,shin14,shin15,crdt,crid,updt,upid) values ";
            $sql .= "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $par = array($data['shin01'], $data['shin02'], $data['shin03'], $data['shin04'], $data['shin05'], $data['shin06'], $data['shin07'], $data['shin08'], $data['shin09'], $data['shin10'], $data['shin11'], $data['shin12'], $data['shin13'], $data['shin14'], $data['shin15'], $now, $_SESSION['従業員コード'], $now, $_SESSION['従業員コード']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            echo json_encode($wrk);
        }
    }

    
    // 削除準備(JS)
    function delbtn1ClickJs() {
        $this->clearJs();
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
			$('#modal_d').remove();
			$('#modalParent2').append(data[0]['html']);
			$('#modal_d').modal({backdrop:'static'});
__;
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#delbtn1', 'click', 'delmodal', 'ajax');
    }

    // 
    function delmodal($data) {
        $modal = $this->readModalSource('modal_d');
        $res[0]['html'] = $modal;
        echo json_encode($res);
    }

    // 削除(JS)
    function delbtn2ClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
            //var obj = {'jyuk_hid':$('#jyuk_hid').val(),'lab_hid':$('#lab_hid').text()};
            var obj = {'jyuk01':$('#syid').text()};
            params = JSON.stringify(obj);
            console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            $('#sdate').trigger('change');
            $('#modal_d').modal('hide');
            $('#modal_ud').modal('hide');
__;
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#delbtn2', 'click', 'deleteData', 'ajax');
    }

    // 削除(PHP)
    function deleteData($data) {
        //$parwrk = explode(',',$data['jyuk_hid']);   // 元のキー情報（社員CD　開始日　終了日　種別CD）
        $sql = "delete from jyu_k where jyuk01=?;";
        $par = array($data['jyuk01']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        $sql = "delete from jyu_i where jyui01=?;";
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        $sql = "delete from jyu_s where jyus01=?;";
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
    }


    // 削除準備(JS)
    function delbtn1kClickJs() {
        $this->clearJs();
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $('#modal_dk').remove();
            $('#modalParent2').append(data[0]['html']);
            $('#modal_dk').modal({backdrop:'static'});
__;
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#delbtn1k', 'click', 'delmodalk', 'ajax');
    }

    // 
    function delmodalk($data) {
        $modal = $this->readModalSource('modal_dk');
        $res[0]['html'] = $modal;
        echo json_encode($res);
    }

    // 削除(JS)
    function delbtn2kClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {
                'jyui_hid':$('#jyui_hid').val(),'lab_hid':$('#lab_hid').text(),
                'jyus_hid':$('#jyus_hid').val(),'nenc_hid':$('#nenc_hid').val(),
                'shin_hid':$('#shin_hid').val()
            };
            params = JSON.stringify(obj);
            console.log(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
            if(data[0]['res']){
                $('#sdate').trigger('change');
                $('#modal_dk').modal('hide');
                $('#modal_ud').modal('hide');
            }else{
                $('[name=button2]').prop("disabled",false);
                $('#modal_n').remove();
                $('#modalParent3').append(data[0]["html"]);
                $('#modal_n').modal({backdrop:'static'});
            }
            
__;
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#delbtn2k', 'click', 'deleteDatak', 'ajax');
    }

    // 削除(PHP)
    function deleteDatak($data) {
        if ($data['jyui_hid']) {
            $hid = explode(',', $data['jyui_hid']); // 従業員CD 種別　作成日　元の開始日　元の終了日
            // 削除可能は最新のデータのみ（エラーチェック）
            if ($hid[4] != '2200-01-01') { // 終了日
                $this->err = '最新のデータ以外は削除できません。';
                $this->notice();
            }
            // 複数項目の判別
            if($hid[1] == '姓名'){
                $delk = array('姓','名','姓カナ','名カナ');
            }elseif ($hid[1] == '') {
                $delk = array();
            }else{
                $delk = array($hid[1]);
            }
            $cnt = count($delk);
            // 削除
            $now = date('Y-m-d H:i:s');
            $w = '';
            for($i=0; $i<$cnt; $i++){
                if($i != 0){
                    $w .= ' or jyui04=?';
                }
                $ad[] = $delk[$i];
            }
            $par = array($hid[0],$hid[2]);
            $par = array_merge($par,$ad);
            $sql = "delete from jyu_i where jyui01=? and crdt=? and (jyui04=?" . $w . ");";
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            // 元の開始日-1が終了日となっているデータの終了日を2200-01-01に
            $hi = strtotime($hid[3]);
            $hi = strtotime( "-1 day", $hi);
            $hi = date("Y-m-d",$hi);
            $par = array('2200-01-01',$now,$_SESSION['従業員コード'],$hid[0],$hi);
            $par = array_merge($par,$ad);
            $sql = "update jyu_i set jyui03=?,updt=?,upid=? where jyui01=? and jyui03=? and (jyui04=?" . $w . ");";
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            
        }elseif ($data['shin_hid']) {
            $hid = explode(',', $data['shin_hid']); // 従業員CD 種別　作成日　元の開始日　元の終了日
            // 削除可能は最新のデータのみ（エラーチェック）
            if ($hid[4] != '2200-01-01') { // 終了日
                $this->err = '最新のデータ以外は削除できません。';
                $this->notice();
            }
            // 削除
            $now = date('Y-m-d H:i:s');
            $par = array($hid[0],$hid[2]);
            $sql = "delete from shinzoku where shin01=? and crdt=?;";
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            // 元の開始日-1が終了日となっているデータの終了日を2200-01-01に
            $hi = strtotime($hid[3]);
            $hi = strtotime( "-1 day", $hi);
            $hi = date("Y-m-d",$hi);
            $par = array('2200-01-01',$now,$_SESSION['従業員コード'],$hid[0],$hi);
            $sql = "update shinzoku set shin03=?,updt=?,upid=? where shin01=? and shin03=?;";
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
            
        } elseif ($data['jyus_hid']) {
            $hid = explode(',', $data['jyus_hid']);
            $par = array($hid[0],$hid[1],$hid[2]);
            $now = date('Y-m-d H:i:s');
            $sql = "delete from jyu_s where jyus01=? and jyus04=? and crdt=?;";
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);
        }
        echo json_encode($wrk);
    }

    // データ検索 ＝ 日付変更(JS)
    function sdateChangeJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {"day":$('#sdate').val(),"syid":$('#syid').text()};
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||null);
            console.log(data);
            //$("#tablebody1 tr").remove();
            $("#tablebody1 tr td:not(.subj)").remove();
            $("#入社日").after(data['html'][0]);
            $("#姓名").after(data['html'][1]);
            $("#住所").after(data['html'][2]);
            $("#国籍").after(data['html'][3]);
            $("#学歴").after(data['html'][4]);
            $("#学生").after(data['html'][5]);
            $("#障碍者区分").after(data['html'][6]);
            $("#携帯電話番号").after(data['html'][7]);
            $("#自宅電話番号").after(data['html'][8]);
            $("#個人メール").after(data['html'][9]);
            $("#緊急連絡先").after(data['html'][10]);
            $("#所属部署コード").after(data['html'][11]);
            $("#職群").after(data['html'][12]);
            $("#号数").after(data['html'][13]);
            $("#拠点").after(data['html'][14]);
            $("#管理権限").after(data['html'][15]);
            $("#管理領域コード").after(data['html'][16]);
            $("#稟議権限").after(data['html'][17]);
            $("#権限").after(data['html'][18]);
            $("#通勤方法").after(data['html'][19]);
            $("#会社携帯電話番号").after(data['html'][20]);
            $("#会社メールアドレス").after(data['html'][21]);
            $("#社宅有無").after(data['html'][22]);

            $("#tablebody2 tr td:not(.subj)").remove();
            $("#請求単価").after(data['html'][23]);
            $("#振込日").after(data['html'][24]);
            $("#口座").after(data['html'][25]);
            $("#課税区分").after(data['html'][26]);
            $("#保険＿労災").after(data['html'][27]);
            $("#保険＿雇用").after(data['html'][28]);
            $("#保険＿健康").after(data['html'][29]);
            $("#保険＿厚生").after(data['html'][30]);
            $("#保険＿介護").after(data['html'][31]);
            $("#住民税").after(data['html'][32]);
            $("#支払交通費").after(data['html'][33]);
            
            $("#支払手当１").after(data['html'][34]);
            $("#支払手当２").after(data['html'][35]);
            $("#支払手当３").after(data['html'][36]);
            $("#支払手当４").after(data['html'][37]);
            $("#syotei").after(data['html'][38]);
            $("#youbi").after(data['html'][39]);
            $("#固定支給１").after(data['html'][40]);
            $("#固定支給２").after(data['html'][41]);
            $("#固定支給３").after(data['html'][42]);
            $("#固定支給４").after(data['html'][43]);
            $("#固定支給５").after(data['html'][44]);
            $("#固定支給６").after(data['html'][45]);
            $("#固定支給７").after(data['html'][46]);
            $("#固定支給８").after(data['html'][47]);
            $("#固定支給９").after(data['html'][48]);
            $("#固定支給１０").after(data['html'][49]);
            $("#固定控除１").after(data['html'][50]);
            $("#固定控除２").after(data['html'][51]);
            $("#固定控除３").after(data['html'][52]);
            $("#固定控除４").after(data['html'][53]);
            $("#固定控除５").after(data['html'][54]);
            $("#固定控除６").after(data['html'][55]);
            $("#固定控除７").after(data['html'][56]);
            $("#固定控除８").after(data['html'][57]);
            $("#固定控除９").after(data['html'][58]);
            $("#固定控除１０").after(data['html'][59]);
            $("#支払単価").after(data['html'][60]);

            $("#tablebody3 tr td:not(.subj)").remove();
            $("#nh_syougai").after(data['html'][61]);
            $("#nh_tokusyou").after(data['html'][62]);
            $("#nh_rounen").after(data['html'][63]);
            $("#nh_kafu").after(data['html'][64]);
            $("#nh_gakusei").after(data['html'][65]);
            $("#nh_otto").after(data['html'][66]);
            $("#nh_saigai").after(data['html'][67]);
            $("#nh_gaikoku").after(data['html'][68]);
            $("#nhg_ippan").after(data['html'][69]);
            $("#nhg_syougai").after(data['html'][70]);
            $("#nhg_tokusyou").after(data['html'][71]);
            $("#nhg_dktokusyou").after(data['html'][72]);
            $("#nhg_roujin").after(data['html'][73]);
            $("#nhg_rdktokusyou").after(data['html'][74]);
            $("#nrj_ippan").after(data['html'][75]);
            $("#nrj_doukyo").after(data['html'][76]);
            $("#nrj_dktokusyount").after(data['html'][77]);
            $("#nrj_dktokusyou").after(data['html'][78]);
            $("#nip_ippan").after(data['html'][79]);
            $("#nip_dktokusyou").after(data['html'][80]);
            $("#nip_dktokusyou16").after(data['html'][81]);
            $("#nip_tfuyou").after(data['html'][82]);
            $("#nip_dktokusyour").after(data['html'][83]);
            $("#nsg_ippan").after(data['html'][84]);
            $("#nsg_tokusyou").after(data['html'][85]);
            $("#nsg_dktokusyou").after(data['html'][86]);
            $("#shinzoku").after(data['html'][87]);
//            $("#扶養親族").after(data['html'][88]);

            $("#tablebody4 tr td:not(.subj)").remove();
            $("#menkyo").after(data['html'][89]);
            $("#shikaku").after(data['html'][90]);
            $("#kensyu").after(data['html'][91]);
            
            $("#ch_kihon").after(data['html'][92]);
            $("#ch_bicy").after(data['html'][93]);
            $("#ch_carbike").after(data['html'][94]);
            $("#ch_car").after(data['html'][95]);
            $("#請求交通費").after(data['html'][96]); // 請求交通費 289~
            $("#利用乗物").after(data['html'][97]); // 通勤利用乗物 292~
            $("#パスワード").after(data['html'][98]); // パスワード
            $("#応募者＿状態").after(data['html'][99]);
            $("#応募者＿備考").after(data['html'][100]);
            $("#応募者共有情報").after(data['html'][101]);
            $("#性別").after(data['html'][102]);
            $("#名").after(data['html'][103]);
            $("#生年月日").after(data['html'][106]);
            $("#扶養親族").after(data['html'][107]);
                
            //console.log(data['html'][96]);
//            $("#tablebody1").append(data[0]['html']);
            FixedMidashi.remove();
			FixedMidashi.create();
__;
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->js4 = "";

        $this->addEventListener('#sdate', 'change', 'searchData', 'ajax');
    }

    // 検索データ取得(PHP)
    function searchData($data) {
        if ($data['day'] == '') {
            $data['day'] = date('Y-m-d');
        }
        $wsrc = array();
        if ($data['syid']) {
            $par = array($data['syid']);
        } else {
            $par = array($_SESSION['post_dat']['act']);
        }

        // 選択用の項目を取得
        $sql = 'select koum01,group_concat(koum04) as "l",group_concat(koum05) as "m" from koumoku group by koum01 order by koum02;';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $k01 = $result['koum01'];
            $k04 = $result['l']; // 項目(カンマ区切)
            $k05 = $result['m']; // キー(カンマ区切)
            $k04ar = explode(',', $k04);
            $k05ar = explode(',', $k05);
            for ($i = 0; $i < count($k04ar); $i++) {
                $koumoku[$k01][$k05ar[$i]] = $k04ar[$i]; // ex [職群][sp] = スタッフ・PA
            }
        }
        
        // jyu_kテーブルは１社員に1レコードのみ
        $sql = "select * from jyu_k where jyuk01 = ? order by jyuk01;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        $i = 1;
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $jyuk01 = $result['jyuk01'];
            $jyuk02 = $result['jyuk02'];
            $jyuk03 = $result['jyuk03'];
            $jyuk04 = $result['jyuk04'];
            $jyuk05 = $result['jyuk05'];
            $jyuk06 = $result['jyuk06'];
            $jyuk07 = $result['jyuk07']; // 前回入社日
            $jyuk08 = $result['jyuk08']; // 前回退職日

            $obj = "{syu:'入社日',cd:$jyuk01,st:'$jyuk02',table:'jyu_k'}";
            $wrk = "<div>{$jyuk04}</div>";
            $wrk .= "<div>{$jyuk02} ～ {$jyuk03}</div>";
            $wrk .= "<div>{$jyuk05} ～ {$jyuk06}</div>";
            if ($jyuk02 <= $data['day'] and $jyuk03 >= $data['day']) { // 現在
                $wsrc[1] = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $wrk . '</a>';
            } elseif ($jyuk02 >= $data['day']) { // 未来
                $wsrc[2] = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $wrk . '</a>';
            }
            $wrk = "<div>{$jyuk07} ～ {$jyuk08}</div>";
            $wsrc[3] = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $wrk . '</a>';
        }

        // 基本以外の情報
        $sql = "select * from jyu_i where jyui01 = ? order by jyui04,jyui02;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        $su_ar = array();
        $su = 0;
        // $a : 最終結果 / $b = 結合用配列 / $w = ワーク配列
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $w = array(
                'j01' => $r['jyui01'],
                'j02' => $r['jyui02'],
                'j03' => $r['jyui03'],
                'j04' => $r['jyui04'],
                'j05' => $r['jyui05'],
            );
            $a[] = $w; // 最終結果にワーク配列を挿入

            $j04 = $w['j04']; // 種別
            $su_ar[$j04] = $su_ar[$j04] + 1; // レコード数(種別毎)
            $su = $su_ar[$j04];

            // 結合用配列にデータを挿入 $b[種別][番号][項目ID]
            $b[$j04][$su]['j01'] = $w['j01'];
            $b[$j04][$su]['j02'] = $w['j02'];
            $b[$j04][$su]['j03'] = $w['j03'];
            $b[$j04][$su]['j04'] = $w['j04'];
            $b[$j04][$su]['j05'] = $w['j05'];
        }
        unset($w);

        // 複数同時に更新する項目処理 ---------------------------------------
        // 姓名
        for ($i = 1; $i <= $su_ar['姓']; $i++) {
            $a[] = array(
                "j01" => $b['姓'][$i]['j01'],
                "j02" => $b['姓'][$i]['j02'],
                "j03" => $b['姓'][$i]['j03'],
                "j04" => '姓名',
                "j05" => '<div>' . $b['姓'][$i]['j05'] . '　' . $b['名'][$i]['j05'] . '</div><div>' . $b['姓カナ'][$i]['j05'] . '　' . $b['名カナ'][$i]['j05'] . '</div>');
        }
        // 住所
        for ($i = 1; $i <= $su_ar['郵便番号']; $i++) {
            $a[] = array(
                "j01" => $b['郵便番号'][$i]['j01'],
                "j02" => $b['郵便番号'][$i]['j02'],
                "j03" => $b['郵便番号'][$i]['j03'],
                "j04" => '住所',
                "j05" => '<div>〒 ' . $b['郵便番号'][$i]['j05'] . '</div><div>' . $b['都道府県'][$i]['j05'] . '　' . $b['市区町村'][$i]['j05'] . '</div><div>' . $b['町域'][$i]['j05'] . '</div><div>' . $b['アパート名など'][$i]['j05'] . '</div>');
        }
        // 最終学歴
        for ($i = 1; $i <= $su_ar['学歴＿区分']; $i++) {
            $a[] = array(
                "j01" => $b['学歴＿区分'][$i]['j01'],
                "j02" => $b['学歴＿区分'][$i]['j02'],
                "j03" => $b['学歴＿区分'][$i]['j03'],
                "j04" => '最終学歴',
                "j05" => $b['学歴＿区分'][$i]['j05'] . '　（' . $b['学歴＿学校名'][$i]['j05'] . '）');
        }
        // 学生区分
        for ($i = 1; $i <= $su_ar['学生＿区分']; $i++) {
            $a[] = array(
                "j01" => $b['学生＿区分'][$i]['j01'],
                "j02" => $b['学生＿区分'][$i]['j02'],
                "j03" => $b['学生＿区分'][$i]['j03'],
                "j04" => '学生区分',
                "j05" => $b['学生＿区分'][$i]['j05'] . '　（' . $b['学生＿学校名'][$i]['j05'] . '）');
        }
        // 緊急連絡先
        for ($i = 1; $i <= $su_ar['緊急連絡先区分']; $i++) {
            $a[] = array(
                "j01" => $b['緊急連絡先区分'][$i]['j01'],
                "j02" => $b['緊急連絡先区分'][$i]['j02'],
                "j03" => $b['緊急連絡先区分'][$i]['j03'],
                "j04" => '緊急連絡先',
                "j05" => $b['緊急連絡先区分'][$i]['j05'] . '　（' . $b['緊急連絡先番号'][$i]['j05'] . '）');
        }
        // 口座
        for ($i = 1; $i <= $su_ar['口座＿銀行コード']; $i++) {
            // 銀行・支店情報を取得
            $sql = 'select concat(gink04,"銀行 ",gsit04,"支店") as name from ginkou left join shiten on gink01 = gsit01 where gink01=? and gsit02=?;';
            $parw = array($b['口座＿銀行コード'][$i]['j05'],$b['口座＿支店コード'][$i]['j05']);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($parw);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $w = $r['name'];
            }
            $a[] = array(
                "j01" => $b['口座＿銀行コード'][$i]['j01'],
                "j02" => $b['口座＿銀行コード'][$i]['j02'],
                "j03" => $b['口座＿銀行コード'][$i]['j03'],
                "j04" => '口座',
                "j05" => '<div>' . $b['口座＿銀行コード'][$i]['j05'] . '-' . $b['口座＿支店コード'][$i]['j05'] . '</div><div>' . $w . '</div><div>【' . $b['口座＿種別'][$i]['j05'] . '】' . $b['口座＿番号'][$i]['j05'] . '</div>');
        }
        unset($w);
        // 手当・支給・控除
        $w_ar = array(
            '支払手当１','支払手当２','支払手当３','支払手当４',
            '固定支給１','固定支給２','固定支給３','固定支給４','固定支給５','固定支給６','固定支給７','固定支給８','固定支給９','固定支給１０',
            '固定控除１','固定控除２','固定控除３','固定控除４','固定控除５','固定控除６','固定控除７','固定控除８','固定控除９','固定控除１０'
            );
        foreach ($w_ar as $value) {
            $w_name1 = $value.'＿区分';
            if(substr($w_name1,0,4) == '支払手当'){
                $w_name2 = $value.'＿単価';
            }else{
                $w_name2 = $value.'＿金額';
            }
            for ($i = 1; $i <= $su_ar[$w_name1]; $i++) {
                if($b[$w_name2][$i]['j05'] != ''){
                    $w = '<div>' . $b[$w_name1][$i]['j05'] . '： ' . $b[$w_name2][$i]['j05'] . ' 円</div>';
                }
                $a[] = array(
                    "j01" => $b[$w_name1][$i]['j01'],
                    "j02" => $b[$w_name1][$i]['j02'],
                    "j03" => $b[$w_name1][$i]['j03'],
                    "j04" => $value,
                    "j05" => $w);
            }
        }
        unset($w_name1);
        unset($w_name2);
        unset($w_ar);
        unset($value);
        
        // 交通費
        $w_ar = array('請求交通費','支払交通費');
        foreach ($w_ar as $value) {
            $w_name1 = $value.'＿区分';
            $w_name2 = $value.'＿金額';
            $w_name3 = $value.'＿上限';
            for ($i = 1; $i <= $su_ar[$w_name1]; $i++) {
                if($b[$w_name2][$i]['j05'] != ''){
                    $w = '<div>' . $b[$w_name2][$i]['j05'] . ' 円／' . $b[$w_name1][$i]['j05'];
                }
                if($b[$w_name3][$i]['j05'] != ''){
                    $w .= '（max:' . $b[$w_name3][$i]['j05'] . '）</div>';
                }else{
                    $w .= '</div>';
                }
                $a[] = array(
                    "j01" => $b[$w_name1][$i]['j01'],
                    "j02" => $b[$w_name1][$i]['j02'],
                    "j03" => $b[$w_name1][$i]['j03'],
                    "j04" => $value,
                    "j05" => $w);
            }
        }
        unset($w_name1);
        unset($w_name2);
        unset($w_ar);
        unset($value);

        // 住民税
        for ($i = 1; $i <= $su_ar['住民税＿納付先コード']; $i++) {
            $sql2 = "select danta02,danta03 from tkdantai where danta01=?;";
            $par2 = array($b['住民税＿納付先コード'][$i]['j05']);
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute($par2);
            while ($r2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $w = $r2['danta02'] . ' ' . $r2['danta03'];
            }
            $a[] = array(
                "j01" => $b['住民税＿納付先コード'][$i]['j01'],
                "j02" => $b['住民税＿納付先コード'][$i]['j02'],
                "j03" => $b['住民税＿納付先コード'][$i]['j03'],
                "j04" => '住民税',
                "j05" => '<div>' . $b['住民税＿納付先コード'][$i]['j05'] . ' ' . $w . '</div><div>６月：' . $b['住民税＿６月'][$i]['j05'] . ' 円</div><div>７月：' . $b['住民税＿７月'][$i]['j05'] . ' 円</div><div>８月：' . $b['住民税＿８月'][$i]['j05'] . ' 円' . '</div>');
        }
                
        // 項目別処理 ---------------------------------------
        foreach ($a as $val) {
            $j01 = $val['j01'];
            $j02 = $val['j02'];
            $j03 = $val['j03'];
            $j04 = $val['j04'];
            $j05 = $val['j05'];

            // modsyu　 基本　カレンダー　選択　候補
            if ($j04 == '姓名') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'$j04'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $j05 . '</a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[4] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[5] .= $wrk;
                } //未来
                else {
                    $wsrc[6] .= $wrk;
                } //過去
            }
            if ($j04 == '性別') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[307] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[308] .= $wrk;
                } //未来
                else {
                    $wsrc[309] .= $wrk;
                } //過去
            }
            if ($j04 == '生年月日') {
                if ($j05 != '') {
                    $nenrei = (int) ((date('Ymd') - str_replace('-', '', $j05)) / 10000) . '歳';
                }
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'カレンダー'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a>';
                $wrk .= "<div>{$nenrei}　【{$j02} ～ {$j03}】</div>";
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[319] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[320] .= $wrk;
                } //未来
                else {
                    $wsrc[321] .= $wrk;
                } //過去
            }
            if ($j04 == '住所') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'$j04'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $j05 . '</a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[7] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[8] .= $wrk;
                } //未来
                else {
                    $wsrc[9] .= $wrk;
                } //過去
            }
            if ($j04 == 'パスワード') {
                if ($j05) {
                    $j05 = '設定あり';
                } else {
                    $j05 = '設定なし(ログイン不可)';
                }
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[295] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[296] .= $wrk;
                } //未来
                else {
                    $wsrc[297] .= $wrk;
                } //過去
            }
            if ($j04 == '応募者＿状態') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[298] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[299] .= $wrk;
                } //未来
                else {
                    $wsrc[300] .= $wrk;
                } //過去
            }
            if ($j04 == '応募者＿備考') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[301] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[302] .= $wrk;
                } //未来
                else {
                    $wsrc[303] .= $wrk;
                } //過去
            }
            if ($j04 == '応募者共有情報') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[304] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[305] .= $wrk;
                } //未来
                else {
                    $wsrc[306] .= $wrk;
                } //過去
            }
            if ($j04 == '最終学歴') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[13] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[14] .= $wrk;
                } //未来
                else {
                    $wsrc[15] .= $wrk;
                } //過去
            }
            if ($j04 == '学生区分') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[16] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[17] .= $wrk;
                } //未来
                else {
                    $wsrc[18] .= $wrk;
                } //過去
            }

            if ($j04 == '国籍') {
                $sql2 = "select cntry01,cntry02 from country where cntry01 = ?;";
                $par2 = array($j05);
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->execute($par2);
                while ($result2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $cntry02 = $result2['cntry02'];
                }
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'候補'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $cntry02 . ' .#' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[10] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[11] .= $wrk;
                } //未来
                else {
                    $wsrc[12] .= $wrk;
                } //過去
            }
            if ($j04 == '障碍者区分') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[19] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[20] .= $wrk;
                } //未来
                else {
                    $wsrc[21] .= $wrk;
                } //過去
            }
            if ($j04 == '携帯電話番号') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[22] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[23] .= $wrk;
                } //未来
                else {
                    $wsrc[24] .= $wrk;
                } //過去
            }
            if ($j04 == '自宅電話番号') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[25] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[26] .= $wrk;
                } //未来
                else {
                    $wsrc[27] .= $wrk;
                } //過去
            }
            if ($j04 == '個人メール') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[28] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[29] .= $wrk;
                } //未来
                else {
                    $wsrc[30] .= $wrk;
                } //過去
            }
            if ($j04 == '緊急連絡先') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $j05 . '</a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[31] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[32] .= $wrk;
                } //未来
                else {
                    $wsrc[33] .= $wrk;
                } //過去
            }

            if ($j04 == '所属部署コード') {
                $sql2 = "select bum04 from bumon where bum01 <= ? and bum02 >= ? and bum03 = ?;";
                $par2 = array($j03, $j03, $j05);
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->execute($par2);
                while ($result2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $bum04 = $result2['bum04'];
                }
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'候補'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $bum04 . ' .#' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[34] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[35] .= $wrk;
                } //未来
                else {
                    $wsrc[36] .= $wrk;
                } //過去
            }
            if ($j04 == '職群') {
                $koum04 = $koumoku['職群'][$j05];
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $koum04 . ' .#' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[37] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[38] .= $wrk;
                } //未来
                else {
                    $wsrc[39] .= $wrk;
                } //過去
            }
            if ($j04 == '号数') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[40] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[41] .= $wrk;
                } //未来
                else {
                    $wsrc[42] .= $wrk;
                } //過去
            }
            if ($j04 == '拠点') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[43] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[44] .= $wrk;
                } //未来
                else {
                    $wsrc[45] .= $wrk;
                } //過去
            }
            if ($j04 == '管理権限') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[46] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[47] .= $wrk;
                } //未来
                else {
                    $wsrc[48] .= $wrk;
                } //過去
            }
            if ($j04 == '管理領域コード') {
                // 管理領域用データ取得
                $sql2 = 'select grp04,grp05 from bgroup where grp02=?;';
                $stmt2 = $this->db->prepare($sql2);
                $par2 = array($j05);
                $stmt2->execute($par2);
                while ($result2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    if ($result2['grp05'] < 10) {
                        $grp04 = '★' . $result2['grp04'];
                    } else {
                        $grp04 = $result2['grp04'];
                    }
                }
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $grp04 . ' .#' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[49] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[50] .= $wrk;
                } //未来
                else {
                    $wsrc[51] .= $wrk;
                } //過去
            }
            if ($j04 == '稟議権限') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[52] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[53] .= $wrk;
                } //未来
                else {
                    $wsrc[54] .= $wrk;
                } //過去
            }
            if ($j04 == '権限') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[55] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[56] .= $wrk;
                } //未来
                else {
                    $wsrc[57] .= $wrk;
                } //過去
            }
            if ($j04 == '通勤方法') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[58] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[59] .= $wrk;
                } //未来
                else {
                    $wsrc[60] .= $wrk;
                } //過去
            }
            if ($j04 == '会社携帯電話番号') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[61] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[62] .= $wrk;
                } //未来
                else {
                    $wsrc[63] .= $wrk;
                } //過去
            }
            if ($j04 == '会社メールアドレス') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[64] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[65] .= $wrk;
                } //未来
                else {
                    $wsrc[66] .= $wrk;
                } //過去
            }
            if ($j04 == '社宅有無') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[67] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[68] .= $wrk;
                } //未来
                else {
                    $wsrc[69] .= $wrk;
                } //過去
            }
            if ($j04 == '利用乗物') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[292] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[293] .= $wrk;
                } //未来
                else {
                    $wsrc[294] .= $wrk;
                } //過去
                
                // 現在の利用乗物
                $today = date('Y-m-d');
                if ($j02 <= $today and $j03 >= $today) {
                    if ($j05 == '車') {
                        $ary_norimono = array('車');
                    } elseif ($j05 == '車とバイク') {
                        $ary_norimono = array('車','バイク');
                    } elseif ($j05 == '車と自転車') {
                        $ary_norimono = array('車','自転車');
                    } elseif ($j05 == 'バイク') {
                        $ary_norimono = array('バイク');
                    } elseif ($j05 == 'バイクと自転車') {
                        $ary_norimono = array('バイク','自転車');
                    } elseif ($j05 == '自転車') {
                        $ary_norimono = array('自転車');
                    } elseif ($j05 == '車とバイクと自転車') {
                        $ary_norimono = array('車','バイク','自転車');
                    }
                }
            }


            // tab2
            if ($j04 == '振込日') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . ' 日</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[73] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[74] .= $wrk;
                } else { //過去
                    $wsrc[75] .= $wrk;
                }
            }

            if ($j04 == '口座') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'$j04'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $j05 . '</a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[76] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[77] .= $wrk;
                } //未来
                else {
                    $wsrc[78] .= $wrk;
                } //過去
            }

            if ($j04 == '課税区分') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[79] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[80] .= $wrk;
                } else { //過去
                    $wsrc[81] .= $wrk;
                }
            }
            if ($j04 == '保険＿労災') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[82] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[83] .= $wrk;
                } else { //過去
                    $wsrc[84] .= $wrk;
                }
            }
            if ($j04 == '保険＿雇用') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[85] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[86] .= $wrk;
                } else { //過去
                    $wsrc[87] .= $wrk;
                }
            }
            if ($j04 == '保険＿健康') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[88] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[89] .= $wrk;
                } else { //過去
                    $wsrc[90] .= $wrk;
                }
            }
            if ($j04 == '保険＿厚生') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[91] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[92] .= $wrk;
                } else { //過去
                    $wsrc[93] .= $wrk;
                }
            }
            if ($j04 == '保険＿介護') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[94] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[95] .= $wrk;
                } else { //過去
                    $wsrc[96] .= $wrk;
                }
            }
            if ($j04 == '住民税') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'$j04'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $j05 . '</a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[97] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[98] .= $wrk;
                } else { //過去
                    $wsrc[99] .= $wrk;
                }
            }
            
            if ($j04 == '請求単価') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . ' 円</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) {
                    $wsrc[70] .= $wrk;
                } // 現在
                elseif ($j02 > $data['day']) {
                    $wsrc[71] .= $wrk;
                } //未来
                else {
                    $wsrc[72] .= $wrk;
                } //過去
            }
            // 181 - 183 t_shiharai
            if ($j04 == '請求交通費') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'$j04'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $j05 . '</a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[289] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[290] .= $wrk;
                } else { //過去
                    $wsrc[291] .= $wrk;
                }
            }

            if ($j04 == '支払単価') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'基本'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . ' 円</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[181] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[182] .= $wrk;
                } else { //過去
                    $wsrc[183] .= $wrk;
                }
            }

            if ($j04 == '支払交通費') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'$j04'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');">' . $j05 . '</a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[100] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[101] .= $wrk;
                } else { //過去
                    $wsrc[102] .= $wrk;
                }
            }

            if ($j04 == '支払手当１') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[103] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[104] .= $wrk;
                } else { //過去
                    $wsrc[105] .= $wrk;
                }
            }
            if ($j04 == '支払手当２') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[106] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[107] .= $wrk;
                } else { //過去
                    $wsrc[108] .= $wrk;
                }
            }
            if ($j04 == '支払手当３') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[109] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[110] .= $wrk;
                } else { //過去
                    $wsrc[111] .= $wrk;
                }
            }
            if ($j04 == '支払手当４') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[112] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[113] .= $wrk;
                } else { //過去
                    $wsrc[114] .= $wrk;
                }
            }
            if ($j04 == 'syotei') {
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[115] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[116] .= $wrk;
                } else { //過去
                    $wsrc[117] .= $wrk;
                }
            }
            if ($j04 == 'youbi') {
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[118] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[119] .= $wrk;
                } else { //過去
                    $wsrc[120] .= $wrk;
                }
            }
            if ($j04 == '固定支給１') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[121] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[122] .= $wrk;
                } else { //過去
                    $wsrc[123] .= $wrk;
                }
            }
            if ($j04 == '固定支給２') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[124] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[125] .= $wrk;
                } else { //過去
                    $wsrc[126] .= $wrk;
                }
            }
            if ($j04 == '固定支給３') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[127] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[128] .= $wrk;
                } else { //過去
                    $wsrc[129] .= $wrk;
                }
            }
            if ($j04 == '固定支給４') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[130] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[131] .= $wrk;
                } else { //過去
                    $wsrc[132] .= $wrk;
                }
            }
            if ($j04 == '固定支給５') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[133] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[134] .= $wrk;
                } else { //過去
                    $wsrc[135] .= $wrk;
                }
            }
            if ($j04 == '固定支給６') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[136] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[137] .= $wrk;
                } else { //過去
                    $wsrc[138] .= $wrk;
                }
            }
            if ($j04 == '固定支給７') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[139] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[140] .= $wrk;
                } else { //過去
                    $wsrc[141] .= $wrk;
                }
            }
            if ($j04 == '固定支給８') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[142] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[143] .= $wrk;
                } else { //過去
                    $wsrc[144] .= $wrk;
                }
            }
            if ($j04 == '固定支給９') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[145] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[146] .= $wrk;
                } else { //過去
                    $wsrc[147] .= $wrk;
                }
            }
            if ($j04 == '固定支給１０') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[148] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[149] .= $wrk;
                } else { //過去
                    $wsrc[150] .= $wrk;
                }
            }
            if ($j04 == '固定控除１') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[151] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[152] .= $wrk;
                } else { //過去
                    $wsrc[153] .= $wrk;
                }
            }
            if ($j04 == '固定控除２') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[154] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[155] .= $wrk;
                } else { //過去
                    $wsrc[156] .= $wrk;
                }
            }
            if ($j04 == '固定控除３') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[157] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[158] .= $wrk;
                } else { //過去
                    $wsrc[159] .= $wrk;
                }
            }
            if ($j04 == '固定控除４') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[160] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[161] .= $wrk;
                } else { //過去
                    $wsrc[162] .= $wrk;
                }
            }
            if ($j04 == '固定控除５') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[163] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[164] .= $wrk;
                } else { //過去
                    $wsrc[165] .= $wrk;
                }
            }
            if ($j04 == '固定控除６') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[166] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[167] .= $wrk;
                } else { //過去
                    $wsrc[168] .= $wrk;
                }
            }
            if ($j04 == '固定控除７') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[169] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[170] .= $wrk;
                } else { //過去
                    $wsrc[171] .= $wrk;
                }
            }
            if ($j04 == '固定控除８') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[172] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[173] .= $wrk;
                } else { //過去
                    $wsrc[174] .= $wrk;
                }
            }
            if ($j04 == '固定控除９') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[175] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[176] .= $wrk;
                } else { //過去
                    $wsrc[177] .= $wrk;
                }
            }
            if ($j04 == '固定控除１０') {
                $obj = "{syu:'$j04',cd:'$j01',st:'$j02',en:'$j03',table:'jyu_i',modsyu:'選択と入力'}";
                $wrk = '<a href="#" onClick="modalCallUd(' . $obj . ');"><div>' . $j05 . '　</div></a><div>【' . $j02 . ' ～ ' . $j03 . '】</div>';
                if ($j02 <= $data['day'] and $j03 >= $data['day']) { //現在
                    $wsrc[178] .= $wrk;
                } elseif ($j02 > $data['day']) { //未来
                    $wsrc[179] .= $wrk;
                } else { //過去
                    $wsrc[180] .= $wrk;
                }
            }

        }

        // 年調の情報
        $sql = "select * from nenchou where nenc01 = ? order by nenc02;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $obj = '{syu:"年調",cd:"' . $result["nenc01"] . '",st:"' . $result["nenc02"] . '",en:"' . $result["nenc03"] . '",table:"nenchou"}';
            if ($result['nenc02'] <= $data['day'] and $result['nenc03'] >= $data['day']) { //現在
                $wsrc[184] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc05']}　</div></a><div>【{$result['nenc02']} ～ {$result['nenc03']}】</div>";
                $wsrc[187] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc06']}　</div></a>";
                $wsrc[190] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc07']}　</div></a>";
                $wsrc[193] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc08']}　</div></a>";
                $wsrc[196] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc09']}　</div></a>";
                $wsrc[199] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc10']}　</div></a>";
                $wsrc[202] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc11']}　</div></a>";
                $wsrc[205] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc12']}　</div></a>";
                $wsrc[208] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc13']}　</div></a>";
                $wsrc[211] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc14']}　</div></a>";
                $wsrc[214] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc15']}　</div></a>";
                $wsrc[217] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc16']}　</div></a>";
                $wsrc[220] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc17']}　</div></a>";
                $wsrc[223] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc18']}　</div></a>";
                $wsrc[226] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc19']}　</div></a>";
                $wsrc[229] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc20']}　</div></a>";
                $wsrc[232] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc21']}　</div></a>";
                $wsrc[235] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc22']}　</div></a>";
                $wsrc[238] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc23']}　</div></a>";
                $wsrc[241] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc24']}　</div></a>";
                $wsrc[244] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc25']}　</div></a>";
                $wsrc[247] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc26']}　</div></a>";
                $wsrc[250] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc27']}　</div></a>";
                $wsrc[253] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc28']}　</div></a>";
                $wsrc[256] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc29']}　</div></a>";
                $wsrc[259] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc30']}　</div></a>";
                $wsrc[262] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc31']}　</div></a>";
                $wsrc[265] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc32']}　</div></a>";
            }
            elseif ($result['nenc02'] > $data['day']) { //未来
                $wsrc[184 + 1] .= '<a href="#" onClick="modalCallUd(' . $obj . ');">';
                $wsrc[184 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc05']}　</div></a>";
                $wsrc[187 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc06']}　</div></a>";
                $wsrc[190 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc07']}　</div></a>";
                $wsrc[193 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc08']}　</div></a>";
                $wsrc[196 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc09']}　</div></a>";
                $wsrc[199 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc10']}　</div></a>";
                $wsrc[202 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc11']}　</div></a>";
                $wsrc[205 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc12']}　</div></a>";
                $wsrc[208 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc13']}　</div></a>";
                $wsrc[211 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc14']}　</div></a>";
                $wsrc[214 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc15']}　</div></a>";
                $wsrc[217 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc16']}　</div></a>";
                $wsrc[220 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc17']}　</div></a>";
                $wsrc[223 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc18']}　</div></a>";
                $wsrc[226 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc19']}　</div></a>";
                $wsrc[229 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc20']}　</div></a>";
                $wsrc[232 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc21']}　</div></a>";
                $wsrc[235 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc22']}　</div></a>";
                $wsrc[238 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc23']}　</div></a>";
                $wsrc[241 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc24']}　</div></a>";
                $wsrc[244 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc25']}　</div></a>";
                $wsrc[247 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc26']}　</div></a>";
                $wsrc[250 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc27']}　</div></a>";
                $wsrc[253 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc28']}　</div></a>";
                $wsrc[256 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc29']}　</div></a>";
                $wsrc[259 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc30']}　</div></a>";
                $wsrc[262 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc31']}　</div></a>";
                $wsrc[265 + 1] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc32']}　</div></a>";
                $wsrc[265 + 1] .= '</a>';
            } else { //過去
                $wsrc[100] .= '<a href="#" onClick="modalCallUd(' . $obj . ');">';
                $wsrc[184 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc05']}　</div></a>";
                $wsrc[187 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc06']}　</div></a>";
                $wsrc[190 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc07']}　</div></a>";
                $wsrc[193 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc08']}　</div></a>";
                $wsrc[196 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc09']}　</div></a>";
                $wsrc[199 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc10']}　</div></a>";
                $wsrc[202 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc11']}　</div></a>";
                $wsrc[205 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc12']}　</div></a>";
                $wsrc[208 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc13']}　</div></a>";
                $wsrc[211 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc14']}　</div></a>";
                $wsrc[214 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc15']}　</div></a>";
                $wsrc[217 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc16']}　</div></a>";
                $wsrc[220 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc17']}　</div></a>";
                $wsrc[223 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc18']}　</div></a>";
                $wsrc[226 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc19']}　</div></a>";
                $wsrc[229 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc20']}　</div></a>";
                $wsrc[232 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc21']}　</div></a>";
                $wsrc[235 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc22']}　</div></a>";
                $wsrc[238 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc23']}　</div></a>";
                $wsrc[241 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc24']}　</div></a>";
                $wsrc[244 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc25']}　</div></a>";
                $wsrc[247 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc26']}　</div></a>";
                $wsrc[250 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc27']}　</div></a>";
                $wsrc[253 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc28']}　</div></a>";
                $wsrc[256 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc29']}　</div></a>";
                $wsrc[259 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc30']}　</div></a>";
                $wsrc[262 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc31']}　</div></a>";
                $wsrc[265 + 2] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['nenc32']}　</div></a>";
                $wsrc[265 + 2] .= '</a>';
            }
        }

        // 扶養親族情報
        $sql = "select * from shinzoku where shin01 = ? order by shin10 DESC,shin08 DESC;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if($result['shin11']){
                $result['shin11'] = '【' . $result['shin11']. '】';
            }
            if($result['shin12']){
                $result['shin12'] = '【' . $result['shin12']. '】';
            }
            $obj = '{syu:"扶養親族",cd:"' . $result["shin01"] . '",crdt:"' . $result["crdt"] . '",table:"shinzoku"}';
            if ($result['shin09'] != '0000-00-00') {
                $nenrei = (int) ((date('Ymd') - str_replace('-', '', $result['shin09'])) / 10000) . '歳'; // 年齢計算式
            }else{
                $nenrei = '';
            }
            if ($result['shin02'] <= $data['day'] and $result['shin03'] >= $data['day']) { //現在
                $wsrc[322] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['shin09']}　{$result['shin05']}{$result['shin06']}（{$result['shin10']}）{$nenrei}　{$result['shin11']}{$result['shin12']}</div></a><div>【{$result['shin02']} ～ {$result['shin03']}】</div>";
            }elseif ($result['shin02'] > $data['day']) { //未来
                $wsrc[323] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['shin09']}　{$result['shin05']}{$result['shin06']}（{$result['shin10']}）{$nenrei}　{$result['shin11']}{$result['shin12']}</div></a><div>【{$result['shin02']} ～ {$result['shin03']}】</div>";
            } else { //過去
                $wsrc[324] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>{$result['shin09']}　{$result['shin05']}{$result['shin06']}（{$result['shin10']}）{$nenrei}　{$result['shin11']}{$result['shin12']}</div></a><div>【{$result['shin02']} ～ {$result['shin03']}】</div>";
            }
        }
        if(!$wsrc[322]){
            $obj = '{syu:"扶養親族",cd:"' . $data['syid'] . '",crdt:"",table:"shinzoku"}';
            $wsrc[322] = "<a href='#' onClick='modalCallUd(" . $obj . ");'>　</a>";
        }
        
        // 技能・教育情報
        $sql = "select * from jyu_s where jyus01 = ? order by jyus07;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $obj = '{syu:"' . $result["jyus04"] . '",cd:"' . $result["jyus01"] . '",koumoku:"' . $result["jyus05"] . '",table:"jyu_s"}';
            // 268～
            if ($result['jyus04'] == 'menkyo') {
                $wsrc[268] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>【{$result['jyus02']} - {$result['jyus03']}】{$result['jyus06']}　{$result['jyus08']}</div>";
                if ($result['jyus09']) {
                    $wsrc[268] .= "<div>　{$result['jyus09']}</div>";
                }
                if ($result['jyus10']) {
                    $wsrc[268] .= "<div>　{$result['jyus10']}</div>";
                }
                $wsrc[268] .= "<div>　</div></a>";
            } elseif ($result['jyus04'] == 'shikaku') {
                $wsrc[268 + 3] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>【{$result['jyus02']} - {$result['jyus03']}】{$result['jyus06']}　{$result['jyus08']}</div>";
                if ($result['jyus09']) {
                    $wsrc[268 + 3] .= "<div>　{$result['jyus09']}</div>";
                }
                if ($result['jyus10']) {
                    $wsrc[268 + 3] .= "<div>　{$result['jyus10']}</div>";
                }
                $wsrc[268 + 3] .= "<div>　</div></a>";
            } elseif ($result['jyus04'] == 'kensyu') {
                $wsrc[268 + 6] .= "<a href='#' onClick='modalCallUd(" . $obj . ");'><div>【{$result['jyus02']} - {$result['jyus03']}】{$result['jyus06']}　{$result['jyus08']}</div>";
                if ($result['jyus09']) {
                    $wsrc[268 + 6] .= "<div>　{$result['jyus09']}</div>";
                }
                if ($result['jyus10']) {
                    $wsrc[268 + 6] .= "<div>　{$result['jyus10']}</div>";
                }
                $wsrc[268 + 6] .= "<div>　</div></a>";
            }
        }

        // 在職者配列作成
        unset($jy);
        $sql = "select * from jyu_k left join jyu_i on jyuk01=jyui01 and jyui04='姓' where jyuk03 = '2200-01-01';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $jy[$r['jyui01']] = $r['jyui05'];
        }
        
        // 技能・教育情報
        unset($s);
        unset($obj);
        $sql = "select * from jyu_s where jyus01 = ?;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if($result['jyus02'] == '0000-00-00'){
                $result['jyus02'] = '';
            }
            if($result['jyus03'] == '0000-00-00'){
                $result['jyus03'] = '';
            }
            if(!$result['jyus02'] and !$result['jyus03']){
                $result['kikan'] = '';
            }else{
                $result['kikan'] = '（' . $result['jyus02'] . ' ～ ' . $result['jyus03'] . '）';
            }
            $s[$result["jyus04"]] = $result;
            // チェック　or　スキル
            if(in_array($result['jyus04'], array('入社書類','入社２年','自転車保険','免許証','自賠責','任意保険','車検証','駐車場'))){
                $obj[$result["jyus04"]] = '{syu:"' . $result["jyus04"] .'",cd:"' . $result["jyus01"] . '",crdt:"' . $result["crdt"] . '",table:"jyu_s","modsyu":"チェック"}';
            }else{
                $obj[$result["jyus04"]] = '{syu:"' . $result["jyus04"] .'",cd:"' . $result["jyus01"] . '",crdt:"' . $result["crdt"] . '",table:"jyu_s","modsyu":"スキル"}';
            }
///////////            // 最後の番号：324　shin01 ==  ※項目追加後は必ず更新すること
        }
        
        // 入社書類
        $syu = '入社書類';
        if($s['入社書類']['jyus01']){
            $wsrc[268 + 9] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 9] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 未提出</div></a>";
        }
        // 入社2年チェック
        $syu = '入社２年';
        if($s['入社２年']['jyus01']){
            $wsrc[268 + 9] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 9] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 未チェック</div></a>";
        }

        // 自転車
        if(in_array('自転車', $ary_norimono)){
            $wsrc[268 + 12] .= "<div style='color:red;'>★自転車利用者★</div>";
        }
        $syu = '自転車保険';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 12] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 12] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }

        // バイク
        if(in_array('バイク', $ary_norimono)){
            $wsrc[268 + 15] .= "<div style='color:red;'>★バイク利用者★</div>";
        }
        $syu = '免許証';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 15] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 15] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }
        $syu = '自賠責';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 15] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 15] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }
        $syu = '任意保険';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 15] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 15] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }
        $syu = '車検証';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 15] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 15] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }

        // 車
        if(in_array('車', $ary_norimono)){
            $wsrc[268 + 18] .= "<div style='color:red;'>★車利用者★</div>";
        }
        $syu = '免許証';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }
        $syu = '自賠責';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }
        $syu = '任意保険';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }
        $syu = '車検証';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }
        $syu = '駐車場';
        if($s[$syu]['jyus01']){
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: {$s[$syu]['jyus05']}{$s[$syu]['kikan']}　{$s[$syu]['jyus06']}　# {$jy[$s[$syu]['upid']]}</div></a>";
        }else{
            $obj[$syu] = '{syu:"' . $syu .'",cd:"' . $par[0] . '",crdt:"",table:"jyu_s","modsyu":"チェック"}';
            $wsrc[268 + 18] .= "<a href='#' onClick='modalCallUd(" . $obj[$syu] . ");'><div>{$syu}: 入力なし </div></a>";
        }

        
        $src = '';
        for ($i = 1; $i < 400; $i++) {
            if (!$wsrc[$i]) {
                $wsrc[$i] = '<td></td>'; // <td></td>で置換
            } else {
                $wsrc[$i] = "<td>$wsrc[$i]</td>"; // <td>でくくる
            }
            $src .= $wsrc[$i];
            if ($i % 3 == 0) {
                $res['html'][] = $src; //$this->createTableSource($wrk);
                $src = '';
            }
        }
        $src = str_replace('*|*|*|*', '<td></td>', $wsrc);
        $res[0]['html'] = $wsrc; //$this->createTableSource($wrk);
        echo json_encode($res);
    }

    // 新規(JS)
    function createbtn2ClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
            var obj = {
            'jyuk01':$('#jyuk01c').val(),'jyuk02':$('#jyuk02c').val(),
            'jyuk03':$('#jyuk03c').val(),'jyuk04':$('#jyuk04c').val(),
            'jyuk05':$('#jyuk05c').val(),'jyuk06':$('#jyuk06c').val(),
            'jyuk07':$('#jyuk07c').val(),'jyuk08':$('#jyuk08c').val(),
            'jyuk09':$('#jyuk09c').val(),'jyuk10':$('#jyuk10c').val(),
            'jyuk11':$('#jyuk11c').val(),'jyuk12':$('#jyuk12c').val(),
            'jyuk13':$('#jyuk13c').val(),'jyuk14':$('#jyuk14c').val(),
            'jyuk15':$('#jyuk15c').val(),'jyuk16':$('#jyuk16c').val(),
            'jyuk17':$('#jyuk17c').val(),'jyuk18':$('#jyuk18c').val(),
            'jyuk19':$('#jyuk19c').val(),'jyuk20':$('#jyuk20c').val(),
            'jyuk21':$('#jyuk21c').val(),'jyuk22':$('#jyuk22c').val(),
            'jyuk23':$('#jyuk23c').val(),'jyuk24':$('#jyuk24c').val(),
            'jyuk25':$('#jyuk25c').val(),'jyuk26':$('#jyuk26c').val(),
            'jyuk27':$('#jyuk27c').val(),'jyuk28':$('#jyuk28c').val(),
            'jyuk29':$('#jyuk29c').val(),'jyuk30':$('#jyuk30c').val(),
            'lab01':$('#lab01c').text(),'lab02':$('#lab02c').text(),
            'lab03':$('#lab03c').text(),'lab04':$('#lab04c').text(),
            'lab05':$('#lab05c').text(),'lab06':$('#lab06c').text(),
            'lab07':$('#lab07c').text(),'lab08':$('#lab08c').text(),
            'lab09':$('#lab09c').text(),'lab10':$('#lab10c').text(),
            'lab11':$('#lab11c').text(),'lab12':$('#lab12c').text(),
            'lab13':$('#lab13c').text(),'lab14':$('#lab14c').text(),
            'lab15':$('#lab15c').text(),'lab16':$('#lab16c').text(),
            'lab17':$('#lab17c').text(),'lab18':$('#lab18c').text(),
            'lab19':$('#lab19c').text(),'lab20':$('#lab20c').text(),
            'lab21':$('#lab21c').text(),'lab22':$('#lab22c').text(),
            'lab23':$('#lab23c').text(),'lab24':$('#lab24c').text(),
            'lab25':$('#lab25c').text(),'lab26':$('#lab26c').text(),
            'lab27':$('#lab27c').text(),'lab28':$('#lab28c').text(),
            'lab29':$('#lab29c').text(),'lab30':$('#lab30c').text()
            };
            params = JSON.stringify(obj);
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            //console.log(data);
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
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#createbtn2', 'click', 'createData', 'ajax');
    }

    // 新規(PHP)
    function createData($data) {
        $this->err = '';
        // 受信データの加工
        $data['jyuk01'] = str_replace('-', '', $data['jyuk01']);
        $data['jyuk02'] = str_replace('-', '', $data['jyuk02']);

        // エラーチェック
        if (!$this->err) {
            $this->validate($data['jyuk01'], 'date', array(0, 10, false), $data['lab01']);
        } // 適用開始日
        if (!$this->err) {
            $this->validate($data['jyuk02'], 'date', array(0, 10, false), $data['lab02']);
        } // 適用終了日
        if (!$this->err) {
            $this->validate($data['jyuk03'], 'str', array(0, 0, false), $data['lab03']);
        } // 会社No.
        if (!$this->err) {
            $this->validate($data['jyuk04'], 'str', array(1, 10, true), $data['lab04']);
        } // 会社名
        if (!$this->err) {
            $this->validate($data['jyuk05'], 'str', array(1, 10, true), $data['lab05']);
        } // 会社名カナ
        if (!$this->err) {
            $this->validate($data['jyuk06'], 'str', array(1, 50, true), $data['lab06']);
        } // 会社名（正式）
        if (!$this->err) {
            $this->validate($data['jyuk07'], 'str', array(1, 50, true), $data['lab07']);
        } // 会社名カナ（正式）
        if (!$this->err) {
            $this->validate($data['jyuk08'], 'str', array(1, 100, false), $data['lab08']);
        } // 備考
        if (!$this->err) {
            $this->validate($data['jyuk09'], 'str', array(0, 20, false), $data['lab09']);
        } // 識別ID
        if (!$this->err) {
            $this->validate($data['jyuk10'], 'str', array(1, 2, false), $data['lab10']);
        } // 種別
        if (!$this->err) {
            $this->validate($data['jyuk11'], 'str', array(7, 8, false), $data['lab11']);
        } // 郵便番号
        if (!$this->err) {
            $this->validate($data['jyuk12'], 'str', array(0, 25, false), $data['lab12']);
        } // 都道府県
        if (!$this->err) {
            $this->validate($data['jyuk13'], 'str', array(0, 25, false), $data['lab13']);
        } // 市区町村
        if (!$this->err) {
            $this->validate($data['jyuk14'], 'str', array(0, 25, false), $data['lab14']);
        } // 町域
        if (!$this->err) {
            $this->validate($data['jyuk15'], 'str', array(0, 25, false), $data['lab15']);
        } // アパート
        if (!$this->err) {
            $this->validate($data['jyuk16'], 'str', array(0, 15, false), $data['lab16']);
        } // 電話番号
        if (!$this->err) {
            $this->validate($data['jyuk17'], 'str', array(0, 15, false), $data['lab17']);
        } // FAX番号
        if (!$this->err) {
            $this->validate($data['jyuk18'], 'str', array(0, 50, false), $data['lab18']);
        } // メールアドレス
        if (!$this->err) {
            $this->validate($data['jyuk19'], 'str', array(0, 25, false), $data['lab19']);
        } // 担当部署
        if (!$this->err) {
            $this->validate($data['jyuk20'], 'str', array(0, 25, false), $data['lab20']);
        } // 担当者名
        if (!$this->err) {
            $this->validate($data['jyuk21'], 'str', array(0, 2, false), $data['lab21']);
        } // 請求日

        if ($this->err) {
            $modal = $this->readModalSource('modal_n');
            $modal['body'] = '<div>' . $this->err . '</div>';
            $wrk[0]['html'] = implode($modal);
            $wrk[0]['res'] = false;
            echo json_encode($wrk);
            exit();
        }

        $today = date('Ymd');
        $now = date('Hi');
        // 採番
        $data['jyuk03'] = '';
        $sql = "select sai02 from saiban where sai01='jyukya';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data['jyuk03'] = $result['sai02'] + 1;
        }
        if ($data['jyuk03'] != '') {
            $par = array();
            $sql = "insert into jyukya (";
            for ($i = 1; $i <= $this->koumokusu; $i++) {
                $a = sprintf('%02d', $i);
                $sql .= "jyuk" . $a . ",";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for ($i = 1; $i <= $this->koumokusu; $i++) {
                $sql .= "?,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",?,?,?,?,?,?);";
            for ($i = 1; $i <= $this->koumokusu; $i++) {
                $a = sprintf('%02d', $i);
                $par[] = $data["jyuk$a"];
            }
            array_push($par, $today, $now, $_SESSION['従業員コード'], $today, $now, $_SESSION['従業員コード']);
            $stmt = $this->db->prepare($sql);
            $wrk[0]['res'] = $stmt->execute($par);

            if ($wrk[0]['res']) {
                $sql2 = "update saiban set sai02 = ? where sai01 = 'jyukya';";
                $par2 = array($data['jyuk03']);
                $stmt = $this->db->prepare($sql2);
                $wrk[0]['res'] = $stmt->execute($par2);
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
        $sql = "select * from kensakuptka where knsk01=? order by knsk02 DESC;";
        $stmt = $this->db->prepare($sql);
        $par = array($_SESSION['従業員コード']);
        $stmt->execute($par);
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $knsk02 = $result['knsk02'];
            $knsk03 = $result['knsk03'];
            $modal['combo0'] .= "<OPTION value='$knsk02'>$knsk03</OPTION>";
        }
        $wrk = array('' => '未選択', 'jyuk01' => '適用開始日', 'jyuk02' => '適用終了日', 'jyuk03' => '会社No.', 'jyuk04' => '会社名', 'jyuk05' => '会社名カナ', 'jyuk06' => '会社名（正式）', 'jyuk07' => '会社名カナ（正式）', 'jyuk08' => '備考', 'jyuk09' => '識別番号', 'jyuk10' => '種別', 'jyuk11' => '郵便番号', 'jyuk12' => '都道府県', 'jyuk13' => '市区町村', 'jyuk14' => '町域', 'jyuk15' => 'アパート', 'jyuk16' => '電話番号', 'jyuk17' => 'FAX番号', 'jyuk18' => 'メールアドレス', 'jyuk19' => '担当部署', 'jyuk20' => '担当者名', 'jyuk21' => '請求日');

        foreach ($wrk as $key => $val) {
            $combo1 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo1_1'] = $modal['combo1_2'] = $modal['combo1_3'] = $modal['combo1_4'] = $modal['combo1_n1'] = $modal['combo1_n2'] = $modal['combo1_n3'] = $modal['combo1_n4'] = $combo1;
        $wrk = array('' => '未選択', '=' => 'に等しい', '!=' => 'に等しくない', '>=' => '以上', '<=' => '以下', 'not in' => 'に含まれない', 'in' => 'に含まれる', 'between' => 'の間(以上/以下)', 'not between' => 'の間にない', 'like' => 'の文字を含む');
        foreach ($wrk as $key => $val) {
            $combo2 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo2_1'] = $modal['combo2_2'] = $modal['combo2_3'] = $modal['combo2_4'] = $combo2;

        $wrk = array('' => '', 'and' => 'かつ', 'or' => '又は');
        foreach ($wrk as $key => $val) {
            $combo3 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo3_1'] = $modal['combo3_2'] = $modal['combo3_3'] = $modal['combo3_4'] = $combo3;

        $wrk = array('' => '', '(' => '(');
        foreach ($wrk as $key => $val) {
            $combo4 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo4_1'] = $modal['combo4_2'] = $modal['combo4_3'] = $modal['combo4_4'] = $combo4;

        $wrk = array('' => '', ')' => ')');
        foreach ($wrk as $key => $val) {
            $combo5 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo5_1'] = $modal['combo5_2'] = $modal['combo5_3'] = $modal['combo5_4'] = $combo5;

        $wrk = array('' => '', 'ASC' => '昇順', 'DESC' => '降順');
        foreach ($wrk as $key => $val) {
            $combo6 .= "<OPTION value='$key'>$val</OPTION>";
        }
        $modal['combo6_n1'] = $modal['combo6_n2'] = $modal['combo6_n3'] = $modal['combo6_n4'] = $combo6;

        $res[0]['html'] = implode($modal);
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
            $par = array($_SESSION['従業員コード']);
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
            array_push($par, $_SESSION['従業員コード'], $wrkcd, $data["ptnm"], $data["ptdef"], $today, $now, $_SESSION['従業員コード'], $today, $now, $_SESSION['従業員コード']);
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
        $par = array($_SESSION['従業員コード']);
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
        $par = array($_SESSION['従業員コード'], $data['knsk02']);
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
        $par = array($_SESSION['従業員コード'], $data['knsk02']);
        $stmt = $this->db->prepare($sql);
        $wrk[0]['res'] = $stmt->execute($par);
        echo json_encode($wrk);
    }

    // 郵便番号から住所を取得(JS)
    function yubinKeyupJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        if(event.keyCode != 13){
            return false;
        }else if($('.yubin').val().length >= 7 && $('.yubin').val().length <= 8 && $('#jyui06').val() == ''){
            var obj = {"zip":$('.yubin').val()};
            params = JSON.stringify(obj);
        }
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
            $('#jyui06').val(data[0]['ken_name']);
            $('#jyui07').val(data[0]['city_name']);
            $('#jyui08').val(data[0]['town_name']);
//          console.log(data);
__;
        $this->addEventListener('.yubin', 'keyup', 'getZipData', 'ajax');
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

    // コピー準備(JS)
    function cpybtn1ClickJs() {
        $this->clearJs();
        $str = modalControl::appendSrc('modal2', 'data[0]["html"]', 'modalParent2');
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||"null");
            $str
__;
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#cpybtn1', 'click', 'cpymodal', 'ajax');
    }

    // 
    function cpymodal($data) {
        $id = 'modal2';
        $header = '<div class=modal-title><b>確認</b></div>';
        $body = '<div>このデータを従業員マスターに移行します。宜しいですか？</div>';
        $footer = <<<"__"
        <button type="button" class="btn btn-default" name="button2" id="cancelbtn2" data-dismiss="modal">いいえ</button>
        <button type="button" class="btn btn-danger" name="button2" id="cpybtn2">はい</button>
__;
        $params = array('id' => $id, 'size' => 'modal-sm', 'header' => $header, 'body' => $body, 'footer' => $footer);
        $wrk[0]['html'] = modalControl::createSrc($params);
        echo json_encode($wrk);
    }

    // コピー(JS)
    function cpybtn2ClickJs() {
        $this->clearJs();
        for ($i = 1; $i <= $this->koumokusu; $i++) {
            $a = sprintf('%02d', $i);
            $wrkstr .= '"stf' . $a . '":$("#stf' . $a . '").val(),';
        }
        $wrkstr = substr($wrkstr, 0, -1);
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
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('#cpybtn2', 'click', 'copyData', 'ajax');
    }

    // コピー(PHP)
    function copyData($data) {
        $today = date('Ymd');
        $now = date('Hi');
        // 従業員マスター内の同一人物重複チェック
        // コピー
        $sql = "select sai02 from saiban where sai01='jyugyoin';";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bango = $result["sai02"];
        }
        if ($bango) {
            //$data['stf01'] = $bango; // 採番した番号
            $sql = "update saiban set sai02 = (sai02 + 1) where sai01='jyugyoin';";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $par = array();
            $sql = "insert into jyugyoin (";
            for ($i = 1; $i <= 34; $i++) { // 従業員マスター項目数
                $a = sprintf('%02d', $i);
                $sql .= "jyu" . $a . ",";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",cday,ctim,cid,uday,utim,uid) values (";
            for ($i = 1; $i <= 34; $i++) {
                $sql .= "?,";
            }
            $sql = substr($sql, 0, -1);
            $sql .= ",?,?,?,?,?,?);";
            /*
              for($i=1; $i <= $this->koumokusu; $i++){
              $a = sprintf('%02d',$i);
              $par[] = $data["stf$a"];
              }
             */
            $par = array(
                "", $bango, $data['stf02'], $data['stf03'], "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "",
                "", "", "", ""
            );
            array_push($par, $today, $now, $_SESSION['従業員コード'], $today, $now, $_SESSION['従業員コード']);
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
    function excelbtnClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
		window.open('exceldown-7djq4thd51js0ahde.php','_blank');
__;
        $this->js2 = <<<"__"
__;
        $this->js3 = <<<"__"
            alert('エラーが発生しました。');
__;
        $this->addEventListener('', 'wait', 'excelDownload', '');
    }

    // エクセルダウンロード(PHP)
    function excelDownload($data) {
        // exceldown-7djq4thd51js0ahde.php にアクセス
    }

    // 銀行名から銀行コードを取得(JS)
    // 候補式モーダルの値確定(JS)
    // 住民税納付名から住民税納付先コード確定(JS)
    function jyui05sKeyupJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
            if(event.keyCode == 13)
            {
                var ary = $('#jyui05s').val().split('.#');
                if(ary.length == 2)
                {
                    $('#jyui05').val(ary[1]);
                }
                 else
                {
				    $('#jyui05').val('');
                }
            }
            return false;
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data);
        	$('.tsArea5').TapSuggest({
            	tsInputElement : '#jyui08',
            	tsArrayList : data[1],
            	tsRegExpAll : true
            });
            //console.log(data[1]);
__;
        $this->addEventListener('#jyui05s', 'keyup', 'getBranchOfficeData', 'ajax');
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
                $res[1][] = array($disp, $kana);
            }
        }
        echo json_encode($res);
    }

    // 支店名から支店コード確定(JS)
    function jyui08KeyupJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        if($('#jyui04').val()=='kouza'){
        	var ary = $('#jyui08').val().split('.#');
			if(ary.length == 2){
				$('#jyui07').val(ary[1]);
			}else{
				$('#jyui07').val('');
			}
        }else{
            return false;
        }
__;
        $this->js2 = <<<"__"
__;
        $this->addEventListener('#jyui08', 'keyup', '', '');
    }

    // tsAreaクリック(JS)
    function tsAreaClickJs() {
        $this->clearJs();
        $this->js1 = <<<"__"
        	var ary = $('#jyui05s').val().split('.#');
			if(ary.length == 2){
				$('#jyui05').val(ary[1]);
			}else{
				$('#jyui05').val('');
			}
            alert(1);
__;
        $this->js2 = <<<"__"
__;
        $this->addEventListener('.TapSuggest li', 'click', '', '');
    }

}

// ** page info ** //
$p = new page();

$data['pr1'] = array('title' => '従業員データ編集'); // ヘッダ
$data['pr2'] = true; // スタンダートDOM
//$data['pr3'] = array('active' => 'マスター', 'name' => $_SESSION['姓'] . '　' . $_SESSION['名']); // ナビメニュー

loadResource($p,$data);