<?php
namespace Touriends\Backend\AJAX;
class Member extends Base {
    public static function init() {
        parent::registerAction('login', [__CLASS__, 'login']);
        parent::registerAction('register', [__CLASS__, 'register']);
        parent::registerAction('logout', [__CLASS__, 'logout']);
    }
    public static function login() {
        $login = $_POST['login'];
        $pwd   = $_POST['pwd'];
        // 로그인 된 상태면 로그아웃 우선
        if (is_user_logged_in()) {
            wp_logout();
        }
        $user = wp_signon([
            'user_login'    => $login,
            'user_pass' => $pwd,
            'remember'      => true
        ], false);
        // 로그인 실패 = \WP_Error
        $success = is_wp_error($user) ? false : true;
        if (! $success) {
            die(json_encode([
                'success' => false,
                'message' => '로그인 실패!'
            ]));
        }
        die(json_encode([
            'success' => $success,
            'uid'     => $user->ID
        ]));
    }
    public static function register() {
        $login = $_POST['login'];
        $pwd   = $_POST['pwd'];
        $email   = $_POST['login'];
        $website   = $_POST['website'];
        // 기존 유저와 겹칠 경우
        if (get_user_by('login', $login)) {
            die(json_encode([
                'success' => false
            ]));
        }
        $userdata = array(
            'user_login'  =>  $login,
            'user_pass'   =>  $pwd,  // When creating an user, `user_pass` is expected.
            'user_email'  =>  $login,  // When creating an user, `user_pass` is expected.
            'user_url'    =>  $website
        );
        $user_id = wp_insert_user( $userdata ) ;

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
