<?php

/**
 * Created by PhpStorm.
 * User: nguyenvanduocit
 * Date: 3/4/2015
 * Time: 11:33 AM
 */
class AE_Mapstyle
{
    public static $instance;
    private static $styles = array(
        'style_0'=>array(
            'name' => "Default",
            'code' => '[]'
        ),
        'style_1'=>array(
            'name' => "Style 1",
            'code' => '[{"featureType":"administrative.locality","elementType":"all","stylers":[{"hue":"#2c2e33"},{"saturation":7},{"lightness":19},{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":-2},{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"hue":"#e9ebed"},{"saturation":-90},{"lightness":-8},{"visibility":"simplified"}]},{"featureType":"transit","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":10},{"lightness":69},{"visibility":"on"}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":-78},{"lightness":67},{"visibility":"simplified"}]}]'
        ),
        'style_2'=>array(
            'name' => "Style 2",
            'code' => '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#112251"}]},{"featureType":"administrative.land_parcel","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#ffce00"},{"saturation":"28"},{"lightness":"75"},{"gamma":"2.00"},{"weight":"0.50"}]},{"featureType":"landscape","elementType":"labels","stylers":[{"color":"#112251"},{"weight":"0.05"}]},{"featureType":"landscape","elementType":"labels.icon","stylers":[{"visibility":"on"}]},{"featureType":"poi.attraction","elementType":"all","stylers":[{"visibility":"on"},{"color":"#ffce00"},{"lightness":"60"}]},{"featureType":"poi.attraction","elementType":"labels","stylers":[{"visibility":"simplified"},{"color":"#ff5e47"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.government","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"poi.park","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#5ed17c"},{"saturation":"-20"},{"lightness":"40"},{"gamma":"1.00"}]},{"featureType":"poi.park","elementType":"labels","stylers":[{"color":"#5ed17c"},{"lightness":"-60"},{"gamma":"1"}]},{"featureType":"poi.park","elementType":"labels.icon","stylers":[{"visibility":"simplified"}]},{"featureType":"poi.place_of_worship","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"poi.school","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.sports_complex","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":"-200"},{"lightness":"50"},{"gamma":"1"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"},{"lightness":"75"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.airport","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"transit.station.airport","elementType":"labels.text.fill","stylers":[{"color":"#112251"}]},{"featureType":"transit.station.bus","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"transit.station.rail","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit.station.rail","elementType":"labels.icon","stylers":[{"visibility":"simplified"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7ac4e5"},{"visibility":"on"},{"lightness":"40"},{"gamma":"1"}]}]'
        ),
        'style_3'=>array(
            'name' => "Style 3",
            'code' => '[{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"on"},{"saturation":-100},{"lightness":20}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"on"},{"saturation":-100},{"lightness":40}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"saturation":-10},{"lightness":30}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"visibility":"simplified"},{"saturation":-60},{"lightness":10}]},{"featureType":"landscape.natural","elementType":"all","stylers":[{"visibility":"simplified"},{"saturation":-60},{"lightness":60}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"},{"saturation":-100},{"lightness":60}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"},{"saturation":-100},{"lightness":60}]}]'
        ),
        'style_4'=>array(
            'name' => "Style 4",
            'code' => '[{"featureType":"all","stylers":[{"saturation":0},{"hue":"#e7ecf0"}]},{"featureType":"road","stylers":[{"saturation":-70}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"visibility":"simplified"},{"saturation":-60}]}]'
        ),
        'style_5'=>array(
            'name' => "Style 5",
            'code' => '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#6195a0"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#e6f3d6"},{"visibility":"on"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#f4d2c5"},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"labels.text","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#f4f4f4"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#787878"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#eaf6f8"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#eaf6f8"}]}]'
        ),
        'style_6'=>array(
            'name' => "Style 6",
            'code' => '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#a2bcc7"},{"visibility":"on"}]}]'
        ),
        'style_7'=>array(
            'name' => "Style 7",
            'code' => '[{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"color":"#ff5a5f"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels.text","stylers":[{"visibility":"on"},{"saturation":"0"},{"lightness":"0"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"lightness":"-15"},{"color":"#ff5a5f"}]},{"featureType":"administrative.locality","elementType":"labels.text.stroke","stylers":[{"lightness":"55"},{"gamma":"0.33"},{"weight":"5.50"},{"color":"#ffffff"},{"saturation":"-13"},{"visibility":"on"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.fill","stylers":[{"color":"#ff5a5f"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.stroke","stylers":[{"weight":"4.70"},{"lightness":"40"},{"color":"#ffffff"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"lightness":"38"},{"color":"#f5f6f5"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"lightness":"-2"},{"color":"#ecedec"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#ff5a5f"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"weight":"5.12"},{"gamma":"1.52"},{"lightness":"100"},{"color":"#ffffff"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#bce4c1"}]},{"featureType":"poi.park","elementType":"labels.icon","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"lightness":"27"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"lightness":"0"},{"weight":"0.26"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"on"},{"saturation":"20"}]},{"featureType":"road.highway","elementType":"labels.icon","stylers":[{"gamma":"1.42"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry.fill","stylers":[{"lightness":"-16"},{"saturation":"67"}]},{"featureType":"road.highway.controlled_access","elementType":"labels.icon","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"lightness":"20"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"lightness":"28"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"lightness":"28"}]},{"featureType":"transit.station.airport","elementType":"labels.text.fill","stylers":[{"color":"#7b0051"}]},{"featureType":"transit.station.bus","elementType":"labels.text.fill","stylers":[{"color":"#7b0051"}]},{"featureType":"transit.station.rail","elementType":"labels.text.fill","stylers":[{"color":"#7b0051"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"saturation":"-30"},{"lightness":"18"},{"color":"#b2f1ec"}]}]'
        )
    );

    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get_current_style()
    {
        $current_style_id = ae_get_option('map_center_default', null);
        if ( ($current_style_id !== null) && isset($current_style_id['style']) ) {
            $current_style_id  = $current_style_id['style'];
            $style_object = $this->get_style($current_style_id);
            if (!is_wp_error($style_object)) {
                return $style_object;
            }
        }
        return null;
    }

    function get_list_style($type = ''){
        if($type == 'json')
        {
            $object = array();
            foreach(self::$styles as $key => $style){
                $object[$key]=json_decode($style['code'], true);
            }
            return json_decode($object);
        }
        else{
            return self::$styles;
        }
    }

    function get_style($index)
    {
        if (isset(self::$styles[$index])) {
            $style_object = json_decode(self::$styles[$index]['code'], true);
            return $style_object;
        }
        return new WP_Error("map_style", "Style not found.");
    }
}