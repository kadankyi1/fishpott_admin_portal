<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title><?php if($all_notifications_count > 0){echo "(" . $all_notifications_count . ")";} ?> FishPott Administrator</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="../assets/demo/demo.css" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
      <div class="logo">
        <a href="http://company.fishpott.com" class="simple-text logo-normal">
          FishPott
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">
          <li class="nav-item <?php if($page_name_real == "dashboard"){ echo 'active'; } ?> ">
            <a class="nav-link" href="index.php">
              <i class="material-icons">dashboard</i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item <?php if($page_name_real == "current_transfers"){ echo 'active'; } ?> ">
            <a class="nav-link" href="./_1transfers_view_transfers.php?o=1">
              <img class="material-icons" src="../img/transfers.png" style="width: 34px; height: 34px; margin-left: 3px;  margin-right: 10px;">
              <p style="display: inline;">Transfers 
              	<?php if(isset($sidebar_transfers_not) && isset($sidebar_transfers_not_style)){ ?>
              	<span class="menotification" >
              		<span style="height: 25px; width: 25px; <?php echo $sidebar_transfers_not_style; ?>" class="mebadge">
              			<?php echo $sidebar_transfers_not; ?>
              			</span>
              		</span> 
              		<?php } ?>
              	</p>
            </a>
          </li>
          <li class="nav-item <?php if($page_name_real == "current_purchases"){ echo 'active'; } ?> ">
            <a class="nav-link" href="./_1purchases_view_purchases.php?o=1">
              <img class="material-icons" src="../img/purchases.png" style="width: 34px; height: 34px; margin-left: 3px;  margin-right: 10px;">
              <p style="display: inline;">Purchases 
              	<?php if(isset($sidebar_sale_not) && isset($sidebar_sale_not_style)){ ?>
              	<span class="menotification" >
              		<span style="height: 25px; width: 25px; <?php echo $sidebar_sale_not_style; ?>" class="mebadge">
              			<?php echo $sidebar_sale_not; ?>
              			</span>
              		</span> 
              		<?php } ?>
              	</p>
            </a>
          </li>
          <li class="nav-item <?php if($page_name_real == "credit_requests"){ echo 'active'; } ?> ">
            <a class="nav-link" href="./_1wallet_view_credit_alerts.php?o=1">
              <img class="material-icons" src="../img/credit.png" style="width: 34px; height: 34px; margin-left: 3px;  margin-right: 10px;">
              <p style="display: inline;">Credits 
              	<?php if(isset($sidebar_credit_req_not) && isset($sidebar_credit_req_not_style)){ ?>
              	<span class="menotification" >
              		<span style="height: 25px; width: 25px; <?php echo $sidebar_credit_req_not_style; ?>" class="mebadge">
              			<?php echo $sidebar_credit_req_not; ?>
              			</span>
              		</span>
              		<?php } ?>
              	</p>
            </a>
          </li>
          <li class="nav-item <?php if($page_name_real == "current_withdrawals"){ echo 'active'; } ?> ">
            <a class="nav-link" href="./_1withdrawal_requests.php?o=1">
              <img class="material-icons" src="../img/withdraw.png" style="width: 34px; height: 34px; margin-left: 3px; margin-right: 10px;">
              <p style="display: inline;">Withdrawals 
              	<?php if(isset($sidebar_withdrawal_req_not) && isset($sidebar_withdrawal_req_not_style)){ ?>
              	<span class="menotification" >
              		<span style="height: 25px; width: 25px; <?php echo $sidebar_withdrawal_req_not_style; ?>" class="mebadge">
              			<?php echo $sidebar_withdrawal_req_not; ?>
              			</span>
              		</span>
              		<?php } ?>
              	</p>
            </a>
          </li>

          <li style="cursor: pointer;" class="nav-item dropdown  <?php if($page_name_real == "unpaid_dividends"){ echo 'active'; } ?> ">
            <a class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="material-icons" src="../img/dividend.png" style="width: 34px; height: 34px; margin-left: 3px;  margin-right: 10px;">
              <p style="display: inline;">
                Investment Yields
                <?php if(isset($sidebar_dividends_not) && isset($sidebar_dividends_not_style)){ ?>
                <span class="menotification" >
                  <span style="height: 25px; width: 25px; <?php echo $sidebar_dividends_not_style; ?>" class="mebadge">
                    <?php echo $sidebar_dividends_not; ?>
                    </span>
                  </span>
                  <?php } ?>
              </p>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="./_1dividends_unpaid.php?o=1">T-Bills</a>
              <a class="dropdown-item" href="./_1pay_all_shareholders.php?o=1">Dividends</a>
              <!--<a class="dropdown-item" href="_1user_view_one_user_profile.php">Notify User(s)</a>-->
            </div>
          </li>
          <!--
              <li class="nav-item <?php if($page_name_real == "unpaid_dividends"){ echo 'active'; } ?> ">
            <a class="nav-link" href="./_1dividends_unpaid.php?o=1">
              <img class="material-icons" src="../img/dividend.png" style="width: 34px; height: 34px; margin-left: 3px; margin-right: 10px;">
              <p style="display: inline;"> 
              	<?php if(isset($sidebar_dividends_not) && isset($sidebar_dividends_not_style)){ ?>
              	<span class="menotification" >
              		<span style="height: 25px; width: 25px; <?php echo $sidebar_dividends_not_style; ?>" class="mebadge">
              			<?php echo $sidebar_dividends_not; ?>
              			</span>
              		</span>
              		<?php } ?>
              	</p>
            </a>
          </li>
          <li class="nav-item <?php if($page_name_real == "shares_credit_coupon_requests"){ echo 'active'; } ?> ">
            <a class="nav-link" href="./_1shares_credit_coupon.php">
              <img class="material-icons" src="../img/coupon_shares_credit.png" style="width: 34px; height: 34px; margin-left: 3px;  margin-right: 10px;">
              <p style="display: inline;">S-Coupon 
              	<?php if(isset($sidebar_s_credit_not) && isset($sidebar_s_credit_not_style)){ ?>
              	<span class="menotification" >
              		<span style="height: 25px; width: 25px; <?php echo $sidebar_s_credit_not_style; ?>" class="mebadge">
              			<?php echo $sidebar_s_credit_not; ?>
              			</span>
              		</span>
              		<?php } ?>
              	</p>
            </a>
          </li>
          -->
          <li style="cursor: pointer;" class="nav-item <?php if($page_name_real == "unread_messages"){ echo 'active'; } ?> ">
            <a class="nav-link" href="./_1messenger.php">
              <img class="material-icons" src="../img/messenger.png" style="width: 34px; height: 34px; margin-left: 3px; margin-right: 10px;">
              <p style="display: inline;">Messenger 
              	<?php if(isset($sidebar_msgs_not) && isset($sidebar_msgs_not_style)){ ?>
              	<span class="menotification" >
              		<span style="height: 25px; width: 25px; <?php echo $sidebar_msgs_not_style; ?>" class="mebadge">
              			<?php echo $sidebar_msgs_not; ?>
              			</span>
              		</span>
              		<?php } ?>
              	</p>
            </a>
          </li>
          <li style="cursor: pointer;" class="nav-item dropdown  <?php if($page_name_real == "shares_management"){ echo 'active'; } ?> ">
            <a class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="material-icons" src="../img/shares.png" style="width: 34px; height: 34px; margin-left: 3px;  margin-right: 10px;">
              <p style="display: inline;">
                Shares 
            </p>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="_1add_new_share_value.php">Add New Share Value</a>
              <a class="dropdown-item" href="_1add_earnings_report.php">Add Business Earnings Report</a>
             
             <!--
              <a class="dropdown-item" href="_1users_view_users.php">View Hosted Shares</a>
              <a class="dropdown-item" href="_1user.php">View Investor(s) Stock</a>
              <a class="dropdown-item" href="_1user.php">Create Shares Coupon Token</a>
             -->
            </div>
          </li>
          <!--
          <li class="nav-item dropdown  <?php if($page_name_real == "wallets_management"){ echo 'active'; } ?> ">
            <a class="nav-link" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="material-icons" src="../img/wallet.png" style="width: 34px; height: 34px; margin-left: 3px;  margin-right: 10px;">
              <p style="display: inline;">
                Wallets
              </p>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="_1user.php">Create Coupon Token</a>
            </div>
          </li>
          -->
          <li style="cursor: pointer;" class="nav-item dropdown  <?php if($page_name_real == "users"){ echo 'active'; } ?> ">
            <a class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="material-icons" src="../img/users.png" style="width: 34px; height: 34px; margin-left: 3px;  margin-right: 10px;">
              <p style="display: inline;">
                Users
              </p>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="_1users_view_users.php">View Users</a>
              <a class="dropdown-item" href="_1user_view_one_user_profile.php">View User Profile</a>
              <!--<a class="dropdown-item" href="_1user_view_one_user_profile.php">Notify User(s)</a>-->
            </div>
          </li>
          <!--
          <li class="nav-item dropdown  <?php if($page_name_real == "transactions_management"){ echo 'active'; } ?> ">
            <a class="nav-link" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="material-icons" src="../img/transactions.png" style="width: 34px; height: 34px; margin-left: 3px; margin-right: 10px;">
              <p style="display: inline;">
                Transactions
              </p>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="_1users_view_users.php">Old Reviewed Transactions</a>
              <a class="dropdown-item" href="_1users_view_users.php">View Single Transactions</a>
            </div>
          </li>
          -->
          <li style="cursor: pointer;" class="nav-item dropdown  <?php if($page_name_real == "news_management"){ echo 'active'; } ?> ">
            <a class="nav-link" href="_1news_manager.php">
              <img class="material-icons" src="../img/news.png" style="width: 34px; height: 34px; margin-left: 3px; margin-right: 10px;">
              <p style="display: inline;">
                News
              </p>
            </a>
          </li>
          <!--
          <li class="nav-item dropdown">
            <a class="nav-link" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="material-icons">Payouts</i>
              <p>
                Users
              </p>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="#">View Payouts</a>
              <a class="dropdown-item" href="#">Pay User</a>
              <a class="dropdown-item" href="#">Request Pay-Back</a>
              <a class="dropdown-item" href="#">Flag User</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="material-icons">Payouts</i>
              <p>
                Old Pages
              </p>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="upgrade.php">Upgrade</a>
              <a class="dropdown-item" href="_1user_view_one_user_profile.php">User</a>
              <a class="dropdown-item" href="icons.php">Icons</a>
              <a class="dropdown-item" href="users_view_users.php">Tables</a>
              <a class="dropdown-item" href="map.php">Maps</a>
              <a class="dropdown-item" href="notifications.php">Notifications</a>
              <a class="dropdown-item" href="rtl.php">Rtl</a>
              <a class="dropdown-item" href="typography.php">Typography</a>
            </div>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./user.html">
              <i class="material-icons">person</i>
              <p>User</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./tables.html">
              <i class="material-icons">content_paste</i>
              <p>Table List</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./typography.html">
              <i class="material-icons">library_books</i>
              <p>Typography</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./icons.html">
              <i class="material-icons">bubble_chart</i>
              <p>Icons</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./map.html">
              <i class="material-icons">location_ons</i>
              <p>Maps</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./notifications.html">
              <i class="material-icons">notifications</i>
              <p>Notifications</p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="./rtl.html">
              <i class="material-icons">language</i>
              <p>RTL Support</p>
            </a>
          </li>
          <li class="nav-item active-pro ">
            <a class="nav-link" href="./upgrade.html">
              <i class="material-icons">unarchive</i>
              <p>Upgrade to PRO</p>
            </a>
          </li>
        -->
        </ul>
      </div>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <a class="navbar-brand" href="#pablo"><strong><?php echo $page_name; ?></strong></a>
          </div>
          <button style="display: none;" class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end">
            <form style="display: none;" class="navbar-form">
              <div class="input-group no-border">
                <input type="text" value="" class="form-control" placeholder="Search...">
                <button type="submit" class="btn btn-white btn-round btn-just-icon">
                  <i class="material-icons">search</i>
                  <div class="ripple-container"></div>
                </button>
              </div>
            </form>
            <ul class="navbar-nav">
              <li style="display: none;" class="nav-item">
                <a class="nav-link" href="#pablo">
                  <i class="material-icons">dashboard</i>
                  <p class="d-lg-none d-md-block">
                    Stats
                  </p>
                </a>
              </li>
              <li style="display: none;" class="nav-item dropdown">
                <a class="nav-link" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">notifications</i>
                  <span class="notification">5</span>
                  <p class="d-lg-none d-md-block">
                    Some Actions
                  </p>
                </a>
                <div style="display: none;" class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="#">Mike John responded to your email</a>
                  <a class="dropdown-item" href="#">You have 5 new tasks</a>
                  <a class="dropdown-item" href="#">You're now friend with Andrew</a>
                  <a class="dropdown-item" href="#">Another Notification</a>
                  <a class="dropdown-item" href="#">Another One</a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
                  <p class="d-lg-none d-md-block">
                    Account
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <a class="dropdown-item" href="../../../inc/admin/logout.php">Log out</a>
                  <div class="dropdown-divider"></div>
                  <!--<a class="dropdown-item" href="#">Settings</a>-->
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
