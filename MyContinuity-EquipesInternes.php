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
//include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_IndisponibilitesPersonnel_PDO.inc.php' );


// Crée l'instance de l'objet Entites.
$objEntites = new HBL_Entites();
$objSocietes = new HBL_Societes();
$objIndispoPersonnel = new IndisponibilitesPersonnel();


$L_EquipeInterne = $PageHTML->getLibelle('__LRI_EQUIPE_INTERNE');


// Définit le format des colonnes du tableau central.
$Trier = 'ent_libelle';

$Format_Colonnes[ 'Prefixe' ] = 'EIN';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'ein_id' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'ein_nom', 'titre' => $L_EquipeInterne, 'taille' => '4', 'maximum' => '100',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ein_nom', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'ein_description', 'titre' => $L_Description, 'taille' => '5',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ein_description', 'type' => 'textarea', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '3', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );

$L_Gestion_Equipes_Internes = $PageHTML->getLibelle('__LRI_GESTION_EQUIPES_INTERNES');


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

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Equipes_Internes, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Equipes_Internes, $Liste_Societes, $Boutons_Alternatifs )
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
		'L_Titre_Ajouter' => $PageHTML->getLibelle('__LRI_AJOUTER_EQUIPE_INTERNE'),
		'L_Titre_Modifier' => $PageHTML->getLibelle('__LRI_MODIFIER_EQUIPE_INTERNE'),
		'L_Titre_Supprimer' => $PageHTML->getLibelle('__LRI_SUPPRIMER_EQUIPE_INTERNE'),
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Libelle' => $L_Label,
		'L_Nom' => $L_Nom,
		'L_Description' => $L_Description,
		'L_Administrateur' => $L_Administrateur,
		'is_super_admin' => $_SESSION['idn_super_admin'],
		);

	if ( isset($_POST['ein_nom']) ) {
		$Libelles['L_Confirmer_Suppression_Equipe_Interne'] = sprintf( $PageHTML->getLibelle('__LRI_CONFIRMER_SUPPRESSION_EQUIPE_INTERNE'),
			'<span class="fg_rouge">' . $_POST['ein_nom'] . '</span>' );
	}

	if ( isset($_POST['ein_id']) ) {
		$Libelles['objEquipeInterne'] = $objIndispoPersonnel->rechercherEquipes($_SESSION['s_sct_id'], 'ein_nom', $_POST['ein_id']);
	}

	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['ein_nom']) && isset($_POST['ein_description']) ) {
			if ($_POST['ein_nom'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . ' (ein_nom)' );

				echo json_encode( $Resultat );
				exit();
			}

			if ( $_POST['ein_description'] != '' ) {
				$_POST['ein_description'] = $PageHTML->controlerTypeValeur( $_POST['ein_description'], 'ASCII' );
				if ( $_POST['ein_description'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (ein_description)'
					) );
					
					exit();
				}
			}

			try {
				$objIndispoPersonnel->ajouterModifierEquipeInterne( '', $_SESSION['s_sct_id'], $_POST[ 'ein_nom' ], $_POST[ 'ein_description' ] );
				$Id_Equipe = $objIndispoPersonnel->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_EQUIPE_INTERNE', 'ein_id="' . $Id_Equipe . '", ein_nom="' . $_POST[ 'ein_nom' ] . '", ein_description="' . $_POST[ 'ein_description' ] . '"' );
				
				$NewOccurrence = new stdClass();
				$NewOccurrence->ein_nom = $_POST[ 'ein_nom' ];
				$NewOccurrence->ein_description = $_POST[ 'ein_description' ];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_Equipe, $NewOccurrence, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $PageHTML->getLibelle('__LRI_EQUIPE_INTERNE_AJOUTEE'),
					'texte' => $Occurrence,
					'id' => $Id_Equipe,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $_POST[ 'ein_nom' ] . ' : ' . $PageHTML->getLibelle('__LRI_ERR_SYS_DEJA_EXISTANT');
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
		if ( isset($_POST['ein_nom']) && isset($_POST['ein_description']) ) {
			if ($_POST['ein_nom'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );

				echo json_encode( $Resultat );
				exit();
			}

			$_POST['ein_nom'] = $PageHTML->controlerTypeValeur( $_POST['ein_nom'], 'ASCII' );
			if ( $_POST['ein_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ein_nom)'
				) );

				exit();
			}

			if ( $_POST['ein_description'] != '' ) {
				$_POST['ein_description'] = $PageHTML->controlerTypeValeur( $_POST['ein_description'], 'ASCII' );
				if ( $_POST['ein_description'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (ein_description)'
					) );

					exit();
				}
			}

			try {
				$objIndispoPersonnel->ajouterModifierEquipeInterne( $_POST[ 'ein_id' ], $_SESSION['s_sct_id'], $_POST[ 'ein_nom' ], $_POST[ 'ein_description' ] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_EQUIPE_INTERNE', 'ein_id="' . $_POST[ 'ein_id' ] . '", ein_nom="' . $_POST[ 'ein_nom' ] . '", ein_description="' . $_POST[ 'ein_description' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $PageHTML->getLibelle('__LRI_EQUIPE_INTERNE_MODIFIEE'),
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $_POST[ 'ein_nom' ] . ' : ' . $PageHTML->getLibelle('__LRI_ERR_SYS_DEJA_EXISTANT');
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
			try {
				$objIndispoPersonnel->majEquipeInterneParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_EQUIPE_INTERNE', 'ein_id="' . $_POST['id'] . '", ' . $_POST['source'] . '="' . $_POST['valeur'] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $PageHTML->getLibelle('__LRI_EQUIPE_INTERNE_MODIFIEE')
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $PageHTML->getLibelle('__LRI_EQUIPE_INTERNE') . ' ' . $_POST['valeur'] . ' : ' . $PageHTML->getLibelle('__LRI_ERR_SYS_DEJA_EXISTANT');
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
				$objIndispoPersonnel->supprimerEquipeInterne( $_POST['id'] );

				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_EQUIPE_INTERNE', 'ein_id="' . $_POST['id'] . '", ein_nom="' . $_POST[ 'ein_nom' ] . '"' );

				$Limitation = $PageHTML->recupererParametre('limitation_entites');

				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $PageHTML->getLibelle('__LRI_EQUIPE_INTERNE_SUPPRIMEE')
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

			$EquipesInternes = $objIndispoPersonnel->rechercherEquipes( $sct_id, $Trier );
			$Total = $objIndispoPersonnel->RowCount;

			$Texte_HTML = '';

			foreach ($EquipesInternes as $Occurrence) {
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->ein_id, $Occurrence, $Format_Colonnes );
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