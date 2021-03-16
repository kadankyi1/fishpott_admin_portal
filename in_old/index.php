<?php
session_start();
$page_name = "Dashboard"; 
include 'header_and_sidebar.php'; 
?>
<?php

//CALLING THE CONFIGURATION FILE
require_once("../../inc/admin/config.php");
//CALLING THE INPUT VALIDATOR CLASS
include_once '../../inc/android/classes/input_validation_class.php';
//CALLING THE MISCELLANOUS CLASS
include_once '../../inc/android/classes/miscellaneous_class.php';
//CALLING TO THE DATABASE CLASS
include_once '../../inc/android/classes/db_class.php';
//CALLING TO THE PREPARED STATEMENT QUERY CLASS
include_once '../../inc/android/classes/prepared_statement_class.php';
//CALLING TO THE SUPPORTED LANGUAGES CLASS
include_once '../../inc/android/classes/languages_class.php';
//CALLING THE TIME CLASS
include_once '../../inc/android/classes/time_class.php';
//CALLING THE TIME CLASS
include_once '../../inc/android/classes/country_codes_class.php';

// CREATING DATABASE MYSQLI OBJECT
$dbObject = new dbConnect();

// CREATING TIME OBJECT
$timeObject = new timeOperator();

// CREATING COUNTRY OBJECT
$countryObject = new countryCodes();

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

if($dbObject->connectToDatabase(0) === false){
	echo "error 1"; exit;
}

// CREATING PREPARE STATEMENT OBJECT
$preparedStatementObject = new preparedStatement();


/***************************************************************************************************************************

										GETTING ALL USERS ONLINE TODAY DATA

****************************************************************************************************************************/

$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE coins_secure_datetime >= ? AND coins_secure_datetime <= ?";

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_1_hour_ago_datetime, $sys_todays_date_time));

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

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_a_weeks_agos_datetime, $sys_todays_date_time));

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

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_a_month_agos_datetime, $sys_todays_date_time));

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

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_12_weeks_agos_datetime, $sys_todays_date));

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

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 1, "s", array($sys_todays_date));

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

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_a_weeks_agos_date, $sys_todays_date));

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

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_a_month_agos_date, $sys_todays_date));

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

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_12_weeks_agos_date, $sys_todays_date));

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


										GETTING WEEKLY GRAPH DATA FOR USERS ONLINE	


****************************************************************************************************************************/

for ($i=0; $i <= 11; $i++) { 
		
		$week_number = "-" . strval($i+1);
		$sys_week_number_ago_datetime = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($week_number, "month", "Y-m-d H:i:s");

		if($i > 0){
			$week_after_number = "-" . $i;
			$sys_todays_date_time = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($week_after_number, "month", "Y-m-d H:i:s");
		}

		$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE coins_secure_datetime >= ? AND coins_secure_datetime <= ?";

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_week_number_ago_datetime, $sys_todays_date_time));

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
		
		$week_number = "-" . strval($i+1);
		$sys_week_number_ago_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($week_number, "month", "Y-m-d");

		if($i > 0){
			$week_after_number = "-" . $i;
			$sys_todays_date = $timeObject->getDateBeforeOrAfterGivenNumberOfTime($week_after_number, "month", "Y-m-d");
		}

		$query = "SELECT count(*) FROM " . USER_BIO_TABLE_NAME . " WHERE signup_date >= ? AND signup_date <= ?";

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 2, "ss", array($sys_week_number_ago_date, $sys_todays_date));

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

