<?php

/**
* Ce script gère les Applications.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-08-28
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Fournisseurs.php' );
#include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Applications.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Fournisseurs_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_EchellesTemps_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_MatriceImpacts_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_Applications_PDO.inc.php' );


// Crée une instance de l'objet HTML.
$objCampagnes = new Campagnes();
$objSocietes = new HBL_Societes();
$objApplications = new Applications();
$objFournisseurs = new Fournisseurs();
$objEchellesTemps = new EchellesTemps();
$objMatriceImpacts = new MatriceImpacts();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'APP';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'app_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'app_nom', 'titre' => $L_Nom, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'app_nom', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'app_nom_alias', 'titre' => $L_Alias, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'app_nom_alias', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'act_noms', 'titre' => $L_Activites, 'taille' => '5',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'act_noms', 'type' => 'textarea', 'modifiable' => 'non', 'affichage' => 'img' );
/*$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_nom_dima', 'titre' => $L_DMIA_Court, 'taille' => '1',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ete_nom_code_dima', 'type' => 'input', 'modifiable' => 'non', );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_nom_pdma', 'titre' => $L_PDMA_Court, 'taille' => '1',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ete_nom_code_pdma', 'type' => 'input', 'modifiable' => 'non', );*/
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_nom_dima_dsi', 'titre' => $L_DMIA_SI, 'taille' => '1',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ete_nom_code_dima_dsi', 'type' => 'select', 'modifiable' => 'oui', 'fonction' => 'listerEchelleTemps');
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_nom_pdma_dsi', 'titre' => $L_PDMA_SI, 'taille' => '1',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'ete_nom_code_pdma_dsi', 'type' => 'select', 'modifiable' => 'oui', 'fonction' => 'listerEchelleTemps' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '1', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );

$Droit_Ajouter_Fournisseurs = $PageHTML->controlerPermission('MyContinuity-Fournisseurs.php', 'RGH_2');


