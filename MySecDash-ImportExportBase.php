<?php

/**
* Ce script gère les Imports/Exports de la base de données MySecDash.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \package MySecDash
* \date 2016-08-15
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

// Démarre le gestionnaire de session du serveur.
session_save_path( DIR_SESSION );
session_start();

// Initialise la langue Française par défaut.
if ( ! isset( $_SESSION[ 'Language' ] ) ) $_SESSION[ 'Language' ] = 'fr';

// Récupère le code langue, quand celui-ci est précisé.
if ( array_key_exists( 'Lang', $_GET ) ) {
   $_SESSION[ 'Language' ] = $_GET[ 'Lang' ];
}   

$Script = $_SERVER[ 'SCRIPT_NAME' ];
$URI = $_SERVER[ 'REQUEST_URI' ];
$IP_Source = $_SERVER[ 'REMOTE_ADDR' ];

// Force la connexion en HTTPS.
if ( ! array_key_exists( 'HTTPS', $_SERVER ) )
	header( 'Location: ' . URL_BASE . $URI );

// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );
include( DIR_LIBRAIRIES . '/Class_ActifsPrimordiaux_PDO.inc.php' );


// Crée une instance de l'objet HTML.
$PageHTML = new HTML();


// Vérifie si la session de l'utilisateur n'a pas expiré.
if ( $PageHTML->validerTempsSession() ) {
	$PageHTML->sauverTempsSession();
} else {
	print( $PageHTML->construirePageAlerte( $L_Session_Expired, '/Loxense-Connexion.php' ) );
	exit();
}


// Récupère les droits de l'utilisateur sur ce script.
$Permissions = $PageHTML->permissionsGroupees( $Script );


// Vérifie que l'utilisateur est habilité en lecture sur ce script.
if ( $PageHTML->permission( $Script ) === false ) {
	include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Autorisations.inc.php' );
	print( $PageHTML->construirePageAlerte( $L_No_Authorize_Script, '/Loxense-Principal.php' ) );
	exit();
}


// Identifie l'action à réaliser.
if ( array_key_exists( 'Action', $_GET ) ) {
	$Action = $_GET[ 'Action' ];
} else {
	$Action = '';
}


// Charge les javascripts relatifs aux droits de l'utilisateur.
$Droit_Lecture = FALSE;
$Droit_Ajouter = FALSE;
$Droit_Modifier = FALSE;
$Droit_Supprimer = FALSE;

$Path_Parts = pathinfo( $Script );
$Fichiers_JavaScript = [];

foreach ($Permissions[ basename( $Script ) ]["rights"] as $Droit) {
	switch ($Droit) {
		case 'RGH_1':
			$Droit_Lecture = TRUE;
			$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Lecture.js';
			break;
		case 'RGH_2':
			$Droit_Ajouter = TRUE;
			$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Ajouter.js';

			break;
		case 'RGH_3':
			$Droit_Modifier = TRUE;
			$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Modifier.js';
			break;
		case 'RGH_4':
			$Droit_Supprimer = TRUE;
			$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Supprimer.js';
			break;
	}
}


// Récupère les droits de l'utilisateur sur tous les scripts.
$Permissions = $PageHTML->permissionsGroupees();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'SAV';

$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';

$Format_Colonnes[ 'Id' ] = array( 'nom' => 'sav_id' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'sav_version', 'titre' => $L_Version, 'taille' => '2',
	'triable' => 'non', 'tri_actif' => 'oui', 'sens_tri' => 'version', 'type' => '', 'modifiable' => 'non' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'sav_type', 'titre' => $L_Type, 'taille' => '5',
	'triable' => 'non', 'sens_tri' => 'type', 'type' => '', 'modifiable' => 'non' );

$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'sav_date', 'titre' => $L_Date, 'taille' => '3',
	'triable' => 'non', 'sens_tri' => 'date', 'type' => '', 'modifiable' => 'non' );

$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'exporter' => $Droit_Ajouter, 'restaurer' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	// Définit le titre de navigation.
	$Titres_Nav = $PageHTML->prepareTitreMenuImportExport( $Permissions, $Script );

	$Boutons_Alternatifs[0]['class'] = 'btn-sauver';
	$Boutons_Alternatifs[0]['libelle']	= $L_Sauvegarder_Base;
	$Boutons_Alternatifs[0]['glyph'] = 'box-arrow-up-right';

	$Boutons_Alternatifs[1]['class'] = 'btn-importer';
	$Boutons_Alternatifs[1]['libelle']	= $L_Importer;
	$Boutons_Alternatifs[1]['glyph'] = 'cloud-download';


	print( $PageHTML->construireEnteteHTML( $L_Gestion_ImportExport_Base, $Fichiers_JavaScript ) .
	 $PageHTML->construireNavbar() .
	 $PageHTML->construireTitreEcran( $Titres_Nav, FALSE, NULL, $Boutons_Alternatifs )
	);


	if ( $Droit_Lecture === TRUE ) {
		// Construit un tableau central vide.
		print( $PageHTML->contruireTableauVide( $Format_Colonnes ) );
	}

	print( $PageHTML->construireFooter( TRUE ) .
	   $PageHTML->construirePiedHTML() );

	break;


 /* ========================================================================
 ** Réponses aux appels AJAX
 */

 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];
		
		try {
			if ( isset( $_POST['chercher']) ) {
				$Chercher = $_POST['chercher'];
			} else {
				$Chercher = '';
			}

			$Listes = scandir( DIR_SAUVEGARDES );

			$Total = 0;

			$Texte_HTML = '';
			
			foreach ($Listes as $Occurrence) {
				if ( is_dir( DIR_SAUVEGARDES . DIRECTORY_SEPARATOR . $Occurrence ) == FALSE ) {
					$_Tmp = explode( '-', $Occurrence );

					if ( $_Tmp[0] == 'Loxense' && $_Tmp[2] == 'Base' ) {
						$Total += 1;

						$objSave = new stdClass();

						$_Version = explode('_', $_Tmp[1]);
						$_Date = str_replace('_', '-', $_Tmp[3]);
						$_Heure = str_replace('_', ':', explode('.', $_Tmp[4])[0]);

						$objSave->sav_id = $Total;
						$objSave->sav_version = $_Version[0] . '.' . $_Version[1] . '-' . $_Version[2];
						$objSave->sav_type = $L_Sauvegarde_Totale;
						$objSave->sav_date = $_Date . ' ' . $_Heure;

						$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $_Tmp[3].'-'.$objSave->sav_id, $objSave, $Format_Colonnes );
					}
				}
			}

			$Limitation = 0; //$PageHTML->recupererParametre('limitation_actifs_primordiaux');

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'JourCourant' => date( 'Y-m-d' ),
				'droit_ajouter' => $Droit_Ajouter,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
				'limitation' => $Limitation,
				'libelle_limitation' => $L_Limitation_Licence
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );
		}
	}	
	break;


 case 'AJAX_Libeller':
	$Libelles = array(
		'statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'L_Creer' => $L_Creer,
		'L_Sauvegarder_Base' => $L_Sauvegarder_Base,
		'L_Sauvegarder' => $L_Sauvegarder,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Date' => $L_Date,
		'L_Version' => $L_Version,
		'L_Libelle' => $L_Libelle,
		'L_Type' => $L_Type,
		'AnneeCourante' => date( 'Y' ),
		'JourCourant' => date( 'Y-m-d' ),
		'account_lifetime' => $PageHTML->recupererParametre('account_lifetime'),
		'is_super_admin' => $_SESSION['idn_super_admin'],
		'L_Confirmer_Suppression_Base' => $L_Confirmer_Suppression_Base,
		'L_Suppression_Fichier_Sauvegarde' => $L_Suppression_Fichier_Sauvegarde,
		'L_Choisir_Type_Sauvegarde_Base' => $L_Choisir_Type_Sauvegarde_Base,
		'L_Choisir_Exporter_Base' => $L_Choisir_Exporter_Base,
		'L_Exporter' => $L_Exporter,
		'L_Exporter_Base' => $L_Exporter_Base,
		'L_Importer' => $L_Importer,
		'L_Importer_Base' => $L_Importer_Base,
		'L_Restaurer' => $L_Restaurer,
		'L_Restaurer_Base' => $L_Restaurer_Base,
		'L_Alerte_Restauration_Structure' => $L_Alerte_Restauration_Structure,
		'L_Alerte_Restauration_Donnees' => $L_Alerte_Restauration_Donnees,
		'L_Detail_Base' => $L_Detail_Base,
		'L_Confirmer_Sauvegarde_Base' => $L_Confirmer_Sauvegarde_Base,
		'L_Mot_Passe' => $L_Mot_Passe,
		'L_Nom_Base_Chiffree' => $L_Nom_Base_Chiffree,
		'L_Mot_Passe_Dechiffrer_Base' => $L_Mot_Passe_Dechiffrer_Base
		
		);
		
	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Supprimer':
	if ( isset($_POST['version']) && isset($_POST['date']) ) {
		try  {
			if ( @unlink( DIR_SAUVEGARDES . DIRECTORY_SEPARATOR .
				'Loxense-' . $_POST['version'] . '-Base-' . $_POST['date'] . '.sql.enc') ) {
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Fichier_Supprime
					);
			} else {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Fichier_Introuvable_Inaccessible
					);
			}
		} catch (Exception $e) {
			$Resultat = array( 'statut' => 'error',
				'titreMsg' => $L_Error,
				'texteMsg' => $e->getMessage() );
		}
	} else {
		$Resultat = array( 'statut' => 'error',
			'titreMsg' => $L_Error,
			'texteMsg' => $L_Parametre_Invalide );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Sauvegarder_Base':
	include( DIR_LIBRAIRIES . '/Class_HBL_Base.inc.php' );
 	
	$Base = new SGBDR();
 	
 	set_time_limit( 600 );

	try {
		// Sauvegarde la structure de la Base.
		list( $Statut, $Nom_Fichier ) = $Base->sauvegardeBase($PageHTML->Version_Outil, 2);

		if ( $Statut == 1 ) {
			$PageHTML->chiffrerFichierInterne( $Nom_Fichier, $Nom_Fichier . '.enc', 1 );
		}
		
		$Resultat = array(
			'statut' => 'success',
			'texteMsg' => $L_Sauvegarde_Terminee
			);
	} catch( Exception $e ) {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			);
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Exporter_Base':
	if ( isset($_POST['version']) && isset($_POST['date']) && isset($_POST['mot_passe']) ) {
		try  {
			$Nom_Fichier_Complet = DIR_SAUVEGARDES . DIRECTORY_SEPARATOR .
				'Loxense-' . $_POST['version'] . '-Base-' . $_POST['date'] . '.sql.enc';
			$Nom_Fichier_Tmp = DIR_SESSION . DIRECTORY_SEPARATOR .
			'Loxense-' . $_POST['version'] . '-Base-' . $_POST['date'] . '.sql.tmp';
			$Nom_Fichier_Export = DIR_SESSION . DIRECTORY_SEPARATOR .
			'Loxense-' . $_POST['version'] . '-Base-' . $_POST['date'] . '.sql.exp';

			$PageHTML->dechiffrerFichierInterne( $Nom_Fichier_Complet, $Nom_Fichier_Tmp );
			
			if ( $Nom_Fichier_Destination = $PageHTML->chiffrerFichierParCleUtilisateur( $_POST['mot_passe'], $Nom_Fichier_Tmp, $Nom_Fichier_Export, 1 ) ) {

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Exportation_Terminee,
					'nom_fichier' => pathinfo( $Nom_Fichier_Export, PATHINFO_BASENAME ) //,
					);
			} else {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Fichier_Introuvable_Inaccessible
					);
			}
		} catch (Exception $e) {
			$Resultat = array( 'statut' => 'error',
				'titreMsg' => $L_Error,
				'texteMsg' => $e->getMessage()
				);
		}
	} else {
		$Resultat = array( 'statut' => 'error',
			'titreMsg' => $L_Error,
			'texteMsg' => $L_Parametre_Invalide
			);
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Importer_Base':
 	if ( isset($_POST['mot_passe']) ) {
 		try  {
 			$_Version = explode( '_', explode( '-', $_FILES['fichier']['name'] )[1] );
 			$_Version = $_Version[0] . '.' . $_Version[1] . '-' . $_Version[2];

 			$_Date1 = str_replace( '_', '-', explode( '-', $_FILES['fichier']['name'] )[3] );
 			$_Date2 = str_replace( '_', ':', explode( '.', explode('-', $_FILES['fichier']['name'] )[4] )[0] );
 			$_Date = $_Date1 . ' ' . $_Date2;

 			$Nom_Fichier = preg_replace( '/\.exp$/', '', $_FILES['fichier']['name'] );
 			$Nom_Fichier_Exp = DIR_SESSION . DIRECTORY_SEPARATOR . $Nom_Fichier . '.exp';
 			$Nom_Fichier_Tmp = DIR_SESSION . DIRECTORY_SEPARATOR . $Nom_Fichier . '.tmp';
 			$Nom_Fichier_Complet = DIR_SAUVEGARDES . DIRECTORY_SEPARATOR . $Nom_Fichier . '.enc';
 			
 			move_uploaded_file($_FILES['fichier']['tmp_name'], $Nom_Fichier_Exp );
 			
 			if ( $Nom_Fichier_Destination = $PageHTML->dechiffrerFichierParCleUtilisateur( $_POST['mot_passe'], $Nom_Fichier_Exp, $Nom_Fichier_Tmp, 1 ) ) {
/* 				include( DIR_LIBRAIRIES . '/Class_HBL_Base.inc.php' );
 				
 				$Base = new SGBDR();
 				
 				set_time_limit( 600 );

 				// Restaure la structure et les données de la Base.
 				list( $Statut, $Resultat, $Affichage, $Code ) = $Base->restaureBase( $_Version, $_Date );
*/

 				$PageHTML->chiffrerFichierInterne( $Nom_Fichier_Tmp, $Nom_Fichier_Complet, 1 );

 				$Listes = scandir( DIR_SAUVEGARDES );
 				
 				$Texte_HTML = '';
 				$Total = count( $Listes );
 				
 				$_Tmp = explode( '-', $Nom_Fichier );
 						
				if ( $_Tmp[0] == 'Loxense' && $_Tmp[2] == 'Base' ) {
					$objSave = new stdClass();
 							
					$_Version = explode('_', $_Tmp[1]);
					$_Date = str_replace('_', '-', $_Tmp[3]);
					$_Heure = str_replace('_', ':', explode('.', $_Tmp[4])[0]);
 							
					$objSave->sav_id = $Total;
					$objSave->sav_version = $_Version[0] . '.' . $_Version[1] . '-' . $_Version[2];
					$objSave->sav_type = $L_Sauvegarde_Totale;
					$objSave->sav_date = $_Date . ' ' . $_Heure;
 							
					$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $_Tmp[3].'-'.$objSave->sav_id, $objSave, $Format_Colonnes );
				}
 				
 				$Resultat = array(
 					'statut' => 'success',
 					'texteMsg' => $L_Importation_Terminee,
 					'texteHTML' => $Texte_HTML,
 					'total' => $Total
 				);
 			} else {
 				if ( file_exists( $Nom_Fichier_Tmp ) ) unlink( $Nom_Fichier_Tmp );

 				$Resultat = array( 'statut' => 'error',
 					'titreMsg' => $L_Error,
 					'texteMsg' => $L_Fichier_Introuvable_Inaccessible
 				);
 			}
 		} catch (Exception $e) {
 			$Resultat = array( 'statut' => 'error',
 				'titreMsg' => $L_Error,
 				'texteMsg' => $e->getMessage()
 			);
 		}
 	} else {
 		$Resultat = array( 'statut' => 'error',
 			'titreMsg' => $L_Error,
 			'texteMsg' => $L_Parametre_Invalide
 		);
 	}

 	
 	//print_r($_FILES);exit();

 	echo json_encode( $Resultat );
 	
 	break;


 case 'AJAX_Charge_Fichier':
	$NomFichier = $_GET[ 'Nom_Fichier' ];
	$NomCompletFichier = DIR_SESSION . DIRECTORY_SEPARATOR . $_GET[ 'Nom_Fichier' ];

	if ( file_exists( $NomCompletFichier ) ) {
		header( 'Content-Description: File Transfer' );
		//header( 'Content-Type: application/octet-stream' );
		header( 'Content-Type: application/force-download' );
		header( 'Content-Disposition: attachment; filename="' . $NomFichier . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		//header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0' );
		//header( 'Pragma: public' );
		header( 'Pragma: no-cache' );
		header( 'Content-Length: ' . filesize( $NomCompletFichier ) );
		ob_clean();
		flush();
		readfile( $NomCompletFichier );

		unlink( $NomCompletFichier );

		exit( 1 );
	} else {
		//header( 'Location: Loxense-EditionsRisques.php?Action=AJAX_Pas_De_Fichier&crs_id='.$crs_id.'&Type=excel' );
		throw new Exception("no_file", 404);
	}

	break;


 case 'AJAX_Restaurer_Base':
	include( DIR_LIBRAIRIES . '/Class_HBL_Base.inc.php' );

 	$Base = new SGBDR();

 	set_time_limit( 600 );

	try {
		if ( isset( $_POST['version'] ) && isset( $_POST['date'] ) ) {
			$Nom_Fichier_Chiffre = DIR_SAUVEGARDES . DIRECTORY_SEPARATOR . 'Loxense-' . $_POST['version'] . '-Base-' . $_POST['date'] . '.sql.enc';
			$Nom_Fichier_Clair = DIR_SAUVEGARDES . DIRECTORY_SEPARATOR . 'Loxense-' . $_POST['version'] . '-Base-' . $_POST['date'] . '.sql';

			$PageHTML->dechiffrerFichierInterne( $Nom_Fichier_Chiffre, $Nom_Fichier_Clair, 0 );
			
			// Supprime la connexion à la base de données
			$PageHTML = NULL;

			// Restaure la structure et les données de la Base.
			list( $Statut, $Resultat, $Affichage, $Code ) = $Base->restaureBase( $_POST['version'], $_POST['date'] );

			unlink($Nom_Fichier_Clair);
			
			// Recrée la connexion à la base de données
			$PageHTML = new HTML();
		}

		if ( $Statut == 0 ) $Statut == 'error';
		else $Statut = 'success';

		$Resultat = array(
			'statut' => $Statut,
			'texteMsg' => $L_Restauration_Terminee,
			'affichage' => $Affichage,
			'code' => $Code
			);
	} catch( Exception $e ) {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage(),
			'affichage' => '',
			'code' => ''
			);
	}

	echo json_encode( $Resultat );

	break;
}

?>
