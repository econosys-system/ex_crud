<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['template_dir']   = APPPATH.'views';

$config['cache_dir']      = APPPATH.'cache/twig';

$config['extention_user'] = array(
	'Twig_Extensions_Extension_Text' ,	// Twig extention Text
	'Twig_Extensionuser_Evaluate' ,		// execute eval in Twig Template
	'Twig_Extensionuser_Mastertable' ,	// DB マスターテーブルから項目名を取得して返す
	'Twig_Extensionuser_Wa2sei' ,		// 和暦と年から西暦を返す
	'Twig_Extensionuser_Arinashi' ,		// 「有」「無」を返す
	'Twig_Extensionuser_Myfilters' ,	// my filters
);
