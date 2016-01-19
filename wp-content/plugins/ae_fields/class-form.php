<?php
class AE_Form
{
    public static function input($type, $name, $value = '', $option = array()) {
    	return $this->$type($name, $value, $option);
    }
    public static function text($name, $value = '', $option = array()) {
    }
    public static function password($name, $option = array()) {
    }
    public static function hidden($name, $value = '', $option = array()) {
    }
    public static function email($name, $value = '', $option = array()) {
    }
    public static function url($name, $value = '', $option = array()) {
    }
    public static function file($name, $value = '', $option = array()) {
    }
    public static function textarea($name, $value = '', $option = array()) {
    }
    public static function number($name, $value = '', $option = array()) {
    }
    
    public static function select($name, $value = '', $option = array()) {
    }
    
    public static function checkbox($name, $value = '', $option = array()) {
    }
    public static function radio($name, $value = '', $option = array()) {
    }
    public static function image($name, $value = '', $option = array()) {
    }
    public static function submit($name, $value = '', $option = array()) {
    }
    
}
