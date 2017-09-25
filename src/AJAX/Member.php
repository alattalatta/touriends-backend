<?php

namespace Touriends\Backend\AJAX;

class Member extends Base {
	public static function init() {
		parent::registerAction('login', [__CLASS__, 'login']);
		parent::registerAction('register', [__CLASS__, 'register']);
		parent::registerAction('logout', [__CLASS__, 'logout']);
		parent::registerAction('set_intro', [__CLASS__, 'setIntro']);
		parent::registerAction('get_user_image', [__CLASS__, 'getUserImage']);
	}

	/**
	 * 로그인
	 */
	public static function login() {
		$login = $_POST['login'];
		$pwd   = $_POST['pwd'];
		die(json_encode(self::signIn($login, $pwd)));
	}

	/**
	 * 회원등록
	 */
	public static function register() {
		$login   = $_POST['login'];
		$name    = $_POST['name'];
		$pwd     = $_POST['pwd'];
		$email   = $_POST['email'];
		$website = $_POST['website'];
		// 기존 유저와 겹칠 경우
		if (get_user_by('login', $login)) {
			die(json_encode([
				'success' => false,
				'error'   => 'login_duplicate'
			]));
		}
		$userdata = [
			'user_login'   => $login,
			'user_pass'    => $pwd,
			'display_name' => $name,
			'user_email'   => $email,
			'user_url'     => $website
		];
		$user_id  = wp_insert_user($userdata);
		// 이미지 업데이트 (프사업로드)
		// 이미지 업로드는 워드프레스 기본 업로더를 사용함
		// 해상도도 알아서 나눠준다고?
		$attachment_id = media_handle_upload('image', 0);
		if (isset($_FILES['image']) && is_wp_error($attachment_id)) {
			die(json_encode([
				'success' => false,
				'error'   => 'upload_failed', // 프론트에서 에러 핸들링 할 수 있도록 키워드로 넘겨줌
				'message' => $attachment_id->get_error_message()
			]));
		} else {
			// 유저 메타에 외래키로 사진 ID 넣기
			update_user_meta($user_id, 'user_image', $attachment_id);
		}
		update_user_meta($user_id, 'user_birth', $_POST['birth']);
		update_user_meta($user_id, 'user_nation', $_POST['nation']);
		update_user_meta($user_id, 'user_gender', $_POST['gender']);

		// 가입 후 자동 로그인
		die(json_encode(self::signIn($login, $pwd)));
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
		$attachment_id = get_user_meta($user_id, 'user_image', true);
		if ($attachment_id) {
			wp_delete_attachment($attachment_id, true);
		}
		wp_delete_user($user_id);
		die(json_encode([
			'success' => true
		]));
	}

    /**
     * 자기소개
     */
    public static function setIntro() {
        $intro = $_POST['intro'];
        $uid = get_current_user_id();
        $res = update_user_meta($uid, 'intro', $intro);
        die(json_encode([
            'success' => $res
        ]));
    }

    /**
     * 프사 반환
     */
    public static function getUserImage() {
        // attachment id
        $aid = get_user_meta(get_current_user_id(), 'user_image', true);
        if ($aid === '') {
            die(json_encode([
                'success' => false
            ]));
        }
        $attachment_url = wp_get_attachment_image_url($aid);
        die(json_encode([
            'success' => true,
            'url' => $attachment_url
        ]));
    }

    /**
     * 로그인/가입 공용 Sign in 메소드
     * @param string $login ID
     * @param string $pwd Password
     *
     * @return array
     */
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
		if (is_wp_error($user)) {
			return [
				'success' => false,
				'error'   => 'login_failed',
				'message' => $user->get_error_message()
			];
		}

		return [
			'success' => true,
			'uid'     => $user->ID
		];
	}
}
