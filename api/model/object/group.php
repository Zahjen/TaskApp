<?php

    /**
     * Classe relative à un groupe. On aura donc accès aux différentes caractéristques d'un groupe.
     */
    class Group implements JsonSerializable {

        // ----------------
        // Déclaration des attributs 
        // ----------------

        /** L'id d'un groupe */
        private $_id;
        /** Le label d'un groupe, i.e. le nom donné à un groupe */
        private $_label;

        // ----------------
        // Constructeur
        // ----------------

        public function __construct($id, $label) {
            $this->set_id($id);
            $this->set_label($label);
        }

        // ----------------
        // Getter
        // ----------------

        /** 
         * Méthode permettant de récupérer l'id d'un groupe 
         * 
         * @return int L'id d'un groupe
         */
        public function get_id() {
            return (int) $this->_id;
        }

        /** 
         * Méthode permettant de récupérer le label d'un groupe 
         * 
         * @return string Le label donné à un groupe
         */
        public function get_label() {
            return (string) $this->_label;
        }

        // ----------------
        // Setter
        // ----------------

        /** 
         * Méthode permettant d'instancier ou modifier l'id d'un groupe 
         * 
         * @param mixed $id L'id associé à un groupe
         */
        public function set_id($id) {
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

            $this->_id = $id;
        }

        /** 
         * Méthode permettant d'instancier ou modifier le label d'un groupe 
         * 
         * @param mixed $label Le label associé à un groupe
         */
        public function set_label($label) {
            // On commence par retirer l'entièreté des espaces présents à droite et à gauche du label
            $label = trim($label);

            // On lève ensuite une Exception si, après le trim, le label est null ou égale à "".
            if ($label === null || $label === "") {
                throw new Exception("Le label d'un groupe doit être renseigné.");
            }

            $this->_label = $label;
        }

        // ----------------
        // Méthode
        // ----------------



        // ----------------
        // Surcharge
        // ----------------

        /**
         * Ètant donné que les attributs sont privés, la représentation sous forme de json sera moins joli et moins parlante. On introduit donc une méthode venant de l'interface JsonSerializable. On aura donc pour l'objet `Group(2, "Un label")` une représentation comme suit :
         * 
         * ```
         * {
         *      id: 2,
         *      label: "Un label"
         * }
         * ```
         */
        public function jsonSerialize() {
            $json = [
                "id" => $this->get_id(),
                "label" => $this->get_label()
            ];

            return $json;
        }
    }

?>
