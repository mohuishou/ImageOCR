<?php
/**
 * Created by PhpStorm.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2017/3/19
 * Time: 15:39
 */
$hash_data=[
    [1,0,2,3],
    [4,5,6,0],
    [7,8,9,0]
];
$data=[];
for ($i=0;$i<count($hash_data[0]);$i++){
    $column=array_column($hash_data,$i);
    if(implode("",$column)!=0){
        $data[]=$column;
    }
}
print_r($data);

$res_data=[];
for ($i=0;$i<count($data);$i++){
    $column=array_column($data,$i);
    $res_data[]=$column;
}
print_r($res_data);