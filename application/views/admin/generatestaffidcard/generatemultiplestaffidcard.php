<style type="text/css">
    *{ margin:0; padding: 0;}
    table{ font-family: 'arial'; margin:0; padding: 0;font-size: 12px; color: #000;}
    .tc-container{width: 100%;position: relative; text-align: center;margin-bottom:60px;padding-bottom: 10px;}
    .tcmybg {
        background: top center;
        background-size: contain;
        position: absolute;left: 0; bottom: 10px;
        width: 160px;height: 160px;
        margin-left: auto;margin-right: auto;
        right: 0;opacity: 0.10;}
    .patientmain{background: #efefef;width: 100%; margin-bottom: 30px;}
    .patienttop img{width:30px;vertical-align: initial;}
    .patienttop{background: #453278;padding:2px;color: #fff;overflow: hidden;position: relative;z-index: 1;}
    .sttext1{font-size: 24px;font-weight: bold;line-height: 30px;}
    .stgray{background: #efefef;padding-top: 5px; padding-bottom: 10px;}
    .staddress{margin-bottom: 0; padding-top: 2px;}
    .stdivider{border-bottom: 2px solid #000;margin-top: 5px; margin-bottom: 5px;}
    .stlist{padding: 0; margin:0; list-style: none;}
    .stlist li{text-align: left;display: inline-block;width: 100%;padding: 0px 5px;}
    .stlist li span{width:65%;float: right;}
    .stimg{ width: 80px; height: auto;}
    .stimg img{width: 100%;height: auto;border-radius: 2px;display: block;}
    .staround{padding:3px 10px 3px 0;position: relative;overflow: hidden;}
    .staround2{position: relative; z-index: 9;}
    .stbottom{background: #453278;height: 20px;width: 100%;clear: both;margin-bottom: 5px;}
    .principal{margin-top: -40px;margin-right:10px; float:right;}
    .stred{color: #000;}
    .spanlr{padding-left: 5px; padding-right: 5px;}
    .cardleft{width: 20%;float: left;}
    .cardright{width: 77%;float: right; }
    .width32{width: 32.75%; padding: 3px; float: left;}
    .barcode{display:block; text-align: center;  margin-top: 1px;}
    .vertlist{padding: 0; margin:0; list-style: none;}
    .vertlist li{text-align: left;display: inline-block;width: 100%;color: #000;padding-bottom: 2px;}
    .vertlist li span{width:55%;float: right;}
    .barcodeimg{display: block;margin-top: 2px;text-align: center;}

</style>
<?php $i=0; ?>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <?php
        foreach ($staffs as $staff_value) {
            $i++;
            ?>
            <td valign="top" class="width32">
                <table cellpadding="0" cellspacing="0" width="100%" class="tc-container" style="background: #f0f8fd;">
                    <tr>
                        <td valign="top">
                            <img src="<?php echo base_url('uploads/staff_id_card/background/' . $id_card[0]->background.img_time()); ?>" class="tcmybg" /></td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="patienttop" style="background: <?php echo $id_card[0]->header_color ?>">
                                <div class="sttext1">
                                <?php if(!empty($id_card[0]->logo)) { ?>
                                <img src="<?php echo base_url('uploads/staff_id_card/logo/' . $id_card[0]->logo.img_time()); ?>" width="30" height="30" />
                                <?php  } ?>
                                    <?php echo $id_card[0]->hospital_name ?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="text-center" style="padding: 1px 0; position: relative; z-index: 1">
                            <p> <?php echo $id_card[0]->hospital_address ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" style="color: #fff;font-size: 16px; padding: 2px 0 0; position: relative; z-index: 1;background: <?php echo $id_card[0]->header_color ?>;text-transform: uppercase;"><?php echo $id_card[0]->title ?></td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <div class="staround">
                                <div class="cardleft">
                                    <div class="stimg">
                                        <?php if(!empty($staff_value->image)){ ?>
                                        <img src="<?php echo base_url(); ?>uploads/staff_images/<?php echo $staff_value->image.img_time() ?>" class="img-responsive" />
                                        <?php }else{ ?>
                                        <img src="<?php echo base_url('uploads/patient_images/no_image.png'.img_time()); ?>" class="img-responsive" />
                                        <?php } ?>
                                    </div>
                                    <?php if ($id_card[0]->enable_staff_barcode == 1) { ?>
                                     <div class="barcodeimg" style="width: 90%;margin:0 auto">
                                        <img src="<?php echo base_url($staff_value->barcode); ?>" style="max-width: 65px; margin: 0 auto; height:auto;" />
                                     </div>
                                 <?php } ?>
                                </div><!--./cardleft-->
                                <div class="cardright">
                                    <ul class="stlist">
                                        <?php if ($id_card[0]->enable_name == 1) { ?><li><?php echo $this->lang->line('staff_name'); ?><span> <?php echo $staff_value->name; ?> <?php echo $staff_value->surname; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_staff_id == 1) { ?><li><?php echo $this->lang->line('staff_id'); ?><span> <?php echo $staff_value->employee_id; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_designation == 1) { ?><li><?php echo $this->lang->line('designation'); ?><span><?php echo $staff_value->designation; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_staff_department == 1) { ?><li><?php echo $this->lang->line('department'); ?><span> <?php echo $staff_value->department; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_fathers_name == 1) { ?><li><?php echo $this->lang->line('father_name'); ?><span><?php echo $staff_value->father_name; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_mothers_name == 1) { ?><li><?php echo $this->lang->line('mother_name'); ?><span><?php echo $staff_value->mother_name; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_date_of_joining == 1) { ?><li><?php echo $this->lang->line('date_of_joining'); ?><span><?php if(!empty($staff_value->date_of_joining) && $staff_value->date_of_joining !='0000-00-00'){echo date($this->customlib->getHospitalDateFormat(), $this->customlib->dateYYYYMMDDtoStrtotime($staff_value->date_of_joining));} ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_permanent_address == 1) { ?><li class="stred"><?php echo $this->lang->line('address'); ?><span><?php echo $staff_value->local_address; ?></span></li><?php } ?>
                                        <?php if ($id_card[0]->enable_staff_phone == 1) { ?><li><?php echo $this->lang->line('phone'); ?><span><?php echo $staff_value->contact_no; ?></span></li><?php } ?>
                                        <?php
                                        if ($id_card[0]->enable_staff_dob == 1) {
                                            ?>
                                            <li><?php echo $this->lang->line('date_of_birth'); ?>
                                                <span>
                                                    <?php
                                                    echo $dob = "";
                                                    if ($staff_value->dob != "0000-00-00") {
                                                        $dob = date($this->customlib->getHospitalDateFormat(), $this->customlib->dateYYYYMMDDtoStrtotime($staff_value->dob));
                                                    }
                                                    
                                                    echo $dob;
                                                    ?>
                                                </span></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div><!--./cardright-->
                            </div><!--./staround-->
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="principal text-right">
                        <?php if(!empty($id_card[0]->sign_image)){ ?>
                        <img src="<?php echo base_url('uploads/staff_id_card/signature/' . $id_card[0]->sign_image.img_time()); ?>" width="66" height="40" />
                        <?php } ?>
                        </td>
                    </tr>
                </table>
            </td>
            <?php
            if ($i == 3) {
                // three items in a row. Edit this to get more or less items on a row
                ?></tr><tr><?php
                $i = 0;
            }
        }
        ?>
    </tr>
</table>