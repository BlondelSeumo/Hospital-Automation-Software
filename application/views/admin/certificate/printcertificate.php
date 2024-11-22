
<style type="text/css">
    *{padding: 0; margin:0;}
    body{ font-family: 'arial';}
    .tc-container{width: 100%;position: relative; text-align: center;padding: 2%;}
    .tc-container tr td{vertical-align: bottom;}
    /*.tc-container{
        width: 100%;
        padding: 2%;
        position: relative;
        z-index: 2;
    }*/
    .tcmybg {
        background:top center;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1;
    }
    .tc-container tr td h1, h2 ,h3{margin-top: 0; font-weight: normal;}
    /*@media (max-width:210mm) and (min-width:297mm){
        .tc-container{
            margin-top: 200px;
            margin-bottom: 100px;}
    }*/
</style>

<?php

$certificate[0]->certificate_text = str_replace('[name]', '[patient_name] ', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[patient_id]', '[patient_id] ', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[dob]', '[dob]', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[age]', '[age]', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[email]', '[email]', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[phone]', '[mobileno]', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[address]', '[address]', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[gender]', '[gender]', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[guardian_name]', '[guardian_name]', $certificate[0]->certificate_text);
$certificate[0]->certificate_text = str_replace('[consultant_doctor]', '[doctorname]', $certificate[0]->certificate_text);


if ($certificate[0]->module == "opd") {
   $certificate[0]->certificate_text = str_replace('[opd_ipd_no]', '[opd_no]', $certificate[0]->certificate_text);
     $certificate[0]->certificate_text = str_replace('[opd_checkup_id]', '[checkup_id]', $certificate[0]->certificate_text);
}elseif ($certificate[0]->module == "ipd") {
   $certificate[0]->certificate_text = str_replace('[opd_ipd_no]', '[ipd_no]', $certificate[0]->certificate_text);
}

foreach ($patients as $patient) {
	
    $certificate_body = "";
    $certificate_body = $certificate[0]->certificate_text;
    $patient->age= "";
    $patient->ipd_no = "";
    $patient->opd_no = "";
    $opd_no="";
    $ipd_no="";
    $age="";
    $checkup_id="";
     
    foreach ($patient as $pat_key => $pat_value) {
       
        if ($pat_key == "dob") {

            if ($pat_value != "0000-00-00") {
                $age = $this->customlib->getAgeBydob($pat_value);
                if($pat_value){
                    $pat_value = $this->customlib->YYYYMMDDTodateFormat($pat_value);
                }else{
                    $pat_value = '';
                }
            }

        }
        
        if ($pat_key == "age") {
            $pat_value = $age ;
        }

        if($pat_key == "id"){
            if ($certificate[0]->module == "opd") {
             $opd_no = $this->customlib->getSessionPrefixByType('opd_no'). $pat_value;
         }else{
             $ipd_no = $this->customlib->getSessionPrefixByType('ipd_no'). $pat_value;
         }
        }

        if($pat_key == "checkup_id"){
             $checkup_id = $this->customlib->getSessionPrefixByType('checkup_id'). $pat_value;
             $pat_value = $checkup_id;
        }

        if($pat_key == "ipd_no"){
            $pat_value = $ipd_no;
        }
        if($pat_key == "opd_no"){
            $pat_value = $opd_no;
        }      
        
        $certificate_body = $pat_value ? str_replace('[' . $pat_key . ']', $pat_value, $certificate_body) : $certificate_body;       
        
    }

    ?>


    <div class="" style="position: relative; text-align: center; font-family: 'arial';">
        <?php if (!empty($certificate[0]->background_image)) {?>
            <img src="<?php echo base_url('uploads/certificate/' . $certificate[0]->background_image.img_time()); ?>" style="width: 100%; height: 100vh" />
        <?php }?>

        <table width="100%" cellspacing="0" cellpadding="0" style="position: absolute;top: 0; margin-left: auto;margin-right: auto;left: 0;right: 0;<?php echo "width:" . $certificate[0]->content_width . "px" ?>">
            <tr>
                <td style="position: absolute;left:0;">
                    <?php if ($certificate[0]->enable_patient_image == 1) {?>
                        <img style="position: relative; <?php echo "top:" . $certificate[0]->enable_image_height . "px" ?>;" src="<?php echo base_url($patient->image.img_time()); ?>" width="100" height="auto">
                    <?php }?>
                </td>
            </tr>
            <tr>
                <td valign="top" style="text-align:left; position: relative; <?php echo "top:" . $certificate[0]->header_height . "px" ?>"><?php echo $certificate[0]->left_header ?></td>
                <td valign="top" style="text-align:center; position: relative; <?php echo "top:" . $certificate[0]->header_height . "px" ?>"><?php echo $certificate[0]->center_header ?></td>
                <td valign="top" style="text-align:right; position: relative; <?php echo "top:" . $certificate[0]->header_height . "px" ?>"><?php echo $certificate[0]->right_header ?></td>
            </tr>
            <tr>
                <td colspan="3" valign="top" style="position: relative; <?php echo "top:" . $certificate[0]->content_height . "px" ?>">
                    <p style="font-size: 14px; line-height: 24px; text-align:center;"><?php echo $certificate_body;

    ?></p></td>
            </tr>
            <tr>
                <td valign="top" style="text-align:left;position: relative; <?php echo "top:" . $certificate[0]->footer_height . "px" ?>"><?php echo $certificate[0]->left_footer ?></td>
                <td valign="top" style="text-align:center;position: relative; <?php echo "top:" . $certificate[0]->footer_height . "px" ?>"><?php echo $certificate[0]->center_footer ?></td>
                <td valign="top" style="text-align:right;position: relative; <?php echo "top:" . $certificate[0]->footer_height . "px" ?>"><?php echo $certificate[0]->right_footer ?></td>
            </tr>
        </table>
    </div>


    <?php
} //}
?>