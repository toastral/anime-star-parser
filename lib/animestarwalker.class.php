<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 15.09.2016
 * Time: 12:41
 */
class AnimeStarWalker extends Curl{
    public $AnimeUser;

    public $url_www;
    public $url_login_form;
    public $url_login_action;

    public $a_product_urls = [];
    public $a_product_obj = [];

    public function __construct($AnimeUser)
    {
        parent::__construct();
        $this->AnimeUser = $AnimeUser;

        $this->url_www = 'http://www.anime-star.com/';
        $this->url_login_form = $this->url_www.'login.html';
        $this->url_login_action = $this->url_www.'login.html?action=process';
    }

    function parseProductsUrlByCatUrl($cat_url){
        // получить html = $cat_url
        // получить ссылки на продукты
        // если есть следующая страница вызвать самого себя

        $this->a_product_urls = [];
        $this->_parseProductsUrlByCatUrl($cat_url);
        $this->a_product_urls = array_unique($this->a_product_urls);
    }

    // рекурсия
    function _parseProductsUrlByCatUrl($cat_url){
echo "parse category: $cat_url\n";
        $cat_html = $this->get($cat_url, $cat_url);
        $is_valid_cat_html = $this->loginWatchDog($cat_html);
        if(!$is_valid_cat_html) $cat_html = $this->get($cat_url, $cat_url);
        $a_product_urls = $this->getProdcutUrlsFromCategory($cat_html);
        $this->a_product_urls = array_merge($a_product_urls, $this->a_product_urls);

        $next_cat_url = $this->getNextPage($cat_html);
        if(strlen($next_cat_url)>0){
            $this->_parseProductsUrlByCatUrl($next_cat_url);
        }
    }

    // возвращаем true - если не было повторной авторизации, false - в противном случае
    function loginWatchDog($html){
        if(!$this->weAreIn($html)){
echo "try to login...\n";
            $this->logIn(
                $this->get($this->url_login_form, $this->url_login_form, [CURLOPT_FOLLOWLOCATION => true])
            );
            return false;
        }
        return true;
    }

    function getProdcutUrlsFromCategory($cat_html){
        if(preg_match_all('/<div class="centerBoxContentsProducts.*?<a href="([^>]+)">.*?<\/div><\/div><\/div>/s', $cat_html, $patt)){
            return $patt[1];
        }
        return [];
    }

    public function getNextPage($cat_html)
    {
        if(preg_match('/<a href="([^<,"]+)"[^>]+>\[Next/i', $cat_html, $patt)) return $patt[1];
        return '';
    }

    function parseProducts(){
        $this->a_product_obj=[];
        foreach($this->a_product_urls as $product_url){

            $product_html = $this->get($product_url, $product_url);
            $is_valid_cat_html = $this->loginWatchDog($product_html);
            if(!$is_valid_cat_html) $product_html = $this->get($product_url, $product_url);
            $Product = new Donor\Product();
            $Product->parseHtml($product_html);
            $this->a_product_obj[] = $Product;
        }
    }


    function weAreIn($any_html)
    {
        if(preg_match('/main_page=logoff/', $any_html, $patt))
        {
            return true;
        }
        return false;
    }

    function logIn($html_login_form)
    {
        if(!preg_match('/<input[^>]+name="securityToken" value="([a-z\d]+)"/', $html_login_form, $patt)){
            throw new MyException("NOT FOUND SECURITY TOKEN", '1000');
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

        if(!$this->weAreIn($html))
            throw new MyException("CAN'T LOGIN", '1001');

    }

    function logOut()
    {

    }
}
