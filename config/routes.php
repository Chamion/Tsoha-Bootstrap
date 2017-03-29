<?php

$routes->get('/', function() {
    HelloWorldController::index();
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
    HelloWorldController::paasivu();
});

$routes->get('/kirjaus', function() {
    GameController::loggingPage(null);
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
    HelloWorldController::ryhma1();
});

$routes->post('/ryhma1', function() {
    TeamController::teamPage();
});
