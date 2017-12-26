<?php
// ** HTML書き換えクラス ** //
// ★ $p はpage で global利用 //
class module
{
    static function useModuleTemplate($path)
    {
        if (file_exists($path))
        {
            $template = file_get_contents($path);
            $src = explode('<!-- del -->',$template);
            $htmlcode = '';
            foreach ($src as $key => $value)
            {
                if($key % 2 == 0) // 偶数番目を残す（delimiterに挟まれた箇所は削除）
                {
                    $htmlcode = $htmlcode . $value;
                }
            }
            $src = explode('||',$htmlcode);
            foreach ($src as $key => $value)
            {
                if($key % 2 == 1) // 奇数番目（delimiterに挟まれた箇所）
                {
                    $keystring = explode('@',$value);
                    $keyArray[] = $keystring[1]; // @で囲まれた文字列を配列キーに書き込み
                }
                else
                {
                    $keyArray[] = $key;
                }
            }
            $htmlcode = array_combine($keyArray,$src); // キーとソースの対応付け　[0]src [1]src [nav]src ...
            return $htmlcode;
        }
        else
        {
            return 'template error';
        }
    }

    static function moduleShow($src)
    {
        if(is_array($src)) // 分割ソース
        {
            return implode('',$src);
        }
        elseif($src) // 単品ソース
        {
            return $src;
        }
        else
        {
            return 'error';
        }
    }
}

// ** HTMLヘッダの書き換え ** // 
class headerReplace extends module
{
    static function createSrc($par)
    {
        global $p;
        $src = self::useModuleTemplate('incl/tmp_headerReplace.html');
        $src['title'] = $par['title'];
        if($p->jsflg == 1)
        {
            $src['jslink'] = '<script src="'.$p->pgname.'?linktype=js"></script>';
        }
        else
        {
            $src['jslink'] = '';
        }
        if($p->cssflg == 1)
        {
            $src['csslink'] = '<link href="'.$p->pgname.'?linktype=css" rel="stylesheet">';
        }
        else
        {
            $src['csslink'] = '';
        }
        return self::moduleShow($src);
    }
}

// ** bootstrapナビゲーション書き換え ** //
class bootstrapNavigationReplace extends module
{
    static function createSrc($par)
    {
        if($par['active'] == 'ホーム')
        {
            $homeActive = 'active';
        }
        elseif($par['active'] == 'マスター')
        {
            $masterActive = 'active';
        }
        elseif($par['active'] == '申請・承認')
        {
            $shinseiActive = 'active';
        }
        elseif($par['active'] == '売上・請求')
        {
            $orderActive = 'active';
        }
        elseif($par['active'] == '経費')
        {
            $keihiActive = 'active';
        }
        elseif($par['active'] == '収支')
        {
            $syushiActive = 'active';
        }
        elseif($par['active'] == '勤怠')
        {
            $kintaiActive = 'active';
        }
        elseif($par['active'] == 'マニュアル')
        {
            $manualActive = 'active';
        }
        
        $src = self::useModuleTemplate('incl/tmp_bootstrapNavigationReplace.html');
        $src['homeActive'] = $homeActive;
        $src['masterActive'] = $masterActive;
        $src['orderActive'] = $orderActive;
        $src['keihiActive'] = $keihiActive;
        $src['shinseiActive'] = $shinseiActive;
        $src['syushiActive'] = $syushiActive;
        $src['kintaiActive'] = $kintaiActive;
        $src['manualActive'] = $manualActive;
        $src['name'] = $_SESSION['姓'] . '　' . $_SESSION['名']; //$par['name']; // ログイン名
        $src['sess'] = $par['kmail'] . $_SESSION['petty_cash']; // 
        
        $src['petty_cash_link'] = '../../../../../pettycash/managementsystem/public/login?prm='.$_SESSION['petty_cash'];
//        $src['petty_cash_link'] = 'home.php?prm='.$_SESSION['petty_cash'];
        
        return self::moduleShow($src);
    }
}

// ** 標準コンポーネントロード ** //
class standardComponentsLoad extends module
{
    static function createSrc()
    {
        $src = self::useModuleTemplate('incl/tmp_standardComponentsLoad.html');
        return self::moduleShow($src);
    }
}

// ** モーダルコンポーネントロード ** //
class modalControl extends module
{
    static function appendSrc($id,$data,$parent)
    {
        $code = <<<"__"
            $('#$id').remove();
            $('#$parent').append($data);
            $('#$id').modal({backdrop:'static'});
__;
        return $code;
    }
    
}