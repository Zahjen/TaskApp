<?php

        class Database {

                // ----------------
                // Déclaration d'attribut
                // ----------------

                /** Instance de la classe Database */
                private static $_instance;
                /** Connexion à la BdD */
                private $_db;

                // ----------------
                // Constructeur
                // ----------------

                private function __construct() { }

                // ----------------
                // Récupération de l'instance de la classe Database 
                // pour l'implémentation du design pattern Singleton
                // ----------------

                /**
                 * Méthode permettant de récupérer une unique instance de la classe Database
                 */
                public static function get_instance() {
                        if (is_null(self::$_instance)) {
                                self::$_instance = new Database();
                        }

                        return self::$_instance;
                }

                // ----------------
                // Méthode
                // ----------------

                /**
                 * Méthode permettant de créer et récupérer la connexion à la base de données SQLite.
                 */
                public function get_connection() {
                        // On tente d'établir une connection avec la BdD
                        try {
                                // On tente d'établir une connexion avec la BdD à l'aide de la classe PDO.
                                $this->_db = new PDO(
                                        "sqlite:/home/sab/Bureau/task_app/api/database/db.sqlite",
                                        "",
                                        "",
                                        array(PDO::ATTR_PERSISTENT => true)
                                );
                        } catch (PDOException $sqle) {
                                // Si la connexion échoue, on stoppe le programme et on récupère le massage d'erreur
                                die('Error : '.$sqle->getMessage());
                        }

                        return $this->_db;
                }

        }

?>