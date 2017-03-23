<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  $routes->get('/etusivu', function() {
    HelloWorldController::etusivu();
  });
  
  $routes->get('/paasivu', function() {
    HelloWorldController::paasivu();
  });
  
  $routes->get('/kirjaus', function() {
    HelloWorldController::kirjaus();
  });
  
  $routes->get('/ryhmat', function() {
    HelloWorldController::ryhmat();
  });
  
  $routes->get('/analyysi', function() {
    HelloWorldController::analyysi();
  });
  
  $routes->get('/poista', function() {
    HelloWorldController::poista();
  });
  
  $routes->get('/ryhma1', function() {
    HelloWorldController::ryhma1();
  });