<?php
namespace Framework;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Simplex
{
    public function handle(Request $request)
    {
        $response = new Response();
        $routes = require __DIR__.'/../src/routes.php';

        $context = new RequestContext();
        $context->fromRequest($request);

        $urlMatcher = new UrlMatcher($routes, $context);

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver(); 

        try {
            $request->attributes->add($urlMatcher->match($request->getPathInfo()));

            $controller = $controllerResolver->getController($request);
            //[$instance, 'nomDuneMethode']

            $arguments = $argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        }catch(ResourceNotFoundException $e){
            $response = new Response('La page demandée n\'existe pas', 404);
        }catch(Exception $e){
            $response = new Response('Une erreur est survenue', 500);
        }

        return $response;
    }
}