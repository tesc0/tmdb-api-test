<?php
namespace Local\TmdbApi;

class Genres
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function getGenreId($genre_tmdb_id)
    {
        $movie_model = new \Local\TmdbApi\Models\Genres_model();
        return $movie_model->getByTMDBId($genre_tmdb_id);
    }

    /**
     * műfajok listájának megszerzése api-n keresztül
     */
    public function getGenres()
    {        
        $this->api->curlCall("genre/movie/list");
        $result = $this->api->getJson();
        //print_R($result);
        return $result["genres"];
    }

    /**
     * műfajok listázása adatbázisból
     */
    public function listGenres() 
    {
        $genres_model = new \Local\TmdbApi\Models\Genres_model();
        $genres_all = $genres_model->getAll();

        return $genres_all;
    }

    /**
     * műfajok összehasonlítása
     */
    public function compareGenres($genres_list_old, $genres_list_new)
    {
        if(count($genres_list_old) != count($genres_list_new)) {

            foreach($genres_list_old as $genre_indb) {
                $genres_temp[] = $genre_indb["tmdb_id"];
            }

            foreach($genres_list_new as $key => $genre_item) {

                if(empty($genres_list_old)) {

                    $this->addGenre($genre_item["id"], $genre_item["name"]);

                } else {

                    foreach($genres_list_old as $genre_indb) {
            
                        if(in_array($genre_item["id"], $genres_temp)) {
                            break;
                        }

                        $this->addGenre($genre_item["id"], $genre_item["name"]);
                        break;
                    }
                }
            }
        }
    }

    /**
     * műfaj mentése
     */
    public function addGenre($genre_tmdb_id, $genre_name) 
    {
        $genres_model = new \Local\TmdbApi\Models\Genres_model();
        $genres_model->tmdb_id = $genre_tmdb_id;
        $genres_model->name = $genre_name;
        $genres_model->save();
    }
}