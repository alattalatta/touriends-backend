<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Like extends Base {
	public static function init() {
		parent::registerAction('bookmark', [__CLASS__, 'bookmark']);
		parent::registerAction('getBookmark', [__CLASS__, 'getBookmark']);
		parent::registerAction('getCommunityList', [__CLASS__, 'getCommunityList']);
	}

	/**
	 * like 좋아요 기능 구현 -> 즐겨찾기로 생각
	 */
	public static function bookmark() {
		$like_id = $_POST['like']; // 대상 id
		$user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id
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

		$users_per_page = 12;
		$page = isset($_POST['page']) ? $_POST['page'] : 0;
		$offset = $users_per_page * $page;

		// 검색 키워드 있는 경우
		$clause_keyword = '';
		if (isset($_POST['keyword'])) {
			$keyword = $_POST['keyword'];
			$clause_keyword .= " AND user_login LIKE '%$keyword'";
		}

		// 현재 사용자 아니고 (WHERE:1)
		// user_toDate 가 지금 시간보다 이후일 때 (WHERE:2~4)
		$statement = <<<SQL
SELECT ID
FROM $wpdb->users u, $wpdb->usermeta m
WHERE
	u.ID <> $uid AND
	u.ID = m.user_id AND
	m.meta_key = 'user_toDate' AND
	CURDATE() < CONVERT(m.meta_value, DATE) $clause_keyword
LIMIT $users_per_page OFFSET $offset
SQL;
		$users = $wpdb->get_col($statement);

		die(json_encode([
			'success' => true,
			'users'  => $users
		]));
	}
}
