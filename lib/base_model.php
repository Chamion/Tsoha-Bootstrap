<?php

class BaseModel {

    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct() {
        // Käydään assosiaatiolistan avaimet läpi
        /* foreach($attributes as $attribute => $value){
          // Jos avaimen niminen attribuutti on olemassa...
          if(property_exists($this, $attribute)){
          // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
          $this->{$attribute} = $value;
          }
          } */
    }

    public function errors() {
        // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
        $errors = array();

        foreach ($this->validators as $validator) {
            $errors = array_merge($errors, $this->{$validator}());
        }

        return $errors;
    }

    public function validate_string($str, $errors) {
        $errors = self::validate_string_null($str, $errors);
        $errors = self::validate_string_tags($str, $errors);
        return $errors;
    }

    public function validate_string_null($str, $errors) {
        if ($str == null) {
            $errors[] = 'Input must not be empty.';
        }
        return $errors;
    }

    public function validate_string_tags($str, $errors) {
        $stripped = strip_tags($str);
        if ($stripped != $str) {
            $errors[] = 'Input must not include html/php tags.';
        }
        return $errors;
    }

}
