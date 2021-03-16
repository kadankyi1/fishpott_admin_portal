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
$page_name = "News Manager"; 
$page_name_real = "news_management"; 
$all_notifications_count = 0;

$error = "";
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


if(isset($_POST["id"]) && trim($_POST["id"]) != ""){
	$investor_id = $_POST["id"];
	$_GET["id"] = $investor_id;
}

if(isset($_GET["id"]) && trim($_GET["id"]) != ""){

	$input_news_id = trim($_GET["id"]);
  //USER INFO ARRAY
  $user_info_array = array();

  // GETTING USER INFO.
  $query =  "SELECT " 
      . NEWS_TABLE_NAME . ".sku,  " 
      . NEWS_TABLE_NAME . ".type,  " 
      . NEWS_TABLE_NAME . ".news_id,  " 
      . NEWS_TABLE_NAME . ".news,  " 
      . NEWS_TABLE_NAME . ".date_time,  " 
      . NEWS_TABLE_NAME . ".inputtor_id,  " 
      . NEWS_TABLE_NAME . ".added_item_news_id,  " 
      . NEWS_TABLE_NAME . ".news_image, "  
      . NEWS_TABLE_NAME . ".added_item_type, "   
      . NEWS_TABLE_NAME . ".news_video, "    
      . NEWS_TABLE_NAME . ".news_id_ref, "  
      . NEWS_TABLE_NAME . ".shares4sale_id, "  
      . NEWS_TABLE_NAME . ".flag FROM "  
      . NEWS_TABLE_NAME
      . " WHERE " . NEWS_TABLE_NAME . ".news_id = ?";



  // PREPARING THE SQL STATEMENT
  $prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s", array($input_news_id));

  if($prepared_statement !== false){
    // GETTING RESULTS OF QUERY INTO AN ARRAY
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
      NEWS_TABLE_NAME . ".sku",
      NEWS_TABLE_NAME . ".type", 
      NEWS_TABLE_NAME . ".news_id", 
      NEWS_TABLE_NAME . ".news", 
      NEWS_TABLE_NAME . ".date_time", 
      NEWS_TABLE_NAME . ".inputtor_id", 
      NEWS_TABLE_NAME . ".added_item_news_id", 
      NEWS_TABLE_NAME . ".news_image",  
      NEWS_TABLE_NAME . ".added_item_type",   
      NEWS_TABLE_NAME . ".news_video",    
      NEWS_TABLE_NAME . ".news_id_ref", 
      NEWS_TABLE_NAME . ".shares4sale_id", 
      NEWS_TABLE_NAME . ".flag"
    ), 
    13, 1);
          
    if($prepared_statement_results_array !== false){

      $investor_id = $prepared_statement_results_array[5];
      $user_info_array["sku"] = $prepared_statement_results_array[0];
      $user_info_array["type"] = $prepared_statement_results_array[1];
      $user_info_array["news_id"] = $prepared_statement_results_array[2];
      $user_info_array["news"] = $prepared_statement_results_array[3];
      $user_info_array["date_time"] = $prepared_statement_results_array[4];
      $user_info_array["inputtor_id"] = $prepared_statement_results_array[5];
      $user_info_array["added_item_news_id"] = $prepared_statement_results_array[6];
      $user_info_array["news_image"] = $prepared_statement_results_array[7];
      $user_info_array["added_item_type"] = $prepared_statement_results_array[8];
      $user_info_array["news_video"] = $prepared_statement_results_array[9];
      $user_info_array["news_id_ref"] = $prepared_statement_results_array[10];
      $user_info_array["shares4sale_id"] = $prepared_statement_results_array[11];
      $user_info_array["news_flag"] = $prepared_statement_results_array[12];

      }

    }

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
      . LOGIN_TABLE_NAME . ".number_verified, "  
      . LOGIN_TABLE_NAME . ".number_verifcation_date, "
      . LOGIN_TABLE_NAME . ".password_reset_date, "  
      . LOGIN_TABLE_NAME . ".government_id_verified, " 
      . LOGIN_TABLE_NAME . ".flag_reason, " 
      . LOGIN_TABLE_NAME . ".government_id_type, "  
      . LOGIN_TABLE_NAME . ".government_id_number FROM "  
      . USER_BIO_TABLE_NAME . " INNER JOIN " 
      . LOGIN_TABLE_NAME . " ON  "  
      . USER_BIO_TABLE_NAME . ".investor_id="  
      . LOGIN_TABLE_NAME . ".id "
      . " WHERE " . USER_BIO_TABLE_NAME . ".investor_id = ?";


  // PREPARING THE SQL STATEMENT
  $prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s",array($investor_id));

  if($prepared_statement !== false){
    // GETTING RESULTS OF QUERY INTO AN ARRAY
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
    USER_BIO_TABLE_NAME . ".sku", 
    USER_BIO_TABLE_NAME . ".profile_picture", 
    USER_BIO_TABLE_NAME . ".pot_name", 
    USER_BIO_TABLE_NAME . ".first_name", 
    USER_BIO_TABLE_NAME . ".last_name", 
    USER_BIO_TABLE_NAME . ".country", 
    USER_BIO_TABLE_NAME . ".phone", 
    USER_BIO_TABLE_NAME . ".investor_id", 
    USER_BIO_TABLE_NAME . ".verified_tag", 
    LOGIN_TABLE_NAME . ".login_type", 
    USER_BIO_TABLE_NAME . ".flag", 
    USER_BIO_TABLE_NAME . ".net_worth",
    LOGIN_TABLE_NAME . ".number_verified", 
    LOGIN_TABLE_NAME . ".number_verifcation_date", 
    LOGIN_TABLE_NAME . ".password_reset_date",
    LOGIN_TABLE_NAME . ".government_id_verified",
    LOGIN_TABLE_NAME . ".flag_reason",
    LOGIN_TABLE_NAME . ".government_id_type",
    LOGIN_TABLE_NAME . ".government_id_number"
    ), 
    19, 1);
          
    if($prepared_statement_results_array !== false){

      $investor_id = $prepared_statement_results_array[7];
      $user_info_array["sku"] = $prepared_statement_results_array[0];
      $user_info_array["first_name"] = $prepared_statement_results_array[3];
      $user_info_array["last_name"] = $prepared_statement_results_array[4];
      $user_info_array["pot_name"] = $prepared_statement_results_array[2];
      $user_info_array["country"] = $prepared_statement_results_array[5];
      $user_info_array["phone"] = $prepared_statement_results_array[6];
      $user_info_array["investor_id"] = $prepared_statement_results_array[7];
      $user_info_array["verified_tag"] = $prepared_statement_results_array[8];
      $user_info_array["pot_name"] = $prepared_statement_results_array[2];
      $user_info_array["account_type"] = $prepared_statement_results_array[9];
      $user_info_array["number_verifcation_date"] = $prepared_statement_results_array[13];
      $user_info_array["net_worth"] = $prepared_statement_results_array[11];
      $user_info_array["password_reset_date"] = $prepared_statement_results_array[14];
      $user_info_array["flag_reason"] = $prepared_statement_results_array[16];
      $user_info_array["government_id_type"] = $prepared_statement_results_array[17];
      $user_info_array["government_id_number"] = $prepared_statement_results_array[18];

      if($prepared_statement_results_array[10] == 1){
        $user_info_array["flag"] = "Yes";
      } else {
        $user_info_array["flag"] = "No";
      }


      if($prepared_statement_results_array[12] == 1){
        $user_info_array["number_verified"] = "Yes";
      } else {
        $user_info_array["number_verified"] = "No";
      }

      if($prepared_statement_results_array[15] == 1){
        $user_info_array["government_id_verified"] = "Yes";
      } else {
        $user_info_array["government_id_verified"] = "No";
      }


      $db_profile_picture = "../../../pic_upload/" . $prepared_statement_results_array[1]; 
      if(trim($prepared_statement_results_array[1]) != "" && $validatorObject->fileExists($db_profile_picture) !== false){
          $user_info_array["profile_picture"] = HTTP_HEAD . "://fishpott.com/pic_upload/" . $prepared_statement_results_array[1];
      } else {
          $user_info_array["profile_picture"] = DEFAULT_NO_PHOTO_AVATAR_PICTURE_LINK;
      }

      if(strtolower($prepared_statement_results_array[9]) == "business"){
          $business_icon_display_style = "";
      } else {
          $business_icon_display_style = "display : none;";
      }

      if($prepared_statement_results_array[8] == 1){
          $verified_tag_display_style = "";
          $verified_tag_icon = "verified_seller.png";
      } else if($prepared_statement_results_array[8] == 2){
          $verified_tag_display_style = "";
          $verified_tag_icon = "verified.png";
      } else {
          $verified_tag_icon = "";
          $verified_tag_display_style = "display : none;";
      }

      if($prepared_statement_results_array[10] == 1){
          $flag_icon_display_style = "display : ;";
      } else {
          $flag_icon_display_style = "display : none;";
      }

    } else {

      $feedback = "1 QUERY FAILED";
      $feedback_color_type = "danger";

    }

  } else {
      $feedback = "2 QUERY FAILED";
      $feedback_color_type = "danger";
  }
}


