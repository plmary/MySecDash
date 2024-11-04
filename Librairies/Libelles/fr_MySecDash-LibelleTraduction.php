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


	$L_LibelleTraduction = 'traduction';
	$L_LibelleTraductions = $L_LibelleTraduction.'s';

	$L_LibelleTraduction_Confirm_Deleted = 'Attention, êtes vous sur de vouloir supprimer cette '. $L_LibelleTraduction.' ?'; // NB : jamais appelé
	$L_Associee_LibelleTraduction_Confirm_Deleted = ' et sa';
	$L_Associees_LibellesTraductions_Confirm_Deleted = ' et ses ';

	$L_List_LibelleTraduction = 'Liste des '. $L_LibelleTraductions;

	$L_LibelleTraduction_Add = "Ajouter une ". $L_LibelleTraduction;
	$L_LibelleTraduction_Delete = 'Retirer la '. $L_LibelleTraduction;

	$L_LibelleTraduction_Added = ucfirst($L_LibelleTraduction). " ajoutée";
	$L_LibelleTraduction_Created = ucfirst($L_LibelleTraduction).' créée' ; // redondant avec ajoutée, non ?
	$L_LibelleTraduction_Modified = ucfirst($L_LibelleTraduction).' modifiée';
	$L_LibelleTraduction_Deleted = ucfirst($L_LibelleTraduction).' supprimée';

	$L_ERR_CREA_LibelleTraduction = 'Erreur durant la création de la '. $L_LibelleTraduction;
	$L_ERR_MODI_LibelleTraduction = 'Erreur durant la modification de la '. $L_LibelleTraduction;
	$L_ERR_DELE_LibelleTraduction = 'Erreur durant la suppression de la '. $L_LibelleTraduction;
	$L_ERR_DUPL_LibelleTraduction = 'Cette ' . $L_LibelleTraduction . ' existe déjà';

?>