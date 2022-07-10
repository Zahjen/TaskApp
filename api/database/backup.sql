-- Création de la table groupe 
CREATE TABLE IF NOT EXISTS groupe(
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        label VARCHAR(100)
);

-- Création de la table liste
CREATE TABLE IF NOT EXISTS liste(
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        label VARCHAR(100),
        id_group INTEGER NOT NULL,
        FOREIGN KEY(id_group) REFERENCES groupe(id)
);

-- Création de la table task
CREATE TABLE IF NOT EXISTS task(
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
        title VARCHAR(255),
        description VARCHAR(300),
        deadline DATETIME,
        is_complete BOOLEAN DEFAULT false,
        id_list INTEGER NOT NULL,
        FOREIGN KEY(id_list) REFERENCES liste(id)
);