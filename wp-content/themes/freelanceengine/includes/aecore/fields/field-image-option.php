<?php
class AE_image_option {

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
        echo '<div id="' . $this->field['id'] . '" class="image-select-option ' . $this->field['class'] . '">';
        foreach ($data as $key => $value) {
            $is_selected = ($this->value==$key);
        ?>
            <span class="image-option-item <?php if($is_selected){echo "selected";} ?>">
                <label for="<?php echo $this->field['id']."_".$key ?>">
                    <img width="150" height="150" src="<?php echo get_stylesheet_directory_uri().$value["img"] ?>" alt=""/>
                </label>
                <input id="<?php echo $this->field['id']."_".$key ?>" <?php if($is_selected){echo "checked";} ?> type="radio" name="<?php echo $this->field['name']; ?>" value="<?php echo $key ?>">
            </span>
        <?php
        }
        echo '</div>';

    }//render

}
