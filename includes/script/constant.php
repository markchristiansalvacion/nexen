<?php


    /**
     * Define Separator Aliases
     */
    define("URL_SEPARATOR","/");
    define("DS",DIRECTORY_SEPARATOR);


    /**
     * Other Defined Aliases
     */
    define("EmptyString", "");
    define ("STR_VOID",'');

   /*
    * MONTH DEFINITION
    */
      define("C_MONTH", [
        "A"=>["month"=>"01","day"=>"31"],
        "B"=>["month"=>"02","day"=>"28"],
        "C"=>["month"=>"03","day"=>"31"],
        "D"=>["month"=>"04","day"=>"30"],
        "E"=>["month"=>"05","day"=>"31"],
        "F"=>["month"=>"06","day"=>"30"],
        "G"=>["month"=>"07","day"=>"31"],
        "H"=>["month"=>"08","day"=>"31"],
        "I"=>["month"=>"09","day"=>"30"],
        "J"=>["month"=>"10","day"=>"31"],
        "K"=>["month"=>"11","day"=>"30"],
        "L"=>["month"=>"12","day"=>"31"]
      ]);

      /*
       * YEAR DEFINITION
       */

      define("C_YEAR", [
        0=>2020,
        1=>2021,
        2=>2022,
        3=>2023,
        4=>2024,
        5=>2025,
        6=>2026,
        7=>2027,
        8=>2028,
        9=>2029,
        10=>2030
      ]);


?>