<?php

    /**
     * Classe relative à une tache. On aura donc accès à l'état actuel de la tache ainsi qu'à ses caractéristique
     */
    class Task implements JsonSerializable {

        // ----------------
        // Déclaration des attributs 
        // ----------------

        /** L'id d'une tache */
        private $_id;
        /** Le titre d'une tache */
        private $_title;
        /** La description / le détail donné à une tache */
        private $_description;
        /** La deadline donnée à une tache */
        private $_deadline;
        /* Le statut d'une tache, i.e. tache terminée ou non */
        private $_is_complete;
        /** L'id de la liste à laquel la tache  appartient */
        private $_id_list;

        // ----------------
        // Constructeur
        // ----------------

        public function __construct($id, $title, $description, $deadline, $is_complete, $id_list) {
            $this->set_id($id);
            $this->set_title($title);
            $this->set_description($description);
            $this->set_deadline($deadline);
            $this->set_is_complete($is_complete);
            $this->set_id_list($id_list);
        }

        // ----------------
        // Getter
        // ----------------

        /** 
         * Méthode permettant de récupérer l'id d'une tache 
         * 
         * @return int L'id d'une tache
         */
        public function get_id() {
            return (int) $this->_id;
        }

        /** 
         * Méthode permettant de récupérer le title d'une tache
         * 
         * @return string Le titre associé à une tache
         */
        public function get_title() {
            return (string) $this->_title;
        }

        /** 
         * Méthode permettant de récupérer la description d'une tache 
         * 
         * @return string La description associée à une tache
         */
        public function get_description() {
            return (string) $this->_description;
        }

        /** 
         * Méthode permettant de récupérer la date de fin d'une tache 
         * 
         * @return mixed La deadline associée à une tache
         */
        public function get_deadline() {
            return $this->_deadline;
        }

        /** 
         * Méthode permettant de récupérer le statut d'une tache, à savoir si une tache est terminée ou non 
         * 
         * @return bool L'état de la tache, i.e. tache finie ou non
         */
        public function is_complete() {
            return (bool) $this->_is_complete;
        }

        /** 
         * Méthode permettant de récupérer de récupérer l'id de liste à laquelle est rattachée une tache
         * 
         * @return int L'id de liste à laquelle est rattachée une tache
         */
        public function get_id_list() {
            return (int) $this->_id_list;
        }

        // ----------------
        // Setter
        // ----------------

        /** 
         * Méthode permettant d'instancier ou modifier l'id d'une tache
         * 
         * @param mixed $îd L'id d'une tache     
         */
        public function set_id($id) {
            // Si l'id entré est null, on lève une Exception 
            if ($id === null) {
                throw new Exception("L'id d'une tache ne peut être null.");
            }

            // Si l'id entré est autre chose qu'un entier, on lève une Exception.
            if (!is_int($id)) {
                throw new Exception("L'id d'une tache doit être un nombre entier.");
            }

            // On utilisera dans la BdD une auto incrémentation de l'id. Ainsi, on fait une vérification supplémentaire. Si l'id entré est inférieur à -1 ou égale à 0, on lève à nouveau une Exception.
            if ($id < -1 || $id === 0) {
                throw new Exception("L'id d'une tache ne peut être égale à 0 ou inférieur à -1.");
            }

            $this->_id = (int) $id;
        }

        /** 
         * Méthode permettant d'instancier ou modifier le titre d'une tache 
         * 
         * @param mixed $title Le titre associé à une tache 
         */
        public function set_title($title) {
            // On commence par retirer l'entièreté des espaces présents à droite et à gauche du titre
            $title = trim($title);

            // On lève ensuite une Exception si, après le trim, le titre est null ou égale à "".
            if ($title === null || $title === "") {
                throw new Exception("Le titre d'une tache doit être renseigné.");
            }

            $this->_title = $title;
        }

        /** 
         * Méthode permettant d'instancier ou modifier la description d'une tache 
         * 
         * @param mixed $description La description associée à une tache
         */
        public function set_description($description) {
            // On commence par retirer l'entièreté des espaces présents à droite et à gauche de la description
            $description = trim($description);

            // On lève ensuite une Exception si la description est null. On autorise ici le fait qu'une description peut ne pas être renseignée.
            if ($description === null) {
                throw new Exception("La description d'une tache ne peut être null.");
            }

            $this->_description = (string) $description;
        }

        /** 
         * Méthode permettant d'instancier ou modifier la deadline d'une tache 
         * 
         * @param mixed $deadline La deadline associée à une tache
         */
        public function set_deadline($deadline) {
            // On lève une Exception si la deadline est null.
            if ($deadline === null) {
                throw new Exception("La deadline d'une tache ne peut être null.");
            }

            $this->_deadline = $deadline;
        }

        /** 
         * Méthode permettant d'instancier ou modifier le statut d'une tache 
         * 
         * @param mixed $is_complete L'état d'une tache, i.e. tache est finie ou non
         */
        public function set_is_complete($is_complete) {
            // On lève une Exception si le statut est null.
            if ($is_complete === null) {
                throw new Exception("Le statut d'une tache ne peut être null.");
            }

            $this->_is_complete = (bool) $is_complete;
        }

        /** 
         * Méthode permettant d'instancier ou modifier l'id d'une liste à laquelle une tache appartient 
         * 
         * @param mixed $id L'id de liste à laquelle est rattachée la tache
         */
        public function set_id_list($id) {
            // Si l'id entré est null, on lève une Exception.
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

            $this->_id_list = (int) $id;
        }

        // ----------------
        // Méthode
        // ----------------



        // ----------------
        // Surcharge
        // ----------------

        /**
         * Ètant donné que les attributs sont privés, la représentation sous forme de json sera moins joli et moins parlante. On introduit donc une méthode venant de l'interface JsonSerializable. On aura donc pour l'objet `Task(2, "Un titre", "Une description", "2022-09-02", false, 5)` une représentation comme suit :
         * 
         * ```
         * {
         *      id: 2,
         *      title: "Un titre",
         *      description: "Une description",
         *      deadline: "2022-09-02",
         *      is_complete: false,
         *      id_list: 5
         * }
         * ```
         */
        public function jsonSerialize() {
            $json = [
                "id" => $this->get_id(),
                "title" => $this->get_title(),
                "description" => $this->get_description(),
                "deadline" => $this->get_deadline(),
                "is_complete" => $this->is_complete(),
                "id_list" => $this->get_id_list()
            ];

            return $json;
        }
    }

?>