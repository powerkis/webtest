<?

include "cls.db.php";
class proc {
	static protected $instance;

	static function get_instance() {

		if (!self::$instance) {
			self::$instance = new proc();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public function __init($chk = 0) {

		$this -> db = new db();
		
		mysql_query("set session character_set_connection=utf8;");
		mysql_query("set session character_set_results=utf8;");
		mysql_query("set session character_set_client=utf8;");
	}
	
}
?>