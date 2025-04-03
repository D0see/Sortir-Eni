

	INSERT INTO `etat` (`id`, `libelle`) VALUES
	(1, 'Créée'),
	(2, 'Ouverte'),
	(3, 'Clôturée'),
	(4, 'Activité en cours'),
	(5, 'Passée'),
	(6, 'Annulée');




	INSERT INTO `ville` (`id`, `nom`, `code_postal`) VALUES
	(1, 'Guer', '56380'),
	(2, 'Rennes', '35000'),
	(3, 'Lannion', '22300'),
	(4, 'Calan', '56240');



	INSERT INTO `lieu` (`id`, `ville_id`, `nom`, `rue`, `latitude`, `longitude`) VALUES
	(1, 2, 'bar sympas', 'rue du megafun', 152.02, 597.36),
	(2, 1, 'bar moins sympas', 'rue du chouette', 236.85, 458.12),
	(3, 1, 'patinore', 'rue du boulot', 236.85, 458.12);

	INSERT INTO `site` (`id`, `nom`) VALUES
	(1, 'Rennes'),
	(3, 'Nantes'),
	(4, 'Quimper'),
	(5, 'Niort');



	INSERT INTO `participant` (`id`, `site_id`, `nom`, `prenom`, `telephone`, `mail`, `administrateur`, `actif`, `pseudo`, `roles`, `password`, `ma_photo`) VALUES
	(1, 1, 'Alphonce', 'Albert', '0691552458', 'Albert.Alphoce@email.com', 0, 0, 'abeber', '[\"ROLE_USER\", \"ROLE_ADMIN\"]', '$2y$13$LssaxmmvcccoeiD92uz6WubMO/3cTBM4VmYN.uUv1kHRChtugEjfO', NULL);

