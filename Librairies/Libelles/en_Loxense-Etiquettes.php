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


	$L_Etiquette = 'tag';
	$L_Etiquettes = 'tags';

	$L_Etiquette_Confirm_Suppression = 'Do you really want to delete this ' . $L_Etiquette . ' "<span class="purple">%s</span>"?';
	$L_Etiquette_Confirm_Suppression_Associe = 'Warning, this ' . $L_Etiquette . ' "<span class="purple">%s</span>" is associated with:';

	$L_List_Etiquettes = ucfirst($L_Etiquettes) . ' list';

	$L_Etiquette_Creer     = 'Create '. $L_Etiquette;
	$L_Etiquette_Ajouter   = "Add ". $L_Etiquette;
	$L_Etiquette_Modifier  = 'Modify '. $L_Etiquette;
	$L_Etiquette_Supprimer = 'Remove '. $L_Etiquette;

	$L_Etiquette_Cree    = ucfirst($L_Etiquette).' created' ;
	$L_Etiquette_Ajoute  = ucfirst($L_Etiquette). " added";
	$L_Etiquette_Modifie = ucfirst($L_Etiquette).' modified' ;
	$L_Etiquette_Supprime  = ucfirst($L_Etiquette).' removed' ;

	$L_ERR_CREA_Etiquette = 'An error occurred while creating of the '. $L_Etiquette;
	$L_ERR_MODI_Etiquette = 'An error occurred while modification of the '. $L_Etiquette;
	$L_ERR_DELE_Etiquette = 'An error occurred while removal of the '. $L_Etiquette;
	$L_ERR_DUPL_Etiquette = 'This ' . $L_Etiquette . ' already exists';

	$L_Etiquette = 'Tag';
	$L_Etiquettes = 'Tags';

?>