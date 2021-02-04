<?php
namespace Local\TmdbApi\Models;

use \Local\TmdbApi\Database;

class Directors_model extends Database
{
    public $id;
    public $name;
    public $biography;
    public $tmdb_id;
    public $dob;
    public static $table = "directors";

    public function __construct()
    {
        parent::__construct();
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

    public function save()
    {
        $data = [];
        $data["name"] = $this->name;
        $data["biography"] = $this->biography;
        $data["tmdb_id"] = $this->tmdb_id;
        $data["dob"] = $this->dob;

        if(!empty($this->id)) {
            $this->update(self::$table, $data, ["id" => $this->id]);
        } else {
            $this->insert(self::$table, $data);
        }

        return $this->last_insert_id;
    }
}