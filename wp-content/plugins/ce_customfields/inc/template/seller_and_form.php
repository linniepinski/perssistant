<div class="wrap-meta-fields tab hide seller_tab_div" id="seller_tab">
	<h5 class=" title font-quicksand"><?php _e("List Seller's Fields",ET_DOMAIN);?></h5>
	<div id="sfield_lists">
		<ul class="pay-plans-list seller-fields-list sortable ui-sortable" id="list_seller_fields">
			<?php

			$fields = (array)CE_Fields::get_seller_fields();
			$list_seller_fields 	= array();

			if( $fields ) {
				foreach($fields as $key => $field){
					$name 					= $field['field_name'];
					$list_seller_fields[$name] 	= $field;
					$status 				= (isset($field['tax_status']) && $field['tax_status'] == 1) ? 1 : 0;
					?>
					<li data="<?php echo $name;?>" id="field_key_<?php echo $name;?>" class="item" action="abc">
						<div class="sort-handle"></div>
						<span class="field_label"><?php echo $field['field_label'];?> </span>
						<?php echo  stripcslashes($field['field_des']);?><div class="actions">
							<a data-icon="p" rel = "<?php echo $name;?>" class="icon act-edit" title="<?php _e('Edit',ET_DOMAIN);?>"></a>
							<a data-icon="D" rel = "<?php echo $name;?>" class="icon act-del" title="<?php _e('Delete',ET_DOMAIN);?>" ></a>
						</div>
					</li>
					<?php
				}

			} else {
				echo '<li class="no-result">';
				_e('Do not have fields regitered',ET_DOMAIN);
				echo '</li>';
			}
			?>
		</ul>
	</div>
	<div class="desc">
		<form class="add-seller-field form-edit-field">
			<div class="row-item">
             	<div class="title font-quicksand"> <?php _e('Add a seler\'s field',ET_DOMAIN);?></div>
             	<div class="clearfix"></div>
            </div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field Label',ET_DOMAIN);?></div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="option-item bg-grey-input " required placeholder= "<?php _e('Field label',ET_DOMAIN);?>" id="field_label" name="field_label" type="text">
					</div>
				</div>
			</div>
			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field Name',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="option-item bg-grey-input auto-name" placeholder= "<?php _e('Field name',ET_DOMAIN);?>" name="field_name" required type="text">
					</div>
				</div>
			</div>
			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Placeholder text',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="option-item bg-grey-input " placeholder= "<?php _e('Placeholder text',ET_DOMAIN);?>" name="field_pholder" type="text">
					</div>
				</div>
			</div>

			<div class="row-item">
				<div  class=" label title font-quicksand"><?php _e('Field description',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<textarea name="field_des" id="field_des" rows="10" cols="30"></textarea>
					</div>
				</div>
			</div>
			<input type="hidden" name="action" value="ce-add-field-seller" />
			<div class="row-item">
				<div  class=" label title font-quicksand"><?php _e('Field type',ET_DOMAIN);?> </div>
				<div class="form-item">
					<div class="select-style et-button-select">

						<select name="field_type" style="z-index: 10; opacity: 0;">
							<option value="text"> <?php _e('Text',ET_DOMAIN);?></option>
							<!-- <option value="textarea"> <?php _e('Text box',ET_DOMAIN);?></option> -->
							<option value="date"> <?php _e('Date',ET_DOMAIN);?></option>
							<option value="url"> <?php _e('Url',ET_DOMAIN);?></option>
							<option value="checkbox"> <?php _e('Checkbox',ET_DOMAIN);?></option>
							<option value="radio"> <?php _e('Radio',ET_DOMAIN);?></option>
							<option value="select"> <?php _e('Dropdown',ET_DOMAIN);?></option>
						</select>

					</div>
				</div>
				<ul class="list-options"></ul>
			</div>
			<div class="row-item">
				<div  class=" label title font-quicksand"><?php _e('Required',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="checkbox"  name="field_required" id="sfield_required" /> <label for="sfield_required"><?php _e(' Check this if field is required.',ET_DOMAIN);?></label>
					</div>
				</div>
			</div>

			<div class="row-submit">
				<button class="btn-button btn-primary engine-submit-btn add_payment_plan">
					<span>&nbsp; <?php _e('Add Field',ET_DOMAIN);?> &nbsp; </span><span data-icon="+" class="icon"></span>
				</button>
			</div>
		</form>

	</div><!-- end div.desc !-->
	<script id="list_seller_field_data" type="application/json">
		<?php echo json_encode(array_values($list_seller_fields));?>
	</script>
	<script id="field_item_field" type="text/template">
		<div class="sort-handle"></div>
		<span class="field_label">{{ field_label }} </span>
		 {{ field_des }} <div class="actions">
			<a title="Edit" class="icon act-edit" rel="{{ field_name }}" data-icon="p"></a>
			<a title="Delete" class="icon act-del" rel="{{ field_name }}" data-icon="D"></a>
		</div>
	</script>
</div> <!-- end div.wrap-meta-fields !-->