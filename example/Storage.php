<?php

namespace Mohuishou\ImageOCR\Example;
use Medoo;

class Storage{

    private static $_instance = null;

    private $_database;

    const ROOT = __DIR__;

    private function __construct() {
        $this->_database = new medoo([
            'database_type' => 'sqlite',
            'database_file' => ROOT . '/db/database.db'
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
            "hash" => $hash,
            "created_at" => time(),
            "updated_at" => time()
        ]);
    }

    public function get($code){
        return $this->_database->select("ocr",[
            "code" => $code
        ]);
    }
    
}