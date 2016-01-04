require 'vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'http://apiwebsite',
]);

//post login data, receive token
$response = $client->request('POST', 'login',[
     'form_params' => [
        'username' => 'somename',
        'password' => 'somepassword',
    ]
])->getBody()->getContents();
$json = json_decode($response);

//use token to get movies
$response = $client->request('GET','movies',[
    'headers'=>['X-Auth'=>$json->token]
    ])->getBody()->getContents();
