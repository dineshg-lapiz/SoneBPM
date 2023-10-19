<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="salesorder-reports" class="hide">
 <div class="row">
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
		<!--<a class=""  href="<?php echo admin_url('reports/createDetailedExcel/'); ?>"><i class="fa fa-file-excel-o"></i> Detailed Report</a>-->
		<a class=""  href="#" onclick="download_detailed_report(document.getElementById('months-report').value,document.getElementById('report-from').value,document.getElementById('report-to').value);"><i class="fa fa-file-excel-o"></i> Detailed Report</a>
	</div>
</div>

   <table class="table table-salesorder-report">
      <thead>
         <tr>
            <th><?php echo _l('Employee ID'); ?> #</th>
            <th><?php echo _l('SO #'); ?></th>
            <th><?php echo _l('Date'); ?></th>
			
            <th><?php echo _l('Org Name'); ?></th>
			<th><?php echo _l('Org ID'); ?></th>
            <th><?php echo _l('Amount'); ?></th>
           
            <th><?php echo _l('Status'); ?></th>
			<th><?php echo _l('Delivery Date'); ?></th>
         </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
         <td></td>
         <td></td>
         <td></td>
		 
         <td></td>
		 <td></td>
         <td class="total"></td>
		 
         <td></td>
         <td></td>
		 
      </tfoot>
   </table>
</div>
<script>
function download_detailed_report(selectedValue,from,to){
//call  ajax method to get data from database
//alert(selectedValue);
//alert(selectedValue);
 $.ajax({
            type: "GET",
            url: admin_url + "reports/createDetailedExcel/",//this  should be replace by your server side method
            data: {report_months: selectedValue,report_from: from,report_to: to }, //this is parameter name , make sure parameter name is sure as of your sever side method
            //contentType: "application/json; charset=utf-8",
            //dataType: "json",
            //async: false,
            success: function (response) {
               //alert(response);
			   //response = JSON.parse(response);
			   //alert(response);
			   //alert(response.inventory_number);
			   //$("#available_quantity").val(response);
			   var downloadLink = document.createElement("a");
              var fileData = ['\ufeff'+response];

              var blobObject = new Blob(fileData,{
                 type: "text/csv;charset=utf-8;"
               });

              var url = URL.createObjectURL(blobObject);
              downloadLink.href = url;
              downloadLink.download = "itemlist.csv";
			  
			   document.body.appendChild(downloadLink);
              downloadLink.click();
              document.body.removeChild(downloadLink);

            }
        });
}
</script>
