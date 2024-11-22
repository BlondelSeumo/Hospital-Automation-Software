<?php $uri =  $this->uri->segment(5); 
if($uri){
    $uri = $uri ;
}else{
    $uri = 0 ;
}
?>
<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
		<?php  if ($this->session->flashdata('msg')) { ?> <div>  <?php echo $this->session->flashdata('msg') ?> </div> <?php $this->session->unset_userdata('msg'); }   ?>
        <div class="row">
            <div class="col-md-3">
                <div class="box border0">
                    <ul class="tablists">        
                    <?php
                        $count_tab=0;
                        foreach ($role_permission as $key => $value) {
                    ?>                                         
                        <li class="<?php echo ($count_tab == $open_tab) ?"active":""?>" ><a onclick="activeurl(<?php echo $count_tab;?>,'<?php echo $value->name ?>')" class="tab" id="tab_<?php echo $count_tab;?>" href="#tab<?php echo $count_tab;?>" data-toggle="pill" data-act-tab="<?php echo $count_tab;?>"><?php echo $value->name; ?></a></li>                                         
                    <?php
                        $count_tab++;
                        }
                    ?>      
                    </ul>
                </div>
            </div>            
            <div class="col-md-9"><div class="box box-primary">
				
                <div class="box-header ptbnull">
                    <h3 class="box-title titlefix"><span id="permissionname"> 
                    
                    <?php
                        $count_tab=0;
                        foreach ($role_permission as $key => $value) {
                    ?>                                         
                         <?php if($count_tab == $open_tab){ echo $value->name . " Permissions"; } ?>                                          
                    <?php
                        $count_tab++;
                        }
                    ?>                   
                    
                    </span></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="">                
                        <div class="box-body">
                            <div class="tab-content">
                            <?php echo $this->customlib->getCSRF(); ?> 
                            <?php
                                $tab_content_counter=0;                              
                                for ($i = 0; $i < count($role_permission); $i++) {
                                $value=$role_permission[$i];                              
                            ?>
                                <div class="tab-pane <?php echo ($tab_content_counter == $open_tab) ?"active":""?>" id="tab<?php echo $tab_content_counter?>">
                                    <form id="form1" action="<?php echo site_url('admin/roles/permission/' . $role['id']) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                                    <input type="hidden" name="open_tab" id="open_tab" class="open_tab" value="<?php echo  $uri ; ?>" />
                                 
                                    <input type="hidden" name="role_id" value="<?php echo $role['id'] ?>"/>
                                        <table class="table table-stripped">
                                            <thead>
                                                <tr>                                    
                                                    <th><?php echo $this->lang->line('feature'); ?></th>
                                                    <th><?php echo $this->lang->line('view'); ?></th>
                                                    <th><?php echo $this->lang->line('add'); ?></th>
                                                    <th><?php echo $this->lang->line('edit'); ?></th>
                                                    <th><?php echo $this->lang->line('delete'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>                                           
                                            <?php
                                            if (!empty($value->permission_category)) {
                                                ?>
                                                <td width="60%">
                                                    <input type="hidden" name="per_cat[]" value="<?php echo $value->permission_category[0]->id; ?>" />
                                                    <input type="hidden" name="<?php echo "roles_permissions_id_" . $value->permission_category[0]->id; ?>" value="<?php echo $value->permission_category[0]->roles_permissions_id; ?>" />
                                                    <?php echo $value->permission_category[0]->name ?></td>
                                                <td width="10%">
                                                    <?php
                                                    if ($value->permission_category[0]->enable_view == 1) {
                                                        ?>
                                                        <label class="">
                                                            <input type="checkbox" name="<?php echo "can_view-perm_" . $value->permission_category[0]->id; ?>" value="<?php echo $value->permission_category[0]->id; ?>" <?php echo set_checkbox("can_view-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_view == 1) ? TRUE : FALSE); ?>> 
                                                        </label>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                               <td width="10%">
                                                    <?php
                                                    if ($value->permission_category[0]->enable_add == 1) {
                                                        ?>
                                                        <label class="">
                                                            <?php 
                                                             ?>
                                                            <input type="checkbox" name="<?php echo "can_add-perm_" . $value->permission_category[0]->id; ?>" value="<?php echo $value->permission_category[0]->id; ?>" <?php echo set_checkbox("can_view-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_add == 1) ? TRUE : FALSE); ?>> 
                                                        </label> 
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                 <td width="10%">
                                                    <?php
                                                    if ($value->permission_category[0]->enable_edit == 1) {
                                                        ?>
                                                        <label class="">
                                                            <input type="checkbox" name="<?php echo "can_edit-perm_" . $value->permission_category[0]->id; ?>" value="<?php echo $value->permission_category[0]->id; ?>" <?php echo set_checkbox("can_view-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_edit == 1) ? TRUE : FALSE); ?>> 
                                                        </label> 
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td width="10%">
                                                    <?php
                                                    if ($value->permission_category[0]->enable_delete == 1) {
                                                        ?>
                                                        <label class="">
                                                            <input type="checkbox" name="<?php echo "can_delete-perm_" . $value->permission_category[0]->id; ?>" value="<?php echo $value->permission_category[0]->id; ?>" <?php echo set_checkbox("can_view-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_delete == 1) ? TRUE : FALSE); ?>> 
                                                        </label> 
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                            } else {
                                                ?>
                                                <td colspan="5"></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                        if (!empty($value->permission_category) && count($value->permission_category) > 1) {
                                            unset($value->permission_category[0]);
                                            foreach ($value->permission_category as $new_feature_key => $new_feature_value) {
                                                ?>
                                                <tr>
                                                    <td width="60%">
                                                        <input type="hidden" name="per_cat[]" value="<?php echo $new_feature_value->id; ?>" />
                                                        <input type="hidden" name="<?php echo "roles_permissions_id_" . $new_feature_value->id; ?>" value="<?php echo $new_feature_value->roles_permissions_id; ?>" />
                                                        <?php echo $new_feature_value->name ?></td>
                                                   <td width="10%">
                                                        <?php
                                                        if ($new_feature_value->enable_view == 1) {
                                                            ?>
                                                            <label class="">
                                                                <input type="checkbox" name="<?php echo "can_view-perm_" . $new_feature_value->id; ?>" value="<?php echo $new_feature_value->id; ?>" <?php echo set_checkbox("can_view-perm_" . $new_feature_value->id, $new_feature_value->id, ( $new_feature_value->can_view == 1) ? TRUE : FALSE); ?>> 
                                                            </label> 
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                <td width="10%">
                                                        <?php
                                                        if ($new_feature_value->enable_add == 1) {
                                                            ?>
                                                            <label class="">
                                                                <input type="checkbox" name="<?php echo "can_add-perm_" . $new_feature_value->id; ?>" value="<?php echo $new_feature_value->id; ?>" <?php echo set_checkbox("can_view-perm_" . $new_feature_value->id, $new_feature_value->id, ( $new_feature_value->can_add == 1) ? TRUE : FALSE); ?>> 
                                                            </label> 
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                <td width="10%">
                                                        <?php
                                                        if ($new_feature_value->enable_edit == 1) {
                                                            ?>
                                                            <label class="">
                                                                <input type="checkbox" name="<?php echo "can_edit-perm_" . $new_feature_value->id; ?>" value="<?php echo $new_feature_value->id; ?>" <?php echo set_checkbox("can_view-perm_" . $new_feature_value->id, $new_feature_value->id, ( $new_feature_value->can_edit == 1) ? TRUE : FALSE); ?>> 
                                                            </label> 
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                <td width="10%">
                                                        <?php
                                                        if ($new_feature_value->enable_delete == 1) {
                                                            ?>
                                                            <label class="">
                                                                <input type="checkbox" name="<?php echo "can_delete-perm_" . $new_feature_value->id; ?>" value="<?php echo $new_feature_value->id; ?>" <?php echo set_checkbox("can_view-perm_" . $new_feature_value->id, $new_feature_value->id, ( $new_feature_value->can_delete == 1) ? TRUE : FALSE); ?>> 
                                                            </label> 
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                            </tbody>
                                        </table>                               
                           
                                        <div class="box-footer">
                                            <button type="submit" class="btn btn-info pull-right"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                    </form>
                                </div>
                                <?php
                                    $tab_content_counter++; 
                                }
                                ?>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>            
        </div> 
    </section>
</div>

<script type="text/javascript">
(function () {
    $('#tab_'+<?php echo $uri; ?>).addClass('active');    
})();
</script>
<script type="text/javascript">
    function activeurl(tab,name){     
            
            $('.open_tab').val(tab);           
            $(".tab").removeClass("active");
            $('#tab_'+tab).addClass('active');
            $('#permissionname').html(name+' Permissions'); 
         
    }
</script >  