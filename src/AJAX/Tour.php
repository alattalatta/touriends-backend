<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Tour extends Base {
	public static function init() {
		parent::registerAction('set_calendar', [__CLASS__, 'setCalendar']);
		parent::registerAction('set_language', [__CLASS__, 'setLanguage']);
		parent::registerAction('set_theme', [__CLASS__, 'setTheme']);
		parent::registerAction('set_place', [__CLASS__, 'setPlace']);
	}

	/**
	 * 달력
	 */
	public static function setCalendar() {
		$fromDate = $_POST['from']; // 4Y-2M-2D
		$toDate = $_POST['to']; // 4Y-2M-2D
		$user_id = User\Utility::getCurrentUser()->ID;
		update_user_meta($user_id, 'user_fromDate', $fromDate);
		update_user_meta($user_id, 'user_toDate', $toDate);

		die(json_encode([
			'success' => true
		]));
	}

	/**
	 *  테마
	 */
	public static function setTheme() {
		$theme = $_POST['val'];
		$user_id = User\Utility::getCurrentUser()->ID;
		update_user_meta($user_id, 'user_theme', $theme);

		die(json_encode([
			'success' => true
		]));
	}

	/**
	 * 언어
	 */
	public static function setLanguage() {
		$language = $_POST['val'];
		$user_id = User\Utility::getCurrentUser()->ID;
		update_user_meta($user_id, 'user_language', $language);

		die(json_encode([
			'success' => true
		]));
	}

	/**
	 * 장소
	 */
	public static function setPlace() {
		$place = $_POST['val'];
		$user_id = User\Utility::getCurrentUser()->ID;
		update_user_meta($user_id, 'user_place', $place);

		die(json_encode([
			'success' => true
		]));
	}
}
