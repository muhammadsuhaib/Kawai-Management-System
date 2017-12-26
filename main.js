// JSによるページ遷移
function jsSubmit(formName, url, method) {
    // サブミットするフォームを取得
    var f = document.forms[formName];
    f.method = method; // method(GET or POST)を設定する
    f.action = url; // action(遷移先URL)を設定する
    f.submit(); // submit する
    return true;
}

// カンマ追加
function numberFormat(num) {
    return String(num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
}

// 共通初期処理
$(document).ready(function () {
    // 孫モーダル(classにgran)起動 → キャンセル時のスクロール問題対処 
    $(document).on('hidden.bs.modal', '.gran', function () {
        $('body').addClass('modal-open');
    });
    // datepicker適用
    if ($('.datepicker')[0]) {
        $('.datepicker').bootstrapMaterialDatePicker({
            clearButton: true,
            clearText: 'クリア',
            cancelText: 'キャンセル',
            okText: '決定',
            time: false,
            lang: 'ja'
        });
        $('.timepicker').bootstrapMaterialDatePicker({
            clearButton: true,
            clearText: 'クリア',
            cancelText: 'キャンセル',
            okText: '決定',
            date: false,
            format: 'HH:mm',
            lang: 'ja'
        });
    }
    // モーダル起動後のdate(time)picker適用
    $(document).on('shown.bs.modal', '.modal', function () {
        $('.datepicker').bootstrapMaterialDatePicker({
            clearButton: true,
            clearText: 'クリア',
            cancelText: 'キャンセル',
            okText: '決定',
            time: false,
            lang: 'ja'
        });
        $('.timepicker').bootstrapMaterialDatePicker({
            clearButton: true,
            clearText: 'クリア',
            cancelText: 'キャンセル',
            okText: '決定',
            date: false,
            format: 'HH:mm',
            lang: 'ja'
        });
    });

});