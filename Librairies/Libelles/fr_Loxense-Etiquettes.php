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


	$L_Etiquette = 'étiquette';
	$L_Etiquettes = 'étiquettes';

	$L_Etiquette_Confirm_Suppression = 'Voulez-vous vraiment supprimer cette ' . $L_Etiquette . ' "<span class="purple">%s</span>" ?';
	$L_Etiquette_Confirm_Suppression_Associe = 'Attention, cette ' . $L_Etiquette . ' "<span class="purple">%s</span>" est associée avec :';

	$L_List_Etiquettes = 'Liste des '. $L_Etiquettes;

	$L_Etiquette_Creer     = 'Créer une '. $L_Etiquette;
	$L_Etiquette_Ajouter   = "Ajouter une ". $L_Etiquette;
	$L_Etiquette_Modifier  = 'Modifier une '. $L_Etiquette;
	$L_Etiquette_Supprimer = 'Supprimer une '. $L_Etiquette;

	$L_ERR_CREA_Etiquette = 'Erreur durant la création de l\''. $L_Etiquette;
	$L_ERR_MODI_Etiquette = 'Erreur durant la modification de l\''. $L_Etiquette;
	$L_ERR_DELE_Etiquette = 'Erreur durant la suppression de l\''. $L_Etiquette;
	$L_ERR_DUPL_Etiquette = 'Cette ' . $L_Etiquette . ' existe déjà';

	$L_Etiquette = 'Etiquette';
	$L_Etiquettes = 'Etiquettes';

	$L_Etiquette_Cree    = $L_Etiquette.' créée' ;
	$L_Etiquette_Ajoute  = $L_Etiquette. " ajoutée";
	$L_Etiquette_Modifie = $L_Etiquette.' modifiée' ;
	$L_Etiquette_Supprime  = $L_Etiquette.' supprimée' ;

?>