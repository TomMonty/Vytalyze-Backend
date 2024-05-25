Titre du projet

  Vytalyze x Paris 2024

Contexte du projet

  Les Jeux Olympiques approchent à grands pas. Cependant, la mairie de paris souhaiterait promouvoir l’engouement des parisiens autour de l’événement.

  La mairie lance alors un appel d’offre pour la conception d’un site autour des JO, permettant aux passionnés qui seront sur Paris de partager leur passion pour les Jeux.

Convention de nommage de classes

  Pour les variables : minusculeMajuscule
  Pour les classes : minuscule-minuscule

Guide d’installation et de lancement

  - télécharger le code
  - créer la base de données Paris2024 :


-- Create the nationalities table first as it is referenced by users
CREATE TABLE nationalities (
    id INT NOT NULL AUTO_INCREMENT,
    country_name VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    PRIMARY KEY (id)
);

-- Create the sports table as it is referenced by users and user_sports
CREATE TABLE sports (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    PRIMARY KEY (id)
);

-- Create the users table which references nationalities and sports
CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    last_name VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    gender ENUM('Male', 'Female', 'Other') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    nationality_id INT NOT NULL,
    sport_id INT DEFAULT NULL,
    mail VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    password VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (nationality_id) REFERENCES nationalities(id),
    FOREIGN KEY (sport_id) REFERENCES sports(id)
);

-- Create the user_sports table which references users and sports
CREATE TABLE user_sports (
    user_id INT NOT NULL,
    sport_id INT NOT NULL,
    PRIMARY KEY (user_id, sport_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (sport_id) REFERENCES sports(id)
);

-- Create the user_images table which references users
CREATE TABLE user_images (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_image VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create the posts table which references users
CREATE TABLE posts (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    post_photo VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
    description TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP DEFAULT_GENERATED,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);



  - dans le terminal mettre : php -S localhost:8080

Roadmap (prochaines tâches à entreprendre)
  
  Pour améliorer l'expérience des utilisateurs sur notre site web dédié aux Jeux Olympiques de Paris 2024, nous prévoyons d'ajouter plusieurs fonctionnalités interactives et engageantes. Ces fonctionnalités incluront un agenda détaillé des événements avec des mises à jour en direct des résultats, des profils complets des athlètes accompagnés d'interviews et d'analyses, ainsi que des vidéos en direct et en replay des compétitions et la mise à jour des victoires des médailles, par sports, par pays et par athlètes. De plus, nous intégrerons des fonctionnalités de partage sur les réseaux sociaux, des sections dédiées aux fans avec des quiz , des sondages et des jeux interactifs, ainsi que des options de personnalisation pour adapter l'expérience en fonction des préférences individuelles. Enfin, nous proposerons des informations pratiques telles que des guides pour les touristes, des informations sur les sites de compétition, et un contenu authentique incluant des récits d'athlètes et des reportages en coulisses. Le site possédera également un système de carte sur lequel on peut voir et intéragir avec , elle nous montrera les lieux touristiques ,transports, les restaurants et les hébergements à proximité, les lieux emblématiques , parcours de la flamme etc. Il sera également possible de parler à un chatbot si les utilisateurs ont des questions. Le support client sera disponible 24/7 . Le filtrage sera trier par sport , et ensuite par genre, la barre de recherche aidera également les utilisateurs à trouver plus facilement ce qu'ils cherchent . Pour les finir un "mur des fans" sera mis en avant pour que nos utilisateurs puissent partager leur moment forts entre eux .

Contributeurs (liens vers profils github)

Frontend : https://github.com/baw351/Vytalyze_Frontend
