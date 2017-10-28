<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Like extends Base {
	public static function init() {
		parent::registerAction('bookmark', [__CLASS__, 'bookmark']);
		parent::registerAction('getBookmark', [__CLASS__, 'getBookmark']);
		parent::registerAction('getCommunityList', [__CLASS__, 'getCommunityList']);
		parent::registerAction('getCommunityItem', [__CLASS__, 'getCommunityItem']);
	}

	/**
	 * like 좋아요 기능 구현 -> 즐겨찾기로 생각
	 */
	public static function bookmark() {
		$like_id = $_POST['like']; // 대상 id
		$user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id

		if (! isset($_POST['override'])) {
			$liked = array_search($like_id, get_user_meta($user_id, 'user_like')) !== false; // 기존 좋아요 여부
			if (! $liked) {
				add_user_meta($user_id, 'user_like', $like_id); // db 에 추가한다.
			} else {
				delete_user_meta($user_id, 'user_like', $like_id); // 기존 값 삭제
			}
			die(json_encode([
				'success' => true,
				'like'    => ! $liked
			]));
		}
		else {
			$override = $_POST['override'] === 'true';
			if ($override) {
				add_user_meta($user_id, 'user_like', $like_id); // db 에 추가한다.
			} else {
				delete_user_meta($user_id, 'user_like', $like_id); // 기존 값 삭제
			}
			die(json_encode([
				'success' => true,
				'like' => $override
			]));
		}
	}

	/**
	 * 좋아요 한 사람들
	 */
	public static function getBookmark() {
		$uid = User\Utility::getCurrentUser()->ID;
		$ret_like = get_user_meta($uid, 'user_like'); // 현재 user_id에 like 목록을 받아온다.
		$res = [];
		foreach ($ret_like as $lid) {
			$liked_user = get_user_by('id', $lid);

			$res[] = [
				'uid'       => intval($lid),
				'id'        => $liked_user->user_login,
				'age'       => User\Utility::getUserAge($lid),
				'schedule'  => User\Utility::getUserScheduleFormatted($lid),
				'image'     => User\Utility::getUserImageUrl($lid),
				'theme'     => get_user_meta($lid, 'user_theme', true),
				'languages' => get_user_meta($lid, 'user_language'),
				'comment'   => get_user_meta($lid, 'user_longintro', true),
				'liked'     => true
			];
		}

		die(json_encode([
			'success' => true,
			'liked'   => $res
		]));
	}

	/**
	 * filter 검색 기능
	 */
	public static function getCommunityList() {
		global $wpdb;
		$uid = User\Utility::getCurrentUser()->ID;

		// 검색 키워드 있는 경우
		$clause_keyword = '';
		if (isset($_POST['keyword']) && ! empty($_POST['keyword'])) {
			$keyword = $_POST['keyword'];
			$clause_keyword .= "AND u.user_login LIKE '%$keyword%'";
		}

		// 언어 필터 있는 경우
		$clause_language = '';
		if (isset($_POST['language']) && ! empty($_POST['language'])) {
			$language = $_POST['language'];
			$clause_language = "AND m.meta_value = '$language'";
		}

		// 현재 사용자 아니고 (WHERE:1)
		$statement = <<<SQL
SELECT DISTINCT ID
FROM $wpdb->users u, $wpdb->usermeta m
WHERE
	u.ID <> $uid AND 
	u.ID = m.user_id AND 
	m.meta_key = 'user_language' $clause_keyword $clause_language
SQL;
		$queried = $wpdb->get_col($statement);
		$res = [];

		// 나이 필터
		list($age_min, $age_max) = $_POST['ages'];
		if ($age_min === '0') {
			$age_min = -1;
		}
		else {
			$age_min = intval($age_min);
		}
		if ($age_max === '40') {
			$age_max = 999;
		}
		else {
			$age_max = intval($age_max) + 9; // 29, 39
		}

		foreach ($queried as $q_uid) {
			$age = User\Utility::getUserAge($q_uid);
			if ($age < $age_min || $age > $age_max) {
				continue;
			}

			$matched = get_user_meta($q_uid, 'matched', true);
			if ($matched === '') {
				continue;
			}

			$user = get_user_by('ID', $q_uid);
			$res[] = [
				'id' => intval($q_uid),
				'login' => $user->user_login,
				'age' => $age,
				'image' => User\Utility::getUserImageUrl($q_uid),
				'theme' => get_user_meta($q_uid, 'user_theme', true),
				'languages' => get_user_meta($q_uid, 'user_language'),
				'intro' => get_user_meta($q_uid, 'user_intro', true),
				'liked' => User\Utility::getUserLiked($uid, $q_uid)
			];
		}

		die(json_encode([
			'success' => true,
			'users'  => $res
		]));
	}

	public static function getCommunityItem() {
		$uid = User\Utility::getCurrentUser()->ID;
		$target_uid = $_POST['id'];
		$target_user = get_user_by('ID', $target_uid);
		die(json_encode([
			'success' => true,
			'login' => $target_user->user_login,
			'age' => User\Utility::getUserAge($target_uid),
			'image' => User\Utility::getUserImageUrl($target_uid),
			'schedule' => User\Utility::getUserScheduleFormatted($target_uid),
			'theme' => get_user_meta($target_uid, 'user_theme', true),
			'languages' => get_user_meta($target_uid, 'user_language'),
			'intro' => get_user_meta($target_uid, 'user_intro', true),
			'comment' => get_user_meta($target_uid, 'user_longintro', true),
			'liked' => User\Utility::getUserLiked($uid, $target_uid)
		]));
	}
}
