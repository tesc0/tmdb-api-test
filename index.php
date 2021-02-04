<?php
//ini_set("display_errors", 1);
if(!file_exists("vendor/autoload.php")) {
    echo "'composer install' parancsot futtasd le a csomag telepítéséhez";
} else {

    require "vendor/autoload.php";

    // cron futása
    if(!empty($_REQUEST["type"])) {
        if($_REQUEST["type"] == "cron") {
            $cron = new \Local\TmdbApi\Cron();
            if(method_exists($cron, $_REQUEST["method"])) {
                \Local\TmdbApi\Cron::{$_REQUEST["method"]}();
                die();
            } else {
                die("unknown method");
            }
            
        }
    }

    $site = new \Local\TmdbApi\Site();

    /**
     * műfajok
     */
    $genres = $site->getAllGenres();
    $genres_old = $genres["internal_genres"];
    $genres_new = $genres["external_genres"];

    echo "<pre>";

    /**
     * összehasonlítás, esetleg mentés
     */
    $site->compareGenres($genres_old, $genres_new);

    /**
     * FILMEK
     */
    $limit = 210;
    $list = $site->listMovies($limit);
    $site->processMovies($list, $limit);
    //print_R($list);

    echo "siker";
}