$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0), $query, 1, "i", array(1));

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
        <div class="breadcome-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="breadcome-list">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="breadcome-heading">
                                        <form role="search" class="sr-input-func">
                                            <input type="text" placeholder="Search..." class="search-int form-control">
                                            <a href="#"><i class="fa fa-search"></i></a>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <ul class="breadcome-menu">
                                        <li><a href="#">Home</a> <span class="bread-slash">/</span>
                                        </li>
                                        <li><span class="bread-blod"><?php echo $page_name; ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="analytics-sparkle-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line reso-mg-b-30">
                            <div class="analytics-content">
                                <h5><img src="img/users.png" style="padding-bottom: 10px; height: 42px;">      Users (Total : <?php echo $sys_all_dashboard_data["users_all"]; ?>) </h5>
                                <br>
                                <div class="income-range">
                                    <p>Online Now</p>
                                    <span class="income-percentange bg-green">Avg. :  <span class="counter"><?php echo $sys_all_dashboard_data["users_average"]; ?> </span> per week
                                    </span>
                                </div>
                                <br>
                                <h2><span class="counter"><?php echo $sys_all_dashboard_data["users_online"]; ?></span></h2>
                                <div class="income-range">
                                    <p>Last Week</p>
                                </div>
                                <br>
                                <h2><span class="counter"><?php echo $sys_all_dashboard_data["users_last_week"]; ?></span></h2>
                                <div class="income-range">
                                    <p>Last Month</p>
                                </div>
                                <br>
                                <h2><span class="counter"><?php echo $sys_all_dashboard_data["users_last_month"]; ?></span> <span class="tuition-fees" style="cursor: pointer; font-weight: bolder;">View</span></h2>
                                <span class="text-success" style="font-size: smaller; width: 100%;">Average is of 12 weeks sample size</span></span>
                                <span class="tuition-fees" style="font-size: smaller; width: 100%;">Graph showing users per month</span>
                                <div id="sparkline22"></div>
                                <div style="display: none;" class="progress m-b-0">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:20%;"> <span class="sr-only">20% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line reso-mg-b-30">
                            <div class="analytics-content">
                                <h5><img src="img/signups.png" style="padding-bottom: 10px; height: 42px;">      Signups</h5>
                                <br>
                                <div class="income-range order-cl">
                                    <p>Today</p>
                                    <span class="income-percentange bg-red">Avg. :  <span class="counter"><?php echo $sys_all_dashboard_data["signups_average"]; ?> </span> per week</i>
                                    </span>
                                </div>
                                <br>
                                <h2><span class="counter"><?php echo $sys_all_dashboard_data["signups_today"]; ?></span></h2>
                                <div class="income-range order-cl">
                                    <p>Last Week</p>
                                    <span style="display: none;" class="income-percentange bg-red"><span class="counter">65</span>% <i class="fa fa-level-up"></i>
                                    </span>
                                </div>
                                <br>
                                <h2><span class="counter"><?php echo $sys_all_dashboard_data["signups_last_week"]; ?></span></h2>
                                <div class="income-range order-cl">
                                    <p>Last Month</p>
                                    <span style="display: none;" class="income-percentange bg-red"><span class="counter">65</span>% <i class="fa fa-level-up"></i>
                                    </span>
                                </div>
                                <br>
                                <h2><span class="counter"><?php echo $sys_all_dashboard_data["signups_last_month"]; ?></span> <span class="tuition-fees" style="cursor: pointer; font-weight: bolder;">View</span></h2>
                                <span class="text-success" style="font-size: smaller; width: 100%;">Average is of 12 weeks sample size</span></span>
                                <span class="tuition-fees" style="font-size: smaller; width: 100%;">Graph showing signups per month</span>
                                <div id="sparkline23"></div>
                                <div style="display: none;" class="progress m-b-0">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:20%;"> <span class="sr-only">20% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line reso-mg-b-30">
                            <div class="analytics-content">
                                <h5><img src="img/users.png" style="padding-bottom: 10px; height: 42px;">      Users Last Month</h5>
                                <br>
                                <div class="income-range visitor-cl">
                                    <p>New Visitor</p>
                                    <span class="income-percentange bg-blue"><span class="counter">75</span>% <i class="fa fa-level-up"></i>
                                    </span>
                                </div>
                                <br>
                                <h2>$<span class="counter">5000</span> <span class="tuition-fees">View</span></h2>
                                <span class="text-success">Average : 5000 per day</span>
                                <div id="sparkline24"></div>
                                <div style="display: none;" class="progress m-b-0">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:20%;"> <span class="sr-only">20% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
		        <div class="data-map-area mg-b-15">
		            <div class="container-fluid">
		                <div class="row">
		                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		                        <div class="sparkline11-list res-mg-b-30">
		                            <div class="sparkline11-hd">
		                                <div class="main-spark7-hd">
		                                    <h1>Countries Reached</h1>
											<span class="tuition-fees" style="font-size: smaller; width: 100%;">Each color shows the density of users of country</span>
		                                </div>
		                            </div>
		                            <div class="sparkline11-graph">
		                                <div class="data-map-single basic-choropleth">
		                                    <div id="basic_choropleth" style="position: relative; width: 100%; height: 100%;"></div>
		                                </div>
		                            </div>
		                        </div>
		                    </div>

                    		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		                        <div class="white-box analytics-info-cs mg-b-10 res-mg-t-30 table-mg-t-pro-n res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
		                            <h3 class="box-title">Total Visit</h3>
		                            <ul class="list-inline two-part-sp">
		                                <li>
		                                    <div id="sparklinedash"></div>
		                                </li>
		                                <li class="text-right sp-cn-r"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-success"><span class="counter">1500</span></span>
		                                </li>
		                            </ul>
		                        </div>
		                        <div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
		                            <h3 class="box-title">Page Views</h3>
		                            <ul class="list-inline two-part-sp">
		                                <li>
		                                    <div id="sparklinedash2"></div>
		                                </li>
		                                <li class="text-right graph-two-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-purple"><span class="counter">3000</span></span>
		                                </li>
		                            </ul>
		                        </div>
		                        <div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
		                            <h3 class="box-title">Unique Visitor</h3>
		                            <ul class="list-inline two-part-sp">
		                                <li>
		                                    <div id="sparklinedash3"></div>
		                                </li>
		                                <li class="text-right graph-three-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-info"><span class="counter">5000</span></span>
		                                </li>
		                            </ul>
		                        </div>
		                        <div class="white-box analytics-info-cs tb-sm-res-d-n dk-res-t-d-n">
		                            <h3 class="box-title">Bounce Rate</h3>
		                            <ul class="list-inline two-part-sp">
		                                <li>
		                                    <div id="sparklinedash4"></div>
		                                </li>
		                                <li class="text-right graph-four-ctn"><i class="fa fa-level-down" aria-hidden="true"></i> <span class="text-danger"><span class="counter">18</span>%</span>
		                                </li>
		                            </ul>
		                        </div>
		                    </div>

                    		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		                        <div class="white-box analytics-info-cs mg-b-10 res-mg-t-30 table-mg-t-pro-n res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
		                            <h3 class="box-title">Total Visit</h3>
		                            <ul class="list-inline two-part-sp">
		                                <li>
		                                    <div id="sparklinedash"></div>
		                                </li>
		                                <li class="text-right sp-cn-r"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-success"><span class="counter">1500</span></span>
		                                </li>
		                            </ul>
		                        </div>
		                        <div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
		                            <h3 class="box-title">Page Views</h3>
		                            <ul class="list-inline two-part-sp">
		                                <li>
		                                    <div id="sparklinedash2"></div>
		                                </li>
		                                <li class="text-right graph-two-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-purple"><span class="counter">3000</span></span>
		                                </li>
		                            </ul>
		                        </div>
		                        <div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
		                            <h3 class="box-title">Unique Visitor</h3>
		                            <ul class="list-inline two-part-sp">
		                                <li>
		                                    <div id="sparklinedash3"></div>
		                                </li>
		                                <li class="text-right graph-three-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-info"><span class="counter">5000</span></span>
		                                </li>
		                            </ul>
		                        </div>
		                        <div class="white-box analytics-info-cs tb-sm-res-d-n dk-res-t-d-n">
		                            <h3 class="box-title">Bounce Rate</h3>
		                            <ul class="list-inline two-part-sp">
		                                <li>
		                                    <div id="sparklinedash4"></div>
		                                </li>
		                                <li class="text-right graph-four-ctn"><i class="fa fa-level-down" aria-hidden="true"></i> <span class="text-danger"><span class="counter">18</span>%</span>
		                                </li>
		                            </ul>
		                        </div>
		                    </div>

		                </div>
		            </div>
		        </div>
                <br>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line reso-mg-b-30">
                            <div class="analytics-content">
                                <h5><img src="img/signups.png" style="padding-bottom: 10px; height: 42px;">      Signups </h5>
                                <br>
                                <div class="income-range">
                                    <p>Total</p>
                                    <span class="income-percentange bg-green"><span class="counter">95</span>% <i class="fa fa-bolt"></i>
                                    </span>
                                </div>
                                <br>
                                <h2><span class="counter"><?php echo $sys_all_dashboard_data["signups"]["num"]; ?></span> <span class="tuition-fees">View</span></h2>
                                <span class="text-success">Average : 5000 per day</span>
                                <div id="sparkline27"></div>
                                <div style="display: none;" class="progress m-b-0">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:20%;"> <span class="sr-only">20% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line reso-mg-b-30">
                            <div class="analytics-content">
                                <h5><img src="img/signups.png" style="padding-bottom: 10px; height: 42px;">      Signups Last Week</h5>
                                <br>
                                <div class="income-range order-cl">
                                    <p>New Adminsion</p>
                                    <span class="income-percentange bg-red"><span class="counter">65</span>% <i class="fa fa-level-up"></i>
                                    </span>
                                </div>
                                <br>
                                <h2>$<span class="counter">5000</span> <span class="tuition-fees">View</span></h2>
                                <span class="text-success">Average : 5000 per day</span>
                                <div id="sparkline29"></div>
                                <div style="display: none;" class="progress m-b-0">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:20%;"> <span class="sr-only">20% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line reso-mg-b-30">
                            <div class="analytics-content">
                                <h5><img src="img/signups.png" style="padding-bottom: 10px; height: 42px;">      Signups</h5>
                                <br>
                                <div class="income-range visitor-cl">
                                    <p>New Visitor</p>
                                    <span class="income-percentange bg-blue"><span class="counter">75</span>% <i class="fa fa-level-up"></i>
                                    </span>
                                </div>
                                <br>
                                <h2>$<span class="counter">5000</span> <span class="tuition-fees">View</span></h2>
                                <span class="text-success">Average : 5000 per day <img src="img/refresh-button.png" style="padding-bottom: 10px; height: 25px; float: right; cursor: pointer;"></span>
                                <div id="sparkline24"></div>
                                <div style="display: none;" class="progress m-b-0">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:20%;"> <span class="sr-only">20% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line reso-mg-b-30">
                            <div class="analytics-content">
                                <h5><img src="img/signups.png" style="padding-bottom: 10px;  height: 42px;">      Signups Today</h5>
                                <h2>$<span class="counter">3000</span> <span class="tuition-fees">View</span></h2>
                                <span class="text-success">Total Users : 1,000,000 </span>
                                <div style="display: none;" class="progress m-b-0">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:30%;"> <span class="sr-only">230% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line reso-mg-b-30 table-mg-t-pro dk-res-t-pro-30">
                            <div class="analytics-content">
                                <h5><img src="img/signups.png" style="padding-bottom: 10px;  height: 42px;">      Signups</h5>
                                <h2>$<span class="counter">2000</span> <span class="tuition-fees">Tuition Fees</span></h2>
                                <span class="text-info">60%</span>
                                <div style="display: none;" class="progress m-b-0">
                                    <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:60%;"> <span class="sr-only">20% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="analytics-sparkle-line table-mg-t-pro dk-res-t-pro-30">
                            <div class="analytics-content">
                                <h5>Chemical Engineering</h5>
                                <h2>$<span class="counter">3500</span> <span class="tuition-fees">Tuition Fees</span></h2>
                                <span class="text-inverse">80%</span>
                                <div class="progress m-b-0">
                                    <div class="progress-bar progress-bar-inverse" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:80%;"> <span class="sr-only">230% Complete</span> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="product-sales-area mg-tb-30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                        <div class="product-sales-chart">
                            <div class="portlet-title">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="caption pro-sl-hd">
                                            <span class="caption-subject"><b>University Earnings</b></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="actions graph-rp graph-rp-dl">
                                            <p>All Earnings are in million $</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-inline cus-product-sl-rp">
                                <li>
                                    <h5><i class="fa fa-circle" style="color: #006DF0;"></i>CSE</h5>
                                </li>
                                <li>
                                    <h5><i class="fa fa-circle" style="color: #933EC5;"></i>Accounting</h5>
                                </li>
                                <li>
                                    <h5><i class="fa fa-circle" style="color: #65b12d;"></i>Electrical</h5>
                                </li>
                            </ul>
                            <div id="morris-bar-chart" style="height: 356px;"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="white-box analytics-info-cs mg-b-10 res-mg-t-30 table-mg-t-pro-n res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
                            <h3 class="box-title">Total Visit</h3>
                            <ul class="list-inline two-part-sp">
                                <li>
                                    <div id="sparklinedash"></div>
                                </li>
                                <li class="text-right sp-cn-r"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-success"><span class="counter">1500</span></span>
                                </li>
                            </ul>
                        </div>
                        <div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
                            <h3 class="box-title">Page Views</h3>
                            <ul class="list-inline two-part-sp">
                                <li>
                                    <div id="sparklinedash2"></div>
                                </li>
                                <li class="text-right graph-two-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-purple"><span class="counter">3000</span></span>
                                </li>
                            </ul>
                        </div>
                        <div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
                            <h3 class="box-title">Unique Visitor</h3>
                            <ul class="list-inline two-part-sp">
                                <li>
                                    <div id="sparklinedash3"></div>
                                </li>
                                <li class="text-right graph-three-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-info"><span class="counter">5000</span></span>
                                </li>
                            </ul>
                        </div>
                        <div class="white-box analytics-info-cs tb-sm-res-d-n dk-res-t-d-n">
                            <h3 class="box-title">Bounce Rate</h3>
                            <ul class="list-inline two-part-sp">
                                <li>
                                    <div id="sparklinedash4"></div>
                                </li>
                                <li class="text-right graph-four-ctn"><i class="fa fa-level-down" aria-hidden="true"></i> <span class="text-danger"><span class="counter">18</span>%</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="traffic-analysis-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="social-media-edu">
                            <i class="fa fa-facebook"></i>
                            <div class="social-edu-ctn">
                                <h3>50k Likes</h3>
                                <p>You main list growing</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="social-media-edu twitter-cl res-mg-t-30 table-mg-t-pro-n">
                            <i class="fa fa-twitter"></i>
                            <div class="social-edu-ctn">
                                <h3>30k followers</h3>
                                <p>You main list growing</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="social-media-edu linkedin-cl res-mg-t-30 res-tablet-mg-t-30 dk-res-t-pro-30">
                            <i class="fa fa-linkedin"></i>
                            <div class="social-edu-ctn">
                                <h3>7k Connections</h3>
                                <p>You main list growing</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="social-media-edu youtube-cl res-mg-t-30 res-tablet-mg-t-30 dk-res-t-pro-30">
                            <i class="fa fa-youtube"></i>
                            <div class="social-edu-ctn">
                                <h3>50k Subscribers</h3>
                                <p>You main list growing</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="library-book-area mg-t-30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="single-cards-item">
                            <div class="single-product-image">
                                <a href="#"><img src="img/product/profile-bg.jpg" alt=""></a>
                            </div>
                            <div class="single-product-text">
                                <img src="img/product/pro4.jpg" alt="">
                                <h4><a class="cards-hd-dn" href="#">Angela Dominic</a></h4>
                                <h5>Web Designer & Developer</h5>
                                <p class="ctn-cards">Lorem ipsum dolor sit amet, this is a consectetur adipisicing elit</p>
                                <a class="follow-cards" href="#">Follow</a>
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="cards-dtn">
                                            <h3><span class="counter">199</span></h3>
                                            <p>Articles</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="cards-dtn">
                                            <h3><span class="counter">599</span></h3>
                                            <p>Like</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="cards-dtn">
                                            <h3><span class="counter">399</span></h3>
                                            <p>Comment</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="single-review-st-item res-mg-t-30 table-mg-t-pro-n">
                            <div class="single-review-st-hd">
                                <h2>Reviews</h2>
                            </div>
                            <div class="single-review-st-text">
                                <img src="img/notification/1.jpg" alt="">
                                <div class="review-ctn-hf">
                                    <h3>Sarah Graves</h3>
                                    <p>Highly recommend</p>
                                </div>
                                <div class="review-item-rating">
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star-half"></i>
                                </div>
                            </div>
                            <div class="single-review-st-text">
                                <img src="img/notification/2.jpg" alt="">
                                <div class="review-ctn-hf">
                                    <h3>Garbease sha</h3>
                                    <p>Awesome Pro</p>
                                </div>
                                <div class="review-item-rating">
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star-half"></i>
                                </div>
                            </div>
                            <div class="single-review-st-text">
                                <img src="img/notification/3.jpg" alt="">
                                <div class="review-ctn-hf">
                                    <h3>Gobetro pro</h3>
                                    <p>Great Website</p>
                                </div>
                                <div class="review-item-rating">
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star-half"></i>
                                </div>
                            </div>
                            <div class="single-review-st-text">
                                <img src="img/notification/4.jpg" alt="">
                                <div class="review-ctn-hf">
                                    <h3>Siam Graves</h3>
                                    <p>That's Good</p>
                                </div>
                                <div class="review-item-rating">
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star-half"></i>
                                </div>
                            </div>
                            <div class="single-review-st-text">
                                <img src="img/notification/5.jpg" alt="">
                                <div class="review-ctn-hf">
                                    <h3>Sarah Graves</h3>
                                    <p>Highly recommend</p>
                                </div>
                                <div class="review-item-rating">
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star-half"></i>
                                </div>
                            </div>
                            <div class="single-review-st-text">
                                <img src="img/notification/6.jpg" alt="">
                                <div class="review-ctn-hf">
                                    <h3>Julsha Grav</h3>
                                    <p>Sei Hoise bro</p>
                                </div>
                                <div class="review-item-rating">
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star"></i>
                                    <i class="educate-icon educate-star-half"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="single-product-item res-mg-t-30 table-mg-t-pro-n tb-sm-res-d-n dk-res-t-d-n">
                            <div class="single-product-image">
                                <a href="#"><img src="img/product/book-4.jpg" alt=""></a>
                            </div>
                            <div class="single-product-text edu-pro-tx">
                                <h4><a href="#">Title Demo Here</a></h4>
                                <h5>Lorem ipsum dolor sit amet, this is a consec tetur adipisicing elit</h5>
                                <div class="product-price">
                                    <h3>$ 45</h3>
                                    <div class="single-item-rating">
                                        <i class="educate-icon educate-star"></i>
                                        <i class="educate-icon educate-star"></i>
                                        <i class="educate-icon educate-star"></i>
                                        <i class="educate-icon educate-star"></i>
                                        <i class="educate-icon educate-star-half"></i>
                                    </div>
                                </div>
                                <div class="product-buttons">
                                    <button type="button" class="button-default cart-btn">Read More</button>
                                    <button type="button" class="button-default"><i class="fa fa-heart"></i></button>
                                    <button type="button" class="button-default"><i class="fa fa-share"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="product-sales-area mg-tb-30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                        <div class="product-sales-chart">
                            <div class="portlet-title">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="caption pro-sl-hd">
                                            <span class="caption-subject"><b>Adminsion Statistic</b></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="actions graph-rp actions-graph-rp">
                                            <a href="#" class="btn btn-dark btn-circle active tip-top" data-toggle="tooltip" title="Refresh">
													<i class="fa fa-reply" aria-hidden="true"></i>
												</a>
                                            <a href="#" class="btn btn-blue-grey btn-circle active tip-top" data-toggle="tooltip" title="Delete">
													<i class="fa fa-trash-o" aria-hidden="true"></i>
												</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-inline cus-product-sl-rp">
                                <li>
                                    <h5><i class="fa fa-circle" style="color: #006DF0;"></i>Python</h5>
                                </li>
                                <li>
                                    <h5><i class="fa fa-circle" style="color: #933EC5;"></i>PHP</h5>
                                </li>
                                <li>
                                    <h5><i class="fa fa-circle" style="color: #65b12d;"></i>Java</h5>
                                </li>
                            </ul>
                            <div id="morris-area-chart"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="analysis-progrebar res-mg-t-30 mg-ub-10 table-mg-t-pro-n res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
                            <div class="analysis-progrebar-content">
                                <h5>Usage</h5>
                                <h2 class="storage-right"><span class="counter">90</span>%</h2>
                                <div class="progress progress-mini ug-1">
                                    <div style="width: 68%;" class="progress-bar"></div>
                                </div>
                                <div class="m-t-sm small">
                                    <p>Server down since 1:32 pm.</p>
                                </div>
                            </div>
                        </div>
                        <div class="analysis-progrebar reso-mg-b-30 mg-ub-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
                            <div class="analysis-progrebar-content">
                                <h5>Memory</h5>
                                <h2 class="storage-right"><span class="counter">70</span>%</h2>
                                <div class="progress progress-mini ug-2">
                                    <div style="width: 78%;" class="progress-bar"></div>
                                </div>
                                <div class="m-t-sm small">
                                    <p>Server down since 12:32 pm.</p>
                                </div>
                            </div>
                        </div>
                        <div class="analysis-progrebar reso-mg-b-30 res-mg-t-30 mg-ub-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
                            <div class="analysis-progrebar-content">
                                <h5>Data</h5>
                                <h2 class="storage-right"><span class="counter">50</span>%</h2>
                                <div class="progress progress-mini ug-3">
                                    <div style="width: 38%;" class="progress-bar progress-bar-danger"></div>
                                </div>
                                <div class="m-t-sm small">
                                    <p>Server down since 8:32 pm.</p>
                                </div>
                            </div>
                        </div>
                        <div class="analysis-progrebar res-mg-t-30 table-dis-n-pro tb-sm-res-d-n dk-res-t-d-n">
                            <div class="analysis-progrebar-content">
                                <h5>Space</h5>
                                <h2 class="storage-right"><span class="counter">40</span>%</h2>
                                <div class="progress progress-mini ug-4">
                                    <div style="width: 28%;" class="progress-bar progress-bar-danger"></div>
                                </div>
                                <div class="m-t-sm small">
                                    <p>Server down since 5:32 pm.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="courses-area mg-b-15">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="white-box">
                            <h3 class="box-title">Browser Status</h3>
                            <ul class="basic-list">
                                <li>Google Chrome <span class="pull-right label-danger label-1 label">95.8%</span></li>
                                <li>Mozila Firefox <span class="pull-right label-purple label-2 label">85.8%</span></li>
                                <li>Apple Safari <span class="pull-right label-success label-3 label">23.8%</span></li>
                                <li>Internet Explorer <span class="pull-right label-info label-4 label">55.8%</span></li>
                                <li>Opera mini <span class="pull-right label-warning label-5 label">28.8%</span></li>
                                <li>Mozila Firefox <span class="pull-right label-purple label-6 label">26.8%</span></li>
                                <li>Safari <span class="pull-right label-purple label-7 label">31.8%</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <div class="white-box res-mg-t-30 table-mg-t-pro-n">
                            <h3 class="box-title">Visits from countries</h3>
                            <ul class="country-state">
                                <li>
                                    <h2><span class="counter">1250</span></h2> <small>From Australia</small>
                                    <div class="pull-right">75% <i class="fa fa-level-up text-danger ctn-ic-1"></i></div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-danger ctn-vs-1" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:75%;"> <span class="sr-only">75% Complete</span></div>
                                    </div>
                                </li>
                                <li>
                                    <h2><span class="counter">1050</span></h2> <small>From USA</small>
                                    <div class="pull-right">48% <i class="fa fa-level-up text-success ctn-ic-2"></i></div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-info ctn-vs-2" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:48%;"> <span class="sr-only">48% Complete</span></div>
                                    </div>
                                </li>
                                <li>
                                    <h2><span class="counter">6350</span></h2> <small>From Canada</small>
                                    <div class="pull-right">55% <i class="fa fa-level-up text-success ctn-ic-3"></i></div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success ctn-vs-3" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:55%;"> <span class="sr-only">55% Complete</span></div>
                                    </div>
                                </li>
                                <li>
                                    <h2><span class="counter">950</span></h2> <small>From India</small>
                                    <div class="pull-right">33% <i class="fa fa-level-down text-success ctn-ic-4"></i></div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success ctn-vs-4" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:33%;"> <span class="sr-only">33% Complete</span></div>
                                    </div>
                                </li>
                                <li>
                                    <h2><span class="counter">3250</span></h2> <small>From Bangladesh</small>
                                    <div class="pull-right">60% <i class="fa fa-level-up text-success ctn-ic-5"></i></div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-inverse ctn-vs-5" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:60%;"> <span class="sr-only">60% Complete</span></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="courses-inner res-mg-t-30 table-mg-t-pro-n tb-sm-res-d-n dk-res-t-d-n">
                            <div class="courses-title">
                                <a href="#"><img src="img/courses/1.jpg" alt="" /></a>
                                <h2>Apps Development</h2>
                            </div>
                            <div class="courses-alaltic">
                                <span class="cr-ic-r"><span class="course-icon"><i class="fa fa-clock"></i></span> 1 Year</span>
                                <span class="cr-ic-r"><span class="course-icon"><i class="fa fa-heart"></i></span> 50</span>
                                <span class="cr-ic-r"><span class="course-icon"><i class="fa fa-dollar"></i></span> 500</span>
                            </div>
                            <div class="course-des">
                                <p><span><i class="fa fa-clock"></i></span> <b>Duration:</b> 6 Months</p>
                                <p><span><i class="fa fa-clock"></i></span> <b>Professor:</b> Jane Doe</p>
                                <p><span><i class="fa fa-clock"></i></span> <b>Students:</b> 100+</p>
                            </div>
                            <div class="product-buttons">
                                <button type="button" class="button-default cart-btn">Read More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer-copy-right">
                            <p>Copyright © 2018. All rights reserved. Template by <a href="https://colorlib.com/wp/templates/">Colorlib</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jquery
		============================================ -->
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <!-- bootstrap JS
		============================================ -->
    <script src="js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="js/jquery-price-slider.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="js/jquery.meanmenu.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- sticky JS
		============================================ -->
    <script src="js/jquery.sticky.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="js/jquery.scrollUp.min.js"></script>
    <!-- counterup JS
		============================================ -->
    <script src="js/counterup/jquery.counterup.min.js"></script>
    <script src="js/counterup/waypoints.min.js"></script>
    <script src="js/counterup/counterup-active.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/scrollbar/mCustomScrollbar-active.js"></script>
    <!-- metisMenu JS
		============================================ -->
    <script src="js/metisMenu/metisMenu.min.js"></script>
    <script src="js/metisMenu/metisMenu-active.js"></script>
    <!-- morrisjs JS
		============================================ -->
    <script src="js/morrisjs/raphael-min.js"></script>
    <script src="js/morrisjs/morris.js"></script>
    <script src="js/morrisjs/home3-active.js"></script>
    <!-- morrisjs JS
		============================================ -->
    <script src="js/sparkline/jquery.sparkline.min.js"></script>
    <script src="js/sparkline/jquery.charts-sparkline.js"></script>
    <!-- <script src="js/sparkline/sparkline-active.js"></script> -->
    <script type="text/javascript">
    	(function ($) {
			 "use strict";
			 
				$("#sparkline1").sparkline([34, 43, 43, 35, 44, 32, 44, 52, 25], {
			        type: 'line',
			        lineColor: '#006DF0',
					lineWidth: 1,
					barSpacing: '100px',
			        fillColor: '#006DF0',
			    });
			    $("#sparkline2").sparkline([-4, -2, 2, 0, 4, 5, 6, 7], {
			        type: 'bar',
			        barColor: '#006DF0',
			        negBarColor: '#933EC5'});

			    $("#sparkline3").sparkline([1, 1, 2], {
			        type: 'pie',
			        sliceColors: ['#006DF0', '#933EC5', '#D80027']});

			    $("#sparklinedask1").sparkline([1, 3, 2], {
			        type: 'pie',
					width: '80',
			            height: '80',
			        sliceColors: ['#006DF0', '#933EC5', '#D80027']});

			    $("#sparklinedask2").sparkline([1, 1, 2], {
			        type: 'pie',
					width: '80',
			            height: '80',
			        sliceColors: ['#006DF0', '#933EC5', '#D80027']});

			    $("#sparkline4").sparkline([34, 43, 43, 35, 44, 32, 15, 22, 46, 33, 86, 54, 73, 53, 12, 53, 23, 65, 23, 63, 53, 42, 34, 56, 76, 15, 54, 23, 44], {
			        type: 'line',
			        lineColor: '#006DF0',
			        fillColor: '#ffffff',
			    });

			    $("#sparkline5").sparkline([1, 1, 0, 1, 1, 1, 1, 1, -1, -2, -3, -4], {
			        type: 'tristate',
			        posBarColor: '#006DF0',
			        negBarColor: '#933EC5'});


			    $("#sparkline6").sparkline([4, 6, 7, 7, 4, 3, 2, 1, 4, 4, 5, 6, 3, 4, 5, 8, 7, 6, 9, 3, 2, 4, 1, 5, 6, 4, 3, 7, ], {
			        type: 'discrete',
			        lineColor: '#006DF0'});

			    $("#sparkline7").sparkline([52, 12, 44], {
			        type: 'pie',
			        height: '150px',
			        sliceColors: ['#006DF0', '#933EC5', '#D80027']});

			    $("#sparkline8").sparkline([5, 6, 7, 2, 0, 4, 2, 4, 5, 7, 2, 4, 12, 14, 4, 2, 14, 12, 7], {
			        type: 'bar',
			        barWidth: 8,
			        height: '150px',
			        barColor: '#006DF0',
			        negBarColor: '#933EC5'});

			    $("#sparkline9").sparkline([34, 43, 43, 35, 44, 32, 15, 22, 46, 33, 86, 54, 73, 53, 12, 53, 23, 65, 23, 63, 53, 42, 34, 56, 76, 15, 54, 23, 44], {
			        type: 'line',
			        lineWidth: 1,
			        width: '150px',
			        height: '150px',
			        lineColor: '#999',
			        fillColor: '#006DF0',
			    });
				
				 $('.sparklineedu').sparkline([ [1], [2], [3], [4, 2], [3], [5, 3] ], { type: 'bar', barColor: '#006DF0',
			        negBarColor: '#933EC5',});
				
				
				

				var sparklineCharts = function(){
					 $("#sparkline22").sparkline([
					 	0,
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][0]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][1]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][2]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][3]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][4]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][5]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][6]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][7]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][8]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][9]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][10]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["users_graph_data"][11]); ?>
					 	], {
						 type: 'line',
						 width: '100%',
						 height: '60',
						 lineColor: '#006DF0',
						 fillColor: "#006DF0"
					 });

					 $("#sparkline23").sparkline([
					 	0,
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][0]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][1]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][2]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][3]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][4]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][5]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][6]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][7]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][8]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][9]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][10]); ?>, 
					 	<?php echo intval($sys_all_dashboard_data["signup_graph_data"][11]); ?>], {
						 type: 'line',
						 width: '100%',
						 height: '60',
						 lineColor: '#933EC5',
						 fillColor: "#933EC5"
					 });

					 $("#sparkline24").sparkline([74, 43, 23, 55, 54, 32, 24, 12], {
						 type: 'line',
						 width: '100%',
						 height: '60',
						 lineColor: '#65b12d',
						 fillColor: "#65b12d"
					 });

					 $("#sparkline25").sparkline([24, 43, 33, 55, 64, 72, 44, 22], {
						 type: 'line',
						 width: '100%',
						 height: '60',
						 lineColor: '#D80027',
						 fillColor: "#D80027"
					 });

					 $("#sparkline51").sparkline([1, 4], {
						 type: 'pie',
						 height: '140',
						 sliceColors: ['#006DF0', '#ebebeb']
					 });

					 $("#sparkline52").sparkline([5, 3], {
						 type: 'pie',
						 height: '140',
						 sliceColors: ['#933EC5', '#ebebeb']
					 });

					 $("#sparkline53").sparkline([2, 2], {
						 type: 'pie',
						 height: '140',
						 sliceColors: ['#65b12d', '#ebebeb']
					 });

					 $("#sparkline54").sparkline([2, 3], {
						 type: 'pie',
						 height: '140',
						 sliceColors: ['#D80027', '#ebebeb']
					 });
				};

				var sparkResize;

				$(window).resize(function(e) {
					clearTimeout(sparkResize);
					sparkResize = setTimeout(sparklineCharts, 500);
				});

				sparklineCharts();



				
				
			})(jQuery); 
    </script>
    <!-- calendar JS
		============================================ -->
    <script src="js/calendar/moment.min.js"></script>
    <script src="js/calendar/fullcalendar.min.js"></script>
    <script src="js/calendar/fullcalendar-active.js"></script>
    <!-- Data Maps JS
		============================================ -->
    <script src="js/data-map/d3.min.js"></script>
    <script src="js/data-map/topojson.js"></script>
    <script src="js/data-map/datamaps.all.min.js"></script>
    <!-- <script src="js/data-map/data-maps-active.js"></script>-->
    <script type="text/javascript">
    	(function ($) {
 "use strict";
	
	var basic_choropleth = new Datamap({
			  element: document.getElementById("basic_choropleth"),
			  projection: 'mercator',
        geographyConfig: {
            highlightBorderColor: '#bada55',
           popupTemplate: function(geography, data) {
              return '<div class="hoverinfo">' + geography.properties.name + ' <br> Users :' +  data.fillKey + ' '
            },
            highlightBorderWidth: 3
          },
  			  fills: {
				defaultFill: "#DBDAD6",
				authorHasTraveledTo: "#006DF0"
			  },
			  data: {
				USA: { fillKey: "authorHasTraveledTo", users : "NOT COMPLETED"  },
				JPN: { fillKey: "authorHasTraveledTo", users : "NOT COMPLETED"  },
				ITA: { fillKey: "authorHasTraveledTo", users : "NOT COMPLETED"  },
				CRI: { fillKey: "authorHasTraveledTo", users : "NOT COMPLETED"  },
				KOR: { fillKey: "authorHasTraveledTo", users : "NOT COMPLETED"  },
				DEU: { fillKey: "authorHasTraveledTo", users : "NOT COMPLETED"  },
			  }
			});

			var colors = d3.scale.category10();

      /*
			window.setInterval(function() {
			  basic_choropleth.updateChoropleth({
				USA: colors(Math.random() * 10),
				RUS: colors(Math.random() * 100),
				AUS: { fillKey: 'authorHasTraveledTo' },
				BRA: colors(Math.random() * 50),
				CAN: colors(Math.random() * 50),
				ZAF: colors(Math.random() * 50),
				IND: colors(Math.random() * 50),
			  });
			}, 2000);
      */
	
	
        
	
		var basic = new Datamap({
                element: document.getElementById("basic_map"),
                responsive: true,
                fills: {
                    defaultFill: "#DBDAD6"
                },
                geographyConfig: {
                    highlightFillColor: '#006DF0',
                    highlightBorderWidth: 0,
                },
            });

            var selected_map = new Datamap({
                element: document.getElementById("selected_map"),
                responsive: true,
                fills: {
                    defaultFill: "#DBDAD6",
                    active: "#006DF0"
                },
                geographyConfig: {
                    highlightFillColor: '#006DF0',
                    highlightBorderWidth: 0,
                },
                data: {
                    USA: { fillKey: "active" },
                    RUS: { fillKey: "active" },
                    DEU: { fillKey: "active" },
                    BRA: { fillKey: "active" }
                }
            });

            var usa_map = new Datamap({
                element: document.getElementById("usa_map"),
                responsive: true,
                scope: 'usa',
                fills: {
                    defaultFill: "#DBDAD6",
                    active: "#006DF0"
                },
                geographyConfig: {
                    highlightFillColor: '#006DF0',
                    highlightBorderWidth: 0
                },
                data: {
                    NE: { fillKey: "active" },
                    CA: { fillKey: "active" },
                    NY: { fillKey: "active" },
                }
            });

			
			var map = new Datamap({
        scope: 'world',
        element: document.getElementById('projection_map'),
        projection: 'orthographic',
        fills: {
          defaultFill: "#ABDDA4",
          gt50: colors(Math.random() * 20),
          eq50: colors(Math.random() * 20),
          lt25: colors(Math.random() * 10),
          gt75: colors(Math.random() * 200),
          lt50: colors(Math.random() * 20),
          eq0: colors(Math.random() * 1),
          pink: '#0fa0fa',
          gt500: colors(Math.random() * 1)
        },
        projectionConfig: {
          rotation: [97,-30]
        },
        data: {
          'USA': {fillKey: 'lt50' },
          'MEX': {fillKey: 'lt25' },
          'CAN': {fillKey: 'gt50' },
          'GTM': {fillKey: 'gt500'},
          'HND': {fillKey: 'eq50' },
          'BLZ': {fillKey: 'pink' },
          'GRL': {fillKey: 'eq0' },
          'CAN': {fillKey: 'gt50' }
        }
      });

      map.graticule();

      map.arc([{
        origin: {
          latitude: 61,
          longitude: -149
        },
        destination: {
          latitude: -22,
          longitude: -43
        }
      }], {
        greatArc: true,
        animationSpeed: 2000
      });
 
			
            var arc_map = new Datamap({
                element: document.getElementById("arc_map"),
                responsive: true,
                fills: {
                    defaultFill: "#F2F2F0",
                    active: "#006DF0",
                    usa: "#006DF0"
                },
                geographyConfig: {
                    highlightFillColor: '#006DF0',
                    highlightBorderWidth: 0
                },
                data: {
                    USA: {fillKey: "usa"},
                    RUS: {fillKey: "active"},
                    DEU: {fillKey: "active"},
                    POL: {fillKey: "active"},
                    JAP: {fillKey: "active"},
                    AUS: {fillKey: "active"},
                    BRA: {fillKey: "active"}
                }
            });

            arc_map.arc(
                    [
                        { origin: 'USA', destination: 'RUS'},
                        { origin: 'USA', destination: 'DEU'},
                        { origin: 'USA', destination: 'POL'},
                        { origin: 'USA', destination: 'JAP'},
                        { origin: 'USA', destination: 'AUS'},
                        { origin: 'USA', destination: 'BRA'}
                    ],
                    { strokeColor: '#006DF0', strokeWidth: 1}
            );
			
	
})(jQuery); 
    </script>
    <!-- plugins JS
		============================================ -->
    <script src="js/plugins.js"></script>
    <!-- main JS
		============================================ -->
    <script src="js/main.js"></script>
    <!-- tawk chat JS
		============================================ -->
    <script src="js/tawk-chat.js"></script>
</body>

</html>