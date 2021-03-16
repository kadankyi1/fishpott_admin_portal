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
$page_name = "Dashboard"; 
$page_name_real = "dashboard"; 
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

if($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE) === false){
	echo "error 1"; exit;
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




// SYSTEM GENERATED VARIABLES
$sys_todays_date_time = date("Y-m-d H:i:s");
$sys_1_hour_ago_datetime= $timeObject->getDateBeforeOrAfterGivenNumberOfTime("-1", "hour", "Y-m-d H:i:s");
$sys_a_weeks_agos_datetime = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("-1", "week", "Y-m-d H:i:s");
$sys_a_month_agos_datetime = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("-1", "month", "Y-m-d H:i:s");
$sys_12_weeks_agos_datetime = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("-12", "weeks", "Y-m-d H:i:s");

$sys_todays_date = date("Y-m-d");
$sys_a_weeks_agos_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("-1", "week", "Y-m-d");
$sys_a_month_agos_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("-1", "month", "Y-m-d");
$sys_12_weeks_agos_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("-12", "weeks", "Y-m-d");
$sys_all_dashboard_data = array();

/***************************************************************************************************************************

										GETTING ALL USERS ONLINE TODAY DATA

****************************************************************************************************************************/
/*
echo "<br><br><br><br> sys_todays_date_time : " . $sys_todays_date_time;
echo "<br> sys_1_hour_ago_datetime : " . $sys_1_hour_ago_datetime;
echo "<br> sys_a_weeks_agos_datetime : " . $sys_a_weeks_agos_datetime;
echo "<br> sys_a_month_agos_datetime : " . $sys_a_month_agos_datetime;
echo "<br> sys_12_weeks_agos_datetime : " . $sys_12_weeks_agos_datetime;

echo "<br><br> sys_todays_date : " . $sys_todays_date;
echo "<br> sys_a_weeks_agos_date : " . $sys_a_weeks_agos_date;
echo "<br> sys_a_month_agos_date : " . $sys_a_month_agos_date;
echo "<br> sys_12_weeks_agos_date : " . $sys_12_weeks_agos_date; exit;
*/

$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE coins_secure_datetime >= ? AND coins_secure_datetime <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_1_hour_ago_datetime, $sys_todays_date_time));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["users_online"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["users_online"] = -1;
		}
} else {
	$sys_all_dashboard_data["users_online"] = -1;
}

/***************************************************************************************************************************

										GETTING ALL USERS ONLINE LAST WEEK DATA

****************************************************************************************************************************/

$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE coins_secure_datetime >= ? AND coins_secure_datetime <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_a_weeks_agos_datetime, $sys_todays_date_time));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["users_last_week"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["users_last_week"] = -1;
		}
} else {
	$sys_all_dashboard_data["users_last_week"] = -1;
}

/***************************************************************************************************************************

										GETTING ALL USERS ONLINE LAST MONTH DATA

****************************************************************************************************************************/
$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE coins_secure_datetime >= ? AND coins_secure_datetime <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_a_month_agos_datetime, $sys_todays_date_time));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["users_last_month"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["users_last_month"] = -1;
		}
} else {
	$sys_all_dashboard_data["users_last_month"] = -1;
}

/***************************************************************************************************************************


										GETTING AVERAGE USERS FOR 12 WEEKS	


****************************************************************************************************************************/


$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE coins_secure_datetime >= ? AND coins_secure_datetime <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_12_weeks_agos_datetime, $sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){

			$sys_all_dashboard_data["users_average"] = ceil($prepared_statement_results_array[0] / 12);
		} else {
			$sys_all_dashboard_data["users_average"] = -1;
		}
} else {
	$sys_all_dashboard_data["users_average"] = -1;
}


/***************************************************************************************************************************

										**************************************
										*		GETTING ALL SIGNUPS TODAY DATA	 *
										**************************************

****************************************************************************************************************************/
$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE signup_date = ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s", array($sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["signups_today"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["signups_today"] = -1;
		}
} else {
	$sys_all_dashboard_data["signups_today"] = -1;
}

/***************************************************************************************************************************

										**************************************
										*		GETTING ALL SIGNUPS LAST WEEK DATA	 *
										**************************************

****************************************************************************************************************************/
$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE signup_date >= ? AND signup_date <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_a_weeks_agos_date, $sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["signups_last_week"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["signups_last_week"] = -1;
		}
} else {
	$sys_all_dashboard_data["signups_last_week"] = -1;
}

/***************************************************************************************************************************


										GETTING ALL SIGNUPS LAST MONTH DATA	


****************************************************************************************************************************/
$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE signup_date >= ? AND signup_date <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_a_month_agos_date, $sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["signups_last_month"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["signups_last_month"] = -1;
		}
} else {
	$sys_all_dashboard_data["signups_last_month"] = -1;
}

/***************************************************************************************************************************


										GETTING AVERAGE SIGNUPS FOR 12 WEEKS	


****************************************************************************************************************************/


$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE signup_date >= ? AND signup_date <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_12_weeks_agos_date, $sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){

			$sys_all_dashboard_data["signups_average"] = ceil($prepared_statement_results_array[0] / 12);
		} else {
			$sys_all_dashboard_data["signups_average"] = -1;
		}
} else {
	$sys_all_dashboard_data["signups_average"] = -1;
}

