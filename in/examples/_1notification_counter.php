<?php
// TRANSFERS
$query = "SELECT count(*) FROM " . SHARES_TRANSFER_TABLE_NAME . " WHERE transfer_type != 'sale' AND (admin_review_status = 0 OR admin_id = '')";
$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 0, "", array());
if($prepared_statement !== false ){
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
        if($prepared_statement_results_array !== false && $prepared_statement_results_array[0] != 0){
                $sidebar_transfers_not = strval($prepared_statement_results_array[0]);
                $sidebar_transfers_not_style = "";
        } else {
            $sidebar_transfers_not = "0";
            $sidebar_transfers_not_style = "display: none;";
        }
} else {
    $sidebar_transfers_not = "0";
    $sidebar_transfers_not_style = "display: none;";
}

// SALE
$query = "SELECT count(*) FROM " . SHARES_TRANSFER_TABLE_NAME . " WHERE transfer_type = 'sale' AND admin_review_status = 0 AND admin_id = ''";
$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 0, "", array());
if($prepared_statement !== false ){
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
        if($prepared_statement_results_array !== false && $prepared_statement_results_array[0] != 0){
                $sidebar_sale_not = strval($prepared_statement_results_array[0]);
                $sidebar_sale_not_style = "";
        } else {
            $sidebar_sale_not = "0";
            $sidebar_sale_not_style = "display: none;";
        }
} else {
    $sidebar_sale_not = "0";
    $sidebar_sale_not_style = "display: none;";
}

// CREDIT REQUESTS
$query = "SELECT count(*) FROM " . MONEY_CREDIT_TABLE_NAME . " WHERE done_status = 'pending' AND  reviewer_admin_id = ''";
$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 0, "", array());
if($prepared_statement !== false ){
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
        if($prepared_statement_results_array !== false && $prepared_statement_results_array[0] != 0){
                $sidebar_credit_req_not = strval($prepared_statement_results_array[0]);
                $sidebar_credit_req_not_style = "";
        } else {
            $sidebar_credit_req_not = "0";
            $sidebar_credit_req_not_style = "display: none;";
        }
} else {
    $sidebar_credit_req_not = "0";
    $sidebar_credit_req_not_style = "display: none;";
}


// PENDING DIVIDENDS PAYOUT
$query = "SELECT count(*) FROM " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE yield_date <= ? AND admin_review_status = 1 AND num_of_shares > 0 AND flag = 0";
$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 1, "s", array(date("Y-m-d")));
if($prepared_statement !== false ){
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
        if($prepared_statement_results_array !== false && $prepared_statement_results_array[0] != 0){
                $sidebar_dividends_not = strval($prepared_statement_results_array[0]);
                $sidebar_dividends_not_style = "";
        } else {
            $sidebar_dividends_not = "0";
            $sidebar_dividends_not_style = "display: none;";
        }
} else {
    $sidebar_dividends_not = "0";
    $sidebar_dividends_not_style = "display: none;";
}

// WITHDRAWAL REQUESTS
$query = "SELECT count(*) FROM " . WITHDRAWAL_TABLE_NAME . " WHERE paid_status = 'pending' AND paid_date = '0000-00-00 00:00:00'";
$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 0, "", array());
if($prepared_statement !== false ){
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
        if($prepared_statement_results_array !== false && $prepared_statement_results_array[0] != 0){
                $sidebar_withdrawal_req_not = strval($prepared_statement_results_array[0]);
                $sidebar_withdrawal_req_not_style = "";
        } else {
            $sidebar_withdrawal_req_not = "0";
            $sidebar_withdrawal_req_not_style = "display: none;";
        }
} else {
    $sidebar_withdrawal_req_not = "0";
    $sidebar_withdrawal_req_not_style = "display: none;";
}



// SHARES CREDIT COUPON REQUESTS
$query = "SELECT count(*) FROM " . SHARES_CREDIT_COUPON_TABLE_NAME . " WHERE user_id != '' OR usage_date = '0000-00-00 00:00:00'";
$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 0, "", array());
if($prepared_statement !== false ){
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
        if($prepared_statement_results_array !== false && $prepared_statement_results_array[0] != 0){
                $sidebar_s_credit_not = strval($prepared_statement_results_array[0]);
                $sidebar_s_credit_not_style = "";
        } else {
            $sidebar_s_credit_not = "0";
            $sidebar_s_credit_not_style = "display: none;";
        }
} else {
    $sidebar_s_credit_not = "0";
    $sidebar_s_credit_not_style = "display: none;";
}

// MESSAGES
$query = "SELECT count(DISTINCT(chat_id)) FROM " . CHAT_MESSAGES_TABLE_NAME . " WHERE receiver_pottname = 'fishpot_inc'";
$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 0, "", array());
if($prepared_statement !== false ){
    $prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("count(*)"), 1, 1);
        if($prepared_statement_results_array !== false && $prepared_statement_results_array[0] != 0){
                $sidebar_msgs_not = strval($prepared_statement_results_array[0]);
                $sidebar_msgs_not_style = "";
        } else {
            $sidebar_msgs_not = "0";
            $sidebar_msgs_not_style = "display: none;";
        }
} else {
    $sidebar_msgs_not = "0";
    $sidebar_msgs_not_style = "display: none;";
}


$all_notifications_count = $all_notifications_count + intval($sidebar_transfers_not);
$all_notifications_count = $all_notifications_count + intval($sidebar_sale_not);
$all_notifications_count = $all_notifications_count + intval($sidebar_credit_req_not);
$all_notifications_count = $all_notifications_count + intval($sidebar_dividends_not);
$all_notifications_count = $all_notifications_count + intval($sidebar_withdrawal_req_not);
$all_notifications_count = $all_notifications_count + intval($sidebar_s_credit_not);
$all_notifications_count = $all_notifications_count + intval($sidebar_msgs_not) - 1;

