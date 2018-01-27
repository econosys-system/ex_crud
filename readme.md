![EX_CRUD](./img_src/logo.png)

#EX_CRUD : CRUD SYSTEM for CodeIgniter and MySQL

 - Easy Customizable ( using Bootstrap + Twig Template )
 - You can use CRUD + Search + Execute SQL


#● Install EX_CRUD


## 1. upload files to your server


- codeigniter/application/
- /excrud
  

## 2. configure config.php , database.php

- codeIgniter/application/config/config.php

```
$config['base_url'] = 'http://YOUR_SITE_NAME/';
```

- codeIgniter/application/config/database.php


## 3. change directory permission (0777)

```
chmod 0777 codeigniter/application/excrud
```





# ● Start EX_CRUD
access excrud_admin Controller

ex) http://YOUR_SITE_NAME/excrud_admin  
ex) http://YOUR_SITE_NAME/index.php/excrud_admin



#● Configure ex_crud

## 1. Create Json file "___sample___override.json"
select menu "Config > Create Json Sample"
![](file:./excrud.md.img/create_json_sample.png)


## 2. Rename Json file "___sample___override.json" to "override.json"

```
cd codeIgniter/application/excrud/
mv ___sample___override.json  override.json
```


## 3. Edit "override.json"




## 4. re create Json file
select menu "Config > Json re-create"
![](file:./excrud.md.img/recreate_json.png)



#● How to edit override.json

## ex
```
	"test_dt": {
		"visible_flg"   : 0 ,
		"order_by"      : "test_id ASC",
		"search_columns": [
			"user_name"
		],

      "table_desc": {
        "test_id": {
            "view_list_title": "テーブルID"
        },
        "user_name": {
            "view_list_title": "ユーザー名"
        }
      }
  } ,
  "item_dt": {
      "visible_flg": 0 ,
      "table_desc": {
        "item_id": {
            "view_list_title": "商品ID"
        },
        "item_name": {
            "view_list_title": "商品名"
        }
      }
  }

```

## override.json properties
| property | value(s) |
|:-----------|:------------|
| visible_flg | 1 ( show this table ) ( default )<br>0 ( hide this table ) |
| order_by | ORDER BY in list view |
| search_columns | search columns |



## override.json（table_desc）properties
| property | value(s) |
|:-----------|:------------|
| tabe_header | table header title |
| view\_add\_flag | 1 ( show in add view ) ( default )<br>0 ( hide in edit view ) |
| view\_edit\_flag | 1 ( show in edit view ) ( default )<br>0 ( hide in edit view ) |
| view\_delete\_flag |  1 ( show in delete view ) ( default )<br>0 ( hide in delete view )  |
| editable_flag |  1 ( editable ) ( default )<br>0 ( can not edit )  |
| input_type | text ( default )<br>select<br> checkbox<br> radio<br> textarea |
| input\_type\_css     | width:300px;height:500px; |
| input_values     | values for select, checkbox |
| view\_list\_flag | 1 ( show in list view ) ( default )<br>0 ( hide in list view ) |
| view\_list\_title | title for list view |
| view\_list\_format | format for list view<br>（ You can use variables <br> {{data}}, {{z['db_column_name']}} ） |
| view\_list\_nowrap\_flag | 0 ( wrap )( default )<br>1 ( no wrap ) |
| multiple\_edit\_flag | 1 ( show in multipile edit ) <br>0 ( hide in multipile edit ) ( default ) |



## ex) input_type : "checkbox"
```
"my_flg": {
    "input_type"     : "checkbox" ,
    "input_values"   : [
      { "name"  : "my_flag_label" , "value" : 1 }
    ]
},

```



## ex) input_type : "select"
```
"my_country": {
    "input_type"     : "select" ,
    "input_values"   : [
      { "name"  : "USA"       , "value" : "USA" } ,
      { "name"  : "Brazil"    , "value" : "Brazil" } ,
      { "name"  : "France"    , "value" : "France" } ,
      { "name"  : "Australia" , "value" : "Australia" } ,
      { "name"  : "Canada"    , "value" : "Canada" } 
    ]
},

```



## ex) input_type : "radio"
```
"my_country": {
    "input_type"     : "radio" ,
    "input_values"   : [
      { "name"  : "male"     , "value" : "male" } ,
      { "name"  : "female"   , "value" : "female" } 
    ]
},

```








