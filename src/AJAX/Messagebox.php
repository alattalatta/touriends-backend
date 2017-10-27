<?php
namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Messagebox extends Base{
  public static function init(){
      parent::registerAction('showMessage',[__CLASS__,'showMessage']);
  }
  public static function showMessage(){//메시지목록을보여주는함수
    global$wpdb;
    $user_id=User\Utility::getCurrentUser()->ID;//현재user_id
    $table_name=$wpdb->prefix.'message';
    $statement=<<<SQL
    SELECT ID FROM (SELECT DISTINCT ID,mid,test FROM (SELECT re_id AS ID, mid, 0 as 'test' FROM (SELECT * FROM wp_message WHERE se_id = $user_id ORDER BY mid desc)AS TEMP1 GROUP BY ID UNION SELECT se_id AS ID, mid, 1 as 'test' FROM (SELECT * FROM wp_message WHERE re_id = $user_id ORDER BY mid desc)AS TEMP2 GROUP BY ID)AS TEMP3 GROUP BY ID ORDER BY mid desc)AS TEMP4 WHERE test=0;
SQL;
    $messages_re=$wpdb->get_col($statement); //user_id가 메세지를 받은 사람일 때 상대방 아이디
    $statement=<<<SQL
    SELECT ID FROM (SELECT DISTINCT ID,mid,test FROM (SELECT re_id AS ID, mid, 0 as 'test' FROM (SELECT * FROM wp_message WHERE se_id = $user_id ORDER BY mid desc)AS TEMP1 GROUP BY ID UNION SELECT se_id AS ID, mid, 1 as 'test' FROM (SELECT * FROM wp_message WHERE re_id = $user_id ORDER BY mid desc)AS TEMP2 GROUP BY ID)AS TEMP3 GROUP BY ID ORDER BY mid desc)AS TEMP4 WHERE test=1;
SQL;
    $messages_se=$wpdb->get_col($statement); //user_id가 메세지를 보낸 사람일 때 상대방 아이디
    $box=[];
    foreach($messages_re as $msg){
      $other = $msg;
      $statement=<<<SQL
      SELECT MAX(mid) from $table_name WHERE re_id = $user_id AND se_id = $other;
SQL;
      $mid = $wpdb->get_col($statement);//상대방이 나에게 보낸 최신의 mid
      $statement=<<<SQL
      SELECT read_ck from $table_name WHERE mid = $mid[0];
SQL;
      $read_ck = $wpdb->get_col($statement);//읽었는지 안읽었는지 확인 하기 위한 변수 0 이면 읽지 않은 것 !
      $box[]=[
        'other'   =>  $other,
        'mid'     =>  $mid[0],
        'new_ck'  =>  $read_ck[0]
      ];
    }
    foreach($messages_se as $msg){
      $other = $msg;
      $statement=<<<SQL
      SELECT MAX(mid) from $table_name WHERE se_id = $user_id AND re_id = $other;
SQL;
      $mid = $wpdb->get_col($statement);//상대방이나에게보낸메세지
      $box[]=[
        'other'   =>  $other,
        'mid'     =>  $mid[0],
        'new_ck'  =>  "1" //안읽은 메시지만 신경쓰면 되니깐
      ];
    }
    die(json_encode([
      'success'    =>  true,
      'message_re' =>  $messages_re,
      'message_se' =>  $messages_se,
      'box'        =>  $box
    ]));
  }
}
