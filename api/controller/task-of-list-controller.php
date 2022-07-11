<?php

    class TaskOfListController {

        // --------------------
        // Déclaration des attributs
        // --------------------

        /** La méthode relative à la requête, i.e. GET, POST, ... */
        private $request_method;
        /** Un id de liste */
        private $id;
        /** L'ordre de tri d'un tableau de liste */
        private $order;
        /** Une instance de la classe TaskManager */
        private $task_manager;

        // --------------------
        // Constructeur
        // --------------------

        public function __construct($db, $request_method, $id, $order) {
            $this->request_method = $request_method;
            $this->id = $id;
            $this->order = $order;
            $this->task_manager = new TaskManager($db);
        }

        // --------------------
        // Méthode
        // --------------------

        /**
         * Méthode permettant d'exécuter une requête selon la méthode envoyé, i.e. GET, POST, ...
         */
        public function execute_query() {
            switch ($this->request_method) {
                case 'GET':
                    $response = $this->get_all_by_id_list($this->id);
                    break;

                default:
                    $response = $this->not_found_query();
                    break;
            }

            header($response['status_code_header']);

            if ($response['body']) {
                echo $response['body'];
            }
        }

        /**
         * Méthode permettant de récupérer toutes les taches d'uns même liste dans la base de données à l'aide de son id
         * 
         * @param int $id L'id de liste pour laquelle on souhaite récupérer ses taches
         * @return (string|null)[]|(string|false)[]
         */
        private function get_all_by_id_list($id) {
            if ($this->order) {
                // On récupère l'entièreté des taches triée dans l'ordre décroissant
                $tasks = $this->task_manager->get_all_by_id_list($id, $this->order);
            } else {
                // On récupère l'entièreté des taches
                $tasks = $this->task_manager->get_all_by_id_list($id);
            }

            // Si aucune tache ne correspond à l'id entré, on renvoie que la requête n'a rien donné.
            if (!$tasks) {
                return $this->not_found_query();
            }

            // Si tout va bien, on notifie que tout s'est bien passé
            $response['status_code_header'] = 'HTTP/1.1 200 OK';

            // Le corps de la réponse prends le json contenant tous les listes
            $response['body'] = json_encode($tasks);

            return $response;
        }

        /**
         * Méthode permettant de notifier que la requête n'a pas été trouvée.
         * 
         * @return (string|null)[]
         */
        private function not_found_query() {

            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = null;

            return $response;
        }
    }

?>