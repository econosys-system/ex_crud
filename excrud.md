## ex_crud インストール方法

codeIgniter/application/config/config.php
```
$config['base_url'] = 'http://YOUR_SITE_NAME/';
```





## override.json の設定例
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

## override.json の設定項目
| 項目名 | 値 |
|:-----------|:------------|
| visible_flg | このテーブル自体をユーザーに見えるようにするか？ |
| order_by | ソート順 |
| search_columns | 検索するカラム |



## override.json（table_desc内） の設定項目
| 項目名 | 値 |
|:-----------|:------------|
| input_type |        select（ドロップダウンリスト） <br> checkbox（チェックボックス） <br>textarea（テキストエリア） <br> 指定なし（1行テキスト） |
| input_values     | select, checkbox の値リスト |
| view_list_title | 一覧表示でのタイトル |
| view_list_format | 一覧表示での表示フォーマット<br>（変数 {{data}}, {{z['カラム名']}} 使用可能） |
| view_list_nowrap_flag | 1の時一覧表示で折り返さない |
| view_list_flag| 0の時一覧表示に表示しないようにする |
| view_edit_flag | 0の時編集画面に表示しないようにする |
| view_delete_flag | 0の時削除確認画面に表示しないようにする |
| editable_flag | 0の時編集画面で編集させないようにする |




## ex) input_type : "checkbox"
```
"my_flg": {
    "input_type"     : "checkbox" ,
    "input_values"   : [
      { "name"  : "my_flag_label" , "value" : 1 }
    ]
},

```








### input_values の例
```
"input_values"     : [
  { "name"  : "京都" ,  "value" : "kyoto" },
  { "name"  : "沖縄" , "value" : "okinawa" }
]

```
 