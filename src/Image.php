<?php
/**
* Created by mohuishou<1@lailin.xyz>.
* User: mohuishou<1@lailin.xyz>
* Date: 2016/5/1 0001
* Time: 20:29
*/

namespace Mohuishou\ImageOCR;

class Image
{

    /**
     * 最大灰度值
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    public $max_grey=null;

    /**
     * 最小灰度值
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    public $min_grey=null;

    /**
     * 标准化图像的宽度
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    public $standard_width=10;

    /**
     * 标准化图像的高度
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    public $standard_height=10;
    

    //图像字符串的个数
    const CHAR_NUM=4;
    
    //图像的宽度与高度信息
    private $_image_w;
    private $_image_h;

    /**
    * 输入图像的句柄
    * @var resource
    */
    public $in_img;
    
    /**
    * @var array $_hash_data 二值化的数组
    */
    private $_hash_data;
    
    
    public function __construct($imgPath) {
        
        //判断图像的类型
        $res = exif_imagetype($imgPath);
        
        switch($res) {
            case 1:
                $this->in_img = imagecreatefromgif($imgPath);
                break;
            case 2:
                $this->in_img = imagecreatefromjpeg($imgPath);
                break;
            case 3:
                $this->in_img = imagecreatefrompng($imgPath);
                break;
            case 6:
                $this->in_img = imagecreatefromwbmp($imgPath);
                break;
            default:
                throw new \Exception("不支持的图片格式!");
                break;
        }
        
        //获取图像的大小信息
        $this->_image_w=getimagesize($imgPath)[0];
        $this->_image_h=getimagesize($imgPath)[1];
    }

    /**
     * 二值化，排除背景色，雪花等干扰项
     * @param array $grey_data 灰度图像
     * @param int $max 最大阈值
     * @param int $min 最小阈值
     * @throws \Exception
     * @author mohuishou<1@lailin.xyz>
     * @return array $data 二值化值
     */
    public function imageHash($grey_data,$max,$min){

        if ($grey_data==null)
            throw new \Exception("请先将图片灰度化！",1);

        $max==null && $max=$this->max_grey;
        if ($max==null)
            throw new \Exception("请输入最大灰度值！",1);

        $min==null && $min=$this->min_grey;
        if ($min==null)
            throw new \Exception("请输入最小灰度值！",1);

        $data=[];
        for($i = 0; $i < count($grey_data); $i++) {
            for ($j = 0; $j < count($grey_data[0]);$j++) {
                $grey=$grey_data[$i][$j];
                if($grey>$min&&$grey<$max){
                    $data[$i][$j]=1;
                }else{
                    $data[$i][$j]=0;
                }
            }
        }
        $this->_hash_data=$data;
        return $data;
    }

    /**
    * 彩色图像转灰度图像
    *
    * @return array $data 包含灰度图像的二维图像数组
    * @author mohuishou<1@lailin.xyz>
    */
    public function rgb2grey(){
        $data=[];
        for($i = 0; $i < $this->_image_h; $i++) {
            for ($j = 0; $j < $this->_image_w; $j++) {
                $rgb = imagecolorat($this->in_img,$j,$i);
                $rgb_array = imagecolorsforindex($this->in_img, $rgb);
                //图片灰度化
                $data[$i][$j]=intval(($rgb_array['red']+$rgb_array['green']+$rgb_array['blue'])/3);
            }
        }
        return $data;
    }

    /**
    * 获取背景的灰度值
    *
    * @param [type] $data
    * @return int
    * @author mohuishou<1@lailin.xyz>
    */
    public function getBgGrey($data){
        $tmp=[];
        foreach($data as $v){
            $a=array_count_values($v);
            $max_a=max($a);
            $k=array_keys($a, $max_a);
            if(!empty($k)){
                if(isset($tmp[$k[0]])){
                    $tmp[$k[0]]+=$max_a;
                    
                }else{
                    $tmp[$k[0]]=$max_a;
                }
            }
        }
        return array_keys($tmp, max($tmp))[0];
    }

    /**
    * 去除孤立噪点
    * @author mohuishou<1@lailin.xyz>
    * @param $hash_data
    * @return mixed
    */
    public function removeHotSpots($hash_data){
        for($i = 0; $i < $this->_image_h; $i++) {
            for ($j = 0; $j < $this->_image_w; $j++) {
                if($hash_data[$i][$j]){
                    if($this->isHotSpots($i,$j,$hash_data)) $hash_data[$i][$j]=0;
                }
            }
        }
        return $hash_data;
    }

