<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 16.09.2016
 * Time: 9:29
 */
namespace donor;

class Product{
    public $id;
    public $url;
    public $price;
    public $model;
    public $name;
    public $brand_title;
    public $brand_url;
    public $description;
    public $img_url;

    public function parseHtml($product_html){
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
        if(preg_match('/mu_productName" class="name-type bot-border">([^<>]+)</', $product_html, $patt))
            $this->name = $patt[1];
        if(preg_match('/Products Brand:&nbsp;<span><a[^>]+>([^<>]+)</', $product_html, $patt))
            $this->brand_title = $patt[1];
        if(preg_match("/Products Brand:&nbsp;<span><a href='([^']+)'/", $product_html, $patt))
            $this->brand_url = $patt[1];
        if(preg_match('/productDescription" class="productGeneral biggerText">(.*?)<\/div>/U', $product_html, $patt))
            $this->description = strip_tags($patt[1]);
        if(preg_match('/<a id="jqzoom" href="([^"]+)"/U', $product_html, $patt))
            $this->img_url = $patt[1];
    }


}