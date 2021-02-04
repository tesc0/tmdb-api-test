<?php
namespace Local\TmdbApi\Models;

use \Local\TmdbApi\Database;

class Movie_genres_model extends Database
{
    public $id;
    public $movie_id;
    public $genre_id;
    public static $table = "movie_genres";

    public function __construct()
    {
        parent::__construct();
    }

    public function save()
    {
        $data = [];
        $data["movie_id"] = $this->movie_id;
        $data["genre_id"] = $this->genre_id;

        if(!empty($this->id)) {
            $this->update(self::$table, $data, ["id" => $this->id]);
        } else {
            $this->insert(self::$table, $data);
        }

        return $this->last_insert_id;
    }

    public function deleteByMovieId($movie_id)
    {
        $this->delete(self::$table, ["movie_id" => $movie_id]);
    }
}
