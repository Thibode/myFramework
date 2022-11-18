<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


require __DIR__.'/../vendor/autoload.php';

$request = Request::createFromGlobals();

$framework = new Framework\Simplex;

$response = $framework->handle($request);

$response->send();