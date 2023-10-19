<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_source(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('template_new'); ?>
                    </a>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($sources) > 0) { ?>
                        <table class="table dt-table" data-order-col="1" data-order-type="asc">
                            <thead>
                                <th><?php echo _l('template_list_item'); ?></th>
                                <th><?php echo _l('template_add_edit_name'); ?></th>
								<th><?php echo _l('template_created_date'); ?></th>
                                <th><?php echo _l('options'); ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($sources as $source) { ?>
                                <tr>
                                    <td><?php echo ucfirst($source['type']); ?></td>
                                    <td><a href="#"
                                            onclick="edit_source(this,<?php echo $source['id']; ?>); return false"
                                            data-name="<?php echo $source['name']; ?>" data-rel_to="<?php echo $source['type']; ?>"><?php echo $source['name']; ?></a><br />
                                        <!--<span class="text-muted">
                                            <?php echo _l('leads_table_total', total_rows(db_prefix() . 'leads', ['source' => $source['id']])); ?>
                                        </span>-->
                                    </td>
									<td><?php echo $source['datecreated']; ?></td>
                                    <td>
                                        <div class="tw-flex tw-items-center tw-space-x-3">
                                            <a href="#"
                                                onclick="edit_source(this,<?php echo $source['id']; ?>); return false"
                                                data-name="<?php echo $source['name']; ?>"
                                                class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                            </a>
                                            <a href="<?php echo admin_url('template_list/delete/' . $source['id']); ?>"
                                                class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                <i class="fa-regular fa-trash-can fa-lg"></i>
                                            </a>
											<a href="#" title="Set As Default"><input type="checkbox" name="set_default" <?php if($source['default_template']==1){ ?> checked="checked" <?php } ?> onclick="set_as_default('<?php echo $source['id']; ?>','<?php echo $source['type']; ?>','<?php $source['default_template']; ?>',this.checked);"></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                        <p class="no-margin"><?php echo _l('leads_sources_not_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="source" tabindex="-1" role="dialog">
    <div class="modal-dialog  modal-xxl">
        
		<?php
			/*$name = isset($template) ? $template->name : '';
            if ($name=='') {
                echo form_open('admin/template_list/template', ['id' => 'template-form']);
            } else {
                echo form_open('admin/template_list/template/' . $id, ['id' => 'template-form']);
            }*/
            ?>
		<?php echo form_open(admin_url('template_list/template')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('template_edit'); ?></span>
                    <span class="add-title"><?php echo _l('template_new'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php //echo render_input('name', 'template_add_edit_name'); ?>
						<?php
						//$name = isset($template) ? $template->name : '';
                        echo render_input('name', 'template_name', '');
						?>						
                    </div>
					<div class="col-md-6  ">
					 <div class="form-group">
					  <label for="rel_to"><?php echo _l('related_to'); ?></label>
						<select name="rel_to" id="rel_to" class="selectpicker" onchange="show_available_field(this.value);"  data-width="100%">
							<option value="invoice">Invoice</option>
							<option value="saleorder">SaleOrder</option>
							<option value="proposals">Proposals</option>
							<option value="estimate">Estimate</option>
						</select>
					  </div>
					</div>
					
					
					<div class="col-md-12 proposals_merge_field merge_fields">
						<div class="clearfix"></div>
                        <?php if (isset($proposal_merge_fields)) { ?>
                        <p class="bold"><a href="#"
                                onclick="slideToggle('.proposal_avilable_merge_fields'); return false;"><?php echo _l('Fields Available'); ?></a>
                        </p>
                        <hr class="hr-panel-separator" />
                        <div class="hide proposal_avilable_merge_fields mtop15">
                            <div class="row">
                                
                                    
                                        <?php
                                 foreach ($proposal_merge_fields as $field) {
                                     foreach ($field as $f) {
                                         echo '<div class="col-md-4" style="padding-top:2px;padding-bottom:2px;"><b>' . $f['name'] . '</b> <a href="#" class="pull-right" onclick="insert_proposal_merge_field(this); return false;">' . $f['key'] . '</a></div>';
                                     }
                                 }
                             ?>
                                    
                                
                            </div>
                        </div>
                        <?php } ?>
					</div>
					<div class="col-md-12 invoice_merge_field merge_fields">
						<div class="clearfix"></div>
                        <?php if (isset($invoice_merge_fields)) { ?>
                        <p class="bold"><a href="#"
                                onclick="slideToggle('.invoice_avilable_merge_fields'); return false;"><?php echo _l('Fields Available'); ?></a>
                        </p>
                        <hr class="hr-panel-separator" />
                        <div class="hide invoice_avilable_merge_fields mtop15">
                            <div class="row">
                               
                                        <?php
                                 foreach ($invoice_merge_fields as $field) {
                                     foreach ($field as $f) {
                                         echo '<div class="col-md-4" style="padding-top:2px;padding-bottom:2px;"><b>' . $f['name'] . '</b> <a href="#" class="pull-right" onclick="insert_proposal_merge_field(this); return false;">' . $f['key'] . '</a></div>';
                                     }
                                 }
                             ?>
                                    
                            </div>
                        </div>
                        <?php } ?>
					</div>
					<div class="col-md-12 estimate_merge_field merge_fields">
						<div class="clearfix"></div>
                        <?php if (isset($invoice_merge_fields)) { ?>
                        <p class="bold"><a href="#"
                                onclick="slideToggle('.estimate_avilable_merge_fields'); return false;"><?php echo _l('Fields Available'); ?></a>
                        </p>
                        <hr class="hr-panel-separator" />
                        <div class="hide estimate_avilable_merge_fields mtop15">
                            <div class="row">
                               
                                        <?php
                                 foreach ($estimate_merge_fields as $field) {
                                     foreach ($field as $f) {
                                         echo '<div class="col-md-4" style="padding-top:2px;padding-bottom:2px;"><b>' . $f['name'] . '</b> <a href="#" class="pull-right" onclick="insert_proposal_merge_field(this); return false;">' . $f['key'] . '</a></div>';
                                     }
                                 }
                             ?>
                                    
                            </div>
                        </div>
                        <?php } ?>
					</div>
					<div class="col-md-12 saleorder_merge_field merge_fields">
						<div class="clearfix"></div>
                        <?php if (isset($saleorder_merge_fields)) { ?>
                        <p class="bold"><a href="#"
                                onclick="slideToggle('.saleorder_avilable_merge_fields'); return false;"><?php echo _l('Fields Available'); ?></a>
                        </p>
                        <hr class="hr-panel-separator" />
                        <div class="hide saleorder_avilable_merge_fields mtop15">
                            <div class="row">
                               
                                        <?php
                                 foreach ($saleorder_merge_fields as $field) {
                                     foreach ($field as $f) {
                                         echo '<div class="col-md-4" style="padding-top:2px;padding-bottom:2px;"><b>' . $f['name'] . '</b> <a href="#" class="pull-right" onclick="insert_proposal_merge_field(this); return false;">' . $f['key'] . '</a></div>';
                                     }
                                 }
                             ?>
                                    
                            </div>
                        </div>
                        <?php } ?>
					</div>
					
					<hr class="hr-panel-separator" />
					
					
					
					
					
					
					<div class="col-md-12 mt-5 mtop15">
						<?php
						//$content = isset($template) ? $template->content : '';
                        echo render_textarea('content', 'template_content', '',[],[],'','tinymce' );
						?>
					</div>
					
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php init_tail(); ?>
<script>
$('.merge_fields').hide();
$(function() {
    appValidateForm($('form'), {
        name: 'required'
    }, manage_leads_sources);
    $('#source').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#source input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
});

function show_available_field(type){
	$('.merge_fields').hide();
	$('.'+type+'_merge_field').show();
	tinyMCE.get('content').setContent('');
}

function manage_leads_sources(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        window.location.reload();
    });
    return false;
}

function new_source() {
    $('#source').modal('show');
	tinyMCE.get('content').setContent('');
    $('.edit-title').addClass('hide');
}

function edit_source(invoker, id) {
    var name = $(invoker).data('name');
	var rel_to = $(invoker).data('rel_to');
	//alert(id);
	 $.ajax({
		type: "GET",
		url: admin_url + "template_list/get_template",//this  should be replace by your server side method
		data: {id: id }, //this is parameter name , make sure parameter name is sure as of your sever side method
		//contentType: "application/json; charset=utf-8",
		//dataType: "json",
		//async: false,
		success: function (response) {
		   //alert(response);
		   response = JSON.parse(response);
		   //alert(response.content);
		   $('#additional').append(hidden_input('id', response.id));
			$('#source input[name="name"]').val(response.name);
			$('#source select[name="rel_to"]').val(response.type).change();
			$('#source input[name="content"]').val(response.content);
			tinyMCE.get('content').setContent(response.content);
		}
	});
	
	
	//alert(rel_to);
    
    $('#source').modal('show');
    $('.add-title').addClass('hide');
}
function set_as_default(temp_id, type, def_temp,chkd){  
	if(chkd==true){
		if (confirm("Are you sure to set this template as default for "+type+" ?") == true) {
			if(temp_id!=''){
				$.ajax({
					type:'GET',
					url:admin_url + "template_list/set_default_template",
					data:{temp_id: temp_id,temp_type: type,def_temp: "1"},
					success:function(msg){
						if(msg=="success"){
							window.location.reload();
						}
					}
				});
			}
		} 
	}else{
		if(temp_id!=''){
			$.ajax({
				type:'GET',
				url:admin_url + "template_list/set_default_template",
				data:{temp_id: temp_id,temp_type: type,def_temp: "0"},
				success:function(msg){
					if(msg=="success"){
						window.location.reload();
					}
				}
			});
		}
	}
		
}
</script>
</body>

</html>