/***************************************************************************************************************************

										**************************************
										*		GETTING ALL NEWS POSTED TODAY	 *
										**************************************

****************************************************************************************************************************/
$query = "SELECT count(*) FROM " . NEWS_TABLE_NAME . " WHERE date_time = ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s", array($sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["news_today"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["news_today"] = -1;
		}
} else {
	$sys_all_dashboard_data["news_today"] = -1;
}

/***************************************************************************************************************************

										**************************************
										*		GETTING ALL  NEWS POSTED LAST WEEK DATA	 *
										**************************************

****************************************************************************************************************************/
$query = "SELECT count(*) FROM " . NEWS_TABLE_NAME . " WHERE date_time >= ? AND date_time <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_a_weeks_agos_date, $sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["news_last_week"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["news_last_week"] = -1;
		}
} else {
	$sys_all_dashboard_data["news_last_week"] = -1;
}

/***************************************************************************************************************************


										GETTING ALL  NEWS POSTED LAST MONTH DATA	


****************************************************************************************************************************/
$query = "SELECT count(*) FROM " . NEWS_TABLE_NAME . " WHERE date_time >= ? AND date_time <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_a_month_agos_date, $sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
			$sys_all_dashboard_data["news_last_month"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["news_last_month"] = -1;
		}
} else {
	$sys_all_dashboard_data["news_last_month"] = -1;
}

/***************************************************************************************************************************


										GETTING AVERAGE  NEWS POSTED FOR 12 WEEKS	


****************************************************************************************************************************/


$query = "SELECT count(*) FROM " . NEWS_TABLE_NAME . " WHERE date_time >= ? AND date_time <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_12_weeks_agos_date, $sys_todays_date));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){

			$sys_all_dashboard_data["news_average"] = ceil($prepared_statement_results_array[0] / 12);
		} else {
			$sys_all_dashboard_data["news_average"] = -1;
		}
} else {
	$sys_all_dashboard_data["news_average"] = -1;
}




// sNEWS END


/***************************************************************************************************************************


										GETTING WEEKLY GRAPH DATA FOR USERS ONLINE	


****************************************************************************************************************************/

for ($i=0; $i <= 11; $i++) { 
		
		$week_number = "-" . strval($i+1);
		$sys_week_number_ago_datetime = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($week_number, "month", "Y-m-d H:i:s");

		if($i > 0){
			$week_after_number = "-" . strval($i);
			$sys_todays_date_time = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($week_after_number, "month", "Y-m-d H:i:s");
		}

		$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE coins_secure_datetime >= ? AND coins_secure_datetime <= ?";

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_week_number_ago_datetime, $sys_todays_date_time));

		// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
		if($prepared_statement !== false ){
			// GETTING RESULTS OF QUERY INTO AN ARRAY
			$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
				// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
				if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
					$sys_all_dashboard_data["users_graph_data"][$i] = $prepared_statement_results_array[0];
				} else {
					$sys_all_dashboard_data["users_graph_data"][$i] = -1;
				}
		} else {
			$sys_all_dashboard_data["users_graph_data"][$i] = -1;
		}

}


/***************************************************************************************************************************


										GETTING WEEKLY GRAPH DATA FOR SIGNUPS	


****************************************************************************************************************************/
for ($i=0; $i <= 11; $i++) { 
		
		if($i > 1){
			$week_number = "-" . strval($i);
			$sys_week_number_ago_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($week_number, "month", "Y-m-d");

			$week_after_number = "-" . strval($i-1);
			$sys_todays_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($week_after_number, "month", "Y-m-d");
		} else if ($i == 1){
			$sys_week_number_ago_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("-1", "month", "Y-m-d");

			$sys_todays_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("0", "month", "Y-m-d");
		} else {

			$sys_week_number_ago_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("0", "month", "Y-m-d");

			$sys_todays_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime("1", "month", "Y-m-d");
		}
		
		$query = "SELECT count(*) FROM " . SHARES_TRANSFER_TABLE_NAME . " WHERE date_time >= ? AND date_time <= ?";

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ss", array($sys_week_number_ago_date, $sys_todays_date));

		// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
		if($prepared_statement !== false ){
			// GETTING RESULTS OF QUERY INTO AN ARRAY
			$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
				// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
				if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
					$sys_all_dashboard_data["signup_graph_data"][$i] = $prepared_statement_results_array[0];
				} else {
					$sys_all_dashboard_data["signup_graph_data"][$i] = -1;
				}
		} else {
			$sys_all_dashboard_data["signup_graph_data"][$i] = -1;
		}

}

/***************************************************************************************************************************


										GETTING TOTAL NUMBER OF USERS	


****************************************************************************************************************************/

$query = "SELECT count(*) FROM " . LOGIN_TABLE_NAME . " WHERE flag != ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "i", array(1));

// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
if($prepared_statement !== false ){
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
		// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
		if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){

			$sys_all_dashboard_data["users_all"] = $prepared_statement_results_array[0];
		} else {
			$sys_all_dashboard_data["users_all"] = -1;
		}
} else {
	$sys_all_dashboard_data["users_all"] = -1;
}

