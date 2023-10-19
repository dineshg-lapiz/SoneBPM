<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade proposal-convert-modal" id="convert_to_salesorder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xxl" role="document">
        <?php echo form_open('admin/estimates/convert_to_salesorder/' . $estimate->id, ['id' => 'estimate_convert_to_salesorder_form', 'class' => '_transaction_form invoice-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="close_modal_manually('#convert_to_salesorder')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('proposal_convert_to_salesorder'); //print "<pre>";print_r($proposal); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $this->load->view('admin/salesorder/estimate_to_salesorder_template'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default invoice-form-submit save-as-draft transaction-submit">
                    <?php echo _l('save_as_draft'); ?>
                </button>
                <button class="btn btn-primary invoice-form-submit transaction-submit">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php $this->load->view('admin/invoice_items/item'); ?>
<script>
    init_ajax_search('customer','#clientid.ajax-search');
    init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
    custom_fields_hyperlink();
    init_selectpicker();
    init_tags_inputs();
    init_datepicker();
    init_color_pickers();
    init_items_sortable();
    validate_salesorder_form('#estimate_convert_to_salesorder_form');
    <?php //if ($proposal->assigned != 0) { ?>
     //$('#convert_to_salesorder #sale_agent').selectpicker('val',<?php //echo $proposal->assigned; ?>);
    <?php //} ?>
    $('select[name="discount_type"]').selectpicker('val','<?php echo $estimate->discount_type; ?>');
    $('input[name="discount_percent"]').val('<?php echo $estimate->discount_percent; ?>');
    $('input[name="discount_total"]').val('<?php echo $estimate->discount_total; ?>');
    <?php if (is_sale_discount($estimate, 'fixed')) { ?>
        $('.discount-total-type.discount-type-fixed').click();
    <?php } ?>
    $('input[name="adjustment"]').val('<?php echo $estimate->adjustment; ?>');
    $('input[name="show_quantity_as"][value="<?php echo $estimate->show_quantity_as; ?>"]').prop('checked',true).change();
    <?php if (!isset($project_id) || !$project_id) { ?>
        $('#convert_to_salesorder #clientid').change();
    <?php } else { ?>
        $('#convert_to_salesorder select#currency').val("<?php echo $estimate->currency ?>").trigger('change');
        init_ajax_project_search_by_customer_id('select#project_id');
    <?php } ?>
    // Trigger item select width fix
    $('#convert_to_salesorder').on('shown.bs.modal', function(){
        $('#item_select').trigger('change')
    })
</script>
