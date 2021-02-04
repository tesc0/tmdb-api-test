<?php
namespace Local\TmdbApi;

class API {

    private $headers;
    private $url = "https://api.themoviedb.org/3/";
    private $token = "d1ebda78ccc9fff46ca424016786de2d";
    private $result, $resultJson;

    public function __construct()
    {
        $this->setAuthHeader();
        $this->headers[] = 'Content-Type: application/json;charset=utf-8';
    }

    private function setAuthHeader()
    {
        $this->headers[] = "Authorization: Bearer " . $this->token;
    }

    public function getJson()
    {
        return $this->resultJson;
    }

    public function curlCall($action, $param = "")
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url . $action. "?api_key=" . $this->token . $param);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $this->result = $result = curl_exec($curl);
        //print_r(curl_getinfo($curl));
        $this->resultJson = json_decode($result, true);

        curl_close($curl);
    }

    public function showResult()
    {
        var_Dump($this->result);
    }
}