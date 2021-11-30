<?
define('TOKEN', getenv('TOKEN'));
define('CHANNEL', getenv('CHANNEL'));

$input = file_get_contents('php://input');
$json = json_decode($input, false);
$jsontype = $json->type;

$file = 'test.txt';
file_put_contents($file, $json);

switch ($jsontype) {

  //APP EVENT VALIDATION
  case 'url_verification':

    $challenge = isset($json->challenge) ? $json->challenge : null;


    $response = array(
      'challenge' => $challenge,
    );

    header('Content-type: application/json');
    return json_encode($response);

  break;

  //EVENT CALLBACK from Event API
  case 'event_callback':

    switch ($json->event->type) {

      case 'user_change':

        // Grab some data about the user;
        $userid = $json->event->user;

        $getuserURL = "https://slack.com/api/users.profile.get?user=";
        $getuserprofile = $getuserURL.''.$userid;
        $headers = array(
           "Accept: application/json",
           "Authorization: Bearer " .TOKEN
        );


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $getuserprofile);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  
        $resp = curl_exec($curl);
        curl_close($curl);

        $userjson = json_decode($resp, false);
        $username = $userjson->profile->real_name_normalized;
        $status_text = $userjson->profile->status_text;
        $status_emoji = $userjson->profile->status_emoji;

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

        $attachments = [
          $message,
        ];

        postMessage($attachments);
      break;

    }

}


//HANDLES POSTING THE STATUS CHANGE INTO CHANNEL
function postMessage($attachments) {

    // Make a cURL call

    $args=urlencode(json_encode($attachments)); 

    // Build the full URL call to the API.
    $callurl = "https://slack.com/api/chat.postMessage?channel=" .CHANNEL. "&attachments=" .$args;

    $headers = array(
      "Accept: application/json",
      "Authorization: Bearer " .TOKEN
    );

    // Let's build a cURL query.
  	$ch = curl_init($callurl);

  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
  
    $ch_response = json_decode(curl_exec($ch));
    //closed CURL call
    curl_close($ch);
 



 }

