<?php
/**
* Libellé en Français de l'écran de gestion des importations et des exportations de la Base.
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @copyright Copyright Loxense
* @author Pierre-Luc MARY
* @date 2017-12-16
*/

	include_once( 'Constants.inc.php');

	include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

	$L_Exporter_Base = 'Exporter la base';
	$L_Importer_Base = 'Importer la base';
	$L_Restaurer_Base = 'Restaurer la base';
	$L_Sauvegarder_Base = 'Sauvegarder la base';
	$L_Sauvegarde_Terminee = 'Sauvegarde de la base terminée';
	$L_Restauration_Terminee = 'Restauration de la base terminée';
	$L_Exportation_Terminee = 'Exportation de la base terminée';
	$L_Importation_Terminee = 'Importation de la base terminée';
	$L_Sauvegarder = 'Sauvegarder';
	$L_Version = 'Version de Loxense';
	$L_Mot_Passe_Obligatoire_Export = 'Mot de passe obligatoire pour exporter';
	$L_Exporter = 'Exporter';
	$L_Importer = 'Importer';
	$L_Restaurer = 'Restaurer';
	$L_Fichier_Chiffre = 'Fichier chiffré';
	$L_Erreur_Chiffrement = 'Erreur durant le chiffrement';
	$L_Nom_Base_Chiffree = 'Nom de la base chiffrée à importer';
	$L_Mot_Passe_Dechiffrer_Base = 'Mot de passe pour déchiffrer la base';

	$L_Suppression_Fichier_Sauvegarde = 'Suppression d\'un fichier de sauvegarde';

	$L_Donnees = 'Données de la base';
	$L_Structure = 'Structure de la base';
	$L_Sauvegarde_Totale = 'Sauvegarde totale de la base (données + structure)';

	$L_Detail_Base = '<div class="row"><span class="col-lg-3">' . $L_Version . '</span><span class="col-lg-3" style="background-color: #dcafdd">%sav_version</span></div>'.
		'<div class="row"><span class="col-lg-3">' . $L_Type . '</span><span class="col-lg-6" style="background-color: #dcafdd">%sav_type</span></div>'.
		'<div class="row"><span class="col-lg-3">' . $L_Date . '</span><span class="col-lg-4" style="background-color: #dcafdd">%sav_date</span></div>';

	$L_Confirmer_Suppression_Base = 
		$L_Detail_Base .
		'<div class="row"><span class="col-lg-12"><strong>Attention, êtes vous sur de vouloir supprimer ce fichier ?</strong></span></div>';

	$L_Choisir_Type_Sauvegarde_Base = 
		'<div class="row"><span class="col-lg-1"><input type="checkbox" id="type_d"></span><span class="col-lg-8"><label for="type_d">' . $L_Donnees . '</label></span></div>' .
		'<div class="row"><span class="col-lg-1"><input type="checkbox" id="type_s"></span><span class="col-lg-8"><label for="type_s">' . $L_Structure . '</label></span></div>';

	$L_Confirmer_Sauvegarde_Base = 'Confirmez-vous le lancement de la sauvegarde ?';

	$L_Choisir_Exporter_Base = 
		$L_Detail_Base .
		'<div class="row"></div>' .
		'<div class="row">' .
		'<span class="col-lg-5"><label for="mdp_export">' . $L_Mot_Passe_Obligatoire_Export . '</label></span>' .
		'<span class="col-lg-6"><input type="password" class="form-control" id="mdp_export" required autocomplete="off"></span>' .
		'</div>';

	$L_Alerte_Restauration_Donnees = 'Attention, cette restauration va effacer toutes vos précédentes données et mettre à la place les données contenues dans cette sauvegarde.<br/>' .
		'En revanche, la structure de votre base reste inchangée.';

	$L_Alerte_Restauration_Structure = 'Attention, cette restauration va effacer toutes vos précédentes données et elle va redéfinir une nouvelle structure de base (conforme au fichier de sauvegarde).';

?>