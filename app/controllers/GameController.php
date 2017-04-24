<?php

class GameController extends BaseController {

    public static function removeList() {
        self::check_logged_in();
        $player = $_SESSION['player'];
        $maxGamePage = GameModel::countPagesByPlayer($player);
        if(!isset($_SESSION['gamePage'])){
            $_SESSION['gamePage'] = 1;
        }else if($_SESSION['gamePage'] > $maxGamePage || $_SESSION['gamePage'] == -1){
            $_SESSION['gamePage'] = $maxGamePage;
        }
        $games = GameModel::findByPlayer($player, $_SESSION['gamePage']);
        View::make('pages/poista.html', array('games' => $games, 'gamePage' => $_SESSION['gamePage'], 'maxGamePage' => $maxGamePage));
    }

    public static function remove() {
        self::check_logged_in();
        $params = $_POST;
        GameModel::removeById($params['id']);
        Redirect::to('/logging/list');
    }

    public static function loggingPage() {
        self::check_logged_in();
        if ($_SESSION['gameInput'] == null) {
            $_SESSION['gameInput'] = array(
                'legend' => 1,
                'win' => 1,
                'hero' => 1,
                'opponent' => 1
            );
        }
        $params = array('gameInput' => $_SESSION['gameInput']);
        if(isset($_SESSION['submit'])){
            $params['submit'] = $_SESSION['submit'];
            unset($_SESSION['submit']);
        }
        View::make('pages/kirjaus.html', $params);
    }

    public static function addResult() {
        self::check_logged_in();
        $player = $_SESSION['player'];
        if (isset($_POST['legend'])) {
            $legend = 1;
        } else {
            $legend = 0;
        }
        if ($_POST['win'] == 'win') {
            $win = 1;
        } else {
            $win = 0;
        }
        GameModel::add($player, $legend, $win, $_POST['hero'], $_POST['opponent']);
        $_SESSION['gameInput'] = array(
            'legend' => $legend,
            'win' => $win,
            'hero' => $_POST['hero'],
            'opponent' => $_POST['opponent']
        );
        $_SESSION['submit'] = 'submit';
        Redirect::to('/logging');
    }

    public static function statsRefresh() {
        self::check_logged_in();
        $_SESSION['statsInput'] = array(
            'group' => $_POST['group'],
            'class' => $_POST['class']
        );
        if(isset($_POST['legend'])){
            $_SESSION['statsInput']['legend'] = 1;
        } else{
            $_SESSION['statsInput']['legend'] = 0;
        }
        if(isset($_POST['mirror'])){
            $_SESSION['statsInput']['mirror'] = 1;
        } else{
            $_SESSION['statsInput']['mirror'] = 0;
        }
        if ($_SESSION['statsInput']['group'] == 'custom') {
            $_SESSION['statsInput']['groupIds'] = array();
            foreach (TeamModel::findByMember($_SESSION['player'], 1) as $team) {
                if (isset($_POST['' . $team->id])) {
                    $_SESSION['statsInput']['groupIds'][] = $team->id;
                }
            }
        }
        if ($_SESSION['statsInput']['class'] == 'for') {
            $_SESSION['statsInput']['hero'] = $_POST['hero'];
        }
        Redirect::to('/analysis');
    }

    public static function stats() {
        self::check_logged_in();
        if ($_SESSION['statsInput'] == null) {
            $_SESSION['statsInput'] = array(
                'group' => 'all',
                'class' => 'all',
                'legend' => 0,
                'mirror' => 0
            );
        }
        $player = $_SESSION['player'];
        $allGroups = TeamModel::findAllByMember($player);
        if ($_SESSION['statsInput']['class'] == 'all') {
            if ($_SESSION['statsInput']['group'] == 'all') {
                $groupIds = array();
                foreach ($allGroups as $group) {
                    $groupIds[] = $group->id;
                }
                $stats = GameModel::generalGroupStats($groupIds, $_SESSION['statsInput']['legend'], $_SESSION['statsInput']['mirror'] == 1);
            } else if ($_SESSION['statsInput']['group'] == 'me') {
                $stats = GameModel::generalPrivateStats($player, $_SESSION['statsInput']['legend'], $_SESSION['statsInput']['mirror'] == 1);
            } else {
                $stats = GameModel::generalGroupStats($_SESSION['statsInput']['groupIds'], $_SESSION['statsInput']['legend'], $_SESSION['statsInput']['mirror'] == 1);
            }
        } else if ($_SESSION['statsInput']['class'] == 'for') {
            if ($_SESSION['statsInput']['group'] == 'all') {
                $groupIds = array();
                foreach ($allGroups as $group) {
                    $groupIds[] = $group->id;
                }
                $stats = GameModel::matchupGroupStats($groupIds, $_SESSION['statsInput']['hero'], $_SESSION['statsInput']['legend'], $_SESSION['statsInput']['mirror'] == 1);
            } else if ($_SESSION['statsInput']['group'] == 'me') {
                $stats = GameModel::matchupPrivateStats($player, $_SESSION['statsInput']['hero'], $_SESSION['statsInput']['legend'], $_SESSION['statsInput']['mirror'] == 1);
            } else {
                $stats = GameModel::matchupGroupStats($_SESSION['statsInput']['groupIds'], $_SESSION['statsInput']['hero'], $_SESSION['statsInput']['legend'], $_SESSION['statsInput']['mirror'] == 1);
            }
        }
        View::make('pages/analyysi.html', array('stats' => $stats, 'groups' => $allGroups, 'statsInput' => $_SESSION['statsInput']));
    }

    public static function editInit() {
        self::check_logged_in();
        $_SESSION['gameInput'] = GameModel::inputsById($_POST['id']);
        $_SESSION['gameId'] = $_POST['id'];
        Redirect::to('/logging/list/edit');
    }

    public static function editPage() {
        View::make('pages/edit.html', $_SESSION['gameInput']);
    }
    
    public static function editSave() {
        self::check_logged_in();
        $player = $_SESSION['player'];
        if (isset($_POST['legend'])) {
            $legend = 1;
        } else {
            $legend = 0;
        }
        if ($_POST['win'] == 'win') {
            $win = 1;
        } else {
            $win = 0;
        }
        GameModel::update($_SESSION['gameId'], $player, $legend, $win, $_POST['hero'], $_POST['opponent']);
        $_SESSION['gameInput'] = array(
            'legend' => $legend,
            'win' => $win,
            'hero' => $_POST['hero'],
            'opponent' => $_POST['opponent']
        );
        unset($_SESSION['gameId']);
        Redirect::to('/logging/list');
    }
    
    public static function removeFlipPage(){
        if($_POST['button'] == 'first'){
            $_SESSION['gamePage'] = 1;
        }else if($_POST['button'] == 'previous'){
            $_SESSION['gamePage'] = $_SESSION['gamePage'] - 1;
        }else if($_POST['button'] == 'next'){
            $_SESSION['gamePage'] = $_SESSION['gamePage'] + 1;
        }else {
            $_SESSION['gamePage'] = -1;
        }
        Redirect::to('/logging/list');
    }
}
