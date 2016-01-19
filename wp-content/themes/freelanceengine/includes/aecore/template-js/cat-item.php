<?php
	/**
	 * This template user in AECore to render underscore template category item
	 * the template is sample, for using with custom purpose you should override it
	*/
	$class_no_icon = $this->params['use_icon'] ? '' : 'no-icon';
	$class_no_color = $this->params['use_color'] ? '' : 'no-color';
	$class_no_hierarchical = (isset($this->params['hierarchical'])&& $this->params['hierarchical'] == 0)? 'no-hierarchical' : 'sort-handle';						
?>
<script id="<?php echo $this->params['taxonomy'] ?>_item_template" type="text/template">
	<div class="container">
		<div class="<?php echo $class_no_hierarchical;?>"></div>
		<div class="controls controls-2">
			<a class="button act-open-form" rel="{{= term_id }}"  title="<?php _e('Add sub tax for this tax', ET_DOMAIN) ?>">
				<span class="icon" data-icon="+"></span>
			</a>
			<a class="button act-del" rel="{{= term_id }}">
				<span class="icon" data-icon="*"></span>
			</a>
		</div>
		<div class="input-form input-form-1">
			<?php if($this->params['use_color']) {?>
			<div class="cursor">
				<span class="flag" style="background-color:{{= color }};"></span>
				<div class="color-panel" style="display:none">
					<# arr_colors = ['#000000', '#1abc9c', '#3498db', '#be8cbc', '#a4bedf', '#fff146', '#e67e22', '#4e6c8a', '#9fd4a9', '#68d0f0', '#bdc3c7', '#16a085', '#2980b9', '#a286ba', '#8dbdd8', '#f5c506', '#d35400', '#34495e', '#60bf74', '#00b2d7', '#95a5a6', '#2ecc71', '#0078a0', '#9b59b6', '#8fd7d4', '#ec9e03', '#e74c3c', '#2c3e50', '#12a252', '#0090b0', '#7f8c8d', '#27ae60', '#004c7d', '#8e44ad', '#6ba5a3', '#f99138', '#c0392b', '#212f3d', '#24753c', '#004350']  #>
					<# for (var i = 0; i < arr_colors.length; i++) { #>
					<div class="color-item" data="{{=arr_colors[i]}}" >
						<span style="background-color:{{=arr_colors[i]}}" class="flags"></span>
					</div>
					<# } #>
					<div class="custom-color"><label>Set your custom color</label><input class="input-color color-picker " placeholder="e.g: #fbfbfb" value="" /> </div>
				</div>
			</div>
			<?php } ?>
			<?php if($this->params['use_icon']) {?>
			<div class="icon trigger" data="{{= icon }}"><i class="fa {{= icon }}"></i></div>
			<?php } ?>
			<input style="color:{{= color }};" class="bg-grey-input tax-name <?php echo $class_no_icon.' '.$class_no_color ?>" data-tax="<?php echo $this->params['taxonomy'] ?>" name="name" rel="{{= term_id }}" type="text" value="{{= name }}">
		</div>
	</div>
	<ul>
	</ul>
</script>