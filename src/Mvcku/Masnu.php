<?php namespace Mvcku;

/*
prinsip: gampang-simpel-cepat-aman
- gampang: mudah develop
- simpel: gak banyak file
- cepat: eksekusi aplikasi cepat
- aman: susah dihack

catatan:

*/

Class Masnu
{

	protected $controller;
	
	public $config;

	public $db;
	public $basepath;
	public $docroot;
	public $dir_m;
	public $dir_c;
	public $dir_v;

	function version()
	{
		/*
		* jadi beta kalau sudah bisa login/logout dan CRUD
		*/
		return '1.0.1 ;) - 10 November 2016';
	}
	
	function __construct($config = null){
		// var_dump($config);
		// die();
		$this->basepath = $this->base_path();
		$this->docroot = dirname(dirname(dirname(dirname(dirname( __DIR__ )))));

		if($config)
		{

			$config = $this->configme($config);
			if(isset($config['dbdriver']))
				if($config['dbdriver']=='mysql')
				{
					try
					{
						$this->db = new \PDO('mysql:host='. $config['dbhost'] . ';dbname=' . $config['dbname'] . ';charset=utf8', $config['dbuser'], $config['dbpass']);
					}
					catch(Exception $e)
					{
						var_dump($e->getMessage());
						die(Errors::show(1101,'Koneksi gagal'));
					}
				}
		}
		else
			die('This framework need any configuration. Bye.');
	}

	//setting konfigurasi default
	public function configme($config) {	
		if(!isset($config['rute'])) $config['rute'] = 'r';
		if(!isset($config['urlmanis'])) $config['urlmanis'] = true;
		if(!isset($config['default_c'])) $config['default_c'] = 'site';
		if(!isset($config['dbhost'])) $config['dbhost'] = '127.0.0.1';
		if(!isset($config['dbuser'])) $config['dbuser'] = 'root';
		if(!isset($config['dbpass'])) $config['dbpass'] = '';
		if(!isset($config['dir_m'])) $config['dir_m'] = 'M';
		if(!isset($config['dir_v'])) $config['dir_v'] = 'V';
		if(!isset($config['dir_c'])) $config['dir_c'] = 'C';
		
		$this->dir_m = $this->docroot.('\\' . $config['dir_m'] . '\\');
		$this->dir_v = $this->docroot.('\\' . $config['dir_v'] . '\\');
		$this->dir_c = $this->docroot.('\\' . $config['dir_c'] . '\\');

		// DEFINE('DIR_M', (isset($config['dir_m'])?isset($config['dir_m']):dirname(__DIR__) .'/M/'));
		// DEFINE('DIR_V', (isset($config['dir_v'])?isset($config['dir_v']):dirname(__DIR__) .'/V/'));
		// DEFINE('DIR_C', (isset($config['dir_c'])?isset($config['dir_c']):dirname(__DIR__) .'/C/'));

		$this->config = $config;
		return $config;
	}

	private function get_routes()
	{
		//$_SERVER['PATH_INFO'] = tes ->index.php/tes
		if($this->config['urlmanis'])
		{
			$routes = empty($_SERVER['PATH_INFO']) ? 
				substr($_SERVER['REQUEST_URI'], strlen($this->base_path(false))+2):
				substr($_SERVER['PATH_INFO'], 1);
		}
		else
		{
			$r = $this->config['rute'];
			$routes = empty($_SERVER['PATH_INFO']) ? 
				(isset($_GET[$r]) ? $_GET[$r] : $this->config['default_c']) : 
				substr($_SERVER['PATH_INFO'],1);
		}
		return ($routes=='index.php') ? '' : $routes;
	}

	public function run() {

		$routes = $this->get_routes();
		// echo $routes . '<br>';

		$parts = explode('/', $routes);
		// var_dump($parts);
		$this->controller = empty($parts[0]) ? $this->config['default_c'] : $parts[0];

		//nama controller harus berakhiran huruf C
		$file_controller = $this->dir_c .  ucfirst($this->controller) . 'C.php'; 
		if(file_exists($file_controller))
			$this->eksekusi($parts);
		else
		{
			if(array_key_exists($this->controller, $this->config['urlalias']))
				return $this->run($this->config['urlalias'][$this->controller]);

			\Mvcku\Errors::show(404, 'Controller <code>' . ucfirst($this->controller) . '</code> tidak ditemukan');
		}
	}

	public function includall()
	{
		foreach (glob($this->dir_c . "*C.php") as $filename)
		{
		    include_once $filename;
		}
		foreach (glob($this->dir_m . "*.php") as $filename)
		{
		    include_once $filename;
		}
	}

	public function eksekusi($parts) {
		$action = (isset($parts[1])) ? $parts[1] : 'index'; 

		// echo 'controller :' . $this->controller;
		// echo '<br>';
		// echo 'action: ' . $action;
		// echo '<br>';
		// echo $_SERVER['QUERY_STRING'];
		// echo '<br>';
		// var_dump(($_GET));
		// die();
		// include_once 'file';
		$this->includall();
		include_once(DIR . 'C/'. ucfirst($this->controller).'C.php');

		$class = $this->config['dir_c'] . '\\' . ucfirst($this->controller) . 'C';

		$controller = new $class();
		if(method_exists($controller, $action))
		{
			switch (sizeof($parts)) {
				case 2:
					$controller->$action(); // tidak ada parameter
					break;
				case 3:
					$controller->$action($parts[2]);
					break;
				case 4:
					$controller->$action($parts[2], $parts[3]);
					break;
				case 5:
					$controller->$action($parts[2], $parts[3], $parts[4]);
					break;
				case 6:
					$controller->$action($parts[2], $parts[3], $parts[4], $parts[5]);
					break;
				default:
					$controller->$action(); //dianggap tidak ada parameter
					break;
			}
		}
		else
			Errors::show(100, 'Metode <code>' . $action . '</code> tidak ada');
	}

	private function base_path($protocol = true)
	{
		$namedir = substr(pathinfo($_SERVER['PHP_SELF'])['dirname'],1);
		if($protocol)
		{
			$http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
			return $http . $_SERVER['SERVER_NAME'] . '/' . $namedir;
		}
		return $namedir;
	}
	
}