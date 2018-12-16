<?php

$router = $di->getRouter();

// Define your routes here
$router->add('/admin/login', ['controller' => 'admin', 'action' => 'login']);
$router->add('/admin/login/submit', ['controller' => 'admin', 'action' => 'loginSubmit']);
$router->add('/admin/logout', ['controller' => 'admin', 'action' => 'logout']);


$router->add('/news',['controller' => 'news', 'action' => 'index']);
$router->add('/news/archive',['controller' => 'news', 'action' => 'archive']);
$router->add('/news/detail',['controller' => 'news', 'action' => 'detail']);
$router->add('/news/create', ['controller' => 'news', 'action' => 'create']);
$router->add('/news/create/submit', ['controller' => 'news', 'action' => 'createSubmit']);
$router->add('/news/manage',['controller' => 'news', 'action' => 'manage']);
$router->add('/news/edit',['controller' => 'news', 'action' => 'edit']);
$router->add('/news/edit/submit',['controller' => 'news', 'action' => 'editSubmit']);
$router->add('/news/delete',['controller' => 'news', 'action' => 'delete']);



$router->handle();
