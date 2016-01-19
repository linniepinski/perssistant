<?php
class AE_switch {

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
        $args   =   $this->field;

        $label_1 = __("Enable", ET_DOMAIN);
        $label_2 = __("Disable", ET_DOMAIN);
        if(isset($args['label_1'])) $label_1 = $args['label_1'];
        if(isset($args['label_2'])) $label_2 = $args['label_2'];

        if( !$this->value ){
            if(isset($args['label_desc'])) {
                echo '<label for="'. $this->field['id'] .'">'. $this->field['label_desc'] .'</label>';
            }
            echo '<div class="inner no-border btn-left">
                    <div class="payment"> 
                        <div class="button-enable font-quicksand switch">
                            <a href="#" rel="'. $args['name'] .'" title="" class="toggle-button deactive selected">
                                <span>'. $label_2 .'</span>
                            </a>
                            <a href="#" rel="'. $args['name'] .'" title="" class="toggle-button active ">
                                <span>'. $label_1 .'</span>
                            </a>
                            <input type="hidden" name="'. $args['name'] .'" value="0" />
                        </div>
                    </div>
                </div>';
        }
        else echo '<div class="inner no-border btn-left">
                    <div class="payment"> 
                        <div class="button-enable font-quicksand switch">
                            <a href="#" rel="'. $args['name'] .'" title="" class="toggle-button deactive ">
                                <span>'. $label_2 .'</span>
                            </a>
                            <a href="#" rel="'. $args['name'] .'" title="" class="toggle-button active selected ">
                                <span>'. $label_1 .'</span>
                            </a>
                            <input type="hidden" name="'. $args['name'] .'" value="1" />
                        </div>
                    </div>
                </div>';

    }//render

}
