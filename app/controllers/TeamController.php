<?php

class TeamController extends BaseController{
    
    public static function managePage(){
        self::check_logged_in();
        $player = $_SESSION['player'];
        $params = array('teams' => TeamModel::findByMember($player), 'invites' => TeamModel::findInvites($player));
        if(isset($_SESSION['groupNameInput'])){
            $params['groupNameInput'] = $_SESSION['groupNameInput'];
            $params['error'] = $_SESSION['errors'][0];
            unset($_SESSION['groupNameInput']);
            unset($_SESSION['errors']);
        }
        View::make('suunnitelmat/ryhmat.html', $params);
    }
    
    public static function create(){
        self::check_logged_in();
        $player = $_SESSION['player'];
        $params = $_POST;
        $model = new TeamModel($params['groupName']);
        if(count($model->errors) > 0){
            $_SESSION['groupNameInput'] = $params['groupName'];
            $_SESSION['errors'] = $model->errors;
        }else{
            TeamModel::addTeam($player, $params['groupName']);
        }
        Redirect::to('/ryhmat');
    }
    
    public static function teamPageInit(){
        self::check_logged_in();
        $_SESSION['teamId'] = $_POST['teamId'];
        TeamController::teamPage();
    }
    
    public static function teamPage(){
        self::check_logged_in();
        $player = $_SESSION['player'];
        $team = TeamModel::findById($_SESSION['teamId']);
        $members = PlayerModel::findByTeam($team->id);
        $params = array('team' => $team, 'members' => $members);
        if($player == $team->leader){
            $path = 'suunnitelmat/ryhma1.html';
            $params['invites'] = PlayerModel::findByInvite($team->id);
        }else{
            $path = 'suunnitelmat/ryhma2.html';
        }
        View::make($path, $params);
    }
    
    public static function kick(){
        self::check_logged_in();
        $params = $_POST;
        $team = TeamModel::findById($_SESSION['teamId']);
        $toKick = $params['memberId'];
        if($team->leader == $toKick){
            //johtajaa ei voi poistaa.
        } else {
            TeamModel::severMembership($toKick, $team->id);
        }
        Redirect::to('/ryhma1');
    }
    
    public static function leave(){
        self::check_logged_in();
        TeamModel::severMembership($_SESSION['player'], $_SESSION['teamId']);
        TeamController::managePage();
    }
    
    public static function invite(){
        self::check_logged_in();
        $player = PlayerModel::findByName($_POST['player']);
        if($player != null){
            TeamModel::invite($_SESSION['teamId'], $player->id);
        }
        Redirect::to('/ryhma1');
    }
    
    public static function inviteChoice(){
        self::check_logged_in();
        if($_POST['inviteChoice'] == 'Accept'){
            TeamModel::join($_SESSION['player'], $_POST['team']);
        } else {
            TeamModel::severMembership($_SESSION['player'], $_POST['team']);
        }
        Redirect::to('/ryhmat');
    }
}

