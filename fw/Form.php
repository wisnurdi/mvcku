<?php

Class Form
{
	public $model;
	public $layout;
	public $method = 'POST';
	public $action = '#';

	function __construct($model=null, $args=[])
	{
		if($model)
			$this->model = $model;

		if(isset($args['method']))
			$this->method = $args['method'];

		if(isset($args['action']))
			$this->action = $args['action'];
	}

	function text($value_or_field=null, $atribut = [])
	{
		if($this->model)
		{
			$name = (new \ReflectionClass($this->model))->getShortName() . '['.$value_or_field.']';
			$value = $this->model->$value_or_field;
		}
		else
		{
			$name = isset($atribut['name'])? $atribut['name'] : '';
			$value = $value_or_field;
		}

		return '<input name="'.$name.'" type="text"'. ($value ? ' value="'. $value .'"' : '') .'>';
	}

	function text_row($label_or_field, $value=null, $atribut=[])
	{
		if($this->model)
		{
			$label = isset($atribut['label'])? $atribut['label'] : ucwords($label_or_field);
			$field = $label_or_field;
			return '<div class="row"><label class="col-2 vertical-align">'.$label.'</label><div class="col-10">'.$this->text($field, $atribut).'</div></div>';
		}
		return '<div class="row"><label class="col-2 vertical-align">'.$label_or_field.'</label><div class="col-10">'.$this->text($value, $atribut).'</div></div>';
	}

	function open()
	{
		return '<form method="'. $this->method .'" action="'.$this->action.'">';
	}

	function close()
	{
		return '</form>';
	}

	function submit($value='Submit')
	{
		return '<input type="submit" value="'.$value.'">';
	}
}