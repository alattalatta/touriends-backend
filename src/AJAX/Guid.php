<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\Table;
use Touriends\Backend\User;

class Guid extends Base {
    public static function init() {
        parent::registerAction('defaultInfo', [__CLASS__, 'defaultInfo']);
    }
    public static function defaultInfo() {

        $ch = curl_init();
        $url = 'http://api.visitkorea.or.kr/openapi/service/rest/KorService/areaBasedList'; /*URL*/
        $queryParams = '?' . urlencode('ServiceKey') . '=ZdRsNK%2BUuytOnBip1bcGNL1P5pvkIQQQ3cJfcNlSgGEx%2BI6A8AftgREL7JZFmmi%2FyQZS3XY4irLiNc5BS%2FXIKA%3D%3D';
        $queryParams .= '&' . urlencode('ServiceKey') . '=' . urlencode('SERVICE_KEY'); /*서비스인증*/
        $queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1'); /*페이지번호*/
        $queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('2'); /*한페이지결과수*/
        $queryParams .= '&' . urlencode('MobileApp') . '=' . urlencode('AppTest'); /*서비스명=어플명*/
        $queryParams .= '&' . urlencode('MobileOS') . '=' . urlencode('ETC'); /*AND(안드로이드),IOS(아이폰),WIN(원도우폰),ETC*/
        $queryParams .= '&' . urlencode('arrange') . '=' . urlencode('A') ; /*(A=제목순, B=조회순, C=수정일순, D=생성일순) , 대표이미지 정렬 추가(D=제목순, P=조회순, Q=수정일순, R=생성일순)*/
        $queryParams .= '&' . urlencode('cat1') . '=' . urlencode('A02'); /*대분류*/
        $queryParams .= '&' . urlencode('contentTypeId') . '=' . urlencode('15'); /*관광타입(관광지, 숙박등) ID*/
        $queryParams .= '&' . urlencode('areaCode') . '=' . urlencode('1'); /*지역코드*/
        $queryParams .= '&' . urlencode('sigunguCode') . '=' . urlencode('1'); /*시군구코드*/
        $queryParams .= '&' . urlencode('cat2') . '=' . urlencode(''); /*중분류*/
        $queryParams .= '&' . urlencode('cat3') . '=' . urlencode(''); /*소분류*/
        $queryParams .= '&' . urlencode('listYN') . '=' . urlencode('Y'); /*목록구분*/
        $queryParams .= '&' . urlencode('_type'). '=' .urlencode('json');/*json*/

        curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        curl_close($ch);

       // $testtest[] = var_dump($response);
      //  $response = json_decode($response, true);

        die(json_encode([
            'success'  => true,
            'title' => $response
        ]));
    }
}
