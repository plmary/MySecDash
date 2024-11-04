<?php
/**
* Libellés spécifiques à la gestion des identitiés (utilisateurs).
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @copyright Copyright Loxense
* @author Pierre-Luc MARY
* @date 2017-03-05
*/

	$L_Title = 'User Management';
	$L_List_Users = 'User List';
	
	$L_Super_Admin = "Superadmin";
	$L_Admin = "Admin";
	$L_Administrateur = 'Administrator';
	$L_Assignement = "Member"; // // ex "assignation" apparait ds creation utilisateur pour le lier à 1 ou xx entités
	$L_Entite_Employeur = "Employer's entity";
	$L_Courriel = 'Email';

	$L_Ajouter_Utilisateur = 'Add user';
	$L_Creer_Utilisateur = 'Create user';
	$L_Supprimer_Utilisateur = 'Remove user';
	$L_Modifier_Utilisateur = 'Modify user';
	$L_Visualiser_Utilisateur = 'Display user\'s information';

	$L_Utilisateur_Ajoute = 'User added' ;
	$L_Utilisateur_Cree = 'User created' ; // pas redondant avec added ?
	$L_Utilisateur_Modifie = 'User modified' ;
	$L_Utilisateur_Supprime = 'User removed' ;

	$L_Flag_Changement_Authentifiant = 'Change your credentials';
	$L_Tentative = 'Attempt';
	$L_Desactiver = 'Disable';
	$L_Activer = 'Enable';
	$L_Derniere_Connexion = 'Previous connection';
	$L_Date_Expiration = 'Expiration date';
	$L_Date_Changement_Authentifiant = 'Last credential modification'; // on parle bien de la derniere date de modif des authentifiants ?
	$L_Retour_Liste_Utilisateurs = 'Return to User list';
	$L_Jamais_Connecte = 'Never logged';
	$L_Activer_Utilisateur = 'Enable user';
	$L_Desactiver_Utilisateur = 'Disable user';

	$L_Reinitialiser_Mot_Passe = 'Reset the password';
	$L_Mot_Passe_Reinitialise = 'The password has been reset';
	$L_Reinitialiser_Tentative = 'Reset the login attempt counter';
	$L_Tentative_Reinitialise = 'The login attempt counter has been reset';
	$L_Reinitialiser_Date_Expiration = 'Reset the expiration date';
	$L_Date_Expiration_Reinitialisee = 'The expiration date has been reset';

	$L_ERR_CREA_Identite = 'An error occurred while creating the identity';
	$L_ERR_MODI_Identite = 'An error occurred while editing the identity';
	$L_ERR_SUPP_Identite = 'An error occurred while deleting the identity';
	$L_ERR_DUPL_Identite = "'Username' already used";
	
	$L_ERR_RMZ_Mot_Passe = 'An error occurred while resetting password'; // ou "during the reset password process".
	$L_ERR_RMZ_Tentative = 'An error occurred while resetting the login attempt counter';
	$L_ERR_RMZ_Date_Expiration = 'An error occurred wile changing the expiration date';
	$L_ERR_RMZ_Activer_Desactiver_Utilisateur = 'An error occurred while enabling or disabling the user';

	$L_Utilisateur_Active = 'The user has been enabled';
	$L_Utilisateur_Desactive = 'The user has been disabled';
	$L_Associer_Utilisateur = 'Join users'; // associate, bind, link ? selon contexte d'utilisation

	$L_Confirmer_Suppression_Utilisateur = 'Do you really want to delete the user "<span class="fg_couleur_1">%idn</span>" (<span class="fg_couleur_1">%cvl</span>)?';

?>