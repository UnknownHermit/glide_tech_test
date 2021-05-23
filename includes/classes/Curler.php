<?php

class Curler{
    private $url = "";
    private $options = Array();

    public function __construct($url, $options=Array()){
        $this->url = $url;
        $this->options = $options;
    }

    public function doPost($post){
        $ch = curl_init($this->url);
        if(!empty($this->options)){
            foreach($this->options as $k => $v){
                curl_setopt($ch, $k, $v);
            }
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

}

?>