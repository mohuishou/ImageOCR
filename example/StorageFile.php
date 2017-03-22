<?php
/**
 * Created by mohuishou<1@lailin.xyz>.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2016/5/2 0002
 * Time: 12:51
 */
namespace Mohuishou\ImageOCR\Example;
class StorageFile{
    /**
     * 储存文件目录
     */
    const FILE_DIR=__DIR__."/db/";

    /**
     * 储存文件路径
     */
    const FILE_PATH=self::FILE_DIR."db.json";

    /**
     *  数据保存相关
     */
    protected $_db;

    public function __construct()
    {
        $this->connect();
    }

    /**
     * 初始化连接
     * @author mohuishou<1@lailin.xyz>
     * @throws \Exception
     */
    public function connect(){

        //检查是否拥有存在文件，不存在则新建
        if (!file_exists(self::FILE_PATH)) {
            if(!@file_put_contents(self::FILE_PATH,json_encode([]))){
                throw new \Exception("初始化错误，无法写入文件".self::FILE_PATH);
            }
        }
        $this->_db=json_decode(file_get_contents(self::FILE_PATH),true);
    }

    /**
     * 添加数据
     * @author mohuishou<1@lailin.xyz>
     * @param $code
     * @param $hash_data
     */
    public function add($code,$hash_data){
        $this->_db[$code][]=$hash_data;
        $this->save();
    }

    /**
     * 保存数据
     * @author mohuishou<1@lailin.xyz>
     * @throws \Exception
     */
    public function save(){
        if(!@file_put_contents(self::FILE_PATH,json_encode($this->_db))){
            throw new \Exception("初始化错误，无法写入文件".self::FILE_PATH);
        }
    }

    /**
     * 获取所有数据
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    public function get(){
        return $this->_db;
    }


}