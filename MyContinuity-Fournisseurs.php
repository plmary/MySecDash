<?php

/**
* Ce script gère les Fournisseurs.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
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
include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_Fournisseurs_PDO.inc.php' );

// Crée une instance de l'objet HTML.
$objCampagnes = new Campagnes();
$objSocietes = new HBL_Societes();
$objFournisseurs = new Fournisseurs();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'FRN';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'frn_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'frn_nom', 'titre' => $L_Nom, 'taille' => '3',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'frn_nom', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'tfr_id', 'titre' => $L_Type, 'taille' => '3',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'tfr_nom_code', 'type' => 'select', 'modifiable' => 'oui', 'fonction' => 'listerTypesFournisseur' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'frn_description', 'titre' => $L_Description, 'taille' => '4',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'frn_description', 'type' => 'textarea', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );

$Droit_Ajouter_Types_Fournisseur = $PageHTML->controlerPermission('MyContinuity-TypesFournisseur.php', 'RGH_2');



// Exécute l'action identifie
switch( $Action ) {
 default:
	$Liste_Societes = '';

	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Fournisseurs, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Fournisseurs, $Liste_Societes, $Boutons_Alternatifs )
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
		'L_Titre_Ajouter' => $L_Ajouter_Fournisseur,
		'L_Titre_Modifier' => $L_Modifier_Fournisseur,
		'L_Titre_Supprimer' => $L_Supprimer_Fournisseur,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Nom' => $L_Nom,
		'L_Libelle' => $L_Label,
		'L_Description' => $L_Description,
		'L_Type' => $L_Type,
		'Liste_Types_Fournisseur' => $objFournisseurs->rechercherTypesFournisseur(),
		'L_Ajouter_Type_Fournisseur' => $L_Ajouter_Type_Fournisseur,
		'L_Type_Fournisseur_Cree' => $L_Type_Fournisseur_Cree,
		'L_Aucun' => $L_Neither,
		'Droit_Ajouter_Types_Fournisseur' => $Droit_Ajouter_Types_Fournisseur
		);

	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['frn_id']) and $_POST['frn_id'] != '') {
			$Libelles['Fournisseur'] = $objFournisseurs->rechercherFournisseurs( '', 'frn_nom', $_POST['frn_id'] );
		}
	}

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['frn_nom']) ) {
			$_POST['frn_nom'] = $PageHTML->controlerTypeValeur( $_POST['frn_nom'], 'ASCII' );
			if ( $_POST['frn_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_nom)'
				) );
				
				exit();
			}

			$_POST['tfr_id'] = $PageHTML->controlerTypeValeur( $_POST['tfr_id'], 'NUMBER' );
			if ( $_POST['tfr_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tfr_id)'
				) );
				
				exit();
			}

			$_POST['frn_description'] = $PageHTML->controlerTypeValeur( $_POST['frn_description'], 'ASCII' );
			if ( $_POST['frn_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_description)'
				) );
				
				exit();
			}

			try {
				$objFournisseurs->majFournisseur( '', $_POST['tfr_id'], $_POST['frn_nom'], $_POST['frn_description'] );

				$Id_Fournisseur = $objFournisseurs->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_FOURNISSEUR', 'frn_id="' . $Id_Fournisseur .
					'", tfr_id="' . $_POST['tfr_id'] .
					'", frn_nom="' . $_POST['frn_nom'] . '", frn_description="' . $_POST['frn_description'] . '"' );

				$objLibelle = $objFournisseurs->rechercherTypesFournisseur( $_POST['tfr_id'] );

				$Valeurs = new stdClass();
				$Valeurs->tfr_id = $objLibelle[0]->tfr_nom_code;
				$Valeurs->frn_nom = $_POST['frn_nom'];
				$Valeurs->frn_description = $_POST['frn_description'];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_Fournisseur, $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Fournisseur_Cree,
					'texte' => $Occurrence,
					'id' => $Id_Fournisseur,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);

			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Fournisseur;
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
				$objFournisseurs->majFournisseurParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_FOURNISSEUR', $_POST[ 'source' ] . ' = "' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Fournisseur_Modifie
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_Fournisseur;
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
			$_POST['id'] = $PageHTML->controlerTypeValeur( $_POST['id'], 'NUMBER' );
			if ( $_POST['id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (id)'
				) );
				
				exit();
			}
	
			try {
				$objFournisseurs->supprimerFournisseur( $_POST['id'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_FOURNISSEUR', 'frn_id="' . $_POST['id'] . '", ' .
					'frn_nom="' . $_POST[ 'libelle' ] . '"' );
	
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Fournisseur_Supprime
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
			$ListeFournisseurs = $objFournisseurs->rechercherFournisseurs( '', $Trier );
			$Total = $objFournisseurs->RowCount;

			$Texte_HTML = '';
			
			foreach ($ListeFournisseurs as $Occurrence) {
				$Occurrence->tfr_id = $Occurrence->tfr_nom_code;

				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->frn_id, $Occurrence, $Format_Colonnes );
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
		if ( isset($_POST['frn_id']) and isset($_POST['frn_nom']) and isset($_POST['tfr_id']) and isset($_POST['frn_description']) ) {
			$_POST['frn_id'] = $PageHTML->controlerTypeValeur( $_POST['frn_id'], 'NUMBER' );
			if ( $_POST['frn_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_id)'
				) );
				
				exit();
			}

			$_POST['frn_nom'] = $PageHTML->controlerTypeValeur( $_POST['frn_nom'], 'ASCII' );
			if ( $_POST['frn_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_nom)'
				) );
				
				exit();
			}

			$_POST['tfr_id'] = $PageHTML->controlerTypeValeur( $_POST['tfr_id'], 'NUMBER' );
			if ( $_POST['tfr_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tfr_id)'
				) );
				
				exit();
			}

			$_POST['frn_description'] = $PageHTML->controlerTypeValeur( $_POST['frn_description'], 'ASCII' );
			if ( $_POST['frn_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_description)'
				) );
				
				exit();
			}

			try {
				$objFournisseurs->majFournisseur( $_POST['frn_id'], $_POST['tfr_id'], $_POST['frn_nom'], $_POST['frn_description'] );
				
				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_FOURNISSEUR', 'frn_id="' . $_POST['frn_id'] . '", ' .
					'tfr_id="' . $_POST[ 'tfr_id' ] . '", frn_nom="' . $_POST[ 'frn_nom' ] . '", ' .
					'frn_description="' . $_POST[ 'frn_description' ] . '" ' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Fournisseur_Modifie
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Fournisseur;
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

 case 'AJAX_Verifier_Associer':
	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['id']) ) {
			try { 
				$Compteurs = $objFournisseurs->controlerAssociationFournisseur( $_POST['id'] );
	
				$CodeHTML = '';
	
				if ( $Compteurs->total_act ) {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_Fournisseur_Associe, $_POST['libelle'] ) .
						'<ul style="margin-top: 10px;">';
	
					if ( $Compteurs->total_act > 1 ) $Libelle = $L_Activites;
					else $Libelle = $L_Activite;
	
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_act . '</span> ' . $Libelle . '</li>' .
						'</ul>' . $L_Cascading_Delete;
				} else {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_Fournisseur, $_POST['libelle'] );
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
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
	}

	break;


 case 'AJAX_Selectioner_Societe':
	$PageHTML->selectionnerSociete();

	break;


 case 'AJAX_Selectioner_Campagne':
	if ( isset($_POST['cmp_id']) ) {
		if ( $PageHTML->verifierCampagneAutorisee($_POST['cmp_id']) ) {
			echo json_encode( array( 'statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' ) );
			exit();
		}

		$_SESSION['s_cmp_id'] = $_POST['cmp_id'];

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Campagne_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id']
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (cmp_id)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_listerTypesFournisseur':
	if ( $Droit_Lecture === TRUE ) {
		$Resultat = array(
			'statut' => 'success',
			'texteMsg' => listerTypesFournisseur( $_POST['id'], $_POST['libelle'] )
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


 case 'AJAX_Ajouter_Type_Fournisseur':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['n_tfr_nom_code'])  ) {
			$_POST['n_tfr_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['n_tfr_nom_code'], 'ASCII' );
			if ( $_POST['n_tfr_nom_code'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (n_tfr_nom_code)'
				) );
				
				exit();
			}

			try {
				$objFournisseurs->majTypeFournisseur( '', $_POST['n_tfr_nom_code'] );
			} catch( Exception $e ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $e->getMessage() );
			}

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Type_Fournisseur_Cree,
				'tfr_id' => $objFournisseurs->LastInsertId
			);
			
			echo json_encode( $Resultat );
			exit();
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
}


function listerTypesFournisseur( $Init_Id = '', $Init_Libelle = '' ) {
	$objFournisseurs = new Fournisseurs();

	$Liste = $objFournisseurs->rechercherTypesFournisseur();

	$Code_HTML = '';

	foreach ($Liste as $Occurrence) {
		if ( $Init_Id != '' and $Init_Id == $Occurrence->tfr_id ) $Selected = ' selected';
		else $Selected = '';

		if ( $Init_Libelle != '' and $Init_Libelle == $Occurrence->tfr_nom_code ) $Selected = ' selected';
		else $Selected = '';

		$Code_HTML .= '<option value="' . $Occurrence->tfr_id . '"' . $Selected . '>' . $Occurrence->tfr_nom_code . '</option>' ;
	}

	return $Code_HTML;
}

?>