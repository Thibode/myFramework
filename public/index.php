<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;


require __DIR__.'/../vendor/autoload.php';

$request = Request::createFromGlobals();
$response = new Response();
$routes = require __DIR__.'/../src/routes.php';

$context = new RequestContext();
$context->fromRequest($request);

$urlMatcher = new UrlMatcher($routes, $context);

try {
    $resultat = ($urlMatcher->match($request->getPathInfo()));
    $request->attributes->add($resultat);

    //classe a instancier
    $className = substr($resultat['_controller'], 
    0, 
    strpos($resultat['_controller'], '@'));
    
    //nom de la methode
    $methodName = substr($resultat['_controller'], 
    strpos($resultat['_controller'], '@') + 1);

    // Voila un callable 
    $controller = [new $className, $methodName];

    $response = call_user_func($controller, $request);
}catch(ResourceNotFoundException $e){
    $response = new Response('La page demandée n\'existe pas', 404);
}catch(Exception $e){
    $response = new Response('Une erreur est survenue', 500);
}

$response->send();