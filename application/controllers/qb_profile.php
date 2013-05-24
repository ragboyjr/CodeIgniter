<?php

$print_query = TRUE;

class Qb_profile extends CI_Controller
{
	public $db;
	public $db_fast;

	public function __construct()
	{
		parent::__construct();
		
		$this->db		= $this->load->database('', true, true);
		$this->db_fast	= $this->load->database('', true, 'tmp');
	}
	
	public function index($type = '')
	{
		global $print_query;
		$print_query = FALSE;
		
		// func1 and func2 must be defined before you include
		$num_iters = 1000;
		$vals = array();
		$res = array(
			array(), array()
		);
		
		$obj = $this;

		$loop_func = function () use ($obj, $vals, &$res, $num_iters, $type)
		{
			for ($i = 0; $i < $num_iters; $i++)
			{
				$ts = microtime(TRUE);
	
				$obj->func1($type);
	
				$tf = microtime(TRUE);
	
				$vals[] = $tf - $ts;
			}

			$res[0][] = (array_sum($vals) / $num_iters);
			$vals = array();

			for ($i = 0; $i < $num_iters; $i++)
			{
				$ts = microtime(TRUE);
	
				$obj->func2($type);
	
				$tf = microtime(TRUE);
	
				$vals[] = $tf - $ts;
			}

			$res[1][] = (array_sum($vals) / $num_iters);
			$vals = array();
		};

		for ($i = 0; $i < 100; $i++)
		{
			$loop_func();
		}
		
		$res0 = (array_sum($res[0]) / count($res[0])) * 1000;
		$res1 = (array_sum($res[1]) / count($res[1])) * 1000;

		echo "func1: {$res0}\n";
		echo "func2: {$res1}\n";
		echo 'func1 speedup (res[1] / res[0]): ' . round($res1 / $res0, 5) . PHP_EOL;
		echo 'func2 speedup (res[0] / res[1]): ' . round($res0 / $res1, 5) . PHP_EOL;
		exit;
	}
	
	public function test($type = '')
	{
		if ($type == 'get')
		{
			$this->db->get('mytable');		
			$this->db_fast->get('mytable');
		}
		
		exit;
	}
	
	public function func1($type = '')
	{
		if ($type == 'get')
		{
			$this->db->get('mytable');
		}
	}
	
	public function func2($type = '')
	{
		if ($type == 'get')
		{
			$this->db_fast->get('mytable');
		}
	}
	
	public function print_db()
	{
		print_r($this->db);
		print_r($this->db_fast);
	}
}

