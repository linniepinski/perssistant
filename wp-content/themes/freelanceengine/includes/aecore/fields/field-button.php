<?php
class AE_button {

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
        if( isset( $this->field['label']) && $this->field['label'] !== '') {
            echo '<label for="'. $this->field['id'] .'">'. $this->field['label'] .'</label>';
        }
        $class = '';
        if( isset( $this->field['class'] ) && $this->field['class'] != ''){
            $class = $this->field['class'];
        }
        $id = '';
        if ( isset( $this->field['id'] ) && $this->field['id'] != '' ){
            $id = $this->field['id'];
        }
        $value = 'Button';
        if( isset( $this->field['value'] ) && $this->field['value'] != '' ){
            $value = $this->field['value'];
        }
        $icon = 'fa fa-bolt';
        if( isset( $this->field['icon'] ) && $this->field['icon'] != '' ){
            $icon = $this->field['icon'];
        }
        $action = '';
        if( isset( $this->field['action'] ) && $this->field['action'] != '' ){
            $action = $this->field['action'];
        }
        $button_type = 'button';
        if( isset( $this->field['type'] ) && $this->field['type'] != '' ){
            $button_type = $this->field['type'];
        }
        $html_code = '<div class="backend-button-wrapper">';
        $html_code .= '<button type="'.$button_type.'" class="backend-button backend-action-button '.$class.'" id ="'.$id.'" data-action="'.$action.'" >';
        $html_code .= $value;
        $html_code .= ' <i class="'.$icon.'"></i>';
        $html_code .= '</button>';
        $html_code .= '<span class="backend-button-notification"></span>';
        $html_code .= '</div>';
        echo $html_code;
    }


}
