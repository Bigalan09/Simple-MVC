<?php
class Model extends Object {
	public static $db;
	private $stmt;
	public static function init($db) {
		self::$db = $db;
	}
	public function query($query) {
		$this->stmt = self::$db->prepare ( $query );
		return $this;
	}
	public function bind($pos, $value, $type = null) {
		if (is_null ( $type )) {
			switch (true) {
				case is_int ( $value ) :
					$type = PDO::PARAM_INT;
					break;
				case is_bool ( $value ) :
					$type = PDO::PARAM_BOOL;
					break;
				case is_null ( $value ) :
					$type = PDO::PARAM_NULL;
					break;
				default :
					$type = PDO::PARAM_STR;
			}
		}
		
		$this->stmt->bindValue ( $pos, $value, $type );
		return $this;
	}
	public function execute() {
		return $this->stmt->execute ();
	}
	public function resultset() {
		$this->execute ();
		return $this->stmt->fetchAll (PDO::FETCH_ASSOC);
	}
	public function single() {
		$this->execute ();
		return $this->stmt->fetch (PDO::FETCH_ASSOC);
	}
	public function get_last_insert_ID($column) {
		return self::$db->lastInsertId($column);
	}
}