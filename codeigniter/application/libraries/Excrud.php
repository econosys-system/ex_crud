<?php

/*
  /library/Excrud.php (c)2017 econosys system  http://econosys-system.com/

  version 0.45  [fix] error message
  version 0.46  [fix] if $config['excrud_override'] is null . don't read override.json
  version 0.47  [add] tables() method
  version 0.48  [fix] $db_visible_table_loop move to public
  version 0.49  [add] config override
  version 0.491 [add] error message
  version 0.492 [add] add title column define_data()
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Excrud {

  private $db_table_loop;
  private $column_list;

  public $db_visible_table_loop;
  public $crud_table;
  public $db_visible_table_loop;

  public function __construct()
  {
    $this->CI =& get_instance();
    $this->CI->load->database();
    $this->CI->load->library('extwig');

    if($this->CI->db->dbdriver==='mysql'){
      $this->CI->db->query("SET NAMES 'utf8'");
      $this->CI->db->query("SET CHARACTER SET utf8");
      $this->CI->db->query("SET character_set_connection=utf8");
      $this->CI->db->query("SET character_set_server=utf8");
      $this->CI->db->query("SET collation_server=utf8_general_ci");
    }

    $this->db_table_loop = $this->CI->db->list_tables();
    $this->CI->config->load('excrud',TRUE);

    $this->_load_or_creat_json();

    $this->db_visible_table_loop = $this->_visible_table_loop();
   $this->CI->extwig->addExtension(new Twig_Extensions_Extension_Text()); //truncate filter
  }



  public function define_data($table_name)
  {
    $data = array();
    $data['base_url']  = base_url();
    $data['primary_column_name'] = $this->load_primary_column_name($table_name);
    $data['column_define_loop'] = $this->load_column_define($table_name);
    $data['path_class']  = $this->CI->uri->segment(1);
    $data['path_method'] = $this->CI->uri->segment(2);
    $data['table_name'] = $table_name;
    $data['db_visible_table_loop'] = $this->db_visible_table_loop;
    // language
    $lang = $this->CI->config->item('language');
    $this->CI->lang->load('excrud', $lang);
    $data['language'] = $this->CI->lang->language;

    return $data;
  }



  public function load_primary_column_name($table_name)
  {
    $this->_check_json_file($table_name);
    if (! @$this->crud_table[$table_name]['primary_column_name'] ){
      $this->_view_error('Excrud : load_primary_column_name() : "primary_column_name" is not exists in table '.$table_name."<br>\n please check ".$this->CI->config->item('excrud_data_path', 'excrud').'/table.json');
    }
    return $this->crud_table[$table_name]['primary_column_name'];
  }



  public function load_column_list($table_name, $flag_name='view_list_flag')
  {
    $this->_check_json_file($table_name);
    $column = array();
    foreach ($this->crud_table[$table_name]['table_desc'] as $k => $v) {
      if ( @$v[$flag_name]==1 ){
        array_push($column, $k);
      }
    }
    if ( count($column)==0 ){
      $this->_view_error('Excrud : load_column_list() : column not found $flag_name -> '.$flag_name);
    }
    return $column;
  }



  public function load_column_define($table_name)
  {
    $this->_check_json_file($table_name);
    return $this->crud_table[$table_name]['table_desc'];
  }



  public function load_column_define_multiple($table_name)
  {
    $this->_check_json_file($table_name);

    $column_define_multiple = array();

    foreach ($this->crud_table[$table_name]['table_desc'] as $k => $v) {
      if ( isset($v['multiple_edit_flag']) ){
        if ($v['multiple_edit_flag'] == 1){
          $column_define_multiple[$k] = $v;
        }
      }
    }
    return $column_define_multiple;
  }



  private function _create_pagination($table_name, $total_rows, $q='')
  {
    $path_class  = $this->CI->uri->segment(1);
    $path_method = $this->CI->uri->segment(2);
    $this->CI->load->library('pagination');
    if ( strcmp($path_method,'search')==0 ){
      $config['base_url'] = base_url()."{$path_class}/{$path_method}/{$table_name}/" .urlencode($q). "/page/";
    }
    elseif ( strcmp($path_method,'sql_submit')==0 ){

      $config['base_url'] = base_url()."{$path_class}/{$path_method}/{$table_name}/page/";
      $config['suffix'] = '?' . http_build_query($this->CI->input->get());
      $config['first_url'] = $config['base_url']. '?' .http_build_query($this->CI->input->get());

    }
    else{
      $config['base_url'] = base_url()."{$path_class}/{$path_method}/{$table_name}/page/";
    }
    $config['num_links'] = 5;
    $config['total_rows'] = $total_rows;
    $config['per_page'] = $this->CI->config->item('excrud_list_item_per_page', 'excrud');
    $this->CI->pagination->initialize($config);
    $pagination = $this->CI->pagination->create_links();
    return $pagination;
  }



  public function load_only_table_list()
  {
    $data = array();
    $data['base_url']  = base_url();
		$data['path_class']  = $this->CI->uri->segment(1);
    $data['db_table_loop']         = $this->db_table_loop;
    $data['db_visible_table_loop'] = $this->db_visible_table_loop;
    return $data;
  }



  public function load($table_name,$start_no=0)
  {
    $this->_check_json_file($table_name);
    $column = array();
    foreach ($this->crud_table[$table_name]['table_desc'] as $k => $v) {
        array_push($column, $v['name']);
    }
    $data_count = $this->CI->db->count_all_results($table_name);
    $this->CI->db->select( join(',',$column) );
    $list_item_per_page = $this->CI->config->item('excrud_list_item_per_page', 'excrud');
    $this->CI->db->order_by("{$this->crud_table[$table_name]['order_by']}");
    $query = $this->CI->db->get($table_name,$list_item_per_page, $start_no);

    $data = array();
    $data['base_url']  = base_url();
    $data['data_count'] = $data_count;
    $data['data_loop']  = $query->result_array();
    $num_rows = $this->CI->db->count_all_results($table_name);
    $data['pagination'] = $this->_create_pagination($table_name,$num_rows);
    $data['primary_column_name'] = $this->load_primary_column_name($table_name);
		$data['column_loop'] = $this->load_column_list($table_name, 'view_list_flag');
		$data['column_define_loop'] = $this->load_column_define($table_name);
		$data['table_name'] = $table_name;
		$data['path_class']  = $this->CI->uri->segment(1);
    $data['path_method'] = $this->CI->uri->segment(2);
    $data['db_table_loop']         = $this->db_table_loop;
    $data['db_visible_table_loop'] = $this->db_visible_table_loop;
    // language
    $lang = $this->CI->config->item('language');
    $this->CI->lang->load('excrud', $lang);
    $data['language'] = $this->CI->lang->language;
    return $data;
  }



  public function load_one($table_name,$id=-1)
  {
    $this->_check_json_file($table_name);
    if (! $id){
      $this->_view_error('Excrud : load_one() : please set id -> '.$id);
    }
    $primary_column_name = $this->load_primary_column_name($table_name);
    $query = $this->CI->db->get_where($table_name,array("{$primary_column_name}" => $id),1,0);
    $data_loop  = $query->result_array();
    if (! isset($data_loop[0]) ){
      $sql = $this->CI->db->last_query();
      $this->_view_error('Excrud : load_one() : data not found SQL -> '.$sql." ; <br>\n on " . __FILE__ .' (' . __LINE__ . ')' );
    }
    return $data_loop[0];
  }



  public function ___backup___load_in( $table_name, $id_array = array() )
  {
    $this->_check_json_file($table_name);
    if (count($id_array)==0){
      $this->_view_error('Excrud : load_in() : please set id_array');
    }
    $primary_column_name = $this->load_primary_column_name($table_name);
    $this->CI->db->where_in($primary_column_name, $id_array);
    $query = $this->CI->db->get($table_name,999999,0);
    $data_loop  = $query->result_array();
    if (! isset($data_loop[0]) ){
      $sql = $this->CI->db->last_query();
      $this->_view_error('Excrud : load_one() : data not found SQL -> '.$sql." ; <br>\n on " . __FILE__ .' (' . __LINE__ . ')' );
    }
    return $data_loop;
  }



  public function load_in( $table_name, $id_array = array() )
  {
    $this->_check_json_file($table_name);
    if (count($id_array)==0){
      $this->_view_error('Excrud : load_in() : please set id_array');
    }
    $primary_column_name = $this->load_primary_column_name($table_name);
    $this->CI->db->where_in($primary_column_name, $id_array);
    $query = $this->CI->db->get($table_name,999999,0);
    $data_loop  = $query->result_array();
    if (! isset($data_loop[0]) ){
      $sql = $this->CI->db->last_query();
      $this->_view_error('Excrud : load_one() : data not found SQL -> '.$sql." ; <br>\n on " . __FILE__ .' (' . __LINE__ . ')' );
    }

    $data = array();
    $data['base_url']  = base_url();
    $data['data_loop']  = $data_loop;
    $num_rows = $this->CI->db->count_all_results($table_name);
    $data['pagination'] = $this->_create_pagination($table_name,$num_rows);
    $data['primary_column_name'] = $this->load_primary_column_name($table_name);
    $data['column_loop'] = $this->load_column_list($table_name, 'view_list_flag');

    $data['column_define_multiple_loop'] = $this->load_column_define_multiple($table_name);
    $data['column_define_loop']          = $this->load_column_define($table_name);
    $data['table_name'] = $table_name;
    $data['path_class']  = $this->CI->uri->segment(1);
    $data['path_method'] = $this->CI->uri->segment(2);
    $data['db_table_loop']         = $this->db_table_loop;
    $data['db_visible_table_loop'] = $this->db_visible_table_loop;

    return $data;
  }



  public function search($table_name, $q='', $start_no=0)
  {
    $data_loop = array();
    $this->_check_json_file($table_name);
    if ( ! isset($this->crud_table[$table_name]['search_columns']) ){
      $this->_view_error('Excrud : search() : please set search_columns in JSON FILE -> '. $this->CI->config->item('excrud_data_path', 'excrud').'/table.json');
    }

    // count
    foreach ( $this->crud_table[$table_name]['search_columns'] as $v ){
      $this->CI->db->or_like($v, $q );
    }
    $this->CI->db->from($table_name);
    $num_rows = $this->CI->db->count_all_results();

    // select
    foreach ( $this->crud_table[$table_name]['search_columns'] as $v ){
      $this->CI->db->or_like($v, $q );
    }
    if ( @$this->crud_table[$table_name]['search_order_by'] ){
      $this->CI->db->order_by($this->crud_table[$table_name]['search_order_by']);
    }
    $list_item_per_page = $this->CI->config->item('excrud_list_item_per_page', 'excrud');
    $query = $this->CI->db->get($table_name,$list_item_per_page, $start_no);

    $data['q']  = $q;
    $data['base_url']  = base_url();
    $data['data_loop']  = $query->result_array();
    $data['pagination'] = $this->_create_pagination($table_name, $num_rows, $q);
    $data['primary_column_name'] = $this->load_primary_column_name($table_name);
		$data['column_loop'] = $this->load_column_list($table_name, 'view_list_flag');
		$data['column_define_loop'] = $this->load_column_define($table_name);
		$data['table_name'] = $table_name;
		$data['path_class']  = $this->CI->uri->segment(1);
    $data['path_method'] = $this->CI->uri->segment(2);
    $data['db_table_loop']         = $this->db_table_loop;
    $data['db_visible_table_loop'] = $this->db_visible_table_loop;
    return $data;
  }



  public function insert($table_name, $q=array())
  {
    $this->_check_json_file($table_name);
    if ( ! count($q) ){
      $this->_view_error('Excrud : update() : column is not set');
    }
    $primary_column_name = $this->load_primary_column_name($table_name);
    $this->CI->db->insert($table_name, $q);
    $affected_row = $this->CI->db->affected_rows();
    if ( $affected_row < 1 ){
      $sql = $this->CI->db->last_query();
      $this->_view_error('Excrud : insert() : cannot data insert in SQL -> '. $sql .' ;' );
    }
    $insert_id = $this->CI->db->insert_id();
    return $insert_id;
  }



  public function update($table_name, $id, $q=array())
  {
    $this->_check_json_file($table_name);
    if (! $id){
      $this->_view_error('Excrud : update() : please set id -> '.$id);
    }
    if ( ! count($q) ){
      $this->_view_error('Excrud : update() : column is not set');
    }

    foreach ($q as $k => $v) {
      if ( strcmp($q[$k],'')==0 ){
        $q[$k] = null;
      }
    }

    $primary_column_name = $this->load_primary_column_name($table_name);
    $this->CI->db->where($primary_column_name, $id);
    $this->CI->db->update($table_name, $q);
    $affected_row = $this->CI->db->affected_rows();
    return $affected_row;
  }



  public function delete($table_name, $id)
  {
    $this->_check_json_file($table_name);
    if (! $id){
      $this->_view_error('Excrud : update() : please set id -> '.$id);
    }
    $primary_column_name = $this->load_primary_column_name($table_name);
    $this->CI->db->delete($table_name, array($primary_column_name => $id));
    $affected_row = $this->CI->db->affected_rows();
    if ( $affected_row < 1 ){
      $sql = $this->CI->db->last_query();
      $this->_view_error('Excrud : update() : cannot data update in SQL -> '. $sql .' ;' );
    }
    return $affected_row;
  }



  public function exec_sql($table_name, $sql_name, $pager_flag=false,$start_no=0)
  {
    $data_count = 0;

    if ( preg_match("/;/", $sql_name) ){
      $array = preg_split("/;/", $sql_name);
      foreach ($array as $value) {
        $t_value = trim($value);
        if (!empty($t_value)){
          $query = $this->CI->db->query($value);
        }
      }
    }
    else{
      if (preg_match("/^SELECT/i", $sql_name)){
        $count_sql_name = preg_replace("/SELECT \*/i","SELECT count(*) AS numrows",$sql_name);
        $count_query    = $this->CI->db->query($count_sql_name);
        $data_count     = @$count_query->result_array()[0]['numrows'];

        $list_item_per_page = $this->CI->config->item('excrud_list_item_per_page', 'excrud');
        $sql_name .= " LIMIT {$start_no} , {$list_item_per_page}";
      }
      $query = $this->CI->db->query($sql_name);
    }

    if (is_bool($query)){
      $data['data_loop'] = array(
        'result' => $query
      );
    }
    else{
      $data['data_loop'] = $query->result_array();
      $data['data_count'] = $data_count;
    }
     if (preg_match("/^SELECT/i", trim($sql_name))){
        $num_rows = 0;
        $count_sql_name = preg_replace("/SELECT .+ FROM/", "SELECT count(*) AS count FROM", $sql_name);
        $count_sql_name = preg_replace("/LIMIT .+$/", "", $count_sql_name);
        $query2 = $this->CI->db->query($count_sql_name);
        $num_rows = @$query2->result_array()[0]['count'];
    }

      $data['page_title'] = $this->CI->input->get('page_title');
      $data['sql_name']   = $sql_name;
      $data['base_url']   = base_url();
      if ($pager_flag){
        $data['pagination'] = $this->_create_pagination($table_name,$num_rows);
      }
      $data['primary_column_name'] = $this->load_primary_column_name($table_name);
      $data['column_loop'] = $this->load_column_list($table_name, 'view_list_flag');
      $data['column_define_loop'] = $this->load_column_define($table_name);
      $data['table_name'] = $table_name;
      $data['path_class']  = $this->CI->uri->segment(1);
      $data['path_method'] = $this->CI->uri->segment(2);
      $data['db_table_loop']         = $this->db_table_loop;
      $data['db_visible_table_loop'] = $this->db_visible_table_loop;
      return $data;
  }



  public function tables()
  {
    return $this->db_table_loop;
  }



  public function _check_json_file($table_name)
  {
    if (! $table_name){
      $this->_view_error('Excrud : _check_json_file() : please set table name -> '.$table_name);
    }
    if (! isset($this->crud_table[$table_name]) ){
      $this->_add_table_json($table_name);
    }
  }



  public function _add_table_json($table_name)
  {
    $hash = array();
    $hash['visible_flg'] = 1;
    $hash['table_desc'] = array();
    $i = 0;
    $query = $this->CI->db->query("SELECT * FROM {$table_name} limit 1");


    foreach ($query->list_fields() as $field){
      $hash['table_desc'][$field]= array();
      $hash['table_desc'][$field]['name'] = $field;
      $hash['table_desc'][$field]['view_list_flag'] = 1;
      $hash['table_desc'][$field]['view_add_flag'] = 1;
      $hash['table_desc'][$field]['view_edit_flag'] = 1;
      $hash['table_desc'][$field]['view_delete_flag'] = 1;
      $hash['table_desc'][$field]['editable_flag'] = 1;
      if ($i ==0){
        $hash['table_desc'][$field]['editable_flag'] = 0;    //先頭カラムは editable_flag 0
        $hash['primary_column_name'] = $field;
      }
      $i++;
    }
    $this->crud_table[$table_name] = $hash;
    $json_data = json_encode($this->crud_table,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    if ( ! write_file($this->CI->config->item('excrud_data_path', 'excrud').'/table.json', $json_data)){
     die('Excrud : _add_table_json() : can not write ->'.$this->CI->config->item('excrud_data_path', 'excrud').'table.json');
    }
  }



  public function _view_json_error()
  {
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }
  }



  public function _load_or_creat_json()
  {
    $this->CI->load->helper('file');
    if ( is_file($this->CI->config->item('excrud_data_path', 'excrud').'/table.json') ){
      $json = read_file($this->CI->config->item('excrud_data_path', 'excrud').'/table.json');
      $this->crud_table	= json_decode ( $json, true );
      if (! $this->crud_table){
        echo('Excrud : _load_or_creat_json() : invalid JSON FILE -> ' . $this->CI->config->item('excrud_data_path', 'excrud').'/table.json');
        $this->_view_json_error();
        die;
      }
      return;
    }
    foreach ($this->db_table_loop as $k => $v) {
      $crud_table[$v] = array();
      if ( strcmp('migrations',$v)==0 || strcmp('sqlite_sequence',$v)==0 ){
        $crud_table[$v]['visible_flg'] = 0;
      }
      else{
        $crud_table[$v]['visible_flg'] = 1;
      }

      $query  = $this->CI->db->query("SELECT * FROM {$v} limit 1");
      $query2 = $this->CI->db->query("show full columns from {$v}");

      $comment_loop = $query2->result_array();

      $crud_table[$v]['primary_column_name'] = $query->list_fields()[0];
      $crud_table[$v]['order_by'] = "{$crud_table[$v]['primary_column_name']} ASC";

      // search_columns
      $crud_table[$v]['search_columns'] = array();
      foreach ($query->list_fields() as $field){
        array_push($crud_table[$v]['search_columns'], $field);
      }

      $field_data = $query->field_data();

      // table_desc
      $crud_table[$v]['table_desc'] = array();
      $i = 0;
      foreach ($query->list_fields() as $field){
        $crud_table[$v]['table_desc'][$field]= array();
        $crud_table[$v]['table_desc'][$field]['name'] = $field;

        $db_column_type    ='';
        $db_column_default ='';
        $query_tmp = $this->CI->db->query(" DESCRIBE {$v}");
        foreach ($query_tmp->result_array(false) as $tmp_key => $tmp_value) {
          if ($tmp_value['Field']===$field){
            $db_column_type    = $tmp_value['Type'];
            $db_column_default = $tmp_value['Default'];
            break;
          }
        }
        $crud_table[$v]['table_desc'][$field]['type']    = $db_column_type;
        $crud_table[$v]['table_desc'][$field]['default'] = $db_column_default;

        $comment_hash = $this->recursive_array_search('Field', $field, $comment_loop);	// ['class']=>['newp'] のハッシュを選択
        if ($comment_hash){ $crud_table[$v]['table_desc'][$field]['comment'] = $comment_hash['Comment']; }

        $crud_table[$v]['table_desc'][$field]['view_list_flag'] = 1;
        $crud_table[$v]['table_desc'][$field]['view_list_title'] = $field;
        $crud_table[$v]['table_desc'][$field]['view_add_flag'] = 1;
        $crud_table[$v]['table_desc'][$field]['view_edit_flag'] = 1;
        $crud_table[$v]['table_desc'][$field]['view_delete_flag'] = 1;
        $crud_table[$v]['table_desc'][$field]['editable_flag'] = 1;
        $crud_table[$v]['table_desc'][$field]['input_type'] = "text";
        if ($i ==0){ $crud_table[$v]['table_desc'][$field]['editable_flag'] = 0; }  //先頭カラムは editable_flag 0
        elseif ( $field_data[$i]->type === 'timestamp'){ $crud_table[$v]['table_desc'][$field]['editable_flag'] = 0; }  //TIMESTAMP カラムは editable_flag 0
        if ( $field_data[$i]->type === 'datetime' || $field_data[$i]->type === 'timestamp'){
            //DATETIME または TIMESTAMP カラムは view_list_formatを追加
            $crud_table[$v]['table_desc'][$field]['view_list_format'] = "{{ data|date('m/d H:i:s') }}";
        }
        $i++;
      }
    }

    if ( is_array( $this->CI->config->item('excrud_override', 'excrud') ) ){
      foreach ($this->CI->config->item('excrud_override', 'excrud') as $json_v) {
        $crud_table = $this->_override_json($json_v, $crud_table);
      }
    }
    else if ( $this->CI->config->item('excrud_override', 'excrud') ){
        $crud_table = $this->_override_json($this->CI->config->item('excrud_override', 'excrud') , $crud_table);
    }
    else{
      // OFF $crud_table = $this->_override_json('override.json' , $crud_table);
    }

    $json_data = json_encode($crud_table,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    if ( ! write_file($this->CI->config->item('excrud_data_path', 'excrud').'/table.json', $json_data)){
       die('Excrud : _load_or_creat_json() : can not write ->'.$this->CI->config->item('excrud_data_path', 'excrud').'table.json');
     }
     $this->crud_table = $crud_table;
  }



  public function _override_json( $file_name = 'override.json' , $crud_table)
  {
    $file_full_name = $this->CI->config->item('excrud_data_path', 'excrud').'/'. $file_name;
    if ( ! is_file( $file_full_name) ){
      die("please set json file : ".$file_full_name);
    }
    $add_json = read_file( $file_full_name );
    $add_hash = json_decode ( $add_json, true );
    if (! $add_hash){
      echo('Excrud : _load_or_creat_json() : invalid JSON FILE -> ' . $file_full_name);
      $this->_view_json_error();
      die;
    }

    foreach ($add_hash as $add_key => $add_table) {
      if (isset($add_table['order_by']) ){ $crud_table[$add_key]['order_by'] = $add_table['order_by']; }
      if (isset($add_table['search_columns']) ){ $crud_table[$add_key]['search_columns'] = $add_table['search_columns']; }
      if (isset($add_table['visible_flg']) ){ $crud_table[$add_key]['visible_flg'] = $add_table['visible_flg']; }

      if (isset($add_table['table_desc'] )){
        foreach ($add_table['table_desc'] as $ak => $av) {
          foreach ($av as $x_key => $x_value) {
            if ( isset($crud_table[$add_key]['table_desc'][$ak]) ){
              $crud_table[$add_key]['table_desc'][$ak][$x_key] = $x_value;
            }
          }
        }
      }
    }

    return $crud_table;

    // $json_data = json_encode($crud_table,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    // if ( ! write_file($this->CI->config->item('excrud_data_path', 'excrud').'/table.json', $json_data)){
    //    die('Excrud : _load_or_creat_json() : can not write ->'.$this->CI->config->item('excrud_data_path', 'excrud').'table.json');
    //  }
    //  $this->crud_table = $crud_table;
  }



  public function recursive_array_search($key, $value, $hash)
  {
	    foreach($hash as $k => $v) {
				if (is_array($v)){
					$rt = $this->recursive_array_search($key, $value, $v);
					if ($rt != false){ return $rt; }
				}
        else if( strcmp($key,$k)==0 && strcmp($value,$v)==0 ){
					return $hash;
				}
	    }
	    return false;
	}



  public function _visible_table_loop()
  {
    $visible_table_loop = array();
    foreach ($this->crud_table as $k => $v) {
      if ( @$v['visible_flg'] == 1 ){
        array_push($visible_table_loop, $k);
      }
    }
  return $visible_table_loop;
  }



  private function _view_error($text){
    $d['text1'] = $text;
    $d['text2'] = "on " . __FILE__ .' (' . __LINE__ . ')';
    $output = $this->CI->extwig->render('excrud_error.html',$d);
    print $output;
    die;
  }


}