if($feedback != "" && $_GET["id"] != ""){

    echo '<br><br><br><div style="margin-left : 10%; margin-right : 10%;" class="alert alert-' . $feedback_color_type .'">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      </button>
      <span>
        <b> FeedBack - </b>' . $feedback . '</span>
    </div> ';
}
?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">News</h4>
                  <?php if(isset($_SESSION["asem"]) && trim($_SESSION["asem"]) != "") { ?>
                  <p class="card-category"><?php echo $_SESSION["asem"]; unset($_SESSION["asem"]); ?></p>
                  <?php } else { ?>
                  <p class="card-category">Find news using the news ID</p>
                  <?php } ?>
                </div>
                <div class="card-body">
                  <form action="_1news_manager.php">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Enter News ID</label>
                          <input type="text" name="id" required="required" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-success">View News</button>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <?php if($error == "" && $_GET["id"] != ""){ ?>
                    <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">
                    NEWS 
                  </h4>
                  <p class="card-category">...</p>
                </div>
                <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">News Time</label>
                          <input type="text" value="<?php echo $user_info_array["date_time"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">News Type</label>
                          <input type="text" value="<?php echo $user_info_array["type"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">News ID</label>
                          <input type="text" value="<?php echo $user_info_array["news_id"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">News Flag Status</label>
                          <input type="text" value="<?php if($user_info_array["news_flag"] == 0){ echo "NOT flagged"; } else {echo "FLAGGED"; } ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Added Item ID</label>
                          <input type="text" value="<?php echo $user_info_array["added_item_news_id"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Reposted News ID</label>
                          <input type="text" value="<?php echo $user_info_array["news_id_ref"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Shares 4 Sale ID</label>
                          <input type="text" value="<?php echo $user_info_array["shares4sale_id"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">News Text</label>
                          <textarea type="text" class="form-control" readonly="readonly"><?php echo $user_info_array["news"]; ?></textarea>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group" style="text-align: center; align-content: center;">
                          <?php if($user_info_array["news_video"] == "") { ?>
                          <input type="text" value="NO VIDEO" class="form-control" readonly="readonly">
                          <?php } else { ?>
                          <label class="bmd-label-floating bmd-form-group is-focused">News Video</label>
                          <input type="text" value="VIDEO WAS ADDED" class="form-control" readonly="readonly">
                          <br>
                          <video style="width: 80%; height: 250px;" controls>
                            <source src="<?php echo HTTP_HEAD . '://fishpott.com/user/' . $user_info_array['news_video']; ?>" type="video/mp4">
                            Your browser does not support HTML5 video.
                          </video>
                          <?php } ?>
                        </div>
                      </div>

                      <?php

                        $query =  "SELECT " . NEWS_IMAGES_TABLE_NAME . ".link_address  FROM "  . NEWS_IMAGES_TABLE_NAME . " WHERE " . NEWS_IMAGES_TABLE_NAME . ".news_id = ?";


                      $prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s", array($input_news_id));
                      if($prepared_statement === false){
                          echo "<h1>QUERY FAILED</h1>";
                      }

                      // GETTING RESULTS OF QUERY INTO AN ARRAY
                      $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("link_address"), 1, 2);

                      //BINDING THE RESULTS TO VARIABLES
                      $prepared_statement_results_array->bind_result($link_address);

                      while($prepared_statement_results_array->fetch()){

                        if(!isset($label_added)){
                    ?>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">IMAGES</label>
                          <textarea type="text" class="form-control" readonly="readonly">IMAGES WERE ADDED TO POST</textarea>
                        </div>
                      </div>
                        <?php $label_added = 1; } ?>
                      <div class="col-md-4">
                        <img class="form-group" style="height: 250px; width: 100%; border-radius: 5px;cursor: pointer; transition: 0.3s; margin-bottom: 10px;" src="<?php echo HTTP_HEAD . '://fishpott.com/user/' . $link_address; ?>"/>
                      </div>
                    <?php 
                        }
                    ?>


                      <div class="col-md-12">
                        <div class="form-group" style="text-align: center; align-content: center;">
                          <form method="get" action="../../../inc/admin/flag_unflag_news.php">
                            <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['news_id']; ?>" name="id">
                            <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="t">
                            <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="verifying_as_trader">
                            <button type="submit" style="width: 90%;" class="btn btn-success">Flag News</button>
                          </form>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group" style="text-align: center; align-content: center;">
                          <form method="get" action="../../../inc/admin/flag_unflag_news.php">
                            <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['news_id']; ?>" name="id">
                            <input type="text" style="display: none;" readonly="readonly" required="required" value="0" name="t">
                            <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="unverify_user">
                            <button type="submit" style="width: 90%;" class="btn btn-danger">Unflag News</button>
                          </form>
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">
                    NEWS MAKER INFO
                    <br>
                    <hr>
                    <?php echo $user_info_array["first_name"] . " " . $user_info_array["last_name"];  ?> 

                    <img class="img" src="../img/<?php echo $verified_tag_icon; ?>" alt="verified account" style="width: 15px; height: 15px; margin-top: -10px;<?php echo $verified_tag_display_style; ?>" /> 

                    <img class="img" src="../img/business.png" alt="business account" style="width: 15px; height: 15px; margin-top: -10px; margin-left: -5px;<?php echo $business_icon_display_style; ?>" /> 

                    <a style="<?php echo $flag_icon_display_style; ?>">
                        <i style="cursor: pointer; color: red;" class="material-icons">flag</i>
                    </a>
                  </h4>
                  <p class="card-category">...</p>
                </div>
                <div class="card-body">
                  <form>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">First Name</label>
                          <input type="text" value="<?php echo $user_info_array["first_name"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Last Name</label>
                          <input type="text" value="<?php echo $user_info_array["last_name"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Pott Name</label>
                          <input type="text" value="<?php echo $user_info_array["pot_name"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Pearls</label>
                          <input type="text" value="<?php echo $user_info_array["net_worth"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Country</label>
                          <input type="text" value="<?php echo $user_info_array["country"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Phone</label>
                          <input type="text" value="<?php echo $user_info_array["phone"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Account Type</label>
                          <input type="text" value="<?php echo $user_info_array["account_type"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Flagged</label>
                          <input type="text" value="<?php echo $user_info_array["flag"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Flag Reason</label>
                          <input type="text" value="<?php echo $user_info_array["flag_reason"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Government ID Verified?</label>
                          <input type="text" value="<?php echo $user_info_array["government_id_verified"]; ?>" class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Government ID Type</label>
                          <input type="text" value="<?php echo $user_info_array["government_id_type"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Government ID Number</label>
                          <input type="text" value="<?php echo $user_info_array["government_id_number"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Number Verified</label>
                          <input type="text" value="<?php echo $user_info_array["number_verified"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Number Verification Date</label>
                          <input type="text" value="<?php echo $user_info_array["number_verifcation_date"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Password Reset Date</label>
                          <input type="text" value="<?php echo $user_info_array["password_reset_date"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Skew</label>
                          <input type="text" value="<?php echo $user_info_array["sku"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Links</label>
                          <input type="text" value="<?php echo $user_info_array["links"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating bmd-form-group is-focused">Linkups</label>
                          <input type="text" value="<?php echo $user_info_array["linkups"]; ?> " class="form-control" readonly="readonly">
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card card-profile">
                <div class="card-avatar">
                  <a href="#pablo">
                    <img class="img" style="height: 150px; width: 150px;" src="<?php echo $user_info_array['profile_picture'] ?>" />
                  </a>
                </div>
                <div class="card-body">
                  <h6 class="card-category text-gray" style="display: none;">CEO / Co-Founder</h6>
                  <h4 class="card-title" style="display: none;">Alec Thompson</h4>
                  <p class="card-description" style="display: none;">
                    Don't be scared of the truth because we need to restart the human foundation in truth And I love you like Kanye loves Kanye I love Rick Owens’ bed design but the back is...
                  </p>
                  <button type="submit" style="width: 90%;" class="btn btn-rose">Message User</button>
                  <form method="get" action="_1user_view_one_user_profile.php">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="remove_profile_photo">
                    <button type="submit" style="width: 90%;" class="btn btn-primary">Remove Profile Photo</button>

                  </form>
                  <form method="get" action="_1user_view_one_user_profile.php">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="request_phone_verification">
                    <button type="submit" style="width: 90%;" class="btn btn-azure">Request Phone Verification</button>

                  </form>
                  <form method="get" action="_1user_view_one_user_profile.php">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="request_id_verification">
                    <button type="submit" style="width: 90%;" class="btn btn-warning">Request ID Verification</button>

                  </form>
                  <button type="submit" style="width: 90%;" class="btn btn-secondary">xxxxxxxxxxxx</button>

                  <form method="get" action="_1user_view_one_user_profile.php">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="verifying_as_celebrity">
                    <button type="submit" style="width: 90%;" class="btn btn-info">Verify As Celebrity</button>

                  </form>
                  <form method="get" action="_1user_view_one_user_profile.php">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="verifying_as_trader">
                    <button type="submit" style="width: 90%;" class="btn btn-success">Verify As Seller</button>

                  </form>
                  <form method="get" action="_1user_view_one_user_profile.php">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="unverify_user">
                    <button type="submit" style="width: 90%;" class="btn btn-danger">UnVerify</button>

                  </form>

                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <p class="card-category">FLAG USER</p>
                </div>
                <div class="card-body">
                  <form method="post" action="_1user_view_one_user_profile.php">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Enter Reason</label>
                          <input type="text" name="flagging_user_reason" required="required" class="form-control">
                        </div>

                        <button type="submit" style="width: 100%;" class="btn btn-success">Flag User</button>    
                  </form>
                        <hr>
                        <form method="get" action="_1user_view_one_user_profile.php">
                    	<input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                          <input type="text" style="display: none;" readonly="readonly" required="required" value="1" name="unflagging_user">
                          <button type="submit" style="width: 100%;" class="btn btn-info">Unflag User</button>

                        </form>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
              </div>
            </div>
          </div>


          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <p class="card-category">VERIFY USER GOVERNMENT ID</p>
                </div>
                <div class="card-body">
                  <form method="post" action="_1user_view_one_user_profile.php">
                    <div class="row">
                    <input type="text" style="display: none;" readonly="readonly" required="required" value="<?php echo $user_info_array['investor_id']; ?>" name="id">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Enter ID Type</label>
                          <input type="text" name="verifying_government_id_type" required="required" class="form-control">
                        </div>
                        <div class="form-group">
                          <label class="bmd-label-floating">Enter ID Number</label>
                          <input type="text" name="verifying_government_id_number" required="required" class="form-control">
                        </div>
                        <button type="submit" style="width: 100%;" class="btn btn-success">Verify</button>
                  </form>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
              </div>
            </div>
          </div>

          <?php } ?>

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
  <script src="../js/myjs.js"></script>
</body>

</html>
