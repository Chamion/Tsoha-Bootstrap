<?php

class GameController extends BaseController{
    public static function removeList(){
        $player = $_SESSION['player'];
        $games = GameModel::findByPlayer($player);
        View::make('suunnitelmat/poista.html', array('games' => $games));
    }
    
    public static function remove(){
        $params = $_POST;
        GameModel::removeById($params['id']);
        GameController::removeList();
    }
    
    public static function loggingPage($params){
        if($params == null){
            View::make('suunnitelmat/kirjaus.html', array(
                'legend' => 1,
                'win' => 1,
                'hero' => 1,
                'opponent' => 1
            ));
        }else{
            View::make('suunnitelmat/kirjaus.html', $params);
        }
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
        $params = array(
            'legend' => $legend,
            'win' => $win,
            'hero' => $_POST['hero'],
            'opponent' => $_POST['opponent']
        );
        GameController::loggingPage($params);
    }
    
    public static function stats(){
        $player = $_SESSION['player'];
        $stats = GameModel::generalPrivateStats($player);
        View::make('suunnitelmat/analyysi.html', array('stats' => $stats));
    }
}