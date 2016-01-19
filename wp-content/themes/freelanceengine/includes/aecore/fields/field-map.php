<?php
class AE_map {

    /**
     * Field Constructor.
     *
     * @param array $field 
     * - id
     * - name 
     * - placeholder 
     * - readonly 
     * - class 
     * - title 
     * @param $value 
     * @param $parent
     * @since AEFramework 1.0.0
    */
    function __construct( $field = array(), $value ='', $parent ) {

        //parent::__construct( $parent->sections, $parent->args );
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;

    }

    /**
     * Field Render Function.
     *
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @since AEFramework 1.0.0
    */
    function render() {
        $value = wp_parse_args( $this->value, array('latitude' => 10.828248, 'longitude' => 106.629127 , 'address' => 'Hồ Chí Minh, Việt Nam', 'style'=>null) );
        extract($value);
    ?>
        <div class="map-setting" id="map-setting-<?php echo $this->field['id']; ?>" data-name="<?php echo $this->field['name'] ?>" >
                <input value="<?php echo $address; ?>" type="text" class="address clearfix" name="address" placeholder="<?php _e("Enter map address", ET_DOMAIN); ?>" />
                <input value="<?php echo $latitude; ?>" type="hidden"  name="latitude" class="latitude" />
                <input value="<?php echo $longitude; ?>" type="hidden"  name="longitude" class="longitude" />
            <span class="group-desc">Select a style.</span><br>
                <?php
                $style_list = AE_Mapstyle::get_instance()->get_list_style();
                echo '<select id="' . $this->field['id'] . '_map_style" name="style" class="map_style_select" >';
                foreach ($style_list as $key => $value) {
                    ?>
                    <option data-code='<?php echo $value['code']  ?>' value="<?php echo $key ?>" <?php selected( $key, $this->value['style'] ); ?>>
                        <?php echo $value['name'] ?>
                    </option>
                <?php
                }
                echo '</select>';
                ?>
            <style type="text/css">.map{height:300px !important;width:100%!important;margin-top:10px!important;}</style>
            <div id="map-<?php echo $this->field['id']; ?>" class="map" ></div>
        </div>
    <?php 

    }//render

}