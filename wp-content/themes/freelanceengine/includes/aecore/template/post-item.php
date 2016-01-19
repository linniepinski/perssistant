<?php
	$item	=	$this->item;
	/**
	 * This template user in AECore to render list of pack
	 * the template is sample, for using with custom purpose you should override it
	*/
?>
<li class="pack-item item" id="pack_<?php echo $item->ID; ?>" data-ID="<?php echo $item->ID; ?>">
	<span class="" style="background:<?php echo $item->qa_badge_color ?>; width:10px;height:10px;margin-right:10px;"></span>
	<span><?php echo $item->post_title ?> </span>  
	<?php printf(__("%d points", ET_DOMAIN), $item->qa_badge_point); ?>						
	<div class="actions">
		<a href="#" title="Edit" class="icon act-edit" rel="<?php echo $item->ID; ?>" data-icon="p"></a>
		<a href="#" title="Delete" class="icon act-del" rel="<?php echo $item->ID; ?>" data-icon="D"></a>
	</div>
</li>