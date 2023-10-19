<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">

                <div>
                    <div class="row row-margin-bottom">
                        <div class="col-md-4 ">
                            <?php if (has_permission('warehouse', '', 'create') || is_admin() ) { ?>

                            <a href="#" onclick="add_one_warehouse(); return false;" class="btn btn-info pull-left display-block mr-4 button-margin-r-b">
                                <?php echo _l('Add Storage Location'); ?>
                                </a>
                             
                        </div>
                    </div>
                <?php } ?>

                <div class="clearfix"></div>
                <hr class="hr-panel-heading" />
                <div class="clearfix"></div>

                                    <?php 
                                      $table_data = array( 
                                        _l('Storage Location'), 
                                                          _l('warehouse_name'),
                                                         
                                                        );
                                       $cf = get_custom_fields('warehouse_name',array('show_on_table'=>1));

                                      foreach($cf as $custom_field) {
                                        array_push($table_data,$custom_field['name']);
                                      }

                                      render_datatable($table_data,'table_warehouse_location_name',
                                          array('customizable-table'),
                                          array(
                                            'proposal_sm' => 'proposal_sm',
                                             'id'=>'table-table_warehouse_location_name',
                                             'data-last-order-identifier'=>'table_warehouse_location_name',
                                             'data-default-order'=>get_table_last_order('table_warehouse_location_name'),
                                           )); ?>



              

                    <!-- add one warehouse -->
                    <div class="modal1 fade" id="a_warehouse" tabindex="-1" role="dialog">
                        <div class="modal-dialog setting-handsome-table">
                          <?php echo form_open_multipart(admin_url('warehouse/add_storage_location'), array('id'=>'add_warehouse')); ?>

                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">
                                        <span class="add-title"><?php echo _l('Add Storage Location'); ?></span>
                                        <span class="edit-title"><?php echo _l('edit_warehouse_type'); ?></span>
                                    </h4>
                                </div>

                                <div id="warehouse_id"></div>

                                <div class="modal-body">
                                    <div class="horizontal-scrollable-tabs preview-tabs-top">
                                      <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                                      <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                                      <div class="horizontal-tabs">
                                      <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                                       <li role="presentation" class="active">
                                           <a href="#interview_infor" aria-controls="interview_infor" role="tab" data-toggle="tab" aria-controls="interview_infor">
                                           <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('general_infor'); ?>
                                           </a>
                                        </li>
                                        
                                        
                                        
                                        
                                       </ul>
                                     </div>
                                   </div>

                            <div class="tab-content">
              
                            <!-- interview process start -->
                              <div role="tabpanel" class="tab-pane active" id="interview_infor">

                                    <div class="row">
                                        <div class="col-md-12">
                                             <div id="color_id_t"></div>   
                                          <div class="form"> 
                                            

                                            
                                            <div class="col-md-6">
                                            <?php $warehouses = $this->warehouse_model->get_warehouse();
                                            $warehouse_id_value = (isset($goods_receipt) ? $goods_receipt->warehouse_id : '');?>
								<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle skucode-tooltip"  data-toggle="tooltip" title="" data-original-title="<?php echo _l('goods_receipt_warehouse_tooltip'); ?>"></i></a>
								<?php echo render_select('warehouse_id',$warehouses,array('warehouse_id','warehouse_name'),'warehouse_name', $warehouse_id_value); ?>

                                            </div>
                                            <div class="col-md-6">
                                              <?php echo render_input('storage_location', 'Storage Location'); ?>
                                            </div>
 
                                          </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- custome fields -->
                                  <div role="tabpanel" class="tab-pane" id="custom_fields">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <div class="form">
                                          <div id="custom_fields_items">
                                            <?php echo wh_render_custom_fields('warehouse_name'); ?>
                                          </div>

                                        </div>
                                     </div>
                                   </div>
                                 </div>


                                </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                        
                                         <button type="submit" class="btn btn-info intext-btn"><?php echo _l('submit'); ?></button>
                                    </div>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div> 


                </div>

            </div>
        </div>
    </div>
</div>
</div>
</div>


<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/warehouse_location_js.php';?>
</body>
</html>
