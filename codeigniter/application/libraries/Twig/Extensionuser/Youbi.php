<?php

class Twig_Extensionuser_Youbi extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'youbi' => new \Twig_Filter_Method($this, 'youbi', array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true,
                ),
            )),
        );
    }

    public function youbi(\Twig_Environment $environment, $context, $string1)
    {
        $date = new DateTime($string1);
        $w_no = $date->format('w');
        $week = array('日', '月', '火', '水', '木', '金', '土');
        return $week[$w_no];
    }

    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }

    public function getName()
    {
        return 'youbi';
    }
}
