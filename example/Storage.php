<?php

namespace Mohuishou\ImageOCR\Example;
use Medoo\Medoo;

class Storage{

    private static $_instance = null;

    private $_database;

    private function __construct() {
        $this->_database = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => __DIR__ . '/db/db.db'
        ]);
    }

    private function clone(){

    }

    public static function getInstance(){
        if(self::$_instance == null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function add($code,$hash){
        $this->_database->insert("ocr",[
            "code" => $code,
            "hash" => implode("",$hash)
        ]);
    }

    public function get($code = null){
        $arr = null && $code && $arr = ["code" => $code];
        return $this->_database->select("ocr",["hash","code"]);
    }
    
}