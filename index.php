<?
define('TOKEN', getenv('TOKEN'));
define('CHANNEL', getenv('CHANNEL'));


 // Grab event data from the request
//$input = $_POST['body'];

$input = '{"type": "url_verification","token": "sadasdasdasd","challenge": "dasdasdasdasad"}';
$json = json_decode($input, FALSE);
$jsontype = $json->{type};

echo $input;


switch ($jsontype) {

  case 'url_verification':

    $challenge = isset($json->challenge) ? $json->challenge : null;
    $response = array(
      'challenge' => $challenge,
    );
    header('Content-type: application/json');
    print $response;


  break;

  case 'event_callback':

    switch ($json->event->type) {

      case 'status_change':

        // Grab some data about the user;
        $userid = $json->event->user->id;
        $username = $json->event->user->real_name_normalized;
        $status_text = $json->event->user->profile->status_text;
        $status_emoji = $json->event->user->profile->status_emoji;


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

