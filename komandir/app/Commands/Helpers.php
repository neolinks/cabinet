<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 10/18/15
 * Time: 12:34 AM
 */

namespace App\Commands;


class Helpers {
    public  static function merge_two_arrays_of_object($array1, $array2, $key){
        $data = [];
        $arrayAB = array_merge($array1,$array2);
        foreach ($arrayAB as $value) {
            if(isset($value->$key)){
                $id = $value->$key;
                if (!isset($data[$id])) {
                    $data[$id] = array();
                }
                $data[$id] = array_merge($data[$id],$value);
            }else{

            }
        }
        return $data;
    }
    static function merge_two_arrays($array1,$array2,$key) {
        $data = [];
        $arrayAB = array_merge($array1,$array2);
        foreach ($arrayAB as $value) {
            if(isset($value[$key])){
                $id = $value[$key];
                if (!isset($data[$id])) {
                    $data[$id] = array();
                }
                $data[$id] = array_merge($data[$id],$value);
            }else{

            }
        }
        return $data;
    }
    static function merge_two_arrays_with_remove($array1,$array2,$key) {
        $data = [];
        $arrayAB = array_merge($array1,$array2);
        foreach ($arrayAB as $value) {
            if(isset($value[$key])){
                $id = $value[$key];
                if (!isset($data[$id])) {
                    $data[$id] = array();
                }
                $data[$id] = array_merge($data[$id],$value);
            }
        }
        return $data;
    }
     public static function get_address_by_coords($lat, $lon){
        $params = array(
            'geocode'=> $lon.','.$lat,                   // адрес
            'format'  => 'json',                          // формат ответа
            'lang' =>'ru_RU',
            'key'=>'ADRHxFUBAAAApPvlDgMAaP2ynJK84HVo7KyxjJ5k0qHfpPMAAAAAAAAAAADKJqMM6wuBDt7bLnuyY5PVoo3CWg=='
        );
        $response = json_decode(file_get_contents('https://geocode-maps.yandex.ru/1.x/?' . http_build_query($params, '', '&')));

        if ($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0)
        {
            if($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0){
            return $response->response->GeoObjectCollection->featureMember[0]->GeoObject->metaDataProperty->GeocoderMetaData->text;
            }else{
                return false;
            }
        }else
        {
            return false;
        }
    }
    public static function distance($lat1, $long1, $lat2, $long2)
    {
        //радиус Земли
        $R = 6372795;
        //перевод коордитат в радианы
        $lat1 *= pi() / 180;
        $lat2 *= pi() / 180;
        $long1 *= pi() / 180;
        $long2 *= pi() / 180;
        //вычисление косинусов и синусов широт и разницы долгот
        $cl1 = cos($lat1);
        $cl2 = cos($lat2);
        $sl1 = sin($lat1);
        $sl2 = sin($lat2);
        $delta = $long2 - $long1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);
        //вычисления длины большого круга
        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;
        $ad = atan2($y, $x);
        $dist = $ad * $R;
        $dist = round($dist);
        return $dist;
    }
} 