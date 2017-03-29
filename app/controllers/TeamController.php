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
        TeamController::managePage();
    }
    
    public static function teamPage(){
        $player = $_SESSION['player'];
        $teamId = $_POST['teamId'];
        $team = TeamModel::findById($teamId);
        $members = PlayerModel::findByTeam($team->id);
        View::make('suunnitelmat/ryhma1.html', array('team' => $team, 'members' => $members));
    }
}

