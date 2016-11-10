<?php namespace Mvcku;

Class Errors extends \Exception
{
	static function show($kode, $message=null)
	{
		$title = 'Error ' . $kode;
		(new Controller())->render('fw_error', compact('title', 'message'));
		exit();
	}

	static function throw_error()
	{
		$control = new Controller;
		$control->render();
	}
}