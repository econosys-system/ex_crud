<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @version 0.2
 */


class Mydump {

    public function dump( $mix )
    {
		print "\n".'<pre style="text-align:left;">'."\n";
  		print_r($mix);
  		print "\n</pre>\n\n";
    }

    public function dump2( $mix )
    {
		print "\n"."<!--".'<pre style="text-align:left;">'."\n";
  		print_r($mix);
  		print "\n</pre>\n\n"."-->"."\n";
    }

}
