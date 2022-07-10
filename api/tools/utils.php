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

        private function __construct() {}

        // ----------------
        // Récupération de l'instance de la classe Utils 
        // pour l'implémentation du design pattern Singleton
        // ----------------

        /**
         * Méthode permettant de récupérer une unique instance de la classe Utils
         * 
         * @return \Utils Instance de la classe Utils
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
         * Méthode permettant de rechercher un objet selon une propriété donnée dans un tableau d'objet donné sur un intervalle donné. Supposons que l'on ait un tableau formé d'objet du type 
         * ```
         * { "id": int, "label": string }
         * ```
         * Il nous est alors possible de procéder à une recherche sur l'id, ou sur le label. Cela se fera via le paramètre `$key`.
         * 
         * @param array $array Le tableau d'objet dans lequel on recherche un objet
         * @param mixed $target L'objet que l'on recherche 
         * @param string $key La propriété sur laquelle on fait la recherche 
         * @param int $start La borne inf de l'intervalle de recherche 
         * @param int $end La borne sup de l'intervalle de recherche
         * 
         * @return int L'indice correspondant à la position de l'objet dans le tableau.
         */
        public function binary_search_over_object($array, $target, $key, $start, $end) {
            $array = json_encode($array, JSON_FORCE_OBJECT);
            $array = json_decode($array, TRUE);
            $target = strtoupper($target);

            // Si la borne inf de l'intervalle est plus grande que la borne sup, on arrête la recherche
            if ($start >= $end) {
                return -1;
            } else {
                // On récupère l'emplacement correspondant à la moitié du tableau
                $mean = floor(($start + $end - 1) / 2);
                // On récupère l'élément du tableau situé à la moitié du tableau
                $element = strtoupper($array[$mean][$key]);

                if ($target === $element) {
                    // Si l'élément recherché est celui qui est finalment ressorti du découpage, on retourne l'indice correspondant
                    return $mean;
                } else if ($target < $element) {
                    // Si l'élément recherché est inférieur à l'élément médian, cela signifie que l'élement se situe dans la première moitié du tableau
                    return $this->binary_search_over_object($array, $target, $key, $start, $mean);
                } else {
                    // Si l'élément recherché est supérieur à l'élément médian, cela signifie que l'élement se situe dans la deuxième moitié du tableau
                    return $this->binary_search_over_object($array, $target, $key, $mean + 1, $end);
                }
            }
        }
    }

?>