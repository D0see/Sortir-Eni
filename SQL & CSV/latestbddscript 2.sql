

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
	(2, 1, 'bar moins sympas', 'rue du chouette', 236.85, 458.12);
	(3, 1, 'patinore', 'rue du boulot', 236.85, 458.12);

	INSERT INTO `site` (`id`, `nom`) VALUES
	(1, 'Rennes'),
	(3, 'Nantes'),
	(4, 'Quimper'),
	(5, 'Niort');



	INSERT INTO `participant` (`id`, `site_id`, `nom`, `prenom`, `telephone`, `mail`, `administrateur`, `actif`, `pseudo`, `roles`, `password`, `ma_photo`) VALUES
	(1, 1, 'a', 'a', 'a', 'a', 0, 0, 'a', '[\"ROLE_USER\", \"ROLE_ADMIN\"]', '$2y$13$LssaxmmvcccoeiD92uz6WubMO/3cTBM4VmYN.uUv1kHRChtugEjfO', NULL);

    <!-- login : a , mdp : aaaaaa -->



	INSERT INTO `sortie` (`id`, `organisateur_id`, `site_id`, `etat_id`, `lieu_id`, `nom`, `date_heure_debut`, `duree_en_heures`, `date_limite_inscription`, `nb_inscriptions_max`, `infos_sortie`, `date_ouverture`, `motif_annulation`) VALUES
	(1, 1, 4, 2, 2, 'Dégustation', '2025-03-30 12:25:00', 2, '2025-03-27 12:29:00', 10, 'Non', '2025-03-26 12:25:00', NULL);
	INSERT INTO `sortie` (`id`, `organisateur_id`, `site_id`, `etat_id`, `lieu_id`, `nom`, `date_heure_debut`, `duree_en_heures`, `date_limite_inscription`, `nb_inscriptions_max`, `infos_sortie`, `date_ouverture`, `motif_annulation`) VALUES
	(2, 1, 3, 2, 2, 'Piscine', '2025-03-30 12:25:00', 1, '2025-03-27 12:29:00', 10, 'Non', '2025-03-26 12:25:00', NULL);


