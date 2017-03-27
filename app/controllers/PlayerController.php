<?php

class PlayerController extends BaseController{
    public static function loginPage(){
        View::make('suunnitelmat/etusivu.html');
    }
    
    public static function login(){
        $params = $_POST;
        $id = PlayerModel::login($params['username'], $params['password']);
        if($id == 0){
            View::make('suunnitelmat/etusivu.html');
            return;
        }
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['player'] = $id;
        $player = PlayerModel::findById($id);
        View::make('suunnitelmat/paasivu.html', array('player' => $player));
    }
}

