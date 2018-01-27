<?php

class Twig_Extensionuser_Wa2sei extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'wa2sei' => new \Twig_Filter_Method($this, 'wa2sei', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true,
                ),
            )),
        );
    }


    public function wa2sei(\Twig_Environment $environment, $context, $string1, $string2)
    {

        // // print_r($context);
        // print_r( 'string:'.$string );
        // print_r( $db_class );
        // print_r( 'table_name:'.$table_name );

        $wareki_name  = $string1;
        $wareki_year  = (int)$string2;

        if ( strcmp($wareki_name,'明治')==0 ){
            if ( $wareki_year > 45) { return '年号エラー: '.$wareki_name.$wareki_year; }
            return $wareki_year + 1867;
        } else if ( strcmp($wareki_name,'大正')==0 ){
            if ( $wareki_year > 15) { return '年号エラー: '.$wareki_name.$wareki_year; }
            return $wareki_year + 1911;
        } else if ( strcmp($wareki_name,'昭和')==0 ){
            if ( $wareki_year > 64) { return '年号エラー: '.$wareki_name.$wareki_year; }
            return $wareki_year + 1925;
        } else if ( strcmp($wareki_name,'平成')==0 ){
            return $wareki_year + 1988;
        } else {
            return '年号エラー: '.$wareki_name;
        }

    }



    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }


    public function getName()
    {
        return 'wa2sei';
    }
}
