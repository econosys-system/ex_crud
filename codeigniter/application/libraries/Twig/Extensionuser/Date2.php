<?php

class Twig_Extensionuser_Date2 extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'date2' => new \Twig_Filter_Method($this, 'date2', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true,
                ),
            )),
        );
    }

    // public function dateFilter($timestamp, $format = 'F j, Y H:i')

    public function date2(\Twig_Environment $environment, $timestamp, $format)
    {
        $result = '';
        if($timestamp !== null)
        {
            $result = parent::dateFilter($timestamp, $format);
        }
        return $result;
    }

    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }

    public function getName()
    {
        return 'date2';
    }
}
