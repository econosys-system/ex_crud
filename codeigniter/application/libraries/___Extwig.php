<?php

// version 1.00
// version 1.01    move Twig Extentions to Twig/extention_user/

if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Extwig
{
    private $CI;
    private $_twig;
    private $_template_dir;
    private $cache_dir;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('extwig',TRUE);

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'libraries/Twig');

        require_once 'Autoloader.php';
        Twig_Autoloader::register();

        $this->_template_dir = $this->CI->config->item('template_dir', 'extwig');
        $this->_cache_dir    = $this->CI->config->item('cache_dir', 'extwig');

        $loader = new Twig_Loader_Filesystem($this->_template_dir);
        $this->_twig = new Twig_Environment($loader, array(
            'debug' => true,
            // 'cache' => $this->_cache_dir,
            'auto_reload' => true,
        ));


        // Load Twig Extension
        // config file   : codeigniter/application/config/extwig.php
        // extension dir : codeigniter/application/libraries/Twig/Extensionuser
        $extention_user = $this->CI->config->item('extention_user', 'extwig');
        foreach ( @$extention_user as $v) {
            $class = $v;
            $this->_twig->addExtension(new $class);
        }

    }


    public function view($template, $data = array())
    {
        $template = $this->_twig->loadTemplate($template);
        echo $template->render($data);
    }


    public function render($template, $data = array())
    {
        $template = $this->_twig->loadTemplate($template);
        return $template->render($data);
    }


    public function addGlobal($name, $value)
    {
        $this->_twig->addGlobal($name, $value);
    }


    public function addExtension($extension)
    {
        $this->_twig->addExtension($extension);
    }

}
