<?

class db
{
	static private $dbconn;

	/*
	 * 생성자
	 */
	public function __construct()
	{
		$this->connect();
	}
	
	/*
	 * connect
	 */
	private function connect()
	{
		self::$dbconn = mysql_connect(DB_HOST,DB_USER,DB_PASS) or die("Error: DB Not connected");
		mysql_select_db(DB_NAME, self::$dbconn);
		// mysql_query("set names euckr");
	}

	/*
	 * query
	 */
	private function query($sql)
	{
		$result = mysql_query($sql,self::$dbconn);
		return $result;
	}

	/*
	 * 쿼리실행
	 */
	public function exec_sql($sql)
	{
		return $this->query($sql);
	}

	/*
	 * 레코드셋 리턴
	 */
	public function record_set($sql,$typ=0)
	{
		$result = $this->query($sql) or die("Error: ".$sql);
		while($row = mysql_fetch_array($result)) {
			$data[] = $row;
		}
		return $data;
	}

	/*
	 * 단일값 리던
	 */
	public function ret_row($sql,$n=0)
	{
		$result = $this->query($sql) or Die("Error : 쿼리문을 확인하세요. (".$sql.")");
		return @mysql_result($result,0,$n);
	}

	/*
	 * last_insert_id
	 */	
	public function insert_id()
	{
		$sql = "select last_insert_id()";
		return $this->ret_row($sql);
	}

	/*
	 * UTF-8 decode
	 */
	public function deCode($str) {
		return iconv("UTF-8","CP949",$str);
	}

	/*
	 * 소멸자
	 */
	public function __destruct()
	{
		mysql_close(self::$dbconn);
	}
}
?>