<?php

class Twig_Extensionuser_Realpasswd extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'realpasswd' => new \Twig_Filter_Method($this, 'realpasswd', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true,
                ),
            )),
        );
    }


    public function realpasswd(\Twig_Environment $environment, $context, $string='', $bcrypt_class)
    {

        // // print_r($context);
        // print_r( 'string:'.$string );
        // print_r( $db_class );
        // print_r( 'table_name:'.$table_name );

        if ( strcmp($string,'')==0 ){
            return false;
        }
        else {
            // return 1;
            print_r($bcrypt_class);
//            return $bcrypt_class->decrypt($string);
        }


    }


    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }


    public function getName()
    {
        return 'realpasswd';
    }
}
