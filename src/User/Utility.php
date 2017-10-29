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
				'error' => 'no_login',
				'context' => 'getCurrentUser'
			]));
		}
		return $user;
	}

	public static function getUserImageUrl($uid): string {
		$aid = get_user_meta($uid, 'user_image', true); // attachment id
		return wp_get_attachment_image_url($aid);
	}

	public static function getUserAge($uid): int {
		$birth = substr(get_user_meta($uid, 'user_birth', true), 0, 4); // 연도 부분만
		$now = (new \DateTime())->format('Y'); // new \DateTime()->format('Y')인데... PHP 븅신
		return $now - $birth;
	}

	public static function getUserScheduleFormatted($uid): string {
		$schedule_from = str_replace('-', '/',
			get_user_meta($uid, 'user_fromDate', true)); // 1994/09/12
		$schedule_to = str_replace('-', '/',
			substr(get_user_meta($uid, 'user_toDate', true), 5)); // 09/12
		return "$schedule_from - $schedule_to";
	}

	public static function getUserLiked($uid, $target_uid): bool {
		return array_search($target_uid, get_user_meta($uid, 'user_like')) !== false;
	}
}
