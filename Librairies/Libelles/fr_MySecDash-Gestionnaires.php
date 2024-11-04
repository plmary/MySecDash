<?php
/**
* Libellés spécifiques à la gestion des Entités.
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @copyright Copyright Loxense
* @author Pierre-Luc MARY
* @date 2016-07-19
* @version 1.0
*/

	include_once( 'Constants.inc.php');


	$L_Gestionnaire = 'gestionnaire technique';
	$L_Gestionnaires = 'gestionnaires techniques';

	$L_Gestionnaire_Confirm_Suppression = 'Voulez-vous vraiment supprimer ce ' . $L_Gestionnaire . ' "<span class="fg_couleur_1">%s</span>" ?';
	$L_Gestionnaire_Confirm_Suppression_Associe = 'Attention, ce ' . $L_Gestionnaire . ' "<span class="fg_couleur_1">%s</span>" est associée avec :';

	$L_List_Gestionnaires = 'Liste des '. $L_Gestionnaires;

	$L_Gestionnaire_Creer     = 'Créer un '. $L_Gestionnaire;
	$L_Gestionnaire_Ajouter   = "Ajouter un ". $L_Gestionnaire;
	$L_Gestionnaire_Modifier  = 'Modifier un '. $L_Gestionnaire;
	$L_Gestionnaire_Supprimer = 'Supprimer un '. $L_Gestionnaire;

	$L_Gestionnaire_Cree    = ucfirst($L_Gestionnaire).' créé' ;
	$L_Gestionnaire_Ajoute  = ucfirst($L_Gestionnaire). " ajouté";
	$L_Gestionnaire_Modifie = ucfirst($L_Gestionnaire).' modifié' ;
	$L_Gestionnaire_Supprime  = ucfirst($L_Gestionnaire).' supprimé' ;

	$L_ERR_CREA_Gestionnaire = 'Erreur durant la création du '. $L_Gestionnaire;
	$L_ERR_MODI_Gestionnaire = 'Erreur durant la modification du '. $L_Gestionnaire;
	$L_ERR_DELE_Gestionnaire = 'Erreur durant la suppression du '. $L_Gestionnaire;
	$L_ERR_DUPL_Gestionnaire = 'Ce ' . $L_Gestionnaire . ' existe déjà';

	$L_Gestionnaire = 'Gestionnaire technique';
	$L_Gestionnaires = 'Gestionnaires techniques';

?>