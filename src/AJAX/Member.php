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
        $res = self::signIn($login, $pwd);
        die(json_encode($res));
    }
    public static function register() {
        $login = $_POST['login'];
        $pwd   = $_POST['pwd'];
        $email   = $_POST['email'];
        $website   = $_POST['website'];

        // 기존 유저와 겹칠 경우
        if (get_user_by('login', $login)) {
            die(json_encode([
                'success' => false,
                'error' => 'login_duplicate'
            ]));
        }

        $args = [
            'user_login'  =>  $login,
            'user_pass'   =>  $pwd,
            'user_email'  =>  $email,
            'user_url'    =>  $website
        ];
        $user_id = wp_insert_user($args);
        update_user_meta($user_id, 'user_birth', $_POST['birth']);
        update_user_meta($user_id, 'user_nation', $_POST['nation']);
        update_user_meta($user_id, 'user_gender', $_POST['gender']);
        // 이미지 업로드는 워드프레스 기본 업로더를 사용함
        // 해상도도 알아서 나눠준다고?
        $attachment_id = media_handle_upload('image', 0);
        if (isset($_FILES['image']) && is_wp_error($attachment_id)) {
            // 실패
            die(json_encode([
                'success' => false,
                'error' => 'upload_failed',
                'message' => $attachment_id->get_error_message()
            ]));
        }
        else {
            // 성공 => 외래키로 사진과 연결
            update_user_meta($user_id, 'image', $attachment_id);
        }

        $res = self::signIn($login, $pwd);
        die(json_encode($res));
    }
    public static function logout() {
        wp_logout();
        die(json_encode([
            'success' => true
        ]));
    }
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

    private static function signIn(string $login, string $pwd): array {
        // 로그인 된 상태면 로그아웃 우선
        if (is_user_logged_in()) {
            wp_logout();
        }
        $user = wp_signon([
            'user_login'    => $login,
            'user_password' => $pwd,
            'remember'      => true
        ], false);
        // 로그인 실패 = \WP_Error
        $success = is_wp_error($user) ? false : true;

        if (! $success) {
            return [
                'success' => false,
                'message' => 'login_failed'
            ];
        }
        return [
            'success' => true,
            'uid'     => $user->ID
        ];
    }
}
