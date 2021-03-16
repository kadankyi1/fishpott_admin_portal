var url_send_message = '../../../inc/admin/send_message.php';
var url_load_messages = '../../../inc/admin/get_messages.php';
var getting_messages = 0;


function sendMessage(){

  document.getElementById('info_bar').style.display = "none";
  var this_msg = document.getElementById('message_text_textarea').value;
  document.getElementById('message_text_textarea').value = "";
  var loginData = {
      'id_1' : this_id_1,
      'id_2' : this_id_2,
      'msg' : this_msg
  };         

  console.log(loginData);
  $.ajax({
    type: "POST",
    url: url_send_message,
    data: loginData,
    success: function(response){
      console.log(response);
      getMessages();
    },

    error: function(XMLHttpRequest, textStatus, errorThrown) {
      document.getElementById('info_bar').style.display = "";
      document.getElementById('info_bar_text_holder').innerHTML = "Message failed to send. (" + this_msg + ")";
    }
  });

}

function getMessages(){

  if(getting_messages == 0){
      getting_messages = 1;
      var loginData = {
          'id_1' : this_id_1,
          'id_2' : this_id_2,
          'last_sku' : this_last_sku
        };         

                console.log("FIRST this_last_sku : " + this_last_sku);
      $.ajax({
        type: "POST",
        url: url_load_messages,
        data: loginData,
        success: function(response){
          console.log(response);
          if(response.trim() != ''){
            var newsResponse = JSON.parse(response);
            console.log(newsResponse);
            var total_news_num = Object.keys(newsResponse["news_returned"]).length;
            if(total_news_num > 0){
              for (var i = total_news_num-1; i >= 0; i--) {   

                var sender_pottname = newsResponse["news_returned"][i]["2"];
                var receiver_pottname = newsResponse["news_returned"][i]["3"];
                var message_text = newsResponse["news_returned"][i]["4"];
                var messages_date = newsResponse["news_returned"][i]["5"];
                this_last_sku = newsResponse["news_returned"][i]["6"];
                console.log("this_last_sku : " + this_last_sku);

                if(newsResponse["news_returned"][i]["2"] == fp_pottname){
                    $('#all_messages_holder_div').append($('<div  class="card-header card-header-warning" style=" margin-left: 20%; margin-bottom: 20px; margin-top: 20px;"><span style="font-weight: 500"> ' + sender_pottname + ' ||  ' + messages_date + '</span><br><hr>' + message_text + '</div>')); 
                } else {
                    $('#all_messages_holder_div').append($('<div  class="card-header card-header-info" style=" margin-right: 20%; margin-bottom: 20px; margin-top: 20px;"><span style="font-weight: 500"> ' + sender_pottname + ' ||  ' + messages_date + '</span><br><hr>' + message_text + '</div>')); 
                }
              }
            }


          } else {
            document.getElementById('info_bar').style.display = "";
            document.getElementById('info_bar_text_holder').innerHTML = "Failed to get new messages";
          }
          getting_messages = 0;
        },

        error: function(XMLHttpRequest, textStatus, errorThrown) {
          document.getElementById('info_bar').style.display = "";
          document.getElementById('info_bar_text_holder').innerHTML = "Failed to get new messages";
          getting_messages = 0;
        }
      });
  }

}

function updateScroll(){
    var element = document.getElementById("all_messages_holder_div");
    element.scrollTop = element.scrollHeight;
}

$(document).ready(function() {
    $('#all_messages_holder_div').animate({
        scrollTop: $('#all_messages_holder_div').get(0).scrollHeight
    }, 2000);
});

setInterval(getMessages, 2000);
