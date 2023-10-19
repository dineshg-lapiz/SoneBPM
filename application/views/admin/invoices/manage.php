<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			include_once(APPPATH.'views/admin/invoices/filter_params.php');
			$this->load->view('admin/invoices/list_template');
			?>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<div id="modal-wrapper"></div>
<script>var hidden_columns = [2,6,7,8];</script>
<?php init_tail(); ?>
<script>
	$(function(){
		init_invoice();
	});
</script>
<script>
$(function() {
$('.print_pdf').on('click',function() {
var days = [];
$.each($("input[name='pdfs']:checked"), function(){
  days.push($(this).val());
});
//alert();
if(days.length==0){ alert('Select At least one Invoice');}else{
//alert("Selected say(s) are: " + days.join(", "));
$('#prid').val(days.join(", "));
$('#pdfall').trigger('submit');

}
});

});
</script>
</body>
</html>
