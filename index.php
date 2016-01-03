<?php



require 'vendor/autoload.php';

require 'model/db.php';

require 'model/User.php';

require 'model/Table.php';

require 'model/Movie.php';



$site_path = realpath(dirname(__FILE__));

define ('__SITE_PATH', $site_path);



$configuration = [

    'settings' => [

        'displayErrorDetails' => true,

    ],

];

$c = new \Slim\Container($configuration);

$app = new \Slim\App($c);



$auth_mw = function ($request, $response, $next) {



    if ($request->hasHeader('X-Auth')) 

    {        

        $token = $request->getHeader('X-Auth')[0];



        $user = new User(db::getInstance());

        $result = $user->isValidToken($token);

        if($result === true)

        {   

            $response = $next($request, $response);

            return $response;

        }  

    }

    return $response->withStatus(401);

};



$app->get('/', function ($request, $response, $args) {

    $response->write("Welcome to Slim!");

    return $response;

});



$app->get('/register', function ($request, $response, $args) {



    render('views/registration.php');

    return $response;

    

});



$app->post('/registration', function ($request, $response, $args) {



    $data = $request->getParsedBody();

    $user = new User(db::getInstance());

    $result = $user->addUser($data['username'], $data['password'], $data['email']);

    if($result) $response->write("Registration successful");

    else $response->write("Error: wrong data format");



    return $response;

    

});



$app->post('/login', function ($request, $response, $args) {



    $data = $request->getParsedBody();

    $user = new User(db::getInstance());    

    $result = $user->checkUser($data['username'], $data['password']);

    if($result === false)

    {

        $response = $response->withStatus(404);

        echo json_encode( array('error'=> array('message' => 'Wrong login information.' ) ) );   

    }

    else

    {  

        echo json_encode( array( 'token'=> $result ) );   

    }

    return $response;

    

});





$app->get('/movies', function ($request, $response, $args) {

    $movie = new Movie(db::getInstance());

    $result = $movie->getAllMovies();



    if($result === false)

    {

        $response = $response->withStatus(404);

        echo json_encode( array('error'=> array('message' => 'No records found.' ) ) );

    }

    else

    {

        echo json_encode($result);

    }

    return $response;

})->add($auth_mw);


$app->get('/catmail/{email}', function ($request, $response, $args) {
    
    $recipient = $args['email'];
    //Unfortunately, mail transport is used instead of smtp, since smtp is unavailable for free on my server
    $transport =  Swift_MailTransport::newInstance();

    $mailer = Swift_Mailer::newInstance($transport);
    $message = Swift_Message::newInstance();
        $message->setSubject('Your subject')
        ->setFrom(array('catmail@a-melnichuk-projects.hol.es' => 'Cat Mail'))
        ->setTo(array($recipient))
        ->setBody(
            '<html>' .
              '<head></head>' .
              '<body>' .
             'Here is an image of cute kitty: <img src="' . $message->embed(Swift_Image::fromPath('http://a-melnichuk-projects.hol.es/res/cutecat.jpg', 'image/jpeg')) .'" alt="Cat Pic" />' .
              'It\'s adorable!' .
              '</body>' .
            '</html>',
            'text/html')
        ->attach( Swift_Attachment::fromPath('http://a-melnichuk-projects.hol.es/res/cutecat.jpg', 'image/jpeg'));
        $result = $mailer->send($message);
    echo $result;
    return $response;
});


function render($view ,$args = array() )

{

    extract($args);

    include($view);

}





$app->run();

