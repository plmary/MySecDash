<?php

/**
* Ce script gère les Sociétés.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \date 2024-10-10
* \note Check OK 2024-10-10
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Societes.inc.php' );

// Charge les classes utiles à cet écran.
include_once( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );


$objSocietes = new HBL_Societes();


$Trier = 'sct_nom';


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'SCT';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'sct_id' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'sct_nom', 'titre' => $L_Societe, 'taille' => '4', 'maximum' => '100',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'sct_nom', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'sct_description', 'titre' => $L_Description, 'taille' => '6',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'sct_description', 'type' => 'textarea', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Societes, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Societes, '', $Boutons_Alternatifs )
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
		'L_Titre_Ajouter' => $L_Ajouter_Societe,
		'L_Titre_Modifier' => $L_Modifier_Societe,
		'L_Titre_Supprimer' => $L_Supprimer_Societe,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Nom' => $L_Nom,
		'L_Description' => $L_Description,
		'L_Administrateur' => $L_Administrateur,
		'is_super_admin' => $_SESSION['idn_super_admin'] );

	if ( isset($_POST['sct_id']) ) {
		if ( $_POST['sct_id'] != '' ) {
			if ( $PageHTML->verifierSocieteAutorisee($_POST['sct_id']) ) {
				$Libelles['Societe'] = $objSocietes->detaillerSociete( $_POST['sct_id'] );
			} else {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' ) ) );

				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' );

				exit();
			}
		}
	}

	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if (isset($_POST['sct_nom'])) {
			if ($_POST['sct_nom'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );

				echo json_encode( $Resultat );
				exit();
			}

			$_POST['sct_nom'] = $PageHTML->controlerTypeValeur( $_POST['sct_nom'], 'ASCII' );
			if ( $_POST['sct_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sct_nom)'
				) );
				
				exit();
			}

			$_POST['sct_description'] = $PageHTML->controlerTypeValeur( $_POST['sct_description'], 'ASCII' );
			if ( $_POST['sct_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sct_description)'
				) );
				
				exit();
			}
			
			
			try {
				$objSocietes->majSociete( '', $_POST[ 'sct_nom' ], $_POST[ 'sct_description' ] );
				$sct_id = $objSocietes->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_SOCIETE', 'sct_id="' . $sct_id . '", sct_nom="' . $_POST[ 'sct_nom' ] . '", sct_description="' . $_POST[ 'sct_description' ] . '"' );

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $sct_id, $_POST['sct_nom'], $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Societe_Creee,
					'texte' => $Occurrence,
					'id' => $sct_id,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Societe;
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
		if (isset($_POST['sct_nom']) and isset($_POST['sct_id'])) {
		if ( ! $PageHTML->verifierSocieteAutorisee($_POST['sct_id']) ) {
			print( json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' ) ) );

			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' );

			exit();
		}

			$_POST['sct_nom'] = $PageHTML->controlerTypeValeur( $_POST['sct_nom'], 'ASCII' );
			if ( $_POST['sct_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sct_nom)'
				) );

				exit();
			}

			$_POST['sct_description'] = $PageHTML->controlerTypeValeur( $_POST['sct_description'], 'ASCII' );
			if ( $_POST['sct_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sct_description)'
				) );

				exit();
			}

			$_POST['sct_id'] = $PageHTML->controlerTypeValeur( $_POST['sct_id'], 'NUMERIC' );
			if ( $_POST['sct_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sct_id)'
				) );

				exit();
			}

			try {
				$objSocietes->majSociete( $_POST[ 'sct_id' ], $_POST[ 'sct_nom' ], $_POST[ 'sct_description' ] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_SOCIETE', 'sct_id="' . $_POST['sct_id'] . '", sct_nom="' . $_POST[ 'sct_nom' ] . '", sct_description="' . $_POST[ 'sct_description' ] . '"' );

//				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $_POST['sct_id'], $_POST['sct_nom'], $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Societe_Modifiee,
//					'texte' => $Occurrence,
					'id' => $_POST['sct_id'],
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Societe;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
				);
			}

			echo json_encode( $Resultat );
		} else {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			) );
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
		if (isset($_POST['id']) && isset($_POST['valeur'])){
			if ( ! $PageHTML->verifierSocieteAutorisee($_POST['id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['id'] . '")'.' [' . __LINE__ . ']' ) ) );
				
				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['id'] . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}

			try {
				$objSocietes->majSociete($_POST['id'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_SOCIETE', 'sct_id="' . $_POST['id'] . '", sct_nom="' . $_POST[ 'valeur' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Societe_Modifiee
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Societe;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
					);
			}

			echo json_encode( $Resultat );
		} else {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			) );
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
		if ( isset($_POST['sct_id']) ) {
			if ( ! $PageHTML->verifierSocieteAutorisee($_POST['sct_id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' ) ) );
				
				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}

			try {
				$Societe = $objSocietes->detaillerSociete( $_POST['sct_id'] );
				$objSocietes->supprimerSociete( $_POST['sct_id'] );

				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_SOCIETE', 'sct_id="' . $_POST['sct_id'] . '", sct_nom="' . $Societe->sct_nom . '", sct_description="' . $Societe->sct_description . '"' );

				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Societe_Supprimee
					);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $e->getMessage() );
			}

			echo json_encode( $Resultat );
		} else {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			) );
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

		try {
			$Societes = $objSocietes->rechercherSocietes( $Trier );
			$Total = $objSocietes->RowCount;

			$Texte_HTML = '';

			foreach ($Societes as $Occurrence) {
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->sct_id, $Occurrence, $Format_Colonnes );
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
		if ( isset($_POST['sct_id']) ) {
			try { 
				$Compteurs = $objSocietes->SocieteEstAssociee( $_POST['sct_id'] );
	
				$CodeHTML = '';
	
				if ( $Compteurs->total_idn != 0 || $Compteurs->total_cmp != 0 || $Compteurs->total_ppr != 0 ) {
					$CodeHTML .= sprintf( $L_Confirmation_Suppression_Societe_Associee, $Compteurs->sct_nom ) . '<ul style="margin-top: 10px;">';
	
					if ( isset( $Compteurs->total_idn ) ) {
						if ( $Compteurs->total_idn > 1 ) $Libelle = $L_Utilisateurs;
						else $Libelle = $L_Utilisateur;
	
						$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_idn . '</span> ' . $Libelle . '</li>';
					}
	
					if ( isset( $Compteurs->total_cmp ) ) {
						if ( $Compteurs->total_cmp > 1 ) $Libelle = $L_Campagnes;
						else $Libelle = $L_Campagne;
	
						$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_cmp . '</span> ' . $Libelle . '</li>';
					}
					
					if ( isset( $Compteurs->total_ppr ) ) {
						if ( $Compteurs->total_ppr > 1 ) $Libelle = $L_Parties_Prenantes;
						else $Libelle = $L_Partie_Prenante;
						
						$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_ppr . '</span> ' . $Libelle . '</li>';
					}
					
					$CodeHTML .= '</ul>' . $L_Cascading_Delete;
				} else {
					$CodeHTML .= sprintf( $L_Confirmation_Suppression_Societe, $Compteurs->sct_nom );
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
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	echo json_encode( $Resultat );

	break;
}
?>