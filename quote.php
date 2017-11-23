<?php 

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}


require_once('Services/TextmagicRestClient.php');

use Textmagic\Services\TextmagicRestClient;
use Textmagic\Services\RestException;
use Textmagic\Services\HttpClient\HttpCurl;

$client = new TextmagicRestClient('michaellanctot1', 'Nt4LHXtfD7goMB0vDDsLD0DiKnv8Oh');
$result = ' ';
$resultContacts = ' ';

try {

    $result = $client->templates->get($_GET['messagetemplateId']);
    $teamMsg = $client->templates->get($_GET['teamMessagetemplateId']);

    $content = $result['content'];
    $content = str_replace("{First name}",$_GET['firstname'],$content);
    $content = str_replace("{Last name}",$_GET['lastname'],$content);
    $content = str_replace("{Email}",$_GET['email'],$content);
    $content = str_replace("{Mobile Phone}",$_GET['phone'],$content);
    $content = str_replace("{Address}",$_GET['address'],$content);
    $content = str_replace("{Service Description}",$_GET['situitaion'],$content);

    $teamMsg = $teamMsg['content'];
    $teamMsg = str_replace("{First name}",$_GET['firstname'],$teamMsg);
    $teamMsg = str_replace("{Last name}",$_GET['lastname'],$teamMsg);
    $teamMsg = str_replace("{Email}",$_GET['email'],$teamMsg);
    $teamMsg = str_replace("{Mobile Phone}",$_GET['phone'],$teamMsg);
    $teamMsg = str_replace("{Address}",$_GET['address'],$teamMsg);
    $teamMsg = str_replace("{Service Description}",$_GET['situitaion'],$teamMsg);

    $client->messages->create(array(
        'text' => $content,
        'phones' => implode(', ', array('1'.$_GET['phone']))
    ));
    $resultContacts = $client->lists->getContacts($_GET['contactlistId']);

    $contacts = $resultContacts['resources'];

    foreach ($contacts as $contact) {
        $phone = (string)$contact['phone'];

        $client->messages->create(
            array(
                'text' => $teamMsg,
                'phones' => implode(', ', array($phone))
            )
        );
    }

    echo json_encode(array(
        'status' => 1,
        'message' => $content
    ));

}catch (\Exception $e) {
    if ($e instanceof RestException) {
        print '[ERROR] ' . $e->getMessage() . "\n";
        foreach ($e->getErrors() as $key => $value) {
            print '[' . $key . '] ' . implode(',', $value) . "\n";
        }
    } else {
        print '[ERROR] ' . $e->getMessage() . "\n";
    }
    return;
}


