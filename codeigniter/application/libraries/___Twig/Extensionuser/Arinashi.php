<?php

class Twig_Extensionuser_Arinashi extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            'arinashi' => new \Twig_Filter_Method($this, 'arinashi', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true,
                ),
            )),
        );
    }


    public function arinashi(\Twig_Environment $environment, $context, $string )
    {
        if ( $string == 1 ){ return '有';}
        else{ return '無';}
    }


    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }


    public function getName()
    {
        return 'arinashi';
    }
}
