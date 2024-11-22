 
    <div class="topnews"> 
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-9">
                    <div class="sidebar">
                        <?php
                        if (in_array('news', json_decode($front_setting->sidebar_options))) {
                            ?>
                            <div class="newstab"><?php echo $this->lang->line('latest_news'); ?></div>
                            <div class="newscontent">
                                <marquee class="" behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                                    <ul id="" class="" >
                                        <?php
                                        if (!empty($banner_notices)) {
                                            foreach ($banner_notices as $banner_notice_key => $banner_notice_value) {
                                                ?>
                                                <li><a href="<?php echo site_url('read/' . $banner_notice_value['slug']) ?>"><div class="datenews"><?php echo date('d', strtotime($banner_notice_value['date'])); ?><span><?php echo date('F', strtotime($banner_notice_value['date'])); ?></span></div><?php echo $banner_notice_value['title']; ?>
                                                    </a></li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </marquee>
                            </div><!--./newscontent-->
                            <?php
                        }
                        ?>
                    </div><!--./sidebar-->  
                </div><!--./col-md-9--> 
                <?php if($patientpanel == 'enabled'){ ?>
                <div class="col-lg-2 col-md-2 col-sm-32">
                    <ul class="top-right">
                        <li><a href="<?php echo site_url('site/userlogin') ?>"><i class="fa fa-user"></i><?php echo $this->lang->line('login'); ?></a></li>

                    </ul>
                </div><!--./col-md-5-->
                 <?php } ?>
            </div>
        </div>
    </div>  
    <div class="sticky-top2">   
    <header>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <nav class="navbar">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-3">
                                <span class="sr-only"><?php echo $this->lang->line('toggle_navigation'); ?></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand logo" href="<?php echo base_url(); ?>"><img src="<?php echo base_url($front_setting->logo); ?>" alt=""/></a>
                        </div>
                        <div class="collapse navbar-collapse" id="navbar-collapse-3">
                            <ul class="nav navbar-nav navbar-right">
                                <?php
                                foreach ($main_menus as $menu_key => $menu_value) {

                                    $submenus = false;
                                    $cls_menu_dropdown = "";
                                    $menu_selected = "";
                                    if ($menu_value['page_slug'] == $active_menu) {
                                        $menu_selected = "active";
                                    }

                                    if (!empty($menu_value['submenus'])) {
                                        $submenus = true;
                                        $cls_menu_dropdown = "dropdown";
                                    }
                                    if ($menu_value['menu'] == $active_menu) {
                                        $menu_selected = "active";
                                    }
                                    ?>
                                    <li class="<?php echo $menu_selected . " " . $cls_menu_dropdown; ?>" >
                                        <?php
                                        if (!$submenus) {
                                            $top_new_tab = '';
                                            $url = '#';
                                            if ($menu_value['open_new_tab']) {
                                                $top_new_tab = "target='_blank'";
                                            }
                                            if ($menu_value['ext_url']) {
                                                $url = $menu_value['ext_url_link'];
                                            } else {
                                                $url = site_url($menu_value['page_url']);
                                            }
                                            ?>
                                            <a href="<?php echo $url; ?>" <?php echo $top_new_tab; ?>><?php echo $menu_value['menu']; ?></a>
                                            <?php
                                        } else {
                                            $child_new_tab = '';
                                            $url = '#';
                                            ?>
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $menu_value['menu']; ?> <b class="caret"></b></a>
                                            <ul class="dropdown-menu">
                                                <?php
                                                foreach ($menu_value['submenus'] as $submenu_key => $submenu_value) {
                                                    if ($submenu_value['open_new_tab']) {
                                                        $child_new_tab = "target='_blank'";
                                                    }
                                                    if ($submenu_value['ext_url']) {
                                                        $url = $submenu_value['ext_url_link'];
                                                    } else {
                                                        $url = site_url($submenu_value['page_url']);
                                                    }
                                                    ?>
                                                    <li><a href="<?php echo $url; ?>" <?php echo $child_new_tab; ?> ><?php echo $submenu_value['menu'] ?></a></li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                        }
                                        ?>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div><!-- /.navbar-collapse -->
                    </nav><!-- /.navbar -->
                </div>
            </div>
        </div>   
    </header> 
</div>