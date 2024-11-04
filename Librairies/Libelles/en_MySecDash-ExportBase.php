<?php
/**
* Libellé en Anglais de l'écran de gestion des importations et des exportations de la Base.
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

	$L_Exporter_Base = 'Export database';
	$L_Importer_Base = 'Import database';
	$L_Restaurer_Base = 'Restore database';
	$L_Sauvegarder_Base = 'Save database';
	$L_Sauvegarde_Terminee = 'Save terminated';
	$L_Restauration_Terminee = 'Restore terminated';
	$L_Exportation_Terminee = 'Database export completed';
	$L_Importation_Terminee = 'Database import completed';
	$L_Sauvegarder = 'Backup';
	$L_Version = 'Loxense version';
	$L_Mot_Passe_Obligatoire_Export = 'Mandatory password to export';
	$L_Exporter = 'Export';
	$L_Importer = 'Import';
	$L_Restaurer = 'Restore';
	$L_Fichier_Chiffre = 'File encrypted';
	$L_Erreur_Chiffrement = 'Error during encryption';
	$L_Nom_Base_Chiffree = 'Name of database to import';
	$L_Mot_Passe_Dechiffrer_Base = 'Password to decrypt database';
	
	$L_Suppression_Fichier_Sauvegarde = 'Delete save file';

	$L_Donnees = 'Data in database';
	$L_Structure = 'Schema of the database';
	$L_Sauvegarde_Totale = 'Complete backup of the database (data + schema)';

	$L_Detail_Base = '<div class="row"><span class="col-lg-3">' . $L_Version . '</span><span class="col-lg-3" style="background-color: #dcafdd">%sav_version</span></div>'.
		'<div class="row"><span class="col-lg-3">' . $L_Type . '</span><span class="col-lg-6" style="background-color: #dcafdd">%sav_type</span></div>'.
		'<div class="row"><span class="col-lg-3">' . $L_Date . '</span><span class="col-lg-4" style="background-color: #dcafdd">%sav_date</span></div>';

	$L_Confirmer_Suppression_Base = 
		$L_Detail_Base .
		'<div class="row">Warning, do you want delete this file?</div>';

	$L_Choisir_Type_Sauvegarde_Base = 
		'<div class="row"><span class="col-lg-1"><input type="checkbox" id="type_d"></span><span class="col-lg-8"><label for="type_d">' . $L_Donnees . '</label></span></div>' .
		'<div class="row"><span class="col-lg-1"><input type="checkbox" id="type_s"></span><span class="col-lg-8"><label for="type_s">' . $L_Structure . '</label></span></div>';

	$L_Confirmer_Sauvegarde_Base = 'Do you confirm the execution of the backup?';

	$L_Choisir_Exporter_Base = 
		$L_Detail_Base .
		'<div class="row"></div>' .
		'<div class="row">' .
		'<span class="col-lg-5"><label for="mdp_export">' . $L_Mot_Passe_Obligatoire_Export . '</label></span>' .
		'<span class="col-lg-6"><input type="password" class="form-control" id="mdp_export" required autocomplete="off"></span>' .
		'</div>';

	$L_Alerte_Restauration_Donnees = '';

	$L_Alerte_Restauration_Structure = '';

?>