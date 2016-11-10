<?php

trait Util
{
	static function debug($data = 1, $show=true)
	{
		/* untuk mendebug variable data
		 *
		*/
		$data = $data==1 ? $_SERVER : $data;
		$data = ($data) ? $data : 'NULL';
		// $data = ($data) ? $data : $_SERVER;
		$outp = '';

		if(is_array($data))
			foreach ($data as $key =>$value) 
				if(is_array($value))
					$outp .= $key . ' => ' . $this->debug($value);
				else
					$outp .= $key . ' => ' . $value . '<br>';
		else
			$outp = $data;
		
		if($show)
			echo '<pre>' . $outp . '</pre>';

		return $outp;
	}
}