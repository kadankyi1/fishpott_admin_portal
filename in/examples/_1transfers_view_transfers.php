<?php
session_start();
if(   !isset($_SESSION["admin_pass"]) || trim($_SESSION["admin_pass"]) == "" 
   || !isset($_SESSION["admin_id"]) || trim($_SESSION["admin_id"]) == "" 
   || !isset($_SESSION["admin_name"]) || trim($_SESSION["admin_name"]) == "" 
   || !isset($_SESSION["admin_country"]) || trim($_SESSION["admin_country"]) == "" 
   || !isset($_SESSION["admin_currency"]) || trim($_SESSION["admin_currency"]) == "" 
   || !isset($_SESSION["admin_profile_pic"]) || trim($_SESSION["admin_profile_pic"]) == "" 
   || !isset($_SESSION["admin_phone"]) || trim($_SESSION["admin_phone"]) == "" ){

    header("Location: ../../"); exit;

}
$page_name = "Transfers"; 
$page_name_real = "current_transfers"; 
$all_notifications_count = 0;

//CALLING THE CONFIGURATION FILE
require_once("../../../inc/android/config.php");
//CALLING THE INPUT VALIDATOR CLASS
include_once '../../../inc/android/classes/input_validation_class.php';
//CALLING THE MISCELLANOUS CLASS
include_once '../../../inc/android/classes/miscellaneous_class.php';
//CALLING TO THE DATABASE CLASS
include_once '../../../inc/android/classes/db_class.php';
//CALLING TO THE PREPARED STATEMENT QUERY CLASS
include_once '../../../inc/android/classes/prepared_statement_class.php';
//CALLING TO THE SUPPORTED LANGUAGES CLASS
include_once '../../../inc/android/classes/languages_class.php';
//CALLING THE TIME CLASS
include_once '../../../inc/android/classes/time_class.php';
//CALLING THE TIME CLASS
include_once '../../../inc/android/classes/country_codes_class.php';

// CREATING DATABASE MYSQLI OBJECT
$dbObject = new dbConnect();

// CREATING TIME OBJECT
$timeObject = new timeOperator();

// CREATING COUNTRY OBJECT
$countryObject = new countryCodes();

// CREATING PREPARED STATEMENT QUERY OBJECT
$preparedStatementObject = new preparedStatement();

// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
$validatorObject = new inputValidator();

if($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE) === false){
    $error = "DATABASE ERROR -1 ";
    echo '<br><br><br><div style="margin-left : 10%; margin-right : 10%;" class="alert alert-danger">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      </button>
      <span>
        <b> Error - </b>' . $error . '</span>
    </div> '; exit;
}

// CREATING PREPARE STATEMENT OBJECT
$preparedStatementObject = new preparedStatement();


/********************************************************************************************************************

                  FETCHING NOTIFICATION BADGES OF SIDEBAR ::: START

********************************************************************************************************************/

include '_1notification_counter.php'; 
include '_1header_and_sidebar.php'; 

/********************************************************************************************************************

                                    FETCHING NOTIFICATION BADGES OF SIDEBAR ::: END

********************************************************************************************************************/

