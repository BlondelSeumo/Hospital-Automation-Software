<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<style type="text/css">
    @media print {
        .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
            float: left;
        }
        .col-sm-12 {
            width: 100%;
        }
        .col-sm-11 {
            width: 91.66666667%;
        }
        .col-sm-10 {
            width: 83.33333333%;
        }
        .col-sm-9 {
            width: 75%;
        }
        .col-sm-8 {
            width: 66.66666667%;
        }
        .col-sm-7 {
            width: 58.33333333%;
        }
        .col-sm-6 {
            width: 50%;
        }
        .col-sm-5 {
            width: 41.66666667%;
        }
        .col-sm-4 {
            width: 33.33333333%;
        }
        .col-sm-3 {
            width: 25%;
        }
        .col-sm-2 {
            width: 16.66666667%;
        }
        .col-sm-1 {
            width: 8.33333333%;
        }
        .col-sm-pull-12 {
            right: 100%;
        }
        .col-sm-pull-11 {
            right: 91.66666667%;
        }
        .col-sm-pull-10 {
            right: 83.33333333%;
        }
        .col-sm-pull-9 {
            right: 75%;
        }
        .col-sm-pull-8 {
            right: 66.66666667%;
        }
        .col-sm-pull-7 {
            right: 58.33333333%;
        }
        .col-sm-pull-6 {
            right: 50%;
        }
        .col-sm-pull-5 {
            right: 41.66666667%;
        }
        .col-sm-pull-4 {
            right: 33.33333333%;
        }
        .col-sm-pull-3 {
            right: 25%;
        }
        .col-sm-pull-2 {
            right: 16.66666667%;
        }
        .col-sm-pull-1 {
            right: 8.33333333%;
        }
        .col-sm-pull-0 {
            right: auto;
        }
        .col-sm-push-12 {
            left: 100%;
        }
        .col-sm-push-11 {
            left: 91.66666667%;
        }
        .col-sm-push-10 {
            left: 83.33333333%;
        }
        .col-sm-push-9 {
            left: 75%;
        }
        .col-sm-push-8 {
            left: 66.66666667%;
        }
        .col-sm-push-7 {
            left: 58.33333333%;
        }
        .col-sm-push-6 {
            left: 50%;
        }
        .col-sm-push-5 {
            left: 41.66666667%;
        }
        .col-sm-push-4 {
            left: 33.33333333%;
        }
        .col-sm-push-3 {
            left: 25%;
        }
        .col-sm-push-2 {
            left: 16.66666667%;
        }
        .col-sm-push-1 {
            left: 8.33333333%;
        }
        .col-sm-push-0 {
            left: auto;
        }
        .col-sm-offset-12 {
            margin-left: 100%;
        }
        .col-sm-offset-11 {
            margin-left: 91.66666667%;
        }
        .col-sm-offset-10 {
            margin-left: 83.33333333%;
        }
        .col-sm-offset-9 {
            margin-left: 75%;
        }
        .col-sm-offset-8 {
            margin-left: 66.66666667%;
        }
        .col-sm-offset-7 {
            margin-left: 58.33333333%;
        }
        .col-sm-offset-6 {
            margin-left: 50%;
        }
        .col-sm-offset-5 {
            margin-left: 41.66666667%;
        }
        .col-sm-offset-4 {
            margin-left: 33.33333333%;
        }
        .col-sm-offset-3 {
            margin-left: 25%;
        }
        .col-sm-offset-2 {
            margin-left: 16.66666667%;
        }
        .col-sm-offset-1 {
            margin-left: 8.33333333%;
        }
        .col-sm-offset-0 {
            margin-left: 0%;
        }
        .visible-xs {
            display: none !important;
        }
        .hidden-xs {
            display: block !important;
        }
        table.hidden-xs {
            display: table;
        }
        tr.hidden-xs {
            display: table-row !important;
        }
        th.hidden-xs,
        td.hidden-xs {
            display: table-cell !important;
        }
        .hidden-xs.hidden-print {
            display: none !important;
        }
        .hidden-sm {
            display: none !important;
        }
        .visible-sm {
            display: block !important;
        }
        table.visible-sm {
            display: table;
        }
        tr.visible-sm {
            display: table-row !important;
        }
        th.visible-sm,
        td.visible-sm {
            display: table-cell !important;
        }
    }

    .printablea4{width: 100%;}
    .printablea4>tbody>tr>th,
    .printablea4>tbody>tr>td{padding:2px 0; line-height: 1.42857143;vertical-align: top; font-size: 12px;}
</style>

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('prescription'); ?></title>
    </head>
    <div class="fixed-print-header">
                    <?php  if (!empty($print_details['print_header'])) { ?>
                        <img src="<?php
                        if (!empty($print_details['print_header'])) {
                            echo base_url() . $print_details['print_header'].img_time();
                        }
                        ?>" style="height:100px; width:100%;" class="img-responsive">
                    <?php }?>
                </div> 
