<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 15.09.2016
 * Time: 8:31
 */

require 'config.php';
require 'lib/curl.class.php';
require 'lib/myexception.class.php';
require 'lib/animeuser.class.php';
require 'lib/animestarwalker.class.php';
require 'lib/product.class.php';

/*
$html = file_get_contents('category.html');
preg_match_all('/(<div class="centerBoxContentsProducts.*?<\/div><\/div><\/div>)/s', $html, $patt);
$a_res = $patt[1];
if(count){
    foreach($a_res as $html_product){}
}

function parseProduct($html_product){}
*/


try{

    /*
    $Curl = new Curl();
    $url='http://www.anime-star.com/login.html';
    $ref=$url;
    $html = $Curl->get($url, $ref, [CURLOPT_FOLLOWLOCATION => true]);
    file_put_contents('login.html', $html);
    if(!preg_match('/<input[^>]+name="securityToken" value="([a-z\d]+)"/', $html, $patt)){
        echo "can't find securityToken\n";
        throw new MyException("NOT FOUND SECURITY TOKEN", '1000');
    }
    $security_code = $patt[1];
    */

    $AnimeUser = new AnimeUser(ANIME_USER_EMAIL, ANIME_USER_PASSWORD);
    $AnimeStarWalker = new AnimeStarWalker($AnimeUser);

    $product_html = file_get_contents('product.html');
    $Product = new donor\Product();
    $Product->parseHtml($product_html);

    print_r($Product);


exit;
    foreach($a_links as $our_product_id => $donor_cat_url){
        $AnimeStarWalker->parseProductsUrlByCatUrl($cat_url);
        $AnimeStarWalker->a_product_urls;
    }

    /*
    $cat_html = file_get_contents('category.html');
    print_r($AnimeStarWalker->getProdcutUrlsFromCategory($cat_html));
    */

    //$cat_html = file_get_contents('category.html');

    $cat_url = 'http://www.anime-star.com/anime-poster-c-47_52/';
    //$cat_url = 'http://www.anime-star.com/kunai-c-1_153/';

    print_r($AnimeStarWalker->a_product_urls);

exit;

    //$AnimeStarWalker->loginWatchDog($cat_html);


    /*
    $url = $AnimeStarWalker->url_login_form;
    $html_login_form = $AnimeStarWalker->get($url,$url, [CURLOPT_FOLLOWLOCATION => true]);
    if(!$AnimeStarWalker->weAreIn($html_login_form)){
        $AnimeStarWalker->logIn($html_login_form);
    }

    // получаем все url-ы для парсинга и обходим их в цикле

    $category_url = 'http://www.anime-star.com/all-anime-action-figures-c-6_8/';
    $category_html = $AnimeStarWalker->get($category_url, $AnimeStarWalker->url_www);
    file_put_contents('category.html', $category_html);
    */

    /*
    $cat_html = file_get_contents('category.html');
    echo $AnimeStarWalker->getNextPage($cat_html);
    */

    //

}catch(MyException $e){
    print_r($e);
    //echo json_encode( array('error' => $e->getMessage(), 'code' => $e->getMyCode()) );
}

