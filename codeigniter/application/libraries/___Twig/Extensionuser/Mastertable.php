<?php

class Twig_Extensionuser_Mastertable extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'mastertable' => new \Twig_Filter_Method($this, 'mastertable', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true,
                ),
            )),
        );
    }


    public function mastertable(\Twig_Environment $environment, $context, $string='', $db_class, $flag_name)
    {

        // // print_r($context);
        // print_r( 'string:'.$string );
        // print_r( $db_class );
        // print_r( 'table_name:'.$table_name );

        if ( strcmp($string,'')==0 ){
            return false;
        }

        $column_name = $flag_name . '_kbn';
        $table_name = 'mt_' . $flag_name;
        $return_column_name = $flag_name . '_name';

        $query = $db_class->get_where($table_name, array($column_name => $string), 1);
        $hash = $query->result_array()[0];
        return $hash[$return_column_name];

    }


    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }


    public function getName()
    {
        return 'mastertable';
    }
}
