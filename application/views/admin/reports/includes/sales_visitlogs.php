<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="log-reports" class="hide">
 <!--<div class="row">
   <div class="col-md-4">
      <div class="form-group">
         <label for="proposal_status"><?php echo _l('proposal_status'); ?></label>
         <select name="proposal_status" class="selectpicker" multiple data-width="100%" data-none-selected-text="<?php echo _l('invoice_status_report_all'); ?>">
            <?php foreach($proposals_statuses as $status){ ?>
            <option value="<?php echo $status; ?>"><?php echo format_proposal_status($status,'',false) ?></option>
            <?php } ?>
         </select>
      </div>
   </div>
   <?php if(count($proposals_sale_agents) > 0 ) { ?>
   <div class="col-md-4">
      <div class="form-group">
         <label for="proposals_sale_agents"><?php echo _l('sale_agent_string'); ?> (<?php echo _l('proposal_assigned'); ?>)</label>
         <select name="proposals_sale_agents" class="selectpicker" multiple data-width="100%" data-none-selected-text="<?php echo _l('invoice_status_report_all'); ?>">
            <?php foreach($proposals_sale_agents as $agent){ ?>
            <option value="<?php echo $agent['sale_agent']; ?>"><?php echo get_staff_full_name($agent['sale_agent']); ?></option>
            <?php } ?>
         </select>
      </div>
   </div>
   <?php } ?>
   <div class="col-md-4" style="margin-top:40px">
		
		<a class=""  href="#" onclick="download_detailed_report(document.getElementById('months-report').value,document.getElementById('report-from').value,document.getElementById('report-to').value);"><i class="fa fa-file-excel-o"></i> Detailed Report</a>
	</div>
</div>-->

   <table class="table table-log-report">
      <thead>
         <tr>
            <th><?php echo _l('Emp Code'); ?> #</th>
            <th><?php echo _l('Emp Name'); ?></th>
			<th><?php echo _l('Org Name'); ?> #</th>
            <th><?php echo _l('Org Id'); ?></th>
            <th><?php echo _l('Visit Date'); ?></th>
			
            <th><?php echo _l('In Time'); ?></th>
			<th><?php echo _l('Out Time'); ?></th>
            <th><?php echo _l('Duration(mins)'); ?></th>
           
         </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
         <td></td>
         <td></td>
         <td></td>
		 
         <td></td>
		 <td></td>
         <td></td>
		 
      </tfoot>
   </table>
</div>
<script>

</script>
