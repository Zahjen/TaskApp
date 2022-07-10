<?php

    require 'model/object/list.php';
    require 'model/connection/connection.php';
    require 'model/manager/list-manager.php';
    require 'tools/utils.php';
    require 'controller/list-controller.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $db = Database::get_instance();
    $connection = $db->get_connection();

    // On parse l'url pour recupèrer chacun des membres de l'url. 
    // Par exemple ici l'url qui nous permettra de procéder aux requêtes sur les réponses sera donné par : 
    // http://http://127.0.0.1/dashboard/Fintech/api/answer.php
    // Il vient que url[4] = answer.php
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode('/', $uri);

    // Si on suit le raisonnement nous permettant de récupérer les élèments de l'url, si on a un nombre pour $uri[5], on le stock dans une variable
    $id = null;
    // Détermine l'order à appliquer à l'ensemble des groupes, listes, ou taches
    $order = null;
    // Détermine le controller à utiliser
    $directive_controller = null;

    function directive($uri, $id, $order) {
        if (isset($uri)) {
            if (is_numeric($uri)) {
                $id = (int) $uri;
            } else if ($uri === 'desc') {
                $order = strtoupper($uri);
            } else {
                die('Erreur : Adresse incorrecte');
            }
        }

        return [$id, $order];
    }

    if (isset($uri[2])) {
        switch ($uri[2]) {
            case "groups":
                $res = directive($uri[3], $id, $order);
                $id = $res[0];
                $order = $res[1];

                // On récupère le type de requête envoyée, i.e. GET, POST, PUT, DELETE
                $request_method = $_SERVER["REQUEST_METHOD"];

                // En instanciant la classe GroupController, et selon la méthode, on executera la requête demandée
                $directive_controller = new GroupController($connection, $request_method, $id, $order);

                break;

            case "lists":
                $res = directive($uri[3], $id, $order);
                $id = $res[0];
                $order = $res[1];

                // On récupère le type de requête envoyée, i.e. GET, POST, PUT, DELETE
                $request_method = $_SERVER["REQUEST_METHOD"];

                // En instanciant la classe ListeController, et selon la méthode, on executera la requête demandée
                $directive_controller = new ListeController($connection, $request_method, $id, $order);

                break;

            case "listsOfGroup":
                $res = directive($uri[3], $id, $order);
                $id = $res[0];
                $order = $res[1];

                break;

            case "tasks":
                $res = directive($uri[3], $id, $order);
                $id = $res[0];
                $order = $res[1];

                break;

            case "tasksOfList":
                $res = directive($uri[3], $id, $order);
                $id = $res[0];
                $order = $res[1];

                break;

            default:
                die('Erreur : Adresse incorrecte');
        }
    }

    $directive_controller->execute_query();

?>