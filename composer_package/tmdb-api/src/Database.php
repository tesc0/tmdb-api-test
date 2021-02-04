<?php
namespace Local\TmdbApi;

class Database {

    private $link;
    private $data;
    private $query;
    protected $last_insert_id;

    public function __construct()
    {
        $this->link = new \PDO("mysql:dbname=tmdb;host=127.0.0.1", "root", "", array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    public function getLastInsertId()
    {
        return $this->last_insert_id;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setQuery($sql)
    {
        $this->query = $sql;
    }

    public function insert($table, $data)
    {

        $fields_part = "";
        $param_part = "";
        $index = 0;
        foreach($data as $field => $item) {

            if($index > 0) {
                $fields_part .= ", ";
                $param_part .= ", ";
            }

            $fields_part .= $field;
            $param_part .= ":" . $field;
            $index++;
        }

        $query = "INSERT INTO " . $table . " (" . $fields_part . ") VALUES (" . $param_part . ")";

        $this->setQuery($query);
        $this->setData($data);
        $this->exec();

        $this->last_insert_id = $this->link->lastInsertId();
    }

    public function select($table, $where = [])
    {

        $query = "SELECT * FROM " . $table;

        if(!empty($where)) {
            $query .= " WHERE " ;
            $index = 0;
            foreach($where as $field => $condition) {
                if($index > 0) {
                    $query .= " AND ";
                }
                $query .= $field . " LIKE '" . $condition . "'";

                $index++;
            }
        }

        $this->setQuery($query);
        
        $statement = $this->exec();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }

    public function update($table, $data, $where)
    {
        $set_sql = "";
        $where_sql = "";
        $index = 0;
        foreach($data as $field => $item) {

            if($index > 0) {
                $set_sql .= ", ";
            }

            $set_sql .= $field . " = '" . $item . "'";
            $index++;
        }

        $index = 0;
        foreach($where as $field => $item) {

            if($index > 0) {
                $where_sql .= " AND ";
            }

            $where_sql .= $field . " LIKE '" . $field . "'";
            $index++;
        }

        $query = "UPDATE " . $table . " SET " . $set_sql . " WHERE " . $where_sql;
        $this->setQuery($query);
        $this->setData($data);
        
        $statement = $this->exec();
    }

    public function delete($table, $where)
    {
        $query = "DELETE * FROM " . $table;

        if(!empty($where)) {
            $query .= " WHERE " ;
            $index = 0;
            foreach($where as $field => $condition) {
                if($index > 0) {
                    $query .= " AND ";
                }
                $query .= $field . " LIKE '" . $condition . "'";

                $index++;
            }
        }

        $this->setQuery($query);
        
        $statement = $this->exec();
    }

    public function exec()
    {
        
        $query = $this->link->prepare($this->query);
        $query->execute($this->data);

        return $query;
    }
}