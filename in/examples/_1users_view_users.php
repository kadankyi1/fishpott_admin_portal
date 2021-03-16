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
$page_name = "View Users"; 
$page_name_real = "users"; 
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
          . USER_BIO_TABLE_NAME . ".sku,  " 
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
          . USER_BIO_TABLE_NAME . ".net_worth, "  
          . USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd, "  
          . USER_BIO_TABLE_NAME . ".debit_wallet_usd FROM "  
          . USER_BIO_TABLE_NAME . " INNER JOIN " 
          . LOGIN_TABLE_NAME . " ON  "  
          . USER_BIO_TABLE_NAME . ".investor_id="  
          . LOGIN_TABLE_NAME . ".id";

$where_clause_sql_query_addition = "";
$or_where_and_statements = "";
$page_results_quantity = " 20 ";
$order_by_addition = " ORDER BY sku DESC LIMIT  " . $page_results_quantity;
$value_holder_array = array();
$all_users = array();
$value_types_string = "";
$first_sku = 0;


// IF A SEARCH HAS BEEN MADE
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){

	// USING THE VALUES SEARCH FORM TO BUILD THE QUERY
    if(isset($_POST["user_pott_name"]) && trim($_POST["user_pott_name"]) != ""){
		$user_pott_name = "%" . trim($_POST["user_pott_name"]) . "%";
    	$where_clause_sql_query_addition = " WHERE UPPER(pot_name) LIKE UPPER(?)";
    	array_push($value_holder_array, $user_pott_name);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_phone"]) && trim($_POST["user_phone"]) != ""){
		$user_phone = "%" . trim($_POST["user_phone"]) . "%";
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements = $_POST["user_phone_or_and_type"];
    	}
    	$where_clause_sql_query_addition .= " $or_where_and_statements UPPER(" . USER_BIO_TABLE_NAME . ".phone) LIKE UPPER(?) ";
    	array_push($value_holder_array, $user_phone);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_first_name"]) && trim($_POST["user_first_name"]) != ""){
		$user_first_name = "%" . trim($_POST["user_first_name"]) . "%";
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements = " " . $_POST["user_first_name_or_and_type"] . " ";
    	}
    	$where_clause_sql_query_addition .= " $or_where_and_statements UPPER(" . USER_BIO_TABLE_NAME . ".first_name) LIKE UPPER(?) ";
    	array_push($value_holder_array, $user_first_name);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_last_name"]) && trim($_POST["user_last_name"]) != ""){
		$user_last_name = "%" . trim($_POST["user_last_name"]) . "%";
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_last_name_or_and_type"] . " ";
    	}
    	$where_clause_sql_query_addition .= " $or_where_and_statements UPPER(" . USER_BIO_TABLE_NAME . ".last_name) LIKE UPPER(?) ";
    	array_push($value_holder_array, $user_last_name);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_age_start"]) && intval($_POST["user_age_start"]) > 0){
		$user_age_start = "-" . strval($_POST["user_age_start"]);
		$user_age_start = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($user_age_start, "years", "Y");

    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_age_start_or_and_type"] . " ";
    	}

    	if(isset($_POST["user_age_end"]) && intval($_POST["user_age_end"]) > 0){
    		$where_clause_sql_query_addition .= " $or_where_and_statements ( " . USER_BIO_TABLE_NAME . ".dob <= ? AND ";
    	} else {
    		$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".dob <= ? ";
    	}

    	array_push($value_holder_array, $user_age_start);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_age_end"]) && intval($_POST["user_age_end"]) > 0){
		$user_age_end = "-" . strval($_POST["user_age_end"]);
		$user_age_end = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($user_age_end, "years", "Y");

    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_age_end_or_and_type"] . " ";
    	}
    	if(isset($_POST["user_age_start"]) && intval($_POST["user_age_start"]) > 0){
    		$where_clause_sql_query_addition .= " " . USER_BIO_TABLE_NAME . ".dob >= ? )";
    	}  else {
    		$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".dob >= ? ";
    	}
    	array_push($value_holder_array, $user_age_end);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_signup_date"]) && trim($_POST["user_signup_date"]) != ""){
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_signup_date_or_and_type"] . " ";
    	}
		$user_signup_date = trim($_POST["user_signup_date"]);
    	$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".signup_date = ? ";
    	array_push($value_holder_array, $user_signup_date);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_country"]) && trim($_POST["user_country"]) != ""){
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_country_or_and_type"] . " ";
    	}
		$user_country = trim($_POST["user_country"]);
    	$where_clause_sql_query_addition .= " $or_where_and_statements UPPER(" . USER_BIO_TABLE_NAME . ".country) = UPPER(?) ";
    	array_push($value_holder_array, $user_country);
    	$value_types_string .= "s";
    }

	if(isset($_POST["account_review_status"]) && trim($_POST["account_review_status"]) != ""){
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["account_review_status_or_and_type"] . " ";
    	}
		$account_review_status = intval($_POST["account_review_status"]);
    	$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".account_review_done = ? ";
    	array_push($value_holder_array, $account_review_status);
    	$value_types_string .= "i";
    }

	if(isset($_POST["account_google_index_status"]) && trim($_POST["account_google_index_status"]) != ""){
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["account_google_index_status_or_and_type"] . " ";
    	}
		$account_google_index_status = intval($_POST["account_google_index_status"]);
    	$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".sitemap_status = ? ";
    	array_push($value_holder_array, $account_google_index_status);
    	$value_types_string .= "i";
    }


    if(isset($_POST["account_type"]) && trim($_POST["account_type"]) != ""){
		$account_type = trim($_POST["account_type"]);
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["account_type_or_and_type"] . " ";
    	}
    	$where_clause_sql_query_addition .= " $or_where_and_statements " . LOGIN_TABLE_NAME . ".login_type = ? ";
    	array_push($value_holder_array, $account_type);
    	$value_types_string .= "s";
    }

    if(isset($_POST["profile_picture_status"]) && trim($_POST["profile_picture_status"]) != ""){
		$profile_picture_status = trim($_POST["profile_picture_status"]);
    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["profile_picture_status_or_and_type"] . " ";
    	}
    	if($profile_picture_status == "1"){
    		$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".profile_picture != '' ";
    	} else {
    		$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".profile_picture = '' ";
    	}
    }

    if(isset($_POST["user_online_datetime_start"]) && trim($_POST["user_online_datetime_start"]) != ""){
        $user_online_datetime_start = new DateTime($_POST["user_online_datetime_start"]);
        $user_online_datetime_start = $user_online_datetime_start->format('Y-m-d H:i:s');

    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_online_datetime_start_or_and_type"] . " ";
    	}

    	if(isset($_POST["user_online_datetime_end"]) && intval($_POST["user_online_datetime_end"]) > 0){
    		$where_clause_sql_query_addition .= " $or_where_and_statements ( " . USER_BIO_TABLE_NAME . ".coins_secure_datetime >= ? AND ";
    	} else {
    		$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".coins_secure_datetime >= ? ";
    	}

    	array_push($value_holder_array, $user_online_datetime_start);
    	$value_types_string .= "s";
    }

    if(isset($_POST["user_online_datetime_end"]) && trim($_POST["user_online_datetime_end"]) != ""){
        $user_online_datetime_end = new DateTime($_POST["user_online_datetime_end"]);
        $user_online_datetime_end = $user_online_datetime_end->format('Y-m-d H:i:s');

    	if($where_clause_sql_query_addition == ""){
    		$or_where_and_statements = " WHERE ";
    	} else {
    		$or_where_and_statements =  " " . $_POST["user_online_datetime_end_or_and_type"] . " ";
    	}
    	if(isset($_POST["user_online_datetime_start"]) && trim($_POST["user_online_datetime_start"]) != ""){
    		$where_clause_sql_query_addition .= " " . USER_BIO_TABLE_NAME . ".coins_secure_datetime <= ? )";
    	}  else {
    		$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".coins_secure_datetime <= ? ";
    	}
    	array_push($value_holder_array, $user_online_datetime_end);
    	$value_types_string .= "s";
    }

    $_SESSION["where_clause"] = $where_clause_sql_query_addition;


    // IF START SKU IS MORE THAN 0
	if(isset($_GET["start_sku"]) && intval($_GET["start_sku"]) > 0){

		if($where_clause_sql_query_addition == ""){
			$or_where_and_statements = " WHERE ";
		} else {
			$or_where_and_statements =  " " . $_POST["_or_and_type"] . " ";
		}

		$start_sku = intval($_GET["start_sku"]);
        $first_sku = $start_sku + intval($page_results_quantity);
		$where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".sku <= ? ";
        array_push($value_holder_array, $start_sku);
        $value_types_string .= "i";
	}


    if(isset($_POST["total_made_on_fishpott"]) && trim($_POST["total_made_on_fishpott"]) != ""){


		$total_made_on_fishpott = trim($_POST["total_made_on_fishpott"]);
		if($total_made_on_fishpott == "1"){
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd DESC " . " LIMIT " . $page_results_quantity;
        } else if($total_made_on_fishpott == "2"){
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd ASC " . " LIMIT " . $page_results_quantity;
        } else if($total_made_on_fishpott == "3"){
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".debit_wallet_usd DESC " . " LIMIT " . $page_results_quantity;
        } else if($total_made_on_fishpott == "4"){
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".debit_wallet_usd ASC " . " LIMIT " . $page_results_quantity;
        } else {
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd DESC " . " LIMIT " . $page_results_quantity;
		}
    }

    if(isset($_POST["pearls_count"]) && trim($_POST["pearls_count"]) != "") {
        $pearls_count = trim($_POST["pearls_count"]);
        if($pearls_count == "1"){
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".net_worth DESC " . " LIMIT " . $page_results_quantity;
        } else {
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".net_worth ASC " . " LIMIT " . $page_results_quantity;
        }
    }

    if(isset($_POST["results_order"]) && trim($_POST["results_order"]) != "") {
        $results_order = trim($_POST["results_order"]);
        if($results_order == "1"){
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".sku DESC " . " LIMIT " . $page_results_quantity;
        } else {
            $order_by_addition = " ORDER BY " . USER_BIO_TABLE_NAME . ".sku ASC " . " LIMIT " . $page_results_quantity;
        }
    }

    $_SESSION["order_by_clause"] = $order_by_addition;
    $_SESSION["value_holder_array"] = $value_holder_array;
    $_SESSION["value_types_string"] = $value_types_string;
    $query .= $where_clause_sql_query_addition . $order_by_addition;

} else if(isset($_SESSION["where_clause"]) && trim($_SESSION["where_clause"]) != ""){
    echo "here";
    //unset($_SESSION["where_clause"]);
    $value_holder_array = $_SESSION["value_holder_array"];
    $value_types_string = $_SESSION["value_types_string"];
    // IF START SKU IS MORE THAN 0
    if(isset($_GET["start_sku"]) && intval($_GET["start_sku"]) > 0){

        if($_SESSION["where_clause"] == ""){
            $or_where_and_statements = " WHERE ";
        } else {
            $or_where_and_statements =  " AND ";
        }
        $start_sku = intval($_GET["start_sku"]);
        $first_sku = $start_sku + intval($page_results_quantity);
        $where_clause_sql_query_addition .= " $or_where_and_statements " . USER_BIO_TABLE_NAME . ".sku <= ? ";
        array_push($value_holder_array, $start_sku);
        $value_types_string .= "i";
    }

    // CONSTRUCTING THE OLD QUERY OF THE SEARCH
    $query .= $_SESSION["where_clause"] . $where_clause_sql_query_addition . $_SESSION["order_by_clause"];

}  else {

  //FETCHING THE NORMAL PAGE CONTENT
  if(isset($_GET["start_sku"]) && intval($_GET["start_sku"]) > 0){

    $start_sku = intval($_GET["start_sku"]);
    $first_sku = $start_sku + intval($page_results_quantity);

    $where_clause_sql_query_addition =   " WHERE " . USER_BIO_TABLE_NAME . ".sku <= ? ";
    array_push($value_holder_array, $start_sku);
    $value_types_string .= "i";

  }
 // CONSTRUCTING THE OLD QUERY OF THE SEARCH
 $query .= $where_clause_sql_query_addition . $order_by_addition;

}


