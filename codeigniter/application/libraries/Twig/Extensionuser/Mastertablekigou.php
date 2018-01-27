<?php

class Twig_Extensionuser_Mastertablekigou extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'mastertablekigou' => new \Twig_Filter_Method($this, 'mastertablekigou', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true,
                ),
            )),
        );
    }


    public function mastertablekigou(\Twig_Environment $environment, $context, $string='', $db_class, $flag_name)
    {

        if ( strcmp($string,'')==0 ){
            return false;
        }
        else {
            $column_name = $flag_name . '_kbn';
            $table_name = 'mt_' . $flag_name;
            $return_column_name = $flag_name . '_name';

            $query = $db_class->get_where($table_name, array($column_name => $string), 1);
            $hash = $query->result_array()[0];

// print_r($hash);
            // $out = preg_replace("{\[([a-zA-Z]+)?\].+}","$1",$hash[$return_column_name]);

            $out = preg_replace("{\[(.+)?\].+}","$1",$hash[$return_column_name]);

            return $out;
        }


    }


    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }


    public function getName()
    {
        return 'mastertablekigou';
    }
}
