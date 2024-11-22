<?php
$CI =& get_instance();
$CI->load->library('customlib');
?>
<style>
    .ui-accordion{border: 1px solid #f4f4f4;}    
    .panel-heading {
        padding: 0;
        border: 0;
    }
    .panel-title > a,
    .panel-title > a:active {
        display: block;
        padding: 15px;
        color: #555;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        word-spacing: 3px;
        text-decoration: none;
    }
    .panel-heading a:before {
        font-family: 'FontAwesome';
        content: "\f106";
        float: right;
        transition: all 0.5s;
    }
    .panel-heading.active a:before {
        -webkit-transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        transform: rotate(180deg);
    }
    .accordianheader {
        color: #000;
        background: #fff;
        padding: 10px 10px;
        margin-bottom: 0px;
        border-top: 1px solid #ddd;
        position: relative;
        overflow: hidden;
        outline: 0;
        cursor: pointer;
    }
    .accordianbody {
        background: #f4f4f4;
    }
    .accordianbody i {
        color: #fff !important;
        position: absolute;
        right: 20px;
        top: 14px;
        -webkit-transition: all 300ms ease-in 0s;
        -moz-transition: all 300ms ease-in 0s;
        -o-transition: all 300ms ease-in 0s;
        transition: all 300ms ease-in 0s;
    }
    .ui-state-active i {
        color: #000;
        -webkit-transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        -o-transform: rotate(180deg);
        -ms-transform: rotate(180deg);
        transform: rotate(180deg);
        -webkit-transition: all 300ms ease-in 0s;
        -moz-transition: all 300ms ease-in 0s;
        -o-transition: all 300ms ease-in 0s;
        transition: all 300ms ease-in 0s;
    } 
    .notigybg{background: #fafafa; padding: 10px; overflow: hidden;font-weight: bold;}
    .notifyleft{width: 8%; float: left;}
    .notifymiddle{width: 70%; float: left;}
    .notifyright{width: 18%; float: left;}
    .noteangle{position: absolute; right:20px; font-size: 18px; top:20px;}
    .note-content{padding-left: 8.5%;padding-bottom: 15px;}
    .noteDM10{padding-top: 10px;}
    .unreadbg{background:#e1eeff;}
    .readbg{background:#fff;}
    .accordianheader{-webkit-transition: all 0.5s ease 0s;
                     -moz-transition: all 0.5s ease 0s;
                     -ms-transition: all 0.5s ease 0s;
                     -o-transition: all 0.5s ease 0s;
                     transition: all 0.5s ease 0s;}
    .accordianheader:focus,
    .accordianheader:visited,
    .accordianheader:hover{background:#f5f5f5;}
    @media(max-width:767px){
        .notifyleft{width: 60px;}
        .notifymiddle{width: 40%;}
        .notifyright{width: 20%;}
        .noteDM10{padding-top: 0px;}
        .note-content{padding-left: 70px;}
    }
</style>
<script>
    function updateStatus(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'patient/systemnotifications/updateStatus/',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (res) {

            }
        })
    }

    $(function () {
        $(".accordianheader").click(function () {
            var id = $(this).attr("data-noticeid");
            $(this).addClass('readbg');
            updateStatus(id);           
        });
    });
</script>
<div class="content-wrapper">
    <section class="content">
        <div class="row"> 
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('notifications'); ?></h3>
                         <div class="box-tools pull-right">
                            <button class="btn btn-primary btn-sm checkbox-toggle delete_all"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete_all'); ?></button>
                        </div>   
                    </div>
                    <div class="box-body">
                        <?php                         
                          if (!empty($notifications)) {
                        ?>
                        <div id="accordion">
                            <div class="sysnbg">
                                <div class="sysm-main sysmleft font-weight-bold"><?php echo $this->lang->line('type'); ?></div>
                                <div class="sysm-main sysmmiddle font-weight-bold"><?php echo $this->lang->line('subject'); ?></div>
                                <div class="sysm-main sysmlast font-weight-bold"><?php echo $this->lang->line('date'); ?></div>
                            </div>                       
                            <?php if (!empty($notifications)) {                         

    $count = 1;
    $color = "";
    foreach ($notifications as $result) {
        if ((!empty($result['read'])) && ($result['read'] == 'no')) {
            $class = "readbg";
        } else {
            $class = "unreadbg";
        }
        ?>
                                    <div class="accordianheader <?php echo $class ?>" data-noticeid="<?php echo $result['id'] ?>">
                                        <div class="sysm-main sysmleft">
                                            <div class="bellcircle">
                                                <?php
                                                    $class = $CI->customlib->notification_icon($result['notification_type']);
                                                ?>
                                                <i class="<?php echo $class; ?>" style="transform: rotate(0deg); color: #fff;"></i>
                                            </div>
                                        </div>
                                        <div class="sysm-main sysmmiddle sysmtop10"><?php echo $result['notification_title']; ?></div>
                                        <div class="sysm-main sysmlast sysmtop10"><?php echo $this->customlib->YYYYMMDDHisTodateFormat($result['date'], $this->customlib->getHospitalTimeFormat()); ?></div>
                                        <div class="sysmangle"><i class="fa fa-angle-down" ></i>
                                        </div>
                                    </div>
                                    <div class="accordianbody relative">
                                        <div class="sysbottomcontent">
                                            <?php echo $result['notification_desc']; ?>
                                        </div>
                                    </div>
                                    <?php
                                $count++;
                                    }
                                }
                                ?>
                        </div>

                        <br /> <br />
                        <ul class="pagination">
<?php echo $this->pagination->create_links(); ?>

                        </ul>
                                 <?php 
                         }else{
                             
                             ?>
                <div class="alert alert-info"><?php echo $this->lang->line('no_record_found');?></div>
                                 <?php
                         }
                         
                        ?>
                    
                    </div>
                </div>
            </div><!--./row-->
    </section>
</div>

<script src="<?php echo base_url() ?>backend/js/Chart.bundle.js"></script>
<script src="<?php echo base_url() ?>backend/js/utils.js"></script>
<script type="text/javascript">
    $(document).on('click','.delete_all',function(){
     delete_recordByIdReload('patient/systemnotifications/deleteall');
    });

    $("#accordion").accordion({
        heightStyle: "content",
        active: true,
        collapsible: true,
        header: ".accordianheader"
    });

</script> 
<!-- https://bootsnipp.com/snippets/Q6zjv -->