// QUERY VALUES DECLARED HERE
$query =  "SELECT " 
          . USER_BIO_TABLE_NAME . ".profile_picture,  " 
          . USER_BIO_TABLE_NAME . ".pot_name,  " 
          . USER_BIO_TABLE_NAME . ".first_name,  " 
          . USER_BIO_TABLE_NAME . ".last_name,  " 
          . USER_BIO_TABLE_NAME . ".country,  " 
          . USER_BIO_TABLE_NAME . ".phone,  " 
          . USER_BIO_TABLE_NAME . ".investor_id,  " 
          . USER_BIO_TABLE_NAME . ".verified_tag, "  
          . LOGIN_TABLE_NAME . ".login_type, "  
          . LOGIN_TABLE_NAME . ".flag, "     
          . SHARES_TRANSFER_TABLE_NAME . ".sku,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".transfer_type,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".receiver_id,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".shares_parent_id,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".share_id,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".num_shares_transfered,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".date_time,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".admin_review_status,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".shares_parent_name,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".admin_id,  " 
          . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".start_date,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".sender_old_quantity ,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".transfer_fee ,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".sender_old_deb_balance ,  " 
          . SHARES_TRANSFER_TABLE_NAME . ".sender_old_withdra_balance FROM "  
          . SHARES_TRANSFER_TABLE_NAME . " INNER JOIN " 
          . USER_BIO_TABLE_NAME . " ON  "  
          . SHARES_TRANSFER_TABLE_NAME . ".sender_id="  
          . USER_BIO_TABLE_NAME . ".investor_id INNER JOIN " 
          . LOGIN_TABLE_NAME . " ON  "  
          . LOGIN_TABLE_NAME . ".id="  
          . USER_BIO_TABLE_NAME . ".investor_id INNER JOIN " 
          . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " ON  "  
          . SHARES_TRANSFER_TABLE_NAME . ".share_id="  
          . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id";

$where_clause_sql_query_addition = "";
$or_where_and_statements = "";
$page_results_quantity = " 20 ";
$order_by_addition = " ORDER BY " . SHARES_TRANSFER_TABLE_NAME . ".sku ASC LIMIT  " . $page_results_quantity;
$value_holder_array = array();
$all_users = array();
$value_types_string = "";
$first_sku = 0;


