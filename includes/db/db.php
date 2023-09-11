<?php    
    class db{

        protected $conn;
        protected $query;
        protected $show_errors = TRUE;
        protected $query_closed = TRUE;
        public $query_count = 0;

        public function __construct($db_host = 'localhost', $db_user = 'root', $db_pass = '',$db_name = '',$charset = 'utf8')
        {
           $this->conn= new mysqli($db_host, $db_user, $db_pass, $db_name);
            
           if($this->conn->connect_error){
                die('Failed to Connect to Mysql'  . $this->conn->connect_error);
           }
           $this->conn->set_charset($charset);
        }

        public function query($query){
            if($this->query = $this->conn->prepare($query)){
               if(func_num_args() > 1){
                $x = func_get_args();
                $args = array_slice($x, 1);
				$types = '';
                $args_ref = array();
                foreach ($args as $k => &$arg) {
					if (is_array($args[$k])) {
						foreach ($args[$k] as $j => &$a) {
							$types .= $this->_gettype($args[$k][$j]);
							$args_ref[] = &$a;
						}
					} else {
	                	$types .= $this->_gettype($args[$k]);
	                    $args_ref[] = &$arg;
					}
                }
				array_unshift($args_ref, $types);
                call_user_func_array(array($this->query, 'bind_param'), $args_ref);
               }
               $this->query->execute();
               if($this->query->errno){
                   die("Unable to process Database Query - Check your Parameters - ".$this->query->error);
               }
               $this->query_count++;
            }else{
                die("Unable to prepare query statement - Check your query syntax - ". $this->conn->error);
            }
            return $this;
        }

        public function fetch_all($callback = null) {
            $params = array();
            $row = array();
            $meta = $this->query->result_metadata();
            while ($field = $meta->fetch_field()) {
                $params[] = &$row[$field->name];
            }
            call_user_func_array(array($this->query, 'bind_result'), $params);
            $result = array();
            while ($this->query->fetch()) {
                $r = array();
                foreach ($row as $key => $val) {
                    $r[$key] = $val;
                }
                if ($callback != null && is_callable($callback)) {
                    $value = call_user_func($callback, $r);
                    if ($value == 'break') break;
                } else {
                    $result[] = $r;
                }
            }
            $this->query->close();
            $this->query_closed = TRUE;
            return $result;
        }
    

        public function fetch_array() {
            $params = array();
            $meta = $this->query->result_metadata();
            while ($field = $meta->fetch_field()) {
                $params[] = &$row[$field->name];
            }
            call_user_func_array(array($this->query, 'bind_result'), $params);
            $result = array();
            while ($this->query->fetch()) {
                foreach ($row as $key => $val) {
                    $result[$key] = $val;
                }
            }
            $this->query->close();
            return $result;
        }

        public function close(){
            return $this->conn->close();
        }

        public function escape_string($str){
            return $this->conn->real_escape_string($str);
        }

        private function _gettype($var) {
            if(is_string($var)) return 's';
            if(is_float($var)) return 'd';
            if(is_int($var)) return 'i';
            return 'b';
        }

        public function affected_rows() {
            return $this->query->affected_rows;
        }

        public function num_rows() {
            $this->query->store_result();
            return $this->query->num_rows;
        }

        public function insert_id() {
            return $this->conn->insert_id;
        }
    
        public function error($error) {
            if ($this->show_errors) {
                exit($error);
            }
        }

    }

?>