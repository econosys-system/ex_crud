<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Version 0.31 Change view directory
// Version 1.00 Share Ware
// Version 1.01 add excrud_search_encode_slash option


class Excrud_admin extends CI_Controller {

	public $extend_mode = 0;
	private $language = array();

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('mydump');
		$this->load->library('extwig');
		$this->load->library('excrud');
		// load [/application/language/[english]/excrud_lang.php]
		$lang = $this->config->item('language');
		$this->lang->load('excrud', $lang);
		$this->language = $this->lang->language;


		if (file_exists(APPPATH.'libraries/Excrud_extend.php')){
				$this->extend_mode = 1;
				$this->load->library('excrud_extend');
		}

		// minimal autho
		// $this->load->library('minimal_auth');
		// $now_url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		// $this->minimal_auth->login( site_url("excrud_admin/login_submit"), $now_url);

	}


	// minimal_auth : 以下のメソッドを追加（中身は空でいいです。クラス名やメソッド名を変更した場合は上の「admin/login_submit」も変更すること）
	public function login_submit(){
	}




	public function index($table_name='',$arg1='',$arg2=''){

		$start_no = 0;
		if ( strcmp($arg1,'page')==0 && preg_match('/^[0-9]+$/',$arg2) ){
			$start_no = $arg2;
		}
		$data = array();
		if ($table_name){
			$data = $this->excrud->load($table_name,$start_no);
		}
		else{
			$data = $this->excrud->load_only_table_list();
			$data['path_method'] = 'index';
		}
		$data['config']['excrud'] = $this->config->item('excrud');
		$data['language'] = $this->language;
		$excrud_view_dir = '';
		if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
		$output = $this->extwig->render($excrud_view_dir.'excrud_index.html',$data);
		print $output;
	}




	public function search($table_name, $q='', $arg1='', $arg2='')
	{

		$c = $this->config->item('excrud');
		if ( @$c['excrud_search_encode_slash']==1){
			$q = preg_replace("{%252f}","%2f",$q);
		}

		$q = urldecode($q);
		$start_no = 0;
		if ( strcmp($arg1,'page')==0 && preg_match('/^[0-9]+$/',$arg2) ){
			$start_no = $arg2;
		}
		$data = array();
		$data = $this->excrud->search($table_name,$q,$start_no);
		$data['config']['excrud'] = $this->config->item('excrud');
		$data['language']         = $this->language;
		$excrud_view_dir = '';
		if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
		$output = $this->extwig->render($excrud_view_dir.'excrud_index.html',$data);
		print $output;
	}




	public function add($table_name)
	{
		$data = array();
		$data = $this->excrud->define_data($table_name);

		// OFF $data['data_hash'] = $this->input->get(null,true);		// null :GET ALL DATA / true :not use XSS filtering
		$data['column_loop'] = $this->excrud->load_column_list($table_name, 'view_add_flag');
		$data['config']['excrud'] = $this->config->item('excrud');
		$data['language']         = $this->language;
		$excrud_view_dir = '';
		if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
		$html = $this->extwig->render($excrud_view_dir.'excrud_edit.html',$data);

		require_once APPPATH.'libraries/FillInForm.class.php';
		$data_hash = $this->input->get(null,true);		// null :GET ALL DATA / true :not use XSS filtering
		$fill = new HTML_FillInForm();
		$output =$fill->fill(array(
		   'scalar' => $html,
		   'fdat'   => $data_hash  ,
		));
		print $output;
	}




	public function add_submit($table_name){
		$_back_url = $this->input->post('_back_url');
		$q = $this->input->post(null,false);		// null :GET ALL DATA / false :not use XSS filtering
		unset($q['_back_url']);
		$insert_id = $this->excrud->insert($table_name,$q);
		$path_class = $this->uri->segment(1);
		// Extend Start
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'add_submit_extend' ) ){
			$this->excrud_extend->add_submit_extend($table_name,$insert_id);
		}
		// Extend End
		redirect( "{$path_class}/index/{$table_name}" );
	}



	public function edit($table_name, $data_id){
		$data = array();
		$data = $this->excrud->define_data($table_name);
		$data['_back_url'] = $this->input->get('_back_url');
		$data['data_hash'] = $this->excrud->load_one($table_name,$data_id);
		$data['column_loop'] = $this->excrud->load_column_list($table_name, 'view_edit_flag');
		$primary_column_name = $this->excrud->load_primary_column_name($table_name);
		$data[$primary_column_name] = $data_id;
		$data['config']['excrud'] = $this->config->item('excrud');
		$data['language']         = $this->language;
		$excrud_view_dir = '';
		if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
		$html = $this->extwig->render($excrud_view_dir.'excrud_edit.html',$data);

		require_once APPPATH.'libraries/FillInForm.class.php';
		$data_hash = $this->input->get(null,true);		// null :GET ALL DATA / true :not use XSS filtering
		$fill = new HTML_FillInForm();
		$output =$fill->fill(array(
		   'scalar' => $html,
		   'fdat'   => $data['data_hash']  ,
		));
		print $output;
	}




	public function edit_submit($table_name, $data_id){
		$_back_url = $this->input->post('_back_url');
		$q = $this->input->post(null,false);		// null :GET ALL DATA / false :not use XSS filtering

		// Extend Start
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'edit_submit_pre' ) ){ $this->excrud_extend->edit_submit_pre($table_name,$data_id); }
		// Extend End

		$data = $this->excrud->define_data($table_name);

		foreach ($data['column_define_loop'] as $k => $v) {
			if ( strcmp(@$v['input_type'],'checkbox')==0 ){
				if (! isset($q[$k])){
					$q[$k] = '';
				}
			}
		}
		unset($q['_back_url']);
		$this->excrud->update($table_name,$data_id,$q);
		// Extend Start
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'edit_submit_post' ) ){ $this->excrud_extend->edit_submit_post($table_name,$data_id); }
		// Extend End
		redirect( $_back_url );
	}


	public function delete($table_name, $data_id){
		// Extend Start
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'delete_pre' ) ){
			$this->excrud_extend->delete_pre($table_name,$data_id);
		}
		$data = array();
		$data = $this->excrud->define_data($table_name);
		$data['_back_url'] = $this->input->get('_back_url');
		$data['data_hash'] = $this->excrud->load_one($table_name,$data_id);
		$data['column_loop'] = $this->excrud->load_column_list($table_name, 'view_edit_flag');
		$primary_column_name = $this->excrud->load_primary_column_name($table_name);
		$data[$primary_column_name] = $data_id;
		$data['config']['excrud'] = $this->config->item('excrud');
		$data['language']         = $this->language;
		$excrud_view_dir = '';
		if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
		$output = $this->extwig->render($excrud_view_dir.'excrud_delete.html',$data);
		print $output;
	}




	public function delete_submit($table_name, $data_id){
		// Extend Start
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'delete_submit_pre' ) ){
			$this->excrud_extend->delete_submit_pre($table_name,$data_id);
		}

		$_back_url = $this->input->post('_back_url');
		$q = $this->input->post(null,false);		// null :GET ALL DATA / false :not use XSS filtering
		unset($q['_back_url']);
		$this->excrud->delete($table_name,$data_id);

		// Extend Start
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'delete_submit_post' ) ){
			$this->excrud_extend->delete_submit_post($table_name,$data_id);
		}

		redirect( $_back_url );
		// $path_class = $this->uri->segment(1);
		//redirect( "{$path_class}/index/{$table_name}" );
	}




	public function dl_delete_submit($table_name){
		$_back_url = $this->input->get('_back_url');
		$dl = $this->input->get('dl');

		// Extend pre
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'dl_delete_submit_pre' ) ){
			$this->excrud_extend->dl_delete_submit_pre($table_name,$dl);
		}

		foreach ($dl as $k => $v) {
			$this->excrud->delete($table_name,$v);
		}

		// Extend post
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'dl_delete_submit_post' ) ){
			$this->excrud_extend->dl_delete_submit_post($table_name,$dl);
		}
		redirect( $_back_url );
	}




	// 一括編集の入力
	public function dl_multiple_edit($table_name){
		$_back_url = $this->input->get('_back_url');
		$dl = $this->input->get('dl');

		// Extend pre
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'dl_multiple_edit_pre' ) ){
			$this->excrud_extend->dl_multiple_edit_pre($table_name,$dl);
		}

		$data                     = $this->excrud->load_in($table_name,$dl);
		$data['_back_url']        = $_back_url;
		$data['dl']               = $dl;
		$data['config']['excrud'] = $this->config->item('excrud');
		$data['language']         = $this->language;
		$excrud_view_dir = '';
		if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
		$html = $this->extwig->render($excrud_view_dir.'excrud_dl_multiple_edit.html',$data);
		print $html;

		// Extend post
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'dl_multiple_edit_post' ) ){
			$this->excrud_extend->dl_multiple_edit_post($table_name,$dl);
		}
	}




	// exec multiple edit
	public function dl_multiple_edit_submit($table_name){

		// Extend pre
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'dl_multiple_edit_submit_pre' ) ){
			$this->excrud_extend->dl_multiple_edit_submit_pre($table_name,$dl);
		}

		$q = $this->input->post(null,true);		// null :GET ALL DATA / true :not use XSS filtering
		$_back_url = $q['_back_url'];
		$dl       = $q['_dl'];
		unset($q['_back_url']);
		unset($q['_dl']);

		foreach ($q as $k => $v) {
			if ( strcmp($v,'')==0 ){
				unset($q[$k]);
	    }
		}

		if ( count($q)>0 ){
			foreach ($dl as $data_id) {
				$this->excrud->update($table_name,$data_id,$q);
			}
		}

		// Extend post
		if ( $this->extend_mode && method_exists ( $this->excrud_extend , 'dl_multiple_edit_submit_post' ) ){
			$this->excrud_extend->dl_multiple_edit_submit_post($table_name,$dl);
		}

		redirect( $_back_url );
	}




	public function sql($table_name){
		$data = array();
		$data = $this->excrud->define_data($table_name);
		$data['table_name'] = $table_name;
		$data['path_method'] = 'sql';
		$query = $this->db->query( "DESC {$table_name};" );
		$data['desc_loop']  = $query->result_array();
		$data['config']['excrud'] = $this->config->item('excrud');
		$data['language']         = $this->language;
		$excrud_view_dir = '';
		if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
		$output = $this->extwig->render($excrud_view_dir.'excrud_sql.html',$data);
		print $output;
	}




	public function sql_submit($table_name,$arg1='',$arg2=''){
		$q = $this->input->post(null,true);		// null :GET ALL DATA / true :not use XSS filtering
		if (! isset($q['sql_name']) ){
			$q = $this->input->get(null,true);		// null :GET ALL DATA / true :not use XSS filtering
		}
		$start_no = 0;
		if ( strcmp($arg1,'page')==0 && preg_match('/^[0-9]+$/',$arg2) ){
			$start_no = $arg2;
		}

		$q['sql_name'] = html_entity_decode($q['sql_name']);
		$data = array();

		if ( @$q['view_from_sql']){
			$data = $this->excrud->exec_sql($table_name,$q['sql_name'],true,$start_no);		// true: pager_flag
			$data['config']['excrud'] = $this->config->item('excrud');
			$data['language']         = $this->language;
			$excrud_view_dir = '';
			if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
			$output = $this->extwig->render($excrud_view_dir.'excrud_index.html',$data);
		}
		else{
			$data = $this->excrud->exec_sql($table_name,$q['sql_name']);
			$data['config']['excrud'] = $this->config->item('excrud');
			$data['language']         = $this->language;
			$excrud_view_dir = '';
			if ( @$data['config']['excrud']['excrud_view_dir'] ){ $excrud_view_dir = $data['config']['excrud']['excrud_view_dir'].'/'; }
			$output = $this->extwig->render($excrud_view_dir.'excrud_sql.html',$data);
		}
		print $output;
	}




		public function json_recreate()
		{
			unlink( $this->config->item('excrud_data_path', 'excrud').'/table.json' );
			$this->excrud->_load_or_creat_json();
			$back_url = $this->input->get('back_url');
			if ($back_url){
				redirect( $back_url );
			}
			else{
				redirect( "/excrud_admin/" );
			}
		}




		public function json_sample_create()
		{
			$back_url = $this->input->get('back_url');
			$sample_hash = array();
			foreach ( $this->excrud->db_visible_table_loop as $kkk => $vvv ) {
				$sample_hash[$vvv] = array();
				$data = $this->excrud->define_data($vvv);
				// $this->mydump->dump( $data['column_define_loop'] );
				foreach ($data['column_define_loop'] as $k => $v) {
					$sample_hash[$vvv]['table_desc'][$k] = array(
						"table_header"          => '' ,
						"view_add_flag"         => 1 ,
						"view_edit_flag"        => 1 ,
						"view_delete_flag"      => 1 ,
						"editable_flag"         => $v['editable_flag'] ,
						"input_type"            => "text" ,
            "default"               => $v['default'] ,
						"view_list_flag"        => 1 ,
						"view_list_title"       => $k ,
						"view_list_nowrap_flag" => 0 ,
						"view_list_format"      => "{{data}}" ,
			      "multiple_edit_flag"    => ($v['editable_flag']==1 && preg_match("/int/i", $v['type'])) ? 1 : 0 ,
					);

				}
			}

	    $json_data = json_encode($sample_hash,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
	    if ( ! write_file($this->config->item('excrud_data_path', 'excrud').'/___sample___override.json', $json_data)){
				die('Excrud : _load_or_creat_json() : can not write ->'.$this->CI->config->item('excrud_data_path', 'excrud').'table.json');
			}

			if ($back_url){ redirect( $back_url ); }
			else{ redirect( "/excrud_admin/" ); }
		}





}
