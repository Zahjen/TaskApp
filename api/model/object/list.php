<?php

    /**
     * Classe relative à une liste. On aura donc accès aux différentes caractéristques d'une liste.
     */
    class Liste implements JsonSerializable {

        // ----------------
        // Déclaration des attributs 
        // ----------------

        /** L'id d'une liste */
        private $_id;
        /** Le label d'une liste, i.e. le nom donné à une liste */
        private $_label;
        /** L'id du groupe auquel la liste appartient */
        private $_id_group;

        // ----------------
        // Constructeur
        // ----------------

        public function __construct($id, $label, $id_group) {
            $this->set_id($id);
            $this->set_label($label);
            $this->set_id_group($id_group);
        }

        // ----------------
        // Getter
        // ----------------

        /** 
         * Méthode permettant de récupérer l'id d'une liste 
         * 
         * @return int L'id d'une liste
         */
        public function get_id() {
            return (int) $this->_id;
        }

        /** 
         * Méthode permettant de récupérer le label d'une liste 
         * 
         * @return string Le label associé à une liste
         */
        public function get_label() {
            return (string) $this->_label;
        }

        /** 
         * Méthode permettant de récupérer l'id du group auquel la liste appartient 
         * 
         * @return int L'id de groupe auquel est rattaché une liste
         */
        public function get_id_group() {
            return (int) $this->_id_group;
        }

        // ----------------
        // Setter
        // ----------------

        /** 
         * Méthode permettant d'instancier ou modifier l'id d'une liste 
         * 
         * @param mixed $id L'id d'une liste
         */
        public function set_id($id) {
            // Si l'id entré est null, on lève une Exception 
            if ($id === null) {
                throw new Exception("L'id d'une liste ne peut être null.");
            }

            // Si l'id entré est autre chose qu'un entier, on lève une Exception.
            if (!is_int($id)) {
                throw new Exception("L'id d'une liste doit être un nombre entier.");
            }

            // On utilisera dans la BdD une auto incrémentation de l'id. Ainsi, on fait une vérification supplémentaire. Si l'id entré est inférieur à -1 ou égale à 0, on lève à nouveau une Exception.
            if ($id < -1 || $id === 0) {
                throw new Exception("L'id d'une liste ne peut être égale à 0 ou inférieur à -1.");
            }

            $this->_id = (int) $id;
        }

        /** 
         * Méthode permettant d'instancier ou modifier le label d'une liste 
         * 
         * @param mixed $label Le label associé à une liste
         */
        public function set_label($label) {
            // On commence par retirer l'entièreté des espaces présents à droite et à gauche du label
            $label = trim($label);

            // On lève ensuite une Exception si, après le trim, le label est null ou égale à "".
            if ($label === null || $label === "") {
                throw new Exception("Le label d'une liste doit être renseigné.");
            }

            $this->_label = (string) $label;
        }

        /** 
         * Méthode permettant d'instancier ou modifier l'id d'un groupe auquel la liste appartient 
         * 
         * @param int $id L'id de group auquel est rattaché une liste
         */
        public function set_id_group($id) {
            // Si l'id entré est null, on lève une Exception 
            if ($id === null) {
                throw new Exception("L'id d'un groupe ne peut être null.");
            }

            // Si l'id entré est autre chose qu'un entier, on lève une Exception.
            if (!is_int($id)) {
                throw new Exception("L'id d'un groupe doit être un nombre entier.");
            }

            // On utilisera dans la BdD une auto incrémentation de l'id. Ainsi, on fait une vérification supplémentaire. Si l'id entré est inférieur à -1 ou égale à 0, on lève à nouveau une Exception.
            if ($id < -1 || $id === 0) {
                throw new Exception("L'id d'un groupe ne peut être égale à 0 ou inférieur à -1.");
            }

            $this->_id_group = (int) $id;
        }

        // ----------------
        // Méthode
        // ----------------



        // ----------------
        // Surcharge
        // ----------------

        /**
         * Ètant donné que les attributs sont privés, la représentation sous forme de json sera moins joli et moins parlante. On introduit donc une méthode venant de l'interface JsonSerializable. On aura donc pour l'objet `Liste(2, "Un label", 3)` une représentation comme suit :
         * 
         * ```
         * {
         *      id: 2,
         *      label: "Un label",
         *      id_group: 3
         * }
         * ```
         */
        public function jsonSerialize() {
            $json = [
                "id" => $this->get_id(),
                "label" => $this->get_label(),
                "id_group" => $this->get_id_group()
            ];

            return $json;
        }
    }

?>
