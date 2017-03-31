<?php

class GameController extends BaseController{
    public static function removeList(){
        $player = $_SESSION['player'];
        $games = GameModel::all();
        View::make('suunnitelmat/poista.html', array('games' => $games));
    }
    
    public static function remove(){
        $params = $_POST;
        GameModel::removeById($params['id']);
        Redirect::to('/poista');
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
        View::make('suunnitelmat/kirjaus.html', $_SESSION['gameInput']);
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
        Redirect::to('/kirjaus');
    }
    
    public static function stats(){
        $player = $_SESSION['player'];
        $groups = array('1','2');
        //$stats = GameModel::generalPrivateStats($player);
        $stats = GameModel::generalGroupStats($groups);
        View::make('suunnitelmat/analyysi.html', array('stats' => $stats));
    }
}