<?php

    class TaskController {

        // --------------------
        // Déclaration des attributs
        // --------------------

        /** La méthode relative à la requête, i.e. GET, POST, ... */
        private $request_method;
        /** Un id d'une tache */
        private $id;
        /** L'ordre de tri d'un tableau de taches */
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
                    if ($this->id) {
                        $response = $this->get_by_id($this->id);
                    } else {
                        $response = $this->get_all();
                    }

                    break;

                case 'POST':
                    $response = $this->insert();
                    break;

                case 'PUT':
                    $response = $this->update($this->id);
                    break;

                case 'DELETE':
                    $response = $this->delete($this->id);
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
         * Méthode permettant de récupérer toutes les taches présentes dans la BdD.
         * 
         * @return (string|false)[]
         */
        private function get_all() {
            if ($this->order) {
                // On récupère l'entièreté des taches triée dans l'ordre décroissant
                $tasks = $this->task_manager->get_all($this->order);
            } else {
                // On récupère l'entièreté des taches
                $tasks = $this->task_manager->get_all();
            }

            // Si tout va bien, on notifie que tout s'est bien passé
            $response['status_code_header'] = 'HTTP/1.1 200 OK';

            // Le corps de la réponse prends le json contenant toutes les taches
            $response['body'] = json_encode($tasks);

            // On retourne la réponse donnée
            return $response;
        }

        /**
         * Méthode permettant de récupérer une tache dans la base de données à l'aide de son id
         * 
         * @param int $id L'id de la tache que l'on souhaite trouver
         * @return (string|null)[]|(string|false)[]
         */
        private function get_by_id($id) {
            // On récupère la tache ayant l'id correspondant
            $task = $this->task_manager->get($id);

            // Si aucune tache ne correspond à l'id entré, on renvoie que la requête n'a rien donné.
            if (!$task) {
                return $this->not_found_query();
            }

            // Si tout va bien, on notifie que tout s'est bien passé
            $response['status_code_header'] = 'HTTP/1.1 200 OK';

            // Le corps de la réponse prends le json contenant tous les taches
            $response['body'] = json_encode($task);

            return $response;
        }

        /** 
         * Méthode permettant d'ajouter un tache à la base de données 
         * 
         * @return (string|false)[]|(string|null)[]
         */
        private function insert() {
            // On récupère les informations entrées par l'utilisateur
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);

            // Si les informations ne sont pas valides, on le fait savoir. La requête n'est pas executable
            if (!$this->is_valid($input)) {
                return $this->not_executable_query();
            }

            // On récupère les informations valides pour l'ajout
            $id = $input["id"];
            $title = $input["title"];
            $description = $input['description'];
            $deadline = $input['deadline'];
            $is_complete = $input['is_complete'];
            $id_list = $input['id_list'];

            // On instancie une nouvelle tache
            $task = new Task($id, $title, $description, $deadline, $is_complete, $id_list);

            // On insère la tache à la BdD
            $this->task_manager->insert($task);

            // On informe que tout s'est bien passé
            $response['status_code_header'] = 'HTTP/1.1 201 Created';

            // On n'a pas besoin de stocker quoi que ce soit à l'insertion
            $response['body'] = null;

            return $response;
        }

        /**
         * Méthode permettant de mettre à jour un tache dans la base de données
         * 
         * @param int $id L'id de la tache que l'on souhaite mettre à jour
         * @return (string|null)[]|(string|false)[]
         */
        private function update($id) {
            // On récupère la tache qu'il faut mettre à jour
            $task = $this->task_manager->get($id);

            // Si on ne trouve pas de tache correspondant à l'id entré on fait savoir que la requête n'a pas été trouvée
            if (!$task) {
                return $this->not_found_query();
            }

            // On récupère ce qui à été envoyé 
            $input = (array) json_decode(file_get_contents('php://input'), TRUE);

            // Si ce qui a été entré n'est pas valide, on fait savoir que la requête n'est pas executable
            if (!$this->is_valid($input)) {
                return $this->not_executable_query();
            }

            // On récupère les informations valides pour l'ajout
            $title = $input["title"];
            $description = $input['description'];
            $deadline = $input['deadline'];
            $is_complete = $input['is_complete'];
            $id_list = $input['id_list'];

            // On instancie une nouvelle tache
            $task = new Task($id, $title, $description, $deadline, $is_complete, $id_list);

            // On met à jour la tache
            $this->task_manager->update($task);

            // On informe que tout s'est bien passé
            $response['status_code_header'] = 'HTTP/1.1 200 OK';

            // On n'a pas besoin de stocker quoi que ce soit à l'insertion
            $response['body'] = null;

            return $response;
        }

        /**
         * Méthode permettant de supprimer une tache de la base de données
         * 
         * @param int $id L'id de la tache que l'on souhaite supprimer
         * @return (string|null)[]
         */
        private function delete($id) {
            // On récupère le tache qu'il faut mettre à jour
            $task = $this->task_manager->get($id);

            // Si on ne trouve pas de tache correspondant à l'id entré on fait savoir que la requête n'a pas été trouvée
            if (!$task) {
                return $this->not_found_query();
            }

            // On supprime la tache 
            $this->task_manager->delete($task);

            // On informe que tout s'est bien passé
            $response['status_code_header'] = 'HTTP/1.1 200 OK';

            // On n'a pas besoin de stocker quoi que ce soit à l'insertion
            $response['body'] = null;

            return $response;
        }

        /**
         * Méthode permettant de vérifier qu'une tache est valide, i.e. les données fournies par l'utilisateur sont correctes.
         * 
         * @param mixed $input Les informations fournies par l'utilisateur
         * @return bool
         */
        private function is_valid($input) {
            return isset($input['id']) && isset($input['title']) && isset($input['description']) && isset($input['deadline']) && isset($input['is_complete']) && isset($input['id_list']);
        }

        /**
         * Méthode peremttant de notifier que la requête n'est pas traitable
         * 
         * @return (string|false)[]
         */
        private function not_executable_query() {

            $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
            $response['body'] = json_encode([
                'error' => 'Invalid input'
            ]);

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