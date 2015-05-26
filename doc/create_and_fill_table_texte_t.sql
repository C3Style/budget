--
-- Structure de la table `texte_t`
--
CREATE TABLE IF NOT EXISTS `texte_t` (
  `TE_ID` int(11) NOT NULL AUTO_INCREMENT,
  `TE_LANGUE` varchar(2) NOT NULL,
  `TE_CODE` varchar(100) NOT NULL,
  `TE_TEXTE` varchar(1000) NOT NULL,
  PRIMARY KEY (`TE_ID`),
  UNIQUE KEY `TE_CODE` (`TE_CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "recap_annuel", "Récapitulatif annuel");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "transac_mensuelle", "Transactions mensuelles");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "ajouter_transac", "Ajouter une transaction");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "parametre", "Paramètres");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "deconnexion", "Déconnexion");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "msg_confirm_suppr", "En acceptant, toutes les occurences ainsi que la transaction vont être supprimée.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "msg_confirm_rem", "Remarque");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "msg_confirm_rem_info", "pour supprimer une seule occurence, il faut passer par l'édition de la transaction et décocher la case.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "login", "Login");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "password", "Mot de passe");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "access_to_year", "Accéder à l'année");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "account", "Compte");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "recap_mensuel", "Récapitulatif mensuel");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "mois", "Mois");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_initial", "Solde initial");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_final", "Solde final");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "a_disposition", "A disposition");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_estime", "Solde estimé");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "access_to_month", "Accéder au mois de");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_initial_1_jan", "Solde initial au 1 janvier");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_effectif_31", "Solde effectif au 31");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_estime_31", "Solde estimé au 31 décembre");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "operation", "Opération");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "toute_confondue", "Toutes confondues (Total)");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "moyenne_abrev", "Moy");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "total_abrev", "Tot");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "budget_annuel", "Budget annuel");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "budget", "Budget");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "debit", "Débit");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "disposition", "Disposition");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucun_budget_annee", "Aucun budget et aucun débit n'ont été trouvés pour cette année.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucun_debit_budget", "Aucune débit n'a été trouvée pour ce budget.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "edit_budget_annuel", "Editer le budget annuel.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "delete_budget_annuel", "Supprimer le budget annuel.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "access_mois_debit", "Accéder au mois où a été effectué le débit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "total_budget_debit", "Total des budgets et débits");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "modifier", "Modifier");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "ajouter", "Ajouter");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "une_transaction", "une transaction");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "date", "Date");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "annee", "Année");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "recurrence", "Récurrence");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "tout_cocher", "Tout cocher");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "tout_decocher", "Tout décocher");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "recurrence_rem", "Remarque");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "recurrence_rem_info", "si aucune case n'est cochée, cela indique qu'un budget annuel est entrain d'être saisi.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "montant", "Montant");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "remarque", "Remarque");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "ajouter_la_transac", "Ajouter la transaction.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "compte_doit_select", "Un compte doit être sélectionné");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "go_transac_mensuelle", "se rendre sur l'écran Transaction mensuelle");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "type", "Type");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "voir_unpaid", "Voir les transactions non payées.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "print_decompte", "Imprimer le décompte mensuel en format PDF.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "credit", "Crédit");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_mois_passe", "Solde du mois passé");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucun_credit_mois", "Aucun crédit n'a été trouvée pour ce mois.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "unpaid_credit", "Annuler le paiement du crédit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "paid_credit", "Effectuer le paiment du crédit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "edit_credit", "Editer le crédit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "delete_credit", "Supprimer le crédit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "total_credit", "Total des crédits");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "budget_debit", "Budget et débit");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucun_budget_debit_mois", "Aucun budget et aucun débit n'a été trouvée pour ce mois.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "edit_budget_menusel", "Editer le budget.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "delete_budget_mensuel", "Supprimer le budget.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucun_debit_mois", "Aucune débit n'a été trouvé pour ce budget.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "unpaid_debit", "Annuler le paiement du débit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "paid_debit", "Effectuer le paiment du débit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "edit_debit", "Editer le débit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "delete_debit", "Supprimer le débit.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "total", "Total");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_mois", "Solde du mois courant");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde_estime_mois", "Solde estimé du mois courant");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "access_to_mois", "Accéder au mois de");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "list_unpaid", "Liste des transactions non payées");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucun_credit_unpaid", "Aucun crédit non payés dans les mois antérieurs.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucun_debit_unpaid", "Aucun débit non payés dans les mois antérieurs.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "gestion_operation", "Gestion des opérations");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "gestion_solde", "Gestion des soldes annuels");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "copie_transac", "Copie de transactions");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "ajouter_une_operation", "Ajouter une opération");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "ajouter_l_operation", "Ajouter l'opération.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "list_operation", "Liste des opérations");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucune_operation", "Aucune opération n'existe actuellement.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "delete_operation", "Supprimer l'opération.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "ajouter_un_solde", "Ajouter un solde de");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "pour_annee", "pour l'année");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "ajouter_le_solde", "Ajouter le solde annuel.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "list_solde", "Liste des soldes annuels");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "aucun_solde", "Aucune solde n'existe actuellement.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "solde", "Solde");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "delete_solde", "Supprimer le solde annuel.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "choix_annee_source", "Choisir l'année source");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "choix_annee_dest", "Choisir l'année de destination");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "choisir", "Choisir...");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "copier_transac", "Copier les transactions.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "copie_transac_ok", "Les données ont correctement été copiées.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "copie_transac_ko", "Les données n'ont pas pu être copiées.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "rapport_financier_titre", "Rapport financier pour");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "page", "Page");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "budget_mensuel", "Budget mensuel");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "retour", "Retour");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "paye", "Payé");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "oui", "Oui");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "non", "Non");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "save_transac_ok", "La transaction a bien été sauvée.");
INSERT INTO `c3stylech1`.`texte_t` (`TE_LANGUE`, `TE_CODE`, `TE_TEXTE`) VALUES ("FR", "save_transac_ko", "La transaction n'a pas été sauvée.");
