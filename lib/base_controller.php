<?php

class BaseController {

    public static function get_user_logged_in() {
        if(isset($_SESSION['player'])) {
            return PlayerModel::findById($_SESSION['player']);
        }
        return null;
    }

    public static function check_logged_in() {
        if (!isset($_SESSION['player'])) {
            Redirect::to('/etusivu');
        }
    }

}
