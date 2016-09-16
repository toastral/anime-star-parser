<?php
class Curl{
    public $cookie_file;
    public $useragent;

    public function __construct()
    {
        $this->cookie_file = COOKIE_PATH.'/cookie.txt';
        #$this->useragent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36 OPR/38.0.2220.41';
        #$this->useragent = 'Mozilla/5.0 (Linux; U; Android 4.4.4; Nexus 5 Build/KTU84P) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30';
        $this->useragent = 'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.04';
    }

    public function getBaseCurlData(){
        $data = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_COOKIEJAR => "$this->cookie_file",
            CURLOPT_COOKIEFILE => "$this->cookie_file",
            CURLOPT_USERAGENT => $this->useragent,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 30,
        ];
        return $data;
    }

    public function get($url, $ref, $more_curl_init_data=[])
    {
echo "\n get=$url\n";
        if(defined('TIME_OUT')) { echo "TIMEOUT start\n"; sleep(TIME_OUT);  echo "TIMEOUT stop\n"; }
        $curl = curl_init();
        $data = $this->getBaseCurlData($url, $ref);
        $data += $more_curl_init_data;
        $data += [
            CURLOPT_URL => $url,
            CURLOPT_REFERER => $ref,
        ];
        curl_setopt_array($curl, $data);
        $response = curl_exec($curl);
        return $response;
    }

    public function post($url, $ref, $post_data=[], $more_curl_init_data=[])
    {
echo "\n post=$url\n";
        if(defined('TIME_OUT')) { echo "TIMEOUT start\n"; sleep(TIME_OUT);  echo "TIMEOUT stop\n"; }
        $curl = curl_init();
        $data = $this->getBaseCurlData($url, $ref);
        $data += $more_curl_init_data;
        $data += [
            CURLOPT_URL => $url,
            CURLOPT_REFERER => $ref,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post_data,
        ];
        curl_setopt_array($curl, $data);
        $response = curl_exec($curl);
        return $response;
    }

}