<table class="table-print-full" width="100%">
    <thead>
        <tr>
            <td><div class="header-space">&nbsp;</div></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
      <div class="content-body">            
    <div id="html-2-pdfwrapper">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
               
                <div class="">
                    <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("opd_id"); ?></th>
                            <td width="25%"><?php if(!empty($result->opd_details_id)){ echo $prefix_opd_no .$result->opd_details_id ; } ?></td>
                            <th width="25%"><?php echo $this->lang->line("temperature"); ?></th>
                            <td width="25%"><?php if(!empty($result->temperature)){ echo $result->temperature ; } ?></td>                        
                        </tr>
                        <tr> 
                            <th width="25%"><?php echo $this->lang->line("checkup_id"); ?></th>
                            <td width="25%"><?php if(!empty($result->visit_details_id)){ echo $prefix_checkup_id .$result->visit_details_id ; } ?></td>
                            <th width="25%"><?php echo $this->lang->line("pulse"); ?></th>
                            <td width="25%"><?php if(!empty($result->pulse)){ echo $result->pulse; } ?></td>                       
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("patient_name"); ?></th>
                            <td width="25%"><?php if(!empty($result->patient_name)){ echo $result->patient_name ." (".  $result->id .")"; } ?></td>             
                            <th width="25%"><?php echo $this->lang->line("height"); ?></th>
                            <td><?php if(!empty($result->height)){ echo $result->height; } ?></td>  
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("age"); ?></th>
                            <td><?php if(!empty($result->day)){ echo $this->customlib->getPatientAge($result->age,$result->month,$result->day); } ?></td>
                            <th width="25%"><?php echo $this->lang->line("blood_group"); ?></th>
                            <td><?php if(!empty($result->blood_group)){ echo $result->blood_group; } ?></td>                           
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("gender"); ?></th>
                            <td><?php if(!empty($result->gender)){ echo $result->gender; } ?></td>
                            <th width="25%"><?php echo $this->lang->line("weight"); ?></th>
                            <td><?php if(!empty($result->weight)){ echo $result->weight; } ?></td>
                        </tr>                        
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("phone"); ?></th>
                            <td width="25%"><?php if(!empty($result->mobileno)){ echo $result->mobileno ;} ?></td>   
                            <th width="25%"><?php echo $this->lang->line("email"); ?></th>
                            <td width="25%"><?php if(!empty($result->email)){ echo $result->email; } ?></td>
                        </tr> 
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line("known_allergies"); ?></th>
                            <td><?php if(!empty($result->known_allergies)){ echo $result->known_allergies; } ?></td>
                        </tr>
                    </table>
                    <hr> 
                     <h4><?php echo $this->lang->line('primary_examine'); ?></h4>
                     <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('bleeding'); ?></th>
                            <td width="25%"><?php if(!empty($result->bleeding)){ echo $result->bleeding; } ?></td>
                            <th width="25%"><?php echo $this->lang->line('headache'); ?></th>
                            <td width="25%"><?php if(!empty($result->headache)){ echo $result->headache; } ?></td>
                        </tr>
                        <tr> 
                            <th width="25%"><?php echo $this->lang->line('pain'); ?></th>
                            <td width="25%"><?php if(!empty($result->pain)){ echo $result->pain; } ?></td>
                            <th width="25%"><?php echo $this->lang->line('constipation'); ?></th>
                            <td width="25%"><?php if(!empty($result->constipation)){ echo $result->constipation; } ?></td>
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('vomiting'); ?></th>
                            <td width="25%"><?php if(!empty($result->vomiting)){ echo $result->vomiting; } ?> </td>
                            <th width="25%"><?php echo $this->lang->line('cough'); ?></th>
                            <td width="25%"><?php if(!empty($result->cough)){ echo $result->cough; } ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('vaginal'); ?></th>
                            <td width="25%"><?php if(!empty($result->vaginal)){ echo $result->vaginal; } ?></td>
                            <th width="25%"><?php echo $this->lang->line("weight"); ?></th>
                            <td width="25%"><?php if(!empty($result->antenatal_weight)){ echo $result->antenatal_weight; } ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('height'); ?></th>
                            <td><?php if(!empty($result->antenatal_height)){ echo $result->antenatal_height; } ?></td>
                            <th><?php echo $this->lang->line('date'); ?></th>
                            <td><?php  if(!empty($result->antenatal_date)) {
                                    echo $this->customlib->YYYYMMDDHisTodateFormat($result->antenatal_date);
                                } ?> </td>
                        </tr>                      
                        <tr>
                            <th width="25%"><?php echo $this->lang->line("discharge"); ?></th>
                            <td width="25%"><?php if(!empty($result->discharge)){ echo $result->discharge; } ?></td>   
                            <th width="25%"><?php echo $this->lang->line("oedema"); ?></th>
                            <td width="25%"><?php if(!empty($result->oedema)){ echo $result->oedema; } ?></td>
                        </tr> 
                        <tr>        
                            <th><?php echo $this->lang->line('condition'); ?></th>
                            <td ><?php if(!empty($result->general_condition)){ echo $result->general_condition ; } ?> </td>
                            <th><?php echo $this->lang->line('special_findings_and_remark'); ?></th>
                            <td><?php if(!empty($result->finding_remark)){ echo $result->finding_remark ; } ?></td>
                        </tr>
                        <tr>        
                            <th><?php echo $this->lang->line('pelvic_examination'); ?></th>
                            <td><?php if(!empty($result->pelvic_examination)){ echo $result->pelvic_examination ; } ?></td>       
                            <th><?php echo $this->lang->line('sp'); ?></th>
                            <td><?php if(!empty($result->sp)){ echo $result->sp ; } ?></td>
                        </tr> 
                    </table>
                    <hr/>
                    <h4><?php echo $this->lang->line('antenatal_examine'); ?></h4>
                     <table width="100%" class="printablea4">
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('uter_size'); ?></th>
                            <td width="25%"><?php if(!empty($result->uter_size)){ echo $result->uter_size ; } ?></td>
                            <th width="25%"><?php echo $this->lang->line('uterus_size'); ?></th>
                            <td width="25%"><?php if(!empty($result->uterus_size)){ echo $result->uterus_size ; } ?></td>                        
                        </tr>
                        <tr> 
                            <th width="25%"><?php echo $this->lang->line('presentation_position'); ?></th>
                            <td width="25%"><?php if(!empty($result->presentation_position)){ echo $result->presentation_position; } ?></td>
                            <th width="25%"><?php echo $this->lang->line('presenting_part_to_brim'); ?></th>
                            <td width="25%"><?php if(!empty($result->brim_presentation)){ echo $result->brim_presentation; } ?></td> 
                        </tr>
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('foeta_heart'); ?></th>
                            <td width="25%"><?php if(!empty($result->foeta_heart)){ echo $result->foeta_heart; } ?> </td>
                            <th width="25%"><?php echo $this->lang->line('blood_pressure'); ?></th>
                            <td><?php if(!empty($result->blood_pressure)){ echo $result->blood_pressure; } ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('vaginal'); ?></th>
                            <td><?php if(!empty($result->vaginal)){ echo $result->vaginal; } ?></td>
                            <th width="25%"><?php echo $this->lang->line('antenatal_weight'); ?></th>
                            <td><?php if(!empty($result->antenatal_weight)){ echo $result->antenatal_weight; } ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('antenatal_oedema'); ?></th>
                            <td><?php if(!empty($result->antenatal_oedema)){ echo $result->antenatal_oedema; } ?></td>
                            <th width="25%"><?php echo $this->lang->line('urine_sugar'); ?></th>
                            <td><?php if(!empty($result->urine_sugar)){ echo $result->urine_sugar; } ?></td>
                        </tr>
                        <tr>                            
                            <th width="25%"><?php echo $this->lang->line('urine_aaibumen'); ?></th>
                            <td><?php if(!empty($result->urine)){ echo $result->urine; } ?></td>
                            <th width="25%"><?php echo $this->lang->line('remark') ; ?></th>
                            <td width="25%"><?php if(!empty($result->remark)){ echo $result->remark; } ?></td>
                        </tr> 
                        <tr>
                            <th width="25%"><?php echo $this->lang->line('next_visit'); ?></th>
                            <td width="25%"><?php if(!empty($result->next_visit)){ echo $result->next_visit; } ?></td>
                        </tr> 
                         <?php                                                              
                        if (!empty($fields_prescription)) {
                            $display_field = '';
                            foreach ($fields_prescription as $fields_key => $fields_value) {                            
                            ?>
                                <tr>
                                    <th><?php echo $fields_value->name; ?></th>
                                    <td colspan="3"><?php if(!empty($result->{"$fields_value->name"})){ echo $result->{"$fields_value->name"} ; }?></td>
                                </tr>
                            <?php
                            }
                        } 
                        ?>
                    </table>
                    <div class="divider mt-10 mb-10"></div>                 
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </div>
    </div>
</td></tr></tbody>
    <tfoot><tr><td>

    <?php
                    if (!empty($print_details[0]['print_footer'])) {
                        ?>
       <div class="footer-space">&nbsp;</div>
  <?php
}
?>



    </td></tr></tfoot>
  </table>
  <?php
                    if (!empty($print_details[0]['print_footer'])) {
                        ?>
  <div class="footer-fixed">
  
  <?php   echo $print_details[0]['print_footer'];?>
                
  </div>
  <?php
}
?>
</html>