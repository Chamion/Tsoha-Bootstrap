<?php

class TeamController extends BaseController{
    
    public static function managePage(){
        self::check_logged_in();
        $player = $_SESSION['player'];
        $maxTeamPage = TeamModel::countPagesByMember($player);
        $maxInvitePage = TeamModel::countPagesInvites($player);
        if(!isset($_SESSION['teamPage'])){
            $_SESSION['teamPage'] = 1;
        }else if($_SESSION['teamPage'] == -1 || $_SESSION['teamPage'] > $maxTeamPage){
            $_SESSION['teamPage'] = $maxTeamPage;
        }
        if(!isset($_SESSION['invitePage'])){
            $_SESSION['invitePage'] = 1;
        }else if($_SESSION['invitePage'] == -1 || $_SESSION['invitePage'] > $maxInvitePage){
            $_SESSION['invitePage'] = $maxInvitePage;
        }
        $teams = TeamModel::findByMember($player, $_SESSION['teamPage']);
        $invites = TeamModel::findInvites($player, $_SESSION['invitePage']);
        $params = array('teams' => $teams, 'invites' => $invites);
        $params['teamPage'] = $_SESSION['teamPage'];
        $params['invitePage'] = $_SESSION['invitePage'];
        $params['maxTeamPage'] = $maxTeamPage;
        $params['maxInvitePage'] = $maxInvitePage;
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
        $maxMemberPage = PlayerModel::countPagesByTeam($_SESSION['teamId']);
        if(!isset($_SESSION['memberPage'])){
            $_SESSION['memberPage'] = 1;
        }else if($_SESSION['memberPage'] > $maxMemberPage || $_SESSION['memberPage'] == -1){
            $_SESSION['memberPage'] = $maxMemberPage;
        }
        if($player == $team->leader){
            $maxInviteePage = PlayerModel::countPagesByInvite($_SESSION['teamId']);;
            if(!isset($_SESSION['inviteePage'])){
                $_SESSION['inviteePage'] = 1;
            }else if($_SESSION['inviteePage'] > $maxInviteePage || $_SESSION['inviteePage'] == -1){
                $_SESSION['inviteePage'] = $maxInviteePage;
            }
        }
        $members = PlayerModel::findByTeam($team->id, $_SESSION['memberPage']);
        $params = array('team' => $team,
            'members' => $members,
            'memberPage' => $_SESSION['memberPage'],
            'maxMemberPage' => $maxMemberPage);
        if($player == $team->leader){
            $path = 'suunnitelmat/ryhma1.html';
            $params['invites'] = PlayerModel::findByInvite($team->id, $_SESSION['inviteePage']);
            $params['inviteePage'] = $_SESSION['inviteePage'];
            $params['maxInviteePage'] = $maxInviteePage;
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
    
    public static function setClosed(){
        $value = $_POST['closed'] == 'Invite-only';
        TeamModel::setClosed($_SESSION['teamId'], $value);
        Redirect::to('/ryhma1');
    }
    
    public static function manageSetPage(){
        if($_POST['list'] == 'team'){
            self::manageFlipPage('teamPage');
        }else{
            self::manageFlipPage('invitePage');
        }
        Redirect::to('/ryhmat');
    }
    
    public static function manageFlipPage($list){
        if($_POST['button'] == 'first'){
            $_SESSION[$list] = 1;
        }else if($_POST['button'] == 'previous'){
            $_SESSION[$list] = $_SESSION[$list] - 1;
        }else if($_POST['button'] == 'next'){
            $_SESSION[$list] = $_SESSION[$list] + 1;
        }else {
            $_SESSION[$list] = -1;
        }
    }
    
    public static function teamSetPage(){
        if($_POST['list'] == 'member'){
            self::manageFlipPage('memberPage');
        }else{
            self::manageFlipPage('inviteePage');
        }
        Redirect::to('/ryhma1');
    }
    
    public static function joinOpen(){
        if(!ctype_digit($_POST['joinId'])){
            Redirect::to('/ryhmat');
            return;
        }
        TeamModel::joinOpen($_SESSION['player'], $_POST['joinId']);
        Redirect::to('/ryhmat');
    }
    
    public static function disband(){
        //Toteuttamatta
    }
}

