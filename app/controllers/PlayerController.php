<?php

class PlayerController extends BaseController{
    public static function loginPage(){
        $params = array();
        if(isset($_SESSION['registerUsernameInput'])){
            $params['registerUsernameInput'] = $_SESSION['registerUsernameInput'];
            $params['error'] = $_SESSION['errors'][0];
            unset($_SESSION['registerUsernameInput']);
            unset($_SESSION['errors']);
        }
        View::make('suunnitelmat/etusivu.html', $params);
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
        $_SESSION['statsInput'] = null;
        Redirect::to('/paasivu');
    }
    
    public static function register($username, $password){
        if(PlayerModel::nameAvailable($username)){
            //Luodaan uusi PlayerModel olio aina, kun rekisteröidään uusi player. Refaktoroinnin tarvetta.
            $model = new PlayerModel($username, $password);
            if(count($model->errors) > 0){
                $_SESSION['registerUsernameInput'] = $username;
                $_SESSION['errors'] = $model->errors;
            }else{
                $model->addPlayer($username, $password);
            }
        }
        Redirect::to('/etusivu');
    }
    
    public static function navPage(){
        self::check_logged_in();
        $player = self::get_user_logged_in();
        View::make('suunnitelmat/paasivu.html', array('player' => $player));
    }
}

