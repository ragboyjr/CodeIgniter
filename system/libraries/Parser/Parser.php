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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser extends CI_Driver_Library {

	/**
	 * Config array for template paths, to be used by children drivers
	 *
	 * @var array
	 */
	public $template_config = array();

	/**
	 * Valid parser drivers
	 *
	 * @var array
	 */
	protected $valid_drivers = array(
		'simple'
	);

	/**
	 * Reference to the driver
	 *
	 * @var mixed
	 */
	protected $driver;
	
	/**
	 * Reference to Codeigniter Instance
	 *
	 * @var object
	 */
	private $ci;


	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct($config = array())
	{
		$this->ci = &get_instance();
		
		$default_config = array(
			'valid_drivers' => array(),
			'driver'		=> '',
			'template_dir'	=> '',
			'cache_dir'		=> '',
			'compile_dir'	=> '',
		);
		
		if (count($config) > 0)
		{
			$default_config = array_merge($default_config, $config);
		}
		else
		{
			$default_config = $this->ci->config->item('parser');
		}
		
		// if user added some drivers, then we need put them in the valid_drivers array
		// to get loaded
		$this->valid_drivers = array_merge($this->valid_drivers, $default_config['valid_drivers']);
		
		// set template config items
		$this->template_config['template_dir']	= ($default_config['template_dir'])	? $default_config['template_dir'] : APPPATH.'views/templates/';
		$this->template_config['cache_dir']		= ($default_config['cache_dir'])	? $default_config['cache_dir'] : APPPATH.'cache/';
		$this->template_config['compile_dir']	= ($default_config['compile_dir'])	? $default_config['compile_dir'] : APPPATH.'views/templates/compile/';

		// set the driver
		$this->driver = $this->load_driver((($default_config['driver']) ? $default_config['driver'] : 'simple'));	// make sure we have a driver to load
	}
	
	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template view,
	 * replacing them with the data in the second param. Returns the loaded view
	 * as string
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse($template, $data = array(), $return = FALSE)
	{
		return $this->driver->parse($template, $data, $return);
	}
	

	// --------------------------------------------------------------------

	/**
	 * Parse a String
	 *
	 * Parses pseudo-variables contained in the specified string,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse_string($template, $data, $return = FALSE)
	{
		return $this->driver->parse_string($template, $data, $return);
	}
	
	public function __get($name)
	{
		if (property_exists($this->driver, $name))
		{
			return $this->driver->{$name};
		}
		
		return NULL;	
	}
	
	/**
	 * __call magic method
	 *
	 * Any call to the parser driver will default to calling the specified adapter
	 *
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __call($method, $args = array())
	{
		if (method_exists($this->driver, $method))
		{
			return call_user_func_array(array($this->driver, $method), $args);
		}
		
		return NULL;
	}
}

/**
 * CI_Parser_driver Class
 *
 * Extend this class to make a new CI_Parser driver
 * Making a new driver is fairly simple, the only two methods required are parse and parse_string.
 *
 * The parse method will parse the specified file with the given data and will either
 * return the parsed data or output data to the browser (using echo, print, printf, by appending
 * the output to the CI output class, etc...)
 *
 * The parse_string method does the same thing the parse method does except that it parses
 * a given string and not a file.
 *
 * Due to the nature of how CI drivers are loaded, you can't access the "parent" drivers
 * properties in the constructor of your driver. However, if you overload the initialize
 * method then you can init your class AND use the "parent" drivers properties.
 *
 * Parse and parse_string MUST be defined in the new driver or else a php fatal error will be
 * thrown due to the nature of abstract methods. If it's not feasible for your parser driver
 * to support this functionality, then define each method to return true.
 * e.g.
 * public function parse($template, $data = array(), $return = FALSE){return TRUE;}
 * public function parse_string($template, $data = array(), $return = FALSE){return TRUE;}
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
abstract class CI_Parser_driver extends CI_Driver {

	/**
	 * Parse a template file
	 *
	 * Parses pseudo-variables contained in the specified template view,
	 * replacing them with the data in the second param. Thrid param specifies
	 * wheter or not to return data or echo for output.
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	abstract public function parse($template, $data = array(), $return = FALSE);
	
	/**
	 * Parse a template string
	 *
	 * Parses pseudo-variables contained in the specified template string,
	 * replacing them with the data in the second param. Thrid param specifies
	 * wheter or not to return data or echo for output. Can be very useful for templates
	 * stored in a database
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	abstract public function parse_string($template, $data, $return = FALSE);
	
	/**
	 * Initialize driver
	 *
	 * @return	void
	 */
	protected function initialize()
	{
		// Overload this method to implement initialization
	}

	/**
	 * Decorate
	 *
	 * Decorates the child with the parent driver lib's methods and properties
	 *
	 * @param	object	Parent library object
	 * @return	void
	 */
	public function decorate($parent)
	{
		// Call base class decorate first
		parent::decorate($parent);

		// Call initialize method now that driver has access to $this->_parent
		$this->initialize();
	}
}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser/Parser.php */