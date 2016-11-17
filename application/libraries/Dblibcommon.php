<?php

class Dblibcommon{



	private static $CI;

	public function __construct(){
		self::$CI =& get_instance();
	}


	public function getReadHandle(){
		return self::$CI->load->database('read', TRUE);
	}
	
	public function getWriteHandle(){
                return self::$CI->load->database('write', TRUE);
	}
	
}
?>
