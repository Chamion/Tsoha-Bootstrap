<?php

  class HelloWorld extends BaseModel{

    public static function say_hi(){
      return 'Hello World!';
    }
    
    public static function firstGame(){
        $query = DB::connection()->prepare('SELECT * FROM Game WHERE id = 1;');
        $query->execute();
        $row = $query->fetch();
    }
  }
