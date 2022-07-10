<?php

    class ListeManager {

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
         * Méthode permettant d'ajouter une liste à la base de données 
         * 
         * @param \Liste $list Liste que l'on souhaite insérer
         */
        public function insert(Liste $list) {
            // Si la liste n'existe pas déjà...
            if (!$this->exists($list)) {
                try {
                    // On génère la requête permettant d'ajouter un liste à la BdD  
                    $requete = $this->_db->prepare('INSERT INTO liste (label, id_group) VALUES (:label, :id_group)');

                    // On lie les différentes valeurs de manière sécurisée à la requête 
                    $requete->bindValue(':label', $list->get_label(), PDO::PARAM_STR);
                    $requete->bindValue(':id_group', $list->get_id_group(), PDO::PARAM_INT);

                    // On execute la requête
                    $requete->execute();
                } catch (Exception $erreur) {
                    // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                    die('Erreur : ' . $erreur->getMessage());
                }
            } else {
                // Si la liste existe déjà, on lève une exception pour en informer l'utilisateur
                // throw new Exception("Cette liste existe déjà.");
                die('Cette liste existe déjà.');
            }
        }

        /**
         * Méthode permettant de mettre à jour une liste dans la base de données
         * 
         * @param \Liste $list La liste que l'on souhaite mettre à jour
         */
        public function update(Liste $list) {
            try {
                // On génère la requête permettant de mettre à jour une liste de la BdD   
                $requete = $this->_db->prepare('UPDATE liste SET label = :label, id_group = :id_group WHERE id = :id');

                // On lie les différentes valeurs de manière sécurisée à la requête 
                $requete->bindValue(':id', $list->get_id(), PDO::PARAM_INT);
                $requete->bindValue(':label', $list->get_label(), PDO::PARAM_STR);
                $requete->bindValue(':id_group', $list->get_id_group(), PDO::PARAM_INT);

                // On execute la requête
                $requete->execute();
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }
        }

        /**
         * Méthode permettant de supprimer une liste de la base de données
         * 
         * @param \Liste $list Le liste que l'on souhaite supprimer
         */
        public function delete(Liste $list) {
            try {
                // On génère la requête permettant de supprimer une liste de la BdD
                $requete = $this->_db->prepare('DELETE FROM liste WHERE id = :id');

                // On lie les différentes valeurs de manière sécurisée à la requête 
                $requete->bindValue(':id', $list->get_id(), PDO::PARAM_INT);

                // On execute la requête
                $requete->execute();
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }
        }

        /**
         * Méthode permettant de récupérer une liste dans la base de données à l'aide de son id
         * 
         * @param int $id L'id du liste que l'on souhaite trouver
         * @return \Liste
         */
        public function get($id) {
            // On commence par affecter une valeur null à la liste.
            // On fait ça dans le cas où aucune liste ne correspond à l'id entré.
            $list = null;

            try {
                // On génère la requête permettant de trouver une liste de la BdD grace à son id
                $requete = $this->_db->prepare('SELECT id, label, id_group FROM liste WHERE id = :id');

                // On lie les différentes valeurs de manière sécurisée à la requête 
                $requete->bindValue(':id', $id, PDO::PARAM_INT);

                // On execute la requête
                $requete->execute();

                // On place les valeurs récupérer dans la BdD dans une variable 
                $datas = $requete->fetch(PDO::FETCH_ASSOC);

                // On stock dans des variables les données obtenues provenant des différentes colonnes
                $id = $datas["id"];
                $label = $datas["label"];
                $id_group = $datas["id_group"];

                // On instancie un nouveau liste
                $list = new Liste($id, $label, $id_group);
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }

            return $list;
        }

        /**
         * Méthode permettant de récupérer toutes les listes présentes dans la BdD.
         * 
         * @param string $order L'ordre dans lequel on souhaite trier nos listes. Par défaut, on les ordonne du plus petit au plus grand.
         * @return array
         */
        public function get_all($order = "ASC") {
            // On commence par déclarer un tableau de groupes. Il contiendra toutes les listes données par la requête
            $lists = [];

            try {
                // On récupère tous les groupes présents dans la BdD
                $requete = $this->_db->query('SELECT id, label, id_group FROM liste ORDER BY id ' . $order);

                // Tant qu'il y des données dans le tableau $datas, on créé une nouvelle instance de la classe Liste, que l'on insère ensuite dans le tableau
                while ($datas = $requete->fetch(PDO::FETCH_ASSOC)) {
                    // On stocke l'id et le label de la ligne courante
                    $id = $datas["id"];
                    $label = $datas["label"];
                    $id_group = $datas["id_group"];

                    // On créé une nouvelle instance de la classe Liste avec l'id et le label de la ligne courante
                    $list = new Liste($id, $label, $id_group);
                    // On stocke le nouveau liste dans le tableau précédemment déclaré
                    $lists[] = $list;
                }
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }

            return $lists;
        }

        /**
         * Méthode permettant de récupérer toutes les listes d'un même groupe présentes dans la BdD.
         * 
         * @param int $id_group L'id du groupe auquel appartient un même ensemble de listes 
         * @param string $order L'ordre dans lequel on souhaite trier nos listes. Par défaut, on les ordonne du plus petit au plus grand.
         * @return array
         */
        public function get_all_by_id_group($id_group, $order = "ASC") {
            // On commence par déclarer un tableau de groupes. Il contiendra toutes les listes données par la requête
            $lists = [];

            try {
                // On récupère tous les groupes présents dans la BdD
                $requete = $this->_db->prepare('SELECT id, label, id_group FROM liste WHERE id_group = :id_group ORDER BY id ' . $order);

                // On lie les différentes valeurs de manière sécurisée à la requête 
                $requete->bindValue(':id_group', $id_group, PDO::PARAM_INT);

                // On execute la requête
                $requete->execute();

                // Tant qu'il y des données dans le tableau $datas, on créé une nouvelle instance de la classe Liste, que l'on insère ensuite dans le tableau
                while ($datas = $requete->fetch(PDO::FETCH_ASSOC)) {
                    // On stocke l'id et le label de la ligne courante
                    $id = $datas["id"];
                    $label = $datas["label"];
                    $id_group = $datas["id_group"];

                    // On créé une nouvelle instance de la classe Liste avec l'id et le label de la ligne courante
                    $list = new Liste($id, $label, $id_group);
                    // On stocke le nouveau liste dans le tableau précédemment déclaré
                    $lists[] = $list;
                }
            } catch (Exception $erreur) {
                // Si on rencontre un problème avec la requête, ou la BdD, on récupère l'exception, et on stoppe le programme
                die('Erreur : ' . $erreur->getMessage());
            }

            return $lists;
        }

        /**
         * Méthode permettant de vérifier si une liste existe déjà ou non dans la BdD. On partira du principe qu'une liste existe si son id et son label sont identiques à une liste présente dans la BdD. On fait cette vérification uniquement sur les listes d'un groupe, i.e. si une liste existe dans un certain groupe autre que celle actuelle, on ne fait pas de vérification dessus. On peut donc avoir la même liste mais dans des groupes différents.
         * 
         * @param \Liste $list La liste pour laquelle on vérifie l'existence
         * @return bool
         */
        public function exists(Liste $list) {
            // On stocke les diverses informations necessaires
            $id = $list->get_id();
            $label = $list->get_label();
            $id_group = $list->get_id_group();

            // On récupère toutes les listes d'un même groupe dans la base de données ordonnés dans l'ordre croissant
            $lists = $this->get_all_by_id_group($id_group, "ASC");

            // On recherche d'abord si l'id de la liste existe déjà dans la base de données
            $index_id = $this->_utils->binary_search_over_object($lists, $id, "id", 0, sizeof($lists));
            //On recherche ensuite si le label est déjà présent dans la base de données
            $index_label = $this->_utils->binary_search_over_object($lists, $label, "label", 0, sizeof($lists));

            return ($index_id !== -1 || $index_label !== -1);
        }
    }

?>
