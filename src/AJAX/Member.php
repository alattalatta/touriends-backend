<?php
namespace Touriends\Backend\AJAX;
class Member extends Base {
    public static function init() {
        parent::registerAction('login', [__CLASS__, 'login']);
        parent::registerAction('register', [__CLASS__, 'register']);
        parent::registerAction('logout', [__CLASS__, 'logout']);
        parent::registerAction('intro', [__CLASS__, 'intro']);
    }
    
    /**
    * 로그인
    */
    public static function login() {
        $login = $_POST['login'];
        $pwd   = $_POST['pwd'];
        // 로그인 된 상태면 로그아웃 우선
        if (is_user_logged_in()) {
            wp_logout();
        }
        $user = wp_signon([
            'user_login'    => $login,
            'user_pass'     => $pwd,
            'remember'      => true
        ], false);
        // 로그인 실패 = \WP_Error
        if (is_wp_error($user)) {
            die(json_encode([
                'success' => false,
                'error' => 'login_failed', 
                'message' => $user->get_error_message()
            ]));
        }
        die(json_encode([
            'success' => $success,
            'uid'     => $user->ID
        ]));
    }
    /**
    * 회원등록
    */
    public static function register() {
        $name    = $_POST['name'];
        $pwd      = $_POST['pwd'];
        $email    = $_POST['email'];
        $website  = $_POST['website'];
        // 기존 유저와 겹칠 경우
        if (get_user_by('login', $login)) {
            die(json_encode([
                'success' => false
            ]));
        }
        $userdata = array(
            'user_login'  =>  $email,
            'user_pass'   =>  $pwd,  // When creating an user, `user_pass` is expected.
            'user_email'  =>  $email,  // When creating an user, `user_pass` is expected.
            'user_url'    =>  $website
        );
        $user_id = wp_insert_user( $userdata ) ;

        // 이미지 업데이트 (프사업로드)
        // 이미지 업로드는 워드프레스 기본 업로더를 사용함
        // 해상도도 알아서 나눠준다고?
        $attachment_id = media_handle_upload('image', 0);
        if (isset($_FILES['image']) && is_wp_error($attachment_id)) {
            die(json_encode([
                'success' => false,
                'error' => 'upload_failed', // 프론트에서 에러 핸들링 할 수 있도록 키워드로 넘겨줌
                'message' => $attachment_id->get_error_message()
            ]));
        }
        else {
            // 유저 메타에 외래키로 사진 ID 넣기
            update_user_meta($user_id, 'image', $attachment_id);
        }

        $meta_value = $_POST['birth'];
        update_user_meta($user_id, 'user_birth', $meta_value);
        $meta_value = $_POST['image'];
        update_user_meta($user_id, 'user_image', $meta_value);
        $meta_value = $_POST['nation'];
        update_user_meta($user_id, 'user_nation', $meta_value);
        $meta_value = $_POST['gender'];
        update_user_meta($user_id, 'user_gender', $meta_value);
        die(json_encode([
            'success' => true
        ]));
    }
    /**
    * 자기소개
    */
    public static function intro() {
       $intro = $_POST['intro'];
    }
    /**
    * 로그아웃
    */
    public static function logout() {
        wp_logout();
        die(json_encode([
            'success' => true
        ]));
    }
     /**
     * 탈퇴
     */
    public static function disconnect() {
        if (! is_user_logged_in()) {
            die(json_encode([
                'success' => false
            ]));
        }
        $user_id = get_current_user_id();
        wp_logout();
        $attachment_id = get_user_meta($user_id, 'image', true);
        if ($attachment_id) {
            wp_delete_attachment($attachment_id, true);
        }
        wp_delete_user($user_id);
        die(json_encode([
            'success' => true
        ]));
    }
}
