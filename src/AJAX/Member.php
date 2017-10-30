<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Member extends Base {
	public static function init() {
		// 로그인 관리
		parent::registerAction('login', [__CLASS__, 'login']);
		parent::registerAction('logout', [__CLASS__, 'logout']);
		parent::registerAction('disconnect', [__CLASS__, 'disconnect']);
		// 회원 가입 및 소개 GET/SET
		parent::registerAction('register', [__CLASS__, 'register']);
		parent::registerAction('get_intro', [__CLASS__, 'getIntro']); -
		parent::registerAction('set_intro', [__CLASS__, 'setIntro']);
		//이미지 받아오기 GET/SET
		parent::registerAction('get_profile_image', [__CLASS__, 'getProfileImage']);
		parent::registerAction('set_profile_image', [__CLASS__, 'setProfileImage']);
		//프로필 변경
		parent::registerAction('getEdit', [__CLASS__, 'getEdit']);
		parent::registerAction('setEdit', [__CLASS__, 'setEdit']);
	}

	/**
	 * 로그인
	 */
	public static function login() {
		$login = $_POST['login'];
		$pwd = $_POST['pwd'];
		die(json_encode(self::signIn($login, $pwd)));
	}

	/**
	 * 회원등록
	 */
	public static function register() {
		$login = $_POST['login'];
		$name = $_POST['name'];
		$pwd = $_POST['pwd'];
		$email = $_POST['email'];
		$website = $_POST['website'];
		// 기존 유저와 겹칠 경우
		if (get_user_by('login', $login)) {
			die(json_encode([
				'success' => false,
				'error'   => 'login_duplicate'
			]));
		}
		$user_args = [
			'user_login'   => $login,
			'user_pass'    => $pwd,
			'display_name' => $name,
			'user_email'   => $email,
			'user_url'     => $website
		];
		$user_id = wp_insert_user($user_args);
		self::addProfileImage($user_id);
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
		global $wpdb;
		$uid = User\Utility::getCurrentUser()->ID;
		wp_logout();
		$attachment_id = get_user_meta($uid, 'user_image', true);
		$table_name = $wpdb->prefix . 'message';
		$sql = <<<SQL
		DELETE from $table_name WHERE se_id = $uid OR re_id = $uid;
SQL;
		$wpdb->query($sql);
		//delete_user_meta(none, 'user_like', $uid);
		$statement = <<<SQL
		DELETE FROM $wpdb->usermeta WHERE meta_key = 'user_like' and meta_value = $uid;
SQL;
		$wpdb->query($statement);
		if ($attachment_id) {
			wp_delete_attachment($attachment_id, true);
		}
		$wpdb->query($sql);
		wp_delete_user($uid);
		die(json_encode([
			'success' => true
		]));
	}

	/**
	 * 자기소개 반환
	 */
	public static function getIntro() {
		$uid = User\Utility::getCurrentUser()->ID;
		$intro = get_user_meta($uid, 'user_intro', true);
		die(json_encode([
			'success' => true,
			'intro'   => $intro
		]));
	}

	/**
	 * 자기소개 설정
	 */
	public static function setIntro() {
		$intro = $_POST['intro'];
		$uid = User\Utility::getCurrentUser()->ID;
		$res = update_user_meta($uid, 'user_intro', $intro);
		die(json_encode([
			'success' => true
		]));
	}

	/**
	 * 프로필 이미지의 <b>URL</b> 반환
	 */
	public static function getProfileImage() {
		$uid = User\Utility::getCurrentUser()->ID;
		$attachment_url = User\Utility::getUserImageUrl($uid);
		die(json_encode([
			'success' => true,
			'image'   => $attachment_url
		]));
	}

	public static function setProfileImage() {
		$uid = User\Utility::getCurrentUser()->ID;
		self::addProfileImage($uid);
		die(json_encode([
			'success' => true
		]));
	}

	/**
	 * 로그인/가입 공용 Sign in 메소드
	 *
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
			'success'    => true,
			'uid'        => $user->ID,
			'user_login' => $user->user_login
		];
	}

	private static function addProfileImage($uid) {
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
			update_user_meta($uid, 'user_image', $attachment_id);
		}
	}
	/**
	 * 프로필 수정 (값 불러오기)
	 */
	public static function getEdit() {
			$user_id = User\Utility::getCurrentUser()->ID;
			$userinfo = get_user_by('ID', $user_id);
			$login = $userinfo->user_login; //login 아이디를 가져옴
			$name = $userinfo->display_name;
			$pwd = $userinfo->user_pass;
			$email = $userinfo->user_email;
			$website = $userinfo->user_url;
			$birth = get_user_meta($user_id,'user_birth',true);
			$nation = get_user_meta($user_id,'user_nation',true);
			$gender = get_user_meta($user_id,'user_gender',true);
			die(json_encode([
					'success' => true,
					'login' => $login,
					'name' => $name,
					'pwd' => $pwd,
					'email'=> $email,
					'website' => $website,
					'birth' => $birth,
					'nation' => $nation,
					'gender' => $gender
			]));
	}

	/**
	 * 프로필 수정 (변경값 업데이트)
	 */

	public static function setEdit() {
			$login = $_POST['login'];
			$name = $_POST['name'];
			$pwd = $_POST['pwd'];
			$email = $_POST['email'];
			$website = $_POST['website'];
			$user_args = [
					'user_login'   => $login,
					'user_pass'    => $pwd,
					'display_name' => $name,
					'user_email'   => $email,
					'user_url'     => $website
			];
			$user_id = wp_update_user($user_args);
			add_user_meta($user_id,'emailtest',$email);
			self::addProfileImage($user_id);
			update_user_meta($user_id, 'user_birth', $_POST['birth']);
			update_user_meta($user_id, 'user_nation', $_POST['nation']);
			update_user_meta($user_id, 'user_gender', $_POST['gender']);
			die(json_encode([
					'success' => true,
					'login' => $login,
					'name' => $name,
					'pwd' => $pwd,
					'email'=> $email,
					'website' => $website,
					'birth' => $birth,
					'nation' => $nation,
					'gender' => $gender
			]));

	}
	public static function otherInfo() {

			$other = $_POST['other']; // 다른 사용자 uid

			$otherinfo = get_user_by('ID',$other);
			$other_name = $otherinfo->user_login;
			$other_image = $otherinfo->user_image;

			die(json_encode([
			 'success' => true,
			 'other_name'  => $other_name,
			 'other_image' => $other_image
 ]));
}
}
