<?php
class AE_Form
{
    public static function input($type = 'text', $name, $value = '', $attr = array()) {
        $attribute = '';
        if($type == 'checkbox' || $type == 'radio') {
            $class = 'class="field-control input-item"';
            if($type == 'checkbox' && isset($attr['required'])){
                unset($attr['required']);
                $class = 'class="field-control input-item required"';
            }
        }else {
            $class = 'class="field-control input-item form-control text-field"';    
        }        
        echo '<input '.$class.' ' . self::attributes($attr) . ' name="' . $name . '" value="' . $value . '" type="' . $type . '" ' . $attribute . ' >';
    }
    public static function text($name, $value = '', $attr = array()) {
        echo self::input('text', $name, $value, $attr);
    }
    public static function url($name, $value = '', $attr = array()) {
        echo self::input('url', $name, $value, $attr);
    }
    public static function hidden($name, $value = '', $attr = array()) {
        echo self::input('hidden', $name, $value, $attr);
    }

    public static function textarea($name, $value = '', $attr = array()) {
        echo '<textarea rows="5" class="form-control input-item autosize" ' . self::attributes($attr) . ' name="'.$name.'" >'. $value .'</textarea>';
    }

    public static function select($name, $data, $selected = '', $attr = array()) {
?>
		<select <?php echo self::attributes($attr); ?> name="<?php echo $name; ?>">
			<?php foreach ($data as $key => $value) { ?>
			<option value="<?php echo $key; ?>"><?php echo $value ?></option>
			<?php } ?>
		</select>
	<?php
    }
    public static function multi_select($name, $data, $value = '', $attr = array()) {
?>
		<select <?php echo self::attributes($attr); ?> multiple name="<?php echo $name; ?>">
			<option value="option_1">Option_1</option>
			<option value="option_2">Option_2</option>
		</select>
	<?php
    }
    
    public static function radio($name, $checked = array(), $attr = array()) {
        $data = get_terms( $name, array('hide_empty' => false));
        foreach ($data as $key => $term) { 
            if(is_array($checked) && in_array($term->term_id, $checked)) {
                $attr['checked'] = "checked";
            }else{
                if(isset($attr['checked'])) unset($attr['checked']);
            }
            //echo '<div class="radio">';
            echo '<label class="radio-inline">';
            echo self::input('radio', $name, $term->term_id, $attr);
            echo $term->name;
            echo '</label>';
            //echo '</div>';
        }
    }
    
    public static function checkbox($name, $checked = array(), $attr = array()) {
        $data = get_terms( $name, array('hide_empty' => false));
        foreach ($data as $key => $term) { 
            echo '<label class="checkbox-inline">';
            if(is_array($checked) && in_array($term->term_id, $checked)) {
                $attr['checked'] = "checked";
            }else{
                if(isset($attr['checked'])) unset($attr['checked']);
            }
            echo self::input('checkbox', $name, $term->term_id, $attr);
            echo $term->name;
            echo '</label>';
        }
    }
    
    public static function ae_tax($name, $selected = '', $attr = array()) {
        ae_tax_dropdown($name, array(
            'attr' => self::attributes($attr),
            'class' => 'chosen chosen-single chosen-select tax-item',
            'hide_empty' => false,
            'hierarchical' => true,
            'id' => $name,
            'show_option_all' => false,
            'selected' => $selected
        ));
    }

    public static function ae_tax_multi($name, $selected = '', $attr=array()) {
        $attr['multiple'] = true;
        ae_tax_dropdown($name, array(
            'attr' => self::attributes($attr),
            'class' => 'chosen chosen-select multi-tax-item tax-item',
            'hide_empty' => false,
            'hierarchical' => true,
            'id' => $name,
            'show_option_all' => false,
            'selected' => $selected
        ));
    }
    
    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     * @return string
     */
    public static function attributes($attributes) {
        $html = array();
        
        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ((array)$attributes as $key => $value) {
            $element = self::attributeElement($key, $value);
            
            if (!is_null($element)) $html[] = $element;
        }
        
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }
    
    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
    public static function attributeElement($key, $value) {
        if (is_numeric($key)) $key = $value;
        
        if (!is_null($value)) return $key . '="' . $value . '"';
    }
}
