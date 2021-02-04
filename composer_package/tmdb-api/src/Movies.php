<?php
namespace Local\TmdbApi;


class Movies
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }
    
    /**
     * themoviedb-n lévő filmek olvasása
     */
    public function getMovies($limit)
    {
        if($limit < 20) {
            $page = 1;
        } else {
            $page = ceil($limit / 20);
        }

        $result = [];
        if($page > 1) {
            for($i = 0; $i < $page; $i++) {
                $this->api->curlCall("movie/top_rated", "&page=" . ($i + 1));
                $data = $this->api->getJson();

                $result = array_merge($result, $data["results"]);
            }
        }
        //print_R($result);
        return $result;
    }

    /**
     * themoviedb-s részletek
     */
    public function getDetails($movie_id) 
    {
        $this->api->curlCall("movie/" . $movie_id);
        return $this->api->getJson();
    }

    /**
     * themoviedb-s stáblistás adatok
     */
    public function getCredits($movie_id) {
        $this->api->curlCall("movie/" . $movie_id . "/credits");
        return $this->api->getJson();
    }

    /**
     * film adatainak lementése adatbázisba
     */
    public function saveMovie($details, $movie_id = 0)
    {
        $movies_model = new \Local\TmdbApi\Models\Movies_model();
        if(!empty($movie_id)) {
            $movies_model->id = $movie_id;
        }
        $movies_model->title = $details["title"];    
        $movies_model->release_date = $details["release_date"];
        $movies_model->overview = $details["overview"];
        $movies_model->poster_url = $details["poster_path"];
        $movies_model->tmdb_id = $details["id"];
        $movies_model->tmdb_vote_avg = $details["vote_average"];
        $movies_model->tmdb_vote_count = $details["vote_count"];
        $movies_model->tmdb_url = '/movie/' . $details["id"];
        $movies_model->length = $details["runtime"];
        $new_id = $movies_model->save();

        return $new_id;
    }

    /**
     * filmhez műfajok mentése
     */
    public function addGenres($genre_id, $movie_id)
    {
        $m_g_model = new \Local\TmdbApi\Models\Movie_genres_model();
        $m_g_model->genre_id = $genre_id;
        $m_g_model->movie_id = $movie_id;
        $m_g_model->save();
    }

    /**
     * adatbázisban tárolt filmek
     */
    public function savedMovies()
    {
        $movie_model = new \Local\TmdbApi\Models\Movies_model();
        return $movie_model->getAll();
    }

    /**
     * Rendező feldolgozása
     */
    public function addDirector($director_info, $movie_id)
    {
        $directors = new \Local\TmdbApi\Directors($this->api);
        $director_id = $directors->checkDirector($director_info["id"]);

        if(!empty($director_id)) {
            $directors->addDirectorToMovie($director_id, $movie_id);
        } else {
            $director_id = $directors->addDirector($director_info);
            $directors->addDirectorToMovie($director_id, $movie_id);
        }
    }

    public function checkMovie($movie_tmdb_id)
    {
        $movies_model = new \Local\TmdbApi\Models\Movies_model();
        $movies_row = $movies_model->getByTMDBId($movie_tmdb_id);

        $id = 0;
        if(!empty($movies_row["id"])) {
            $id = $movies_row["id"];
        }

        return $id;
    }

    /**
     * filmhez társított műfajok törlése
     */
    public function deleteGenres($movie_id)
    {
        $m_g_model = new \Local\TmdbApi\Models\Movie_genres_model();
        $m_g_model->deleteByMovieId($movie_id);
    }

    /**
     * filmhez társított rendezők törlése
     */
    public function deleteDirectors($movie_id)
    {
        $m_d_model = new \Local\TmdbApi\Models\Movie_directors_model();
        $m_d_model->deleteByMovieId($movie_id);
    }
}