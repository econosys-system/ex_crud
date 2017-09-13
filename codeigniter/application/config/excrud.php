<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// set override.json file folder
$config['excrud_data_path']          = APPPATH.'excrud';


// Items per page in list view
$config['excrud_list_item_per_page'] = 100;


// Top page title
$config['excrud_title_name']         = 'EX_CRUD';


// Override file name
$config['excrud_override'] = array(
	'override.json' ,
);


// Set View Directory
$config['excrud_admin_view_dir']     = 'excrud_admin';


// Set Footer HTML
$config['excrud_footer_html']  = 'EXCRUD &copy; <script type="text/javascript">document.write(new Date().getFullYear());</script> <a href="http://econosys-system.com/" target="_blank">econosys system</a>';

// encode / to %2f when search string in EXCRUD
$config['excrud_search_encode_slash']  = true;


// You can use variables in "override.json"
// Ex:
//
// $config['_my_data']   = 'MY-DATA';		// config/excrud.php
//
// {{ config.excrud._my_data }}					// override.json
$config['_test_data']   = 'TEST-DATA';
