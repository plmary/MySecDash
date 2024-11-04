<?php

/**
* Ce script gère les Applications Internes.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2024-01-10
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include_once( DIR_LIBRAIRIES . '/Class_HBL_ApplicationsInternes_PDO.inc.php' );

// Crée une instance de l'objet HTML.
$Applications = new HBL_ApplicationsInternes();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'AIN';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'ain_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ain_libelle', 'titre' => $L_Label, 'taille' => '4',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ain_libelle', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'tap_code', 'titre' => $L_Type, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'tap_code', 'type' => 'select', 'modifiable' => 'oui', 'fonction' => 'listerTypesApplication' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ain_localisation', 'titre' => $L_Localisation, 'taille' => '4',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ain_localisation', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];
	
	print( $PageHTML->construireEnteteHTML( $L_Gestion_ApplicationsInternes, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_ApplicationsInternes, '', $Boutons_Alternatifs )
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

 case 'AJAX_Libeller':
	$Libelles = array( 'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'L_Titre_Ajouter' => $L_Ajouter_Application,
		'L_Titre_Modifier' => $L_Modifier_Application,
		'L_Titre_Supprimer' => $L_Supprimer_Application,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Application' => $L_Application,
		'L_Libelle' => $L_Label,
		'L_Localisation' => $L_Localisation,
		'L_Type' => $L_Type,
		'Liste_Types_Application' => $Applications->rechercherTypesApplication()
		);

	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['ain_id']) and $_POST['ain_id'] != '') {
			$Libelles['Application'] = $Applications->detaillerApplication( $_POST['ain_id'] );
		}
	}

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['ain_libelle']) and isset($_POST['tap_id']) and isset($_POST['ain_localisation']) and 
			$_POST['ain_libelle'] != '' and $_POST['tap_id'] != '' and $_POST['ain_localisation'] != '' ) {
			try {
				$Applications->majApplication( '', $_POST['ain_libelle'], $_POST['ain_localisation'], $_POST['tap_id'] );

				$Id_Application = $Applications->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_APPLICATION', 'ain_id="' . $Id_Application . '", ain_libelle="' . $_POST[ 'ain_libelle' ] . '", ain_localisation="' . $_POST['ain_localisation'] . '"' );

				$objLibelle = $Applications->rechercherTypesApplication( $_POST['tap_id'] );

				$Valeurs = new stdClass();
				$Valeurs->tap_code = $objLibelle[0]->tap_code;
				$Valeurs->ain_libelle = $_POST['ain_libelle'];
				$Valeurs->ain_localisation = $_POST['ain_localisation'];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_Application, $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Application_Cree,
					'texte' => $Occurrence,
					'id' => $Id_Application,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);

			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Application;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
					);
			}

			echo json_encode( $Resultat );
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
				);

			echo json_encode( $Resultat );
			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur']) ) {
			try {
				$Applications->majApplicationParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_APPLICATION', $_POST[ 'source' ] . ' = "' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Application_Modifiee
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_Application;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
					);
			}
			
			echo json_encode( $Resultat );
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Supprimer':
	if ( $Droit_Supprimer === TRUE ) {
		if ( isset($_POST['id']) ) {
			try  {
				$Applications->supprimerApplication( $_POST['id'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_APPLICATION', 'ain_id="' . $_POST['id'] . '", ' .
					'ain_libelle="' . $_POST[ 'libelle' ] . '", tap_id="' . $_POST[ 'type' ] . '", ' .
					'ain_localisation="' . $_POST[ 'localisation' ] . '" ' );
	
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Application_Supprimee
					);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $e->getMessage() );
			}
	
			echo json_encode( $Resultat );
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];
		
		try {
			$ListeApplications = $Applications->rechercherApplications( $Trier );
			$Total = $Applications->RowCount;

			$Texte_HTML = '';
			
			foreach ($ListeApplications as $Occurrence) {
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->ain_id, $Occurrence, $Format_Colonnes );
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
	}	
	break;


 case 'AJAX_Charger':
	if ( $Droit_Lecture === TRUE ) {
		try {
			$Application = $Applications->detaillerApplication( $_POST['ain_id'] );
	
			echo json_encode( array(
				'statut' => 'success',
				'libelle' => $Application->ain_libelle,
				'type' => $Application->tap_id,
				'code' => $Application->ain_code,
				'localisation' => $Application->ain_localisation
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );		
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
	}

	break;


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['ain_id']) and isset($_POST['ain_libelle']) and isset($_POST['tap_id']) and isset($_POST['ain_localisation']) ) {
			try {
				$Applications->majApplication( $_POST['ain_id'], $_POST['ain_libelle'], $_POST['ain_localisation'], $_POST['tap_id'] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_APPLICATION', 'ain_id="' . $_POST['ain_id'] . '", ' .
					'ain_libelle="' . $_POST[ 'ain_libelle' ] . '", tap_id="' . $_POST[ 'tap_id' ] . '", ' .
					'ain_localisation="' . $_POST[ 'ain_localisation' ] . '" ' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Application_Modifiee
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Application;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
					);
			}

			echo json_encode( $Resultat );
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
				);

			echo json_encode( $Resultat );
			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Lister_Type_App':
 case 'AJAX_listerTypesApplication':
	if ( $Droit_Lecture === TRUE ) {
		$Resultat = array(
			'statut' => 'success',
			'texteMsg' => listerTypesApplication( '', $_POST['libelle'] )
			);
	
		echo json_encode( $Resultat );
		exit();
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Verifier_Associer':
	if ( isset($_POST['id']) ) {
		try { 
			$Compteurs = $Applications->controlerAssociationApplication( $_POST['id'] );

			$CodeHTML = '';

			if ( $Compteurs->total_prf ) {
				$CodeHTML .= sprintf( $L_Confirmer_Suppression_Application_Associee, $_POST['localisation'] ) .
					'<ul style="margin-top: 10px;">';

				if ( $Compteurs->total_prf > 1 ) $Libelle = $L_Access_Controls;
				else $Libelle = $L_Access_Control;

				$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_prf . '</span> ' . $Libelle . '</li>' .
					'</ul>' . $L_Cascading_Delete;
			} else {
				$CodeHTML .= sprintf( $L_Confirmer_Suppression_Application, $_POST['localisation'] );
			}

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $CodeHTML );
		} catch( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
		}
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires );
	}

	echo json_encode( $Resultat );

	break;
}


function listerTypesApplication( $Init_Id = '', $Init_Libelle = '' ) {
	$Applications = new HBL_ApplicationsInternes();

	$Liste = $Applications->rechercherTypesApplication();

	$Code_HTML = '';

	foreach ($Liste as $Occurrence) {
		if ( $Init_Id != '' and $Init_Id == $Occurrence->tap_id ) $Selected = ' selected';
		else $Selected = '';

		if ( $Init_Libelle != '' and $Init_Libelle == $Occurrence->tap_code ) $Selected = ' selected';
		else $Selected = '';

		$Code_HTML .= '<option value="' . $Occurrence->tap_id . '"' . $Selected . '>' . $Occurrence->tap_code . '</option>' ;
	}

	return $Code_HTML;
}

?>