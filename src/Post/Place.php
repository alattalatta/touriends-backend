<?php

namespace Touriends\Backend\Post;

class Place extends Base {
	public static function init() {
		register_post_type('place', [
			'label'         => 'Place',
			'public'        => true,
			'menu_position' => 11,
			'supports' => ['title']
		]);

		$place = array("Eunpyeong", "Jongno", "Gangseo", "Yeongdeungpo", "Songpa", "Gwanak", "Seodaemun", "Gwangjin", "Gangdong", "Yangcheon", "Jung-gu", "Seongbuk", "Gangbuk", "Bobong", "Bongdaemun", "Seong-dong", "Dongjak", "Guro", "Jungnang", "Yongsan", "Geumcheon", "Seocho", "Nowon", "Mapo", "Gangnam");

		if ($place[0]!="Eunpyeong"){
			function wp_insert_post( $place, $wp_error = false ) {
	    global $wpdb;

	    $user_id = get_current_user_id();

	    $defaults = array(
	      'post_author' => $user_id,
	      'post_content' => '',
	      'post_content_filtered' => '',
	      'post_title' => '',
				}
		}


}
