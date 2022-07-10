<?php

        class TaskManager {

                // -----------------------
                // Déclaration des attributs
                // -----------------------

                /** Connexion à la base de données */
                private $_db;
                /** Lien avec le fichier Utils */
                private $_utils;

                // -----------------------
                // Constructeur
                // -----------------------
                
                public function __construct($_db) {
                        $this->set_db($_db);
                        $this->_utils = Utils::get_instance();
                }

                // -----------------------
                // Setter
                // -----------------------

                /** 
                 * Méthode permettant de set le lien avec la base de données 
                 * 
                 * @param db Lien avec la BdD
                 */ 
                public function set_db(PDO $db) {
                        $this->_db = $db;
                }

                // -----------------------
                // Méthodes
                // -----------------------
                
                /** 
                 * Méthode permettant d'ajouter une tache à la base de données 
                 * 
                 * @param task Tache que l'on souhaite insérer
                 */ 
                public function insert(Task $task) {
                        // Si la tache n'existe pas déjà...
                        if (!$this->exists($task)) {
                                try {  
                                        // On génère la requête permettant d'ajouter un tache à la BdD  
                                        $requete = $this->_db->prepare('INSERT INTO task (title, description, deadline, is_complete, id_list) VALUES (:title, :description, :deadline, :is_complete, :id_list)');
                                        
                                        // On lie les différentes valeurs de manière sécurisée à la requête 
                                        $requete->bindValue(':title', $task->get_title(), PDO::PARAM_STR);
                                        $requete->bindValue(':description', $task->get_description(), PDO::PARAM_STR);
                                        $requete->bindValue(':deadline', $task->get_deadline(), PDO::PARAM_STR);
                                        $requete->bindValue(':is_complete', $task->is_complete(), PDO::PARAM_BOOL);
                                        $requete->bindValue(':id_list', $task->get_id_list(), PDO::PARAM_INT);

                                        // On execute la requête
                                        $requete->execute();
                                } catch (Exception $erreur) {
                                        // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                                        die('Erreur : '.$erreur->getMessage());
                                }
                        } else {
                                // Si la tache existe déjà, on lève une exception pour en informer l'utilisateur
                                // throw new Exception("Cette tache existe déjà.");
                                die('Cette tache existe déjà.');
                        }
                }

                /**
                 * Méthode permettant de mettre à jour une tache dans la base de données
                 * 
                 * @param task La tache que l'on souhaite mettre à jour
                 */ 
                public function update(Task $task) {
                        try {  
                                // On génère la requête permettant de mettre à jour une tache de la BdD   
                                $requete = $this->_db->prepare('UPDATE task SET title = :title, description = :description, deadline = :deadline, is_complete = :is_complete, id_list = :id_list  WHERE id = :id');

                                // On lie les différentes valeurs de manière sécurisée à la requête 
                                $requete->bindValue(':id', $task->get_id(), PDO::PARAM_INT);
                                $requete->bindValue(':title', $task->get_title(), PDO::PARAM_STR);
                                $requete->bindValue(':description', $task->get_description(), PDO::PARAM_STR);
                                $requete->bindValue(':deadline', $task->get_deadline(), PDO::PARAM_STR);
                                $requete->bindValue(':is_complete', $task->is_complete(), PDO::PARAM_BOOL);
                                $requete->bindValue(':id_list', $task->get_id_list(), PDO::PARAM_INT);

                                // On execute la requête
                                $requete->execute();
                        } catch (Exception $erreur) {
                                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                                die('Erreur : '.$erreur->getMessage());
                        }
                }

                /**
                 * Méthode permettant de supprimer une tache de la base de données
                 * 
                 * @param task Le tache que l'on souhaite supprimer
                 */
                public function delete(Task $task) {
                        try {  
                                // On génère la requête permettant de supprimer une tache de la BdD
                                $requete = $this->_db->prepare('DELETE FROM task WHERE id = :id');
                                
                                // On lie les différentes valeurs de manière sécurisée à la requête 
                                $requete->bindValue(':id', $task->get_id(), PDO::PARAM_INT);

                                // On execute la requête
                                $requete->execute();
                        } catch (Exception $erreur) {
                                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                                die('Erreur : '.$erreur->getMessage());
                        }
                }

                /**
                 * Méthode permettant de récupérer une tache dans la base de données à l'aide de son id
                 * 
                 * @param id L'id du tache que l'on souhaite trouver
                 */ 
                public function get($id) {
                        // On commence par affecter une valeur null à la tache.
                        // On fait ça dans le cas où aucune tache ne correspond à l'id entré.
                        $task = null;

                        try {   
                                // On génère la requête permettant de trouver une tache de la BdD grace à son id
                                $requete = $this->_db->prepare('SELECT id, title, description, deadline, is_complete, id_list FROM task WHERE id = :id');

                                // On lie les différentes valeurs de manière sécurisée à la requête 
                                $requete->bindValue(':id', $id, PDO::PARAM_INT);

                                // On execute la requête
                                $requete->execute();

                                // On place les valeurs récupérer dans la BdD dans une variable 
                                $datas = $requete->fetch(PDO::FETCH_ASSOC);

                                // On stock dans des variables les données obtenues provenant des différentes colonnes
                                $id = $datas["id"];
                                $title = $datas["title"];
                                $description = $datas["description"];
                                $deadline = $datas["deadline"];
                                $is_complete = $datas["is_complete"];
                                $id_list = $datas["id_list"];

                                // On instancie un nouveau tache
                                $task = new Task($id, $title, $description, $deadline, $is_complete, $id_list);
                        } catch (Exception $erreur) {
                                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                                die('Erreur : '.$erreur->getMessage());
                        }

                        return $task;
                }

                /**
                 * Méthode permettant de récupérer toutes les taches présentes dans la BdD.
                 * 
                 * @param order L'ordre dans lequel on souhaite trier nos taches. Par défaut, on les ordonne du plus petit au plus grand.
                 */
                public function get_all($order = "ASC") {
                        // On commence par déclarer un tableau de taches. Il contiendra toutes les taches données par la requête
                        $tasks = [];

                        try {
                                // On récupère tous les taches présents dans la BdD
                                $requete = $this->_db->query('SELECT id, title, description, deadline, is_complete, id_list FROM task ORDER BY id '.$order);

                                // Tant qu'il y des données dans le tableau $datas, on créé une nouvelle instance de la classe Task, que l'on insère ensuite dans le tableau
                                while ($datas = $requete->fetch(PDO::FETCH_ASSOC)) {
                                        // On stocke l'id et le title de la ligne courante
                                        $id = $datas["id"];
                                        $title = $datas["title"];
                                        $description = $datas["description"];
                                        $deadline = $datas["deadline"];
                                        $is_complete = $datas["is_complete"];
                                        $id_list = $datas["id_list"];

                                        // On instancie un nouveau tache
                                        $task = new Task($id, $title, $description, $deadline, $is_complete, $id_list);

                                        // On stocke le nouveau tache dans le tableau précédemment déclaré
                                        $tasks[] = $task;
                                }
                        } catch (Exception $erreur) {
                                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                                die('Erreur : '.$erreur->getMessage());
                        }

                        return $tasks;
                }

                /**
                 * Méthode permettant de récupérer toutes les taches d'une liste présentes dans la BdD.
                 * 
                 * @param order L'ordre dans lequel on souhaite trier nos taches. Par défaut, on les ordonne du plus petit au plus grand.
                 */
                public function get_all_by_id_list($id_list, $order = "ASC") {
                        // On commence par déclarer un tableau de taches. Il contiendra toutes les taches données par la requête
                        $tasks = [];

                        try {
                                // On récupère tous les taches présents dans la BdD
                                $requete = $this->_db->prepare('SELECT id, title, description, deadline, is_complete, id_list FROM task WHERE id_list = :id_list ORDER BY id '.$order);

                                // On lie les différentes valeurs de manière sécurisée à la requête 
                                $requete->bindValue(':id_list', $id_list, PDO::PARAM_INT);

                                // On execute la requête
                                $requete->execute();

                                // Tant qu'il y des données dans le tableau $datas, on créé une nouvelle instance de la classe Task, que l'on insère ensuite dans le tableau
                                while ($datas = $requete->fetch(PDO::FETCH_ASSOC)) {
                                        // On stocke l'id et le title de la ligne courante
                                        $id = $datas["id"];
                                        $title = $datas["title"];
                                        $description = $datas["description"];
                                        $deadline = $datas["deadline"];
                                        $is_complete = $datas["is_complete"];
                                        $id_list = $datas["id_list"];

                                        // On instancie un nouveau tache
                                        $task = new Task($id, $title, $description, $deadline, $is_complete, $id_list);

                                        // On stocke le nouveau tache dans le tableau précédemment déclaré
                                        $tasks[] = $task;
                                }
                        } catch (Exception $erreur) {
                                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                                die('Erreur : '.$erreur->getMessage());
                        }

                        return $tasks;
                }

                /**
                 * Méthode permettant de vérifier si une tache existe déjà ou non dans la BdD. On partira du principe qu'une tache existe si son id et son titre sont identiques à une tache présente dans la BdD. On fait cette vérification uniquement sur les tache d'une liste, i.e. si une tache existe dans une certaine liste autre que celle actuelle, on ne fait pas de vérification dessus. On peut donc avoir la même tache mais dans des listes différentes.
                 * 
                 * @param task La tache pour laquelle on vérifie l'existence
                 */
                public function exists(Task $task) {
                        // On récupère l'id, le titre et la liste d'appartenance de la tache dont on vérifie l'existence.
                        $id = $task->get_id();
                        $title = strtoupper($task->get_title());
                        $id_list = $task->get_id_list();

                        // On récupère toutes les  taches d'une même liste.
                        $tasks = $this->get_all_by_id_list($id_list, "ASC");    

                        // On vérifie si l'id existe déjà ou non 
                        $index_id = $this->_utils->binary_search_over_object($tasks, $id, "id", 0, sizeof($tasks));
                        // On vérifie si le titre existe déjà ou non
                        $index_title = $this->_utils->binary_search_over_object($tasks, $title, "title", 0, sizeof($tasks));

                        // On retourne l'existence ou non d'une tache dans une liste donnée selon son id et son titre.
                        return ($index_id !== -1 || $index_title !== -1);
                }

        }

?>