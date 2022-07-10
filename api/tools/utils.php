<?php

        class Utils {

                 // ----------------
                // Déclaration d'attribut
                // ----------------

                /** Instance de la classe Utils */
                private static $_instance;

                // ----------------
                // Constructeur
                // ----------------

                private function __construct() { }

                // ----------------
                // Récupération de l'instance de la classe Utils 
                // pour l'implémentation du design pattern Singleton
                // ----------------

                /**
                 * Méthode permettant de récupérer une unique instance de la classe Utils
                 */
                public static function get_instance() {
                        if (is_null(self::$_instance)) {
                                self::$_instance = new Utils();
                        }

                        return self::$_instance;
                }

                // ----------------
                // Méthode
                // ----------------

                /**
                 * Méthode peremttant de rechercher un objet selon une propriété donnée dans un tableau d'objet donné sur un intervalle donné.
                 * 
                 * @param array Le tableau d'objet dans lequel on recherche un objet
                 * @param target L'objet que l'on recherche 
                 * @param key La propriété sur laquelle on fait la recherche 
                 * @param start La borne inf de l'intervalle de recherche 
                 * @param end La borne sup de l'intervalle de recherche
                 */
                public function binary_search_over_object($array, $target, $key, $start, $end) {
                        $array = json_encode($array, JSON_FORCE_OBJECT);
                        $array = json_decode($array, TRUE);
                        $target = strtoupper($target);

                        if ($start >= $end) {
                                return -1;
                        } else {
                                $mean = floor(($start + $end - 1) / 2);
                                $element = strtoupper($array[$mean][$key]);
                                
                                if ($target === $element) {
                                        return $mean;
                                } else if ($target < $element) {
                                        return $this->binary_search_over_object($array, $target, $key, $start, $mean);
                                } else {
                                        return $this->binary_search_over_object($array, $target, $key, $mean + 1, $end);
                                }
                        }
                }

        }

?>