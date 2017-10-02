<?php

namespace Touriends\Backend\AJAX;
class UserData extends Base
{
  public $user_id;
  public $theme;
  public $schedules;

  function __constructor($u, $s, $t) {
    $this->user_id = $u;
    $this->theme = $t;
    $this->schdule = $s;
  }
}
