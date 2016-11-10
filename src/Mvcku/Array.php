<?php namespace Mvcku;


Class Array
{
	public static function get_array_key($arr)
	{
		$names = [];
		foreach ($arr as $key => $value) {
			$names[] = $value;
		}
		return $names;
	}

}