<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Version 1.00
// Version 1.01 [fix] add login button name

class Minimal_auth
{
    private $CI;
    private $minimal_auth_login_flag = 0;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('minimal_auth');
        $this->CI->load->library('session');
    }



    public function view_login($flag = '')
    {
        $sess = $this->CI->session->all_userdata();
        $err_mess = '';
        if (strcmp($flag, 'error') == 0) {
            $err_mess = 'Minimal_auth : Login Error';
        }
        print <<< DOC_END
<html>
<head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/sha256.js"></script>
<script>
function make_hidden(e,n,d){var m=document.createElement("input");m.type="hidden",m.name=e,m.value=n,d?document.forms[d].appendChild(m):document.forms[0].appendChild(m)}
</script>
<style type="text/css">
*{
  font-size:12px;
  font-family: serif;
}
</style>
</head>
<body>
{$err_mess}
<form method="post" action="{$sess['login_url']}" onsubmit="return false;" >
<h1>Enter the password</h1>
<input type="password" id="in_text" name="in_text">
<input type="button" value="login" onclick="var v = document.getElementById('in_text').value; v = CryptoJS.SHA256(v); make_hidden('apb',v); document.getElementById('in_text').value=''; this.form.submit();">
<script>document.getElementById('in_text').focus();</script>
</form>
</body>
</html>
DOC_END;
    }



    public function login_submit()
    {
        $password_sha256 = hash('sha256', $this->CI->config->item('minimal_auth_password'));
        if (strcmp($password_sha256, $this->CI->input->post('apb')) == 0 && (strcmp($this->CI->input->post('apb'), '') != 0)) {
            $sess = $this->CI->session->all_userdata();
            $session['minimal_auth_login_flag'] = 1;
            $this->CI->session->set_userdata($session);
            redirect($sess['jump_to']);
            exit();
        } else {
            $this->view_login('error');
            exit();
        }
    }



    public function login($login_url, $jump_to)
    {
        $sess = $this->CI->session->all_userdata();

        if (@$sess['minimal_auth_login_flag'] == 1) {
            return;
        } elseif (preg_match('/login_submit/', uri_string())) {
            $this->login_submit();
        } else {
            $session['login_url'] = $login_url;
            $session['jump_to'] = $jump_to;
            $session['minimal_auth_login_flag'] = 0;
            $this->CI->session->set_userdata($session);
            $this->view_login($login_url);
            exit();
        }
    }

}
