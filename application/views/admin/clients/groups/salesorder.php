<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (isset($client)) { ?>
<h4 class="customer-profile-group-heading"><?php echo _l('client_salesorder_tab'); ?></h4>
<?php if (has_permission('invoices', '', 'create')) { ?>
<a href="<?php echo admin_url('salesorder/invoice?customer_id=' . $client->userid); ?>"
    class="btn btn-primary mbot15<?php echo $client->active == 0 ? ' disabled' : ''; ?>">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?php echo _l('create_new_salesorder'); ?>
</a>
<?php } ?>
<?php if (has_permission('invoices', '', 'view') || has_permission('invoices', '', 'view_own') || get_option('allow_staff_view_invoices_assigned') == '1') { ?>
<a href="#" class="btn btn-primary mbot15" data-toggle="modal" data-target="#client_zip_salesorder">
    <i class="fa-regular fa-file-zipper tw-mr-1"></i>
    <?php echo _l('zip_salesorder'); ?>
</a>
<!--<div id="salesorder_total" class="tw-mb-5"></div>-->
<?php
        $this->load->view('admin/salesorder/table_html', ['class' => 'salesorder-single-client']);
        $this->load->view('admin/clients/modals/zip_salesorder');
?>
<?php } ?>
<?php } ?>