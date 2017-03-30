<?php

class MalliController extends BaseController{
    
    public static function malliInit(){
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['player'] = 1;
        $_SESSION['gameInput'] = null;
        $_SESSION['find'] = null;
        MalliController::loggingPage();
    }
    
    public static function removeList(){
        $player = $_SESSION['player'];
        $games = GameModel::findByPlayer($player);
        View::make('viikko3vaatimukset/listaus.html', array('games' => $games));
    }
    
    public static function remove(){
        $params = $_POST;
        GameModel::removeById($params['id']);
        Redirect::to('/mallilistaus');
    }
    
    public static function loggingPage(){
        if($_SESSION['gameInput'] == null){
            $_SESSION['gameInput'] = array(
                'legend' => 1,
                'win' => 1,
                'hero' => 1,
                'opponent' => 1
            );
        }
        View::make('viikko3vaatimukset/lisays.html', $_SESSION['gameInput']);
    }
    
    public static function addResult(){
        $player = $_SESSION['player'];
        if(isset($_POST['legend'])){
            $legend = 1;
        }else{
            $legend = 0;
        }
        if($_POST['win'] == 'win'){
            $win = 1;
        }else{
            $win = 0;
        }
        GameModel::add($player, $legend, $win, $_POST['hero'], $_POST['opponent']);
        $_SESSION['gameInput'] = array(
            'legend' => $legend,
            'win' => $win,
            'hero' => $_POST['hero'],
            'opponent' => $_POST['opponent']
        );
        Redirect::to('/malli');
    }
    
    public static function findPage(){
        if($_SESSION['find'] == null){
            View::make('viikko3vaatimukset/haku.html');
        }else{
            $game = GameModel::findById($_SESSION['find']);
            View::make('viikko3vaatimukset/hakuResult.html',array('game' => $game));
        }
    }
    
    public static function find(){
        if($_POST['id'] != ''){
            $_SESSION['find'] = $_POST['id'];
        }
        Redirect::to('/mallihaku');
    }
}