// Exécute l'action identifie
switch( $Action ) {
 default:
	$Liste_Societes = '';

	// Initialise les listes déroulantes : Sociétés, Campagnes et Entités
	try {
		list($Liste_Societes, $Liste_Campagnes) = actualiseSocieteCampagne($objSocietes, $objCampagnes);
	} catch( Exception $e ) {
		print('<h1 class="text-urgent">' . $e->getMessage() . '</h1>');
		break;
	}


	$Choix_Campagnes['id'] = 's_cmp_id';
	$Choix_Campagnes['libelle'] = $L_Campagnes;
	$Choix_Campagnes['desactive'] = 'oui';
	
	if ( $Liste_Campagnes != '' ) {
		foreach( $Liste_Campagnes AS $Campagne ) {
			$Choix_Campagnes['options'][] = array('id' => $Campagne->cmp_id, 'nom' => $Campagne->cmp_date );
		}
	}


	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Applications_SI, $Fichiers_JavaScript, '3' ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Applications_SI, $Liste_Societes, $Boutons_Alternatifs, $Choix_Campagnes )
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
		'L_Creer' => $L_Creer,
		'L_Application' => $L_Application,
		'L_Libelle' => $L_Label,
		'L_Localisation' => $L_Localisation,
		'L_Type' => $L_Type,
		'L_Nom' => $L_Nom,
		'L_Hebergement' => $L_Hebergement,
		'L_Niveau_Service' => $L_Niveau_Service,
		'L_Description' => $L_Description,
		'L_Fournisseur' => $L_Fournisseur,
		'L_Aucun' => $L_Neither,
		'Liste_Types_Fournisseur' => $objFournisseurs->rechercherTypesFournisseur(),
		'Droit_Ajouter_Fournisseurs' => $Droit_Ajouter_Fournisseurs,
		'L_Specifique_A' => $L_Specifique_A,
		'L_Oui' => $L_Yes,
		'L_Non' => $L_No,
		'L_Alias' => $L_Alias,
		'L_DMIA' => $L_DMIA_Court,
		'L_PDMA' => $L_PDMA_Court,
		'L_DMIA_DSI' => $L_DMIA_SI,
		'L_PDMA_DSI' => $L_PDMA_SI
	);

	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['app_id']) and $_POST['app_id'] != '') {
			$Application = $objApplications->rechercherApplicationsSI( 'app_nom', $_SESSION['s_sct_id'], $_POST['app_id'] );
			$Libelles['Application'] = $Application[0];
			$Libelles['Liste_Fournisseurs'] = listerFournisseurs($Application[0]->frn_id);
			$Libelles['Liste_Societes'] = listerSocietes($Application[0]->sct_id);
		} else {
			$Libelles['Liste_Fournisseurs'] = listerFournisseurs();
			$Libelles['Liste_Societes'] = listerSocietes();
		}
		$Libelles['Liste_Echelles'] = $objEchellesTemps->rechercherEchellesTemps($_SESSION['s_sct_id']);
	}

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['app_nom']) ) {

			$_POST['app_nom'] = $PageHTML->controlerTypeValeur( $_POST['app_nom'], 'ASCII' );
			if ( $_POST['app_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_nom)'
				) );

				exit();
			}

			$_POST['app_nom_alias'] = $PageHTML->controlerTypeValeur( $_POST['app_nom_alias'], 'ASCII' );
			if ( $_POST['app_nom_alias'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_nom_alias)'
				) );

				exit();
			}

			$_POST['frn_id'] = $PageHTML->controlerTypeValeur( $_POST['frn_id'], 'NUMERIC' );
			if ( $_POST['frn_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_id)'
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

			$_POST['app_description'] = $PageHTML->controlerTypeValeur( $_POST['app_description'], 'ASCII' );
			if ( $_POST['app_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_description)'
				) );

				exit();
			}

			$_POST['ete_id_dima_dsi'] = $PageHTML->controlerTypeValeur( $_POST['ete_id_dima_dsi'], 'NUMERIC' );
			if ( $_POST['ete_id_dima_dsi'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ete_id_dima_dsi)'
				) );

				exit();
			}

			$_POST['libelle_ete_id_dima_dsi'] = $PageHTML->controlerTypeValeur( $_POST['libelle_ete_id_dima_dsi'], 'ASCII' );
			if ( $_POST['libelle_ete_id_dima_dsi'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (libelle_ete_id_dima_dsi)'
				) );

				exit();
			}

			$_POST['scap_description_dima'] = $PageHTML->controlerTypeValeur( $_POST['scap_description_dima'], 'ASCII' );
			if ( $_POST['cmap_description_dima'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmap_description_dima)'
				) );

				exit();
			}

			$_POST['ete_id_pdma_dsi'] = $PageHTML->controlerTypeValeur( $_POST['ete_id_pdma_dsi'], 'NUMERIC' );
			if ( $_POST['ete_id_pdma_dsi'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ete_id_pdma_dsi)'
				) );

				exit();
			}

			$_POST['libelle_ete_id_pdma_dsi'] = $PageHTML->controlerTypeValeur( $_POST['libelle_ete_id_pdma_dsi'], 'ASCII' );
			if ( $_POST['libelle_ete_id_pdma_dsi'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (libelle_ete_id_pdma_dsi)'
				) );

				exit();
			}

			$_POST['scap_description_pdma'] = $PageHTML->controlerTypeValeur( $_POST['scap_description_pdma'], 'ASCII' );
			if ( $_POST['cmap_description_pdma'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cmap_description_pdma)'
				) );

				exit();
			}


			try {
				$objApplications->majApplication( '', $_POST['app_nom'], $_POST['frn_id'], $_POST['app_hebergement'],
					$_POST['app_niveau_service'], $_POST['app_description'], $_POST['sct_id'], $_POST['app_nom_alias']);

				$_POST['app_id'] = $objApplications->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_APPLICATION', 'app_id="' . $_POST['app_id'] . '", ' .
					'app_nom="' . $_POST[ 'app_nom' ] . '" app_nom_alias="' . $_POST[ 'app_nom_alias' ] . '", frn_id="' . $_POST[ 'frn_id' ] . '", app_hebergement="' . $_POST[ 'app_hebergement' ] . '", ' .
					'app_niveau_service="' . $_POST[ 'app_niveau_service' ] . '", app_description="' . $_POST[ 'app_description' ] . '", ' .
					'sct_id="' . $_POST['app_id'] . '"');

				$objApplications->majApplicationSI( $_POST['app_id'], $_SESSION['s_cmp_id'],
					$_POST['ete_id_dima_dsi'], $_POST['scap_description_dima'],
					$_POST['ete_id_pdma_dsi'], $_POST['scap_description_pdma']);

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_APPLICATION', 'app_id="' . $_POST['app_id'] . '", ' .
					'cmp_id="' . $_SESSION[ 's_cmp_id' ] . '" ete_id_dima_dsi="' . $_POST[ 'ete_id_dima_dsi' ] . '", ' .
					'scap_description_dima="' . $_POST[ 'scap_description_dima' ] . '", ete_id_pdma_dsi="' . $_POST[ 'ete_id_pdma_dsi' ] . '", ' .
					'scap_description_pdma="' . $_POST[ 'scap_description_pdma' ] . '"');

				$Valeurs = new stdClass();
				$Valeurs->app_nom = $_POST[ 'app_nom' ];
				$Valeurs->app_nom_alias = $_POST[ 'app_nom_alias' ];
				$Valeurs->act_noms = '';
				$Valeurs->ete_nom_dima_dsi = $_POST['libelle_ete_id_dima_dsi'];
				$Valeurs->ete_nom_pdma_dsi = $_POST['libelle_ete_id_pdma_dsi'];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $_POST['app_id'], $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Application_Cree,
					'texte' => $Occurrence,
					'id' => $_POST['app_id'],
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
			$_POST['id'] = $PageHTML->controlerTypeValeur( $_POST['id'], 'NUMERIC' );
			if ( $_POST['id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_id=' . $_POST['id'] . ')'
				) );

				exit();
			}

			$_POST['source'] = $PageHTML->controlerTypeValeur( $_POST['source'], 'ASCII' );
			if ( $_POST['source'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (' . $_POST['source'] . ')'
				) );

				exit();
			}

			$_POST['valeur'] = $PageHTML->controlerTypeValeur( $_POST['valeur'], 'ASCII' );
			if ( $_POST['valeur'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (' . $_POST['valeur'] . ')'
				) );

				exit();
			}

			try {
				$objApplications->majApplicationSIParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

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
			$_POST['id'] = $PageHTML->controlerTypeValeur( $_POST['id'], 'NUMERIC' );
			if ( $_POST['id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_id=' . $_POST['id'] . ')'
				) );
				
				exit();
			}

			try  {
				$objApplications->supprimerApplication( $_POST['id'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_APPLICATION', 'app_id="' . $_POST['id'] . '", ' .
					'libelle="' . $_POST[ 'libelle' ] . '"' );
	
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
			$ListeApplications = $objApplications->rechercherApplicationsSI( $Trier, $_SESSION['s_sct_id'] );
			$Total = $objApplications->RowCount;

			$Liste_Campagnes = $objCampagnes->rechercherCampagnes( $_SESSION['s_sct_id' ], 'cmp_date-desc' );
			$Derniere_Campagne = $Liste_Campagnes[0]->cmp_id;

			$ListeMatriceImpactParChamp = $objMatriceImpacts->rechercherMatriceImpactsParChamp( $Derniere_Campagne, 'nim_poids' );
			$ListeEchelleTempsParChamp = $objEchellesTemps->rechercherEchellesTempsParChamp( $_SESSION['s_sct_id'], 'ete_poids' );

			$Liste_Echelles = $objEchellesTemps->rechercherEchellesTemps( $_SESSION['s_sct_id'] );
			foreach( $Liste_Echelles as $Occurrence ) {
				$Liste_Echelles[$Occurrence->ete_poids] = $Occurrence;
			}

			$Texte_HTML = '';

			foreach ($ListeApplications as $Occurrence) {
				$Afficher_Activites = '';

				if ( $Occurrence->act_noms != '' ) {
					$_cpt = 0;

					$_ent_nom = '';
					$_ent_description = '';
					$_act_nom = '';
					$_nim_poids = '';
					$_ete_poids = '';

					foreach( explode('###', $Occurrence->act_noms) as $Activite ) {
						$t_Activite = explode('===', $Activite);

						if ($_cpt == 0 ) {
							$_ent_nom = $t_Activite[0];
							$_ent_description = $t_Activite[1];
							$_act_id = $t_Activite[2];
							$_act_nom = $t_Activite[3];
							$_act_dima = $t_Activite[4];
							$_act_pdma = $t_Activite[5];
						} else {
							if ( $_ent_nom != $t_Activite[0] || $_ent_description != $t_Activite[1] || $_act_nom != $t_Activite[2] ) {
								list($_nim_poids, $_ete_poids) = $objApplications->recupererPoidsNiveauImpactEtEchelleTemps( $_act_id );

								if ($Afficher_Activites != '') $Afficher_Activites .= ',<br>';
								$Afficher_Activites .= $_ent_nom . ($_ent_description != '' ? ' - ' . $_ent_description : '') . ' - ' . $_act_nom . ' (<span class="fw-bold" style="color: #' . $ListeMatriceImpactParChamp[$_nim_poids]->nim_couleur . '">' . $ListeMatriceImpactParChamp[$_nim_poids]->nim_numero . ' - ' . $ListeMatriceImpactParChamp[$_nim_poids]->nim_nom_code . '</span> / ' . ($_ete_poids > 0 ? $ListeEchelleTempsParChamp[$_ete_poids]->ete_nom_code : '') . ') [DIMA : <b>'.$_act_dima.'</b> , PDMA : <b>'.$_act_pdma.'</b>]';

								$_ent_nom = $t_Activite[0];
								$_ent_description = $t_Activite[1];
								$_act_nom = $t_Activite[2];
								$_nim_poids = $t_Activite[3];
								$_ete_poids = $t_Activite[4];
							} else {
								if ($t_Activite[3] > $_nim_poids ) {
									$_nim_poids = $t_Activite[3];
									$_ete_poids = $t_Activite[4];
								}
							}
						}

						$_cpt += 1;
					}

					if ( $_cpt > 0 && $Afficher_Activites == '' ) {
						list($_nim_poids, $_ete_poids) = $objApplications->recupererPoidsNiveauImpactEtEchelleTemps( $_act_id );

						$Afficher_Activites .= $_ent_nom . ($_ent_description != '' ? ' - ' . $_ent_description : '') . ' - ' . $_act_nom . ' (<span class="fw-bold" style="color: #' . $ListeMatriceImpactParChamp[$_nim_poids]->nim_couleur . '">' . $ListeMatriceImpactParChamp[$_nim_poids]->nim_numero . ' - ' . $ListeMatriceImpactParChamp[$_nim_poids]->nim_nom_code . '</span> / ' . ($_ete_poids > 0 ? $ListeEchelleTempsParChamp[$_ete_poids]->ete_nom_code : '') . ') [DIMA : <b>'.$_act_dima.'</b> , PDMA : <b>'.$_act_pdma.'</b>]';
					}
				}

				$Occurrence->act_noms = $Afficher_Activites;

				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->app_id, $Occurrence, $Format_Colonnes );
			}

			if ( $_SESSION['idn_super_admin'] === TRUE ) {
				$Flag_Admin = TRUE;
			} else {
				$Flag_Admin = FALSE;
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
				'flag_admin' => $Flag_Admin
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


 case 'AJAX_Selectioner_Societe':
	if ( isset($_POST['sct_id']) ) {
		if ( ! $PageHTML->verifierSocieteAutorisee($_POST['sct_id']) ) {
			print( json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' ) ) );

			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' );

			exit();
		}

		$_SESSION['s_sct_id'] = $_POST['sct_id'];

		$Liste_Campagnes = $objCampagnes->rechercherCampagnes($_POST['sct_id']);

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Societe_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'Liste_Campagnes' => $Liste_Campagnes
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (sct_id)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Charger':
	if ( $Droit_Lecture === TRUE ) {
		try {
			$Application = $objApplications->detaillerApplication( $_SESSION['s_cmp_id'], '', $_POST['app_id'] );
	
			echo json_encode( array(
				'statut' => 'success',
				'app_nom' => $Application->app_nom,
				'app_hebergement' => $Application->app_hebergement,
				'app_niveau_service' => $Application->app_niveau_service,
				'app_description' => $Application->app_description
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
		if ( isset($_POST['app_id']) && isset($_POST['app_nom']) ) {

			$_POST['app_nom'] = $PageHTML->controlerTypeValeur( $_POST['app_nom'], 'ASCII' );
			if ( $_POST['app_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_nom)'
				) );

				exit();
			}

			$_POST['app_nom_alias'] = $PageHTML->controlerTypeValeur( $_POST['app_nom_alias'], 'ASCII' );
			if ( $_POST['app_nom_alias'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_nom_alias)'
				) );
				
				exit();
			}

			$_POST['frn_id'] = $PageHTML->controlerTypeValeur( $_POST['frn_id'], 'NUMERIC' );
			if ( $_POST['frn_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_id)'
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

			$_POST['app_description'] = $PageHTML->controlerTypeValeur( $_POST['app_description'], 'ASCII' );
			if ( $_POST['app_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_description)'
				) );

				exit();
			}

			$_POST['ete_id_dima_dsi'] = $PageHTML->controlerTypeValeur( $_POST['ete_id_dima_dsi'], 'NUMERIC' );
			if ( $_POST['ete_id_dima_dsi'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ete_id_dima_dsi)'
				) );
				
				exit();
			}

			$_POST['libelle_ete_id_dima_dsi'] = $PageHTML->controlerTypeValeur( $_POST['libelle_ete_id_dima_dsi'], 'ASCII' );
			if ( $_POST['libelle_ete_id_dima_dsi'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (libelle_ete_id_dima_dsi)'
				) );
				
				exit();
			}

			$_POST['scap_description_dima'] = $PageHTML->controlerTypeValeur( $_POST['scap_description_dima'], 'ASCII' );
			if ( $_POST['scap_description_dima'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (scap_description_dima)'
				) );
				
				exit();
			}

			$_POST['ete_id_pdma_dsi'] = $PageHTML->controlerTypeValeur( $_POST['ete_id_pdma_dsi'], 'NUMERIC' );
			if ( $_POST['ete_id_pdma_dsi'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ete_id_pdma_dsi)'
				) );
				
				exit();
			}

			$_POST['libelle_ete_id_pdma_dsi'] = $PageHTML->controlerTypeValeur( $_POST['libelle_ete_id_pdma_dsi'], 'ASCII' );
			if ( $_POST['libelle_ete_id_pdma_dsi'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (libelle_ete_id_pdma_dsi)'
				) );
				
				exit();
			}

			$_POST['scap_description_pdma'] = $PageHTML->controlerTypeValeur( $_POST['scap_description_pdma'], 'ASCII' );
			if ( $_POST['scap_description_pdma'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (scap_description_pdma)'
				) );
				
				exit();
			}


			try {
				$objApplications->majApplication( $_POST['app_id'], $_POST['app_nom'], $_POST['frn_id'], $_POST['app_hebergement'],
					$_POST['app_niveau_service'], $_POST['app_description'], $_SESSION['s_sct_id'], $_POST['app_nom_alias']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_APPLICATION', 'app_id="' . $_POST['app_id'] . '", ' .
					'app_nom="' . $_POST[ 'app_nom' ] . '" app_nom_alias="' . $_POST[ 'app_nom_alias' ] . '", frn_id="' . $_POST[ 'frn_id' ] . '", app_hebergement="' . $_POST[ 'app_hebergement' ] . '", ' .
					'app_niveau_service="' . $_POST[ 'app_niveau_service' ] . '", app_description="' . $_POST[ 'app_description' ] . '", ' .
					'sct_id="' . $_SESSION['s_sct_id'] . '", app_nom_alias="' . $_POST['app_nom_alias'] . '"');

				$objApplications->majApplicationSI( $_POST['app_id'], $_SESSION['s_sct_id'],
					$_POST['ete_id_dima_dsi'], $_POST['scap_description_dima'],
					$_POST['ete_id_pdma_dsi'], $_POST['scap_description_pdma']);
				
				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_APPLICATION', 'app_id="' . $_POST['app_id'] . '", ' .
					'sct_id="' . $_SESSION[ 's_sct_id' ] . '" ete_id_dima_dsi="' . $_POST[ 'ete_id_dima_dsi' ] . '", ' .
					'scap_description_dima="' . $_POST[ 'scap_description_dima' ] . '", ete_id_pdma_dsi="' . $_POST[ 'ete_id_pdma_dsi' ] . '", ' .
					'scap_description_pdma="' . $_POST[ 'scap_description_pdma' ] . '"');
				
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


 case 'AJAX_Verifier_Associer':
	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['id']) ) {
			try { 
				$Compteurs = $objApplications->controlerAssociationApplication( $_POST['id'] );
	
				$CodeHTML = '';
	
				if ( $Compteurs->total_act > 0 ) {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_Application_Associee, $_POST['libelle'] ) .
						'<ul style="margin-top: 10px;">';
	
					if ( $Compteurs->total_act > 1 ) $Libelle = $L_Activites;
					else $Libelle = $L_Activite;
	
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_act . '</span> ' . $Libelle . '</li>';
	
					$CodeHTML .= '</ul>';
				} else {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_Application, $_POST['libelle'] );
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


 case 'AJAX_listerFournisseurs':
	if ( $Droit_Lecture === TRUE ) {
		$Resultat = array(
		'statut' => 'success',
		'texteMsg' => listerFournisseurs( $_POST['id'], $_POST['libelle'] )
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
	
	
 case 'AJAX_listerEchelleTemps':
	if ( $Droit_Lecture === TRUE ) {
		$Resultat = array(
			'statut' => 'success',
			'texteMsg' => listerEchelleTemps( $_POST['id'], $_POST['libelle'] )
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


 case 'AJAX_listerSocietes':
	if ( $Droit_Lecture === TRUE ) {
		$Resultat = array(
			'statut' => 'success',
			'texteMsg' => listerSocietes( $_POST['id'], $_POST['libelle'] )
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


 case 'AJAX_Ajouter_Fournisseur':
	if ( $Droit_Lecture === TRUE ) {
		try {
			$_POST['tfr_id'] = $PageHTML->controlerTypeValeur( $_POST['tfr_id'], 'NUMERIC' );
			if ( $_POST['tfr_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tfr_id)'
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
	
			$objFournisseurs->majFournisseur( '', $_POST['tfr_id'], $_POST['frn_nom'], '' );
	
			
			$Resultat = array(
				'statut' => 'success',
				'texteMsg' => $L_Fournisseur_Cree,
				'frn_id' => $objFournisseurs->LastInsertId
			);
	
		} catch( Exception $e ) {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
			);
		}
	
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
}


function listerFournisseurs( $Init_Id = '', $Init_Libelle = '' ) {
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

	$objFournisseurs = new Fournisseurs();
	
	$Liste = $objFournisseurs->rechercherFournisseurs();

	$Code_HTML = '<option value="">' . $L_Neither . '</option>';
	
	foreach ($Liste as $Occurrence) {
		if ( $Init_Id != '' ) {
			if ( $Init_Id == $Occurrence->frn_id or $Init_Libelle == $Occurrence->frn_nom ) {
				$Selected = ' selected';
			} else {
				$Selected = '';
			}
		} else {
			$Selected = '';
		}
		
		$Code_HTML .= '<option value="' . $Occurrence->frn_id . '"' . $Selected . '>' . $Occurrence->frn_nom . '</option>' ;
	}
	
	return $Code_HTML;
}


function listerSocietes( $Init_Id = '', $Init_Libelle = '' ) {
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

	$objSocietes = new HBL_Societes();

	$Liste = $objSocietes->rechercherSocietes();

	$Code_HTML = '<option value="">' . $L_Neither . '</option>';

	foreach ($Liste as $Occurrence) {
		if ( $Init_Id != '' ) {
			if ( $Init_Id == $Occurrence->sct_id or $Init_Libelle == $Occurrence->sct_nom ) {
				$Selected = ' selected';
			} else {
				$Selected = '';
			}
		} else {
			$Selected = '';
		}

		$Code_HTML .= '<option value="' . $Occurrence->sct_id . '"' . $Selected . '>' . $Occurrence->sct_nom . '</option>' ;
	}

	return $Code_HTML;
}


function listerEchelleTemps( $Init_Id = '', $Init_Libelle = '' ) {
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	
	$objEchelleTemps = new EchellesTemps();
	
	$Liste = $objEchelleTemps->rechercherEchellesTemps($_SESSION['s_sct_id']);
	
	$Code_HTML = '<option value="">' . $L_Neither . '</option>';
	
	foreach ($Liste as $Occurrence) {
		if ( $Init_Id != '' ) {
			if ( $Init_Id == $Occurrence->ete_id or $Init_Libelle == $Occurrence->ete_nom_code ) {
				$Selected = ' selected';
			} else {
				$Selected = '';
			}
		} else {
			$Selected = '';
		}
		
		$Code_HTML .= '<option value="' . $Occurrence->ete_id . '"' . $Selected . '>' . $Occurrence->ete_nom_code . '</option>' ;
	}
	
	return $Code_HTML;
}





function actualiseSocieteCampagne($objSocietes, $objCampagnes, $forcer=0) {
	/**
	 * Actualise les listes Sociétés, Campagnes et Entités à l'entrée dans l'écran et en cas de changement.
	 *
	 * \license Copyleft Loxense
	 * \author Pierre-Luc MARY
	 * \date 2024-10-01
	 *
	 * \param[in] $objSocietes Objet permettant d'accéder aux fonctions de gestion des Sociétés
	 * \param[in] $objCampagnes Objet permettant d'accéder aux fonctions de gestion des Campagnes
	 * \param[in] $forcer Flag permettant de forcer le résultat (0=Tout charger, 1=Changer Société, 2=Changer Campagne)
	 *
	 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	 */
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	
	$Liste_Societes = [];
	$Liste_Campagnes = [];
	
	
	switch ( $forcer ) {
		case 1:
			// Comme on vient de change de Société, on efface les variables de Session qui pointaient :
			//   sur une Campagne
			//   et sur une Entité.
			// Ainsi, on forcera le repositionnement sur la première occurrence de Campagne et d'Entité.
			unset($_SESSION['s_cmp_id']);
			unset($_SESSION['s_ent_id']);
			break;
			
		case 2:
			// Comme on vient de change de Campagne, on efface la variable de Session qui pointait sur une Entité.
			// Ainsi, on forcera le repositionnement sur la première occurrence d'Entité.
			unset($_SESSION['s_ent_id']);
			break;
	}
	
	
	// Récupère les Sociétés accessibles pour l'Utilisateur
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Societes = $objSocietes->rechercherSocietes();
		if ( $Liste_Societes == [] ) {
			// Si pas de société, alors on efface tout et on lève une exception.
			$_SESSION['s_sct_id'] = '';
			$_SESSION['s_cmp_id'] = '';
			
			throw new Exception($L_Plus_De_Societe, 0);
		} else {
			// Si la variable de Session n'est pas initialisé, alors on l'initialise sur la première occurrence de notre résultat.
			if ( ! isset($_SESSION['s_sct_id']) or $_SESSION['s_sct_id'] == '' ) {
				$_SESSION['s_sct_id'] = $Liste_Societes[0]->sct_id;
			}
		}
	} else {
		$Liste_Societes = $objSocietes->rechercherSocietes('', '', $_SESSION['idn_id']);
		if ( $Liste_Societes == [] ) {
			// Si pas de société, alors on efface tout et on lève une exception.
			$_SESSION['s_sct_id'] = '';
			$_SESSION['s_cmp_id'] = '';
			
			throw new Exception($L_Pas_Societe_Autorisee_Pour_Utilisateur, 0);
		} else {
			if ( ! isset($_SESSION['s_sct_id']) or $_SESSION['s_sct_id'] == '' ) {
				$_SESSION['s_sct_id'] = $Liste_Societes[0]->sct_id;
			} else {
				// On contrôle que l'utilisateur a encore accès à cette Société, sinon on lève une exception.
				$_Autorise = 0;
				foreach ($Liste_Societes as $_Tmp) {
					if ( $_Tmp->sct_id == $_SESSION['s_sct_id'] ) {
						$_Autorise = 1;
						break;
					}
				}
				
				if ( $_Autorise == 0 ) {
					throw new Exception($L_Societe_Plus_Autorisee_Pour_Utilisateur, 0);
				}
			}
		}
	}
	
	// Récupère les Campagnes associées à la Société Sélectionnée.
	$Liste_Campagnes = $objCampagnes->rechercherCampagnes($_SESSION['s_sct_id'], 'cmp_date-desc');
	if ( $Liste_Campagnes == [] ) {
		$tmpObj1 = new stdClass();
		$tmpObj1->cmp_id = '';
		$tmpObj1->cmp_date = '---';
		$Liste_Campagnes[0] = $tmpObj1;
		
		$tmpObj2 = new stdClass();
		$tmpObj2->ent_id = '';
		$tmpObj2->ent_nom = '---';
		$tmpObj2->total_activites = 0;
		$Liste_Entites[0] = $tmpObj2;
		
		$_SESSION['s_cmp_id'] = '';
		$_SESSION['s_ent_id'] = '';
		
		return [$Liste_Societes, $Liste_Campagnes];
	} else {
		if ( ! isset($_SESSION['s_cmp_id']) or $_SESSION['s_cmp_id'] == '' ) {
			$_SESSION['s_cmp_id'] = $Liste_Campagnes[0]->cmp_id;
		} else {
			// On contrôle que l'utilisateur a encore accès à cette Société.
			$_Autorise = 0;
			
			foreach ($Liste_Campagnes as $_Tmp) {
				if ( $_Tmp->cmp_id == $_SESSION['s_cmp_id'] ) {
					$_Autorise = 1;
					break;
				}
			}
			
			if ( $_Autorise == 0 ) {
				$_SESSION['s_cmp_id'] = $Liste_Campagnes[0]->cmp_id;
			}
		}
	}
	
	//print($_SESSION['s_sct_id'].' - '.$_SESSION['s_cmp_id'].' - '.$_SESSION['s_ent_id'].'<hr>');
	
	return [$Liste_Societes, $Liste_Campagnes];
}

?>