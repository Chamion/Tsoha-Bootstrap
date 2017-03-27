<?php

class GameController extends BaseController{
    public static function removeList(){
        $games = GameModel::findByPlayer(1);
        View::make('suunnitelmat/poista.html', array('games' => $games));
    }
    
    public static function remove(){
        $params = $_POST;
        GameModel::removeById($params['id']);
        GameController::removeList();
    }
    
    public static function loggingPage(){
        View::make('suunnitelmat/kirjaus.html', array());
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
        View::make('suunnitelmat/kirjaus.html', array());
    }
    
    public static function stats(){
        $player = $_SESSION['player'];
        $stats = GameModel::generalPrivateStats($player);
        View::make('suunnitelmat/analyysi.html', array('stats' => $stats));
    }
}