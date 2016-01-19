<?php
class AE_select {

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

        $data = $this->field['data'];
        $placeholder = (isset($this->field['placeholder']) && !is_array($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';
        if( isset( $this->field['label']) && $this->field['label'] !== '') {
            echo '<label for="'. $this->field['id'] .'">'. $this->field['label'] .'</label>';
        }
        echo '<select id="' . $this->field['id'] . '" name="' . $this->field['name'] .'" ' . $placeholder . ' class="regular-select ' . $this->field['class'] . '" >';
        foreach ($data as $key => $value) {
        ?>
            <option value="<?php echo $key ?>" <?php selected( $key, $this->value ); ?>>
                <?php echo $value ?>
            </option>
        <?php
        }
        echo '</select>';

    }//render

}
