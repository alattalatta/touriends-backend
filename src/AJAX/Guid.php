<?php
namespace Touriends\Backend\AJAX;
use Touriends\Backend\Table;
use Touriends\Backend\User;

class Guid extends Base {
    public static function init() {
        parent::registerAction('tour_Info',[__CLASS__,'tour_Info']);
        parent::registerAction('detail_Info',[__CLASS__,'detail_Info']);
        parent::registerAction('common_Info',[__CLASS__,'common_Info']);
        parent::registerAction('detail_Info_Kor',[__CLASS__,'detail_Info_Kor']);
        parent::registerAction('common_Info_Kor',[__CLASS__,'common_Info_Kor']);
    }
    /**
     *관광정보목록조회
     *content=0이면관광지/문화/축제전체조회(서울강남구기본값)
     *content=1이면관광지조회(서울강남구기본값)
     *content=2이면문화전체조회(서울강남구기본값)
     *content=3이면축제전체조회(서울강남구기본값)
     */
    public static function tour_Info() {

        $content=$_POST['content'];
        $area=$_POST['area'];
        $data1=null;
        $data2=null;
        if($content==0){
            $lan="EngService";
            $data=self::defaultInfo(76,$area,$lan);
            $data1=self::defaultInfo(78,$area,$lan);
            $data2=self::defaultInfo(85,$area,$lan);
        }elseif($content==76||$content==78||$content==85){
            $lan="EngService";
            $data=self::defaultInfo($content,$area,$lan);
        }elseif($content==1){
            $lan="KorService";
            $data=self::defaultInfo(12,$area,$lan);
            $data1=self::defaultInfo(14,$area,$lan);
            $data2=self::defaultInfo(15,$area,$lan);
        }
        elseif($content==12||$content==14||$content==15){
            $lan="KorService";
            $data=self::defaultInfo($content,$area,$lan);
        }

        die(json_encode([
            'success'=>true,
            'data'=>$data,
            'data1'=>$data1,
            'data2'=>$data2
        ]));

    }
    /**
     *지역기반외국어관광정보목록조회
     */
    public static function defaultInfo(int$content,int$area,string$lan){
        $key="ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url="http://api.visitkorea.or.kr/openapi/service/rest/";
        $url.=$lan;
        $url.="/areaBasedList";
        $url.="?ServiceKey=".$key;
        $url.="&cat1=A02";
        $url.="&contentTypeId=".$content;
        $url.="&areaCode=1";
        $url.="&sigunguCode=".$area;
        $url.="&cat2=";
        $url.="&cat3=";
        $url.="&pageNo=1";
        $url.="&numOfRows=10";
        $url.="&MobileApp=AppTest";
        $url.="&MobileOS=ETC";
        $url.="&arrange=A";
        $response=file_get_contents($url);
        $xml=simplexml_load_string($response) or die("Error:Cannotcreateobject");
        $data=$xml->body->items;

        return(array)$data;
    }
    /**
     *영문소개정보
     */
    public static function detail_Info(){

        $contentId=$_POST['contentId'];//"1939670"//621155
        $content=$_POST['content'];//76:test//78
        $key="ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url="http://api.visitkorea.or.kr/openapi/service/rest/EngService/detailIntro";
        $url.="?ServiceKey=".$key;
        $url.="&contentTypeId=".$content;
        $url.="&contentId=".$contentId;
        $url.="&numOfRows=10";
        $url.="&pageSize=10";
        $url.="&pageNo=1";
        $url.="&MobileApp=AppTest";
        $url.="&MobileOS=ETC";
        $url.="&introYN=Y";
        $response=file_get_contents($url);
        $xml=simplexml_load_string($response) or die("Error:Cannotcreateobject");
        $detail=$xml->body->items;

        die(json_encode([
            'success'=>true,
            'detail'=>$detail
        ]));
    }
    /**
     *국문소개정보
     */
    public static function detail_Info_Kor(){

        $contentId=$_POST['contentId'];//1118680:test
        $content=$_POST['content'];//15:test
        $key="ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url="http://api.visitkorea.or.kr/openapi/service/rest/KorService/detailIntro";
        $url.="?ServiceKey=".$key;
        $url.="&contentTypeId=".$content;
        $url.="&contentId=".$contentId;
        $url.="&numOfRows=10";
        $url.="&pageSize=10";
        $url.="&pageNo=1";
        $url.="&startPage=1";
        $url.="&MobileApp=AppTest";
        $url.="&MobileOS=ETC";
        $url.="&introYN=Y";

        $response=file_get_contents($url);
        $xml=simplexml_load_string($response) or die("Error:Cannotcreateobject");
        $detail=$xml->body->items;

        die(json_encode([
            'success'=>true,
            'detail'=>$detail
        ]));
    }
    /**
     *영문공통정보
     */
    public static function common_Info(){

        $contentId=$_POST['contentId'];//1891502:test

        $key="ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url="http://api.visitkorea.or.kr/openapi/service/rest/EngService/detailCommon";
        $url.="?serviceKey=".$key;
        $url.="&contentId=".$contentId;
        $url.="&pageSize=10";
        $url.="&pageNo=1";
        $url.="&numOfRows=10";
        $url.="&MobileApp=AppTest";
        $url.="&MobileOS=ETC";
        $url.="&arrange=A";
        $url.="&defaultYN=Y";
        $url.="&firstImageYN=Y";
        $url.="&areacodeYN=Y";
        $url.="&addrinfoYN=Y";
        $url.="&overviewYN=Y";

        $response=file_get_contents($url);
        $xml=simplexml_load_string($response) or die("Error:Cannotcreateobject");
        $common=$xml->body->items;

        die(json_encode([
            'success'=>true,
            'detail'=>$common
        ]));
    }
/**
 *국문공통정보
 */
    public static function common_Info_Kor(){

        $contentId=$_POST['contentId'];//126508:test

        $key="ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D";
        $url="http://api.visitkorea.or.kr/openapi/service/rest/KorService/detailCommon";
        $url.="?ServiceKey=".$key;
        $url.="&contentId=".$contentId;
        $url.="&pageNo=1";
        $url.="&numOfRows=10";
        $url.="&MobileApp=AppTest";
        $url.="&MobileOS=ETC";
        $url.="&arrange=A";
        $url.="&defaultYN=Y";
        $url.="&firstImageYN=Y";
        $url.="&areacodeYN=Y";
        $url.="&addrinfoYN=Y";
        $url.="&overviewYN=Y";

        $response=file_get_contents($url);
        $xml=simplexml_load_string($response) or die("Error:Cannotcreateobject");
        $common=$xml->body->items;

        die(json_encode([
            'success'=>true,
            'detail'=>$common
        ]));
    }
}
