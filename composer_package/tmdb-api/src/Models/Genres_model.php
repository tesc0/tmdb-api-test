<?php
namespace Local\TmdbApi\Models;

use \Local\TmdbApi\Database;

class Genres_model extends Database
{
    public $id;
    public $name;
    public $tmdb_id;
    public static $table = "genres";

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

    public function getByTMDBId($tmdb_id) {

        $data = $this->select(self::$table, ["tmdb_id" => $tmdb_id]);

        if(!empty($data[0])) {

            foreach($data[0] as $field => $value) {
                $this->{$field} = $value;
            }

            $data = $data[0];
        }

        return $data;
    }

    public function getAll() 
    {
        $data = $this->select(self::$table);
        return $data;
    }

    public function save() 
    {
        $data = [];
        $data["tmdb_id"] = $this->tmdb_id;
        $data["name"] = $this->name;

        if(!empty($this->id)) {
            $this->update(self::$table, $data, ["id" => $this->id]);
        } else {
            $this->insert(self::$table, $data);
        }
    }
}