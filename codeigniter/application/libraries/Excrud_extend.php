<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excrud_extend {

	public function __construct(){
    $this->CI =& get_instance();
	}




	public function delete_pre($table_name='', $data_id='')
	{
	}




	public function delete_submit_pre($table_name='', $data_id='')
	{
	}




	public function delete_submit_post($table_name='', $data_id='')
	{
	}




	public function dl_delete_submit_pre($table_name='', $dl=array() )
	{
	}




	public function dl_delete_submit_post($table_name='', $dl=array() )
	{
	}




}

