<?php

namespace Touriends\Backend\AJAX;

class Demo extends Base {
    public static function init() {
        parent::registerAction('demo-login', [__CLASS__, 'login']);
        parent::registerAction('demo-register', [__CLASS__, 'register']);
        parent::registerAction('demo-logout', [__CLASS__, 'logout']);
    }

    /**
     * 사이트 로그인
     * @uses $_POST: login, pwd
     * @return mixed ['uid' => integer]
     */
    public static function login() {
        // 민감한 정보는 POST... ㅎㅎ...;
        $login = $_POST['login'];
        $pwd   = $_POST['pwd'];

        // 로그인 된 상태면 로그아웃
        if (is_user_logged_in()) {
            wp_logout();
        }
        $user = wp_signon([
            'user_login'    => $login,
            'user_password' => $pwd,
            'remember'      => true
        ], false);

        // 로그인 실패 = \WP_Error
        if (is_wp_error($user)) {
            die(json_encode([
                'success' => false,
                'error' => 'login_failed', // 프론트에서 에러 핸들링 할 수 있도록 키워드로 넘겨줌
                'message' => $user->get_error_message()
            ]));
        }

        die(json_encode([
            'success' => true,
            'uid'     => $user->ID
        ]));
    }

    /**
     * 회원가입
     * @uses $_POST: login, pwd
     * @uses $_FILES: image
     */
    public static function register() {
        // $_FILES['image']는 media_handle_upload에서 알아서 뽑아서 사용
        $login = $_POST['login'];
        $pwd   = $_POST['pwd'];

        // 기존 유저와 겹칠 경우
        if (get_user_by('login', $login)) {
            die(json_encode([
                'success' => false,
                'message' => 'login_duplicate'
            ]));
        }

        $user_id = wp_insert_user([
            'user_pass'  => $pwd,
            'user_login' => $login
        ]);

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