<?php
// ** closing ** //
// CLASS INCLUDE
require_once('main.php');
require_once('incl/htmlk.php');

// ★ POST GET
//if(isset($_REQUEST['msg']))
//{
//    if($_REQUEST['msg']==1)
//        echo "Record has been updated";
//}


class page extends core {

    function initJs() {
        $this->clearJs();

        $date = new DateTime(date('Ymd'));
        $date->modify("+1 months");

        for($i=0;$i<=12;$i++){
            $date->modify("-1 months");
            $value = $date->format('Ym');
            $display = $date->format('Y年m月');
            $dateopt .= "<option value=$value>$display</option>";
        }

        $sql = "select * from koumoku where koum01='締め日' order by koum02";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $option .= $r['koum03'];
        }

        $this->js1 = <<<"__"
            $('#ym').append('$dateopt');
            $('#closingday').append('$option');
            $('#ym').trigger('change');
__;
        $this->addEventListener('', '', '', '');
    }

    function changeDateJs(){
        $this->clearJs();
        $this->js1 .= <<<"__"
            var obj = {ym:$('#ym').val()+'01',closing:$('#closingday').val()};
            params = JSON.stringify(obj);
            $('#json_data_stock').val(params);
            $('#modalLoader1').modal({backdrop:'static'});
__;
        $this->js2 = <<<"__"
            var data = JSON.parse(json_data||null);
                $("#tabu1").tabulator("clearData");
                $("#tabu1").tabulator("setData", data[0]);
__;
        $this->js3 = <<<"__"
            $('[name=button2]').prop("disabled",false);
            alert('error');
__;
        $this->js4 = "$('#modal_search').modal('hide'); $('#modalLoader1').modal('hide');";

        $this->addEventListener('#ym', 'change', 'searchData', 'ajax');
        $this->addEventListener('#closingday', 'change', 'searchData', 'ajax');
        $this->addEventListener('#upbtn1', 'click', 'updateData', 'ajax');

    }

    function searchData($data) {

        $sql = "select * from bumon left join namae on upid=cd where bum01<=? and bum02>=? and bum20=?";
        $par = array($data['ym'],$data['ym'],$data['closing']);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($par);
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tabuwrk['dummy'] = '　';
            $tabuwrk['department'] = $r['bum06'];
            $tabuwrk['closing'] = $r['bum20'] . '日';
            if($r['bum30']){
                $tabuwrk['ym'] = substr($r['bum30'],0,4) . '-' .substr($r['bum30'],4,2);
            }else{
                $tabuwrk['ym'] = '';
            }
			
			
            $sql_update = "UPDATE bumon SET bum30='201711' WHERE bum03='".$r['bum03']."'";
			$this->db->prepare($sql_update);

			
            $tabuwrk['update'] = $r['updt'];
            $tabuwrk['updater'] = $r['nm']; //$r['upid'];
            $tabuwrk['check'] = '<input type="checkbox" id="check" value="'.$r['bum03'].'"> *';
            $tabudata[] = $tabuwrk;



        }

        $res[0] = $tabudata;
        echo json_encode($res);
        exit();
    }

     public function test()
     {
        echo"test";
     }


}

$p = new page();

$data['pr1'] = array('title' => 'closing');
$data['pr2'] = true; // standard DOM
$data['pr3'] = array('active' => '売上・請求'); // nva menu

loadResource($p,$data);