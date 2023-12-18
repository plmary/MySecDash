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

	$L_Action_Confirm_Suppression = '<div class="row col-lg-12">Voulez-vous vraiment supprimer cette ' . $L_Action . ' ?</div>' .
		'<div class="row">' .
		'<span class="col-lg-3">Libellé :</span><span class="purple col-lg-9">%act_libelle</span>' .
		'<span class="col-lg-3">Acteur :</span><span class="purple col-lg-9">%acteur</span>' .
		'<span class="col-lg-3">Priorité :</span><span class="purple col-lg-9">%act_priorite</span>' .
		'<span class="col-lg-3">Date début :</span><span class="purple col-lg-9">%date_debut</span>' .
		'<span class="col-lg-3">Date fin :</span><span class="purple col-lg-9">%date_fin</span>' .
		'<span class="col-lg-3">Statut :</span><span class="purple col-lg-9">%act_statut</span>' .
		'</div>';
	$L_Action_Confirm_Suppression_Associe = 'Attention, cette ' . $L_Action . ' "<span class="purple">%s</span>" est associée avec :';

	$L_Liste_Actions = 'Liste des '. $L_Actions;

	$L_Action_Creer     = 'Créer une '. $L_Action;
	$L_Action_Ajouter   = "Ajouter une ". $L_Action;
	$L_Action_Modifier  = 'Modifier une '. $L_Action;
	$L_Action_Supprimer = 'Supprimer une '. $L_Action;

	$L_Action_Creee     = ucfirst($L_Action).' créée' ;
	$L_Action_Ajoutee   = ucfirst($L_Action). " ajoutée";
	$L_Action_Modifiee  = ucfirst($L_Action).' modifiée' ;
	$L_Action_Supprimee = ucfirst($L_Action).' supprimée' ;

	$L_ERR_CREA_Action = 'Erreur durant la création de l\''. $L_Action;
	$L_ERR_MODI_Action = 'Erreur durant la modification de l\''. $L_Action;
	$L_ERR_DELE_Action = 'Erreur durant la suppression de l\''. $L_Action;
	$L_ERR_DUPL_Action = 'Cette ' . $L_Action . ' existe déjà';

	$L_Action = 'Action';
	$L_Actions = 'Actions';

	$L_Acteur = 'Acteur';
	$L_Dates = 'Dates';
	$L_Date_Debut = 'Date début';
	$L_Date_Debut_p = 'Date début prévisionnelle';
	$L_Date_Debut_r = 'Date début réelle';
	$L_Date_Fin = 'Date fin';
	$L_Date_Fin_p = 'Date fin prévisionnelle';
	$L_Date_Fin_r = 'Date fin réelle';
	$L_Priorite = 'Priorité';
	$L_Sensibilite = 'Sensibilité';
	$L_Frequence = 'Fréquence';
	$L_Preuve = 'Preuve';
	$L_Preuves = 'Preuves';

	$L_Preuve_Visualiser  = 'Visualiser la '. $L_Preuve;
	$L_Preuve_Telecharger = "Télécharger la ". $L_Preuve;
	$L_Preuve_Supprimer = 'Supprimer la '. $L_Preuve;
	$L_Preuve_Transferer = 'Transférer une '. $L_Preuve;

	$L_Preuve_Telechargee  = ucfirst($L_Preuve).' téléchargée' ;
	$L_Preuve_Pb_Telechargement = 'Problème durant le téléchargement de la '.$L_Preuve;
	$L_Preuve_Supprimee = ucfirst($L_Preuve).' supprimée' ;

	$L_Editions_Actions = 'Edition des Actions';

	$L_Confirmer_Supprimer_Preuve = '<div class="col-lg-12"><span>%prv_libelle</span> (<span class="purple">%prv_fichier</span>)</div>' .
		'<div class="col-lg-12">Voulez-vous vraiment supprimer cette preuve ?</div>';
?>