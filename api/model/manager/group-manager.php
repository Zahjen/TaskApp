<?php

    class GroupManager {

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
         * @param \PDO $db Lien avec la BdD
         */
        public function set_db(PDO $db) {
            $this->_db = $db;
        }

        // -----------------------
        // Méthodes
        // -----------------------

        /** 
         * Méthode permettant d'ajouter un groupe à la base de données 
         * 
         * @param \Group $group Groupe que l'on souhaite insérer
         */
        public function insert(Group $group) {
            // Si le groupe n'existe pas déjà...
            if (!$this->exists($group)) {
                try {
                    // On génère la requête permettant d'ajouter un groupe à la BdD  
                    $requete = $this->_db->prepare('INSERT INTO groupe (label) VALUES (:label)');

                    // On lie les différentes valeurs de manière sécurisée à la requête 
                    $requete->bindValue(':label', $group->get_label(), PDO::PARAM_STR);

                    // On execute la requête
                    $requete->execute();
                } catch (Exception $erreur) {
                    // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                    die('Erreur : ' . $erreur->getMessage());
                }
            } else {
                // Si le groupe existe déjà, on lève une exception pour en informer l'utilisateur
                // throw new Exception("Ce groupe existe déjà.");
                die('Ce groupe existe déjà.');
            }
        }

        /**
         * Méthode permettant de mettre à jour un groupe dans la base de données
         * 
         * @param \Group $group Le groupe que l'on souhaite mettre à jour
         */
        public function update(Group $group) {
            try {
                // On génère la requête permettant de mettre à jour un groupe de la BdD   
                $requete = $this->_db->prepare('UPDATE groupe SET label = :label WHERE id = :id');

                // On lie les différentes valeurs de manière sécurisée à la requête 
                $requete->bindValue(':id', $group->get_id(), PDO::PARAM_INT);
                $requete->bindValue(':label', $group->get_label(), PDO::PARAM_STR);

                // On execute la requête
                $requete->execute();
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }
        }

        /**
         * Méthode permettant de supprimer un groupe de la base de données
         * 
         * @param \Group $group Le groupe que l'on souhaite supprimer
         */
        public function delete(Group $group) {
            try {
                // On génère la requête permettant de supprimer un groupe de la BdD
                $requete = $this->_db->prepare('DELETE FROM groupe WHERE id = :id');

                // On lie les différentes valeurs de manière sécurisée à la requête 
                $requete->bindValue(':id', $group->get_id(), PDO::PARAM_INT);

                // On execute la requête
                $requete->execute();
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }
        }

        /**
         * Méthode permettant de récupérer un groupe dans la base de données à l'aide de son id
         * 
         * @param int $id L'id du groupe que l'on souhaite trouver
         * @return \Group
         */
        public function get($id) {
            // On commence par affecter une valeur null au groupe.
            // On fait ça dans le cas où aucun groupe ne correspond à l'id entré.
            $group = null;

            try {
                // On génère la requête permettant de trouver un groupe de la BdD grace à son id
                $requete = $this->_db->prepare('SELECT id, label FROM groupe WHERE id = :id');

                // On lie les différentes valeurs de manière sécurisée à la requête 
                $requete->bindValue(':id', $id, PDO::PARAM_INT);

                // On execute la requête
                $requete->execute();

                // On place les valeurs récupérer dans la DdD dans une variable 
                $datas = $requete->fetch(PDO::FETCH_ASSOC);

                // On stock dans des variables les données obtenues provenant des différentes colonnes
                $id = $datas["id"];
                $label = $datas["label"];

                // On instancie un nouveau groupe
                $group = new Group($id, $label);
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }

            return $group;
        }

        /**
         * Méthode permettant de récupérer tous les groupes présents dans la BdD.
         * 
         * @param string $order L'ordre dans lequel on souhaite trier nos groupes. Par défaut, on les ordonne du plus petit au plus grand.
         * @return array
         */
        public function get_all($order = "ASC") {
            // On commence par déclarer un tableau de groupes. Il contiendra tous les groupes données par la requête
            $groups = [];

            try {
                // On récupère tous les groupes présents dans la BdD
                $requete = $this->_db->query('SELECT id, label FROM groupe ORDER BY id ' . $order);

                // Tant qu'il y des données dans le tableau $datas, on créé une nouvelle instance de la classe Group, que l'on insère ensuite dans le tableau
                while ($datas = $requete->fetch(PDO::FETCH_ASSOC)) {
                    // On stocke l'id et le label de la ligne courante
                    $id = $datas["id"];
                    $label = $datas["label"];

                    // On créé une nouvelle instance de la classe Goup avec l'id et le label de la ligne courante
                    $group = new Group($id, $label);
                    // On stocke le nouveau groupe dans le tableau précédemment déclaré
                    $groups[] = $group;
                }
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }

            return $groups;
        }

        /**
         * Méthode permettant de vérifier si un groupe existe déjà dans la BdD. On part du principe qu'un groupe existe si l'id et le label sont déjà présent dans la BdD.
         * 
         * @param \Group $group Le groupe pour lequel on souhaite vérifier l'existence.
         * @return bool
         */
        public function exists(Group $group) {
            // On stocke les diverses informations necessaires
            $id = $group->get_id();
            $label = $group->get_label();

            // On récupère tous les groupes dans la base de données ordonnés dans l'ordre croissant
            $groups = $this->get_all("ASC");

            // On recherche d'abord si l'id du groupe existe déjà dans la base de données
            $index_id = $this->_utils->binary_search_over_object($groups, $id, "id", 0, sizeof($groups));
            // On recherche ensuite si le label est déjà présent dans la base de données
            $index_label = $this->_utils->binary_search_over_object($groups, $label, "label", 0, sizeof($groups));

            return ($index_id !== -1 || $index_label !== -1);
        }
    }

?>