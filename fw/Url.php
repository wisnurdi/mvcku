<?php


Class Url
{

	static function create($controller_action_params, $query = [])
	{
		/*
		* Contoh $controller_action_params: site/about
		*/
		global $app;
		

		$r = $app->config['rute'];
		if($app->config['urlmanis'])
			$url = '/' . $controller_action_params;
		else
			$url = '/?' . $r . '=' . $controller_action_params;
		
		foreach ($query as $key => $value) {
			$url .= (strpos($url, '?')>0?'&':'?') . $key . '=' . $value;
		}
		
		return self::home() . $url;
	}

	static function home()
	{
		global $app;
		return $app->basepath;
	}
	
}