<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mydump {

    public function dump( $mix )
    {
      print "\n".'<pre style="text-align:left;">'."\n";
  		print_r($mix);
  		print "\n</pre>\n\n";
    }
}
