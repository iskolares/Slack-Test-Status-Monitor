<?
define('TOKEN', getenv('TOKEN'));
define('CHANNEL', getenv('CHANNEL'));


// Removed 301 redirect
// Causing Event Subscription setup to fail 
// without HHTP POST payload response
// http_response_code(301);

// Grab event data from the request


//JSON was not getting received and parsed

//$input = $_POST['body'];
$input = file_get_contents('php://input');
var_dump($input); 
$json = json_decode($input, false);
var_dump($json); // prints array


$jsontype = $json->type;
var_dump($jsontype);


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

    switch ($json->event->type) {

      //update from status_change to user_change
      case 'user_change':

        // Grab some data about the user;
        $userid = $json->event->user->id;

        $getuserprofile = "https://slack.com/api/users.profile.get?user=".$userid;
        $curl = curl_init($getuserprofile);
        curl_setopt($curl, CURLOPT_URL, $getuserprofile);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
           "Accept: application/json",
           "Authorization: Bearer TOKEN",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  
        $resp = curl_exec($curl);
        curl_close($curl);
        var_dump($resp);

        $userjson = json_decode($resp, false);


        //$username = $json->event->user->real_name_normalized;
        $username = $userjson->profile->real_name_normalized;
        $status_text = $userjson->profile->status_text;
        $status_emoji = $userjson->profile->status_emoji;


        // Build the message payload
        // If their status contains some text
        if (isset($status_text) && strlen($status_text) == 0) {
          $message = [
            'text' => $username . " cleared their status.",
          ];
        } else {
          $message = [
            "pretext" => $username . " updated their status:",
            "text" => $status_emoji . " *" . $status_text,
          ];
        }

        // send the message!

        $attachments = [
          $message,
        ];

        $payload = [
          'token' => TOKEN,
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

    // Let's build a cURL query.
  	$ch = curl_init($callurl);
  	curl_setopt($ch, CURLOPT_USERAGENT, "Slack Technical Exercise");
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    if (array_key_exists("filename", $payload)) {
      $callurl = $url . $method;
      $headers = array("Content-Type: multipart/form-data"); // cURL headers for file uploading
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }

    $ch_response = json_decode(curl_exec($ch));
    if ($ch_response->ok == FALSE) {
      error_log($ch_response->error);
    }
 }

