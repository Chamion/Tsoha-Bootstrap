<?php

$routes->get('/', function() {
    PlayerController::loginPage();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/etusivu', function() {
    PlayerController::loginPage();
});

$routes->post('/etusivu', function() {
    PlayerController::login();
});

$routes->get('/paasivu', function() {
    PlayerController::navPage();
});

$routes->get('/kirjaus', function() {
    GameController::loggingPage();
});

$routes->post('/kirjaus', function() {
    GameController::addResult();
});

$routes->get('/ryhmat', function() {
    TeamController::managePage();
});

$routes->post('/ryhmat', function() {
    TeamController::create();
});

$routes->get('/analyysi', function() {
    GameController::stats();
});

$routes->post('/analyysi', function() {
    GameController::statsRefresh();
});

$routes->get('/poista', function() {
    GameController::removeList();
});

$routes->post('/poista', function() {
    GameController::remove();
});

$routes->get('/ryhma1', function() {
    TeamController::teamPage();
});

$routes->post('/ryhma1', function() {
    TeamController::teamPageInit();
});

$routes->post('/kick', function() {
   TeamController::kick(); 
});

$routes->get('/leave', function() {
    TeamController::leave();
});

$routes->post('/invite', function() {
    TeamController::invite();
});

$routes->post('/join', function() {
    TeamController::inviteChoice();
});

$routes->post('/edit', function() {
    GameController::editInit();
});

$routes->get('/edit', function() {
    GameController::editPage();
});

$routes->post('/editSave', function() {
    GameController::editSave();
});

$routes->get('/logout', function() {
    PlayerController::logout();
});