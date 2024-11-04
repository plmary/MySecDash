<?php
/**
* Libellés spécifiques à la gestion des Entités.
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @copyright Copyright Loxense
* @author Pierre-Luc MARY
* @date 2013-07-19
* @version 1.0
*/

	include_once( 'Constants.inc.php');


	$L_LibelleTraduction = 'translation';
	$L_LibelleTraductions = $L_LibelleTraduction.'s';

	$L_LibelleTraduction_Confirm_Deleted = 'Warning, are you sure you want to remove this '. $L_LibelleTraduction.' ?'; // NB : jamais appelé
	$L_Associee_LibelleTraduction_Confirm_Deleted = " and its";
	$L_Associees_LibellesTraductions_Confirm_Deleted = " and its ";

	$L_List_LibelleTraduction = ucfirst($L_LibelleTraduction) ." list";

	$L_LibelleTraduction_Add = "Add a ". $L_LibelleTraduction;
	$L_LibelleTraduction_Delete = 'Remove '. $L_LibelleTraduction;

	$L_LibelleTraduction_Added = ucfirst($L_LibelleTraduction). " added";
	$L_LibelleTraduction_Created = ucfirst($L_LibelleTraduction).' created' ; // redondant avec ajoutée, non ?
	$L_LibelleTraduction_Modified = ucfirst($L_LibelleTraduction).' modified' ;
	$L_LibelleTraduction_Deleted = ucfirst($L_LibelleTraduction).' deleted' ;

	$L_ERR_CREA_LibelleTraduction = 'An error occurred while creating the '. $L_LibelleTraduction;
	$L_ERR_MODI_LibelleTraduction = 'An error occurred while editing the '. $L_LibelleTraduction;
	$L_ERR_DELE_LibelleTraduction = 'An error occurred while deleting the '. $L_LibelleTraduction;
	$L_ERR_DUPL_LibelleTraduction = 'This ' . $L_LibelleTraduction . ' already exists';

?>