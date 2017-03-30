<?php

class PlayerController extends BaseController{
    public static function loginPage(){
        View::make('suunnitelmat/etusivu.html');
    }
    
    public static function login(){
        $params = $_POST;
        if($params['register'] == 'true'){
            PlayerController::register($params['username'], $params['password']);
        }
        $id = PlayerModel::login($params['username'], $params['password']);
        if($id == 0){
            Redirect::to('/etusivu');
            return;
        }
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['player'] = $id;
        $_SESSION['gameInput'] = null;
        Redirect::to('/paasivu');
    }
    
    public static function register($username, $password){
        if(PlayerModel::nameAvailable($username)){
            PlayerModel::addPlayer($username, $password);
        }
        Redirect::to('/etusivu');
    }
    
    public static function navPage(){
        $player = PlayerModel::findById($_SESSION['player']);
        View::make('suunnitelmat/paasivu.html', array('player' => $player));
    }
}

