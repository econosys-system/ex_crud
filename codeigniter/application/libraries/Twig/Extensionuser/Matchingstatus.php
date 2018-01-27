<?php

/**
 * このクラスは お見合いステータスの日本語を返します
 *
 * @version  1.01
 */


class Twig_Extensionuser_Matchingstatus extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'matchingstatus' => new \Twig_Filter_Method($this, 'matchingstatus', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true,
                ),
            )),
        );
    }


    public function matchingstatus(\Twig_Environment $environment, $context, $string )
    {
            if ( strcmp($string,'01_receive')==0 ){ return '会員申込';}
        elseif ( strcmp($string,'01_moushikomi')==0 ){ return '仲人申込済';}
        elseif ( strcmp($string,'01_moushikomi2week')==0 ){ return '仲人申込・2週間経過';}
        elseif ( strcmp($string,'02_okotowari')==0 ){ return '仲人申込・お断り ';}

        elseif ( strcmp($string,'02_judaku')==0 ){ return '会員受諾（相手に伝える前） ';}
        elseif ( strcmp($string,'05_seiritsu')==0 ){ return '受諾済・日程決定前 ';}

        elseif ( strcmp($string,'06_kettei')==0 ){ return 'お見合い日程決定';}
        elseif ( strcmp($string,'07_zenjitsu')==0 ){ return '前日確認（確認前） ';}
        elseif ( strcmp($string,'07_zenjitsu_checked')==0 ){ return '前日確認（確認済み） ';}

        elseif ( strcmp($string,'08_henjimae')==0 ){ return 'お見合い終了・返事前';}
        elseif ( strcmp($string,'08_okotowari')==0 ){ return 'お見合い終了・お断り';}

        elseif ( strcmp($string,'09_kousai')==0 ){ return '交際中';}
        elseif ( strcmp($string,'09_okotowari')==0 ){ return '交際した結果お断り ';}

        elseif ( strcmp($string,'99_seikon')==0 ){ return 'ご成婚';}
        else { return 'プラグイン・エラー';}
    }


    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }


    public function getName()
    {
        return 'matchingstatus';
    }
}
