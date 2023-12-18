<?php
/**
* Libellés spécifiques à la gestion des Actions.
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @copyright Copyright Loxense
* @author Pierre-Luc MARY
* @date 2017-04-27
*/

	include_once( 'Constants.inc.php');

	$L_Action = 'action';
	$L_Actions = 'actions';

	$L_Action_Confirm_Suppression = '<div class="row col-lg-12">Do you really want to delete this ' . $L_Action . ' ?</div>' .
		'<div class="row">' .
		'<span class="col-lg-3">Label :</span><span class="purple col-lg-9">%act_libelle</span>' .
		'<span class="col-lg-3">Actor :</span><span class="purple col-lg-9">%acteur</span>' .
		'<span class="col-lg-3">Priority :</span><span class="purple col-lg-9">%act_priorite</span>' .
		'<span class="col-lg-3">Begin date :</span><span class="purple col-lg-9">%date_debut</span>' .
		'<span class="col-lg-3">End date :</span><span class="purple col-lg-9">%date_fin</span>' .
		'<span class="col-lg-3">Status :</span><span class="purple col-lg-9">%act_statut</span>' .
		'</div>';
	$L_Action_Confirm_Suppression_Associe = 'Warning, this ' . $L_Action . ' "<span class="purple">%s</span>" is associated with:';

	$L_Liste_Actions = ucfirst($L_Actions).' list';

	$L_Action_Creer     = 'Create '. $L_Action;
	$L_Action_Ajouter   = "Add ". $L_Action;
	$L_Action_Modifier  = 'Modify '. $L_Action;
	$L_Action_Supprimer = 'Remove '. $L_Action;

	$L_Action_Creee     = ucfirst($L_Action).' created' ;
	$L_Action_Ajoutee   = ucfirst($L_Action).' added';
	$L_Action_Modifiee  = ucfirst($L_Action).' modified' ;
	$L_Action_Supprimee = ucfirst($L_Action).' removed' ;

	$L_ERR_CREA_Action = 'An error occurred during the creation of this '. $L_Action;
	$L_ERR_MODI_Action = 'An error occurred during the modification of this '. $L_Action;
	$L_ERR_DELE_Action = 'An error occurred during the removal of this '. $L_Action;
	$L_ERR_DUPL_Action = 'This ' . $L_Action . ' already exists';

	$L_Action = 'Action';
	$L_Actions = 'Actions';

	$L_Acteur = 'Actor';
	$L_Dates = 'Dates';
	$L_Date_Debut = 'Begin date';
	$L_Date_Debut_p = 'Forecast start date';
	$L_Date_Debut_r = 'Actual start date';
	$L_Date_Fin = 'End date';
	$L_Date_Fin_p = 'Forecast end date';
	$L_Date_Fin_r = 'Actual end date';
	$L_Priorite = 'Priority';
	$L_Sensibilite = 'Sensibility';
	$L_Frequence = 'Frequency';
	$L_Preuve = 'Evidence';
	$L_Preuves = 'Evidences';

	$L_Preuve_Visualiser  = 'Viewing '. $L_Preuve;
	$L_Preuve_Telecharger = 'Download '. $L_Preuve;
	$L_Preuve_Supprimer = 'Remove '. $L_Preuve;
	$L_Preuve_Transferer = 'To transfer '. $L_Preuve;

	$L_Preuve_Telechargee  = ucfirst($L_Preuve).' downloaded' ;
	$L_Preuve_Pb_Telechargement = 'Problem during download '.$L_Preuve;
	$L_Preuve_Supprimee = ucfirst($L_Preuve).' removed' ;

	$L_Editions_Actions = 'Actions report';

	$L_Confirmer_Supprimer_Preuve = '<div class="col-lg-12"><span>%prv_libelle</span> (<span class="purple">%prv_fichier</span>)</div>' .
		'<div class="col-lg-12">Do you really want to delete this evidence?</div>';
?>