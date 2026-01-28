<?php namespace Admin\Core;
/**
 * 
**/
class Route
{
	private static $instance;
	public $load;
	public function __construct()
	{
		self::$instance =& $this;
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}
		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Route Class Initialized');
	}
	public static function &get_instance()
	{
		return self::$instance;
	}

    public function index() {}
    public function post() {}
}
