<?php

class TeamController extends BaseController{
    
    public static function managePage(){
        $player = $_SESSION['player'];
        View::make('suunnitelmat/ryhmat.html', array('teams' => TeamModel::findByMember($player)));
    }
    
    public static function create(){
        $player = $_SESSION['player'];
        $params = $_POST;
        TeamModel::addTeam($player, $params['groupName']);
        Redirect::to('/ryhmat');
    }
    
    public static function teamPageInit(){
        $_SESSION['teamId'] = $_POST['teamId'];
        TeamController::teamPage();
    }
    
    public static function teamPage(){
        $player = $_SESSION['player'];
        $team = TeamModel::findById($_SESSION['teamId']);
        $members = PlayerModel::findByTeam($team->id);
        if($player == $team->leader){
            $path = 'suunnitelmat/ryhma1.html';
        }else{
            $path = 'suunnitelmat/ryhma2.html';
        }
        View::make($path, array('team' => $team, 'members' => $members));
    }
    
    public static function kick(){
        $params = $_POST;
        $team = TeamModel::findById($_SESSION['teamId']);
        $toKick = $params['memberId'];
        if($team->leader == $toKick){
            //johtajaa ei voi poistaa.
        } else {
            
        }
        Redirect::to('/ryhma1');
    }
    
    public static function leave(){
        TeamModel::severMembership($_SESSION['player'], $_SESSION['teamId']);
        TeamController::managePage();
    }
}

