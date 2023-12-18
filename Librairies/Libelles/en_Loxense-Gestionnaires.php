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


	$L_Gestionnaire = 'technical team';
	$L_Gestionnaires = 'technical team';

	$L_Gestionnaire_Confirm_Suppression = 'Do you really want to delete this ' . $L_Gestionnaire . ' "<span class="purple">%s</span>"?';
	$L_Gestionnaire_Confirm_Suppression_Associe = 'Warning, this ' . $L_Gestionnaire . ' "<span class="purple">%s</span>" is associated with:';

	$L_List_Gestionnaires = ucfirst($L_Gestionnaires) . ' list';

	$L_Gestionnaire_Creer     = 'Create '. $L_Gestionnaire;
	$L_Gestionnaire_Ajouter   = "Add ". $L_Gestionnaire;
	$L_Gestionnaire_Modifier  = 'Modify '. $L_Gestionnaire;
	$L_Gestionnaire_Supprimer = 'Remove '. $L_Gestionnaire;

	$L_Gestionnaire_Cree    = ucfirst($L_Gestionnaire).' created' ;
	$L_Gestionnaire_Ajoute  = ucfirst($L_Gestionnaire). " added";
	$L_Gestionnaire_Modifie = ucfirst($L_Gestionnaire).' modified' ;
	$L_Gestionnaire_Supprime  = ucfirst($L_Gestionnaire).' removed' ;

	$L_ERR_CREA_Gestionnaire = 'An error occurred while creating of the '. $L_Gestionnaire;
	$L_ERR_MODI_Gestionnaire = 'An error occurred while modification of the '. $L_Gestionnaire;
	$L_ERR_DELE_Gestionnaire = 'An error occurred while removal of the '. $L_Gestionnaire;
	$L_ERR_DUPL_Gestionnaire = 'This ' . $L_Gestionnaire . ' already exists';

	$L_Gestionnaire = 'Technical team';
	$L_Gestionnaires = 'Technical team';

?>