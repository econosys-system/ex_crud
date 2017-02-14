<?php

// version 1.00

if (!defined('BASEPATH')) {exit('No direct script access allowed');}
class Extwig{
	private $CI;
	private $_twig;
	private $_template_dir;
	private $cache_dir;
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->config->load('extwig');

		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'libraries/Twig');

		require_once 'Autoloader.php';
		Twig_Autoloader::register();

		$this->_template_dir = $this->CI->config->item('template_dir');
		$this->_cache_dir = $this->CI->config->item('cache_dir');

		$loader = new Twig_Loader_Filesystem($this->_template_dir);
		$this->_twig = new Twig_Environment($loader, array(
			'debug' => true,
			// 'cache' => $this->_cache_dir,
			'auto_reload' => true,
		));
		$this->_twig->addExtension( new Twig_Extension_Evaluate() );

		$this->_twig->addExtension(new Twig_Extensions_Extension_Text());


		// filter : file_exists
		$filter_1 = new Twig_SimpleFilter('file_exists', function ($filename) {
	    return file_exists(dirname(__FILE__).'/'.$filename);
		});
		$this->_twig->addFilter($filter_1);

		// filter : arg_trans
		$filter_2 = new Twig_SimpleFilter('arg_trans', function ($text, $data) {
			$text = preg_replace("/{{data}}/", $data, $text);
	    return $text;
		});
		$this->_twig->addFilter($filter_2);

		// filter : substr_count
		$filter_3 = new Twig_SimpleFilter('substr_count', function ($haystack, $needle) {
	    return substr_count ($haystack, $needle);
		});
		$this->_twig->addFilter($filter_3);

		// filter : amazon_usa_format
		$filter_3 = new Twig_SimpleFilter('amazon_usa_format', function ($number) {
	    return preg_replace("/([0-9][0-9])$/",".$1",$number);
		});
		$this->_twig->addFilter($filter_3);

		// filter : amazon_change_weight
		$filter_4 = new Twig_SimpleFilter('amazon_change_weight', function ($input=0, $unit='') {
			if ( strcmp($unit,'hundredths-pounds')==0 ){
  			$data = ($input * 0.453592 * 0.01);
				if ($data >= 1){
					return round($data, 2).'kg';
				}
				else{
					return round( ($data*1000), 0).'g';
				}
			}
			else{
				return $input.' : '.$unit;
			}
		});
		$this->_twig->addFilter($filter_4);

		// filter : replace_kishu_kanji
		$filter_5 = new Twig_SimpleFilter('replace_kishu_kanji', function ($subject) {
			$search  = array( '①', '②', '③', '④', '⑤', '⑥', '⑦', '⑧', '⑨', '⑩', '⑪', '⑫', '⑬', '⑭', '⑮', '⑯', '⑰', '⑱', '⑲', '⑳', 'Ⅰ', 'Ⅱ', 'Ⅲ', 'Ⅳ', 'Ⅴ', 'Ⅵ', 'Ⅶ', 'Ⅷ', 'Ⅸ', 'Ⅹ', '㍉', '㌔', '㌢', '㍍', '㌘', '㌧', '㌃', '㌶', '㍑', '㍗', '㌍', '㌦', '㌣', '㌫', '㍊', '㌻', '㎜', '㎝', '㎞', '㎎', '㎏', '㏄', '㎡', '㍻', '〝', '〟', '№', '㏍', '℡', '㊤', '㊥', '㊦', '㊧', '㊨', '㈱', '㈲', '㈹', '㍾', '㍽', '㍼', '∮', '∑', '∟', '⊿', '纊', '褜', '鍈', '銈', '蓜', '俉', '炻', '昱', '棈', '鋹', '曻', '彅', '丨', '仡', '仼', '伀', '伃', '伹', '佖', '侒', '侊', '侚', '侔', '俍', '偀', '倢', '俿', '倞', '偆', '偰', '偂', '傔', '僴', '僘', '兊', '兤', '冝', '冾', '凬', '刕', '劜', '劦', '勀', '勛', '匀', '匇', '匤', '卲', '厓', '厲', '叝', '﨎', '咜', '咊', '咩', '哿', '喆', '坙', '坥', '垬', '埈', '埇', '﨏', '塚', '增', '墲', '夋', '奓', '奛', '奝', '奣', '妤', '妺', '孖', '寀', '甯', '寘', '寬', '尞', '岦', '岺', '峵', '崧', '嵓', '﨑', '嵂', '嵭', '嶸', '嶹', '巐', '弡', '弴', '彧', '德', '忞', '恝', '悅', '悊', '惞', '惕', '愠', '惲', '愑', '愷', '愰', '憘', '戓', '抦', '揵', '摠', '撝', '擎', '敎', '昀', '昕', '昻', '昉', '昮', '昞', '昤', '晥', '晗', '晙', '晴', '晳', '暙', '暠', '暲', '暿', '曺', '朎', '朗', '杦', '枻', '桒', '柀', '栁', '桄', '棏', '﨓', '楨', '﨔', '榘', '槢', '樰', '橫', '橆', '橳', '橾', '櫢', '櫤', '毖', '氿', '汜', '沆', '汯', '泚', '洄', '涇', '浯', '涖', '涬', '淏', '淸', '淲', '淼', '渹', '湜', '渧', '渼', '溿', '澈', '澵', '濵', '瀅', '瀇', '瀨', '炅', '炫', '焏', '焄', '煜', '煆', '煇', '凞', '燁', '燾', '犱', '犾', '猤', '猪', '獷', '玽', '珉', '珖', '珣', '珒', '琇', '珵', '琦', '琪', '琩', '琮', '瑢', '璉', '璟', '甁', '畯', '皂', '皜', '皞', '皛', '皦', '益', '睆', '劯', '砡', '硎', '硤', '硺', '礰', '礼', '神', '祥', '禔', '福', '禛', '竑', '竧', '靖', '竫', '箞', '精', '絈', '絜', '綷', '綠', '緖', '繒', '罇', '羡', '羽', '茁', '荢', '荿', '菇', '菶', '葈', '蒴', '蕓', '蕙', '蕫', '﨟', '薰', '蘒', '﨡', '蠇', '裵', '訒', '訷', '詹', '誧', '誾', '諟', '諸', '諶', '譓', '譿', '賰', '賴', '贒', '赶', '﨣', '軏', '﨤', '逸', '遧', '郞', '都', '鄕', '鄧', '釚', '釗', '釞', '釭', '釮', '釤', '釥', '鈆', '鈐', '鈊', '鈺', '鉀', '鈼', '鉎', '鉙', '鉑', '鈹', '鉧', '銧', '鉷', '鉸', '鋧', '鋗', '鋙', '鋐', '﨧', '鋕', '鋠', '鋓', '錥', '錡', '鋻', '﨨', '錞', '鋿', '錝', '錂', '鍰', '鍗', '鎤', '鏆', '鏞', '鏸', '鐱', '鑅', '鑈', '閒', '隆', '﨩', '隝', '隯', '霳', '霻', '靃', '靍', '靏', '靑', '靕', '顗', '顥', '飯', '飼', '餧', '館', '馞', '驎', '髙', '髜', '魵', '魲', '鮏', '鮱', '鮻', '鰀', '鵰', '鵫', '鶴', '鸙', '黑', 'ⅰ', 'ⅱ', 'ⅲ', 'ⅳ', 'ⅴ', 'ⅵ', 'ⅶ', 'ⅷ', 'ⅸ', 'ⅹ', '￤', '＇', '＂' );
			$replace = array('(1)','(2)','(3)','(4)','(5)','(6)','(7)','(8)','(9)','(10)','(11)','(12)','(13)','(14)','(15)','(16)','(17)','(18)','(19)','(20)','I','II','III','IV','V','VI','VII','VIII','IX','X','ミリ','キロ','センチ','メートル','グラム','トン','アール','ヘクタール','リットル','ワット','カロリー','ドル','セント','パーセント','ミリバール','ページ','mm','cm','km','mg','kg','cc','m2','平成' );
			$result = str_replace($search, $replace, $subject);
			return $result;
		});
		$this->_twig->addFilter($filter_5);

		// filter : nl_cut
		$filter_6 = new Twig_SimpleFilter('nl_cut', function ($str) {
			return preg_replace("/\r\n|\r|\n/", "", $str);
		});
		$this->_twig->addFilter($filter_6);

		// filter : csv_escape
		$filter_6 = new Twig_SimpleFilter('csv_escape', function ($str) {
			// return '"'.addslashes($str).'"';
			if ( preg_match("{,}",$str) || preg_match("{;}",$str) ){
				$str = preg_replace('/,/','\,',$str);
				$str = preg_replace('/"/','""',$str);
				return '"'.$str.'"';
			}
			else{
				return $str;
			}
		});
		$this->_twig->addFilter($filter_6);

		// filter : mb_truncate
		$filter_7 = new Twig_SimpleFilter('mb_truncate', function ($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
			if ($length == 0)
	        return '';
	    if (mb_strlen($string) > $length) {
	        $length -= mb_strlen($etc);
	        if (!$break_words && !$middle) {
	            $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1));
	        }
	        if(!$middle) {
	            return mb_substr($string, 0, $length).$etc;
	        } else {
	            return mb_substr($string, 0, $length/2) . $etc . mb_substr($string, -$length/2);
	        }
	    } else {
	        return $string;
	    }
		});
		$this->_twig->addFilter($filter_7);


		// filter : truncate_bytes
		$filter_8 = new Twig_SimpleFilter('truncate_bytes', function ($string, $bytes = 255) {
			if ($bytes == 0){
				return '';
			}
			return mb_strcut( $string , 0 , $bytes );
		});
		$this->_twig->addFilter($filter_8);


		// filter : cut_http
		$filter_9 = new Twig_SimpleFilter('cut_http', function ($string) {
			return preg_replace("{https?://}", "", $string);
		});
		$this->_twig->addFilter($filter_9);

		// filter : delete_style
		$filter_10 = new Twig_SimpleFilter('delete_style', function ($string) {
			return preg_replace("/<style.+?\/style>/", "", $string);
		});
		$this->_twig->addFilter($filter_10);

		// filter : delete_table
		$filter_11 = new Twig_SimpleFilter('delete_table', function ($string) {
			return preg_replace("{<table.+?</table>}", "", $string);
		});
		$this->_twig->addFilter($filter_11);



	}
	public function view($template, $data = array()) {
		$template = $this->_twig->loadTemplate($template);
		echo $template->render($data);
	}

	public function render($template, $data = array()) {
		$template = $this->_twig->loadTemplate($template);
		return $template->render($data);
	}

	public function addGlobal( $name, $value ){
		$this->_twig->addGlobal( $name, $value );
	}


	public function addExtension($extension){
		$this->_twig->addExtension($extension);
	}

}
