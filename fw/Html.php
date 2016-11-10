<?php

Class Html
{

	static function h($teks, $level)
	{
		return '<h'.$level.'>'. $teks.'</h'.$level.'>';
	}

	static function script()
	{
		return '<script></script>';
	}

	static function style()
	{
		return '<stye></style>';
	}

	static function form_open()
	{
		return '<form>';
	}

	static function form_close()
	{
		return '</form>';
	}

	static function input($tipe)
	{
		return '';
	}

	static function img($file_gambar, $attribut)
	{
		return '<img>';
	}

	static function grid()
	{
		return 'grid';
	}
	
	public static function table_header($header, $nomor=true){
		$outp = '<thead><tr>';
		if($nomor)
			$outp .= '<th>#</th>'; 

		foreach ($header as $key=>$value) {
			$outp .= '<th>' . $value . '</th>'; 
		}

		$outp .= '</tr></thead>';
		return $outp;
	}

	public static function table_row($header, $row, $i, $nomor=true, $options = []){
		// $options = [
		// 	1 => ['tag' => 'a', 'href'=>'diklat/view', 'params'=>['id'=>'id']]
		// ];
		$outp = '<tr>';
		if($nomor)
			$outp .= '<td>'. $i .'</td>';
		
		$j = 1;
		foreach ($header as $headval) 
		{
			$row[$headval] = empty($row[$headval] ) ? '-' : $row[$headval] ;
			if(isset($options[$j])){

				if($options[$j]['tag']=='a')
				{
					$parameter = '';
					if(isset($options[$j]['params'])){
						foreach ($options[$j]['params'] as $key => $value) {
							// $parameter .= '/' .$key . '/' . $row[$value];
							// $isi = empty($row[$value]) ? '-' : $row[$value];
							$parameter .= '/' . $row[$value];
							// $parameter .= '/' . $isi;
						}
					}
					$outp .= '<td><a href="/?r' . '=' . $options[$j]['href'] . $parameter .'">' . $row[$headval] . '</a></td>'; 	
					// $outp .= '<td><a href="/?' . $this->app->config['rute'] . '=' . $options[$j]['tag'] .'">' . $row[$headval] . '</a></td>'; 	
				}
			}
			else
				$outp .= '<td>' . $row[$headval] . '</td>'; 
			
			$j++;
		}

		$outp .= '</tr>';
		return $outp;
	}

	static function table_show($data, $header=null, $attribut=null, $nomor=true, $rowOptions=[])
	{
		if(!$data)
			return '<h1>:-(</h1><h2>Data Tidak ada</h2>';

		//untuk menampilkan tabel, semua parameter berupa array
		$table_class = (isset($attribut['class']) && !empty($attribut['class']))? ' class="' . $attribut['class'] . '"' : '' ; 
		$table_style = (isset($attribut['style']))? ' style="' . $attribut['style'] . '"' : '' ; 
		$table_border = (isset($attribut['border']))? ' border="' . $attribut['border'] . '"' : '' ; 

		$attribut = $table_class . $table_style . $table_border;
		$outp = '<table ' . $attribut . '>';
		
		$datahead = array_keys($data[0]);

		if(!$header)
			$header = $datahead;

		$outp .= self::table_header($header, $nomor);

		$outp .= '<tbody>';
		$i = 1;

		foreach ($data as $value) 
			$outp .= self::table_row($header, $value, $i++, $nomor, $rowOptions);

		$outp .= '</tbody></table>';

		return $outp;
	}
	
}