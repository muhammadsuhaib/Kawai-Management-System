$(function () {
    $(function (event, params) {
        // テンプレサンプルデータ削除
        $("#tablebody tr").remove();
        $("#tablebody td").remove();

        // ボタン無効化解除と孫モーダル起動時のスクロール問題対処 
        $('#modalParent1').on('hidden.bs.modal', function () {
            $('[name=button1]').prop("disabled", false);
        });
        $('#modalParent2').on('hidden.bs.modal', function () {
            $('[name=button2]').prop("disabled", false);
            $('body').addClass('modal-open');
        });

        // キャンセルボタン処理
        $(document).on('click', '#cancelbtn1', function () {
            $('#modal1').modal('hide');
        });
        $(document).on('click', '#cancelbtn2', function () {
            $('#modal2').modal('hide');
        });
    });

    function modalCall(event, params) {
        $.ajax({
            type: "POST",
            url: "masterBU1.php",
            data: {
                ajax: "ajax",
                func: "modalCall",
                dat: params
            },
            error: function () {},
            success: function (json_data) {
                var data = JSON.parse(json_data);
                $('#modal1').remove();
                $('#modalParent1').append(data[0]['html']);
                $('#modal1').modal({
                    backdrop: 'static'
                });
            }
        });
    };
    $(document).on('click', '[name=button1]', function (event, params) {
        $('[name=button1]').prop("disabled", true);
        var obj = {
            "bum01": event.target.value
        };
        params = JSON.stringify(obj);
        modalCall(event, params);
    });

    function updateList(event, params) {
        $.ajax({
            type: "POST",
            url: "masterBU1.php",
            data: {
                ajax: "ajax",
                func: "updateList",
                dat: params
            },
            error: function () {},
            success: function (json_data) {
                var data = JSON.parse(json_data);
                $("#tablebody tr").remove();
                $("#tablebody td").remove();
                var str = '';
                for (var i = 0; i < data[0]['len']; i++) {
                    str += '<tr>'
                    str += '<td><div><button name="button1" style="width:50px;" type="button" value="' + data[i]['kaisb01'] + '-' + data[i]['bum01'] + '">' + data[i]['kaisb01'] + '-' + data[i]['bum01'] + '</button></div></td>';
                    str += '<td><div>' + data[i]['kaisb02'] + '　' + data[i]['bum02'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum03'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum04'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum05'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum06'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum07'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum08'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum09'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum10'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum11'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum12'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum13'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum14'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum15'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum16'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum17'] + '</div></td>';
                    str += '<td><div>' + data[i]['bum18'] + '</div></td>';
                    str += '<td><div>' + data[i]['cday'] + '</div></td>';
                    str += '<td><div>' + data[i]['ctim'] + '</div></td>';
                    str += '<td><div>' + data[i]['cid'] + '</div></td>';
                    str += '<td><div>' + data[i]['uday'] + '</div></td>';
                    str += '<td><div>' + data[i]['utim'] + '</div></td>';
                    str += '<td><div>' + data[i]['uid'] + '</div></td>';
                    str += '</tr>'
                }
                $("#tablebody").append(str);
                FixedMidashi.remove();
                FixedMidashi.create();
            }
        });
    };
    $(function (event, params) {

        updateList(event, params);
    });

    function createData(event, params) {
        $.ajax({
            type: "POST",
            url: "masterBU1.php",
            data: {
                ajax: "ajax",
                func: "createData",
                dat: params
            },
            error: function () {
                $('[name=button2]').prop("disabled", false);
                alert('エラーが発生しました。');
            },
            success: function (json_data) {
                var data = JSON.parse(json_data);
                if (data[0]['res']) {
                    $('#modal1').modal('hide');
                    listUpdate();
                } else {
                    $('[name=button2]').prop("disabled", false);
                    alert('重複するCDがあり登録できません');
                }
            }
        });
    };
    $(document).on('click', '#createbtn2', function (event, params) {
        $('[name=button2]').prop("disabled", true);
        var obj = {
            "bum01": $("#bum01").val(),
            "bum02": $("#bum02").val(),
            "bum03": $("#bum03").val(),
            "bum04": $("#bum04").val(),
            "bum05": $("#bum05").val(),
            "bum06": $("#bum06").val(),
            "bum07": $("#bum07").val(),
            "bum08": $("#bum08").val(),
            "bum09": $("#bum09").val(),
            "bum10": $("#bum10").val(),
            "bum11": $("#bum11").val(),
            "bum12": $("#bum12").val(),
            "bum13": $("#bum13").val(),
            "bum14": $("#bum14").val(),
            "bum15": $("#bum15").val(),
            "bum16": $("#bum16").val(),
            "bum17": $("#bum17").val(),
            "bum18": $("#bum18").val()
        };
        params = JSON.stringify(obj);
        createData(event, params);
    });

    function updateData(event, params) {
        $.ajax({
            type: "POST",
            url: "masterBU1.php",
            data: {
                ajax: "ajax",
                func: "updateData",
                dat: params
            },
            error: function () {
                $('[name=button2]').prop("disabled", false);
                alert('エラーが発生しました。');
            },
            success: function (json_data) {
                var data = JSON.parse(json_data);
                $('#modal1').modal('hide');
                listUpdate();
            }
        });
    };
    $(document).on('click', '#upbtn1', function (event, params) {
        $('[name=button2]').prop("disabled", true);
        var obj = {
            "bum01": $("#bum01").val(),
            "bum02": $("#bum02").val(),
            "bum03": $("#bum03").val(),
            "bum04": $("#bum04").val(),
            "bum05": $("#bum05").val(),
            "bum06": $("#bum06").val(),
            "bum07": $("#bum07").val(),
            "bum08": $("#bum08").val(),
            "bum09": $("#bum09").val(),
            "bum10": $("#bum10").val(),
            "bum11": $("#bum11").val(),
            "bum12": $("#bum12").val(),
            "bum13": $("#bum13").val(),
            "bum14": $("#bum14").val(),
            "bum15": $("#bum15").val(),
            "bum16": $("#bum16").val(),
            "bum17": $("#bum17").val(),
            "bum18": $("#bum18").val()
        };
        params = JSON.stringify(obj);
        updateData(event, params);
    });

    function delbtn2ClickJs(event, params) {

    };
    $(document).on('click', '#delbtn1', function (event, params) {
        var diag = '<div class="modal" id="modal2" tabindex="-1"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><div class=modal-title><b>確認</b></div></div><div class="modal-body"><div>削除します。宜しいですか？</div></div><div class="modal-footer"><button type="button" class="btn btn-default" name="button2" id="cancelbtn2">いいえ</button><button type="button" class="btn btn-danger" name="button2" id="delbtn2">はい</button></div></div></div></div>';
        $('#modal2').remove();
        $('#modalParent2').append(diag);
        $('#modal2').modal({
            backdrop: 'static'
        });
        delbtn2ClickJs(event, params);
    });

    function deleteData(event, params) {
        $.ajax({
            type: "POST",
            url: "masterBU1.php",
            data: {
                ajax: "ajax",
                func: "deleteData",
                dat: params
            },
            error: function () {
                $('[name=button2]').prop("disabled", false);
                alert('エラーが発生しました。');
            },
            success: function (json_data) {
                var data = JSON.parse(json_data);
                $('#modal2').modal('hide');
                $('#modal1').modal('hide');
                listUpdate();
            }
        });
    };
    $(document).on('click', '#delbtn2', function (event, params) {
        $('[name=button2]').prop("disabled", true);
        var obj = {
            "bum01": $("#bum01").val()
        };
        params = JSON.stringify(obj);
        deleteData(event, params);
    });
});