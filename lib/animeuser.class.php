<?php
/**
 * Created by PhpStorm.
 * User: work
 * Date: 15.09.2016
 * Time: 12:42
 */
class AnimeUser{
    public $email_address;
    public $password;

    public function __construct($email_address, $password)
    {
        $this->email_address = $email_address;
        $this->password = $password;
    }
}
