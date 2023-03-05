<?php
declare(strict_types=1);
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
 
require __DIR__ . '/../vendor/autoload.php';
 
 // SeaTable PHP-API
class Dtable
{
    private $url = 'https://cloud.seatable.io';
    private $base_api_token = 'f8ce352bda9a3fd93dd4cd74257a63e4ec4398a5';
 
    public function connect()
    {
        return new SeaTable\SeaTableApi\SeaTableApi([
            'url'               => $this->url,
            'base_api_token'    => $this->base_api_token,
        ]);
    }
}

// Create App
$app = AppFactory::create();
 
// Create Twig
$twig = Twig::create('../templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));
 
// Routing and error middlewares
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);
 
// details page

$app->get('/r/{rID}', function (Request $request, Response $response, $args) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'details.html', []);
});

// home page
$app->get("/", function (Request $request, Response $response) {

    // connect to seatable and get all entries from table Requests
    // pass the variables to the twig template
    $seatable = new DTable();
    $conn = $seatable->connect();
    $sql = "select * from Requests ORDER BY `Creation` DESC";
    $items = $conn->querySql($sql, true);
    $view = Twig::fromRequest($request);
    return $view->render($response, 'index.html', ['items' => $items->results]);
});


// Run app
$app->run();