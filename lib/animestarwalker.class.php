<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 22.09.2016
 * Time: 14:41
 */
class AnimeStarWalker extends Curl{
    public $AnimeUser;

    public $url_www;
    public $url_login_form;
    public $url_login_action;

    public $ProductList = []; // donor_shop_id => Product

    const MAX_COUNT_CICLE = 30;
    private $page_html = '';

    public function __construct($AnimeUser)
    {
        parent::__construct();
        $this->AnimeUser = $AnimeUser;

        $this->url_www = 'http://www.anime-star.com/';
        $this->url_login_form = $this->url_www.'login.html';
        $this->url_login_action = $this->url_www.'login.html?action=process';
    }

    public function parseProducts($cat_url)
    {
        $cur_cat_page_url = $cat_url;
        $this->ProductList = [];
        while(1)
        {
            $cat_html = $this->getPage($cur_cat_page_url);
            $this->parsePage($cat_html);
            $next_page_url = $this->getNextPageUrl($cat_html);
            if(empty($next_page_url)) break;
            $cur_cat_page_url = $next_page_url;
        }
        echo "ok, total are ".count($this->ProductList)." products \n";
    }

    public function getNextPageUrl($cat_html)
    {
        if(preg_match('/<a href="([^<,"]+)"[^>]+>\[Next/i', $cat_html, $patt)) return $patt[1];
        return '';
    }


    public function parsePage($cat_html){
        if(!preg_match_all('|(<div class="centerBoxContentsProducts centeredContent back.*?</div></div></div>)|s', $cat_html,  $patt)){
            // пишем в лог, что ничего не найдено. м.б. это пустая страница категорий?
            echo "[!] it very strange point... don't found any products on category page ...\n";
            return;
        }
        // распарсить товары на странице
        // заполнить данными ProductList
        foreach($patt[1] as $product){
            $Product = new Donor\Product();
            $Product->parseShortHtml($product);
            $this->ProductList[$Product->id]=$Product;
        }
    }



    public function getPage($cur_cat_page_url){
        $html = $this->get($cur_cat_page_url, $cur_cat_page_url);
        if(!$this->isValidAuthPage($html)){
            $this->tryLogin();
            $html = $this->get($cur_cat_page_url, $cur_cat_page_url);
        }
        return $html;
    }

    function isValidAuthPage($html){
        if(!preg_match('/main_page=logoff/', $html, $patt))
        {
            return false;
        }
        return true;
    }

    function isValidEndPage($html){
        if(!preg_match('|'.preg_quote("</html>").'|', $html, $patt))
        {
            return false;
        }
        return true;
    }

    function tryLogin(){
        $sleep_sec = 1;
        while(1){
            echo "tryLogin attept num: {$sleep_sec} ... \n";
            $html_login_form = $this->get($this->url_login_form, $this->url_login_form, [CURLOPT_FOLLOWLOCATION => true]);
            $html_after_post = $this->sendLoginForm($html_login_form);
            if($this->isValidAuthPage($html_after_post)) break;
            sleep($sleep_sec);
            // сообщение в лог
            file_put_contents('html_login_form_'.$sleep_sec.' '.date("m_d-H-i", time()).".html", $html_login_form);
            file_put_contents('html_after_post_'.$sleep_sec.' '.date("m_d-H-i", time()).".html", $html_after_post);
            if($sleep_sec++ > self::MAX_COUNT_CICLE)
            {
                // исключение - 30 попыток подряд залогиниться не увенчались успехом. сервер отдает странный результат,
                // страница которую возвращает сервер сохранена в файл try_login_failed_N_month_day_hour_min, где N - номер попытки

                throw new MyException("CAN'T LOGIN {$sleep_sec} TIMES ...", '1001');
            }
        }
    }

    function sendLoginForm($html_login_form)
    {
        if(!preg_match('/<input[^>]+name="securityToken" value="([a-z\d]+)"/', $html_login_form, $patt)){
            return '';
        }
        $security_token = $patt[1];

        $post_data = [
            'email_address' => $this->AnimeUser->email_address,
            'password' => $this->AnimeUser->password,
            'securityToken' => $security_token,
            'x' => rand(10,80),
            'y' => rand(7,27)
        ];

        $html = $this->post(
            $this->url_login_action,
            $this->url_login_form,
            $post_data,
            [CURLOPT_FOLLOWLOCATION => true]
        );

        return $html;
    }

    public function get($url, $ref='', $more_curl_init_data=[]){
        $sleep_sec = 1;
        while(1){

            $html = parent::get($url, $ref, $more_curl_init_data);
            if($this->isValidEndPage($html)) return $html;
            sleep($sleep_sec);
            // сообщение в лог
            echo "try get page num: {$sleep_sec} ... \n";
            file_put_contents('html_page_failed_'.$sleep_sec.' '.date("m_d-H-i", time()).".html", $html);
            if($sleep_sec++ > self::MAX_COUNT_CICLE)
            {
                throw new MyException("CAN'T GET PAGE [{$url}] {$sleep_sec} TIMES ...", '1002');
                // исключение - 30 попыток подряд получить полную html страницк не увенчались успехом. сервер отдает странный результат,
                // страница которую возвращает сервер сохранена в файл get_page_failed_N_month_day_hour_min, где N - номер попытки
            }
        }
    }

}
