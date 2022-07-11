<?php

    class ListOfGroupController {

        // --------------------
        // Déclaration des attributs
        // --------------------

        /** La méthode relative à la requête, i.e. GET, POST, ... */
        private $request_method;
        /** Un id de groupe */
        private $id;
        /** L'ordre de tri d'un tableau de liste */
        private $order;
        /** Une instance de la classe ListeManager */
        private $list_manager;

        // --------------------
        // Constructeur
        // --------------------

        public function __construct($db, $request_method, $id, $order) {
            $this->request_method = $request_method;
            $this->id = $id;
            $this->order = $order;
            $this->list_manager = new ListeManager($db);
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
                    $response = $this->get_all_by_id_group($this->id);
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
         * Méthode permettant de récupérer toutes les listes d'un même groupe dans la base de données à l'aide de son id
         * 
         * @param int $id L'id du groupe pour lequel on souhaite récupérer ses listes
         * @return (string|null)[]|(string|false)[]
         */
        private function get_all_by_id_group($id) {
            if ($this->order) {
                // On récupère l'entièreté des listes triée dans l'ordre décroissant
                $lists = $this->list_manager->get_all_by_id_group($id, $this->order);
            } else {
                // On récupère l'entièreté des listes
                $lists = $this->list_manager->get_all_by_id_group($id);
            }

            // Si aucune liste ne correspond à l'id entré, on renvoie que la requête n'a rien donné.
            if (!$lists) {
                return $this->not_found_query();
            }

            // Si tout va bien, on notifie que tout s'est bien passé
            $response['status_code_header'] = 'HTTP/1.1 200 OK';

            // Le corps de la réponse prends le json contenant tous les listes
            $response['body'] = json_encode($lists);

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