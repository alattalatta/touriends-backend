<?php

namespace Touriends\Backend\User;

class Utility {
	/**
	 * 현재 로그인 한 사용자의 User 객체 반환. 로그인 정보 없을 경우 스크립트 강제 종료(die).
	 * @return \WP_User Current User object
	 */
	public static function getCurrentUser(): \WP_User {
		$user = wp_get_current_user();
		if ($user->ID === 0) {
			die(json_encode([
				'success' => false,
				'error' => 'no_login'
			]));
		}
		return $user;
	}
}