<div class="row">
    <!-- uncomment code for absolute positioning tweek see top comment in css -->
    <!-- <div class="absolute-wrapper"> </div> -->
    <!-- Menu -->
<div class="wrapper home-title no-print" >
    <div class="header-title">
        
  <a href=""><img src="<?=base_url();?>public/images/logob.png"><h4>Resource Portal</h4></a>
    
    </div>
</div>
<div class="header-title-divider no-print">!</div>
    <div class="side-menu no-print">
    
    <nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <div class="brand-wrapper">
            <!-- Hamburger -->
            <button type="button" class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Brand -->
            <div class="brand-name-wrapper">
                <a class="navbar-brand" href="<?=site_url('dashboard');?>">
                    Dashboard
                </a>
            </div>

            <!-- Search -->
            <a data-toggle="collapse" href="#search" class="btn btn-default" id="search-trigger">
                <span class="glyphicon glyphicon-search"></span>
            </a>

            <!-- Search body -->
            <div id="search" class="panel-collapse collapse">
                <div class="panel-body">
                    <form class="navbar-form" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search">
                        </div>
                        <button type="submit" class="btn btn-default "><span class="glyphicon glyphicon-ok"></span></button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Menu -->
    <div class="side-menu-container">
        <ul class="nav navbar-nav">

            <li class="panel panel-default" id="dropdown"><a  data-toggle="collapse" href="#registration"><span class="fa fa-users"></span> Accounts  <span class="caret"></span></a>
                <div id="registration" class="panel-collapse collapse">
                    <div class="panel-body">
                        <ul class="nav navbar-nav">
                            <li><a href="<?=site_url('accounts/guest');?>">Public</a></li>

                            <li><a href="<?=site_url('accounts/students');?>">Student</a></li>
                           
                            <?php if ($this->session->userdata['permit'] == 'staffs' || $this->aauth->is_admin()): ?>
                            <li><a href="<?=site_url('accounts/instructors');?>">Instructor</a></li>
                                
                            <?php endif ?>
                            <?php if ($this->aauth->is_admin()): ?>
                                <li><a href="<?=site_url('accounts/staff');?>">Staff / Subadmin</a></li>
                            
                            <?php endif ?>
                        </ul>
                    </div>
                </div>
            </li>
            <li class="active"><a href="<?=site_url('file');?>"><span class="glyphicon glyphicon-plane"></span> Files</a></li>

            <!-- Dropdown-->
            <li class="panel panel-default" id="dropdown">
                <a data-toggle="collapse" href="#dropdown-lvl1">
                    <span class="glyphicon glyphicon-user"></span> Reports<span class="caret"></span>
                </a>

                <!-- Dropdown level 1 -->
                <div id="dropdown-lvl1" class="panel-collapse collapse">
                    <div class="panel-body">
                        <ul class="nav navbar-nav">
                            <li class=''><a href="<?=site_url();?>/reports/upload">Upload logs</a></li>
                            <li><a href="<?=site_url();?>/reports/download">Download logs</a></li>                            
                            <li class='hidden'><a href="<?=site_url();?>/reports/userlogs">User logs</a></li>

                        </ul>
                    </div>
                </div>
            </li>

     
            <li><a href="<?=site_url('user');?>"><span class="fa fa-sign-in"></span> User panel</a></li>

            <li><a href="<?=site_url('logout');?>"><span class="fa fa-sign-out"></span> Logout</a></li>

        </ul>
    </div><!-- /.navbar-collapse -->
</nav>
    
    </div>