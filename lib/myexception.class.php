<?php
/*
 * Собственный класс исключений
 * **/
class MyException extends Exception{
    public $my_code='';

    public function __construct($message, $code = 0, Exception $previous = null) {
        $this->my_code = $code;
        $code = 0;
        parent::__construct($message, $code, $previous);
    }
    public function getMyCode() {
        return $this->my_code;
    }
}