<?php
/**
* Created by mohuishou<1@lailin.xyz>.
* User: mohuishou<1@lailin.xyz>
* Date: 2016/5/1 0001
* Time: 20:29
*/

namespace Mohuishou\ImageOCR;
use Mohuishou\ImageOCR\ImageConnect;

class Image
{
    
    //标准化的图像的宽高信息，可调
    const HASH_W = 12;
    const HASH_H = 20;
    
    //灰度图像的阈值
    const MAX_GREY=95;
    const MIN_GREY=0;

    //图像字符串的个数
    const CHAR_NUM=4;
    
    //图像的宽度与高度信息
    private $_image_w;
    private $_image_h;

    /**
    * 输入图像的句柄
    * @var resource
    */
    public $_in_img;
    
    /**
    * @var array $_hash_data 二值化的数组
    */
    private $_hash_data;
    
    
    public function __construct($imgPath) {
        
        //判断图像的类型
        $res = exif_imagetype($imgPath);
        
        switch($res) {
            case 1:
                $this->_in_img = ImageCreateFromGif($imgPath);
                break;
            case 2:
                $this->_in_img = ImageCreateFromJpeg($imgPath);
                break;
            case 3:
                $this->_in_img = ImageCreateFromPng($imgPath);
                break;
            case 6:
                $this->_in_img = ImageCreateFromWbmp($imgPath);
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
     * @author mohuishou<1@lailin.xyz>
     * @return array $data 二值化值
     */
    public function imageHash($grey_data,$max=self::MAX_GREY,$min=self::MIN_GREY){
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
                $rgb = imagecolorat($this->_in_img,$j,$i);
                $rgb_array = imagecolorsforindex($this->_in_img, $rgb);
                //图片灰度化
                $data[$i][$j]=intval((0.299*$rgb_array['red']+0.587*$rgb_array['green']+0.114*$rgb_array['blue'])/2);
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
    * 分割图片，并将其标准化
    * @author mohuishou<1@lailin.xyz>
    * @param int $n 图片的第几个字符
    * @return array $hash_data 标准化之后的二值化图像字符串
    */
    public function splitImage($n){
        $data=[];
        $a=$this->_image_w/self::CHAR_NUM;
        for($i=$n*$a;$i<($n+1)*$a;$i++){
            $column=array_column($this->_hash_data,$i);
            if(implode("",$column)!=0){
                $data[]=$column;
            }
        }
        
        $out_img_w=count($data)+4;
        $out_img_h=count($data[0])+4;
        
        $out_img = imagecreatetruecolor($out_img_w,$out_img_h);//创建一幅真彩色图像
        $bg=imagecolorallocate($out_img, 255, 255, 255);//背景色画为白色
        imagefill($out_img, 0,0, $bg);
        
        //一列一列的进行画图
        foreach ($data as $k=>$v){
            foreach ($v as $key=> $val){
                $color=255;
                if($val) $color=0;
                $c = imagecolorallocate($out_img, $color, $color, $color);
                imagesetpixel($out_img, $k+2,$key+2, $c);
            }
        }
        
        
        
        //图像标准化
        $hash_img=$this->imageStandard($out_img,$out_img_w,$out_img_h);
        
        //图像二值化
        for($i = 0; $i <self::HASH_H; $i++) {
            for ($j = 0; $j <self::HASH_H; $j++) {
                $rgb = imagecolorat($hash_img,$j,$i);
                //                echo $rgb."\n";
                if($rgb<10000000){
                    $hash_img_data[]=1;
                }else{
                    $hash_img_data[]=0;
                }
            }
        }
        return $hash_img_data;
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
    public function standard($hash_data,$angle=30,$width=self::HASH_W,$height=self::HASH_H){
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

    /**
    * 识别字符串
    * @author mohuishou<1@lailin.xyz>
    * @return array $res 识别成功的字符串数组
    */
    public function find() {
        $res = [];
        
        //从数据库中取出特征值
        $db=new StorageFile();
        $samples=$db->get();
        
        //分割字符串，并和特征值进行对比
        for($i = 0; $i < self::CHAR_NUM; $i++) {
            $hash= $this->splitImage($i);
            $res[]=$this->compare($hash,$samples);
        }
        
        return $res;
    }

    /**
    * 和特征值库进行对比
    * @author mohuishou<1@lailin.xyz>
    * @param array $hash 待识别的二值化图像字符串
    * @param array $samples 特征值库的数组
    * @return string $code 返回识别的字符
    */
    public function compare($hash,$samples) {
        
        
        $s = 0;
        foreach ($samples as $k=>$v){
            foreach ($v as $val){
                $samples_hash_data=str_split($val);
                $c = count( array_intersect_assoc ($samples_hash_data, $hash) );
                if($c>$s){
                    $s=$c;
                    $code=$k;
                }
                if($s>0.99*count($samples_hash_data)){
                    //                    echo 0.8*count($samples_hash_data);
                    return $k;
                }
            }
        }
        //        foreach($samples as $value) {
        //            $samples_hash_data=str_split($value['hash_data']);
        //            $c = count( array_intersect_assoc ($samples_hash_data, $hash) );
        //            if($c>$s){
        //                $s=$c;
        //                $code=$value['code'];
        //            }
        //            if($s>0.8*count($samples_hash_data)){
        //                echo 0.8*count($samples_hash_data);
        //                return $value['code'];
        //
        //                break;
        //            }
        //        }
        return $code;
    }
}