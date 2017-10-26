<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\Table;
use Touriends\Backend\User;

class Guid extends Base {
    public static function init() {
        parent::registerAction('tourInfo', [__CLASS__, 'tourInfo']);
//        parent::registerAction('defaultInfo', [__CLASS__, 'defaultInfo']);
    }

    public static function tourInfo() {

        $content = $_POST['content'];
        $area = $_POST['area'];
        $data1 = null;
        $data2 = null;
        if ($content == 0)
        {
            $data  = self::defaultInfo(12, $area);
            $data1 = self::defaultInfo(14, $area);
            $data2 = self::defaultInfo(15, $area);
        }

        else if($content == 1){
            $data = self::defaultInfo(12, $area);
        }

        else if($content == 2){
            $data = self::defaultInfo(14, $area);
        }

        else if($content == 3){
            $data = self::defaultInfo(15, $area);
        }

        die(json_encode([
            'success' => true,
            'data' => $data,
            'data1' => $data1,
            'data2' => $data2
        ]));

    }

    public static function defaultInfo(int $content, int $area){

        $key = "ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url = "http://api.visitkorea.or.kr/openapi/service/rest/KorService/areaBasedList";
        $url .= "?ServiceKey=".$key;
        $url .= "&cat1=A02";
        $url .= "&contentTypeId=". $content;
        $url .= "&areaCode=1";
        $url .= "&sigunguCode=" . $area;
        $url .= "&cat2=";
        $url .= "&cat3=";
        $url .= "&pageNo=1";
        $url .= "&numOfRows=10";
        $url .= "&MobileApp=AppTest";
        $url .= "&MobileOS=ETC";
        $url .= "&arrange=A";
        $response = file_get_contents($url);
        $xml = simplexml_load_string($response) or die("Error: Cannot create object");
        $data = $xml->body->items;

        return (array)$data;
    }
}