    /**
    * 判断是否是孤立点
    * @author mohuishou<1@lailin.xyz>
    * @param $i
    * @param $j
    * @param $hash_data
    * @return bool ture:是噪点,false:不是
    */
    public function isHotSpots($i,$j,$hash_data){
        if($i == 0 || $j == 0 || $i == ($this->_image_h - 1) || $j == ($this->_image_w - 1)) return true;
        
        
        //待检查点为中心的九个点
        $points[0]=$hash_data[$i-1][$j-1];
        $points[1]=$hash_data[$i-1][$j];
        $points[2]=$hash_data[$i-1][$j+1];
        $points[3]=$hash_data[$i][$j-1];
        $points[4]=$hash_data[$i][$j];//待检查点
        $points[5]=$hash_data[$i][$j+1];
        $points[6]=$hash_data[$i+1][$j-1];
        $points[7]=$hash_data[$i+1][$j];
        $points[8]=$hash_data[$i+1][$j+1];
        
        $count=0;
        
        foreach ($points as $v){
            if($v){
                $count++;
            }
        }
        
        return $count<4;
    }

    /**
    * 去除零行
    * @author mohuishou<1@lailin.xyz>
    * @param null $data 当为默认值null时，自动赋值为&$this->_hash_data;
    * @return array $data 去掉零行之后的二值化数组
    */
    public function removeZero($data=null){
        $data==null && $data=&$this->_hash_data;
        foreach ($data as $k=>$v) {
            if (implode("", $v) == 0) unset($data[$k]);
        }
        return $data;
    }

    /**
    * 等宽分割
    * @author mohuishou<1@lailin.xyz>
    * @param int $n 图片的第几个字符
    * @return array $hash_data 标准化之后的二值化图像字符串
    */
    public function splitByEqualWidth($n){
        $data=[];
        $a=$this->_image_w/self::CHAR_NUM;
        for($i=$n*$a;$i<($n+1)*$a;$i++){
            $column=array_column($this->_hash_data,$i);
            if(implode("",$column)!=0){
                $data[]=$column;
            }
        }
    }


    /**
     * 标准化，返回标准化之后的二值数组
     * @param array $hash_data 尚未标准化的二值数组
     * @param int $angle 旋转角度，默认30度
     * @param int $width 标准化图像宽度
     * @param int $height 标准化图像高度
     * @author mohuishou<1@lailin.xyz>
     * @return array $standard_data 标准化之后的二值数组
     */
    public function standard($hash_data,$angle=30,$width=null,$height=null){
        //初始化标准化图像的相关设置
        $width==null && $width=$this->standard_width;
        $height==null && $height=$this->standard_height;

        //hash 转 img
        $img=ImageTool::hash2img($hash_data,2);

        //图片旋转，取最小的字符宽度
        //最小的宽度
        $min_w=999;
        $out_hash_data=[];
        $white=imagecolorallocate($img, 255, 255, 255);
        for($i=-$angle;$i<$angle;$i++){
            $tmp_img=imagerotate($img,$i,$white);
            //计算字符宽度
            $tmp_img_hash_data=$this->imgTranspose($tmp_img);
            $w=count($tmp_img_hash_data);
            if($w<$min_w) {
                $out_hash_data = $tmp_img_hash_data;
                $min_w = $w;
            }
        }

        $out_hash_data=ImageTool::hashTranspose($out_hash_data);

        //最小宽度字符的高度与宽度
        $out_img_w=count($out_hash_data[0]);
        $out_img_h=count($out_hash_data);

        //最小字符图片
        $out_img=ImageTool::hash2img($out_hash_data);

        //图像标准化，宽度和高度进行标准化
        $standard_img = imagecreatetruecolor($width, $height);
        imagecopyresized($standard_img, $out_img, 0, 0, 0, 0,$width,$height,$out_img_w,$out_img_h);


        return ImageTool::img2hash($standard_img);
    }

    /**
     * 图像去除零行、零列、转置之后的二值数组
     * @param resource $img 图像资源句柄
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    public function imgTranspose($img){
        $hash_data=ImageTool::img2hash($img);

        $hash_data=ImageTool::removeZero($hash_data);

        $hash_data=ImageTool::transposeAndRemoveZero($hash_data);

        return $hash_data;
    }

}