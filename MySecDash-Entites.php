<?php

/**
* Ce script gère les Entités de MySecDash.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2015-11-20
* \note check ok 2024-10-10
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );


// Charge les informations et droits de base d'un écran.
include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Entites.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );


// Crée l'instance de l'objet Entites.
$objEntites = new HBL_Entites();
$objSocietes = new HBL_Societes();


// Définit le format des colonnes du tableau central.
$Trier = 'ent_libelle';

$Format_Colonnes[ 'Prefixe' ] = 'ENT';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'ent_id' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'ent_nom', 'titre' => $L_Entite, 'taille' => '5', 'maximum' => '100',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ent_nom', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'ent_description', 'titre' => $L_Description, 'taille' => '5', 'maximum' => '100',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ent_description', 'type' => 'textarea', 'modifiable' => 'oui' );
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
	$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Entites, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Entites, $Liste_Societes, $Boutons_Alternatifs )
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
		'L_Titre_Ajouter' => $L_Ajouter_Entite,
		'L_Titre_Modifier' => $L_Modifier_Entite,
		'L_Titre_Supprimer' => $L_Supprimer_Entite,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Libelle' => $L_Label,
		'L_Nom' => $L_Nom,
		'L_Description' => $L_Description,
		'L_Administrateur' => $L_Administrateur,
		'is_super_admin' => $_SESSION['idn_super_admin']
		);

	if ( isset( $_POST['ent_id'] ) ) {
		if ( $_POST['ent_id'] != '' ) {
			if ( ! $PageHTML->verifierEntiteAutorisee($_POST['ent_id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' ) ) );
				
				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}

			$Libelles['objEntite'] = $objEntites->detaillerEntite( $_POST['ent_id'] );
		}
	}
	
	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['ent_nom']) && isset($_POST['ent_description']) ) {
			if ($_POST['ent_nom'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );

				echo json_encode( $Resultat );
				exit();
			}
			
			$_POST['ent_nom'] = $PageHTML->controlerTypeValeur( $_POST['ent_nom'], 'ASCII' );
			if ( $_POST['ent_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ent_nom)'
				) );
				
				exit();
			}

			if ( $_POST['ent_description'] != '' ) {
				$_POST['ent_description'] = $PageHTML->controlerTypeValeur( $_POST['ent_description'], 'ASCII' );
				if ( $_POST['ent_description'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (ent_description)'
					) );
					
					exit();
				}
			}

			try {
				$objEntites->majEntite( $_SESSION['s_sct_id'], '', $_POST[ 'ent_nom' ], $_POST[ 'ent_description' ] );
				$Id_Entity = $objEntites->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ENTITE', 'ent_id="' . $Id_Entity . '", ent_nom="' . $_POST[ 'ent_nom' ] . '", ent_description="' . $_POST[ 'ent_description' ] . '"' );
				
				$NewOccurrence = new stdClass();
				$NewOccurrence->ent_nom = $_POST[ 'ent_nom' ];
				$NewOccurrence->ent_description = $_POST[ 'ent_description' ];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_Entity, $NewOccurrence, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Entite_Cree,
					'texte' => $Occurrence,
					'id' => $Id_Entity,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Entite;
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


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['ent_nom']) && isset($_POST['ent_description']) ) {
			if ( ! $PageHTML->verifierEntiteAutorisee($_POST['ent_id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' ) ) );
				
				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}

			if ($_POST['ent_nom'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );
				
				echo json_encode( $Resultat );
				exit();
			}
			
			$_POST['ent_nom'] = $PageHTML->controlerTypeValeur( $_POST['ent_nom'], 'ASCII' );
			if ( $_POST['ent_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ent_nom)'
				) );
				
				exit();
			}
			
			$_POST['ent_id'] = $PageHTML->controlerTypeValeur( $_POST['ent_id'], 'NUMBER' );
			if ( $_POST['ent_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ent_id)'
				) );
				
				exit();
			}
			
			if ( $_POST['ent_description'] != '' ) {
				$_POST['ent_description'] = $PageHTML->controlerTypeValeur( $_POST['ent_description'], 'ASCII' );
				if ( $_POST['ent_description'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (ent_description)'
					) );
					
					exit();
				}
			}
			
			try {
				$objEntites->majEntite( $_SESSION['s_sct_id'], $_POST[ 'ent_id' ], $_POST[ 'ent_nom' ], $_POST[ 'ent_description' ] );
				$Id_Entity = $objEntites->LastInsertId;
				
				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ENTITE', 'ent_id="' . $Id_Entity . '", ent_nom="' . $_POST[ 'ent_nom' ] . '", ent_description="' . $_POST[ 'ent_description' ] . '"' );
				
				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Entite_Modifiee,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);
				
			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();
				
				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Entite;
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


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur']) ){
			if ( ! $PageHTML->verifierEntiteAutorisee($_POST['id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['id'] . '")'.' [' . __LINE__ . ']' ) ) );
				
				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['id'] . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}
			
			try {
				$objEntites->majEntiteParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ENTITE', 'ent_id="' . $_POST['id'] . '", ' . $_POST['source'] . '="' . $_POST['valeur'] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Entite_Modifiee
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Entite;
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
			if ( ! $PageHTML->verifierEntiteAutorisee($_POST['id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['id'] . '")'.' [' . __LINE__ . ']' ) ) );
				
				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['id'] . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}

			try  {
				$objEntites->supprimerEntite( $_POST['id'] );

				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ENTITE', 'ent_id="' . $_POST['id'] . '", ent_libelle="' . $_POST[ 'libelle' ] . '"' );

				$Limitation = $PageHTML->recupererParametre('limitation_entites');

				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Entite_Supprimee,
					'limitation' => $Limitation,
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
			if ( ! $PageHTML->verifierSocieteAutorisee($sct_id) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $sct_id . '")'.' [' . __LINE__ . ']' ) ) );

				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $sct_id . '")'.' [' . __LINE__ . ']' );

				exit();
			}

			$Entites = $objEntites->rechercherEntites( $sct_id, $Trier );
			$Total = $objEntites->RowCount;

			$Texte_HTML = '';

			foreach ($Entites as $Occurrence) {
				if ($Occurrence->ent_description == NULL) $Occurrence->ent_description = '';
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->ent_id, $Occurrence, $Format_Colonnes );
			}

			$Limitation = $PageHTML->recupererParametre('limitation_entites');

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
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
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			) );
	}
	break;


 case 'AJAX_Verifier_Associer':
	if ( isset($_POST['id']) ) {
		try { 
			$Compteurs = $objEntites->EntiteEstAssociee( $_POST['id'] );

			$CodeHTML = '';

			if ( $Compteurs->total_act > 0 || $Compteurs->total_iden > 0 || $Compteurs->total_cmen > 0 ) {
				$CodeHTML .= sprintf( $L_Confirmer_Suppression_Civilite_Associee, $_POST['libelle'] ) .
					'<ul style="margin-top: 10px;">';

				if ( isset( $Compteurs->total_act ) ) {
					if ( $Compteurs->total_act > 1 ) $Libelle = $L_Activites;
					else $Libelle = $L_Activite;
					
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_act . '</span> ' . $Libelle . '</li>';
				}
				
				if ( isset( $Compteurs->total_iden ) ) {
					if ( $Compteurs->total_iden > 1 ) $Libelle = $L_Utilisateurs_Habilites;
					else $Libelle = $L_Utilisateur_Habilite;
					
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_iden . '</span> ' . $Libelle . '</li>';
				}
				
				if ( isset( $Compteurs->total_cmen ) ) {
					if ( $Compteurs->total_cmen > 1 ) $Libelle = $L_Campagnes;
					else $Libelle = $L_Campagne;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_cmen . '</span> ' . $Libelle . '</li>';
				}

				$CodeHTML .= '</ul>' . $L_Cascading_Delete;
			} else {
				$CodeHTML .= sprintf( $L_Confirmer_Suppression_Civilite, $_POST['libelle'] );
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


 case 'AJAX_Selectioner_Societe':
	$PageHTML->selectionnerSociete();

	break;
}
?>