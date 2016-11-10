<?php namespace Mvcku;

/* masalah akan timbul jika nama attribut tabel sama dengan nama variabel pada Class ini
 * mungkin solusinya bisa dibuat variabel private, kemudian menggunakan fungsi get
 */

Class Modeldb
{
	protected $table;		//setting nama tabel di database
	protected $pkid = 'id';	//setting primary key default
	protected $pagesize = 15; //setting ukuran pagination
	static $DB; //database
	public $stmt; //
	public $sql = '';    //sintaks berjalan
	public $binding = [];
	protected $app; //untuk memanggil konfigurasi
	public $skema;	//skema database
	protected $fillable;


	function __construct()
	{
		global $app;
		$this->app = $app;

		if(!$this->table)
		{
			$namakelas = strtolower(get_class($this));
			$this->table = substr($namakelas, strrpos($namakelas, '\\') + 1);
		}

		$this->sql = 'SELECT * FROM ' . $this->table;

		self::$DB = $app->db;

		$this->set_skema();	
	}

	function set_skema()
	{
		$columns = [];
		$rs = self::$DB->query('SELECT * FROM ' . $this->table . ' LIMIT 0');
		
		for ($i = 0; $i < $rs->columnCount(); $i++) {
		    $col = $rs->getColumnMeta($i);
		    $columns = array_merge( $columns , [$col['name'] => $col['native_type']]);
		}
		
		$this->skema = $columns;

		$this->set_property();
	}

	function load_attribute($data)
	{
		if($data && is_array($data))
		{
			foreach ($data as $key => $value) {
				if(array_key_exists($key, $this->skema))
					$this->$key = $value;
			}

			return true;
		}

		return false;
	}

	function set_property($data = null)
	{
		/* untuk membuat agar setiap property bisa dipanggil dengan gaya OOP
		 * contoh: $model->nama
		 * $model->jeniskelamin, dsb
		*/
		foreach ($this->skema as $key=>$value) {
			$this->$key = ($data && isset($data[$key]))? $data[$key] :null;
		}
	}

	public function all($page = 0)
	{
		/* page digunakan untuk pagination, menunjukkan halaman ke berapa
		 *
		*/
		if(!$this->sql)
		{
			if($page>0)
			{
				$start = ($page -1 ) * $this->pagesize;
				$this->sql = "SELECT * FROM " . $this->table . ' LIMIT ' . $start . ', ' . $this->pagesize;
			}
			else
				$this->sql = "SELECT * FROM " . $this->table . ' LIMIT ' . $this->pagesize;
		}
		
		return $this->exe();
	}

	public function first()
	{
		/* untuk mengambil baris pertama dari hasil query
		*/
		if(!$this->sql)
			$this->sql = 'SELECT * FROM ' . $this->table;

		$this->sql .= ' LIMIT 1';

		return $this->exe()[0];
	}

	public function find($pk)
	{ 
		/* find by primary key
		*/
		$this->sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->pkid . '=' . $pk . ' LIMIT 1';
		$hasil = $this->exe()[0];
		$this->set_property($hasil);
		return $hasil;
	}

	public function save($arrData = null)
	{
		/* fungsi untuk menyimpan data
		 *
		*/

		return (isset($this->id) && $this->id != null) ? $this->update($arrData) : $this->create($arrData);
	}

	public function create($arrData)
	{
		/* insert baru
		 *
		*/
		$fieldTable = '';
		$fieldValue = '';
		$binding = [];
		
		if($this->fillable)
		{
			foreach ($this->fillable as $value) {
				if(isset($arrData[$value]))
				{
					$fieldTable .= $value . ',';
					$fieldValue .= ':' . $value . ',';
					$binding = array_merge($binding, [':'. $value => $arrData[$value]] );					
				}
			}
		}
		else
		{			
			foreach ($this->skema as $key => $value) {
				if(isset($arrData[$key]))
				{
					$fieldTable .= $key . ',';
					$fieldValue .= ':' . $key . ',';
					$binding = array_merge($binding, [':'. $key => $arrData[$key]] );					
				}
			}
		}
			
		$fieldTable = substr($fieldTable, 0, -1);
		$fieldValue = substr($fieldValue, 0, -1);

		$stmt = self::$DB->prepare('INSERT INTO ' . $this->table . '(' . $fieldTable . ') VALUES('. $fieldValue .')');
		
		$stmt->execute($binding);
		
		return $stmt->rowCount();
	}

	public function update($arrData = null)
	{
		//update data
		$update_set = '';
		$params = []; //untuk menyimpan data parameter
		if(!$arrData)
			$arrData = $this->fillable ? $this->fillable : array_keys($this->skema);

		foreach ($this->skema as $key=>$value){
			// if(isset($this->$key) && in_array($key, $arrData))
			if(in_array($key, $arrData))
			{
				if(empty($update_set))
					$update_set .= ' `'. $key.'` =:'.$key;
				else
					$update_set .= ', `'. $key.'` =:' . $key;

				$params = array_merge($params, [$key => $this->$key]);
			}
		}

		$sql_prepare = "UPDATE " . $this->table . " SET " . $update_set .  " WHERE `". $this->pkid . "`=" . $this->{$this->pkid};

		$stmt = self::$DB->prepare($sql_prepare);

		foreach ($params as $key => &$val) {
		    $stmt->bindParam($key, $val);
		}
		
		$stmt->execute();

		return $stmt->rowCount();
	}

	public function delete($id){ //delete data
		$stmt = self::$DB->prepare('DELETE FROM ' . $this->table . ' WHERE ' . $this->pkid . '=:id');
		$stmt->bindValue(':id', $id, \PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->rowCount();
	}

	public function where_like(){
		// $stmt = $db->prepare("SELECT field FROM table WHERE field LIKE ?");
		// $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
		// $stmt->execute();
	}

	public function order_by($columns, $asc = 'ASC')
	{
		if(in_array(strtoupper($asc), ['ASC', 'DESC']))
			$this->sql .= ' ORDER BY ' . $columns . ' ' . $asc;

		return $this;
	}

	function select($columns)
	{
		$this->sql = str_replace('*', $columns, $this->sql);
		return $this;
	}

	public function exe()
	{
		$this->stmt = self::$DB->prepare($this->sql);
		$this->stmt->execute($this->binding);
		$hasil = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
		return $hasil;
	}

	public function where($params)
	{
		if(is_array($params)){
			$conds = '';
			$binding = [];
			foreach ($params as $key => $value) {
				$conds .= $key . '=:' . $key . ' AND ';
				$binding = array_merge($binding, [':'. $key => $value]);
			}
			$conds = substr($conds,0,-4);
			$this->binding = $binding;
			$this->sql .= ' WHERE (' . $conds . ')';
		}
		else
			$this->sql .= ' WHERE ' . $params;
		
		return $this;
	}

	public function and_where($params)
	{
		if(is_array($params)){
			$conds = '';
			$binding = [];
			foreach ($params as $key => $value) {
				$conds .= $key . '=:' . $key . ' AND ';
				$binding = array_merge($binding, [':'. $key => $value]);
			}
			$conds = substr($conds,0,-4);
			$this->binding = $binding;
			$this->sql .= ' AND (' . $conds . ')';
		}
		else
			$this->sql .= ' AND ' . $params;
		
		return $this;
	}

	public function or_where($params)
	{
		if(is_array($params)){
			$conds = '';
			$binding = [];
			foreach ($params as $key => $value) {
				$conds .= $key . '=:' . $key . ' AND ';
				$binding = array_merge($binding, [':'. $key => $value]);
			}
			$conds = substr($conds,0,-4);
			$this->binding = $binding;
			$this->sql .= ' OR (' . $conds . ')';
		}
		else
			$this->sql .= ' OR ' . $params;
		
		return $this;
	}
}