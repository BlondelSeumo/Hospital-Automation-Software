 <?php 
        if(!empty($result)){
        $currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>
<div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form id="view" accept-charset="utf-8" method="get" class="ptt10">
                            <div class="table-responsive">
                                <table class="table mb0 table-striped table-bordered examples tablelr0space">
                                    <tbody>
                                    <tr>
                                        <th><?php echo $this->lang->line('charge_type'); ?></th>
                                        <td><span id="charge_types"><?php echo $result->charge_type_name; ?></span></td>
                                        <th><?php echo $this->lang->line('charge_category'); ?></th>
                                        <td><span id="charge_categorys"><?php echo $result->charge_category_name; ?></span></td>
                                        
                                    </tr>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <td><span id="codes"><?php echo $result->name; ?></span>
                                        </td>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <td><span id="descriptions"><?php echo $result->description; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th ><?php echo $this->lang->line('standard_charge'); ?> (<?php echo $currency_symbol; ?>)</th>
                                        <td><span id="standard_charges"><?php echo $result->standard_charge; ?></span>
                                        </td>
                                        <th ><?php echo $this->lang->line('tax'); ?> (%)</th>
                                        <td><span id="standard_charges"><?php echo $result->percentage; ?></span>
                                        </td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                            </div>
                        </form>
                    </div><!--./col-md-12-->
                </div>

                <table class="table table-striped table-bordered table-hover mt20">
    <thead>
        <tr>
            <th><?php echo $this->lang->line('schedule_charge_name'); ?></th>
            <th class="text-right"><?php echo $this->lang->line('charges'); ?> (<?php echo $currency_symbol; ?>)</th>
        </tr>
    </thead>
    <tbody>
       <?php 
foreach ($result->organisation_charges as $org_charge_key => $org_charge_value) {
    ?>
<tr>
    <td><?php echo $org_charge_value->organisation_name; ?></td>
    <td class="text-right"><?php echo $org_charge_value->org_charge; ?></td>
</tr>
    <?php
}
        ?>
        </tbody>
</table>
<?php
}else{

}
  ?>