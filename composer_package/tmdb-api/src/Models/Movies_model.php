<?php
namespace Local\TmdbApi\Models;

use \Local\TmdbApi\Database;

class Movies_model extends Database
{
    public $id;
    public $title;
    public $length;
    public $release_date;
    public $overview;
    public $poster_url;
    public $tmdb_id;
    public $tmdb_vote_avg;
    public $tmdb_vote_count;
    public $tmdb_url;
    public static $table = "movies";

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $data = $this->select(self::$table);
        return $data;
    }

    public function getById($id)
    {
        $data = $this->select(self::$table, ["id" => $id]);

        if(!empty($data[0])) {

            foreach($data[0] as $field => $value) {
                $this->{$field} = $value;
            }

            $data = $data[0];
        }

        return $data;
    }

    public function save()
    {
        $data = [];
        $data["title"] = $this->title;
        $data["length"] = $this->length;
        $data["release_date"] = $this->release_date;
        $data["overview"] = $this->overview;
        $data["poster_url"] = $this->poster_url;
        $data["tmdb_id"] = $this->tmdb_id;
        $data["tmdb_vote_avg"] = $this->tmdb_vote_avg;
        $data["tmdb_vote_count"] = $this->tmdb_vote_count;
        $data["tmdb_url"] = $this->tmdb_url;

        if(!empty($this->id)) {
            $this->update(self::$table, $data, ["id" => $this->id]);
        } else {
            $this->insert(self::$table, $data);
        }

        return $this->last_insert_id;
    }

    public function getByTMDBId($tmdb_id) 
    {
        $data = $this->select(self::$table, ["tmdb_id" => $tmdb_id]);

        if(!empty($data[0])) {

            foreach($data[0] as $field => $value) {
                $this->{$field} = $value;
            }

            $data = $data[0];
        }

        return $data;
    }
}