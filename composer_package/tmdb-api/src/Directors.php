<?php
namespace Local\TmdbApi;

class Directors
{
    private $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * Rendező ellenőrzése tmdb-s azonosító alapján, szerepel-e már az adatbázisban
     */
    public function checkDirector($director_tmdb_id)
    {        
        require_once "models/Directors_model.php";

        $directors_model = new \Local\TmdbApi\Models\Directors_model();
        $director_row = $directors_model->getByTMDBId($director_tmdb_id);

        $id = 0;
        if(!empty($director_row["id"])) {
            $id = $director_row["id"];
        }

        return $id;
    }

    /**
     * Új rendező mentése filmhez
     */
    public function addDirectorToMovie($director_id, $movie_id)
    {
        require_once "models/Movie_directors_model.php";

        $m_d_model = new \Local\TmdbApi\Models\Movie_directors_model();
        $m_d_model->movie_id = $movie_id;
        $m_d_model->director_id = $director_id;
        $m_d_model->save();
    }

    /**
     * Új rendező mentése
     */
    public function addDirector($director_data)
    {
        $data = $this->getDirectorInfo($director_data["id"]);

        require_once "models/Directors_model.php";

        $directors_model = new \Local\TmdbApi\Models\Directors_model();
        $directors_model->name = $data["name"];
        $directors_model->dob = empty($data["birthday"]) ? '1001-01-01' : $data["birthday"];
        $directors_model->biography = $data["biography"];
        $directors_model->tmdb_id = $director_data["id"];
        return $directors_model->save();
    }

    public function getDirectorInfo($director_tmdb_id)
    {
        $this->api->curlCall("person/" . $director_tmdb_id);
        $result = $this->api->getJson();
        //print_R($result);
        return $result;
    }

}