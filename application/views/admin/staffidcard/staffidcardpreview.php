<style type="text/css">
            { margin:0; padding: 0;}
            .tc-container{width: 100%;position: relative; text-align: center;}
            .tcmybg {
                background: top center;
                background-size: contain;
                position: absolute;
                left: 0;
                bottom: 10px;
                width: 200px;
                height: 200px;
                margin-left: auto;
                margin-right: auto;
                right: 0;
            }
            .patienttop img{width:30px;vertical-align: initial;}
            .patienttop{background: <?php echo $idcard->header_color; ?>;padding:2px;color: #fff;overflow: hidden;
                        position: relative;z-index: 1;}
            .sttext1{font-size: 24px;font-weight: bold;line-height: 30px;}
            .stgray{background: #efefef;padding-top: 5px; padding-bottom: 10px;}
            .staddress{margin-bottom: 0; padding-top: 2px;}
            .stdivider{border-bottom: 2px solid #000;margin-top: 5px; margin-bottom: 5px;}
            .stlist{padding: 0; margin:0; list-style: none;}
            .stlist li{text-align: left;display: inline-block;width: 100%;padding: 0px 5px;}
            .stlist li span{width:65%;float: right;}
            .stimg{width: 80px;height: auto;}
            .stimg img{width: 100%;height: auto;border-radius: 2px;display: block;}
            .staround{padding:3px 10px 3px 0;position: relative;overflow: hidden;}
            .staround2{position: relative; z-index: 9;}
            .stbottom{background: #453278;height: 20px;width: 100%;clear: both;margin-bottom: 5px;}
            .principal{margin-top: -40px;margin-right:10px; float:right;}
            .stred{color: #000;}
            .spanlr{padding-left: 5px; padding-right: 5px;}
            .cardleft{width: 20%;float: left;}
            .cardright{width: 77%;float: right; }
            .barcodeimg{ width: 80px; height: auto; }
            .barcodeimg img{width: 100%;height: auto;border-radius: 2px;display: block;margin-top:2px;}
</style>

        <?php $dummy_date = "2020-01-01"; ?>
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr> 
                <td valign="top" width="32%" style="padding: 3px;">
                    <table cellpadding="0" cellspacing="0" width="100%" class="tc-container" style="background: #f0f8fd;">
                        <tr>
                            <td valign="top">
                                <?php if($idcard->background != ''){ ?>
                                <img src="<?php echo base_url('uploads/staff_id_card/background/'.$idcard->background.img_time()) ?>" class="tcmybg" style="opacity: .1"/>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <div class="patienttop">
                                    <div class="sttext1">
                                    <?php if($idcard->logo != ''){ ?>
                                    <img src="<?php echo base_url('uploads/staff_id_card/logo/'.$idcard->logo.img_time()) ?>"  />
                                    <?php } ?>
                                        <?php echo $idcard->hospital_name; ?></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" class="text-center" style="padding: 1px 0;">
                                <p><?php echo $idcard->hospital_address; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" style="color: #fff;font-size: 16px; padding: 2px 0 0; position: relative; z-index: 1;background: <?php echo $idcard->header_color; ?>;text-transform: uppercase;"><?php echo $idcard->title; ?></td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <div class="staround">
                                    <div class="cardleft">
                                        <div class="stimg">
                                            <img src="<?php echo base_url('uploads/patient_images/no_image.png'.img_time()) ?>" class="img-responsive" />
                                        </div>
                                         <?php
                                    if ($idcard->enable_staff_barcode) {
                                        if($scan_type == "qrcode"){ ?>                                        
                                        <?php if (file_exists("./uploads/staff_id_card/qrcode/default.png")) { ?>
                                            <div class="barcodeimg">
                                                <img src="<?php echo base_url('./uploads/staff_id_card/qrcode/default.png');?>" />
                                            </div>
                                    <?php  }   ?>
                                    <?php
                                    }elseif ($scan_type == "barcode") { ?>                                        
                                    <?php if (file_exists("./uploads/staff_id_card/barcodes/default.png")) {
                                     ?>
                                     <div class="barcodeimg text-center">
                                         <img src="<?php echo base_url('./uploads/staff_id_card/barcodes/default.png');?>" />
                                     </div>
                                    <?php  }  } } ?>
                                    </div><!--./cardleft-->
                                    <div class="cardright">
                                        <ul class="stlist">
                                            <?php
                                            if ($idcard->enable_name == 1) {
                                                echo "<li>"; echo $this->lang->line('staff_name'); echo "<span>Mohan Patil</span></li>";
                                            } 
                                            ?>
                                            <?php
                                            if ($idcard->enable_staff_id == 1) {
                                                echo "<li>"; echo $this->lang->line('staff_id');echo "<span>9000</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_designation == 1) {
                                                echo "<li>"; echo $this->lang->line('designation');echo "<span>Administator</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_staff_department == 1) {
                                                 echo "<li>"; echo $this->lang->line('department');echo "<span>Admin</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_fathers_name == 1) {
                                                echo "<li>"; echo $this->lang->line('father_name'); echo "<span>Sohan Patil</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_mothers_name == 1) {
                                                echo "<li>"; echo $this->lang->line('mother_name'); echo "<span>Kirti Patil</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_date_of_joining == 1) {
                                                echo "<li>"; echo $this->lang->line('date_of_joining'); echo "<span>"; echo $this->customlib->YYYYMMDDTodateFormat($dummy_date); echo "</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_permanent_address == 1) {
                                                echo "<li>"; echo $this->lang->line('address'); echo "<span>Near Railway Station Jabalpur</span></li>";
                                            }
                                            ?>
                                             <?php
                                            if ($idcard->enable_staff_phone == 1) {
                                                echo "<li>"; echo $this->lang->line('phone'); echo "<span>9845624781</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_staff_dob == 1) {
                                                echo "<li>"; echo $this->lang->line('date_of_birth'); echo "<span>"; echo $this->customlib->YYYYMMDDTodateFormat($dummy_date); echo "</span></li>";
                                            }
                                            ?>
                                        </ul>
                                    </div><!--./cardright-->
                                </div><!--./staround-->
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" class="principal text-right">
                            <?php if($idcard->sign_image != ''){ ?>
                            <img src="<?php echo base_url('uploads/staff_id_card/signature/'.$idcard->sign_image.img_time()) ?>" width="66" height="40" />
                            <?php } ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>  
        </table>