?>


      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
		              <img class="material-icons" src="../img/users.png" style="width: 44px; height: 44px; margin-left: 6px; margin-right: 10px;">
                  </div>
                  <p class="card-category" style="font-weight: bolder;"><b>Users</b></p>
                  <h4 class="card-title">
                  	<span style="color: #9c27b0; font-size: smaller;"><?php echo $sys_all_dashboard_data["users_all"]; ?></span>
         			<p style="font-size: small; display: inline;font-weight: bold;"><b>Total</b></p>
                  </h4>
                </div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">cloud</i> <b>Online : </b>    
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["users_online"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-primary card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">update</i> <b>Last Week : </b>    
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["users_last_week"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-primary card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">date_range</i> <b>Last Month : </b>   
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["users_last_month"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-primary card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
					<div class="alert alert-primary" style="width: 100%;">
						  <b> Average : </b> <?php echo $sys_all_dashboard_data["users_average"]; ?> per week
					</div>
				</div>

              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
		              <img class="material-icons" src="../img/signups.png" style="width: 44px; height: 44px; margin-left: 6px; margin-right: 10px;">
                  </div>
                  <p class="card-category" style="font-weight: bolder;"><b>Signups</b></p>

                </div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">cloud</i> <b>Today : </b>    
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["signups_today"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-primary card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">update</i> <b>Last Week : </b>    
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["signups_last_week"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-primary card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">date_range</i> <b>Last Month : </b>   
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["signups_last_month"]; ?></a>
                  </div>
                </div>


                <div class="card-header card-header-primary card-header-icon" style="margin-top: -25px;margin-bottom: 5px;"></div>

                <div class="card-footer">
					<div class="alert alert-primary" style="width: 100%;">
						  <b> Average : </b> <?php echo $sys_all_dashboard_data["signups_average"]; ?> per week
					</div>
				</div>

              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
		              <img class="material-icons" src="../img/shares.png" style="width: 44px; height: 44px; margin-left: 6px; margin-right: 10px;">
                  </div>
                  <p class="card-category" style="font-weight: bolder;"><b>Hosted Shares</b></p>

                </div>
                <?php

				$query = "SELECT total_number, parent_shares_id, share_name  FROM " . SHARES_HOSTED_TABLE_NAME;

			    $prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 0, "",array());

				if($prepared_statement === false){
					$miscellaneousObject->respondFrontEnd2("black", "index.php", $languagesObject->getLanguageString("something_went_wrong", $input_language));
				}

				$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
					"total_number", 
					"parent_shares_id", 
					"share_name"
				), 3, 2);

				$prepared_statement_results_array->bind_result($total_number, $parent_shares_id, $share_name);

				while($prepared_statement_results_array->fetch()){
					$total_style = "";
					$query2 = "SELECT SUM(num_of_shares) FROM " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE parent_shares_id = ?";

					$prepared_statement2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query2, 1, "s", array($parent_shares_id));

					// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
					if($prepared_statement2 !== false ){
						// GETTING RESULTS OF QUERY INTO AN ARRAY
						$prepared_statement_results_array2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement2, array("SUM(num_of_shares)"), 1, 1);
							// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
							if($prepared_statement_results_array2 !== false){
								$total_owned = $prepared_statement_results_array2[0];
								if($total_owned != $total_number){
									$total_style = "color: red;";
								}
							} else {
								$total_owned = "Not Found";
								$total_style = "color: red;";
							}
					} else {
								$total_owned = "Not Found";
								$total_style = "color: red;";
					}

				?>


                <div class="card-footer">
                  <div class="stats" style="<?php echo $total_style; ?>">
                    <i class="material-icons">cloud</i> 
                    <b>
                    	<?php echo $share_name; ?> --- <span>[HOSTED]  <?php echo $total_number; ?></span> : <a>   <?php echo $total_owned; ?>  [TRADING]</a>
                	</b>  
                  </div>
                </div>

                <div class="card-header card-header-warning card-header-icon" style="margin-top: -25px;"></div>

				<?php } ?>

                <div class="card-footer">
					<div class="alert alert-warning" style="width: 100%;">
						  <b> NOTE : </b> If the hosting and trading are not equal, it means there is a problem. Inform a Super Admin to investigate the shares
					</div>
				</div>

              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
		              <img class="material-icons" src="../img/news.png" style="width: 44px; height: 44px; margin-left: 6px; margin-right: 10px;">
                  </div>
                  <p class="card-category" style="font-weight: bolder;"><b>News Posting</b></p>

                </div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">cloud</i> <b>Today : </b>    
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["news_today"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-info card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">update</i> <b>Last Week : </b>    
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["news_last_week"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-info card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">date_range</i> <b>Last Month : </b>   
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["news_last_month"]; ?></a>
                  </div>
                </div>


                <div class="card-header card-header-info card-header-icon" style="margin-top: -25px;margin-bottom: 5px;"></div>

                <div class="card-footer">
					<div class="alert alert-info" style="width: 100%;">
						  <b> Average : </b> <?php echo $sys_all_dashboard_data["news_average"]; ?> per week
					</div>
				</div>

              </div>
            </div>
        	<!--
            <div class="col-lg-8 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
		              <img class="material-icons" src="../img/signups.png" style="width: 44px; height: 44px; margin-left: 6px; margin-right: 10px;">
                  </div>
                  <p class="card-category" style="font-weight: bolder;"><b>Messages</b></p>

                </div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">cloud</i> <b>Credits : </b>    
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["signups_today"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-danger card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">update</i> <b>Withdrawal : </b>    
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["signups_last_week"]; ?></a>
                  </div>
                </div>

                <div class="card-header card-header-danger card-header-icon" style="margin-top: -25px;"></div>

                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">date_range</i> <b>Transfers : </b>   
                    <a href="#pablo">   <?php echo $sys_all_dashboard_data["signups_last_month"]; ?></a>
                  </div>
                </div>


                <div class="card-header card-header-danger card-header-icon" style="margin-top: -25px;margin-bottom: 5px;"></div>

                <div class="card-footer">
					<div class="alert alert-danger" style="width: 100%;">
						  <b> Average : </b> <?php echo $sys_all_dashboard_data["signups_average"]; ?> per week
					</div>
				</div>

              </div>
            </div>
        	-->

            <!--
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">info_outline</i>
                  </div>
                  <p class="card-category">Fixed Issues</p>
                  <h3 class="card-title">75</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">local_offer</i> Tracked from Github
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="fa fa-twitter"></i>
                  </div>
                  <p class="card-category">Followers</p>
                  <h3 class="card-title">+245</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">update</i> Just Updated
                  </div>
                </div>
              </div>
            </div>
        	-->
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card card-chart">
                <div class="card-header card-header-warning">
                  <div class="ct-chart" id="completedTasksChart"></div>
                </div>
                <div class="card-body">
                  <h4 class="card-title">Shares Purchases</h4>
                  <p style="display: none;" class="card-category">Last Campaign Performance</p>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">access_time</i> campaign sent 2 days ago
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="card card-chart">
                <div class="card-header card-header-primary">
                  <div class="ct-chart" id="dailySalesChart"></div>
                </div>
                <div class="card-body">
                  <h4 class="card-title" style="font-weight: bolder;">Users</h4>
                  <p style="display: none;" class="card-category">
                    <span class="text-success"><i class="fa fa-long-arrow-up"></i> 55% </span> increase in today sales.</p>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">access_time</i> Users in the last 12 months
                  </div>
                </div>
              </div>
            </div>
            <!--
            <div class="col-md-12">
              <div class="card card-chart">
                <div class="card-header card-header-danger">
                  <div class="ct-chart" id="websiteViewsChart"></div>
                </div>
                <div class="card-body">
                  <h4 class="card-title" style="font-weight: bolder;">News Posting</h4>
                  <p style="display: none;" class="card-category">Last Campaign Performance</p>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">access_time</i> News posted in the last 12 months
                  </div>
                </div>
              </div>
            </div>
        	-->
          </div>
          <!--
          <div class="row">
            <div class="col-md-6">
              <div class="card card-chart">
                <div class="card-header card-header-info">
                  <div class="ct-chart" id="websiteViewsChart2"></div>
                </div>
                <div class="card-body">
                  <h4 class="card-title">Email Subscriptions</h4>
                  <p class="card-category">Last Campaign Performance</p>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">access_time</i> campaign sent 2 days ago
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card card-chart">
                <div class="card-header card-header-danger">
                  <div class="ct-chart" id="completedTasksChart"></div>
                </div>
                <div class="card-body">
                  <h4 class="card-title">Completed Tasks</h4>
                  <p class="card-category">Last Campaign Performance</p>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons">access_time</i> campaign sent 2 days ago
                  </div>
                </div>
              </div>
            </div>
          </div>
            -->
          <!--
          <div class="row">
            <div class="col-lg-6 col-md-12">
              <div class="card">
                <div class="card-header card-header-tabs card-header-primary">
                  <div class="nav-tabs-navigation">
                    <div class="nav-tabs-wrapper">
                      <span class="nav-tabs-title">Tasks:</span>
                      <ul class="nav nav-tabs" data-tabs="tabs">
                        <li class="nav-item">
                          <a class="nav-link active" href="#profile" data-toggle="tab">
                            <i class="material-icons">bug_report</i> Bugs
                            <div class="ripple-container"></div>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="#messages" data-toggle="tab">
                            <i class="material-icons">code</i> Website
                            <div class="ripple-container"></div>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="#settings" data-toggle="tab">
                            <i class="material-icons">cloud</i> Server
                            <div class="ripple-container"></div>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="tab-content">
                    <div class="tab-pane active" id="profile">
                      <table class="table">
                        <tbody>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="" checked>
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Sign contract for "What are conference organizers afraid of?"</td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="">
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Lines From Great Russian Literature? Or E-mails From My Boss?</td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="">
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit
                            </td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="" checked>
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Create 4 Invisible User Experiences you Never Knew About</td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="tab-pane" id="messages">
                      <table class="table">
                        <tbody>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="" checked>
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit
                            </td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="">
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Sign contract for "What are conference organizers afraid of?"</td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="tab-pane" id="settings">
                      <table class="table">
                        <tbody>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="">
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Lines From Great Russian Literature? Or E-mails From My Boss?</td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="" checked>
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit
                            </td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" value="" checked>
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td>Sign contract for "What are conference organizers afraid of?"</td>
                            <td class="td-actions text-right">
                              <button type="button" rel="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </button>
                              <button type="button" rel="tooltip" title="Remove" class="btn btn-danger btn-link btn-sm">
                                <i class="material-icons">close</i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-12">
              <div class="card">
                <div class="card-header card-header-warning">
                  <h4 class="card-title">Employees Stats</h4>
                  <p class="card-category">New employees on 15th September, 2016</p>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-hover">
                    <thead class="text-warning">
                      <th>ID</th>
                      <th>Name</th>
                      <th>Salary</th>
                      <th>Country</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>Dakota Rice</td>
                        <td>$36,738</td>
                        <td>Niger</td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>Minerva Hooper</td>
                        <td>$23,789</td>
                        <td>Curaçao</td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td>Sage Rodriguez</td>
                        <td>$56,142</td>
                        <td>Netherlands</td>
                      </tr>
                      <tr>
                        <td>4</td>
                        <td>Philip Chaney</td>
                        <td>$38,735</td>
                        <td>Korea, South</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
            	-->
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
  <!-- <script src="../assets/js/material-dashboard.js?v=2.1.1" type="text/javascript"></script> -->
  <script type="text/javascript">
		(function() {
		  isWindows = navigator.platform.indexOf('Win') > -1 ? true : false;

		  if (isWindows) {
		    // if we are on windows OS we activate the perfectScrollbar function
		    $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

		    $('html').addClass('perfect-scrollbar-on');
		  } else {
		    $('html').addClass('perfect-scrollbar-off');
		  }
		})();


		var breakCards = true;

		var searchVisible = 0;
		var transparent = true;

		var transparentDemo = true;
		var fixedTop = false;

		var mobile_menu_visible = 0,
		  mobile_menu_initialized = false,
		  toggle_initialized = false,
		  bootstrap_nav_initialized = false;

		var seq = 0,
		  delays = 80,
		  durations = 500;
		var seq2 = 0,
		  delays2 = 80,
		  durations2 = 500;

		$(document).ready(function() {

		  $('body').bootstrapMaterialDesign();

		  $sidebar = $('.sidebar');

		  md.initSidebarsCheck();

		  window_width = $(window).width();

		  // check if there is an image set for the sidebar's background
		  md.checkSidebarImage();

		  //    Activate bootstrap-select
		  if ($(".selectpicker").length != 0) {
		    $(".selectpicker").selectpicker();
		  }

		  //  Activate the tooltips
		  $('[rel="tooltip"]').tooltip();

		  $('.form-control').on("focus", function() {
		    $(this).parent('.input-group').addClass("input-group-focus");
		  }).on("blur", function() {
		    $(this).parent(".input-group").removeClass("input-group-focus");
		  });

		  // remove class has-error for checkbox validation
		  $('input[type="checkbox"][required="true"], input[type="radio"][required="true"]').on('click', function() {
		    if ($(this).hasClass('error')) {
		      $(this).closest('div').removeClass('has-error');
		    }
		  });

		});

		$(document).on('click', '.navbar-toggler', function() {
		  $toggle = $(this);

		  if (mobile_menu_visible == 1) {
		    $('html').removeClass('nav-open');

		    $('.close-layer').remove();
		    setTimeout(function() {
		      $toggle.removeClass('toggled');
		    }, 400);

		    mobile_menu_visible = 0;
		  } else {
		    setTimeout(function() {
		      $toggle.addClass('toggled');
		    }, 430);

		    var $layer = $('<div class="close-layer"></div>');

		    if ($('body').find('.main-panel').length != 0) {
		      $layer.appendTo(".main-panel");

		    } else if (($('body').hasClass('off-canvas-sidebar'))) {
		      $layer.appendTo(".wrapper-full-page");
		    }

		    setTimeout(function() {
		      $layer.addClass('visible');
		    }, 100);

		    $layer.click(function() {
		      $('html').removeClass('nav-open');
		      mobile_menu_visible = 0;

		      $layer.removeClass('visible');

		      setTimeout(function() {
		        $layer.remove();
		        $toggle.removeClass('toggled');

		      }, 400);
		    });

		    $('html').addClass('nav-open');
		    mobile_menu_visible = 1;

		  }

		});

		// activate collapse right menu when the windows is resized
		$(window).resize(function() {
		  md.initSidebarsCheck();

		  // reset the seq for charts drawing animations
		  seq = seq2 = 0;

		  setTimeout(function() {
		    md.initDashboardPageCharts();
		  }, 500);
		});

		md = {
		  misc: {
		    navbar_menu_visible: 0,
		    active_collapse: true,
		    disabled_collapse_init: 0,
		  },

		  checkSidebarImage: function() {
		    $sidebar = $('.sidebar');
		    image_src = $sidebar.data('image');

		    if (image_src !== undefined) {
		      sidebar_container = '<div class="sidebar-background" style="background-image: url(' + image_src + ') "/>';
		      $sidebar.append(sidebar_container);
		    }
		  },

		  showNotification: function(from, align) {
		    type = ['', 'info', 'danger', 'success', 'warning', 'rose', 'primary'];

		    color = Math.floor((Math.random() * 6) + 1);

		    $.notify({
		      icon: "add_alert",
		      message: "Welcome to <b>Material Dashboard Pro</b> - a beautiful admin panel for every web developer."

		    }, {
		      type: type[color],
		      timer: 3000,
		      placement: {
		        from: from,
		        align: align
		      }
		    });
		  },

		  initDocumentationCharts: function() {
		    if ($('#dailySalesChart').length != 0 && $('#websiteViewsChart').length != 0) {
		      /* ----------==========     Daily Sales Chart initialization For Documentation    ==========---------- */

		      dataDailySalesChart = {
		        labels: ['J0', 'Fe', 'Mc', 'Ap', 'Ma', 'Ju', 'Jl', 'Au', 'Sp', 'Oc', 'No', 'Dc'],
		        series: [
		          [12, 17, 7, 17, 23, 18, 38, 7, 17, 23, 18, 38]
		        ]
		      };

		      optionsDailySalesChart = {
		        lineSmooth: Chartist.Interpolation.cardinal({
		          tension: 0
		        }),
		        low: 0,
		        high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
		        chartPadding: {
		          top: 0,
		          right: 0,
		          bottom: 0,
		          left: 0
		        },
		      }

		      var dailySalesChart = new Chartist.Line('#dailySalesChart', dataDailySalesChart, optionsDailySalesChart);

		      var animationHeaderChart = new Chartist.Line('#websiteViewsChart', dataDailySalesChart, optionsDailySalesChart);
		    }
		  },


		  initFormExtendedDatetimepickers: function() {
		    $('.datetimepicker').datetimepicker({
		      icons: {
		        time: "fa fa-clock-o",
		        date: "fa fa-calendar",
		        up: "fa fa-chevron-up",
		        down: "fa fa-chevron-down",
		        previous: 'fa fa-chevron-left',
		        next: 'fa fa-chevron-right',
		        today: 'fa fa-screenshot',
		        clear: 'fa fa-trash',
		        close: 'fa fa-remove'
		      }
		    });

		    $('.datepicker').datetimepicker({
		      format: 'MM/DD/YYYY',
		      icons: {
		        time: "fa fa-clock-o",
		        date: "fa fa-calendar",
		        up: "fa fa-chevron-up",
		        down: "fa fa-chevron-down",
		        previous: 'fa fa-chevron-left',
		        next: 'fa fa-chevron-right',
		        today: 'fa fa-screenshot',
		        clear: 'fa fa-trash',
		        close: 'fa fa-remove'
		      }
		    });

		    $('.timepicker').datetimepicker({
		      //          format: 'H:mm',    // use this format if you want the 24hours timepicker
		      format: 'h:mm A', //use this format if you want the 12hours timpiecker with AM/PM toggle
		      icons: {
		        time: "fa fa-clock-o",
		        date: "fa fa-calendar",
		        up: "fa fa-chevron-up",
		        down: "fa fa-chevron-down",
		        previous: 'fa fa-chevron-left',
		        next: 'fa fa-chevron-right',
		        today: 'fa fa-screenshot',
		        clear: 'fa fa-trash',
		        close: 'fa fa-remove'

		      }
		    });
		  },


		  initSliders: function() {
		    // Sliders for demo purpose
		    var slider = document.getElementById('sliderRegular');

		    noUiSlider.create(slider, {
		      start: 40,
		      connect: [true, false],
		      range: {
		        min: 0,
		        max: 100
		      }
		    });

		    var slider2 = document.getElementById('sliderDouble');

		    noUiSlider.create(slider2, {
		      start: [20, 60],
		      connect: true,
		      range: {
		        min: 0,
		        max: 100
		      }
		    });
		  },

		  initSidebarsCheck: function() {
		    if ($(window).width() <= 991) {
		      if ($sidebar.length != 0) {
		        md.initRightMenu();
		      }
		    }
		  },

		  checkFullPageBackgroundImage: function() {
		    $page = $('.full-page');
		    image_src = $page.data('image');

		    if (image_src !== undefined) {
		      image_container = '<div class="full-page-background" style="background-image: url(' + image_src + ') "/>'
		      $page.append(image_container);
		    }
		  },

		  initDashboardPageCharts: function() {

		    if ($('#dailySalesChart').length != 0 || $('#completedTasksChart').length != 0 || $('#websiteViewsChart').length != 0) {
		      /* ----------==========     Daily Sales Chart initialization    ==========---------- */

		      dataDailySalesChart = {
		        labels: [
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-11", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-10", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-9", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-8", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-7", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-6", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-5", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-4", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-3", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-2", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-1", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("0", "month", "F"),0,2); ?>'
		        ],
		        series: [
		          [
		          <?php echo $sys_all_dashboard_data["users_graph_data"][11]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][10]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][9]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][8]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][7]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][6]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][5]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][4]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][3]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][2]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][1]; ?>, 
		          <?php echo $sys_all_dashboard_data["users_graph_data"][0]; ?>
		          ]
		        ]
		      };

		      optionsDailySalesChart = {
		        lineSmooth: Chartist.Interpolation.cardinal({
		          tension: 0
		        }),
		        low: <?php echo min($sys_all_dashboard_data["users_graph_data"]); ?>,
		        high: <?php echo max($sys_all_dashboard_data["users_graph_data"]) + 50; ?>,
  				showArea: true,
		        chartPadding: {
		          top: 0,
		          right: 0,
		          bottom: 0,
		          left: 0
		        },
		      }

		      var dailySalesChart = new Chartist.Line('#dailySalesChart', dataDailySalesChart, optionsDailySalesChart);

		      md.startAnimationForLineChart(dailySalesChart);



		     
		      /* ----------==========     Emails Subscription Chart initialization    ==========---------- */

		      var dataWebsiteViewsChart = {
		        labels: [
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-11", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-10", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-9", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-8", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-7", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-6", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-5", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-4", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-3", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-2", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("-1", "month", "F"),0,2); ?>', 
        		'<?php echo substr($timeObject->getDateBeforeOrAfterGivenNumberOfTime("0", "month", "F"),0,2); ?>'
		        ],
		        series: [
		          [
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][11]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][10]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][9]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][8]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][7]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][6]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][5]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][4]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][3]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][2]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][1]; ?>, 
		          <?php echo $sys_all_dashboard_data["signup_graph_data"][0]; ?>
		          ]

		        ]
		      };
		      var optionsWebsiteViewsChart = {
		        axisX: {
		          showGrid: false
		        },
  				high: <?php echo max($sys_all_dashboard_data["signup_graph_data"]) + 10; ?>,
		        low: <?php echo min($sys_all_dashboard_data["signup_graph_data"]); ?>,
		        chartPadding: {
		          top: 0,
		          right: 5,
		          bottom: 0,
		          left: 0
		        }
		      };
		      var responsiveOptions = [
		        ['screen and (max-width: 640px)', {
		          seriesBarDistance: 5,
		          axisX: {
		            labelInterpolationFnc: function(value) {
		              return value[0];
		            }
		          }
		        }]
		      ];
		      var websiteViewsChart = Chartist.Line('#websiteViewsChart', dataWebsiteViewsChart, optionsWebsiteViewsChart);

		      //start animation for the Emails Subscription Chart
		      md.startAnimationForLineChart(websiteViewsChart);

		       /* ----------==========     Completed Tasks Chart initialization    ==========---------- */

		      dataCompletedTasksChart = {
		        labels: ['J2', 'Fe', 'Mc', 'Ap', 'Ma', 'Ju', 'Jl', 'Au', 'Sp', 'Oc', 'No', 'Dc'],
		        series: [
		          [12, 17, 7, 17, 23, 18, 38, 7, 17, 23, 18, 38]
		        ]
		      };

		      optionsCompletedTasksChart = {
		        lineSmooth: Chartist.Interpolation.cardinal({
		          tension: 0
		        }),
		        low: 0,
		        high: 1000, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
		        chartPadding: {
		          top: 0,
		          right: 0,
		          bottom: 0,
		          left: 0
		        }
		      }

		      var completedTasksChart = new Chartist.Line('#completedTasksChart', dataCompletedTasksChart, optionsCompletedTasksChart);

		      // start animation for the Completed Tasks Chart - Line Chart
		      md.startAnimationForLineChart(completedTasksChart);


		    }
		  },

		  initMinimizeSidebar: function() {

		    $('#minimizeSidebar').click(function() {
		      var $btn = $(this);

		      if (md.misc.sidebar_mini_active == true) {
		        $('body').removeClass('sidebar-mini');
		        md.misc.sidebar_mini_active = false;
		      } else {
		        $('body').addClass('sidebar-mini');
		        md.misc.sidebar_mini_active = true;
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
		  },

		  checkScrollForTransparentNavbar: debounce(function() {
		    if ($(document).scrollTop() > 260) {
		      if (transparent) {
		        transparent = false;
		        $('.navbar-color-on-scroll').removeClass('navbar-transparent');
		      }
		    } else {
		      if (!transparent) {
		        transparent = true;
		        $('.navbar-color-on-scroll').addClass('navbar-transparent');
		      }
		    }
		  }, 17),


		  initRightMenu: debounce(function() {
		    $sidebar_wrapper = $('.sidebar-wrapper');

		    if (!mobile_menu_initialized) {
		      $navbar = $('nav').find('.navbar-collapse').children('.navbar-nav');

		      mobile_menu_content = '';

		      nav_content = $navbar.html();

		      nav_content = '<ul class="nav navbar-nav nav-mobile-menu">' + nav_content + '</ul>';

		      navbar_form = $('nav').find('.navbar-form').get(0).outerHTML;

		      $sidebar_nav = $sidebar_wrapper.find(' > .nav');

		      // insert the navbar form before the sidebar list
		      $nav_content = $(nav_content);
		      $navbar_form = $(navbar_form);
		      $nav_content.insertBefore($sidebar_nav);
		      $navbar_form.insertBefore($nav_content);

		      $(".sidebar-wrapper .dropdown .dropdown-menu > li > a").click(function(event) {
		        event.stopPropagation();

		      });

		      // simulate resize so all the charts/maps will be redrawn
		      window.dispatchEvent(new Event('resize'));

		      mobile_menu_initialized = true;
		    } else {
		      if ($(window).width() > 991) {
		        // reset all the additions that we made for the sidebar wrapper only if the screen is bigger than 991px
		        $sidebar_wrapper.find('.navbar-form').remove();
		        $sidebar_wrapper.find('.nav-mobile-menu').remove();

		        mobile_menu_initialized = false;
		      }
		    }
		  }, 200),

		  startAnimationForLineChart: function(chart) {

		    chart.on('draw', function(data) {
		      if (data.type === 'line' || data.type === 'area') {
		        data.element.animate({
		          d: {
		            begin: 600,
		            dur: 700,
		            from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
		            to: data.path.clone().stringify(),
		            easing: Chartist.Svg.Easing.easeOutQuint
		          }
		        });
		      } else if (data.type === 'point') {
		        seq++;
		        data.element.animate({
		          opacity: {
		            begin: seq * delays,
		            dur: durations,
		            from: 0,
		            to: 1,
		            easing: 'ease'
		          }
		        });
		      }
		    });

		    seq = 0;
		  },
		  startAnimationForBarChart: function(chart) {

		    chart.on('draw', function(data) {
		      if (data.type === 'bar') {
		        seq2++;
		        data.element.animate({
		          opacity: {
		            begin: seq2 * delays2,
		            dur: durations2,
		            from: 0,
		            to: 1,
		            easing: 'ease'
		          }
		        });
		      }
		    });

		    seq2 = 0;
		  },


		  initFullCalendar: function() {
		    $calendar = $('#fullCalendar');

		    today = new Date();
		    y = today.getFullYear();
		    m = today.getMonth();
		    d = today.getDate();

		    $calendar.fullCalendar({
		      viewRender: function(view, element) {
		        // We make sure that we activate the perfect scrollbar when the view isn't on Month
		        if (view.name != 'month') {
		          $(element).find('.fc-scroller').perfectScrollbar();
		        }
		      },
		      header: {
		        left: 'title',
		        center: 'month,agendaWeek,agendaDay',
		        right: 'prev,next,today'
		      },
		      defaultDate: today,
		      selectable: true,
		      selectHelper: true,
		      views: {
		        month: { // name of view
		          titleFormat: 'MMMM YYYY'
		          // other view-specific options here
		        },
		        week: {
		          titleFormat: " MMMM D YYYY"
		        },
		        day: {
		          titleFormat: 'D MMM, YYYY'
		        }
		      },

		      select: function(start, end) {

		        // on select we show the Sweet Alert modal with an input
		        swal({
		            title: 'Create an Event',
		            html: '<div class="form-group">' +
		              '<input class="form-control" placeholder="Event Title" id="input-field">' +
		              '</div>',
		            showCancelButton: true,
		            confirmButtonClass: 'btn btn-success',
		            cancelButtonClass: 'btn btn-danger',
		            buttonsStyling: false
		          }).then(function(result) {

		            var eventData;
		            event_title = $('#input-field').val();

		            if (event_title) {
		              eventData = {
		                title: event_title,
		                start: start,
		                end: end
		              };
		              $calendar.fullCalendar('renderEvent', eventData, true); // stick? = true
		            }

		            $calendar.fullCalendar('unselect');

		          })
		          .catch(swal.noop);
		      },
		      editable: true,
		      eventLimit: true, // allow "more" link when too many events


		      // color classes: [ event-blue | event-azure | event-green | event-orange | event-red ]
		      events: [{
		          title: 'All Day Event',
		          start: new Date(y, m, 1),
		          className: 'event-default'
		        },
		        {
		          id: 999,
		          title: 'Repeating Event',
		          start: new Date(y, m, d - 4, 6, 0),
		          allDay: false,
		          className: 'event-rose'
		        },
		        {
		          id: 999,
		          title: 'Repeating Event',
		          start: new Date(y, m, d + 3, 6, 0),
		          allDay: false,
		          className: 'event-rose'
		        },
		        {
		          title: 'Meeting',
		          start: new Date(y, m, d - 1, 10, 30),
		          allDay: false,
		          className: 'event-green'
		        },
		        {
		          title: 'Lunch',
		          start: new Date(y, m, d + 7, 12, 0),
		          end: new Date(y, m, d + 7, 14, 0),
		          allDay: false,
		          className: 'event-red'
		        },
		        {
		          title: 'Md-pro Launch',
		          start: new Date(y, m, d - 2, 12, 0),
		          allDay: true,
		          className: 'event-azure'
		        },
		        {
		          title: 'Birthday Party',
		          start: new Date(y, m, d + 1, 19, 0),
		          end: new Date(y, m, d + 1, 22, 30),
		          allDay: false,
		          className: 'event-azure'
		        },
		        {
		          title: 'Click for Creative Tim',
		          start: new Date(y, m, 21),
		          end: new Date(y, m, 22),
		          url: 'http://www.creative-tim.com/',
		          className: 'event-orange'
		        },
		        {
		          title: 'Click for Google',
		          start: new Date(y, m, 21),
		          end: new Date(y, m, 22),
		          url: 'http://www.creative-tim.com/',
		          className: 'event-orange'
		        }
		      ]
		    });
		  },

		  initVectorMap: function() {
		    var mapData = {
		      "AU": 760,
		      "BR": 550,
		      "CA": 120,
		      "DE": 1300,
		      "FR": 540,
		      "GB": 690,
		      "GE": 200,
		      "IN": 200,
		      "RO": 600,
		      "RU": 300,
		      "US": 2920,
		    };

		    $('#worldMap').vectorMap({
		      map: 'world_mill_en',
		      backgroundColor: "transparent",
		      zoomOnScroll: false,
		      regionStyle: {
		        initial: {
		          fill: '#e4e4e4',
		          "fill-opacity": 0.9,
		          stroke: 'none',
		          "stroke-width": 0,
		          "stroke-opacity": 0
		        }
		      },

		      series: {
		        regions: [{
		          values: mapData,
		          scale: ["#AAAAAA", "#444444"],
		          normalizeFunction: 'polynomial'
		        }]
		      },
		    });
		  }
		}

		// Returns a function, that, as long as it continues to be invoked, will not
		// be triggered. The function will be called after it stops being called for
		// N milliseconds. If `immediate` is passed, trigger the function on the
		// leading edge, instead of the trailing.

		function debounce(func, wait, immediate) {
		  var timeout;
		  return function() {
		    var context = this,
		      args = arguments;
		    clearTimeout(timeout);
		    timeout = setTimeout(function() {
		      timeout = null;
		      if (!immediate) func.apply(context, args);
		    }, wait);
		    if (immediate && !timeout) func.apply(context, args);
		  };
		};
  </script>
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
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      md.initDashboardPageCharts();

    });
  </script>
</body>

</html>
