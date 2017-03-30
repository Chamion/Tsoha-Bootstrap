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

$routes->get('/malliinit', function() {
    MalliController::malliInit();
});

$routes->get('/malli', function() {
    MalliController::loggingPage();
});

$routes->post('/malli', function() {
    MalliController::addResult();
});

$routes->get('/mallilistaus', function() {
    MalliController::removeList();
});

$routes->post('/mallilistaus', function() {
    MalliController::remove();
});

$routes->get('/mallihaku', function() {
    MalliController::findPage();
});

$routes->post('/mallihaku', function() {
    MalliController::find();
});