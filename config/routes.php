<?php

$routes->get('/', function() {
    PlayerController::loginPage();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/login', function() {
    PlayerController::loginPage();
});

$routes->post('/login', function() {
    PlayerController::login();
});

$routes->get('/main', function() {
    PlayerController::navPage();
});

$routes->get('/logging', function() {
    GameController::loggingPage();
});

$routes->post('/logging', function() {
    GameController::addResult();
});

$routes->get('/groups', function() {
    TeamController::managePage();
});

$routes->post('/groups', function() {
    TeamController::create();
});

$routes->get('/analysis', function() {
    GameController::stats();
});

$routes->post('/analysis', function() {
    GameController::statsRefresh();
});

$routes->get('/logging/list', function() {
    GameController::removeList();
});

$routes->post('/logging/list', function() {
    GameController::remove();
});

$routes->get('/groups/group', function() {
    TeamController::teamPage();
});

$routes->post('/groups/group', function() {
    TeamController::teamPageInit();
});

$routes->post('/groups/group/kick', function() {
   TeamController::kick(); 
});

$routes->get('/groups/group/leave', function() {
    TeamController::leave();
});

$routes->post('/groups/group/invite', function() {
    TeamController::invite();
});

$routes->post('/groups/join', function() {
    TeamController::inviteChoice();
});

$routes->post('/logging/list/edit', function() {
    GameController::editInit();
});

$routes->get('/logging/list/edit', function() {
    GameController::editPage();
});

$routes->post('/editSave', function() {
    GameController::editSave();
});

$routes->post('/logging/list/edit/save', function() {
    GameController::editSave();
});

$routes->get('/logout', function() {
    PlayerController::logout();
});

$routes->post('/groups/pageflip', function() {
    TeamController::manageSetPage();
});

$routes->post('/groups/group/pageflip', function(){
    TeamController::teamSetPage();
});

$routes->post('/logging/remove', function(){
    GameController::removeFlipPage();
});

$routes->post('/logging/list/pageflip', function(){
    GameController::removeFlipPage();
});

$routes->post('/groups/join_open', function() {
    TeamController::joinOpen();
});

$routes->post('/groups/group/settings', function() {
    TeamController::setClosed();
});

$routes->post('/groups/group/disband', function() {
    TeamController::disband();
});