<?php
require "main.php";


class abc extends  core
{


    public function test(){

            $ym = $_REQUEST['ym'];
            $id = $_REQUEST['check'];

    $b=0;
            foreach ($id as $getId)
            {
                $stmt = $this->db->query("UPDATE bumon SET bum30='$ym' WHERE bum03='".$getId."'");
                if ($stmt)
                $b=1;
                    else{
                    echo "not";
                }

            }
            if($b) {
                echo "<script>

    window.location='shime1.php?msg=1';
    </script>
";

            }
    }
}

$ob = new abc;

$ob->test();