// IF A SEARCH HAS BEEN MADE
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST" && !isset($_GET["o"])){

	$where_clause_sql_query_addition = " WHERE  ( " . SHARES_TRANSFER_TABLE_NAME . ".transfer_type = '' OR " . SHARES_TRANSFER_TABLE_NAME . ".transfer_type LIKE 'coupon%' )";

	// USING THE VALUES SEARCH FORM TO BUILD THE QUERY
    if(isset($_POST["admin_review_status"]) && trim($_POST["admin_review_status"]) != "" && trim($_POST["admin_review_status"]) != "2"){
    	if(trim($_POST["user_age_start"]) == "all"){
            $_SESSION["current_transfers_where_clause_set_passive"] = true;
        } else {
	    	if($where_clause_sql_query_addition == ""){
	    		$or_where_and_statements = " WHERE ";
	    	} else {
	    		$or_where_and_statements = " " . $_POST["user_first_name_or_and_type"] . " ";
	    	}

			$admin_review_status = intval($_POST["admin_review_status"]);
		    	$where_clause_sql_query_addition .= " $or_where_and_statements " . SHARES_TRANSFER_TABLE_NAME . ".admin_review_status = ? ";
		    	array_push($value_holder_array, $admin_review_status);
		    	$value_types_string .= "i";
        }

    } 

    if(isset($_POST["share_id"]) && trim($_POST["share_id"]) != ""){
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_last_name_or_and_type"] . " ";
    	}
		$user_signup_date = trim($_POST["share_id"]);
    	$where_clause_sql_query_addition .= " $or_where_and_statements " . SHARES_TRANSFER_TABLE_NAME . ".share_id = ? ";
    	array_push($value_holder_array, $user_signup_date);
    	$value_types_string .= "s";
    }

	if(isset($_POST["receiver_share_id"]) && trim($_POST["receiver_share_id"]) != ""){
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_phone_or_and_type"] . " ";
    	}
		$account_review_status = trim($_POST["receiver_share_id"]);
    	$where_clause_sql_query_addition .= " $or_where_and_statements " . SHARES_TRANSFER_TABLE_NAME . ".receiver_share_id = ? ";
    	array_push($value_holder_array, $account_review_status);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_pott_name"]) && trim($_POST["user_pott_name"]) != ""){
		$account_type = trim($_POST["user_pott_name"]);
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_pott_name_or_and_type"] . " ";
    	}
    	$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".pot_name = ? ";
    	array_push($value_holder_array, $account_type);
    	$value_types_string .= "s";
    }


    //$order_by_addition = " ORDER BY " . SHARES_TRANSFER_TABLE_NAME . ".sku ASC " . " LIMIT " . $page_results_quantity;

    $_SESSION["current_transfers_order_by_clause"] = $order_by_addition;
    $_SESSION["current_transfers_value_holder_array"] = $value_holder_array;
    $_SESSION["current_transfers_value_types_string"] = $value_types_string;
    $query .= $where_clause_sql_query_addition . $order_by_addition;

    $_SESSION["current_transfers_where_clause"] = $where_clause_sql_query_addition . " AND " . SHARES_TRANSFER_TABLE_NAME . ".sku > ? ";

} else if(
	!isset($_GET["o"]) &&
	(
		(isset($_SESSION["current_transfers_where_clause_set_passive"]) && $_SESSION["current_transfers_where_clause_set_passive"]=== true) ||
		(isset($_SESSION["current_transfers_where_clause"]) && trim($_SESSION["current_transfers_where_clause"]) != "")
	)
	
){
    $value_holder_array = $_SESSION["current_transfers_value_holder_array"];
    $value_types_string = $_SESSION["current_transfers_value_types_string"];

    $start_sku = intval($_GET["start_sku"]);
    $first_sku = $start_sku - intval($page_results_quantity);
    array_push($value_holder_array, $start_sku);
	$value_types_string .= "i";

    $query .= $_SESSION["current_transfers_where_clause"] . $where_clause_sql_query_addition . $_SESSION["current_transfers_order_by_clause"];
}  else {

  //FETCHING THE NORMAL PAGE CONTENT
  if(isset($_GET["start_sku"]) && intval($_GET["start_sku"]) > 0){

    $start_sku = intval($_GET["start_sku"]);
    $first_sku = $start_sku - intval($page_results_quantity);

    $where_clause_sql_query_addition =   " WHERE ( " . SHARES_TRANSFER_TABLE_NAME . ".transfer_type = '' OR " . SHARES_TRANSFER_TABLE_NAME . ".transfer_type LIKE 'coupon%' ) AND " . SHARES_TRANSFER_TABLE_NAME . ".admin_review_status  = 0 AND " . SHARES_TRANSFER_TABLE_NAME . ".sku > ? ";

    $_SESSION["current_transfers_value_holder_array"] = $value_holder_array;
    $_SESSION["current_transfers_value_types_string"] = $value_types_string;


    array_push($value_holder_array, $start_sku);
    $value_types_string .= "i";

    $_SESSION["current_transfers_where_clause"] = $where_clause_sql_query_addition;
    $_SESSION["current_transfers_order_by_clause"] = $order_by_addition;

  } else {

	$where_clause_sql_query_addition = " WHERE  ( " . SHARES_TRANSFER_TABLE_NAME . ".transfer_type = '' OR " . SHARES_TRANSFER_TABLE_NAME . ".transfer_type LIKE 'coupon%' ) AND " . SHARES_TRANSFER_TABLE_NAME . ".admin_review_status  = 0 ";
  }
 // CONSTRUCTING THE OLD QUERY OF THE SEARCH
 $query .= $where_clause_sql_query_addition . $order_by_addition;

}

/*
echo "<br><br><br>QUERY : " . $query;
echo "<br><br>value_types_string : " . $value_types_string;
echo "<br><br>value_holder_array : ";
var_dump($value_holder_array);
*/

