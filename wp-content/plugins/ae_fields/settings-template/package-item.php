<?php
	$item	=	$this->item;
	/**
	 * This template item in AECore to render list of pack
	 * the template is sample, for using with custom purpose you should override it
	*/
?>
<li class="pack-item item" id="pack_<?php echo $item->ID; ?>" data-ID="<?php echo $item->ID; ?>">
	<div class="sort-handle"></div>
	<span><?php echo $item->post_title ?></span>  
	<span><?php echo $item->field_for ?></span>  
	<span><?php echo $item->field_type ?></span>  
	<?php echo $item->post_content ?>						
	<div class="actions">
		<a href="#" title="Edit" class="icon act-edit" rel="<?php echo $item->ID; ?>" data-icon="p"></a>
		<a href="#" title="Delete" class="icon act-del" rel="<?php echo $item->ID; ?>" data-icon="D"></a>
	</div>
</li>