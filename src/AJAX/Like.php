<?php
namespace Touriends\Backend\AJAX;
use Touriends\Backend\User;
class Like extends Base
{
    public static function init()
    {
        parent::registerAction('bookMark', [__CLASS__, 'bookMark']);
        parent::registerAction('getBookMark', [__CLASS__, 'getBookMark']);
    }
    /*
      like 좋아요 기능 구현 -> 즐겨찾기로 생각
    */
    public static function bookMark () {
        $like = $_POST['like']; //like변수에 like를 누른 user의 아이디가 들어오겠지?
        $user_id = User\Utility::getCurrentUser()->ID; //현재 user_id
        add_user_meta($user_id, 'user_like', $like ); //db에 추가한다.
        die(json_encode([
          'success'  => true
        ]));
    }
    public static function getBookMark () {
        $user_id = User\Utility::getCurrentUser()->ID;
        $ret_like = get_user_meta($user_id,'user_like'); //현재 user_id에 like 목록을 받아온다.
        die(json_encode([
          'success'  => true,
          'like' => $ret_like
        ]));
    }
}
