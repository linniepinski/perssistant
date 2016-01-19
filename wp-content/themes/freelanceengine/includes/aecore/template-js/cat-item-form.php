<?php
	$class_no_icon = $this->params['use_icon'] ? '' : 'no-icon';
	$class_no_color = $this->params['use_color'] ? '' : 'no-color';	
?>
<script id="<?php echo $this->params['taxonomy'] ?>_item_form" type="text/template">
	<div class="container">
		<div class="controls controls-2">
			<button class="button" type="submit"><span class="icon" data-icon="+"></span></button>
		</div>
		<div class="input-form input-form-1 color-default">
			<?php if($this->params['use_color']) {?>
			<div class="cursor color-0" data="#000000">
				<span class="flag" style="background-color:#000;"></span>
				<div class="color-panel" style="display:none">
					<# arr_colors = ['#000000', '#1abc9c', '#3498db', '#be8cbc', '#a4bedf', '#fff146', '#e67e22', '#4e6c8a', '#9fd4a9', '#68d0f0', '#bdc3c7', '#16a085', '#2980b9', '#a286ba', '#8dbdd8', '#f5c506', '#d35400', '#34495e', '#60bf74', '#00b2d7', '#95a5a6', '#2ecc71', '#0078a0', '#9b59b6', '#8fd7d4', '#ec9e03', '#e74c3c', '#2c3e50', '#12a252', '#0090b0', '#7f8c8d', '#27ae60', '#004c7d', '#8e44ad', '#6ba5a3', '#f99138', '#c0392b', '#212f3d', '#24753c', '#004350']  #>
					<# for (var i = 0; i < arr_colors.length; i++) { #>
					<div class="color-item" data="{{=arr_colors[i]}}">
						<span style="background-color:{{=arr_colors[i]}}" class="flags"></span>
					</div>
					<# } #>
					<div class="custom-color"><label>Set your custom color</label><input class="input-color color-picker " placeholder="e.g: #fbfbfb" value="" /> </div>
				</div>
			</div>
			<?php } ?>
			<?php if($this->params['use_icon']) {?>
			<div class="icon trigger" data="fa-map-markder"><i class="fa fa-map-markder"></i></div>
			<?php } ?>
			<input style="color:#000;" class="bg-grey-input tax-name <?php echo $class_no_icon.' '.$class_no_color ?>" data-tax="<?php echo $this->params['taxonomy'] ?>" name="name" placeholder="<?php _e('Add a category', ET_DOMAIN) ?>" type="text" />
		</div>
	</div>
</script>