<?php

/**
* Ce script gère les Activités.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-03-05
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-Entites.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-PartiesPrenantes.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Campagnes.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Sites.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Applications.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Fournisseurs.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );


// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Identites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Civilites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Activites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Sites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_PartiesPrenantes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_EchellesTemps_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_MatriceImpacts_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Applications_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Fournisseurs_PDO.inc.php' );


// Crée une instance de l'objet HTML.
$objSocietes = new HBL_Societes();
$objIdentites = new HBL_Identites();
$objCivilites = new HBL_Civilites();
$objEntites = new HBL_Entites();
$objCampagnes = new Campagnes();
$objActivites = new Activites();
$objPartiesPrenantes = new PartiesPrenantes();
$objSites = new Sites();
$objEchellesTemps = new EchellesTemps();
$objMatriceImpacts = new MatriceImpacts();
$objApplications = new Applications();
$objFournisseurs = new Fournisseurs();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'ACT';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'act_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'act_nom', 'titre' => $L_Nom, 'taille' => '4',
	'maximum' => 100, 'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'act_nom', 'type' => 'input',
	'modifiable' => 'oui' );
//$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ppr_id_responsable', 'titre' => $L_CPCA, 'taille' => '3',
//	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ppr_id_responsable', 'type' => 'select', 'modifiable' => 'oui', 'fonction' => 'listerPartiesPrenantes' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'nim_poids', 'titre' => $L_Niveau_Impact,
	'affichage' => 'img', 'taille' => '2', 'triable' => 'oui', 'sens_tri' => 'nim_poids' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_poids', 'titre' => $L_DMIA,
	'affichage' => 'img', 'taille' => '2', 'triable' => 'oui', 'sens_tri' => 'ete_poids' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'association', 'titre' => $L_Associations,
	'affichage' => 'img', 'taille' => '2' );
//$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'act_description', 'titre' => $L_Description, 'taille' => '3',
//	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'act_description', 'type' => 'textarea', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'dupliquer' => $Droit_Ajouter, 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Vérification de droits spécifiques
$Droit_Ajouter_Personnes_Cles = $PageHTML->controlerPermission('MyContinuity-PartiesPrenantes.php', 'RGH_2');
$Droit_Ajouter_Sites = $PageHTML->controlerPermission('MyContinuity-Sites.php', 'RGH_2');
$Droit_Ajouter_Applications = $PageHTML->controlerPermission('MyContinuity-Applications.php', 'RGH_2');
$Droit_Ajouter_Fournisseurs = $PageHTML->controlerPermission('MyContinuity-Fournisseurs.php', 'RGH_2');


// Exécute l'action identifiée
switch( $Action ) {
 default:
	$Liste_Societes = '';
	$Liste_Campagnes = '';
	$Liste_Entites = '';

	// Initialise les listes déroulantes : Sociétés, Campagnes et Entités
	try {
		list($Liste_Societes, $Liste_Campagnes, $Liste_Entites) = 
			actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites);
	} catch( Exception $e ) {
		print('<h1 class="text-urgent">' . $e->getMessage() . '</h1>');
		break;
	}

	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];


	$Choix_Campagnes['id'] = 's_cmp_id';
	$Choix_Campagnes['libelle'] = $L_Campagnes;

	if ( $Liste_Campagnes != '' ) {
		foreach( $Liste_Campagnes AS $Campagne ) {
			$Choix_Campagnes['options'][] = array('id' => $Campagne->cmp_id, 'nom' => $Campagne->cmp_date );
		}
	}


	$Choix_Entites['id'] = 's_ent_id';
	$Choix_Entites['libelle'] = $L_Entites;

	if ( $Liste_Entites != '' ) {
		foreach( $Liste_Entites AS $Entite ) {
			$Choix_Entites['options'][] = array('id' => $Entite->ent_id, 'nom' => $Entite->ent_nom );
		}
	}
//print_r($Choix_Entites);

	$Fichiers_JavaScript[] = 'MatriceImpacts.js';

	print $PageHTML->construireEnteteHTML( $L_Gestion_Activites, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Activites, $Liste_Societes, $Boutons_Alternatifs,
			$Choix_Campagnes, '', $Choix_Entites );
//print_r($_SERVER);

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
	$Libelles = array( 'statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'L_Titre_Ajouter' => $L_Ajouter_Activite,
		'L_Titre_Modifier' => $L_Modifier_Activite,
		'L_Titre_Supprimer' => $L_Supprimer_Activite,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Creer' => $L_Creer,
		'L_Nom' => $L_Nom,
		'L_CPCA' => $L_CPCA,
		'L_Date_Validation' => $L_Date_Validation,
		'L_Validation' => $L_Validation,
		'L_Libelle' => $L_Label,
		'L_Description' => $L_Description,
		'L_Role' => $L_Role,
		'L_Oui' => $L_Yes,
		'L_Non' => $L_No,
		'L_Site_Nominal' => $L_Site_Nominal,
		'L_Site_Secours' => $L_Site_Secours,
		'L_Besoins_Continuite_Activite' => $L_Besoins_Continuite_Activite,
		'L_DMIA' => $L_DMIA,
		'L_PDMA' => $L_PDMA,
		'L_Libelle_DMIA' => $L_Libelle_DMIA,
		'L_Libelle_PDMA' => $L_Libelle_PDMA,
		'L_Palliatif' => $L_Palliatif,
		'L_Responsable_Activite' => $L_Responsable_Activite,
		'L_Suppleant' => $L_Suppleant,
		'L_Cartouche' => $L_Cartouche,
		'L_Ajouter_PartiePrenante' => $L_Ajouter_PartiePrenante,
		'L_Prenom' => $L_Prenom,
		'L_Interne' => $L_Interne,
		'L_Aucun' => $L_Neither,
		'L_Activite_Teletravaillable' => $L_Activite_Teletravaillable,
		'L_Niveau' => $L_Niveau,
		'L_Type' => $L_Type,
		'L_Sites' => $L_Sites,
		'L_Personnes_Cles' => $L_Personnes_Cles,
		'L_Applications' => $L_Applications,
		'L_Fournisseurs' => $L_Fournisseurs,
		'L_Interdependances' => $L_Interdependances,
		'L_Rechercher' => $L_Rechercher,
		'L_Niveau_Impact' => $L_Niveau_Impact,
		'L_Hebergement' => $L_Hebergement,
		'L_Niveau_Service' => $L_Niveau_Service,
		'L_ERR_Champs_Obligatoires' => $L_ERR_Champs_Obligatoires,
		'L_ERR_Champ_Obligatoire' => $L_ERR_Champ_Obligatoire,
		'L_Donnees' => $L_Donnees,
		'L_Dependances_Internes_Amont' => $L_Dependances_Internes_Amont,
		'L_Dependances_Internes_Aval' => $L_Dependances_Internes_Aval,
		'L_Justification_DMIA' => $L_Justification_DMIA,
		'L_Consequence_Indisponibilite' => $L_Consequence_Indisponibilite,
		'Liste_Civilites' => $objCivilites->rechercherCivilites(),
		'Liste_Identites' => $objIdentites->rechercherIdentites( $_SESSION['s_sct_id'] ),
		'Liste_Sites' => $objActivites->rechercherSitesAssociesActivite( $_SESSION['s_sct_id'], 0 ), //rechercherSitesCampagne( $_SESSION['s_cmp_id'] ),
		'Liste_EchellesTemps' => $objEchellesTemps->rechercherEchellesTemps($_SESSION['s_cmp_id']),
		'Liste_Niveaux_Impact' => $objCampagnes->rechercherNiveauxImpactCampagne( $_SESSION['s_cmp_id'] ),
		'Liste_Types_Impact' => $objCampagnes->rechercherTypesImpactCampagne( $_SESSION['s_cmp_id'] ),
		'Liste_Matrice_Impacts' => $objMatriceImpacts->rechercherMatriceImpactsParID( $_SESSION['s_cmp_id'] ),
		'Liste_Parties_Prenantes' => $objPartiesPrenantes->rechercherPartiesPrenantes( $_SESSION['s_sct_id'] ),
		'Liste_Personnes_Cles' => $objActivites->rechercherPersonnesClesActivites( $_SESSION['s_sct_id'] ),
		'Liste_Applications' => $objActivites->rechercherApplicationsActivites( 0 ),
		'Liste_Fournisseurs' => $objActivites->rechercherFournisseursActivite( 0 ),
		'Liste_Types_Fournisseur' => $objFournisseurs->rechercherTypesFournisseur(),
		'L_Effectifs_A_Distance' => $L_Effectifs_A_Distance,
		'L_Effectifs_En_Nominal' => $L_Effectifs_En_Nominal,
		'Droit_Ajouter_Personnes_Cles' => $Droit_Ajouter_Personnes_Cles,
		'Droit_Ajouter_Sites' => $Droit_Ajouter_Sites,
		'Droit_Ajouter_Applications' => $Droit_Ajouter_Applications,
		'Droit_Ajouter_Fournisseurs' => $Droit_Ajouter_Fournisseurs,
		'L_Dupliquer_Activite' => $L_Dupliquer_Activite,
		'L_Dupliquer' => $L_Dupliquer,
		'L_Nouveau_Nom' => $L_Nouveau_Nom,
		'L_Informations_Complementaires_A_Dupliquer' => $L_Informations_Complementaires_A_Dupliquer,
		'L_Tout_Cocher_Decocher' => $L_Tout_Cocher_Decocher
	);

	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['act_id']) and $_POST['act_id'] != '') {
			if ( $PageHTML->verifierActiviteAutorisee( $_POST['act_id'] ) ) {
				$Libelles['Activite'] = $objActivites->rechercherActivites( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'], '', $_POST['act_id'] );
				$Libelles['Liste_DMIA'] = $objActivites->recupererDMIA( $_SESSION['s_cmp_id'], $_POST['act_id'] );
				$Libelles['Liste_Personnes_Cles'] = $objActivites->rechercherPersonnesClesActivites( $_SESSION['s_sct_id'], $_POST['act_id'] );
				$Libelles['Liste_Sites'] = $objActivites->rechercherSitesAssociesActivite( $_SESSION['s_cmp_id'], $_POST['act_id'] );
				$Libelles['Liste_Applications'] = $objActivites->rechercherApplicationsActivites( $_POST['act_id'] );
				$Libelles['Liste_Fournisseurs'] = $objActivites->rechercherFournisseursActivite( $_POST['act_id'] );
			} else {
				$Libelles = array( 'statut' => 'error', 'texteMsg' => $L_No_Authorize );
			}
		}
	}

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['act_nom']) && isset($_POST['ppr_id_responsable']) && isset($_POST['act_teletravail'])) {

			$_POST['act_nom'] = $PageHTML->controlerTypeValeur( $_POST['act_nom'], 'ASCII' );
			if ( $_POST['act_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_nom)'
				) );
				
				exit();
			}

			$_POST['ppr_id_responsable'] = $PageHTML->controlerTypeValeur( $_POST['ppr_id_responsable'], 'ASCII' );
			if ( $_POST['ppr_id_responsable'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_id_responsable)'
				) );
				
				exit();
			}

			$_POST['ppr_id_suppleant'] = $PageHTML->controlerTypeValeur( $_POST['ppr_id_suppleant'], 'ASCII' );
			if ( $_POST['ppr_id_suppleant'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_id_suppleant)'
				) );
				
				exit();
			}

			$_POST['act_description'] = $PageHTML->controlerTypeValeur( $_POST['act_description'], 'ASCII' );
			if ( $_POST['act_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_description)'
				) );
				
				exit();
			}

			$_POST['act_effectifs_en_nominal'] = $PageHTML->controlerTypeValeur( $_POST['act_effectifs_en_nominal'], 'NUMERIC' );
			if ( $_POST['act_effectifs_en_nominal'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_effectifs_en_nominal)'
				) );

				exit();
			}

			$_POST['act_effectifs_a_distance'] = $PageHTML->controlerTypeValeur( $_POST['act_effectifs_a_distance'], 'NUMERIC' );
			if ( $_POST['act_effectifs_a_distance'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_effectifs_a_distance)'
				) );

				exit();
			}

			$_POST['act_teletravail'] = $PageHTML->controlerTypeValeur( $_POST['act_teletravail'], 'NUMERIC' );
			if ( $_POST['act_teletravail'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_teletravail)'
				) );
				
				exit();
			}

			$_POST['act_dependances_internes_amont'] = $PageHTML->controlerTypeValeur( $_POST['act_dependances_internes_amont'], 'ASCII' );
			if ( $_POST['act_dependances_internes_amont'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_dependances_internes_amont)'
				) );
				
				exit();
			}

			$_POST['act_dependances_internes_aval'] = $PageHTML->controlerTypeValeur( $_POST['act_dependances_internes_aval'], 'ASCII' );
			if ( $_POST['act_dependances_internes_aval'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_dependances_internes_aval)'
				) );
				
				exit();
			}

			$_POST['act_justification_dmia'] = $PageHTML->controlerTypeValeur( $_POST['act_justification_dmia'], 'ASCII' );
			if ( $_POST['act_justification_dmia'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_justification_dmia)'
				) );
				
				exit();
			}

			try {
				$_fonctionCourante = 'majActivite';
				$objActivites->majActivite( '', $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'], $_POST['ppr_id_responsable'],
					$_POST['ppr_id_suppleant'], $_POST['act_nom'], $_POST['act_description'],
					$_POST['act_effectifs_en_nominal'], $_POST['act_effectifs_a_distance'],
					$_POST['act_teletravail'], $_POST['act_dependances_internes_amont'],
					$_POST['act_dependances_internes_aval'], $_POST['act_justification_dmia']);

				$Id_Activite = $objActivites->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE', 'act_id="' . $Id_Activite .
					'", ent_id="' . $_SESSION['s_ent_id'] . '", ppr_id_responsable="' . $_POST['ppr_id_responsable'] .
					'", ppr_id_suppleant="' . $_POST['ppr_id_suppleant'] . '", act_nom="' . $_POST['act_nom'] .
					'", act_description="' . $_POST['act_description'] .
					'", act_teletravail="' . $_POST['act_teletravail'] .
					'", act_dependances_internes_amont="' . $_POST['act_dependances_internes_amont'] .
					'", act_dependances_internes_aval="' . $_POST['act_dependances_internes_aval'] .
					'", act_justification_dmia="' . $_POST['act_justification_dmia'] . '"'
					);
				


				$_fonctionCourante = 'ajouterSiteRattachementActivite';
				foreach( $_POST['sites_a_ajouter'] as $Site ) {
					$objActivites->ajouterSiteRattachementActivite( $_SESSION['s_cmp_id'], $Id_Activite, $Site[0], $Site[1] );

					$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE', 'cmp_id="' . $_SESSION['s_cmp_id'] . ', act_id="' . $Id_Activite .
						'", sts_id_nominal="' . $Site[0] . '", acst_type_site="' . $Site[1] . '"' );
				}


				$_fonctionCourante = 'rechercherPartiesPrenantes';
				$pprLibelle = $objPartiesPrenantes->rechercherPartiesPrenantes( $_SESSION['s_sct_id'], '', $_POST['ppr_id_responsable'] );


				// Ajoute le DMIA à l'Activité
				if ($_POST['total_dmia'] > 0 ) {
					foreach($_POST['dmia_activite'] as $Element) {
						$tElement = explode('=', $Element);
						$ete_id = $tElement[0];
						$mim_id = $tElement[1];
	
						$_fonctionCourante = 'ajouterDMIA';
						$objActivites->ajouterDMIA($_SESSION['s_cmp_id'], $Id_Activite, $ete_id, $mim_id);
	
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE', 'cmp_id="' . $_SESSION['s_cmp_id'] . ', act_id="' . $Id_Activite .
							'", ete_id="' . $ete_id . '", mim_id="' . $mim_id . '"' );
					}
				}


				// Ajoute les Personnes Clés (si nécessaire)
				if (isset($_POST['personnes_cles_a_ajouter']) and $_POST['personnes_cles_a_ajouter'] != []) {
					foreach($_POST['personnes_cles_a_ajouter'] as $Element) {
						$ppr_id = $Element[0];
						$ppac_description = $Element[1];
						
						$_fonctionCourante = 'ajouterPersonneCleActivite';
						$objActivites->ajouterPersonneCleActivite($_SESSION['s_cmp_id'], $Id_Activite, $ppr_id, $ppac_description);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'cmp_id="' . $_SESSION['s_cmp_id'] . ', act_id="' . $Id_Activite .
							'", ppr_id="' . $ppr_id . '", ppac_description="' . $ppac_description . '"' );
					}
				}


				// Ajoute les Applications (si nécessaire)
				if (isset($_POST['applications_a_ajouter']) and $_POST['applications_a_ajouter'] != []) {
					foreach($_POST['applications_a_ajouter'] as $Element) {
						$app_id = $Element[0];
						$ete_id_dima = $Element[1];
						$ete_id_pdma = $Element[2];
						$acap_palliatif = $Element[3];
						
						$_fonctionCourante = 'ajouterApplicationActivite';
						$objActivites->ajouterApplicationActivite($Id_Activite,
							$app_id, $ete_id_dima, $ete_id_pdma, $acap_palliatif);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'act_id="' . $Id_Activite .
							'", app_id="' . $app_id . '", ete_id_dima="' . $ete_id_dima . '"' .
							', ete_id_pdma="' . $ete_id_pdma . '", acap_palliatif="' . $acap_palliatif . '"');
					}
				}
				
				
				// Ajoute les Fournisseurs (si nécessaire)
				if (isset($_POST['fournisseurs_a_ajouter']) and $_POST['fournisseurs_a_ajouter'] != []) {
					foreach($_POST['fournisseurs_a_ajouter'] as $Element) {
						$frn_id = $Element[0];
						$ete_id = $Element[1];
						$acfr_consequence_indisponibilite = $Element[2];
						$acfr_palliatif_tiers = $Element[3];
						
						$_fonctionCourante = 'ajouterFournisseurActivite';
						$objActivites->ajouterFournisseurActivite($Id_Activite,
							$frn_id, $ete_id, $acfr_consequence_indisponibilite, $acfr_palliatif_tiers);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'act_id="' . $Id_Activite .
							'", frn_id="' . $frn_id . '", ete_id="' . $ete_id . '"' .
							', acfr_consequence_indisponibilite="' . $acfr_consequence_indisponibilite . '"' .
							', acap_palliatif="' . $acfr_palliatif_tiers . '"');
					}
				}
				

				$Valeurs = new stdClass();
				$Valeurs->act_id = $Id_Activite;
				$Valeurs->act_nom = $_POST['act_nom'];
				if ( $_POST['nim_numero'] == '' ) {
					$Valeurs->nim_poids = '<button class="btn btn-secondary btn-sm btn-espace btn_mim" style="min-width: 60px;">0</button>';
				} else {
					$Valeurs->nim_poids = '<button class="btn btn-secondary btn-sm btn-espace btn_mim" title="' . $_POST['nim_nom_code'] . '" style="background-color: ' . $_POST['nim_couleur'] . '; min-width: 60px;">' . $_POST['nim_numero'] . '</button>';
				}
				$Valeurs->ete_poids = $_POST['ete_nom_code'];

				$Valeurs->association = '<button class="btn btn-secondary btn-sm btn-espace btn_sts" title="' . $L_Sites . '">1</button>';
				$Valeurs->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_ppr" title="' . $L_Personnes_Cles . '">0</button>';
				$Valeurs->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_app" title="' . $L_Applications . '">0</button>';
				$Valeurs->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_frn" title="' . $L_Fournisseurs . '">0</button>';
				
				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_Activite, $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Activite_Creee,
					'texte' => $Occurrence,
					'id' => $Id_Activite,
					'droit_ajouter' => $Droit_Ajouter,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);

			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Activite;
				}

				if ( isset($_fonctionCourante) ) {
					$Message .= ' (' . $_fonctionCourante . ')';
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
					'texteMsg' => $L_Invalid_Value . ' (' . $_POST['id'] . ')'
				) );
				
				exit();
			}

			if ( ! $PageHTML->verifierActiviteAutorisee( $_POST['id'] ) ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_No_Authorize . ' (act_id=' . $_POST['id'] . ')'
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
				$objActivites->majActiviteParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ACTIVITE', $_POST[ 'source' ] . ' = "' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Activite_Modifiee
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_PartiePrenante;
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
					'texteMsg' => $L_Invalid_Value . ' (act_id)'
				) );

				exit();
			}

			if ( ! $PageHTML->verifierActiviteAutorisee( $_POST['id'] ) ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_No_Authorize . ' (act_id=' . $_POST['id'] . ')'
				) );

				exit();
			}

			try {
				$objActivites->supprimerActivite( $_POST['id'] );

				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ACTIVITE', 'act_id="' . $_POST['id'] . '", ' .
					'act_nom="' . $_POST[ 'libelle' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Activite_Supprimee
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


 case 'AJAX_Dupliquer':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['act_id']) ) {
			$_POST['act_id'] = $PageHTML->controlerTypeValeur( $_POST['act_id'], 'NUMERIC' );
			if ( $_POST['act_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_id)'
				) );

				exit();
			}

			$_POST['n_act_nom'] = $PageHTML->controlerTypeValeur( $_POST['n_act_nom'], 'ASCII' );
			if ( $_POST['act_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (n_act_nom)'
				) );

				exit();
			}

			if ( ! $PageHTML->verifierActiviteAutorisee( $_POST['act_id'] ) ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_No_Authorize . ' (act_id=' . $_POST['act_id'] . ')'
				) );

				exit();
			}

			$_POST['flag_dmia'] = $PageHTML->controlerTypeValeur( $_POST['flag_dmia'], 'BOOLEAN' );
			if ( $_POST['flag_dmia'] === -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (flag_dmia)'
				) );

				exit();
			}

			$_POST['flag_fournisseurs'] = $PageHTML->controlerTypeValeur( $_POST['flag_fournisseurs'], 'BOOLEAN' );
			if ( $_POST['flag_fournisseurs'] === -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (flag_fournisseurs)'
				) );

				exit();
			}

			$_POST['flag_applications'] = $PageHTML->controlerTypeValeur( $_POST['flag_applications'], 'BOOLEAN' );
			if ( $_POST['flag_applications'] === -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (flag_applications)'
				) );

				exit();
			}

			$_POST['flag_personnes_cles'] = $PageHTML->controlerTypeValeur( $_POST['flag_personnes_cles'], 'BOOLEAN' );
			if ( $_POST['flag_personnes_cles'] === -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (flag_personnes_cles)'
				) );

				exit();
			}

			$_POST['flag_sites'] = $PageHTML->controlerTypeValeur( $_POST['flag_sites'], 'BOOLEAN' );
			if ( $_POST['flag_sites'] === -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (flag_sites)'
				) );

				exit();
			}

			try {
				$n_act_id = $objActivites->dupliquerActivite( $_POST['act_id'], $_POST['n_act_nom'],
					$_POST['flag_dmia'], $_POST['flag_fournisseurs'], $_POST['flag_applications'],
					$_POST['flag_personnes_cles'], $_POST['flag_sites'] );

				$PageHTML->ecrireEvenement( 'ATP_DUPLICATION', 'OTP_ACTIVITE', 'act_id="' . $_POST['act_id'] . '" => n_act_id = "' . $n_act_id .'"' );

				$Activite = $objActivites->rechercherActivites( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'], 'act_nom', $n_act_id );

				$ListeMatriceImpactParChamp = $objMatriceImpacts->rechercherMatriceImpactsParChamp( $_SESSION['s_cmp_id'], 'nim_poids' );
				$ListeEchelleTempsParChamp = $objEchellesTemps->rechercherEchellesTempsParChamp( $_SESSION['s_cmp_id'], 'ete_poids' );

				if ($Activite[0]->nim_poids != NULL && $Activite[0]->nim_poids != '') {
					$Activite[0]->nim_poids = '<button class="btn btn-secondary btn-sm btn-espace btn_mim" title="' . $ListeMatriceImpactParChamp[$Activite[0]->nim_poids]->nim_nom_code . '" style="background-color: #' . $ListeMatriceImpactParChamp[$Activite[0]->nim_poids]->nim_couleur . '; min-width: 60px;">' . $ListeMatriceImpactParChamp[$Activite[0]->nim_poids]->nim_numero . '</button>';
				} else {
					$Activite[0]->nim_poids = '<button class="btn btn-secondary btn-sm btn-espace btn_mim" style="min-width: 60px;">0</button>';
				}

				if ($Activite[0]->ete_poids != NULL && $Activite[0]->ete_poids != '') {
					$Activite[0]->ete_poids = $ListeEchelleTempsParChamp[$Activite[0]->ete_poids]->ete_nom_code;
				} else {
					$Activite[0]->ete_poids = '';
				}

				$Activite[0]->association = '<button class="btn btn-secondary btn-sm btn-espace btn_sts" title="' . $L_Sites . '">' . $Activite[0]->total_sts . '</button>';
				$Activite[0]->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_ppr" title="' . $L_Personnes_Cles . '">' . $Activite[0]->total_ppr . '</button>';
				$Activite[0]->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_app" title="' . $L_Applications . '">' . $Activite[0]->total_app . '</button>';
				$Activite[0]->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_frn" title="' . $L_Fournisseurs . '">' . $Activite[0]->total_frn . '</button>';

				$Texte_HTML = $PageHTML->creerOccurrenceCorpsTableau( $Activite[0]->act_id, $Activite[0], $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Activite_Dupliquee,
					'texte' => $Texte_HTML,
					'act_id' => $Activite[0]->act_id,
					'droit_ajouter' => $Droit_Ajouter,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
				);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $e->getMessage() );
			}

			echo json_encode( $Resultat );
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => 'ACT_ID non spécifié'
			);
			
			echo json_encode( $Resultat );
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		);

		echo json_encode( $Resultat );
	}
	break;


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];

		$Total = 0;
		$Texte_HTML = '';

		try {
			if ( isset( $_SESSION['s_ent_id'] ) and $_SESSION['s_ent_id'] != '' ) {
				$_SESSION['s_cmp_id'] = $PageHTML->controlerTypeValeur( $_SESSION['s_cmp_id'], 'NUMERIC' );
				if ( $_SESSION['s_cmp_id'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (s_cmp_id)'
					) );
					
					exit();
				}

				$_SESSION['s_ent_id'] = $PageHTML->controlerTypeValeur( $_SESSION['s_ent_id'], 'NUMERIC' );
				if ( $_SESSION['s_ent_id'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (s_ent_id)'
					) );
					
					exit();
				}

				$ListeActivites = $objActivites->rechercherActivites( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'], $Trier );
				$Total = $objActivites->RowCount;

				$ListeMatriceImpactParChamp = $objMatriceImpacts->rechercherMatriceImpactsParChamp( $_SESSION['s_cmp_id'], 'nim_poids' );
				$ListeEchelleTempsParChamp = $objEchellesTemps->rechercherEchellesTempsParChamp( $_SESSION['s_cmp_id'], 'ete_poids' );

				foreach ($ListeActivites as $Occurrence) {
					if ($Occurrence->nim_poids != NULL && $Occurrence->nim_poids != '') {
						$Occurrence->nim_poids = '<button class="btn btn-secondary btn-sm btn-espace btn_mim" title="' . $ListeMatriceImpactParChamp[$Occurrence->nim_poids]->nim_nom_code . '" style="background-color: #' . $ListeMatriceImpactParChamp[$Occurrence->nim_poids]->nim_couleur . '; min-width: 60px;">' . $ListeMatriceImpactParChamp[$Occurrence->nim_poids]->nim_numero . '</button>';
					} else {
						$Occurrence->nim_poids = '<button class="btn btn-secondary btn-sm btn-espace btn_mim" style="min-width: 60px;">0</button>';
					}

					if ($Occurrence->ete_poids != NULL && $Occurrence->ete_poids != '') {
						$Occurrence->ete_poids = $ListeEchelleTempsParChamp[$Occurrence->ete_poids]->ete_nom_code;
					} else {
						$Occurrence->ete_poids = '';
					}

					$Occurrence->association = '<button class="btn btn-secondary btn-sm btn-espace btn_sts" title="' . $L_Sites . '">' . $Occurrence->total_sts . '</button>';
					$Occurrence->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_ppr" title="' . $L_Personnes_Cles . '">' . $Occurrence->total_ppr . '</button>';
					$Occurrence->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_app" title="' . $L_Applications . '">' . $Occurrence->total_app . '</button>';
					$Occurrence->association .= '<button class="btn btn-secondary btn-sm btn-espace btn_frn" title="' . $L_Fournisseurs . '">' . $Occurrence->total_frn . '</button>';
					
					$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->act_id, $Occurrence, $Format_Colonnes );
				}
			} else {
				$Texte_HTML .= '<div class="row justify-content-md-center mt-2"><div class="col col-lg-8"><h2 class="text-center">' . $L_Campagne_Sans_Entite . '</h2></div></div>' .
					'<div class="row justify-content-md-center mb-2"><div class="col col-lg-4 text-center"><a href="' . URL_BASE . '/MySecDash-Entites.php" class="btn btn-primary btn-gerer-campagnes">' . $L_Gestion_Entites . '</a></div></div>';
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_ajouter' => $Droit_Ajouter,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
				'L_Societe_Sans_Campagne' => $L_Societe_Sans_Campagne,
				'L_Gestion_Campagnes' => $L_Gestion_Campagnes,
				's_cmp_id' => $_SESSION['s_cmp_id']
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
		if ( isset($_POST['act_nom']) && isset($_POST['ppr_id_responsable']) && isset($_POST['ppr_id_suppleant'])
		 && isset($_POST['act_description']) ) {
			if ( ! $PageHTML->verifierActiviteAutorisee( $_POST['act_id'] ) ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_No_Authorize . ' (act_id=' . $_POST['act_id'] . ')'
				) );

				exit();
			}

			$_POST['act_nom'] = $PageHTML->controlerTypeValeur( $_POST['act_nom'], 'ASCII' );
			if ( $_POST['act_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_nom)'
				) );

				exit();
			}

			$_POST['ppr_id_responsable'] = $PageHTML->controlerTypeValeur( $_POST['ppr_id_responsable'], 'NUMERIC' );
			if ( $_POST['ppr_id_responsable'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_id_responsable)'
				) );

				exit();
			}

			$_POST['ppr_id_suppleant'] = $PageHTML->controlerTypeValeur( $_POST['ppr_id_suppleant'], 'NUMERIC' );
			if ( $_POST['ppr_id_suppleant'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_id_suppleant)'
				) );

				exit();
			}

			$_POST['act_description'] = $PageHTML->controlerTypeValeur( $_POST['act_description'], 'ASCII' );
			if ( $_POST['act_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_description)'
				) );

				exit();
			}

			$_POST['act_effectifs_en_nominal'] = $PageHTML->controlerTypeValeur( $_POST['act_effectifs_en_nominal'], 'NUMERIC' );
			if ( $_POST['act_effectifs_en_nominal'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_effectifs_en_nominal)'
				) );

				exit();
			}

			$_POST['act_effectifs_a_distance'] = $PageHTML->controlerTypeValeur( $_POST['act_effectifs_a_distance'], 'NUMERIC' );
			if ( $_POST['act_effectifs_a_distance'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_effectifs_a_distance)'
				) );

				exit();
			}

			$_POST['act_teletravail'] = $PageHTML->controlerTypeValeur( $_POST['act_teletravail'], 'NUMERIC' );
			if ( $_POST['act_teletravail'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_teletravail)'
				) );

				exit();
			}

			$_POST['act_dependances_internes_amont'] = $PageHTML->controlerTypeValeur( $_POST['act_dependances_internes_amont'], 'ASCII' );
			if ( $_POST['act_dependances_internes_amont'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_dependances_internes_amont)'
				) );

				exit();
			}

			$_POST['act_dependances_internes_aval'] = $PageHTML->controlerTypeValeur( $_POST['act_dependances_internes_aval'], 'ASCII' );
			if ( $_POST['act_dependances_internes_aval'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_dependances_internes_aval)'
				) );

				exit();
			}

			$_POST['act_justification_dmia'] = $PageHTML->controlerTypeValeur( $_POST['act_justification_dmia'], 'ASCII' );
			if ( $_POST['act_justification_dmia'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_justification_dmia)'
				) );
				
				exit();
			}


			try {
				$objActivites->majActivite( $_POST['act_id'], $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'],
					$_POST['ppr_id_responsable'], $_POST['ppr_id_suppleant'], $_POST['act_nom'], $_POST['act_description'],
					$_POST['act_effectifs_en_nominal'], $_POST['act_effectifs_a_distance'],
					$_POST['act_teletravail'], $_POST['act_dependances_internes_amont'],
					$_POST['act_dependances_internes_aval'], $_POST['act_justification_dmia'] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ACTIVITE', 'act_id="' . $_POST['act_id'] . '", ent_id="' .
					$_SESSION['s_ent_id'] . '", ppr_id_responsable="' . $_POST['ppr_id_responsable'] .
					'", ppr_id_suppleant="' . $_POST['ppr_id_suppleant'] .
					'", act_nom="' . $_POST['act_nom'] .
					'", act_description="' . $_POST['act_description'] .
					'", act_teletravail="' . $_POST['act_teletravail'] .
					'", act_dependances_internes_amont="' . $_POST['act_dependances_internes_amont'] .
					'", act_dependances_internes_aval="' . $_POST['act_dependances_internes_aval'] .
					'", act_justification_dmia="' . $_POST['act_justification_dmia'] . '"' );
			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage() . ' (majActivite)';
				
				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Activite;
				}

				echo json_encode( array(
					'statut' => $Statut,
					'texteMsg' => $Message
				) );

				exit();
			}

			try {
				if (isset($_POST['dmia_activite'])) {
					foreach($_POST['dmia_activite'] as $Element) {
						$tElement1 = explode('=', $Element);
						$ete_id = $tElement1[0];
						$tElement2 = explode('-', $tElement1[1]);
						$mim_id_old = $tElement2[0];
						$mim_id = $tElement2[1];
	
						$objActivites->modifierDMIA($_SESSION['s_cmp_id'], $_POST['act_id'], $ete_id, $mim_id_old, $mim_id);
	
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'cmp_id="' . $_SESSION['s_cmp_id'] . ', act_id="' . $_POST['act_id'] .
							'", ete_id="' . $ete_id . '", mim_id_old="' . $mim_id_old . '", mim_id="' . $mim_id . '"' );
					}
				}
			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage() . ' (modifierDMIA)';
				
				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Activite;
				}
				
				echo json_encode( array(
					'statut' => $Statut,
					'texteMsg' => $Message
				) );
				
				exit();
			}


			try {
				// Ajoute les Personnes Clés (si nécessaire)
				if (isset($_POST['personnes_cles_a_ajouter']) && $_POST['personnes_cles_a_ajouter'] != []) {
					foreach($_POST['personnes_cles_a_ajouter'] as $Element) {
						$ppr_id = $Element[0];
						$ppac_description = $Element[1];

						$_fonctionCourante = 'modifierPersonneCleActivite';
						$objActivites->ajouterPersonneCleActivite($_SESSION['s_cmp_id'], $_POST['act_id'], $ppr_id, $ppac_description);

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'cmp_id="' . $_SESSION['s_cmp_id'] . ', act_id="' . $_POST['act_id'] .
							'", ppr_id="' . $ppr_id . '", ppac_description="' . $ppac_description . '"' );
					}
				}


				// Modifie les Personnes Clés (si nécessaire)
				if (isset($_POST['personnes_cles_a_modifier']) && $_POST['personnes_cles_a_modifier'] != []) {
					foreach($_POST['personnes_cles_a_modifier'] as $Element) {
						$ppr_id = $Element[0];
						$ppac_description = $Element[1];

						$_fonctionCourante = 'modifierPersonneCleActivite';
						$objActivites->modifierPersonneCleActivite($_SESSION['s_cmp_id'], $_POST['act_id'], $ppr_id, $ppac_description);

						$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ACTIVITE',
							'cmp_id="' . $_SESSION['s_cmp_id'] . ', act_id="' . $_POST['act_id'] .
							'", ppr_id="' . $ppr_id . '", ppac_description="' . $ppac_description . '"' );
					}
				}


				// Supprime les Personnes Clés (si nécessaire)
				if (isset($_POST['personnes_cles_a_supprimer']) && $_POST['personnes_cles_a_supprimer'] != []) {
					foreach($_POST['personnes_cles_a_supprimer'] as $Element) {
						$ppr_id = $Element;
						
						$_fonctionCourante = 'supprimerPersonneCleActivite';
						$objActivites->supprimerPersonneCleActivite($_SESSION['s_cmp_id'], $_POST['act_id'], $ppr_id);
						
						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ACTIVITE',
							'cmp_id="' . $_SESSION['s_cmp_id'] . ', act_id="' . $_POST['act_id'] .
							'", ppr_id="' . $ppr_id . '"' );
					}
				}
			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage() . ' (' . $_fonctionCourante .')';
				
				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Activite;
				}
				
				echo json_encode( array(
					'statut' => $Statut,
					'texteMsg' => $Message
				) );
				
				exit();
			}


			try {
				// Ajoute les Applications (si nécessaire)
				if (isset($_POST['applications_a_ajouter']) && $_POST['applications_a_ajouter'] != []) {
					foreach($_POST['applications_a_ajouter'] as $Element) {
						$app_id = $Element[0];
						$ete_id_dima = $Element[1];
						$ete_id_pdma = $Element[2];
						$acap_donnees = $Element[3];
						$acap_palliatif = $Element[4];
						$acap_hebergement = $Element[5];
						$acap_niveau_service = $Element[6];
						
						$_fonctionCourante = 'ajouterApplicationActivite';
						$objActivites->ajouterApplicationActivite($_POST['act_id'],
							$app_id, $ete_id_dima, $ete_id_pdma, $acap_donnees, $acap_palliatif, $acap_hebergement, $acap_niveau_service);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'act_id="' . $_POST['act_id'] . '"' .
							', app_id="' . $app_id . '", ete_id_dima="' . $ete_id_dima . '"' .
							', ete_id_pdma="' . $ete_id_pdma . '", acap_donnees="' . $acap_donnees . '"' .
							', acap_palliatif="' . $acap_palliatif . '", acap_hebergement="' . $acap_hebergement . '"' .
							', acap_niveau_service="' . $acap_niveau_service . '"');
					}
				}


				// Modifie les Applications (si nécessaire)
				if (isset($_POST['applications_a_modifier']) && $_POST['applications_a_modifier'] != []) {
					foreach($_POST['applications_a_modifier'] as $Element) {
						$app_id = $Element[0];
						$ete_id_dima = $Element[1];
						$ete_id_pdma = $Element[2];
						$acap_donnees = $Element[3];
						$acap_palliatif = $Element[4];
						$acap_hebergement = $Element[5];
						$acap_niveau_service = $Element[6];

						$_fonctionCourante = 'modifierApplicationActivite';
						$objActivites->modifierApplicationActivite($_POST['act_id'], 
							$app_id, $ete_id_dima , $ete_id_pdma, $acap_donnees, $acap_palliatif, $acap_hebergement, $acap_niveau_service);

						$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ACTIVITE',
							'act_id="' . $_POST['act_id'] . '"' .
							', app_id="' . $app_id . '", ete_id_dima="' . $ete_id_dima . '"' .
							', ete_id_pdma="' . $ete_id_pdma . '", acap_donnees="' . $acap_donnees . '"' .
							', acap_palliatif="' . $acap_palliatif . '", acap_hebergement="' . $acap_hebergement . '"' .
							', acap_niveau_service="' . $acap_niveau_service . '"');
					}
				}


				// Supprime les Applications (si nécessaire)
				if (isset($_POST['applications_a_supprimer']) && $_POST['applications_a_supprimer'] != []) {
					foreach($_POST['applications_a_supprimer'] as $Element) {
						$app_id = $Element;
						
						$_fonctionCourante = 'supprimerApplicationActivite';
						$objActivites->supprimerApplicationActivite($_POST['act_id'], $app_id);
						
						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ACTIVITE',
							'act_id="' . $_POST['act_id'] . '", app_id="' . $app_id . '"' );
					}
				}
			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage() . ' (' . $_fonctionCourante .')';
				
				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Activite;
				}
				
				echo json_encode( array(
					'statut' => $Statut,
					'texteMsg' => $Message
				) );
				
				exit();
			}


			try {
				// Ajoute les Fournisseurs (si nécessaire)
				if (isset($_POST['fournisseurs_a_ajouter']) and $_POST['fournisseurs_a_ajouter'] != []) {
					foreach($_POST['fournisseurs_a_ajouter'] as $Element) {
						$frn_id = $Element[0];
						$ete_id = $Element[1];
						$acfr_consequence_indisponibilite = $Element[2];
						$acfr_palliatif_tiers = $Element[3];
						
						$_fonctionCourante = 'ajouterFournisseurActivite';
						$objActivites->ajouterFournisseurActivite($_POST['act_id'],
							$frn_id, $ete_id, $acfr_consequence_indisponibilite, $acfr_palliatif_tiers);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'act_id="' . $_POST['act_id'] . '"' .
							', frn_id="' . $frn_id . '", ete_id="' . $ete_id . '"' .
							', acfr_consequence_indisponibilite="' . $acfr_consequence_indisponibilite . '"' .
							', acfr_palliatif_tiers="' . $acfr_palliatif_tiers . '"');
					}
				}


				// Modifie les Fournisseurs (si nécessaire)
				if (isset($_POST['fournisseurs_a_modifier']) and $_POST['fournisseurs_a_modifier'] != []) {
					foreach($_POST['fournisseurs_a_modifier'] as $Element) {
						$frn_id = $Element[0];
						$ete_id = $Element[1];
						$acfr_consequence_indisponibilite = $Element[2];
						$acfr_palliatif_tiers = $Element[3];

						$_fonctionCourante = 'modifierFournisseurActivite';
						$objActivites->modifierFournisseurActivite($_POST['act_id'],
							$frn_id, $ete_id, $acfr_consequence_indisponibilite, $acfr_palliatif_tiers);

						$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ACTIVITE',
							'act_id="' . $_POST['act_id'] . '"' .
							', frn_id="' . $frn_id . '", ete_id="' . $ete_id . '"' .
							', acfr_consequence_indisponibilite="' . $acfr_consequence_indisponibilite . '"' .
							', acfr_palliatif_tiers="' . $acfr_palliatif_tiers . '"');
					}
				}


				// Supprime les Fournisseurs (si nécessaire)
				if (isset($_POST['fournisseurs_a_supprimer']) and $_POST['fournisseurs_a_supprimer'] != []) {
					foreach($_POST['fournisseurs_a_supprimer'] as $Element) {
						$frn_id = $Element;
						
						$_fonctionCourante = 'supprimerFournisseurActivite';
						$objActivites->supprimerFournisseurActivite($_POST['act_id'], $frn_id);
						
						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ACTIVITE',
							'act_id="' . $_POST['act_id'] . '", frn_id="' . $frn_id . '"' );
					}
				}
			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage() . ' (' . $_fonctionCourante .')';
				
				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Activite;
				}
				
				echo json_encode( array(
					'statut' => $Statut,
					'texteMsg' => $Message
				) );
				
				exit();
			}


			try {
				// Ajoute les Sites (si nécessaire)
				if (isset($_POST['sites_a_ajouter']) && $_POST['sites_a_ajouter'] != []) {
					foreach($_POST['sites_a_ajouter'] as $Element) {
						$sts_id = $Element[0];
						$acst_type_site = $Element[1];

						$_fonctionCourante = 'ajouterSiteActivite';
						$objActivites->ajouterSiteActivite($_SESSION['s_cmp_id'], $_POST['act_id'], $sts_id, $acst_type_site);

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'cmp_id="' . $_SESSION['s_cmp_id'] . '", act_id="' . $_POST['act_id'] . '"' .
							', sts_id="' . $sts_id . '", acst_type_site="' . $acst_type_site . '"');
					}
				}


				// Modifie les Sites (si nécessaire)
				if (isset($_POST['sites_a_modifier']) && $_POST['sites_a_modifier'] != []) {
					foreach($_POST['sites_a_modifier'] as $Element) {
						$sts_id = $Element[0];
						$acst_type_site = $Element[1];
						
						$_fonctionCourante = 'modifierSiteActivite';
						$objActivites->modifierSiteActivite($_POST['act_id'], $sts_id, $acst_type_site);

						$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ACTIVITE',
							'cmp_id="' . $_SESSION['s_cmp_id'] . '", act_id="' . $_POST['act_id'] . '"' .
							', sts_id="' . $sts_id . '", acst_type_site="' . $acst_type_site . '"');
					}
				}


				// Supprime les Sites (si nécessaire)
				if (isset($_POST['sites_a_supprimer']) && $_POST['sites_a_supprimer'] != []) {
					foreach($_POST['sites_a_supprimer'] as $Element) {
						$sts_id = $Element;
						
						$_fonctionCourante = 'supprimerSiteActivite';
						$objActivites->supprimerSiteActivite($_SESSION['s_cmp_id'], $_POST['act_id'], $sts_id);
						
						$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_ACTIVITE',
							'cmp_id="' . $_SESSION['s_cmp_id'] . '", act_id="' . $_POST['act_id'] . '", sts_id="' . $sts_id . '"' );
					}
				}
			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage() . ' (' . $_fonctionCourante .')';

				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Activite;
				}

				echo json_encode( array(
					'statut' => $Statut,
					'texteMsg' => $Message
				) );

				exit();
			}

			echo json_encode(  array(
				'statut' => 'success',
				'texteMsg' => $L_Activite_Modifiee
			) );
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
				$CodeHTML = sprintf( $L_Confirmer_Suppression_Activite, $_POST['libelle'] );
	
	
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


 case 'AJAX_listerRolesPartiePrenante':
	if ( $Droit_Lecture === TRUE ) {
		$Resultat = array(
			'statut' => 'success',
			'texteMsg' => listerRolesPartiePrenante( $_POST['id'], $_POST['libelle'] )
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
	
 case 'AJAX_listerPartiesPrenantes':
	if ( $Droit_Lecture === TRUE ) {
		$Resultat = array(
		'statut' => 'success',
		'texteMsg' => listerPartiesPrenantes( $_POST['id'], $_POST['libelle'] )
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

 case 'AJAX_Ajouter_Role_PartiePrenante':
	if ( $Droit_Ajouter_Personnes_Cles === TRUE ) {
		if ( isset($_POST['n_rpp_nom_code'])  ) {
			$_POST['n_rpp_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['n_rpp_nom_code'], 'ASCII' );
			if ( $_POST['n_rpp_nom_code'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (n_rpp_nom_code)'
				) );

				exit();
			}

			try {
				$objPartiesPrenantes->majRolePartiePrenante( '', $_POST['n_rpp_nom_code'] );
			} catch( Exception $e ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $e->getMessage() );
			}

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Role_PartiePrenante_Cree,
				'rpp_id' => $objPartiesPrenantes->LastInsertId
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


 case 'AJAX_Ajouter_PartiePrenante':
	if ( $Droit_Ajouter_Personnes_Cles === TRUE ) {
		if ( isset($_POST['ppr_nom']) and isset($_POST['ppr_prenom']) and isset($_POST['ppr_interne']) ) {
			$_POST['ppr_nom'] = $PageHTML->controlerTypeValeur( $_POST['ppr_nom'], 'ASCII' );
			if ( $_POST['ppr_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_nom)'
				) );
				
				exit();
			}

			$_POST['ppr_prenom'] = $PageHTML->controlerTypeValeur( $_POST['ppr_prenom'], 'ASCII' );
			if ( $_POST['ppr_prenom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_prenom)'
				) );
				
				exit();
			}

			$_POST['ppr_interne'] = $PageHTML->controlerTypeValeur( $_POST['ppr_interne'], 'NUMERIC' );
			if ( $_POST['ppr_interne'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_interne)'
				) );
				
				exit();
			}

			try {
				$objPartiesPrenantes->majPartiePrenante( '', $_SESSION['s_sct_id'], $_POST['ppr_nom'],
					$_POST['ppr_prenom'], $_POST['ppr_interne'] );
			} catch( Exception $e ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $e->getMessage() );
			}
			
			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_PartiePrenante_Creee,
				'ppr_id' => $objPartiesPrenantes->LastInsertId,
				'L_Description' => $L_Description,
				'L_Interne' => $L_Interne
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


 case 'AJAX_Ajouter_Site':
 	if ( $Droit_Ajouter_Sites === TRUE ) {
		if ( isset($_POST['sts_nom']) ) {
			$_POST['sts_nom'] = $PageHTML->controlerTypeValeur( $_POST['sts_nom'], 'ASCII' );
			if ( $_POST['sts_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (sts_nom)'
				) );
				
				exit();
			}

			if ( isset( $_POST['sts_description'] ) ) {
				$_POST['sts_description'] = $PageHTML->controlerTypeValeur( $_POST['sts_description'], 'ASCII' );
				if ( $_POST['sts_description'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (sts_description)'
					) );
					
					exit();
				}
			} else {
				$_POST['sts_description'] = '';
			}

			try {
				$objSites->majSite( $_SESSION['s_sct_id'], $_POST['sts_nom'], $_POST['sts_description'] );
			} catch( Exception $e ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $e->getMessage() . ' (majSite)' );
			}

			try {
				$objCampagnes->associerSiteCampagne( $_SESSION['s_cmp_id'], $objSites->LastInsertId );
			} catch( Exception $e ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $e->getMessage() . ' (associerSiteCampagne)' );
			}

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Site_Cree,
				'sts_id' => $objSites->LastInsertId,
				'L_Aucun' => $L_Neither,
				'L_Site_Nominal' => $L_Site_Nominal,
				'L_Site_Secours' => $L_Site_Secours
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
	
	
 case 'AJAX_Ajouter_Application':
	if ( $Droit_Ajouter_Applications === TRUE ) {
		if ( isset($_POST['app_nom']) ) {
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

			$_POST['app_description'] = $PageHTML->controlerTypeValeur( $_POST['app_description'], 'ASCII' );
			if ( $_POST['app_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (app_description)'
				) );

				exit();
			}

			try {
				$_Internal_Function = 'majApplication';
				$objApplications->majApplication( '', $_POST['app_nom'], $_POST['app_hebergement'],
					$_POST['app_niveau_service'], $_POST['app_description'] );

				$app_id = $objApplications->LastInsertId;
			} catch( Exception $e ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $e->getMessage() . ' (' . $_Internal_Function . ')' );

				echo json_encode( $Resultat );
				exit();
			}

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Application_Cree,
				'app_id' => $app_id,
				'Liste_EchellesTemps' => $objEchellesTemps->rechercherEchellesTemps($_SESSION['s_cmp_id']),
				'L_DMIA' => $L_DMIA,
				'L_PDMA' => $L_PDMA,
				'L_Palliatif' => $L_Palliatif,
				'L_Aucun' => $L_Neither
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


 case 'AJAX_Ajouter_Fournisseur':
	if ( $Droit_Ajouter_Fournisseurs === TRUE ) {
		if ( isset($_POST['frn_nom']) ) {
			$_POST['frn_nom'] = $PageHTML->controlerTypeValeur( $_POST['frn_nom'], 'ASCII' );
			if ( $_POST['frn_nom'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (frn_nom)'
				) );

				exit();
			}

			$_POST['tfr_id'] = $PageHTML->controlerTypeValeur( $_POST['tfr_id'], 'NUMERIC' );
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
				$_Internal_Function = 'majFournisseur';
				$objFournisseurs->majFournisseur( '', $_POST['tfr_id'], $_POST['frn_nom'],
					$_POST['frn_description'] );

				$frn_id = $objFournisseurs->LastInsertId;
			} catch( Exception $e ) {
				$Resultat = array( 'statut' => 'error',
					'texteMsg' => $e->getMessage() . ' (' . $_Internal_Function . ')' );
			}

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Fournisseur_Cree,
				'frn_id' => $frn_id,
				'Liste_EchellesTemps' => $objEchellesTemps->rechercherEchellesTemps($_SESSION['s_cmp_id']),
				'L_DMIA' => $L_DMIA,
				'L_PDMA' => $L_PDMA,
				'L_Palliatif' => $L_Palliatif,
				'L_Aucun' => $L_Neither
			);

			echo json_encode( $Resultat );
			exit();
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires . '(frn_nom)'
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


 case 'AJAX_Selectioner_Societe':
	if ( isset($_POST['sct_id']) ) {
		if ( ! $PageHTML->verifierSocieteAutorisee($_POST['sct_id']) ) {
			print( json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' ) ) );
			
			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $_POST['sct_id'] . '")'.' [' . __LINE__ . ']' );
			
			exit();
		}

		$_SESSION['s_sct_id'] = $_POST['sct_id'];

		try {
			list($Liste_Societes, $Liste_Campagnes, $Liste_Entites) =
				actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites, 2);
		} catch ( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
			echo json_encode( $Resultat );
			break;
		}

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Societe_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id'],
			'ent_id' => $_SESSION['s_ent_id'],
			'L_Societe_Sans_Campagne' => $L_Societe_Sans_Campagne,
			'L_Gestion_Campagnes' => $L_Gestion_Campagnes,
			'L_Campagne_Sans_Entite' => $L_Campagne_Sans_Entite,
			'L_Gestion_Entites' => $L_Gestion_Entites,
			'Liste_Campagnes' => $Liste_Campagnes,
			'Liste_Entites' => $Liste_Entites
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (sct_id)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Selectioner_Campagne':
	if ( isset($_POST['cmp_id']) ) {
		if ( ! $PageHTML->verifierCampagneAutorisee($_POST['cmp_id']) ) {
			print( json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (cmp_id="' . $_POST['cmp_id'] . '")'.' [' . __LINE__ . ']' ) ) );
			
			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );
			
			exit();
		}

		$_SESSION['s_cmp_id'] = $_POST['cmp_id'];

		try {
			list($Liste_Societes, $Liste_Campagnes, $Liste_Entites) =
				actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites, 3);
		} catch ( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
			echo json_encode( $Resultat );
			break;
		}

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Campagne_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id'],
			'ent_id' => $_SESSION['s_ent_id'],
			'Liste_Entites' => $Liste_Entites
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (cmp_id)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Selectioner_Entite':
	if ( isset($_POST['ent_id']) ) {
		if ( ! $PageHTML->verifierEntiteAutorisee($_POST['ent_id']) ) {
			print( json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' ) ) );
			
			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );
			
			exit();
		}

		$_SESSION['s_ent_id'] = $_POST['ent_id'];

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Entite_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id'],
			'ent_id' => $_SESSION['s_ent_id']
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (ent_id)' );
	}

	echo json_encode( $Resultat );

	break;
}



function listerCivilites( $Init_Id = '', $Init_Libelle = '' ) {
	$objIdentites = new HBL_Identites();

	$Liste = $objPartiesPrenantes->rechercherRolesPartiePrenante();

	$Code_HTML = '';

	foreach ($Liste as $Occurrence) {
		if ( $Init_Id != '' and $Init_Id == $Occurrence->rpp_id ) $Selected = ' selected';
		else $Selected = '';

		if ( $Init_Libelle != '' and $Init_Libelle == $Occurrence->rpp_nom_code ) $Selected = ' selected';
		else $Selected = '';

		$Code_HTML .= '<option value="' . $Occurrence->rpp_id . '"' . $Selected . '>' . $Occurrence->rpp_nom_code . '</option>' ;
	}

	return $Code_HTML;
}



function listerPartiesPrenantes( $Init_Id = '', $Init_Libelle = '' ) {
	$objPartiesPrenantes = new PartiesPrenantes();
	
	$Liste = $objPartiesPrenantes->rechercherPartiesPrenantes( $_SESSION['s_sct_id'] );
	
	$Code_HTML = '';
	
	foreach ($Liste as $Occurrence) {
		if ( $Init_Id != '' and $Init_Id == $Occurrence->ppr_id ) $Selected = ' selected';
		else $Selected = '';
		
		if ( $Init_Libelle != '' and $Init_Libelle == $Occurrence->ppr_nom . ' ' . $Occurrence->ppr_prenom ) $Selected = ' selected';
		else $Selected = '';
		
		$Code_HTML .= '<option value="' . $Occurrence->ppr_id . '"' . $Selected . '>' . $Occurrence->ppr_nom . ' ' . $Occurrence->ppr_prenom . '</option>' ;
	}
	
	return $Code_HTML;
}



function actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites, $forcer=0) {
	/**
	 * Actualise les listes Sociétés, Campagnes et Entités à l'entrée dans l'écran et en cas de changement.
	 *
	 * \license Copyleft Loxense
	 * \author Pierre-Luc MARY
	 * \date 2024-10-01
	 *
	 * \param[in] $objSocietes Objet permettant d'accéder aux fonctions de gestion des Sociétés
	 * \param[in] $objCampagnes Objet permettant d'accéder aux fonctions de gestion des Campagnes
	 * \param[in] $objActivites Objet permettant d'accéder aux fonctions de gestion des Activités
	 * \param[in] $forcer Flag permettant de forcer le résultat (0=Tout charger, 1=Changer Société, 2=Changer Campagne, 3=Changer Entité)
	 *
	 * \return Renvoi un tableau d'objet ou un tableau vide si pas de données trouvées. Lève une exception en cas d'erreur.
	 */
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

	$Liste_Societes = [];
	$Liste_Campagnes = [];
	$Liste_Entites = [];


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
			$_SESSION['s_ent_id'] = '';

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
			$_SESSION['s_ent_id'] = '';

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
		$_SESSION['s_cmp_id'] = '';
		$_SESSION['s_ent_id'] = '';

		throw new Exception($L_Pas_Campagne_Pour_Societe, 0);
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

	// Récupère les Entités associées à la Campagne Sélectionnée
	$Liste_Entites = $objActivites->rechercherEntitesCampagne($_SESSION['s_cmp_id']);
	if ( $Liste_Entites == [] ) {
		$_SESSION['s_ent_id'] = '';
	} else {
		if ( ! isset($_SESSION['s_ent_id']) or $_SESSION['s_ent_id'] == '' ) { // or $_SESSION['s_ent_id'] == '*' ) {
			$_SESSION['s_ent_id'] = $Liste_Entites[0]->ent_id;
		} else {
			// On contrôle que l'utilisateur a encore accès à cette Société.
			$_Autorise = 0;
			foreach ($Liste_Entites as $_Tmp) {
				if ( $_Tmp->ent_id == $_SESSION['s_ent_id'] ) {
					$_Autorise = 1;
					break;
				}
			}
			
			if ( $_Autorise == 0 ) {
				$_SESSION['s_ent_id'] = $Liste_Entites[0]->ent_id;
			}
		}
	}

//print($_SESSION['s_sct_id'].' - '.$_SESSION['s_cmp_id'].' - '.$_SESSION['s_ent_id'].'<hr>');

	return [$Liste_Societes, $Liste_Campagnes, $Liste_Entites];
}

?>