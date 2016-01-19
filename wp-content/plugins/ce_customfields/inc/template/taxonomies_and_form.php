<div class="wrap-taxs-fields tab hide taxs_tab_div" id="taxs_tab">
	<h5 class=" title font-quicksand"><?php _e("List Ad's taxonomies",ET_DOMAIN);?></h5>
	<div id="taxonomy_lists">
		<ul class="pay-plans-list tax-list sortable add-fields ui-sortable">
			<?php
			$taxs = (array)CE_Fields::get_taxs();
			$list_tax 	= array(0 => '');
			if( is_array($taxs) && !empty($taxs) ) {
				foreach($taxs as $key => $tax){
					$status 		= (isset($tax['tax_status']) && $tax['tax_status'] == 1) ? 1 : 0;
					?>
					<li data="<?php echo $key;?>" id="tax_key_<?php echo $key;?>" class="item <?php if(!$status) echo 'off';?>">
						<div class="sort-handle"></div>
						<span class="tax_label"><?php echo  stripcslashes($tax['tax_label']);?> </span>
						<?php echo stripcslashes($tax['tax_des']);?><div class="actions">
							<a data-icon="Q" rel = "<?php echo $key;?>" class="icon power"  title="<?php if($status) _e('Turn off',ET_DOMAIN); else _e('Turn on',ET_DOMAIN);?>" href="#"></a>
							<a data-icon="p" rel = "<?php echo $key;?>" class="icon act-edit" title="Edit"></a>
							<a data-icon="D" rel = "<?php echo $key;?>" class="icon act-del" title="Delete" ></a>
						</div>
					</li>
					<?php
				}
			} else {
				echo '<li class="no-result">';
				_e('Do not have taxonomies regitered',ET_DOMAIN);
				echo '</li>';
			}
			?>
		</ul>
	</div>

	<div class="desc">
		<form class="add-tax">
			<div class="form-item">
             	<div class="title font-quicksand"> <?php _e('Add a Taxonomy',ET_DOMAIN);?></div>
             	<div class="clearfix"></div>
            </div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Taxonomy Label',ET_DOMAIN);?></div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="option-item bg-grey-input " placeholder= "<?php _e('Taxonomy label',ET_DOMAIN);?>" id="tax_label" name="tax_label" type="text">
					</div>
				</div>
			</div>
			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Taxonomy slug',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="option-item bg-grey-input " placeholder= "<?php _e('Taxonomy slug',ET_DOMAIN);?>"  name="tax_slug" type="text">
					</div>
				</div>
			</div>
			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Taxonomy Name',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input class="option-item bg-grey-input auto-name" placeholder= "<?php _e('Taxonomy name',ET_DOMAIN);?>" name="tax_name" type="text">
					</div>
				</div>
			</div>


			<div class="row-item">
				<div  class=" label title font-quicksand"><?php _e('Taxonomy description',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<textarea name="tax_des" id="tax_des" rows="10" cols="30"></textarea>
					</div>
				</div>
			</div>
			<input type="hidden" name="action" value="ce-add-tax" />
			<div class="row-item">
				<div class="label title font-quicksand"><?php _e('Taxonomy type',ET_DOMAIN);?></div>
				<div class="form-item">
					<div class="select-style et-button-select">
						<select style="z-index: 10; opacity: 0;" name="tax_type" class="valid">
							<option value="select"> <?php _e('Dropdown option',ET_DOMAIN);?></option>
							<option value="checkbox"><?php _e('Checkbox',ET_DOMAIN);?></option>
							<option value="radio"> <?php _e('Radio',ET_DOMAIN);?></option>
						</select>
					</div>
				</div>
			</div>
			<div class="row-submit">
					<button class="btn-button btn-primary engine-submit-btn add_payment_plan">
						<span>&nbsp; <?php _e('Add Taxonomy',ET_DOMAIN);?> &nbsp; </span><span data-icon="+" class="icon"></span>
					</button>
			</div>
		</form>

	</div><!-- end div.desc !-->
	<script id="list_tax_data" type="application/json">
		<?php echo json_encode(array_values($taxs));?>
	</script>


	<script id="field_item_tax" type="text/template">
		<div class="sort-handle"></div>
		<span class="tax_label">{{ tax_label }} </span>
		 {{ tax_des }} <div class="actions">
			<a title="toggle status" class="icon power" rel="{{ id }}" data-icon="Q"></a>
			<a title="Edit" class="icon act-edit" rel="{{ id }}" data-icon="p"></a>
			<a title="Delete" class="icon act-del" rel="{{ id }}" data-icon="D"></a>
		</div>
	</script>
	<!-- template edit a field!-->
	<script type="application/json" id="template_add_field">
		<form class="add-field form-edit-field">
			<div class="form-item">
             	<div class="title font-quicksand"><?php _e('Edit Field', ET_DOMAIN);?></div>
             	<div class="clearfix"></div>
            </div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field Label',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="field_label"  value="{{ field_label }}" class="option-item bg-grey-input ">
					</div>
				</div>
			</div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field Name',ET_DOMAIN);?></div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="field_name"  value="{{field_name}}"  readonly class="option-item bg-grey-input ">
						<input type="hidden" name="field_name_edit"  value="{{ field_name }}"  readonly class="option-item bg-grey-input ">

					</div>
				</div>
			</div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Placehoder Text',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="field_pholder"  value="{{ field_pholder }}" class="option-item bg-grey-input ">
					</div>
				</div>
			</div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field description',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<textarea cols="30" rows="10" name="field_des">{{ field_des }}</textarea>
					</div>
				</div>
			</div>
			<input type="hidden" value="ce-add-field" name="action">
			<div class="form-item">
				<div  class=" label title font-quicksand"><?php _e('Field type',ET_DOMAIN);?> </div>
				<div class="form-item">
					<div class="select-style et-button-select">
						<select name="field_type" style="z-index: 10; opacity: 0;">
							<option <# if(field_type != 'text') {#> selected="selected" <# } #> value="text"> <?php _e('Text',ET_DOMAIN);?></option>
							<option <# if(field_type == 'date') {#> selected="selected" <# } #> value="date"> <?php _e('Date',ET_DOMAIN);?></option>
							<option <# if(field_type == 'url') {#> selected="selected" <# } #> value="url"> <?php _e('Url',ET_DOMAIN);?></option>
						</select>

					</div>
				</div>
			</div>

			<div class="row-item">
				<div  class=" label title font-quicksand"><?php _e('Assign to categoriese',ET_DOMAIN);?> </div>
				<div class="form-item">
					<div class="select-style et-button-select">

						<select name = "field_cats[]" id = "field_cats" style="z-index: 10; opacity: 0;">
							<option value='-1' disabled selected><?php _e('Select categoy',ET_DOMAIN);?></option>
						<?php
							$categories = ET_AdCatergory::get_category_list();
							foreach ($categories as $key => $cat) {
								echo '<option value="'.$cat->term_id.'">'. $cat->name.'</option>';
							}
							?>
						</select>
						</div>

				</div>
				<ul class="cat-list form-item"  id ="cat-list"> </ul>
			</div>


			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Required',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="checkbox" <# if(field_required == 1) { #> checked = "checked" <# } #>  name="field_required" id="field_required" /> <label for="field_required"><?php _e(' Check this if field is required.',ET_DOMAIN);?></label>
					</div>
				</div>
			</div>
			<div class="row-submit">
					<button class="btn-button btn-primary engine-submit-btn add_payment_plan">
						<span> <?php _e('Save Field',ET_DOMAIN);?> </span><span class="icon" data-icon="+"></span>
					</button>
			</div>
		</form>
	</script>

	<script type="application/json" id="template_add_tax">
		<form class="add-tax form-edit-tax">
			<div class="form-item">
             	<div class="title font-quicksand"><?php _e('Edit Taxonomy', ET_DOMAIN);?></div>
             	<div class="clearfix"></div>
            </div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Taxonomy Label',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="tax_label"  value="{{ tax_label }}" class="option-item bg-grey-input ">
					</div>
				</div>
			</div>
			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Taxonomy slug',ET_DOMAIN);?></div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="tax_slug"  value="{{tax_slug}}" class="option-item bg-grey-input ">
						<input type="hidden" name="id" id="post_slug" value="{{id}}" class="option-item bg-grey-input ">
						<input type="hidden" name="tax_name_edit"  value="{{tax_name}}" class="option-item bg-grey-input ">

					</div>
				</div>
			</div>
			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Taxonomy Name',ET_DOMAIN);?></div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="tax_name"  value="{{tax_name}}"  readonly class="option-item bg-grey-input ">
					</div>
				</div>
			</div>
			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Taxonomy description',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<textarea cols="30" rows="10" name="tax_des">{{ tax_des }}</textarea>
					</div>
				</div>
			</div>
			<input type="hidden" value="ce-add-tax" name="action">
			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field type',ET_DOMAIN);?></div>
				<div class="form-item">
					<div class="select-style et-button-select">
						<select style="z-index: 10; opacity: 0;" name="tax_type" class="valid">
							<option value="select" <# if(tax_type == 'select') { #>selected="selected" <# } #> > <?php _e('Dropwdown option',ET_DOMAIN);?></option>
							<option value="checkbox" <# if(tax_type == 'checkbox') { #>selected="selected" <# }#>><?php _e('Checkbox',ET_DOMAIN);?></option>
							<option value="radio" <# if(tax_type == 'radio') { #> selected="selected" <# } #>> <?php _e('Radio',ET_DOMAIN);?></option>
						</select>
					</div>
				</div>
			</div>
			<div class="row-submit">
				<button class="btn-button btn-primary engine-submit-btn add_payment_plan">
					<span><?php _e('Save Taxonomy',ET_DOMAIN);?></span><span class="icon" data-icon="+"></span>
				</button>
			</div>
		</form>
	</script>
</div> <!-- end div.wrap-taxs!-->

<!-- template edit a seller's field!-->
	<script type="application/json" id="template_add_seller_field">
		<form class="add-field form-edit-field">
			<div class="form-item">
             	<div class="title font-quicksand"><?php _e('Edit Seller Field', ET_DOMAIN);?></div>
             	<div class="clearfix"></div>
            </div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field Label',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="field_label"  value="{{ field_label }}" class="option-item bg-grey-input ">
					</div>
				</div>
			</div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field Name',ET_DOMAIN);?></div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="field_name"  value="{{field_name}}"  readonly class="option-item bg-grey-input ">
						<input type="hidden" name="field_name_edit"  value="{{ field_name }}"  readonly class="option-item bg-grey-input ">

					</div>
				</div>
			</div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Placehoder Text',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="text" name="field_pholder"  value="{{ field_pholder }}" class="option-item bg-grey-input ">
					</div>
				</div>
			</div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Field description',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<textarea cols="30" rows="10" name="field_des">{{ field_des }}</textarea>
					</div>
				</div>
			</div>
			<input type="hidden" value="ce-add-field-seller" name="action">
			<div class="form-item">
				<div  class=" label title font-quicksand"><?php _e('Field type',ET_DOMAIN);?> </div>
				<div class="form-item">
					<div class="select-style et-button-select">
						<select name="field_type" style="z-index: 10; opacity: 0;">
							<option <# if(field_type != 'text') {#> selected="selected" <# } #> value="text"> <?php _e('Text',ET_DOMAIN);?></option>
							<option <# if(field_type == 'date') {#> selected="selected" <# } #> value="date"> <?php _e('Date',ET_DOMAIN);?></option>
							<option <# if(field_type == 'url') {#> selected="selected" <# } #> value="url"> <?php _e('Url',ET_DOMAIN);?></option>
							<option <# if(field_type == 'checkbox') {#> selected="selected" <# } #> value="checkbox"> <?php _e('Checkbox',ET_DOMAIN);?></option>
							<option <# if(field_type == 'select') {#> selected="selected" <# } #> value='select'> <?php _e('Dropdow',ET_DOMAIN);?></option>
							<option <# if(field_type == 'radio') {#> selected="selected" <# } #> value="radio"> <?php _e('Radio',ET_DOMAIN);?></option>
						</select>

					</div>
				</div>
				<ul class="list-options"></ul>
			</div>

			<div class="row-item">
				<div class="label title font-quicksand"> <?php _e('Required',ET_DOMAIN);?> </div>
				<div class="form no-margin no-padding no-background">
					<div class="form-item">
						<input type="checkbox" <# if(field_required == 1) { #> checked = "checked" <# } #>  name="field_required" id="sfield_required" /> <label for="sfield_required"><?php _e(' Check this if field is required.',ET_DOMAIN);?></label>
					</div>
				</div>
			</div>
			<div class="row-submit">
					<button class="btn-button btn-primary engine-submit-btn add_payment_plan">
						<span> <?php _e('Save Field',ET_DOMAIN);?> </span><span class="icon" data-icon="+"></span>
					</button>
			</div>
		</form>
	</script>