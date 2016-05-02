<?php
/**
 * Created by mohuishou<1@lailin.xyz>.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2016/5/2 0002
 * Time: 12:51
 */
namespace ImageOCR;
class DB{

    protected $_db_host;
    protected $_db_port;
    protected $_db_name;
    protected $_db_user;
    protected $_db_password;

    protected $_db;

    public function __construct($host="127.0.0.1",$port="3306",$name="test",$user="root",$password="lailin")
    {
        $this->_db_host=$host;
        $this->_db_port=$port;
        $this->_db_name=$name;
        $this->_db_user=$user;
        $this->_db_password=$password;

        $this->connect();
    }

    public function connect(){
        try {
            $this->_db = new \PDO("mysql:host={$this->_db_host}:{$this->_db_port};dbname=".$this->_db_name, $this->_db_user, $this->_db_password);
            echo '数据库连接成功';
        } catch (\PDOException $e) {
            print "Error: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function add($code,$hash_data){
        $sql="INSERT INTO `{$this->_db_name}`.`image_ocr` ( `code`, `hash_data`, `created_time`) VALUES ( '{$code}', '{$hash_data}', NOW());";
        try{
            $this->_db->exec($sql);
            echo "入库成功 \n";
        }catch (\PDOException $e) {
            print "Error: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function get(){
        $sql="SELECT code,hash_data FROM image_ocr";
        try{
            $rs=$this->_db->query($sql);
            $rs->setFetchMode(\PDO::FETCH_ASSOC);
            $result_arr=$rs->fetchAll();
            return $result_arr;

        }catch (\PDOException $e) {
            print "Error: " . $e->getMessage() . "<br/>";
            die();
        }
    }


}