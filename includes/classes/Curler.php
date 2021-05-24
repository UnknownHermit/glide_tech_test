<?php
/**
 * Basic curler for posting data
 */
class Curler{
    private $url = "";
    private $options = Array();

    public function __construct($url, $options=Array()){
        $this->url = $url;
        $this->options = $options;
    }

    /**
     * Post data to the website provided upon creation of this class
     */
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
        $errorNo = curl_errno($ch);
        $errorMsg = curl_error($ch);
        curl_close($ch);

        if($errorNo != 0){
            print "$errorNo: $errorMsg";
            die();
        }

        return $response;
    }

}

?>