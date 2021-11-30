<?
define('TOKEN', getenv('TOKEN'));
define('CHANNEL', getenv('CHANNEL'));


// Removed 301 redirect
// Causing Event Subscription setup to fail 
// without HHTP POST payload response
// http_response_code(301);

// Grab event data from the request


//Replaced $_POST['body'] as JSON was not getting received and parsed
//$input = $_POST['body'];
$input = file_get_contents('php://input');
$json = json_decode($input, false);
$jsontype = $json->type;

//var_dump($input);
//var_dump($json);
//var_dump($jsontype);

switch ($jsontype) {

  case 'url_verification':

    $challenge = isset($json->challenge) ? $json->challenge : null;

    // Array to string conversion warning on logs
    $response = array(
      'challenge' => $challenge,
    );

    header('Content-type: application/json');

    //Replaced print to return as it was not fulfilling the expected response
    //Change $response variable to json_encode($response) to send through expected payload 
    return json_encode($response);

  break;

  case 'event_callback':

//echo("it's in here!");
//Tested eventtype return value
//$eventtype = $json->event->type;
//echo($eventtype);

    switch ($json->event->type) {

      //update from status_change to user_change
      case 'user_change':

        // Grab some data about the user;
        // Removed suffixed ID as payload format only has user object
        $userid = $json->event->user;

        print_r($userid);
        //var_dump($userid);
        $getuserURL = "https://slack.com/api/users.profile.get?user=";
        
        //confirmed userid has correct value
        //echo $userid;
        //echo $getuserURL;

        $getuserprofile = $getuserURL.''.$userid;
        $headers = array(
           "Content-Type : application/json; charset=utf-8",
           "Accept: application/json",
           "Authorization: Bearer " .TOKEN
        );

        //confirmed getuserprofile has correct value
        //echo $getuserprofile;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $getuserprofile);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  
        $resp = curl_exec($curl);
        curl_close($curl);

        $userjson = json_decode($resp, false);
        var_dump($userjson);

        //$username = $json->event->user->real_name_normalized;
        $username = $userjson->profile->real_name_normalized;
        $status_text = $userjson->profile->status_text;
        $status_emoji = $userjson->profile->status_emoji;

        //confirmed values received by variables
        //var_dump($status_text);
        //var_dump($username);
        //echo($status_emoji);


        // Build the message payload
        // If their status contains some text
        if (isset($status_text) && strlen($status_text) == 0) {

          $message = [
            'text' => $username . " cleared their status."
          ];
        } 
        else {

          $message = [
            "pretext" => $username . " updated their status:",
            "text" => $status_emoji . " *" . $status_text
          ];
        }

        // send the message!

        $attachments = [
          $message,
        ];

        $payload = [
          //removed token. will be passed via CURL
          //'token' => TOKEN,
          'channel' => CHANNEL,
          'attachments' => $attachments,
        ];


        postMessage($payload);
      break;

    }

}



function postMessage($payload) {

    // Make a cURL call

    // add our payload passed through the function.
    $args = http_build_query($payload);

    // Build the full URL call to the API.
    $callurl = "https://slack.com/api/chat.postMessage" . "?" . $args;

    //echo $callurl;
    
    // Change CURL headers
    // Token passed from set global environment
    //$headers = array("Content-Type: multipart/form-data"); // cURL headers for file uploading
    $headers = array(
      "Content-Type : application/json; charset=utf-8",
      "Accept: application/json",
      "Authorization: Bearer " .TOKEN
    );

    // Let's build a cURL query.
  	$ch = curl_init($callurl);
  	//curl_setopt($ch, CURLOPT_USERAGENT, "Slack Technical Exercise");
    //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);

    //We are running the arguments inside the cURL URL, no need to POST payload
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  	//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    //removed if statement as this filename key isnt part of the postMessage payload, so will not be met
    //curl arguments inside it wont run
    //if (array_key_exists("filename", $payload)) {
      //$callurl = $url . $method;
    //}
    
    $ch_response = json_decode(curl_exec($ch));

    if ($ch_response->ok == FALSE) {
      error_log($ch_response->error);
    }

    //closed CURL call
    curl_close($ch);
    var_dump($ch_response);




 }

