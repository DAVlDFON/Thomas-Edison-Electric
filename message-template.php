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


try {

    $result = $client->templates->getList( array(
                'page' => $page,
                'limit' => $limit,
                'shared' => true));

    $templates = $result['resources'];
    echo 'Template List: <br>';
    foreach ($templates as $template) {
        echo "{$template['id']}. {$template['name']} , {$template['content']} </br>";
    }


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