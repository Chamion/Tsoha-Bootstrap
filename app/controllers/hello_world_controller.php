<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      View::make('helloworld.html');
    }
    
    public static function etusivu(){
        View::make('suunnitelmat/etusivu.html');
    }
    
    public static function paasivu(){
        View::make('suunnitelmat/paasivu.html');
    }
    
    public static function kirjaus(){
        View::make('suunnitelmat/kirjaus.html');
    }
    
    public static function ryhmat(){
        View::make('suunnitelmat/ryhmat.html');
    }
    
    public static function analyysi(){
        View::make('suunnitelmat/analyysi.html');
    }
  }
