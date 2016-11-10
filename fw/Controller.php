<?php

// namespace fw;

// use fw\HelperHtml;

Class Controller
{

	protected $app; 
	protected $layout = 'layout'; //default file layout (V/layouts/layout.php)
	protected $title;
	protected $request; //untuk request

	function __construct(){
		global $app; //Mvcku($config)
		$this->app = $app;
		
		require_once 'functions.php';

		if(!$this->title)
			$this->title = $app->config['appname'];
	}

	public function render($view_file, $vars = null, $layout=null)
	{
		/* untuk menangani render
		 *
		*/
		
		if($vars)
			extract($vars);

		if(!isset($title)) 
			$title = $this->title;
		
		$view_file = str_replace('.', DIRECTORY_SEPARATOR, $view_file);

		ob_start();
		require_once (DIR_V . $view_file . '.php');
		$content = ob_get_contents();
		ob_end_clean();
		
		include(DIR_V . 'layouts/' . ($layout==null? $this->layout : $layout) . '.php');
	}

	public function get_request()
	{
		/* untuk mendapatkan request
		 *
		*/
	}

	public function has_post()
	{
		/* cek apakah ada data post
		 *
		 */
		if($_POST)
		{
			// $this->request = $_POST;
			$this->set_request($_POST);
			return true;
		}
		return false;
	}

	function set_request($data)
	{
		/* untuk membuat agar setiap data bisa dipanggil dengan gaya OOP
		 * contoh: $this-request->nama ,dsb
		*/
		foreach ($data as $key=>$value) {
			$this->request->$key = $value;
		}
	}

	function redirect($url)
	{
		header('location:' . $_SERVER['SCRIPT_NAME'] . $url);
	}
}