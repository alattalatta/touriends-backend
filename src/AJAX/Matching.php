<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Matching extends Base {
	public static function init() {
		parent::registerAction('getMatching', [__CLASS__, 'getMatching']);
	}

	public static function getMatching() {
		global $wpdb;
		$user_id = get_current_user_id();
		$user_language = get_user_meta($user_id, 'user_language');
		$user_theme = get_user_meta($user_id, 'user_theme', true);
		$user_fromDate = get_user_meta($user_id, 'user_fromDate', true);
		$user_toDate = get_user_meta($user_id, 'user_toDate', true);

		// 현재 사용자 언어 다 가져옴
		$is_first = true;
		$clause_where = '';
		foreach ($user_language as $lang) {
			if ($is_first) {
				$is_first = false;
			}
			else {
				$clause_where .= ' OR ';
			}
			$clause_where .= "meta_value = '${lang}'";
		}

		$statement = <<<SQL
SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE $clause_where
SQL;
		$ret_language = $wpdb->get_col($statement);

		date_default_timezone_set('Asia/Seoul');
		$my_from = new \DateTime($user_fromDate); // 검색 주체
		$my_to = new \DateTime($user_toDate);

		$result = [];
		foreach ($ret_language as $tour_id) {
			$tour_fromDate = get_user_meta($tour_id, 'user_fromDate', true);
			$tour_toDate = get_user_meta($tour_id, 'user_toDate', true);

			# 검색될 내용
			$your_from = new \DateTime($tour_fromDate);
			$your_to = new \DateTime($tour_toDate);
			$days = 0; // 겹치는 기간

			// 내꺼 = 검색 주체 / 네꺼 = 검색 결과
			if ($my_from > $your_to || $your_from > $my_to) { // 안 겹침
				continue;
			}

			if ($my_from > $your_from && $my_to > $your_to) { // 내꺼가 네꺼 다 감쌈
				$days = $my_from->diff($your_to)->days + 1;
			} else if ($your_from > $my_from && $your_to > $my_to) { // 네꺼가 내꺼 다 감쌈
				$days = $your_from->diff($my_to)->days + 1;
			} else if ($my_from > $your_from && $your_to > $my_to) { // 내꺼 먼저 네꺼 나중
				$days = $my_from->diff($my_to)->days + 1;
			} else if ($my_from < $your_from && $your_to < $my_to) { // 네꺼 먼저 내꺼 나중
				$days = $your_from->diff($your_to)->days + 1;
			}
			if ($days > 0) {
				$result[] = [
					'uid'       => intval($tour_id),
					'theme'     => get_user_meta($tour_id, 'user_theme', true),
					'languages' => get_user_meta($tour_id, 'user_language'),
					'image'     => User\Utility::getUserImageUrl($tour_id),
					'comment'   => get_user_meta($tour_id, 'user_longintro', true),
					'days'      => $days,
					'liked'     => array_search($tour_id, get_user_meta($user_id, 'user_like')) !== false
				];
			}
		}

		// http://php.net/manual/en/functions.anonymous.php#example-165
		// function ($param) use ($variable) { ... }
		usort($result, function ($a, $b) use ($user_theme) {
			// 스케쥴 다르면 잘 맞는 사람 앞으로
			if ($a['days'] !== $b['days']) {
				return $a['days'] > $b['days'] ? -1 : 1;
			}

			// 둘 다 스케쥴 같은데...
			// 테마가 둘 다 나랑 다르면 스킵
			if ($a['theme'] !== $user_theme && $b['theme'] !== $user_theme) {
				return 0;
			}

			// 테마가 같은 사람을 앞으로
			if ($a['theme'] !== $b['theme']) {
				return $a['theme'] === $user_theme ? 1 : -1;
			}

			return 0;
		});
		die(json_encode([
			'success'  => true,
			'matching' => $result
		]));
	}
}
