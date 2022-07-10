<?php

        class ListeController {

                // --------------------
                // Déclaration des attributs
                // --------------------

                private $db;
                private $request_method;
                private $id;
                private $order;
                private $list_manager;

                // --------------------
                // Constructeur
                // --------------------

                public function __construct($db, $request_method, $id, $order) {
                        $this->db = $db;
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
                 * Méthode permettant de récupérer toutes les listes présentes dans la BdD.
                 * 
                 * @param order L'ordre dans lequel on souhaite trier nos listes. Par défaut, on les ordonne du plus petit au plus grand.
                 */
                private function get_all() {
                        if ($this->order) {
                                // On récupère l'entièreté des listes triée dans l'ordre décroissant
                                $lists = $this->list_manager->get_all($this->order);
                        } else {
                                // On récupère l'entièreté des listes
                                $lists = $this->list_manager->get_all();
                        }

                        // Si tout va bien, on notifie que tout s'est bien passé
                        $response['status_code_header'] = 'HTTP/1.1 200 OK';

                        // Le corps de la réponse prends le json contenant toutes les listes
                        $response['body'] = json_encode($lists);

                        // On retourne la réponse donnée
                        return $response;
                }

                 /**
                 * Méthode permettant de récupérer une liste dans la base de données à l'aide de son id
                 * 
                 * @param id L'id de la liste que l'on souhaite trouver
                 */ 
                private function get_by_id($id) {
                        // On récupère la liste ayant l'id correspondant
                        $list = $this->list_manager->get($id);

                        // Si aucune liste ne correspond à l'id entré, on renvoie que la requête n'a rien donné.
                        if (!$list) {
                                return $this->not_found_query();
                        }

                        // Si tout va bien, on notifie que tout s'est bien passé
                        $response['status_code_header'] = 'HTTP/1.1 200 OK';

                        // Le corps de la réponse prends le json contenant tous les listes
                        $response['body'] = json_encode($list);

                        return $response;
                }

                /** 
                 * Méthode permettant d'ajouter un liste à la base de données 
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
                        $label = $input["label"];
                        $id_group = $input['id_group'];

                        // On instancie une nouvelle liste
                        $list = new Liste($id, $label, $id_group);

                        // On insère la liste à la BdD
                        $this->list_manager->insert($list);

                        // On informe que tout s'est bien passé
                        $response['status_code_header'] = 'HTTP/1.1 201 Created';

                        // On n'a pas besoin de stocker quoi que ce soit à l'insertion
                        $response['body'] = null;

                        return $response;
                }

                /**
                 * Méthode permettant de mettre à jour un liste dans la base de données
                 * 
                 * @param id L'id de la liste que l'on souhaite mettre à jour
                 */ 
                private function update($id) {
                        // On récupère la liste qu'il faut mettre à jour
                        $list = $this->list_manager->get($id);

                        // Si on ne trouve pas de liste correspondant à l'id entré on fait savoir que la requête n'a pas été trouvée
                        if (! $list) {
                                return $this->not_found_query();
                        }

                        // On récupère ce qui à été envoyé 
                        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

                        // Si ce qui a été entré n'est pas valide, on fait savoir que la requête n'est pas executable
                        if (!$this->is_valid($input)) {
                                return $this->not_executable_query();
                        }

                         // On récupère les informations valides pour l'ajout
                        $label = $input["label"];
                        $id_group = $input['id_group'];

                        // On instancie une nouvelle liste
                        $list = new Liste($id, $label, $id_group);

                        // On met à jour la liste
                        $this->list_manager->update($list);

                        // On informe que tout s'est bien passé
                        $response['status_code_header'] = 'HTTP/1.1 200 OK';

                        // On n'a pas besoin de stocker quoi que ce soit à l'insertion
                        $response['body'] = null;

                        return $response;
                }

                /**
                 * Méthode permettant de supprimer une liste de la base de données
                 * 
                 * @param id L'id de la liste que l'on souhaite supprimer
                 */
                private function delete($id) {
                        // On récupère le liste qu'il faut mettre à jour
                        $list = $this->list_manager->get($id);

                        // Si on ne trouve pas de liste correspondant à l'id entré on fait savoir que la requête n'a pas été trouvée
                        if (!$list) {
                                return $this->not_found_query();
                        }

                        // On supprime la liste 
                        $this->list_manager->delete($list);

                        // On informe que tout s'est bien passé
                        $response['status_code_header'] = 'HTTP/1.1 200 OK';

                        // On n'a pas besoin de stocker quoi que ce soit à l'insertion
                        $response['body'] = null;

                        return $response;

                }

                /**
                 * Méthode permettant de vérifier qu'une liste est valide, i.e. les données fournies par l'utilisateur sont correctes.
                 * 
                 * @param input Les informations fournies par l'utilisateur
                 */
                private function is_valid($input) {
                        return isset($input['id']) && isset($input['label']) && isset($input['id_group']);
                }

                /**
                 * Méthode peremttant de notifier que la requête n'est pas traitable
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
                 */
                private function not_found_query() {

                        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                        $response['body'] = null;

                        return $response;
                }
        }

?>