?>

      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Search Users</h4>
                  <p class="card-category">Table below by default shows all users in descending order of their signup date</p>
                </div>
                <div class="card-body">
                  <form method="POST" action="_1users_view_users.php">
                    <div class="row">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Pott name</label>
                          <input type="text" name="user_pott_name" maxlength="30" class="form-control">
                        </div>
                        <input type="radio" name="user_pott_name_or_and_type"  id="user_pott_name_or_type" value="OR"  checked="checked"> <label id="user_pott_name_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_pott_name_or_and_type"  id="user_pott_name_and_type" value="AND"> <label for="user_pott_name_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Phone Number</label>
                          <input type="text" name="user_phone" maxlength="30" class="form-control">
                        </div>
                        <input type="radio" name="user_phone_or_and_type" id="user_phone_or_type" value="OR"  checked="checked"> <label for="user_phone_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_phone_or_and_type" id="user_phone_and_type" value="AND"> <label for="user_phone_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">First Name</label>
                          <input type="text" name="user_first_name" maxlength="30" class="form-control">
                        </div>
                        <input type="radio" name="user_first_name_or_and_type" id="user_first_name_or_type" value="OR"  checked="checked"> <label for="user_first_name_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_first_name_or_and_type" id="user_first_name_and_type" value="AND"> <label for="user_first_name_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Last name</label>
                          <input type="text" name="user_last_name" maxlength="30" class="form-control">
                        </div>
                        <input type="radio" name="user_last_name_or_and_type" id="user_last_name_or_type" value="OR"  checked="checked"> <label for="user_last_name_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_last_name_or_and_type" id="user_last_name_and_type" value="AND"> <label for="user_last_name_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Age Start</label>
                          <input type="number" name="user_age_start" min="13" max="150" class="form-control">
                        </div>
                        <input type="radio" name="user_age_start_or_and_type" id="user_age_start_or_type" value="OR"  checked="checked"> <label for="user_age_start_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_age_start_or_and_type" id="user_age_start_and_type" value="AND"> <label for="user_age_start_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Age End</label>
                          <input type="number" name="user_age_end" min="13" max="150" class="form-control">
                        </div>
                        <input type="radio" name="user_age_end_or_and_type" id="user_age_end_or_type" value="OR"  checked="checked"> <label for="user_age_end_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_age_end_or_and_type" id="user_age_end_and_type" value="AND"> <label for="user_age_end_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Signup Date</label>
                          <input type="date" name="user_signup_date" class="form-control">
                        </div>
                        <input type="radio" name="user_signup_date_or_and_type" id="user_signup_date_or_type" value="OR"  checked="checked"> <label for="user_signup_date_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_signup_date_or_and_type" id="user_signup_date_and_type" value="AND"> <label for="user_signup_date_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
						  <select name="user_country" class="form-control">
							<option value="">Country</option>
							<option value="USA" >USA </option>
							<option value="United Kingdom">United Kingdom</option>
							<option value="Ghana" >Ghana </option>
							<option disabled="disabled">Other Countries</option>
							<option value="Albania" >Albania </option>
							<option value="Algeria" >Algeria </option>
							<option value="American Samoa" >American Samoa </option>
							<option value="Andorra" >Andorra </option>
							<option value="Angola" >Angola </option>
							<option value="Anguilla">Anguilla</option>
							<option value="Antigua and Barbuda">Antigua and Barbuda</option>
							<option value="Argentina">Argentina</option>
							<option value="Armenia" >Armenia </option>
							<option value="Aruba" >Aruba </option>
							<option value="Australia">Australia</option>
							<option value="Austria">Austria</option>
							<option value="Azerbaijan" >Azerbaijan </option>
							<option value="Bahamas">Bahamas</option>
							<option value="Bahrain" >Bahrain </option>
							<option value="Bangladesh" >Bangladesh </option>
							<option value="Barbados">Barbados</option>
							<option value="Belarus" >Belarus </option>
							<option value="Belgium">Belgium</option>
							<option value="Belize" >Belize </option>
							<option value="Benin" >Benin </option>
							<option value="Bermuda">Bermuda</option>
							<option value="Bhutan" >Bhutan </option>
							<option value="Bolivia" >Bolivia </option>
							<option value="Bosnia Herzegovina" >Bosnia Herzegovina </option>
							<option value="Botswana" >Botswana </option>
							<option value="Brazil">Brazil</option>
							<option value="Brunei" >Brunei </option>
							<option value="Bulgaria" >Bulgaria </option>
							<option value="Burkina Faso" >Burkina Faso </option>
							<option value="Burundi" >Burundi </option>
							<option value="Cambodia" >Cambodia </option>
							<option value="Cameroon" >Cameroon </option>
							<option value="Canada" >Canada </option>
							<option value="Cape Verde Islands" >Cape Verde Islands </option>
							<option value="Cayman Islands">Cayman Islands</option>
							<option value="Central African Republic" >Central African Republic </option>
							<option value="Chile">Chile</option>
							<option value="China">China</option>
							<option value="Colombia">Colombia</option>
							<option value="Comoros" >Comoros </option>
							<option value="Congo" >Congo </option>
							<option value="Cook Islands" >Cook Islands </option>
							<option value="Costa Rica" >Costa Rica </option>
							<option value="Croatia" >Croatia </option>
							<option value="Cuba">Cuba</option>
							<option value="Cyprus - North">Cyprus - North</option>
							<option value="Cyprus - South" >Cyprus - South </option>
							<option value="Czech Republic" >Czech Republic </option>
							<option value="Denmark">Denmark</option>
							<option value="Djibouti" >Djibouti </option>
							<option value="Dominica">Dominica</option>
							<option value="Dominican Republic">Dominican Republic</option>
							<option value="Ecuador" >Ecuador </option>
							<option value="Egypt">Egypt</option>
							<option value="El Salvador" >El Salvador </option>
							<option value="Equatorial Guinea" >Equatorial Guinea </option>
							<option value="Eritrea" >Eritrea </option>
							<option value="Estonia" >Estonia </option>
							<option value="Ethiopia" >Ethiopia </option>
							<option value="Falkland Islands" >Falkland Islands </option>
							<option value="Faroe Islands" >Faroe Islands </option>
							<option value="Fiji" >Fiji </option>
							<option value="Finland" >Finland </option>
							<option value="France">France</option>
							<option value="French Guiana" >French Guiana </option>
							<option value="French Polynesia" >French Polynesia </option>
							<option value="Gabon" >Gabon </option>
							<option value="Gambia" >Gambia </option>
							<option value="Georgia">Georgia 8</option>
							<option value="Germany">Germany</option>
							<option value="Gibraltar" >Gibraltar </option>
							<option value="Greece">Greece</option>
							<option value="Greenland" >Greenland </option>
							<option value="Grenada">Grenada</option>
							<option value="Guadeloupe" >Guadeloupe </option>
							<option value="Guam" >Guam </option>
							<option value="Guatemala" >Guatemala </option>
							<option value="Guinea" >Guinea </option>
							<option value="Guinea - Bissau" >Guinea - Bissau </option>
							<option value="Guyana" >Guyana </option>
							<option value="Haiti" >Haiti </option>
							<option value="Honduras" >Honduras </option>
							<option value="Hong Kong" >Hong Kong </option>
							<option value="Hungary">Hungary</option>
							<option value="Iceland" >Iceland </option>
							<option value="India">India</option>
							<option value="Indonesia">Indonesia</option>
							<option value="Iraq" >Iraq </option>
							<option value="Iran">Iran</option>
							<option value="Ireland" >Ireland </option>
							<option value="Israel" >Israel </option>
							<option value="Italy">Italy</option>
							<option value="Jamaica">Jamaica 7</option>
							<option value="Japan">Japan</option>
							<option value="Jordan" >Jordan </option>
							<option value="Kazakhstan" >Kazakhstan </option>
							<option value="Kenya" >Kenya </option>
							<option value="Kiribati" >Kiribati </option>
							<option value="Kuwait" >Kuwait </option>
							<option value="Kyrgyzstan" >Kyrgyzstan </option>
							<option value="Laos" >Laos </option>
							<option value="Latvia" >Latvia </option>
							<option value="Lebanon" >Lebanon </option>
							<option value="Lesotho" >Lesotho </option>
							<option value="Liberia" >Liberia </option>
							<option value="Libya" >Libya </option>
							<option value="Liechtenstein" >Liechtenstein </option>
							<option value="Lithuania" >Lithuania </option>
							<option value="Luxembourg" >Luxembourg </option>
							<option value="Macao" >Macao </option>
							<option value="Macedonia" >Macedonia </option>
							<option value="Madagascar" >Madagascar </option>
							<option value="Malawi" >Malawi </option>
							<option value="Malaysia">Malaysia</option>
							<option value="Maldives" >Maldives </option>
							<option value="Mali" >Mali </option>
							<option value="Malta" >Malta </option>
							<option value="Marshall Islands" >Marshall Islands </option>
							<option value="Martinique" >Martinique </option>
							<option value="Mauritania" >Mauritania </option>
							<option value="Mayotte" >Mayotte </option>
							<option value="Mexico">Mexico</option>
							<option value="Micronesia" >Micronesia </option>
							<option value="Moldova" >Moldova </option>
							<option value="Monaco" >Monaco </option>
							<option value="Mongolia" >Mongolia </option>
							<option value="Montserrat">Montserrat</option>
							<option value="Morocco" >Morocco </option>
							<option value="Mozambique" >Mozambique </option>
							<option value="Myanmar">Myanmar</option>
							<option value="Namibia" >Namibia </option>
							<option value="Nauru" >Nauru </option>
							<option value="Nepal" >Nepal </option>
							<option value="Netherlands">Netherlands</option>
							<option value="New Caledonia" >New Caledonia </option>
							<option value="New Zealand">New Zealand</option>
							<option value="Nicaragua" >Nicaragua </option>
							<option value="Niger" >Niger </option>
							<option value="Nigeria" >Nigeria </option>
							<option value="Niue" >Niue </option>
							<option value="Norfolk Islands" >Norfolk Islands </option>
							<option value="North Korea" >North Korea</option>
							<option value="Northern Marianas" >Northern Marianas </option>
							<option value="Norway">Norway</option>
							<option value="Oman" >Oman </option>
							<option value="Pakistan">Pakistan</option>
							<option value="Palau" >Palau </option>
							<option value="Panama" >Panama </option>
							<option value="Papua New Guinea" >Papua New Guinea </option>
							<option value="Paraguay" >Paraguay </option>
							<option value="Peru">Peru</option>
							<option value="Philippines">Philippines</option>
							<option value="Poland">Poland</option>
							<option value="Portugal" >Portugal </option>
							<option value="Puerto Rico">Puerto Rico 8</option>
							<option value="Qatar" >Qatar </option>
							<option value="Reunion" >Reunion </option>
							<option value="Romania">Romania</option>
							<option value="Russia" >Russia </option>
							<option value="Rwanda" >Rwanda </option>
							<option value="San Marino" >San Marino </option>
							<option value="Sao Tome and Principe" >Sao Tome and Principe </option>
							<option value="Saudi Arabia" >Saudi Arabia </option>
							<option value="Senegal" >Senegal </option>
							<option value="Serbia" >Serbia </option>
							<option value="Seychelles" >Seychelles </option>
							<option value="Sierra Leone" >Sierra Leone </option>
							<option value="Singapore">Singapore</option>
							<option value="Slovak Republic" >Slovak Republic </option>
							<option value="Slovenia" >Slovenia </option>
							<option value="Solomon Islands" >Solomon Islands </option>
							<option value="Somalia" >Somalia </option>
							<option value="South Africa">South Africa</option>
							<option value="South Korea">South Korea</option>
							<option value="Spain">Spain</option>
							<option value="Sri Lanka">Sri Lanka</option>
							<option value="St. Helena" >St. Helena </option>
							<option value="St. Kitts">St. Kitts 6</option>
							<option value="St. Lucia">St. Lucia 5</option>
							<option value="Suriname" >Suriname </option>
							<option value="Sudan" >Sudan </option>
							<option value="Swaziland" >Swaziland </option>
							<option value="Sweden">Sweden</option>
							<option value="Switzerland">Switzerland</option>
							<option value="Syria" >Syria </option>
							<option value="Taiwan" >Taiwan </option>
							<option value="Tajikistan" >Tajikistan </option>
							<option value="Thailand">Thailand</option>
							<option value="Togo" >Togo </option>
							<option value="Tonga" >Tonga </option>
							<option value="Trinidad and Tobago">Trinidad and Tobago</option>
							<option value="Tunisia" >Tunisia </option>
							<option value="Turkey">Turkey</option>
							<option value="Turkmenistan" >Turkmenistan </option>
							<option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
							<option value="Tuvalu" >Tuvalu </option>
							<option value="Uganda" >Uganda </option>
							<option value="Ukraine" >Ukraine </option>
							<option value="United" >United Arab Emirates </option>
							<option value="Uruguay" >Uruguay </option>
							<option value="Uzbekistan" >Uzbekistan </option>
							<option value="Vanuatu" >Vanuatu </option>
							<option value="Vatican City" >Vatican City </option>
							<option value="Venezuela">Venezuela</option>
							<option value="Vietnam">Vietnam</option>
							<option value="Virgin Islands - British" >Virgin Islands - British </option>
							<option value="Virgin Islands - US" >Virgin Islands - US </option>
							<option value="Wallis and Futuna" >Wallis and Futuna</option>
							<option value="Yemen">Yemen</option>
							<option value="Zambia" >Zambia </option>
							<option value="Zimbabwe" >Zimbabwe </option>
						  </select> 
                        </div>
                        <input type="radio" name="user_country_or_and_type" id="user_country_or_type" value="OR"  checked="checked"> <label for="user_country_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_country_or_and_type" id="user_country_and_type" value="AND"> <label for="user_country_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
						  <select name="account_review_status" class="form-control">
							<option value="">Account Review Status</option>
							<option value="1">Reviewed Accounts</option>
							<option value="0">Non-Reviewed Account</option>
						  </select> 
                        </div>
                        <input type="radio" name="account_review_status_or_and_type" id="account_review_status_or_type" value="OR"  checked="checked"> <label for="account_review_status_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="account_review_status_or_and_type" id="account_review_status_and_type" value="AND"> <label for="account_review_status_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
						  <select name="account_google_index_status" class="form-control">
							<option value="">Google Index Status</option>
							<option value="1">Google Indexed Accounts</option>
							<option value="0">Non-Indexed Account</option>
						  </select> 
                        </div>
                        <input type="radio" name="account_google_index_status_or_and_type" id="account_google_index_status_or_type" value="OR"  checked="checked"> <label for="account_google_index_status_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="account_google_index_status_or_and_type" id="account_google_index_status_and_type" value="AND"> <label for="account_google_index_status_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
						  <select name="account_type" class="form-control">
							<option value="">Account Type</option>
							<option value="Personal">Personal Accounts</option>
							<option value="Business">Business Accounts</option>
						  </select> 
                        </div>
                        <input type="radio" name="account_type_or_and_type" id="account_type_or_type" value="OR"  checked="checked"> <label for="account_type_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="account_type_or_and_type" id="account_type_and_type" value="AND"> <label for="account_type_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
						  <select name="profile_picture_status" class="form-control">
							<option value="">Profile Picture Status</option>
							<option value="1">Set</option>
							<option value="0">Unset</option>
						  </select> 
                        </div>
                        <input type="radio" name="profile_picture_status_or_and_type" id="profile_picture_status_or_type" value="OR"  checked="checked"> <label for="profile_picture_status_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="profile_picture_status_or_and_type" id="profile_picture_status_and_type" value="AND"> <label for="profile_picture_status_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Online From</label>
                          <input type="datetime-local" name="user_online_datetime_start" class="form-control">
                        </div>
                        <input type="radio" name="user_online_datetime_start_or_and_type" id="user_online_datetime_start_or_type" value="OR"  checked="checked"> <label for="user_online_datetime_start_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_online_datetime_start_or_and_type" id="user_online_datetime_start_and_type" value="AND"> <label for="user_online_datetime_start_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Online To</label>
                          <input type="datetime-local" name="user_online_datetime_end" class="form-control">
                        </div>
                        <input type="radio" name="user_online_datetime_end_or_and_type" id="user_online_datetime_end_or_type" value="OR"  checked="checked"> <label for="user_online_datetime_end_or_type" style="cursor: pointer;">OR</label> 
						<input type="radio" name="user_online_datetime_end_or_and_type" id="user_online_datetime_end_and_type" value="AND"> <label for="user_online_datetime_end_and_type" style="cursor: pointer;">AND</label><br>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
						  <select name="total_made_on_fishpott" class="form-control">
							<option value="">Wallet Factor</option>
                            <option value="1">W-Wallet High To Low</option>
                            <option value="2">W-Wallet Low To High</option>
                            <option value="3">D-Wallet High To Low</option>
                            <option value="4">D-Wallet Low To High</option>
						  </select> 
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
						  <select name="pearls_count" class="form-control">
							<option value="">Pearls Factor</option>
							<option value="1">High To Low</option>
							<option value="0">Low To Heigh</option>
						  </select> 
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
						  <select name="results_order" class="form-control">
							<option value="">Results Order</option>
                            <option value="1">Descending</option>
							<option value="0">Ascending</option>
						  </select> 
                        </div>
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
                  <h4 class="card-title ">Users List Table</h4>
                  <p class="card-category">...</p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                      <thead class=" text-primary">
                        <th>
                          Pott-Pic
                        </th>
                        <th>
                          Pottname
                        </th>
                        <th>
                          Fullname
                        </th>
                        <th>
                          Shares
                        </th>
                        <th>
                          D-Wallet
                        </th>
                        <th>
                          Pearls
                        </th>
                        <th>
                          W-Wallet
                        </th>
                        <th>
                          Country
                        </th>
                        <th>
                          Phone
                        </th>
                        <th>
                          Actions
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
                    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("sku", "profile_picture", "pot_name", "first_name", "last_name", "country", "phone", "investor_id", "verified_tag", "login_type", "flag", "net_worth", "withdrawal_wallet_usd", "debit_wallet_usd"), 14, 2);

                    //BINDING THE RESULTS TO VARIABLES
                    $prepared_statement_results_array->bind_result($sku, $profile_picture, $pot_name, $first_name, $last_name, $country, $phone, $investor_id, $verified_tag, $login_type, $flag, $net_worth, $withdrawal_wallet_usd, $debit_wallet_usd);

                    $sku = 0;
                    while($prepared_statement_results_array->fetch()){
                        $total_shares = 0;
                        $total_unpaid_on_fishpott = $withdrawal_wallet_usd;
                      if(!isset($_GET["start_sku"]) && $first_sku == 0){
                            $first_sku = $sku;
                        }
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
                        $prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT SUM(num_of_shares) FROM " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE owner_id = ?", 1, "s", array($investor_id));

                        if($prepared_statement_2 !== false){
                            $prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("SUM(num_of_shares)"), 1, 1);

                            if($prepared_statement_results_array_2 !== false){
                                $total_shares = intval($prepared_statement_results_array_2[0]);
                            } else {

                                $total_shares = 0;
                                
                                }
                        } else {

                        $total_shares = 0;

                        }

                    ?>

                        <tr>
                          <td style="width: 80px;">
                            
                                    <img class="img" src="../img/<?php echo $verified_tag_icon; ?>" alt="verified account" style="width: 15px; height: 15px; float: right; margin-top: -10px;<?php echo $verified_tag_display_style; ?>" />
                                    <img class="img" src="../img/business.png" alt="business account" style="width: 15px; height: 15px; float: left; margin-top: -10px; margin-left: -5px;<?php echo $business_icon_display_style; ?>" />
                              <div class="card card-profile" style="width: 90%; height: 90%; margin-top: 45px; margin-bottom: 0px;">
                                <div class="card-avatar">
                                  <a href="#pablo">
                                    <img style="height: 70px; width: 70px;" class="img" src="<?php echo $profile_picture; ?>" />
                                  </a>
                                </div>
                              </div>

                          </td>
                          <td>
                            <span class="text-primary">@</span><b><?php echo $pot_name; ?></b>
                          </td>
                          <td>
                            <span class="text-primary"><?php echo $fullname; ?></span>
                          </td>
                          <td>
                            <?php echo $total_shares; ?>
                          </td>
                          <td>
                            <?php echo $debit_wallet_usd; ?>
                          </td>
                          <td>
                           <span class="text-primary"><?php echo $net_worth; ?></span>
                          </td>
                          <td class="text-primary">
                            $<?php echo $total_unpaid_on_fishpott; ?>
                          </td>
                          <td class="text-primary">
                            <span class="text-primary"><?php echo $country; ?></span>
                          </td>
                          <td class="text-primary">
                            <?php echo $phone; ?>
                          </td>
                          <td>
                            <a href="_1user_view_one_user_profile.php?id=<?php echo $sku; ?>">
                                <i style="cursor: pointer; color: gray;" class="material-icons">pageview</i>
                            </a>
                            <a href="_1messenger.php?sp=<?php echo $pot_name; ?>&rp=<?php echo FISHPOT_POTT_NAME; ?>">
                                <i style="cursor: pointer; color: green;" class="material-icons">message</i>
                            </a>
                            <a style="<?php echo $flag_icon_display_style; ?>" href="_1user_view_one_user_profile.php?id=<?php echo $sku; ?>">
                                <i style="cursor: pointer; color: red;" class="material-icons">flag</i>
                            </a>
                          </td>
                        </tr>


                    <?php
                        
                    }

                    $next_page_link = "_1users_view_users.php?start_sku=" . $sku;
                    $previous_page_link = "_1users_view_users.php?start_sku=" . $first_sku;
                    //echo $languagesObject->getLanguageString("query_failed_to_execute", $_SESSION["admin_country"]);
                    ?>

                      </tbody>
                    </table>
                  </div>
                </div>

              <div class="col-md-8" style="margin-bottom: 20px;">
            	<a href="<?php echo $next_page_link; ?>"><button type="submit" class="btn btn-success pull-right" style="width: 170px">NEXT</button></a>
            	<a href="<?php echo $previous_page_link; ?>"><button type="submit" class="btn btn-warning pull-right" style="width: 170px">PREVIOUS</button></a>
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
