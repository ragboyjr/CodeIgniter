<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'third_party/Rain/Tpl.php';


/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser_raintpl extends CI_Driver {

	/**
	* tpl template instance
	*
	* @var object
	*/
	protected $tpl;
	
	protected $tpl_methods;
	
	protected $tpl_properties;
	
	protected $tpl_config;

	public function __construct()
	{
		$ci = &get_instance();
		
		$config = array(
			'charset'			=> 'UTF-8',
			'debug'				=> FALSE,
			'tpl_dir'			=> APPPATH . 'templates/',
			'cache_dir'			=> APPPATH . 'cache/',
			'tpl_ext'			=> 'html',
			'base_url'			=> '',
			'php_enabled'		=> FALSE,
			'template_syntax'	=> 'Rain',
			'auto_escape'		=> TRUE,
			'sandbox'			=> TRUE
		);
		
		if ($ci->config->load('raintpl', TRUE, TRUE))
		{
			$config = array_merge($config, $ci->config->item('raintpl'));
		}
		
		$this->tpl = new Rain\Tpl();
		
		// set the config items
		Rain\Tpl::configure($config);
		
		log_message('debug', "Rain\Tpl Class Initialized");
	}
	
	public function parse($template, $data = array(), $return = FALSE)
	{
		foreach ($data as $key => $value)
		{
			$this->tpl->assign($key, $value);
		}

		return $this->tpl->draw($template, $return);
	}
	
	public function parse_string($template, $data = array(), $return = FALSE)
	{
		foreach ($data as $key => $value)
		{
			$this->tpl->assign($key, $value);
		}

		return $this->tpl->drawString($template, $return);
	}
	
	/*
	 * Rain\Tpl Methods
	 */
	public function draw($template_path, $to_string = FALSE)
	{
        $this->tpl->draw($template_path, $to_string);
    }

    public function drawString($string, $to_string = FALSE)
    {
        $this->tpl->drawString($string, $to_string);
    }

    public function assign($variable, $value = NULL)
    {
    	$this->tpl->assign($variable, $value);
    }
}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser.php */