<?php

/**
* Ce script gère les Campagnes.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-01-15
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );


// Charge les informations et droits de base d'un écran.
include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Entites.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Applications.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Fournisseurs.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Sites.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Sites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Applications_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Fournisseurs_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_MatriceImpacts_PDO.inc.php' );


// Crée l'instance de l'objet Entites.
$objCampagnes = new Campagnes();
$objSocietes = new HBL_Societes();
$objEntites = new HBL_Entites();
$objSites = new Sites();
$objApplications = new Applications();
$objFournisseurs = new Fournisseurs();
$objMatriceImpacts = new MatriceImpacts();


// Définit le format des colonnes du tableau central.
$Trier = 'cmp_date';

$Format_Colonnes[ 'Prefixe' ] = 'CMP';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'cmp_id' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'cmp_date', 'titre' => $L_Date, 'taille' => '2', 'maximum' => '10',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'cmp_date-desc', 'type' => 'input-date', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'cmp_flag_validation', 'titre' => $L_Validation, 'taille' => '2', 'triable' => 'oui',
	'tri_actif' => 'oui', 'sens_tri' => 'cmp_date-desc', 'type' => 'select', 'liste' => '0='.$L_No.';1='.$L_Yes, 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'associations', 'titre' => $L_Associations, 'affichage' => 'img',
	'taille' => '6' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'dupliquer' => $Droit_Ajouter, 'supprimer' => $Droit_Supprimer ) );


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

	$Fichiers_JavaScript[] = 'MatriceImpacts.js';

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Campagnes, $Fichiers_JavaScript, '3' ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Campagnes, $Liste_Societes, $Boutons_Alternatifs )
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
		'L_Titre_Ajouter' => $L_Ajouter_Campagne,
		'L_Titre_Modifier' => $L_Modifier_Campagne,
		'L_Titre_Supprimer' => $L_Supprimer_Campagne,
		'L_Titre_Dupliquer' => $L_Dupliquer_Campagne,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Dupliquer' => $L_Dupliquer,
		'L_Rechercher' => $L_Rechercher,
		'L_Validation' => $L_Validation,
		'L_Libelle' => $L_Label,
		'L_Nom' => $L_Nom,
		'L_Description' => $L_Description,
		'L_Administrateur' => $L_Administrateur,
		'is_super_admin' => $_SESSION['idn_super_admin'],
		'L_Niveaux_Appreciation' => $L_Niveaux_Appreciation,
		'L_Sites' => $L_Sites,
		'L_Matrice_Impacts' => $L_Matrice_Impacts,
		'L_Entites' => $L_Entites,
		'L_Echelles_Temps' => $L_Echelles_Temps,
		'L_Applications' => $L_Applications,
		'L_Fournisseurs' => $L_Fournisseurs,
		'L_Date' => $L_Date,
		'L_Poids' => $L_Poids,
		'L_Niveau_Service' => $L_Niveau_Service,
		'L_Hebergement' => $L_Hebergement,
		'L_Type' => $L_Type,
		'L_Go_Echelle_Temps' => $L_Go_Echelle_Temps,
		'L_Go_Matrice_Impacts' => $L_Go_Matrice_Impacts,
		'L_Source' => $L_Source,
		'L_Cible' => $L_Cible,
		'L_A_Partir_Precedente_Campagne' => $L_A_Partir_Precedente_Campagne,
		'L_Oui' => $L_Yes,
		'L_Non' => $L_No,
		'L_Type' => $L_Type,
		'L_Niveau' => $L_Niveau,
		'Droits_Entites' => $PageHTML->permissionsGroupees('MySecDash-Entites.php'),
		'Droits_Sites' => $PageHTML->permissionsGroupees('MyContinuity-Sites.php')
		
	);

	if ( isset( $_POST['cmp_id'] ) ) {
		if ( $Droit_Lecture === TRUE || $PageHTML->verifierCampagneAutorisee($_POST['cmp_id']) ) {
			if ( $_POST['cmp_id'] != '' ) {
				$Libelles['objCampagne'] = $objCampagnes->detaillerCampagne( $_POST['cmp_id'] );
				$Libelles['Liste_Campagnes'] = $objCampagnes->rechercherCampagnes( $_SESSION['s_sct_id'], 'cmp_date-desc' );
				$Libelles['Liste_Entites'] = $objCampagnes->rechercherEntitesAssocieesCampagne( $_SESSION['s_sct_id'], $_POST['cmp_id'] );
				$Libelles['Liste_Sites'] = $objCampagnes->rechercherSitesCampagne( $_SESSION['s_sct_id'], $_POST['cmp_id'] );
				$Libelles['Liste_Echelle_Temps'] = $objCampagnes->rechercherEchelleTempsCampagne( $_POST['cmp_id'] );
				$Libelles['Liste_Types_Fournisseur'] = $objFournisseurs->rechercherTypesFournisseur();
				$Libelles['Liste_Niveaux_Impact'] = $objCampagnes->rechercherNiveauxImpactCampagne( $_POST['cmp_id'] );
				$Libelles['Liste_Types_Impact'] = $objCampagnes->rechercherTypesImpactCampagne( $_POST['cmp_id'] );
				$Libelles['Liste_Matrice_Impacts'] = $objMatriceImpacts->rechercherMatriceImpactsParID( $_POST['cmp_id'] );
				
			} else {
				$Libelles['Liste_Campagnes'] = $objCampagnes->rechercherCampagnes( $_SESSION['s_sct_id'], 'cmp_date-desc' );
				$Libelles['Liste_Entites'] = $objEntites->rechercherEntites( $_SESSION['s_sct_id'] );
				$Libelles['Liste_Sites'] = $objSites->rechercherSites( $_SESSION['s_sct_id'] );
				$Libelles['Liste_Types_Fournisseur'] = $objFournisseurs->rechercherTypesFournisseur();
			}
		} else {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_No_Authorize
			) );
		}
	}
	
	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['cmp_date']) ) {
			if ($_POST['cmp_date'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );

				echo json_encode( $Resultat );
				exit();
			}
			
			$_POST['cmp_date'] = $PageHTML->controlerTypeValeur( $_POST['cmp_date'], 'ASCII' );
			if ( $_POST['cmp_date'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmp_date)'
				) );
				
				exit();
			}

			try {
				$objCampagnes->majCampagne( $_SESSION['s_sct_id'], '', $_POST[ 'cmp_date' ] );
				$Id_Campagne= $objCampagnes->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $Id_Campagne . '", cmp_date="' . $_POST[ 'cmp_date' ] . '"' );


				if (isset($_POST['liste_ent_ajouter'])) {
					if ($_POST['liste_ent_ajouter'] != []) {
						foreach($_POST['liste_ent_ajouter'] as $ent_id) {
							$objCampagnes->associerEntiteCampagne($Id_Campagne, $ent_id);
							
							$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $Id_Campagne . '" => ent_id="' . $ent_id . '"' );
						}
					}

					$Total_Entites = count($_POST['liste_ent_ajouter']);

					if (strlen($Total_Entites) == 1) $Total_Entites = '0' . $Total_Entites;
				} else {
					$Total_Entites = '00';
				}


				if (isset($_POST['liste_app_ajouter'])) {
					if ($_POST['liste_app_ajouter'] != []) {
						foreach($_POST['liste_app_ajouter'] as $app_id) {
							$objApplications->ajouterApplicationCampagne($app_id, $Id_Campagne);
							
							$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $Id_Campagne . '" => app_id="' . $app_id . '"' );
						}
					}

					$Total_Applications = count($_POST['liste_app_ajouter']);

					if (strlen($Total_Applications) == 1) $Total_Applications = '0' . $Total_Applications;
				} else {
					$Total_Applications = '00';
				}


				if (isset($_POST['liste_frn_ajouter'])) {
					if ($_POST['liste_frn_ajouter'] != []) {
						foreach($_POST['liste_frn_ajouter'] as $frn_id) {
							$objFournisseurs->ajouterFournisseurCampagne($frn_id, $Id_Campagne);
							
							$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $Id_Campagne . '" => frn_id="' . $frn_id . '"' );
						}
					}
					
					$Total_Fournisseurs = count($_POST['liste_frn_ajouter']);
					
					if (strlen($Total_Fournisseurs) == 1) $Total_Fournisseurs = '0' . $Total_Fournisseurs;
				} else {
					$Total_Fournisseurs = '00';
				}


				if (isset($_POST['liste_sts_ajouter'])) {
					if ($_POST['liste_sts_ajouter'] != []) {
						foreach($_POST['liste_sts_ajouter'] as $sts_id) {
							$objCampagnes->associerSiteCampagne($Id_Campagne, $sts_id);
							
							$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $Id_Campagne . '" => sts_id="' . $sts_id . '"' );
						}
					}
					
					$Total_Sites = count($_POST['liste_sts_ajouter']);
					
					if (strlen($Total_Sites) == 1) $Total_Sites = '0' . $Total_Sites;
				} else {
					$Total_Sites = '00';
				}
				

				$Valeurs = new stdClass();
				$Valeurs->cmp_date = $_POST['cmp_date'];
				$Valeurs->cmp_flag_validation = $L_No;
				
				$Valeurs->associations = '<button type="button" class="btn btn-warning btn-sm btn-espace btn-entites" title="' . $L_Entites . '">' . $Total_Entites . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-sites" title="' . $L_Sites . '">' . $Total_Sites . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-echelles-temps" title="' . $L_Echelles_Temps . '">00</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-matrices-impacts" title="' . $L_Matrices_Impacts . '">00</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-activites" title="' . $L_Activites . '">' . sprintf('%02d', $Occurrence->total_act) . '</button>';
				
				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_Campagne, $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Campagne_Cree,
					'texte' => $Occurrence,
					'id' => $Id_Campagne,
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
	
	
 case 'AJAX_Dupliquer':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['cmp_id']) and isset($_POST['cmp_date']) ) {
			$_POST['cmp_id'] = $PageHTML->controlerTypeValeur( $_POST['cmp_id'], 'NUMERIC' );
			if ( $_POST['cmp_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmp_id)'
				) );
				
				exit();
			}

			$_POST['p_cmp_date'] = $PageHTML->controlerTypeValeur( $_POST['p_cmp_date'], 'ASCII' );
			if ( $_POST['p_cmp_date'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (p_cmp_date)'
				) );

				exit();
			}

			$_POST['cmp_date'] = $PageHTML->controlerTypeValeur( $_POST['cmp_date'], 'ASCII' );
			if ( $_POST['cmp_date'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmp_date)'
				) );

				exit();
			}

			try {
				$Id_Campagne = $objCampagnes->dupliquerCampagne( $_POST['cmp_id'], $_POST[ 'cmp_date' ] );

				$PageHTML->ecrireEvenement( 'ATP_DUPLICATION', 'OTP_CAMPAGNE',
					'cmp_id="' . $_POST[ 'cmp_id' ] . '" => cmp_id="' . $Id_Campagne . '", cmp_date="' . $_POST[ 'cmp_date' ] . '" => cmp_date="' . $_POST[ 'cmp_date' ] . '"' );


				$Campagne = $objCampagnes->rechercherCampagnes( $_SESSION['s_sct_id'], $Trier, $Id_Campagne );
				$Occurrence = $Campagne[0];

				// Matérialise si l'utilisateur a été déscativé.
				if ( $Occurrence->cmp_flag_validation == TRUE ) $Occurrence->cmp_flag_validation = $L_Yes;
				else $Occurrence->cmp_flag_validation = $L_No;
					
				$Occurrence->associations = '<button type="button" class="btn btn-warning btn-sm btn-espace btn-entites" title="' . $L_Entites . '">' . sprintf('%02d', $Occurrence->total_ent) . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-sites" title="' . $L_Sites . '">' . sprintf('%02d', $Occurrence->total_sts) . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-echelles-temps" title="' . $L_Echelles_Temps . '">' . sprintf('%02d', $Occurrence->total_ete) . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-matrices-impacts" title="' . $L_Matrices_Impacts . '">' . sprintf('%02d', $Occurrence->total_mim) . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-activites" title="' . $L_Activites . '">' . sprintf('%02d', $Occurrence->total_act) . '</button>';
					
				$Texte_HTML = $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->cmp_id, $Occurrence, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Campagne_Dupliquee,
					'texte' => $Texte_HTML,
					'id' => $Id_Campagne,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Campagne;
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
		if ( isset($_POST['cmp_date']) && isset($_POST['cmp_validation']) ) {
			if ( ! $PageHTML->verifierCampagneAutorisee($_POST['cmp_id']) ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $L_No_Authorize . ' (cmp_id)' );
				
				echo json_encode( $Resultat );
				exit();
			}

			if ($_POST['cmp_date'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . ' (cmp_date)' );
				
				echo json_encode( $Resultat );
				exit();
			}


			if ($_POST['cmp_validation'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . ' (cmp_validation)' );
				
				echo json_encode( $Resultat );
				exit();
			}


			if (isset($_POST['liste_ent_ajouter'])) {
				if ($_POST['liste_ent_ajouter'] != []) {
					foreach($_POST['liste_ent_ajouter'] as $ent_id) {
						$objCampagnes->associerEntiteCampagne($_POST[ 'cmp_id' ], $ent_id);

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST[ 'cmp_id' ] . '" => ent_id="' . $ent_id . '"' );
					}
				}
			}


			if (isset($_POST['liste_ent_supprimer'])) {
				if ($_POST['liste_ent_supprimer'] != []) {
					foreach($_POST['liste_ent_supprimer'] as $ent_id) {
						$objCampagnes->dissocierEntiteCampagne($_POST[ 'cmp_id' ], $ent_id);
					
						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST[ 'cmp_id' ] . '" => ent_id="' . $ent_id . '"' );
					}
				}
			}


			if (isset($_POST['liste_app_ajouter'])) {
				if ($_POST['liste_app_ajouter'] != []) {
					foreach($_POST['liste_app_ajouter'] as $app_id) {
						$objCampagnes->associerApplicationCampagne($_POST[ 'cmp_id' ], $app_id);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST[ 'cmp_id' ] . '" => app_id="' . $app_id . '"' );
					}
				}
			}


			if (isset($_POST['liste_app_supprimer'])) {
				if ($_POST['liste_app_supprimer'] != []) {
					foreach($_POST['liste_app_supprimer'] as $app_id) {
						$objCampagnes->dissocierApplicationCampagne($_POST[ 'cmp_id' ], $app_id);
						
						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST[ 'cmp_id' ] . '" => app_id="' . $app_id . '"' );
					}
				}
			}


			if (isset($_POST['liste_frn_ajouter'])) {
				if ($_POST['liste_frn_ajouter'] != []) {
					foreach($_POST['liste_frn_ajouter'] as $frn_id) {
						$objCampagnes->associerFournisseurCampagne($_POST[ 'cmp_id' ], $frn_id);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST[ 'cmp_id' ] . '" => frn_id="' . $frn_id . '"' );
					}
				}
			}


			if (isset($_POST['liste_frn_supprimer'])) {
				if ($_POST['liste_frn_supprimer'] != []) {
					foreach($_POST['liste_frn_supprimer'] as $frn_id) {
						$objCampagnes->dissocierFournisseurCampagne($_POST[ 'cmp_id' ], $frn_id);
						
						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST[ 'cmp_id' ] . '" => frn_id="' . $frn_id . '"' );
					}
				}
			}


			if (isset($_POST['liste_sts_ajouter'])) {
				if ($_POST['liste_sts_ajouter'] != []) {
					foreach($_POST['liste_sts_ajouter'] as $sts_id) {
						$objCampagnes->associerSiteCampagne($_POST[ 'cmp_id' ], $sts_id);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST[ 'cmp_id' ] . '" => sts_id="' . $sts_id . '"' );
					}
				}
			}


			if (isset($_POST['liste_sts_supprimer'])) {
				if ($_POST['liste_sts_supprimer'] != []) {
					foreach($_POST['liste_sts_supprimer'] as $sts_id) {
						$objCampagnes->dissocierSiteCampagne($_POST[ 'cmp_id' ], $sts_id);
						
						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST[ 'cmp_id' ] . '" => sts_id="' . $sts_id . '"' );
					}
				}
			}


			$_POST['cmp_date'] = $PageHTML->controlerTypeValeur( $_POST['cmp_date'], 'ASCII' );
			if ( $_POST['cmp_date'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmp_date)'
				) );
				
				exit();
			}


			$_POST['cmp_validation'] = $PageHTML->controlerTypeValeur( $_POST['cmp_validation'], 'NUMERIC' );
			if ( $_POST['cmp_validation'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmp_validation)'
				) );
				
				exit();
			} else {
				if ( $_POST['cmp_validation'] == 0 ) {
					$_POST['cmp_validation'] = FALSE;
				} else {
					$_POST['cmp_validation'] = TRUE;
				}
			}


			try {
				$objCampagnes->majCampagne( $_SESSION['s_sct_id'], $_POST[ 'cmp_id' ], $_POST[ 'cmp_date' ], $_POST['cmp_validation'] );
				
				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_CAMPAGNE', 'sct_id="' . $_SESSION['s_sct_id'] . '", cmp_id="' . $_POST[ 'cmp_id' ] . '", cmp_date="' . $_POST[ 'cmp_date' ] . '"' );
				
				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Campagne_Modifiee,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);
				
				if ( isset($_POST['liste_ajouter']) ) {
					
				}
			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();
				
				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Campagne;
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
		if ( isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur']) ){
			if ( ! $PageHTML->verifierCampagneAutorisee($_POST['id']) ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $L_No_Authorize . ' (cmp_id)['.__LINE__.']' );

				echo json_encode( $Resultat );
				exit();
			}

			try {
				$objCampagnes->majCampagneParChamp($_SESSION['s_sct_id'], $_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_CAMPAGNE', 'sct_id="' . $_SESSION['s_sct_id'] . '", cmp_id="' . $_POST['id'] . '", ' . $_POST['source'] . '="' . $_POST[ 'valeur' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Campagne_Modifiee
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Campagne;
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
			if ( ! $PageHTML->verifierCampagneAutorisee($_POST['id']) ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $L_No_Authorize . ' (cmp_id)['.__LINE__.']' );
				
				echo json_encode( $Resultat );
				exit();
			}

			try {
				$objCampagnes->supprimerCampagne( $_POST['id'] );

				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_CAMPAGNE', 'cmp_id="' . $_POST['id'] . '", cmp_date="' . $_POST[ 'libelle' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Campagne_Supprimee,
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
			$Campagnes = $objCampagnes->rechercherCampagnes( $sct_id, $Trier );
			$Total = $objCampagnes->RowCount;

			$Texte_HTML = '';

			foreach ($Campagnes as $Occurrence) {
				// Matérialise si l'utilisateur a été déscativé.
				if ( $Occurrence->cmp_flag_validation == TRUE ) $Occurrence->cmp_flag_validation = $L_Yes;
				else $Occurrence->cmp_flag_validation = $L_No;
				
				$Occurrence->associations = '<button type="button" class="btn btn-warning btn-sm btn-espace btn-entites" title="' . $L_Entites . '">' . sprintf('%02d', $Occurrence->total_ent) . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-sites" title="' . $L_Sites . '">' . sprintf('%02d', $Occurrence->total_sts) . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-echelles-temps" title="' . $L_Echelles_Temps . '">' . sprintf('%02d', $Occurrence->total_ete) . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-matrices-impacts" title="' . $L_Matrices_Impacts . '">' . sprintf('%02d', $Occurrence->total_mim) . '</button>' .
					'<button type="button" class="btn btn-warning btn-sm btn-espace btn-activites" title="' . $L_Activites . '">' . sprintf('%02d', $Occurrence->total_act) . '</button>';
					
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->cmp_id, $Occurrence, $Format_Colonnes );
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
			$Compteurs = $objCampagnes->CampagneEstAssociee( $_POST['id'] );

			$CodeHTML = '';

			if ( $Compteurs->total_dma > 0 || $Compteurs->total_sts > 0
			|| $Compteurs->total_ppr > 0 || $Compteurs->total_mim > 0
			|| $Compteurs->total_ete > 0
			|| $Compteurs->total_act > 0 || $Compteurs->total_ent > 0 ) {
				$CodeHTML .= sprintf( $L_Campagne_Est_Associee, $_POST['libelle'] ) .
					'<ul style="margin-top: 10px;">';

				if ( isset( $Compteurs->total_dma ) ) {
					if ( $Compteurs->total_dma > 1 ) $Libelle = $L_DMIA_Court;
					else $Libelle = $L_DMIA_Court;
					
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_dma . '</span> ' . $Libelle . '</li>';
				}

				if ( isset( $Compteurs->total_sts ) ) {
					if ( $Compteurs->total_sts > 1 ) $Libelle = $L_Sites;
					else $Libelle = $L_Site;
					
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_sts . '</span> ' . $Libelle . '</li>';
				}

				if ( isset( $Compteurs->total_act ) ) {
					if ( $Compteurs->total_act > 1 ) $Libelle = $L_Activites;
					else $Libelle = $L_Activite;
						
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_act . '</span> ' . $Libelle . '</li>';
				}

				if ( isset( $Compteurs->total_mim ) ) {
					if ( $Compteurs->total_mim > 1 ) $Libelle = $L_Matrices_Impacts;
					else $Libelle = $L_Matrice_Impacts;
						
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_mim . '</span> ' . $Libelle . '</li>';
				}

				if ( isset( $Compteurs->total_ent ) ) {
					if ( $Compteurs->total_ent > 1 ) $Libelle = $L_Entites;
					else $Libelle = $L_Entite;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_ent . '</span> ' . $Libelle . '</li>';
				}

				if ( isset( $Compteurs->total_ete ) ) {
					if ( $Compteurs->total_ete > 1 ) $Libelle = $L_Echelles_Temps;
					else $Libelle = $L_Echelle_Temps;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_ete . '</span> ' . $Libelle . '</li>';
				}
				
				if ( isset( $Compteurs->total_app ) ) {
					if ( $Compteurs->total_app > 1 ) $Libelle = $L_Applications;
					else $Libelle = $L_Application;

					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_app . '</span> ' . $Libelle . '</li>';
				}

				if ( isset( $Compteurs->total_frn ) ) {
					if ( $Compteurs->total_frn > 1 ) $Libelle = $L_Fournisseurs;
					else $Libelle = $L_Fournisseur;
					
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_frn . '</span> ' . $Libelle . '</li>';
				}

				if ( isset( $Compteurs->total_ppr ) ) {
					if ( $Compteurs->total_ppr > 1 ) $Libelle = $L_Parties_Prenantes;
					else $Libelle = $L_Partie_Prenante;
					
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_ppr . '</span> ' . $Libelle . '</li>';
				}

				$CodeHTML .= '</ul>' . sprintf( $L_Confirmer_Suppression_Campagne, '<span class="orange_moyen fw-bold">' . $_POST['libelle'] . '</span>' );
			} else {
				$CodeHTML .= sprintf( $L_Confirmer_Suppression_Campagne, '<span class="orange_moyen fw-bold">' . $_POST['libelle'] . '</span>' );
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


 case 'AJAX_Ajouter_Entite':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['ent_nom']) ) {
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

			$_POST['ent_description'] = $PageHTML->controlerTypeValeur( $_POST['ent_description'], 'ASCII' );
			if ( $_POST['ent_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ent_description)'
				) );

				exit();
			}

			try {
				$objEntites->majEntite( $_SESSION['s_sct_id'], '', $_POST[ 'ent_nom' ],
						$_POST[ 'ent_description' ] );
				$Id_Entite = $objEntites->LastInsertId;
			} catch( Exception $e ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $e->getMessage()
				) );

				exit();
			}
			echo json_encode( array(
				'statut' => 'success',
				'texteMsg' => $L_Entite_Cree,
				'ent_id' => $Id_Entite
			) );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Ajouter_Application':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['app_nom']) && isset($_POST['app_hebergement'])
			&& isset($_POST['app_niveau_service']) ) {
			if ($_POST['app_nom'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . ' (' . $_POST['app_nom'] . ')' );
				
				echo json_encode( $Resultat );
				exit();
			}

			if ($_POST['app_hebergement'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . ' (' . $_POST['app_hebergement'] . ')' );
				
				echo json_encode( $Resultat );
				exit();
			}

			if ($_POST['app_niveau_service'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . ' (' . $_POST['app_niveau_service'] . ')' );
				
				echo json_encode( $Resultat );
				exit();
			}

			$_POST['app_nom'] = $PageHTML->controlerTypeValeur( $_POST['app_nom'], 'ASCII' );
			if ( $_POST['app_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_nom)'
				) );

				exit();
			}

			$_POST['app_hebergement'] = $PageHTML->controlerTypeValeur( $_POST['app_hebergement'], 'ASCII' );
			if ( $_POST['app_hebergement'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_hebergement)'
				) );

				exit();
			}

			$_POST['app_niveau_service'] = $PageHTML->controlerTypeValeur( $_POST['app_niveau_service'], 'ASCII' );
			if ( $_POST['app_niveau_service'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_niveau_service)'
				) );

				exit();
			}

			$_POST['cmp_id'] = $PageHTML->controlerTypeValeur( $_POST['cmp_id'], 'NUMBER' );
			if ( $_POST['cmp_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmp_id)'
				) );

				exit();
			}


			try {
				$objApplications->majApplication( '', $_POST['app_nom'], $_POST['app_hebergement'],
					$_POST['app_niveau_service'] );
				$Id_Application = $objApplications->LastInsertId;
			} catch( Exception $e ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $e->getMessage()
				) );

				exit();
			}
			echo json_encode( array(
				'statut' => 'success',
				'texteMsg' => $L_Application_Cree,
				'app_id' => $Id_Application
			) );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Ajouter_Fournisseur':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['frn_nom']) && isset($_POST['tfr_id']) && isset($_POST['frn_description']) ) {
			if ($_POST['frn_nom'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . '(frn_nom)' );

				echo json_encode( $Resultat );
				exit();
			}

			if ($_POST['frn_description'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . '(frn_description)' );
				
				echo json_encode( $Resultat );
				exit();
			}

			if ($_POST['tfr_id'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory . '(tfr_id)' );
				
				echo json_encode( $Resultat );
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

			$_POST['frn_description'] = $PageHTML->controlerTypeValeur( $_POST['frn_description'], 'ASCII' );
			if ( $_POST['frn_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_description)'
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

			try {
				$objFournisseurs->majFournisseur( '', $_POST[ 'tfr_id' ], $_POST[ 'frn_nom' ],
					$_POST[ 'frn_description' ] );
				$Id_Fournisseur = $objFournisseurs->LastInsertId;
			} catch( Exception $e ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $e->getMessage()
				) );
				
				exit();
			}
			echo json_encode( array(
				'statut' => 'success',
				'texteMsg' => $L_Fournisseur_Cree,
				'frn_id' => $Id_Fournisseur
			) );
		} else {
			$Resultat = array( 'statut' => 'error',
				'titreMsg' => $L_Error,
				'texteMsg' => $L_Field_Mandatory . '[frn_nom, tfr_id, frn_description]' );
			
			echo json_encode( $Resultat );
			exit();

		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;
	
	
 case 'AJAX_Ajouter_Site':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['sts_nom']) ) {
			if ($_POST['sts_nom'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );

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
				$objSites->majSite( $_SESSION['s_sct_id'], $_POST[ 'sts_nom' ], $_POST[ 'sts_description' ] );
				$Id_Site = $objSites->LastInsertId;
			} catch( Exception $e ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $e->getMessage()
				) );

				exit();
			}
			echo json_encode( array(
				'statut' => 'success',
				'texteMsg' => $L_Site_Cree,
				'sts_id' => $Id_Site
			) );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;
	
}
?>