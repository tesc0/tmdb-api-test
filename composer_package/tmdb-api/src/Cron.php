<?php
namespace Local\TmdbApi;

class Cron {

    /**
     * Már elmentett filmek adatainak frissítése
     */
    public static function updateMovies()
    {
        $movies = new \Local\TmdbApi\Movies("");
        $genres = new \Local\TmdbApi\Genres("");

        $movies_all = $movies->savedMovies();

        if(!empty($movies_all)) {
            foreach($movies_all as $movie) {

                //film részletei
                $movie_details = $movies->getDetails($movie["id"]);        
                
                //stáblista
                $credits = $movies->getCredits($movie["id"]);
                $crew = $credits["crew"];

                //film mentése
                $movie_id = $movies->saveMovie($movie_details);       
                        
                $genre_ids = $movie["genre_ids"];
                if(!empty($genre_ids)) {
                    $movies->deleteGenres($movie_id);
                    //műfajok mentése
                    foreach($genre_ids as $genre_id) {
                        $genre_row = $genres->getGenreId($genre_id);
                        $movies->addGenres($genre_row["id"], $movie_id);
                    }
                }

                //rendező mentése
                $movies->deleteDirectors($movie_id);
                foreach($crew as $crew_member) {
                    if($crew_member["job"] == "Director") {
                        $movies->addDirector($crew_member, $movie_id);
                    }
                }
            }
        } else {
            echo "no movie to update";
        }
    }
}