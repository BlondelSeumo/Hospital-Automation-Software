<style type="text/css">
            { margin:0; padding: 0;}
            .tc-container{width: 100%;position: relative; text-align: center;}
            .tcmybg {
                background: top center;
                background-size: contain;
                position: absolute;
                left: 0;
                bottom: 10px;
                width: 160px;
                height: 160px;
                margin-left: auto;
                margin-right: auto;
                right: 0;
            }
            .patientmain{background: #efefef;width: 100%; margin-bottom: 30px;}
            .patienttop img{height:30px;vertical-align: initial;}
            .patienttop{background: <?php echo $idcard->header_color; ?>;padding:2px;color: #fff;overflow: hidden;position: relative;z-index: 1;}
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
            .cardright{width: 77%;float: right;}
            .barcodeimg{ width: 80px; height: auto;}
            .barcodeimg img{width: 100%;height: auto;border-radius: 2px;display: block;margin-top:2px;}

</style>

<table cellpadding="0" cellspacing="0" width="100%">
    <tr> 
        <td valign="top" width="32%" style="padding: 3px;">
        <table cellpadding="0" cellspacing="0" width="100%" class="tc-container" style="background: #f0f8fd;">
            <tr>
                <td valign="top">
                    <?php if($idcard->background != ''){ ?>
                    <img src="<?php echo base_url('uploads/patient_id_card/background/'.$idcard->background.img_time()); ?>" class="tcmybg" style="opacity: .1"/>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <div class="patienttop">
                        <div class="sttext1">
                        <?php if($idcard->logo != ''){ ?>
                        <img src="<?php echo base_url('uploads/patient_id_card/logo/'.$idcard->logo.img_time()) ?><?php echo $idcard->logo; ?>"  /><?php } ?>
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
                                    if ($idcard->enable_barcode) {
                                        if($scan_type == "qrcode"){ ?>                                        
                                        <?php if (file_exists("./uploads/patient_id_card/qrcode/default.png")) { ?>
                                            <div class="barcodeimg">
                                                <img src="<?php echo base_url('./uploads/patient_id_card/qrcode/default.png');?>"  />
                                            </div>
                                    <?php  }   ?>
                                    <?php
                                    }elseif ($scan_type == "barcode") { ?>                                        
                                    <?php if (file_exists("./uploads/patient_id_card/barcodes/default1.png")) {
                                     ?>
                                     <div class="barcodeimg text-center">
                                         <img src="<?php echo base_url('./uploads/patient_id_card/barcodes/default1.png');?>" />
                                     </div>
                                    <?php  }  } } ?>

                                    </div><!--./cardleft-->
                                    <div class="cardright">
                                        <ul class="stlist">
                                            <?php
                                            if ($idcard->enable_patient_name == 1) {
                                                echo "<li>".$this->lang->line('patient_name')."<span> James Bond</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_guardian_name == 1) {
                                                echo "<li>".$this->lang->line('guardian_name')."<span> Guardian Name</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_patient_unique_id == 1) {
                                                echo "<li>".$this->lang->line('patient_unique_id')."<span> 1001</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_address == 1) {
                                                echo "<li>".$this->lang->line('address')."<span>D.No.1 Street Name Address Line 2 Address Line 3</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_phone == 1) {
                                                echo "<li>".$this->lang->line('phone')."<span>1234567890</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_dob == 1) {
                                                echo "<li>".$this->lang->line('dbo')."<span>25.06.2006</span></li>";
                                            }
                                            ?>
                                            <?php
                                            if ($idcard->enable_blood_group == 1) {
                                                echo "<li class='stred'>".$this->lang->line('blood_group')."<span>A+</span></li>";
                                            }
                                            ?>

                                        </ul>
                                    </div><!--./cardright-->
                                </div><!--./staround-->
                            </td>
                        </tr>
                <tr>
					<td valign="top" class="principal text-right">
                        <?php if(!empty($idcard->sign_image)){ ?>
                        <img src="<?php echo base_url('uploads/patient_id_card/signature/'.$idcard->sign_image.img_time()) ?>" width="66" height="40" />
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>  
</table>