<?php

/**
* Ce script gère les Types de Fournisseur.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \date 2024-10-09
* \note Contrôle sécurité réalisé
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include_once( DIR_LIBRAIRIES . '/Class_Fournisseurs_PDO.inc.php' );

// Crée une instance de l'objet HTML.
$objFournisseurs = new Fournisseurs();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'TFR';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'tfr_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'tfr_nom_code', 'titre' => $L_Nom, 'taille' => '9',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'tfr_nom_code', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '3', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
/*	$Boutons_Alternatifs = [
		['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'],
		['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search']
	];
*/
	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];
	
	print( $PageHTML->construireEnteteHTML( $L_Gestion_Types_Fournisseur, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Types_Fournisseur, '', $Boutons_Alternatifs )
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
		'L_Titre_Ajouter' => $L_Ajouter_Type_Fournisseur,
		'L_Titre_Modifier' => $L_Modifier_Type_Fournisseur,
		'L_Titre_Supprimer' => $L_Supprimer_Type_Fournisseur,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Nom' => $L_Nom,
		'L_Libelle' => $L_Label,
		'L_Description' => $L_Description,
		'L_Type' => $L_Type,
		);

	if ( isset($_POST['tfr_id']) and $_POST['tfr_id'] != '') {
		$Libelles['TypeFournisseur'] = $objFournisseurs->rechercherTypesFournisseur( $_POST['tfr_id'] );
	}

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['tfr_nom_code']) ) {
			$_POST['tfr_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['tfr_nom_code'], 'ASCII' );
			if ( $_POST['tfr_nom_code'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tfr_nom_code)'
				) );
				
				exit();
			}

			try {
				$objFournisseurs->majTypeFournisseur( '', $_POST['tfr_nom_code'] );

				$Id_TypeFournisseur = $objFournisseurs->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_FOURNISSEUR', 'tfr_id="' . $Id_TypeFournisseur .
					'", tfr_nom_code="' . $_POST['tfr_nom_code'] . '"' );

				$Valeurs = new stdClass();
				$Valeurs->tfr_nom_code = $_POST['tfr_nom_code'];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_TypeFournisseur, $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Type_Fournisseur_Cree,
					'texte' => $Occurrence,
					'id' => $Id_TypeFournisseur,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);

			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Type_Fournisseur;
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
				$objFournisseurs->majTypeFournisseurParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_FOURNISSEUR', $_POST[ 'source' ] . ' = "' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Type_Fournisseur_Modifie
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_Type_Fournisseur;
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
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['id']) ) {
			$_POST['id'] = $PageHTML->controlerTypeValeur( $_POST['id'], 'NUMBER' );
			if ( $_POST['id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tfr_id)'
				) );
				
				exit();
			}
	
			try {
				$objFournisseurs->supprimerTypeFournisseur( $_POST['id'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_FOURNISSEUR', 'tfr_id="' . $_POST['id'] . '", ' .
					'tfr_nom_code="' . $_POST[ 'libelle' ] . '"' );
	
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Type_Fournisseur_Supprime
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
			$ListeFournisseurs = $objFournisseurs->rechercherTypesFournisseur( '', $Trier );
			$Total = $objFournisseurs->RowCount;

			$Texte_HTML = '';
			
			foreach ($ListeFournisseurs as $Occurrence) {
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->tfr_id, $Occurrence, $Format_Colonnes );
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


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['tfr_id']) and isset($_POST['tfr_nom_code']) ) {
			$_POST['tfr_id'] = $PageHTML->controlerTypeValeur( $_POST['tfr_id'], 'NUMBER' );
			if ( $_POST['tfr_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tfr_id)'
				) );
				
				exit();
			}

			$_POST['tfr_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['tfr_nom_code'], 'ASCII' );
			if ( $_POST['tfr_nom_code'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tfr_nom_code)'
				) );
				
				exit();
			}

			try {
				$objFournisseurs->majTypeFournisseur( $_POST['tfr_id'], $_POST['tfr_nom_code'] );
				
				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_FOURNISSEUR',
					'tfr_id="' . $_POST[ 'tfr_id' ] . '", tfr_nom_code="' . $_POST[ 'tfr_nom_code' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Type_Fournisseur_Modifie
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Type_Fournisseur;
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


 case 'AJAX_Verifier_Associer':
	if ( isset($_POST['id']) ) {
		try { 
			$Compteurs = $objFournisseurs->controlerAssociationTypeFournisseur( $_POST['id'] );

			$CodeHTML = '';

			if ( $Compteurs->total_frn ) {
				$CodeHTML .= sprintf( $L_Confirmer_Suppression_Type_Fournisseur_Associe, $_POST['libelle'] ) .
					'<ul style="margin-top: 10px;">';

				if ( $Compteurs->total_frn > 1 ) $Libelle = $L_Fournisseurs;
				else $Libelle = $L_Fournisseur;

				$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_frn . '</span> ' . $Libelle . '</li>' .
					'</ul>' . $L_Cascading_Delete;
			} else {
				$CodeHTML .= sprintf( $L_Confirmer_Suppression_Type_Fournisseur, $_POST['libelle'] );
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

?>