<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Tour extends Base {
	public static function init() {
		// SET
		parent::registerAction('calendar', [__CLASS__, 'calendar']);
		parent::registerAction('theme', [__CLASS__, 'theme']);
		parent::registerAction('language', [__CLASS__, 'language']);
		parent::registerAction('place', [__CLASS__, 'place']);
		parent::registerAction('longIntro', [__CLASS__, 'longIntro']);
		// GET
		parent::registerAction('get_calendar', [__CLASS__, 'getCalendar']);
		parent::registerAction('get_schedule', [__CLASS__, 'getSchedule']);
		parent::registerAction('get_theme', [__CLASS__, 'getTheme']);
		parent::registerAction('get_language', [__CLASS__, 'getLanguage']);
		parent::registerAction('get_place', [__CLASS__, 'getPlace']);
		parent::registerAction('get_longIntro', [__CLASS__, 'getLongIntro']);
	}

	/**
	 * 달력
	 */
	public static function calendar() {
		$fromDate = $_POST['from']; // YYYY-MM-DD
		$toDate = $_POST['to']; // YYYY-MM-DD
		$user_id = User\Utility::getCurrentUser()->ID;
		update_user_meta($user_id, 'user_fromDate', $fromDate);
		update_user_meta($user_id, 'user_toDate', $toDate);
		die(json_encode([
			'success' => true
		]));
	}
	public static function getCalendar() {
		$user_id = User\Utility::getCurrentUser()->ID;
		$from = get_user_meta($user_id, 'user_fromDate', true);
		$to = get_user_meta($user_id, 'user_toDate', true);
		die(json_encode([
			'success' => true,
			'from'    => $from,
			'to'      => $to
		]));
	}
	public static function getSchedule() {
		$uid = User\Utility::getCurrentUser()->ID;
		die(json_encode([
			'success' => true,
			'schedule' => User\Utility::getUserScheduleFormatted($uid)
		]));
	}

	/**
	 * 테마
	 */
	public static function theme() {
		$theme = $_POST['theme'];
		$user_id = User\Utility::getCurrentUser()->ID;
		update_user_meta($user_id, 'user_theme', $theme);
		die(json_encode([
			'success' => true
		]));
	}
	public static function getTheme() {
		$user_id = User\Utility::getCurrentUser()->ID;
		$theme = get_user_meta($user_id, 'user_theme', true);
		die(json_encode([
			'success' => true,
			'theme'   => $theme
		]));
	}

	/**
	 * 언어
	 */
	public static function language() {
		$language = $_POST['language'];
		$user_id = User\Utility::getCurrentUser()->ID;
		delete_user_meta($user_id, 'user_language');
		foreach ($language as $lang) {
			add_user_meta($user_id, 'user_language', $lang);
		}
		die(json_encode([
			'success' => true
		]));
	}
	public static function getLanguage() {
		$user_id = User\Utility::getCurrentUser()->ID;
		$language = get_user_meta($user_id, 'user_language');
		die(json_encode([
			'success'  => true,
			'language' => $language
		]));
	}

	/**
	 * 장소
	 */
	public static function place() {
		$place = $_POST['place'];
		$user_id = User\Utility::getCurrentUser()->ID;
		update_user_meta($user_id, 'user_place', $place);
		die(json_encode([
			'success' => true
		]));
	}
	public static function getPlace() {
		$user_id = User\Utility::getCurrentUser()->ID;
		$place = get_user_meta($user_id, 'user_place', true);
		die(json_encode([
			'success' => true,
			'place'   => $place
		]));
	}

	/**
	 * 긴글 소개글
	 */
	public static function longIntro() {
		$long = $_POST['longIntro'];
		$user_id = User\Utility::getCurrentUser()->ID;
		update_user_meta($user_id, 'user_longintro', $long);

		die(json_encode([
			'success' => true
		]));
	}
	public static function getLongIntro() {
		$user_id = User\Utility::getCurrentUser()->ID;
		$long = get_user_meta($user_id, 'user_longintro', true);
		die(json_encode([
			'success'   => true,
			'longIntro' => $long
		]));
	}
}
