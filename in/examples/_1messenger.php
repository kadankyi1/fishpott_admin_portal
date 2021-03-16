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
$page_name = "Messenger"; 
$page_name_real = "unread_messages"; 
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

if(!isset($_GET["sp"]) || trim($_GET["sp"]) == "" || !isset($_GET["rp"]) || trim($_GET["rp"]) == ""){

    // QUERY VALUES DECLARED HERE
    $query =  "SELECT sku, chat_id, sender_pottname,  receiver_pottname, message_text, message_time FROM " . CHAT_MESSAGES_TABLE_NAME;

    $where_clause_sql_query_addition = "";
    $or_where_and_statements = "";
    $page_results_quantity = " 20 ";
    $order_by_addition = " ORDER BY sku ASC LIMIT  " . $page_results_quantity;
    $value_holder_array = array();
    $all_users = array();
    $value_types_string = "";
    $first_sku = 0;


    // IF A SEARCH HAS BEEN MADE
    if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST" && !isset($_GET["o"]) ){

        if(isset($_POST["user_age_start"]) && trim($_POST["user_age_start"]) != ""){
                $account_type = trim($_POST["user_age_start"]);
                if($where_clause_sql_query_addition == ""){
                    $or_where_and_statements = " WHERE ";
                } else {
                    $or_where_and_statements =  " " . $_POST["user_age_start_or_and_type"] . " ";
                }
                $where_clause_sql_query_addition .= " $or_where_and_statements sender_pottname = ? ";
                array_push($value_holder_array, $account_type);
                $value_types_string .= "s";
        }

        if(isset($_POST["user_pott_name"]) && trim($_POST["user_pott_name"]) != ""){
            $user_pott_name = trim($_POST["user_pott_name"]);
            if($where_clause_sql_query_addition == ""){
                $or_where_and_statements = " WHERE ";
            } else {
                $or_where_and_statements =  " " . $_POST["user_pott_name_or_and_type"] . " ";
            }
            $where_clause_sql_query_addition .= " $or_where_and_statements receiver_pottname = ?";;
            array_push($value_holder_array, $user_pott_name);
            $value_types_string .= "s";
        }


        $query .= $where_clause_sql_query_addition . $order_by_addition;

        if($where_clause_sql_query_addition == ""){
            $or_where_and_statements = " WHERE ";
        } else {
            $or_where_and_statements =  " AND ";
        }
            

      $start_sku = intval($_GET["start_sku"]);
        $first_sku = $start_sku - intval($page_results_quantity);
      $where_clause_sql_query_addition .= " $or_where_and_statements sku > ? ";

        $_SESSION["unread_messages_where_clause"] = $where_clause_sql_query_addition;
        $_SESSION["unread_messages_order_by_clause"] = $order_by_addition;
        $_SESSION["unread_messages_value_holder_array"] = $value_holder_array;
        $_SESSION["unread_messages_value_types_string"] = $value_types_string;

    } else if(
        !isset($_GET["o"]) && 
        ( 
            isset($_SESSION["unread_messages_where_clause"]) && trim($_SESSION["unread_messages_where_clause"]) != ""
        )
    ){
        $value_holder_array = $_SESSION["unread_messages_value_holder_array"];
        $value_types_string = $_SESSION["unread_messages_value_types_string"];

        $start_sku = intval($_GET["start_sku"]);
        $first_sku = $start_sku - intval($page_results_quantity);
        array_push($value_holder_array, $start_sku);
        $value_types_string .= "i";

        $query .= $_SESSION["unread_messages_where_clause"] . $_SESSION["unread_messages_order_by_clause"];

    }  else {

      if(isset($_GET["start_sku"]) && intval($_GET["start_sku"]) > 0){

        $start_sku = intval($_GET["start_sku"]);
        $first_sku = $start_sku - intval($page_results_quantity);

        $where_clause_sql_query_addition =    " WHERE receiver_pottname = '" . FISHPOT_POTT_NAME . "' AND sku > ? ";

        $_SESSION["unread_messages_value_holder_array"] = $value_holder_array;
        $_SESSION["unread_messages_value_types_string"] = $value_types_string;


        array_push($value_holder_array, $start_sku);
        $value_types_string .= "i";

        $_SESSION["unread_messages_where_clause"] = $where_clause_sql_query_addition;
        $_SESSION["unread_messages_order_by_clause"] = $order_by_addition;

      } else {

        $where_clause_sql_query_addition =    " WHERE receiver_pottname = '" . FISHPOT_POTT_NAME . "'";
        }

     $query .= $where_clause_sql_query_addition . $order_by_addition;
    } 

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
                  <h4 class="card-title">Search Conversations</h4>
                  <?php if(isset($_SESSION["asem"]) && trim($_SESSION["asem"]) != "") { ?>
                  <p class="card-category"><?php echo $_SESSION["asem"]; unset($_SESSION["asem"]); ?></p>
                  <?php } else { ?>
                  <p class="card-category">Find any conversation using the pottname of the sender and/or receiver</p>
                  <?php } ?>
                </div>
                <div class="card-body">
                  <form method="POST" action="_1messenger.php">
                    <div class="row">
                      <div class="col-md-5">
                        <div class="form-group">
                          <label class="bmd-label-floating">Sender Pott name</label>
                          <input type="text" name="user_age_start" class="form-control">
                        </div>
                        <input type="radio" checked="checked" name="user_age_start_or_and_type" id="user_age_start_and_type" value="AND"> 
                        <label for="user_age_start_and_type" style="cursor: pointer;">AND</label>
                        <input type="radio" name="user_age_start_or_and_type" id="user_age_start_or_type" value="OR"> 
                        <label for="user_age_start_or_type" style="cursor: pointer;">OR</label> 
                        <br>
                      </div>
                      <div class="col-md-7">
                        <div class="form-group">
                          <label class="bmd-label-floating">Receiver Pott name</label>
                          <input type="text" name="user_pott_name" class="form-control">
                        </div>
                        <input type="radio" checked="checked" name="user_pott_name_or_and_type"  id="user_pott_name_and_type" value="AND"> 
                        <label for="user_pott_name_and_type" style="cursor: pointer;">AND</label>
                        <input type="radio" name="user_pott_name_or_and_type"  id="user_pott_name_or_type" value="OR" > 
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

      <?php if(!isset($_GET["sp"]) || trim($_GET["sp"]) == "" || !isset($_GET["rp"]) || trim($_GET["rp"]) == ""){ ?>

          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Conversations</h4>
                  <p class="card-category">If the inquiry is worth attending to, click the green action button to open chat, if not, click the red to close chat by sending user automated chat closed messages</p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                      <thead class=" text-primary">
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Sender PottName
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Receiver PottName
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Last Message
                        </th>
                        <th style="color: #1f1f14; font-weight: bolder;">
                          Message Time
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
                    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("sku", "chat_id", "sender_pottname", "receiver_pottname", "message_text", "message_time"), 5, 2);

                    //BINDING THE RESULTS TO VARIABLES
                    $prepared_statement_results_array->bind_result($sku, $chat_id, $sender_pottname, $receiver_pottname, $message_text, $message_time);

                    $all_chats = array();
                    $sku = 0;
                    while($prepared_statement_results_array->fetch()){
                        if (in_array($chat_id, $all_chats))
                          {
                            continue;
                          }
                        if($first_sku <= 0 && $sku > 0){
                            $first_sku = $sku;
                        }

                    ?>

                        <tr>
                          <td style="width: 80px;">
                           <span class="text-primary">@</span><b><?php echo $sender_pottname; ?></b>
                          </td>
                          <td>
                           <span class="text-primary">@</span><b><?php echo $receiver_pottname; ?></b>
                          </td>
                          <td>
                           <?php echo $message_text; ?>
                          </td>
                          <td>
                            <?php echo $timeObject->reformatDate("M j, Y, g:i a", $message_time); ?>
                          </td>
                          <td>
                            <a href="_1messenger.php?sp=<?php echo $sender_pottname; ?>&rp=<?php echo $receiver_pottname; ?>">
                                <img class="material-icons" src="../img/messenger.png" alt="complete" style="width: 20px; height: 20px;">
                            </a>
                          </td>
                        </tr>

                    <?php
                        array_push($all_chats, $chat_id);
                    }

                    $next_page_link = "_1messenger.php?start_sku=" . $sku;
                    $previous_page_link = "_1messenger.php?start_sku=" . $first_sku;
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

          <?php } else { ?>

          <?php
            
            $sp = $_GET["sp"];
            $rp = $_GET["rp"];



            $query =  "SELECT " 
            . USER_BIO_TABLE_NAME . ".profile_picture,  " 
            . USER_BIO_TABLE_NAME . ".pot_name,  " 
            . USER_BIO_TABLE_NAME . ".first_name,  " 
            . USER_BIO_TABLE_NAME . ".last_name,  " 
            . USER_BIO_TABLE_NAME . ".country,  " 
            . USER_BIO_TABLE_NAME . ".phone,  " 
            . USER_BIO_TABLE_NAME . ".investor_id,  " 
            . USER_BIO_TABLE_NAME . ".verified_tag, "  
            . USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd, "   
            . USER_BIO_TABLE_NAME . ".debit_wallet_usd  FROM "
            . USER_BIO_TABLE_NAME 
            . " WHERE " . USER_BIO_TABLE_NAME . ".pot_name = ? ";

            $customer_found = 0;

              // GETTING THE SHARES QUANTITY
              $prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s", array($sp));

              if($prepared_statement_2 !== false){
                  $prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("profile_picture", "pot_name", "first_name", "last_name", "country", "phone", "investor_id", "verified_tag", "login_type", "flag", "withdrawal_wallet_usd", "debit_wallet_usd"), 12, 2);

                  if($prepared_statement_results_array_2 !== false){
                      $customer_found = 1;
                      $prepared_statement_results_array_2->bind_result($profile_picture, $pot_name, $first_name, $last_name, $country, $phone, $investor_id, $verified_tag, $withdrawal_wallet_usd, $debit_wallet_usd);

                  } else {
                        $customer_found = 0;
                      }
              } else {
                  $customer_found = 0;
              }

          ?>

          <?php if( $customer_found == 1){ ?>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">Messenger --- Sender : <?php echo $sp; ?> || Receiver : <?php echo $rp; ?></h4>
                  <p class="card-category">Respond to a user who has made an inquiry. Once you are done, make sure the last message in the chat if from FishPot otherwise it will still read the chat as unattended. FishPot is not allowed to send messages as a third party in a conversation</p>
                </div>
                <div class="card-body" id="all_messages_holder_div" style="overflow-y: scroll; height:400px;">
                  <?php
                    $query =  "(SELECT sku, chat_id, sender_pottname,  receiver_pottname, message_text, message_time FROM " . CHAT_MESSAGES_TABLE_NAME . " WHERE (sender_pottname = ? AND receiver_pottname = ?) OR (sender_pottname = ? AND receiver_pottname = ?) ORDER BY sku DESC LIMIT 50) ORDER BY sku ASC";

                    $prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 4, "ssss", array($sp, $rp, $rp, $sp));
                    if($prepared_statement === false){
                        echo "<h1>QUERY FAILED</h1>";
                    }


                    // GETTING RESULTS OF QUERY INTO AN ARRAY
                    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("sku", "chat_id", "sender_pottname", "receiver_pottname", "message_text", "message_time"), 6, 2);

                    //BINDING THE RESULTS TO VARIABLES
                    $prepared_statement_results_array->bind_result($sku, $chat_id, $sender_pottname, $receiver_pottname, $message_text, $message_time);

                    $all_chats = array();
                    $sku = 0;
                    while($prepared_statement_results_array->fetch()){

                  ?>

                    <?php if($sp ==  FISHPOT_POTT_NAME || $rp == FISHPOT_POTT_NAME) { ?>

                        <?php if($sender_pottname == FISHPOT_POTT_NAME){ ?>
                            <div  class="card-header card-header-warning" style=" margin-left: 20%; margin-bottom: 20px; margin-top: 20px;">
                              <span style="font-weight: 500"><?php echo $sender_pottname; ?> ||  <?php echo $timeObject->reformatDate("M j, Y, g:i a", $message_time); ?></span>
                              <br>
                              <hr>
                              <?php echo $message_text; ?>
                            </div>
                        <?php } else { ?>
                            <div  class="card-header card-header-info" style=" margin-right: 20%; margin-bottom: 20px; margin-top: 20px;">
                              <span style="font-weight: 500"><?php echo $sender_pottname; ?> || <?php echo $timeObject->reformatDate("M j, Y, g:i a", $message_time); ?></span>
                              <br>
                              <hr>
                              <?php echo $message_text; ?>
                            </div>
                        <?php } ?>

                    <?php } else { ?>

                        <?php if($sender_pottname == $sp){ ?>
                            <div  class="card-header card-header-warning" style=" margin-left: 20%; margin-bottom: 20px; margin-top: 20px;">
                              <span style="font-weight: 500"><?php echo $sender_pottname; ?> ||  <?php echo $timeObject->reformatDate("M j, Y, g:i a", $message_time); ?></span>
                              <br>
                              <hr>
                              <?php echo $message_text; ?>
                            </div>
                        <?php } else { ?>
                            <div  class="card-header card-header-info" style=" margin-right: 20%; margin-bottom: 20px; margin-top: 20px;">
                              <span style="font-weight: 500"><?php echo $sender_pottname; ?> || <?php echo $timeObject->reformatDate("M j, Y, g:i a", $message_time); ?></span>
                              <br>
                              <hr>
                              <?php echo $message_text; ?>
                            </div>
                        <?php } ?>

                    <?php } ?>
                  <?php } ?>

                </div>

              <?php if($customer_found == 1 && $rp == FISHPOT_POTT_NAME || $sp == FISHPOT_POTT_NAME){ ?>
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Message</label>
                          <textarea id="message_text_textarea" type="text" name="id" required="required" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success" onclick="sendMessage();">Send</button>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                <br>
                </div>
              <?php } ?>
                <div style="margin-left : 10%; margin-right : 10%; text-align: center; display: none;" id="info_bar" class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    <b><span style="text-align: center;" id="info_bar_text_holder"></span></b>
                </div>
              </div>
            </div>
          </div>
          <?php } else { ?>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">CLIENT NOT FOUND</h4>
                  <p class="card-category">The client you are trying to contact was not found</p>
                </div>
              </div>
            </div>
          </div>


        <?php 

            }
          }

         ?>


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
  <!--  Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="../assets/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="../assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
  <script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
  <!--  Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
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
  <script type="text/javascript">
    var this_id_1 = '<?php echo $sp; ?>';
    var this_id_2 = '<?php echo $rp; ?>';
    var this_last_sku = '<?php echo $sku; ?>';
    var fp_pottname = '<?php echo FISHPOT_POTT_NAME; ?>';
  </script>
  <script src="../assets/demo/chat.js"></script>
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
