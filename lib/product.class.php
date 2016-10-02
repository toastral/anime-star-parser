<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 16.09.2016
 * Time: 9:29
 */
namespace Donor;

class Product{
    public $id;
    public $url='';
    public $price=0.0000;
    public $model='';
    public $name='';
    public $brand_title='';
    public $brand_url='';
    public $description='';
    public $img_url='';

    public $our_cate_id=0;
    public $alias_for_graceful_url='';

    public function parseShortHtml($front_block_html){
        if(preg_match('|class="musheji_img"[^"]+"([^"]+)"|', $front_block_html, $patt))
            $this->url = $patt[1];

        if(preg_match('|-([\d]+)\.htm|', $this->url, $patt))
            $this->id = $patt[1];

        if(preg_match('|class="musheji_name"[^"]+"([^"]+)"|', $front_block_html, $patt))
            $this->name = $patt[1];

        if(preg_match('|>Model: ([^<]+)<|', $front_block_html, $patt))
            $this->model = $patt[1];

        if(preg_match('|class="musheji_price">\$([^<]+)<|', $front_block_html, $patt))
            $this->price = $patt[1];

        $this->makeGracefulAlias();
    }


    public function parseFullHtml($product_html){
        if(preg_match('/hidden" name="products_id" value="([\d]+)"/', $product_html, $patt))
            $this->id = $patt[1];
        if(preg_match('/link rel="canonical" href="([^"]+)"/', $product_html, $patt))
            $this->url = $patt[1];
        if(preg_match('/<div id="productPrices"[^\$]+\$([\d\.]+)</', $product_html, $patt))
            $this->price = $patt[1];
        if(preg_match('/<li>Model: <span>([^<>]+)</', $product_html, $patt))
            $this->model = $patt[1];

        if(preg_match('/mu_productName" class="name-type bot-border">([^<>]+)</', $product_html, $patt))
            $this->name = $patt[1];

        if(preg_match('/Products Brand:&nbsp;<span><a[^>]+>([^<>]+)</', $product_html, $patt))
            $this->brand_title = $patt[1];
        if(preg_match("/Products Brand:&nbsp;<span><a href='([^']+)'/", $product_html, $patt))
            $this->brand_url = $patt[1];
        if(preg_match('/productDescription" class="productGeneral biggerText">(.*?)<\/div>/U', $product_html, $patt))
            $this->description = strip_tags($patt[1]);

        $this->description = preg_replace('/&nbsp;/', ' ', $this->description);
        $this->description = trim($this->description);
        $this->description = preg_replace('/[\s]+/', ' ', $this->description);

        if(preg_match('/<a id="jqzoom" href="([^"]+)"/U', $product_html, $patt))
            $this->img_url = 'http://www.anime-star.com/'.$patt[1];

        $this->makeGracefulAlias();

    }

    public function makeGracefulAlias(){
        if(strlen($this->name)>0){
            $tmp = strtolower($this->name);
            $tmp = preg_replace('/[^a-z\s]+/', ' ', $tmp);
            $tmp = trim($tmp);
            $tmp = preg_replace('/[\s]+/', '-', $tmp);
            $this->alias_for_graceful_url = $this->id.'-'.$tmp;
        }
    }


}