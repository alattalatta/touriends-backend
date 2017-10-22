<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Profile extends Base {
    public static function init() {
        // 프로필 수정 GET/SET
        parent::registerAction('getEdit', [__CLASS__, 'getEdit']);
        parent::registerAction('setEdit', [__CLASS__, 'setEdit']);
        parent::registerAction('fileIntro', [__CLASS__, 'fileIntro']);
        parent::registerAction('editIntro', [__CLASS__, 'editIntro']);
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
            'user_url' => $website,
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
  /**
  * 자기소개 넘겨주기
  */
 public static function fileIntro() {
     $uid = User\Utility::getCurrentUser()->ID;
     $intro = get_user_meta($uid, 'user_intro', true);
     die(json_encode([
         'success' => true,
         'intro'   => $intro
     ]));
 }

 /**
  * 변경된 자기소개 저장
  */
 public static function editIntro() {
     $intro = $_POST['intro'];
     $uid = User\Utility::getCurrentUser()->ID;
     update_user_meta($uid, 'user_intro', $intro);
     die(json_encode([
         'success' => true,
         'intro' => $intro
     ]));
 }

}
