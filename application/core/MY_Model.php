<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    protected $_db; 

    function __construct()
    {
        parent::__construct();
	    $this->load->database();
        $this->_db = $this->db;

        $this->load->library('dblibcommon');
    }
    
    public function setDB($db)
	{
		$this->_db = $db;
	}

	protected function getReadHandle()
	{
		return $this->dblibcommon->getReadHandle();
	}

	protected function getWriteHandle()
	{
		return $this->dblibcommon->getWriteHandle();
	}
	
}

