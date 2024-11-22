<?php
$currency_symbol = $this->customlib->getHospitalCurrencyFormat();
?>

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary border0 mb0">
                    <?php $this->load->view('admin/report/_appointment');?>
                </div>
            </div>  
        </div>    
   </section>
</div>
