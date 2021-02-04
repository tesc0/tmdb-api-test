<?php
namespace Local\TmdbApi;

class Site
{
    public $api;

    public function __construct()
    {
        $this->api = new \Local\TmdbApi\API();
    }

    /**
     * Műfajok megszerzése
     */
    public function getAllGenres()
    {
        $genres = new \Local\TmdbApi\Genres($this->api);
        // themoviedb-n lévő műfajok
        $external = $genres->getGenres();
        // adatbázisban lévő műfajok
        $internal = $genres->listGenres();

        return ["internal_genres" => $internal, "external_genres" => $external];
    }

    /**
     * műfajok összehasonlítása: adatbázisból olvasott és apival szedett
     */
    public function compareGenres($genres_list_old, $genres_list_new)
    {
        $genres = new \Local\TmdbApi\Genres($this->api);
        $genres->compareGenres($genres_list_old, $genres_list_new);
    }

    /**
     * themoviedb-n lévő filmek olvasása
     */
    public function listMovies($limit = 20)
    {
        $movies = new \Local\TmdbApi\Movies($this->api);
        return $movies->getMovies($limit);
    }

    /**
     * leszedett filmes lista feldolgozása
     */
    public function processMovies($list, $limit = 0)
    {        
        $movies = new \Local\TmdbApi\Movies($this->api);
        $genres = new \Local\TmdbApi\Genres($this->api);
        //echo count($list);

        // ha a leszedett darabszám több, mint a kért, akkor a végéről szedjük le a plusz darabokat
        if(!empty($limit)) {
            if(count($list) > $limit) {
                for($i = count($list) - 1; $i >= $limit; $i--) {
                    unset($list[$i]);
                }
            }
        }
        //echo "<br>";
        //echo count($list);

        $loop = 0;
        foreach($list as $item) {
            //print_R($item);    

            //ha már a film szerepel, ne mentse el újra
            if(!empty($movies->checkMovie($item["id"]))) {
                continue;
            }

            //film részletei
            $movie_details = $movies->getDetails($item["id"]);        
            
            //stáblista
            $credits = $movies->getCredits($item["id"]);
            $crew = $credits["crew"];

            //film mentése
            $movie_id = $movies->saveMovie($movie_details);       
                    
            $genre_ids = $item["genre_ids"];
            if(!empty($genre_ids)) {
                //műfajok mentése
                foreach($genre_ids as $genre_id) {
                    $genre_row = $genres->getGenreId($genre_id);
                    $movies->addGenres($genre_row["id"], $movie_id);
                }
            }

            //rendező mentése
            foreach($crew as $crew_member) {
                if($crew_member["job"] == "Director") {
                    $movies->addDirector($crew_member, $movie_id);
                }
            }

         /*   
            if($loop == 5) {
                die();
            }
            */
            $loop++;
        }
    }
}