?>

      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Search Transfers</h4>
                  <?php if(isset($_SESSION["asem"]) && trim($_SESSION["asem"]) != "") { ?>
                  <p class="card-category"><?php echo $_SESSION["asem"]; unset($_SESSION["asem"]); ?></p>
                  <?php } else { ?>
                  <p class="card-category">Find transfers based on the criteria you want</p>
                  <?php } ?>
                </div>
                <div class="card-body">
                  <form method="POST" action="_1transfers_view_transfers.php">
                    <div class="row">
                      <div class="col-md-2">
                        <div class="form-group">
                          <select name="admin_review_status" class="form-control">
                            <option value="">Status</option>
                            <option value="0">Not Reviewed</option>
                            <option value="1">Reviewed</option>
                            <option value="2">All</option>
                          </select>
                        </div>
						<input type="radio" checked="checked" name="user_first_name_or_and_type" id="user_first_name_and_type" value="AND">
						<label for="user_first_name_and_type" style="cursor: pointer;">AND</label>
                        <input type="radio" name="user_first_name_or_and_type" id="user_first_name_or_type" value="OR"> 
                        <label for="user_first_name_or_type" style="cursor: pointer;">OR</label> 
                        <br>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Sent Share ID</label>
                          <input type="text" name="share_id" class="form-control">
                        </div>
						<input type="radio" checked="checked" name="user_last_name_or_and_type" id="user_last_name_and_type" value="AND">
						<label for="user_last_name_and_type" style="cursor: pointer;">AND</label>
                        <input type="radio" name="user_last_name_or_and_type" id="user_last_name_or_type" value="OR"> 
                        <label for="user_last_name_or_type" style="cursor: pointer;">OR</label> 
						<br>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Received Share ID</label>
                          <input type="text" name="receiver_share_id" class="form-control">
                        </div>
                        <input type="radio"  checked="checked" name="user_phone_or_and_type" id="user_phone_and_type" value="AND">
                        <label for="user_phone_and_type" style="cursor: pointer;">AND</label>
                        <input type="radio" name="user_phone_or_and_type" id="user_phone_or_type" value="OR">
                        <label for="user_phone_or_type" style="cursor: pointer;">OR</label> 
                        <br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Sender Pott name</label>
                          <input type="text" name="user_pott_name" class="form-control">
                        </div>
                        <input type="radio" checked="checked" name="user_pott_name_or_and_type"  id="user_pott_name_and_type" value="AND">
                        <label for="user_pott_name_and_type" style="cursor: pointer;">AND</label>
                        <input type="radio" name="user_pott_name_or_and_type"  id="user_pott_name_or_type" value="OR">
                        <label for="user_pott_name_or_type" style="cursor: pointer;">OR</label> 
                        <br>
                      </div>

                      <div class="col-md-2">
                      	<br>
                    	<button type="submit" class="btn btn-primary pull-right">Search</button>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Transfers Transactions Table</h4>
                  <p class="card-category">Make sure you are completely sure the transfer is legit before confirming it.</p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                      <thead class=" text-primary">
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Status
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Type
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Sender <br>- - - -<br> Receiver
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Old Stock
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Sender OldDebWallet <br>- - - -<br> OldWithWallet
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Start Date <br>- - - -<br> Transfer Fee
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Share ID
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          No.
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Date
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Action
                        </th>
                      </thead>
                      <tbody>

                    <?php 
                    // PREPARING THE SQL STATEMENT
                    $prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, count($value_holder_array), $value_types_string, $value_holder_array);
                    if($prepared_statement === false){
                        echo "<h1>QUERY FAILED</h1>";
                    }

                    // GETTING RESULTS OF QUERY INTO AN ARRAY
                    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("profile_picture", "pot_name", "first_name", "last_name", "country", "phone", "investor_id", "verified_tag", "login_type", "flag", "sku", "transfer_type", "receiver_id", "shares_parent_id", "share_id", "num_shares_transfered", "date_time", "admin_review_status", "shares_parent_name", "admin_id", "start_date", "sender_old_quantity", "transfer_fee", "sender_old_deb_balance", "sender_old_withdra_balance"), 25, 2);



                    //BINDING THE RESULTS TO VARIABLES
                    $prepared_statement_results_array->bind_result($profile_picture, $pot_name, $first_name, $last_name, $country, $phone, $investor_id, $verified_tag, $login_type, $flag, $sku, $transfer_type, $receiver_id, $shares_parent_id, $share_id, $num_shares_transfered, $date_time, $admin_review_status, $shares_parent_name, $admin_id, $start_date, $sender_old_quantity, $transfer_fee, $sender_old_deb_balance, $sender_old_withdra_balance);

                    $sku = 0;
                    while($prepared_statement_results_array->fetch()){
                        $total_shares = 0;
                        if($first_sku <= 0 && $sku > 0){
                        	$first_sku = $sku;
                        }
                        $withdrawal_wallet_usd;
                        $fullname = $first_name." ".$last_name;
                        $db_profile_picture = "../../../pic_upload/" . $profile_picture; 
                        if(trim($profile_picture) != "" && $validatorObject->fileExists($db_profile_picture) !== false){
                            $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture;
                        } else {
                            $profile_picture = DEFAULT_NO_PHOTO_AVATAR_PICTURE_LINK;
                        }

                        if(strtolower($login_type) == "business"){
                            $business_icon_display_style = "";
                        } else {
                            $business_icon_display_style = "display : none;";
                        }

                        if($verified_tag == 1){
                            $verified_tag_display_style = "";
                            $verified_tag_icon = "verified_seller.png";
                        } else if($verified_tag == 2){
                            $verified_tag_display_style = "";
                            $verified_tag_icon = "verified.png";
                        } else {
                            $verified_tag_icon = "";
                            $verified_tag_display_style = "display : none;";
                        }

                        if($flag == 1){
                            $flag_icon_display_style = "display : ;";
                        } else {
                            $flag_icon_display_style = "display : none;";
                        }

                        
                        // GETTING THE SHARES QUANTITY
                        $prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT pot_name FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($receiver_id));

                        if($prepared_statement_2 !== false){
                            $prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("pot_name"), 1, 1);

                            if($prepared_statement_results_array_2 !== false){
                                $receiver_pottname = $prepared_statement_results_array_2[0];
                            } else {

                                $receiver_pottname = "N/A";
                                
                                }
                        } else {

                                $receiver_pottname = "N/A";

                        }
                        

                    ?>
                        <tr>
                          <td style="width: 80px;">
                            <?php if($admin_review_status == 0){ echo "pending"; } 
                                else if($admin_review_status == 1){ echo "approved";} 
                                else if($admin_review_status == 2){ echo "reversed";} 
                                else { echo "N/A"; } 
                                    ?>
                          </td>
                          <td>
                            <?php if($transfer_type == ""){echo "TRANSFER";} else if($transfer_type == "coupon"){echo "COUPON";} else {echo $transfer_type;} ?>
                          </td>
                          <td>
                            <span class="text-primary">@</span><b><?php echo $pot_name; ?><br>- - - -<br>@<?php echo $receiver_pottname; ?></b>
                          </td>
                          <td>
                            <span class="text-primary"><?php echo $sender_old_quantity; ?></span>
                          </td>
                          <td>
                            <span class="text-primary"></span><b>USD<?php echo $sender_old_deb_balance; ?><br>- - - -<br>USD<?php echo $sender_old_withdra_balance; ?></b>
                          </td>
                          <td>
                           <span class="text-primary"><?php echo $start_date; ?><br>- - - -<br>USD<?php echo $transfer_fee; ?></span>
                          </td>
                          <td class="text-primary">
                            <?php echo $share_id; ?>
                          </td>
                          <td class="text-primary">
                            <span class="text-primary"><?php echo $num_shares_transfered; ?></span>
                          </td>
                          <td class="text-primary">
                            <?php echo $date_time; ?>
                          </td>
                          <td>
                            <?php if($admin_review_status == 0 && $admin_id == "") { ?>
                            <a href="../../../inc/admin/transfers_take_action.php?i=<?php echo $sku; ?>&t=1">
                                <img class="material-icons" src="../img/correct.png" alt="complete" style="width: 20px; height: 20px;">
                            </a>
                            <a href="../../../inc/admin/transfers_take_action.php?i=<?php echo $sku; ?>&t=0">
                                <img class="material-icons" src="../img/wrong.png" alt="cancel"   style="width: 20px; height: 20px;">
                            </a>
                            <?php } else { ?>
                            <a>
                                N/A
                            </a>
                            <?php } ?>
                          </td>
                        </tr>

                    <?php
                        
                    }

                    $next_page_link = "_1transfers_view_transfers.php?start_sku=" . $sku;
                    $previous_page_link = "_1transfers_view_transfers.php?start_sku=" . $first_sku;
                    ?>

                      </tbody>
                    </table>
                  </div>
                </div>

              <div class="col-md-8" style="margin-bottom: 20px;">
            	<a href="<?php echo $next_page_link; ?>"><button type="submit" class="btn btn-success pull-right" style="width: 170px">NEXT</button></a>
            	<a href="<?php echo $previous_page_link; ?>" <?php if(!isset($_GET["start_sku"]) || $first_sku == 0){ echo 'style="display: none;"';} ?> ><button type="submit" class="btn btn-warning pull-right" style="width: 170px">PREVIOUS</button></a>
            	<br>
              </div>

              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer">
        <div class="container-fluid">
          <nav class="float-left">
            <ul>
              <li>
                <a href="https://www.creative-tim.com">
                  Creative Tim
                </a>
              </li>
              <li>
                <a href="https://creative-tim.com/presentation">
                  About Us
                </a>
              </li>
              <li>
                <a href="http://blog.creative-tim.com">
                  Blog
                </a>
              </li>
              <li>
                <a href="https://www.creative-tim.com/license">
                  Licenses
                </a>
              </li>
            </ul>
          </nav>
          <div class="copyright float-right">
            &copy;
            <script>
              document.write(new Date().getFullYear())
            </script>, made with <i class="material-icons">favorite</i> by
            <a href="https://www.creative-tim.com" target="_blank">Creative Tim</a> for a better web.
          </div>
        </div>
      </footer>
    </div>
  </div>
  <div class="fixed-plugin">
    <div class="dropdown show-dropdown">
      <a href="#" data-toggle="dropdown">
        <i class="fa fa-cog fa-2x"> </i>
      </a>
      <ul class="dropdown-menu">
        <li class="header-title"> Sidebar Filters</li>
        <li class="adjustments-line">
          <a href="javascript:void(0)" class="switch-trigger active-color">
            <div class="badge-colors ml-auto mr-auto">
              <span class="badge filter badge-purple" data-color="purple"></span>
              <span class="badge filter badge-azure" data-color="azure"></span>
              <span class="badge filter badge-green" data-color="green"></span>
              <span class="badge filter badge-warning" data-color="orange"></span>
              <span class="badge filter badge-danger" data-color="danger"></span>
              <span class="badge filter badge-rose active" data-color="rose"></span>
            </div>
            <div class="clearfix"></div>
          </a>
        </li>
        <li class="header-title">Images</li>
        <li class="active">
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="../assets/img/sidebar-1.jpg" alt="">
          </a>
        </li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="../assets/img/sidebar-2.jpg" alt="">
          </a>
        </li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="../assets/img/sidebar-3.jpg" alt="">
          </a>
        </li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="../assets/img/sidebar-4.jpg" alt="">
          </a>
        </li>
        <li class="button-container">
          <a href="https://www.creative-tim.com/product/material-dashboard" target="_blank" class="btn btn-primary btn-block">Free Download</a>
        </li>
        <!-- <li class="header-title">Want more components?</li>
            <li class="button-container">
                <a href="https://www.creative-tim.com/product/material-dashboard-pro" target="_blank" class="btn btn-warning btn-block">
                  Get the pro version
                </a>
            </li> -->
        <li class="button-container">
          <a href="https://demos.creative-tim.com/material-dashboard/docs/2.1/getting-started/introduction.html" target="_blank" class="btn btn-default btn-block">
            View Documentation
          </a>
        </li>
        <li class="button-container github-star">
          <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star ntkme/github-buttons on GitHub">Star</a>
        </li>
        <li class="header-title">Thank you for 95 shares!</li>
        <li class="button-container text-center">
          <button id="twitter" class="btn btn-round btn-twitter"><i class="fa fa-twitter"></i> &middot; 45</button>
          <button id="facebook" class="btn btn-round btn-facebook"><i class="fa fa-facebook-f"></i> &middot; 50</button>
          <br>
          <br>
        </li>
      </ul>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/jquery.min.js"></script>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!-- Plugin for the momentJs  -->
  <script src="../assets/js/plugins/moment.min.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="../assets/js/plugins/sweetalert2.js"></script>
  <!-- Forms Validations Plugin -->
  <script src="../assets/js/plugins/jquery.validate.min.js"></script>
  <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="../assets/js/plugins/jquery.bootstrap-wizard.js"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="../assets/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="../assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
  <script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="../assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="../assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  <script src="../assets/js/plugins/fullcalendar.min.js"></script>
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="../assets/js/plugins/jquery-jvectormap.js"></script>
  <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
  <script src="../assets/js/plugins/nouislider.min.js"></script>
  <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
  <!-- Library for adding dinamically elements -->
  <script src="../assets/js/plugins/arrive.min.js"></script>
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chartist JS -->
  <script src="../assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.js?v=2.1.1" type="text/javascript"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script src="../assets/demo/demo.js"></script>
  <script>
    $(document).ready(function() {
      $().ready(function() {
        $sidebar = $('.sidebar');

        $sidebar_img_container = $sidebar.find('.sidebar-background');

        $full_page = $('.full-page');

        $sidebar_responsive = $('body > .navbar-collapse');

        window_width = $(window).width();

        fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

        if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
          if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
            $('.fixed-plugin .dropdown').addClass('open');
          }

        }

        $('.fixed-plugin a').click(function(event) {
          // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
          if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
              event.stopPropagation();
            } else if (window.event) {
              window.event.cancelBubble = true;
            }
          }
        });

        $('.fixed-plugin .active-color span').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-color', new_color);
          }

          if ($full_page.length != 0) {
            $full_page.attr('filter-color', new_color);
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr('data-color', new_color);
          }
        });

        $('.fixed-plugin .background-color .badge').click(function() {
          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('background-color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-background-color', new_color);
          }
        });

        $('.fixed-plugin .img-holder').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).parent('li').siblings().removeClass('active');
          $(this).parent('li').addClass('active');


          var new_image = $(this).find("img").attr('src');

          if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            $sidebar_img_container.fadeOut('fast', function() {
              $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
              $sidebar_img_container.fadeIn('fast');
            });
          }

          if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $full_page_background.fadeOut('fast', function() {
              $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
              $full_page_background.fadeIn('fast');
            });
          }

          if ($('.switch-sidebar-image input:checked').length == 0) {
            var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
          }
        });

        $('.switch-sidebar-image input').change(function() {
          $full_page_background = $('.full-page-background');

          $input = $(this);

          if ($input.is(':checked')) {
            if ($sidebar_img_container.length != 0) {
              $sidebar_img_container.fadeIn('fast');
              $sidebar.attr('data-image', '#');
            }

            if ($full_page_background.length != 0) {
              $full_page_background.fadeIn('fast');
              $full_page.attr('data-image', '#');
            }

            background_image = true;
          } else {
            if ($sidebar_img_container.length != 0) {
              $sidebar.removeAttr('data-image');
              $sidebar_img_container.fadeOut('fast');
            }

            if ($full_page_background.length != 0) {
              $full_page.removeAttr('data-image', '#');
              $full_page_background.fadeOut('fast');
            }

            background_image = false;
          }
        });

        $('.switch-sidebar-mini input').change(function() {
          $body = $('body');

          $input = $(this);

          if (md.misc.sidebar_mini_active == true) {
            $('body').removeClass('sidebar-mini');
            md.misc.sidebar_mini_active = false;

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

          } else {

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');

            setTimeout(function() {
              $('body').addClass('sidebar-mini');

              md.misc.sidebar_mini_active = true;
            }, 300);
          }

          // we simulate the window Resize so the charts will get updated in realtime.
          var simulateWindowResize = setInterval(function() {
            window.dispatchEvent(new Event('resize'));
          }, 180);

          // we stop the simulation of Window Resize after the animations are completed
          setTimeout(function() {
            clearInterval(simulateWindowResize);
          }, 1000);

        });
      });
    });
  </script>
</body>

</html>
