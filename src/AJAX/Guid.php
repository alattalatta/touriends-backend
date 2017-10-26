<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\Table;
use Touriends\Backend\User;

class Guid extends Base {
    public static function init() {
        parent::registerAction('tour_Info', [__CLASS__, 'tour_Info']);
        parent::registerAction('detail_Info', [__CLASS__, 'detail_Info']);
        parent::registerAction('common_Info', [__CLASS__, 'common_Info']);
    }
    /**
     * 관광정보 목록 조회
     * content = 0 이면 관광지/문화/축제 전체 조회(서울 강남구 기본값)
     * content = 1 이면 관광지 조회(서울 강남구 기본값)
     * content = 2 이면 문화 전체 조회(서울 강남구 기본값)
     * content = 3 이면 축제 전체 조회(서울 강남구 기본값)
     */
    public static function tourInfo() {

        $content = $_POST['content'];
        $area = $_POST['area'];
        $data1 = null;
        $data2 = null;
        if ($content == 0) {
            $data = self::defaultInfo(76, $area);
            $data1 = self::defaultInfo(78, $area);
            $data2 = self::defaultInfo(85, $area);
        } else if ($content == 1) {
            $data = self::defaultInfo(76, $area);
        } else if ($content == 2) {
            $data = self::defaultInfo(78, $area);
        } else if ($content == 3) {
            $data = self::defaultInfo(85, $area);
        }

        die(json_encode([
            'success' => true,
            'data' => $data,
            'data1' => $data1,
            'data2' => $data2
        ]));

    }

    public static function defaultInfo(int $content, int $area) {

        $key = "ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url = "http://api.visitkorea.or.kr/openapi/service/rest/EngService/areaBasedList";
        $url .= "?ServiceKey=" . $key;
        $url .= "&cat1=A02";
        $url .= "&contentTypeId=" . $content;
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
    /**
     * 소개 정보
     */
    public static function detail_Info() {

        $contentId = $_POST['contentId']; // 1118680 : test

        $key = "ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url = "http://api.visitkorea.or.kr/openapi/service/rest/EngService/areaBasedList";
        $url .= "?ServiceKey=" . $key;
        // $url .= "&contentTypeId=". $content;
        $url .= "&contentId=" . $contentId;
        $url .= "&pageNo=1";
        $url .= "&numOfRows=1";
        $url .= "&MobileApp=AppTest";
        $url .= "&MobileOS=ETC";
        $url .= "&arrange=A";
        $response = file_get_contents($url);
        $xml = simplexml_load_string($response) or die("Error: Cannot create object");
        $detail = $xml->body->items;

        die(json_encode([
            'success' => true,
            'detail' => $detail
        ]));
    }
    /**
     * 공통 정보
     */
    public static function common_Info() {

        $contentId = $_POST['contentId']; // 1118680 : test

        $key = "ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url = "http://api.visitkorea.or.kr/openapi/service/rest/EngService/areaBasedList";
        $url .= "?ServiceKey=" . $key;
        $url .= "&contentId=" . $contentId;
        $url .= "&pageNo=1";
        $url .= "&numOfRows=1";
        $url .= "&MobileApp=AppTest";
        $url .= "&MobileOS=ETC";
        $url .= "&arrange=A";
        $url .= "&firstImageYN=Y";
        $url .= "&areacodeYN=Y";
        $url .= "&addrinfoYN=Y";
        $url .= "&overviewYN=Y";

        $response = file_get_contents($url);
        $xml = simplexml_load_string($response) or die("Error: Cannot create object");
        $common = $xml->body->items;

        die(json_encode([
            'success' => true,
            'detail' => $common
        ]));
    }

}
