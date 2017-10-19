<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Like extends Base {
	public static function init() {
		parent::registerAction('bookmark', [__CLASS__, 'bookmark']);
		parent::registerAction('getBookmark', [__CLASS__, 'getBookmark']);
		parent::registerAction('search', [__CLASS__, 'search']);
	}
	/*
	  like 좋아요 기능 구현 -> 즐겨찾기로 생각
	*/
	public static function bookmark() {
		$like_id = $_POST['like']; // 대상 id
		$user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id
		$liked = array_search($like_id, get_user_meta($user_id, 'user_like')) !== false; // 기존 좋아요 여부
		if (! $liked) {
			add_user_meta($user_id, 'user_like', $like_id); // db 에 추가한다.
		}
		else {
			delete_user_meta($user_id, 'user_like', $like_id); // 기존 값 삭제
		}
		die(json_encode([
			'success' => true,
			'like' => ! $liked
		]));
	}
	public static function getBookmark() {
		// 이거 안쓸거 같음
		$user_id = User\Utility::getCurrentUser()->ID;
		$ret_like = get_user_meta($user_id, 'user_like'); // 현재 user_id에 like 목록을 받아온다.
		die(json_encode([
			'success' => true,
			'like'    => $ret_like
		]));
	}
	/*
	filter 검색 기능
	*/
	public static function search() {
		global $wpdb; //필요한가 ?
		$content = $_POST['content']; //검색어
		$user_id = User\Utility::getCurrentUser()->ID; //현재 user_id
		$result = []; //검색어가 포함되어 있는 결과를 보내줄 배열
		$ret = get_user_meta($user_id, 'user_like'); //현재의 아이디가 가지고 있는 좋아요 uid를 가져온다
		foreach ($ret as $comp) {
			$userinfo = get_user_by('ID', $comp);
			$comp_user = $userinfo->user_login; //login 아이디를 가져옴
			if (strpos($comp_user, $content) !== false) { //strpos는 문자열을 검색해주는 기능이다 ex) 검색어 "lik" 이면 "like"가 존재하면 true 반환
				$tour_id = $comp;
				$theme = get_user_meta($tour_id, 'user_theme', true);
				$languages = get_user_meta($tour_id, 'user_language');
				$image = User\Utility::getUserImageUrl($tour_id);
				$intro = get_user_meta($tour_id, 'user_intro', true);
				$birth = get_user_meta($tour_id, 'user_birth', true);
				$result[] = [
					'uid'       => intval($tour_id),
					'age'       => $birth,
					'theme'     => $theme,
					'languages' => $languages,
					'image'     => $image,
					'intro'     => $intro
				];
			}
		}
		die(json_encode([
			'success' => true,
			'search'  => $result
		]));
	}
}
