<?php

require_once("QLPlus_Setting.php");

// interface for dumping the wiki semantic data into a database via ODBC
class DLVDB
{
	public $connection = null;
	
	function open()
	{
		global $wgQLPlus_DlvODBC, $wgQLPlus_DlvODBCUser, $wgQLPlus_DlvODBCPass;
		$this->connection = odbc_connect($wgQLPlus_DlvODBC, $wgQLPlus_DlvODBCUser, 
											$wgQLPlus_DlvODBCPass);
		return $this->connection;		
	}
	
	function close()
	{
		if ($this->connection)
			odbc_close($this->connection);
	}
	
	function clean($subject = null)
	{
		if ($this->connection){
			$query = "DELETE FROM triple";
			if ($subject != null)
			{
				$query .= " WHERE s='$subject'";
			}
			//print_r($query ."\n");
			$result = odbc_exec($this->connection, $query);
			return ($result != false);
		}
		return false;
	}
	
	function insertTriple($s, $p, $o)
	{
		if ($this->connection){
			$query = "INSERT INTO triple(s,p,o) VALUES ('$s','$p','$o')";
			//print_r($query."\n");
			$result = odbc_exec($this->connection, $query);
			return ($result != false);
		}
		return false;
	}
	
	// triple = array (s,p,o)
	function insertTripleArray($triple)
	{
		return $this->insertTriple($triple[0],$triple[1],$triple[2]);
	}
	
	function getPropertyMapping($p)
	{
		//e.g., #import(testodbc,"dlvodbc","dlvodbc","SELECT s,o FROM p",p).
		global $wgQLPlus_DlvODBC, $wgQLPlus_DlvODBCUser, $wgQLPlus_DlvODBCPass;
		return "#import($wgQLPlus_DlvODBC,".
						"\"$wgQLPlus_DlvODBCUser\",".
						"\"$wgQLPlus_DlvODBCPass\",".
						"\"SELECT s,o FROM triple WHERE p='$p'\",$p).\n";
	}
	
	function getCategoryMapping($c)
	{
		//e.g., #import(testodbc,"dlvodbc","dlvodbc","SELECT * FROM p",p).
		global $wgQLPlus_DlvODBC, $wgQLPlus_DlvODBCUser, $wgQLPlus_DlvODBCPass;
		$rdfs_type = "_INST";
		return "#import($wgQLPlus_DlvODBC,".
						"\"$wgQLPlus_DlvODBCUser\",".
						"\"$wgQLPlus_DlvODBCPass\",".
						"\"SELECT s FROM triple WHERE o='$c' and p='$rdfs_type'\",$c).\n";
	}
}
?>