<?php

/**
* Ce script gère les Sites.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-02-19
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );


// Charge les informations et droits de base d'un écran.
include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

include( DIR_LIBRAIRIES . '/Class_Sites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );


// Crée l'instance de l'objet Entites.
$objSites = new Sites();
$objSocietes = new HBL_Societes();


// Définit le format des colonnes du tableau central.
$Trier = 'sts_nom';

$Format_Colonnes[ 'Prefixe' ] = 'STS';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'sts_id' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'sts_nom', 'titre' => $L_Nom, 'taille' => '3', 'maximum' => '50',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => $Trier, 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'sts_description', 'titre' => $L_Description, 'taille' => '7',
	'triable' => 'oui', 'tri_actif' => 'oui', 'type' => 'textarea', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Societes = $objSocietes->rechercherSocietes();
	} else {
		$Liste_Societes = $objSocietes->rechercherSocietes('', '', $_SESSION['idn_id'] );
	}
	
	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs = [
		['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus']
		];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Sites, $Fichiers_JavaScript, '3' ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Sites, $Liste_Societes, $Boutons_Alternatifs )
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
	$Libelles = array(
		'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'L_Titre_Ajouter' => $L_Ajouter_Site,
		'L_Titre_Modifier' => $L_Modifier_Site,
		'L_Titre_Supprimer' => $L_Supprimer_Site,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Libelle' => $L_Label,
		'L_Nom' => $L_Nom,
		'L_Description' => $L_Description,
		'L_Administrateur' => $L_Administrateur,
		'is_super_admin' => $_SESSION['idn_super_admin'],
		'L_Date' => $L_Date,
		'L_Societe' => $L_Societe
		);

	if ( $Droit_Modifier === TRUE ) {
		if ( isset( $_POST['sts_id'] ) ) {
			if ( $_POST['sts_id'] != '' ) {
				$Libelles['objSite'] = $objSites->rechercherSites( $_SESSION['s_sct_id'], 'sts_nom', $_POST['sts_id'] );
			}
		}
	}

	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['sts_nom']) && isset($_POST['sts_description']) ) {
			if ( $_POST['sts_nom'] == '' ) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory. ' (sts_nom)' );

				echo json_encode( $Resultat );
				exit();
			}

			if ( $_POST['sts_description'] == '' ) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . ' (sts_description)' );
				
				echo json_encode( $Resultat );
				exit();
			}


			$_POST['sts_nom'] = $PageHTML->controlerTypeValeur( $_POST['sts_nom'], 'ASCII' );
			if ( $_POST['sts_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sts_nom)'
				) );
				
				exit();
			}

			$_POST['sts_description'] = $PageHTML->controlerTypeValeur( $_POST['sts_description'], 'ASCII' );
			if ( $_POST['sts_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sts_description)'
				) );
				
				exit();
			}


			try {
				$objSites->majSite( $_SESSION['s_sct_id'], $_POST[ 'sts_nom' ], $_POST['sts_description'] );
				$Id_Site= $objSites->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_SITE', 'sts_id="' . $Id_Site . '", sts_nom="' . $_POST[ 'sts_nom' ] . '", sts_description="' . $_POST[ 'sts_description' ] . '"' );

				$Occurrence = $objSites->rechercherSites($_SESSION['s_sct_id'], 'sts_nom', $Id_Site);
				
				$Occurrences_HTML = $PageHTML->creerOccurrenceCorpsTableau( $Id_Site, $Occurrence[0], $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Site_Cree,
					'texte' => $Occurrences_HTML,
					'sts_id' => $Id_Site,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Site;
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
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			) );
	}
	break;


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['sts_id']) && isset($_POST['sts_nom']) && isset($_POST['sts_description']) ) {
			if ( $_POST['sts_id'] == '' ) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory. ' (sts_id)' );

				echo json_encode( $Resultat );
				exit();
			}

			if ( $_POST['sts_nom'] == '' ) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory. ' (sts_nom)' );

				echo json_encode( $Resultat );
				exit();
			}

			if ( $_POST['sts_description'] == '' ) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . ' (sts_description)' );

				echo json_encode( $Resultat );
				exit();
			}


			$_POST['sts_id'] = $PageHTML->controlerTypeValeur( $_POST['sts_id'], 'NUMBER' );
			if ( $_POST['sts_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sts_id)'
				) );

				exit();
			}

			$_POST['sts_nom'] = $PageHTML->controlerTypeValeur( $_POST['sts_nom'], 'ASCII' );
			if ( $_POST['sts_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sts_nom)'
				) );

				exit();
			}
			
			$_POST['sts_description'] = $PageHTML->controlerTypeValeur( $_POST['sts_description'], 'ASCII' );
			if ( $_POST['sts_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sts_description)'
				) );

				exit();
			}


			try {
				$objSites->majSite( $_SESSION['s_sct_id'], $_POST[ 'sts_nom' ], $_POST['sts_description'], $_POST['sts_id'] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_SITE', 'sts_id="' . $_POST['sts_id'] . '", sts_nom="' . $_POST[ 'sts_nom' ] . '", sts_description="' . $_POST[ 'sts_description' ] . '"' );
				
				$Occurrence = $objSites->rechercherSites($_SESSION['s_sct_id'], 'sts_nom', $_POST['sts_id']);

				$Occurrences_HTML = $PageHTML->creerOccurrenceCorpsTableau( $_POST['sts_id'], $Occurrence[0], $Format_Colonnes );
				
				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Site_Modifie,
					'texte' => $Occurrences_HTML,
					'id' => $_POST['sts_id'],
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();
				
				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Site;
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
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === TRUE ) {
		if (isset($_POST['id']) && isset($_POST['source'])&& isset($_POST['valeur'])){
			$_POST['id'] = $PageHTML->controlerTypeValeur( $_POST['id'], 'NUMBER' );
			if ( $_POST['id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sts_id)'
				) );
				
				exit();
			}
			
			$_POST['source'] = $PageHTML->controlerTypeValeur( $_POST['source'], 'ASCII' );
			if ( $_POST['source'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (#source)'
				) );
				
				exit();
			}
			
			$_POST['valeur'] = $PageHTML->controlerTypeValeur( $_POST['valeur'], 'ASCII' );
			if ( $_POST['valeur'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (#valeur)'
				) );
				
				exit();
			}


			try {
				$objSites->majSiteParChamp( $_POST['id'], $_POST['source'], $_POST['valeur'] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_SITE', 'sts_id="' . $_POST['id'] . '", '. $_POST['source'] . '="' . $_POST['valeur'] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Site_Modifie
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Site;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
					);
			}
			
			echo json_encode( $Resultat );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			) );
	}
	break;


 case 'AJAX_Supprimer':
	if ( $Droit_Supprimer === TRUE ) {
		if ( isset($_POST['id']) ) {
			try  {
				$objSites->supprimerSite( $_POST['id'] );

				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_SITE', 'sts_id="' . $_POST['id'] . '", libelle="' . $_POST[ 'libelle' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Site_Supprime,
					'libelle_limitation' => $L_Limitation_Licence
					);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $e->getMessage() );
			}

			echo json_encode( $Resultat );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			) );
	}
	break;


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];

		if ( isset( $_SESSION['s_sct_id'] ) ) {
			$sct_id = $_SESSION['s_sct_id'];
		} else {
			$sct_id = $_SESSION['sct_id'];
		}

		try {
			$Sites = $objSites->rechercherSites( $sct_id, $Trier );
			$Total = $objSites->RowCount;

			$Texte_HTML = '';

			foreach ($Sites as $Occurrence) {
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->sts_id, $Occurrence, $Format_Colonnes );
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
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			) );
	}
	break;


 case 'AJAX_Verifier_Associer':
	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['id']) ) {
			try { 
				$Compteurs = $objSites->SiteEstAssociee( $_POST['id'] );
	
				$CodeHTML = '';
	
				if ( $Compteurs->total_cmp > 0 ) {
					if ( isset( $Compteurs->total_cmp ) ) {
						if ( $Compteurs->total_cmp > 1 ) $Libelle = $L_Campagnes;
						else $Libelle = $L_Campagne;
						
						$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_cmp . '</span> ' . $Libelle . '</li>';
					}
	
					$CodeHTML .= '</ul>' . $L_Confirmer_Suppression_Site;
				} else {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_Site, $_POST['libelle'] );
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
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);
		
		echo json_encode( $Resultat );
		exit();
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Selectioner_Societe':
	$PageHTML->selectionnerSociete();

	break;
}
?>