<!-- login : abeber , mdp : aaaaaa -->

    INSERT INTO `participant` (`id`, `site_id`, `nom`, `prenom`, `telephone`, `mail`, `administrateur`, `actif`, `pseudo`, `roles`, `password`, `ma_photo`) VALUES
    (2, 1, 'Dupont', 'Marie', '0601020304', 'marie.dupont@email.com', 0, 1, 'marieD', '[\"ROLE_USER\"]', '$2y$13$QFz/aXv7IhLAXP3NN5y/h0yTPwhcuVv7enLrdkb2xFFZmz4/9HxlG', NULL),
    (3, 1, 'Martin', 'Paul', '0605060708', 'paul.martin@email.com', 0, 1, 'paulM', '[\"ROLE_USER\"]', '$2y$13$2pPbUKfP7b7VKJXjkaPyZ4HbftP0wyEVZXU8yWjM1pZdDtnK0pgyG', NULL),
    (4, 1, 'Durand', 'Sophie', '0612345678', 'sophie.durand@email.com', 0, 1, 'sophieD', '[\"ROLE_USER\"]', '$2y$13$N7ZryJ1jZT1gUdLNLRy9kFqjGivFnyHiRVhx9hbEob7XW/sHR4/i6', NULL),
    (5, 1, 'Lemoine', 'Julien', '0698765432', 'julien.lemoine@email.com', 0, 1, 'julienL', '[\"ROLE_USER\"]', '$2y$13$SKd3zVoFqVfchGVUtCghPfEKmi9Hqe51h/zoIT/eVAq0z/CpiX9Wy', NULL),
    (6, 1, 'Lefevre', 'Claire', '0623456789', 'claire.lefevre@email.com', 0, 1, 'claireL', '[\"ROLE_USER\"]', '$2y$13$bcpt4Cfh6waZq5fD2XtfqHE0ZYlTYF1fqmEoGx4hbde/UBH7ka1XK', NULL),
    (7, 1, 'Roux', 'Marc', '0688888888', 'marc.roux@email.com', 0, 1, 'marcR', '[\"ROLE_USER\"]', '$2y$13$6q0Zq5dqKgeIK0mX9eckGeQJH96cb.vnFSwZxyjXUoUodtMxx3Jr.', NULL),
    (8, 1, 'Fournier', 'Lina', '0644444444', 'lina.fournier@email.com', 0, 1, 'linaF', '[\"ROLE_USER\"]', '$2y$13$Fz5wpQLWadPCmbgD8hytZgo9mZ6lSrfkjkd49n9lqf3O6zABo.OXy', NULL),
    (9, 1, 'Pires', 'Alexandre', '0677777777', 'alexandre.pires@email.com', 0, 1, 'alexandreP', '[\"ROLE_USER\"]', '$2y$13$4LVwewlh5hFSZfAnREWlfXtOwsTk6knHqpn7g4HX0h37X91tIK7uC', NULL),
    (10, 1, 'Garnier', 'Isabelle', '0633333333', 'isabelle.garnier@email.com', 0, 1, 'isabelleG', '[\"ROLE_USER\"]', '$2y$13$bcpt4Cfh6waZq5fD2XtfqHE0ZYlTYF1fqmEoGx4hbde/UBH7ka1XK', NULL),
    (11, 1, 'Blanc', 'Nicolas', '0655555555', 'nicolas.blanc@email.com', 0, 1, 'nicolasB', '[\"ROLE_USER\"]', '$2y$13$1qt9Rl6kvZ99WljL5pywBOC3KMi4rgFh3gG7pbO5jiX4.c02OGXCe', NULL);





	INSERT INTO `sortie` (`id`, `organisateur_id`, `site_id`, `etat_id`, `lieu_id`, `nom`, `date_heure_debut`, `duree_en_heures`, `date_limite_inscription`, `nb_inscriptions_max`, `infos_sortie`, `date_ouverture`, `motif_annulation`) VALUES
	(1, 1, 4, 2, 2, 'Dégustation', '2025-03-30 12:25:00', 2, '2025-03-27 12:29:00', 10, 'Non', '2025-03-26 12:25:00', NULL);
	INSERT INTO `sortie` (`id`, `organisateur_id`, `site_id`, `etat_id`, `lieu_id`, `nom`, `date_heure_debut`, `duree_en_heures`, `date_limite_inscription`, `nb_inscriptions_max`, `infos_sortie`, `date_ouverture`, `motif_annulation`) VALUES
	(2, 1, 3, 2, 2, 'Piscine', '2025-03-30 12:25:00', 1, '2025-03-27 12:29:00', 10, 'Non', '2025-03-26 12:25:00', NULL);


    INSERT INTO `sortie` (`id`, `organisateur_id`, `site_id`, `etat_id`, `lieu_id`, `nom`, `date_heure_debut`, `duree_en_heures`, `date_limite_inscription`, `nb_inscriptions_max`, `infos_sortie`, `date_ouverture`, `motif_annulation`) VALUES
    (3, 1, 1, 2, 1, 'Soirée karaoké', '2025-04-05 20:00:00', 4, '2025-04-03 18:00:00', 15, 'Chansons à volonté !', '2025-04-01 12:00:00', NULL),
    (4, 1, 3, 2, 1, 'Tournoi de cartes', '2025-04-10 14:00:00', 3, '2025-04-08 12:00:00', 12, 'Decks autorisés : Standard et Classique.', '2025-04-05 10:00:00', NULL),
    (5, 2, 4, 1, 2, 'Dégustation de vins', '2025-04-15 19:00:00', 2, '2025-04-13 18:00:00', 10, 'Sélection spéciale de la région en passant du sud au nord, passer un momment de degustation avec nos meilleur vignerons.', '2025-04-10 12:00:00', NULL),
    (6, 3, 3, 2, 3, 'Soirée patinoire', '2025-04-18 18:30:00', 2, '2025-04-16 17:00:00', 20, 'Pensez aux gants et écharpes !', '2025-04-12 14:00:00', NULL),
    (7, 3, 1, 2, 1, 'Blind test musical', '2025-04-20 21:00:00', 3, '2025-04-18 19:00:00', 30, 'Thèmes variés, en solo ou en équipe.', '2025-04-15 12:00:00', NULL),
    (8, 4, 4, 1, 2, 'Randonnée nocturne', '2025-04-25 22:00:00', 5, '2025-04-23 18:00:00', 8, 'Marche en pleine nature sous la lune.', '2025-04-20 15:00:00', NULL),
    (9, 6, 3, 2, 3, 'Soirée disco', '2025-04-28 23:00:00', 5, '2025-04-26 20:00:00', 50, 'Années 80 et 90 à l’honneur !', '2025-04-22 12:00:00', NULL),
    (10, 5, 1, 2, 1, 'Apéro quiz', '2025-05-02 18:00:00', 3, '2025-04-30 17:00:00', 25, 'Quiz général sur tous les sujets.', '2025-04-25 12:00:00', NULL);
