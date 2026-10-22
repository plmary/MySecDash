<?php

/**
* Ce script gère les indisponibilités des Applications sur une Activité.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2026-08-26
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
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Activites.php' );


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
//include( DIR_LIBRAIRIES . '/Class_IndisponibilitesFournisseurs_PDO.inc.php' );
//include( DIR_LIBRAIRIES . '/Class_IndisponibilitesPersonnels_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_IndisponibilitesApplications_PDO.inc.php' );


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
//$objIndispoFournisseur = new IndisponibilitesFournisseurs();
//$objIndispoPersonnel = new IndisponibilitesPersonnels();
$objIndispoApplication = new IndisponibilitesApplications();


// Chargement des libellés spécifiques à cet écran
$L_Indispo_Fournisseur_Equipe = $PageHTML->getLibelle('__LRI_REPRISE_EQUIPE');
$L_Indispo_Fournisseur_Autre_Fournisseur = $PageHTML->getLibelle('__LRI_REPRISE_PAR_AUTRE_FOURNISSEUR');
$L_Titre_Ecran = $PageHTML->getLibelle('__LRI_INDISPO_APPLICATIONS');
$L_Equipe = $PageHTML->getLibelle('__LRI_EQUIPE');
$L_Responsable = $PageHTML->getLibelle('__LRI_RESPONSABLE');
$L_Fournisseur = $PageHTML->getLibelle('__LRI_FOURNISSEUR');
$L_Prestataire = $PageHTML->getLibelle('__LRI_PRESTATAIRE');
$L_Contact = $PageHTML->getLibelle('__LRI_CONTACT');
$L_Application_Palliative = $PageHTML->getLibelle('__LRI_APPLICATION_PALLIATIVE');
$L_Applications_Palliatives = $PageHTML->getLibelle('__LRI_APPLICATIONS_PALLIATIVES');
$L_Mode_Operatoire = $PageHTML->getLibelle('__LRI_MODE_OPERATOIRE');


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'APP';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'act_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'app_nom', 'titre' => $L_Application, 'taille' => '3',
	'affichage' => 'img', 'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'frn_nom', 'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_dmia_nom', 'titre' => $L_DMIA,
	'affichage' => 'img', 'taille' => '1', 'triable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ete_pdma_nom', 'titre' => $L_PDMA,
	'affichage' => 'img', 'taille' => '1', 'triable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'activites', 'titre' => $L_Activites,
	'affichage' => 'img', 'taille' => '4', 'triable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'applications_palliatives', 'titre' => $L_Applications_Palliatives,
	'affichage' => 'img', 'taille' => '2', 'triable' => 'non' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '1', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier ) ); //, 'supprimer' => $Droit_Supprimer ) );


// Vérification de droits spécifiques
$Droit_Ajouter_Personnes_Cles = $PageHTML->controlerPermission('MyContinuity-PartiesPrenantes.php', 'RGH_2');
$Droit_Ajouter_Applications = $PageHTML->controlerPermission('MyContinuity-Applications.php', 'RGH_2');
$Droit_Ajouter_Fournisseurs = $PageHTML->controlerPermission('MyContinuity-Fournisseurs.php', 'RGH_2');


// Exécute l'action identifiée
switch( $Action ) {
 default:
	$Liste_Societes = '';
	$Liste_Campagnes = '';
	$Liste_Entites = '';
	$Liste_Activites = '';

	// Initialise les listes déroulantes : Sociétés, Campagnes et Entités
	try {
		list($Liste_Societes, $Liste_Campagnes, $Liste_Entites, $Liste_Activites) = 
			actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites);
	} catch( Exception $e ) {
		print('<h1 class="text-urgent">' . $e->getMessage() . '</h1>');
	}

	if ( $Droit_Ajouter === TRUE ) {
//		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
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
			$Choix_Entites['options'][] = array('id' => $Entite->ent_id, 'nom' => $Entite->ent_nom . ' (' . $Entite->total_activites . ' ' . ($Entite->total_activites > 1 ? $PageHTML->getLibelle('__LRI_ACTIVITES') : $PageHTML->getLibelle('__LRI_ACTIVITE') ) . ')' );
		}
	}


	$Choix_Entites['id2'] = 's_act_id';
	$Choix_Entites['libelle2'] = $L_Activites;
	
	if ( $Liste_Activites != '' ) {
		foreach( $Liste_Activites AS $Activite ) {
			$Choix_Entites['options2'][] = array('id' => $Activite->act_id, 'nom' => $Activite->act_nom);
		}
	}

	$Fichiers_JavaScript[] = 'MatriceImpacts.js';

	print $PageHTML->construireEnteteHTML( $L_Titre_Ecran, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Titre_Ecran, $Liste_Societes, $Boutons_Alternatifs,
		$Choix_Campagnes, '', $Choix_Entites );

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
		'L_Titre_Modifier' => $PageHTML->getLibelle('__LRI_GESTION_INDISPO_FOURNISSEUR_ACTIVITES'),
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
		'L_Action' => $L_Action,
		'L_Role' => $L_Role,
		'L_Oui' => $L_Yes,
		'L_Non' => $L_No,
		'L_Site_Nominal' => $L_Site_Nominal,
		'L_Site_Secours' => $L_Site_Secours,
		'L_Besoins_Continuite_Activite' => $L_Besoins_Continuite_Activite,
		'L_DMIA' => $L_DMIA,
		'L_PDMA' => $L_PDMA,
		'L_DMIA_SI' => $L_DMIA_SI,
		'L_PDMA_SI' => $L_PDMA_SI,
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
		'L_Aucune' => $L_Neither_f,
		'L_Activite_Teletravaillable' => $L_Activite_Teletravaillable,
		'L_Niveau' => $L_Niveau,
		'L_Type' => $L_Type,
		'L_Sites' => $L_Sites,
		'L_Personnes_Cles' => $L_Personnes_Cles,
		'L_Applications' => $L_Applications,
		'L_Application' => $L_Application,
		'L_Fournisseur' => $L_Fournisseur,
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
		'Liste_EchellesTemps' => $objEchellesTemps->rechercherEchellesTemps($_SESSION['s_sct_id']),
//		'Liste_Niveaux_Impact' => $objCampagnes->rechercherNiveauxImpactCampagne( $_SESSION['s_cmp_id'] ),
//		'Liste_Types_Impact' => $objCampagnes->rechercherTypesImpactCampagne( $_SESSION['s_cmp_id'] ),
//		'Liste_Matrice_Impacts' => $objMatriceImpacts->rechercherMatriceImpactsParID( $_SESSION['s_cmp_id'] ),
		'Liste_Parties_Prenantes' => $objPartiesPrenantes->rechercherPartiesPrenantes( $_SESSION['s_sct_id'] ),
//		'Liste_Personnes_Cles' => $objActivites->rechercherPersonnesClesActivites( $_SESSION['s_sct_id'] ),
		'Liste_Fournisseurs' => $objFournisseurs->rechercherFournisseurs(),
//		'Liste_Applications' => $objApplications->rechercherApplications(),
		'Liste_Types_Fournisseur' => $objFournisseurs->rechercherTypesFournisseur(),
		'L_Effectifs_A_Distance' => $L_Effectifs_A_Distance,
		'L_Effectifs_En_Nominal' => $L_Effectifs_En_Nominal,
		'Droit_Ajouter_Personnes_Cles' => $Droit_Ajouter_Personnes_Cles,
		'Droit_Ajouter_Applications' => $Droit_Ajouter_Applications,
		'Droit_Ajouter_Fournisseurs' => $Droit_Ajouter_Fournisseurs,
		'L_Dupliquer_Activite' => $L_Dupliquer_Activite,
		'L_Dupliquer' => $L_Dupliquer,
		'L_Nouveau_Nom' => $L_Nouveau_Nom,
		'L_Informations_Complementaires_A_Dupliquer' => $L_Informations_Complementaires_A_Dupliquer,
		'L_Tout_Cocher_Decocher' => $L_Tout_Cocher_Decocher,
		'L_Effectif_Total_Entite' => $PageHTML->getLibelle('__LRI_EFFECTIF_TOTAL_ENTITE'),
		'L_Taux_Occupation' => $PageHTML->getLibelle('__LRI_TAUX_OCCUPATION'),
		'L_Personnes_Prioritaires' => $PageHTML->getLibelle('__LRI_PERSONNES_PRIORITAIRES'),
		'L_Description_Entraides' => $PageHTML->getLibelle('__LRI_DESCRITPION_ENTRAIDES_INTERNE_EXTERNE'),
		'L_Description_Strategie_Montee_Charge' => $PageHTML->getLibelle('__LRI_DESCRITPION_STRATEGIE_MONTEE_CHARGE'),
		'Total_Effectif_Entite' => $objEntites->recupererEffectifEntite( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'] ),
		'L_Indispo_Fournisseur_Equipe' => $L_Indispo_Fournisseur_Equipe,
		'L_Indispo_Fournisseur_Autre_Fournisseur' => $L_Indispo_Fournisseur_Autre_Fournisseur,
		'L_Titre_Ecran' => $L_Titre_Ecran,
		'L_Equipe' => $L_Equipe,
		'L_Responsable' => $L_Responsable,
		'L_Fournisseur' => $L_Fournisseur,
		'L_Prestataire' => $L_Prestataire,
		'L_Contact' => $L_Contact,
		'L_Activite' => $L_Activite,
		'L_Application_Palliative' => $L_Application_Palliative,
		'L_Applications_Palliatives' => $L_Applications_Palliatives,
		'L_Mode_Operatoire' => $L_Mode_Operatoire
	);

	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['app_id']) and $_POST['app_id'] != '') {
//			$Libelles['Liste_Equipes'] = $objIndispoPersonnel->rechercherEquipes( $_SESSION['s_sct_id'] );
//			$Libelles['Fournisseurs'] = $objIndispoFournisseur->rechercherActivites( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'], 'frn_nom', $_POST['frn_id'] );
			$Libelles['Liste_Activites'] = $objActivites->rechercherActivites( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'] );
			$Libelles['Liste_Applications'] = $objApplications->rechercherApplications('app_nom', '', '*');
			$Libelles['Detail_Application'] = $objIndispoApplication->rechercherApplicationsSecours( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'],
				$_SESSION['s_act_id'], 'app_nom', $_POST['app_id'] );
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

			$_POST['act_taux_occupation'] = $PageHTML->controlerTypeValeur( $_POST['act_taux_occupation'], 'ASCII' );
			if ( $_POST['act_taux_occupation'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_taux_occupation)'
				) );
				
				exit();
			}

			if ( isset($_POST['cmen_effectif_total']) ) {
				$_POST['cmen_effectif_total'] = $PageHTML->controlerTypeValeur( $_POST['cmen_effectif_total'], 'NUMBER' );
				if ( $_POST['cmen_effectif_total'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (cmen_effectif_total)'
					) );
					
					exit();
				}
			}

			$_POST['act_description_entraides'] = $PageHTML->controlerTypeValeur( $_POST['act_description_entraides'], 'ASCII' );
			if ( $_POST['act_description_entraides'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_description_entraides)'
				) );
				
				exit();
			}

			$_POST['act_strategie_montee_en_charge'] = $PageHTML->controlerTypeValeur( $_POST['act_strategie_montee_en_charge'], 'ASCII' );
			if ( $_POST['act_strategie_montee_en_charge'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (act_strategie_montee_en_charge)'
				) );
				
				exit();
			}
			

			try {
				$_fonctionCourante = 'majActivite';
				$objActivites->majActivite( '', $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'], $_POST['ppr_id_responsable'],
					$_POST['ppr_id_suppleant'], $_POST['act_nom'], $_POST['act_description'],
					$_POST['act_effectifs_en_nominal'], $_POST['act_effectifs_a_distance'],
					$_POST['act_teletravail'], $_POST['act_dependances_internes_amont'],
					$_POST['act_dependances_internes_aval'], $_POST['act_justification_dmia'],
					$_POST['act_taux_occupation'], $_POST['act_description_entraides'], $_POST['act_strategie_montee_en_charge'] );

				$Id_Activite = $objActivites->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE', 'act_id="' . $Id_Activite .
					'", ent_id="' . $_SESSION['s_ent_id'] . '", ppr_id_responsable="' . $_POST['ppr_id_responsable'] .
					'", ppr_id_suppleant="' . $_POST['ppr_id_suppleant'] . '", act_nom="' . $_POST['act_nom'] .
					'", act_description="' . $_POST['act_description'] .
					'", act_teletravail="' . $_POST['act_teletravail'] .
					'", act_dependances_internes_amont="' . $_POST['act_dependances_internes_amont'] .
					'", act_dependances_internes_aval="' . $_POST['act_dependances_internes_aval'] .
					'", act_justification_dmia="' . $_POST['act_justification_dmia'] .
					'", act_taux_occupation="' . $_POST['act_taux_occupation'] .
					'", act_description_entraides="' . $_POST['act_description_entraides'] .
					'", act_strategie_montee_en_charge="' . $_POST['act_strategie_montee_en_charge'] . '"'
					);
				

				if ( $_POST['cmen_effectif_total'] != '' ) {
					$objCampagnes->modifierEffectifEntiteCampagne($_SESSION['s_cmp_id'], $_SESSION['s_ent_id'], $_POST['cmen_effectif_total']);

					$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_ENTITE', 'cmp_id="' . $_SESSION['s_cmp_id'] . ', ent_id="' . $_SESSION['s_ent_id'] .
						'", cmen_effectif_total="' . $_POST['cmen_effectif_total'] . '"' );
				}

				if ( isset($_POST['sites_a_ajouter']) ) {
					$_fonctionCourante = 'ajouterSiteActivite';
					foreach( $_POST['sites_a_ajouter'] as $Site ) {
						$objActivites->ajouterSiteActivite( $_SESSION['s_cmp_id'], $Id_Activite, $Site[0], $Site[1] );
	
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE', 'cmp_id="' . $_SESSION['s_cmp_id'] . ', act_id="' . $Id_Activite .
							'", sts_id_nominal="' . $Site[0] . '", acst_type_site="' . $Site[1] . '"' );
					}
				}

				$_fonctionCourante = 'rechercherPartiesPrenantes';
				$pprLibelle = $objPartiesPrenantes->rechercherPartiesPrenantes( $_SESSION['s_sct_id'], '', $_POST['ppr_id_responsable'] );


				// Ajoute le DMIA à l'Activité
				if ( isset($_POST['dmia_activite']) ) {
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
						$acap_donnees = $Element[3];
						$acap_palliatif = $Element[4];
						$acap_hebergement = $Element[5];
						$acap_niveau_service = $Element[6];
						
						$_fonctionCourante = 'ajouterApplicationActivite';
						$objActivites->ajouterApplicationActivite($Id_Activite,
							$app_id, $ete_id_dima, $ete_id_pdma, $acap_donnees, $acap_palliatif, $acap_hebergement, $acap_niveau_service);
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'act_id="' . $Id_Activite .
							'", app_id="' . $app_id . '", ete_id_dima="' . $ete_id_dima . '", ete_id_pdma="' . $ete_id_pdma . '"' .
							', acap_donnees="' . $acap_donnees . '", acap_palliatif="' . $acap_palliatif . '"' .
							', acap_hebergement="' . $acap_hebergement . '", acap_niveau_service="' . $acap_niveau_service . '"' );
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


				// Ajoute les Personnes Prioritaires (si nécessaire)
				if (isset($_POST['personnes_prioritaires_a_ajouter']) && $_POST['personnes_prioritaires_a_ajouter'] != []) {
					foreach($_POST['personnes_prioritaires_a_ajouter'] as $Element) {
						$act_id = $Element[0];
						$ete_id = $Element[1];
						$rut_nbr_utilisateurs_a_redemarrer = $Element[2];

						$_fonctionCourante = 'ajouterNombrePersonnesPrioritairesActivite';
						$objActivites->ajouterNombrePersonnesPrioritairesActivite($Id_Activite, $ete_id, $rut_nbr_utilisateurs_a_redemarrer);

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_ACTIVITE',
							'act_id="' . $Id_Activite . ', ete_id="' . $ete_id .
							'", rut_nbr_utilisateurs_a_redemarrer="' . $rut_nbr_utilisateurs_a_redemarrer . '"' );
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

			$_POST['flag_personnes_prioritaires'] = $PageHTML->controlerTypeValeur( $_POST['flag_personnes_prioritaires'], 'BOOLEAN' );
			if ( $_POST['flag_personnes_prioritaires'] === -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (flag_personnes_prioritaires)'
				) );
				
				exit();
			}

			try {
				$n_act_id = $objActivites->dupliquerActivite( $_POST['act_id'], $_POST['n_act_nom'],
					$_POST['flag_dmia'], $_POST['flag_fournisseurs'], $_POST['flag_applications'],
					$_POST['flag_personnes_cles'], $_POST['flag_sites'], NULL, $_POST['flag_personnes_prioritaires'] );

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

				$ListeApplications = $objIndispoApplication->rechercherApplicationsSecours( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'],
					$_SESSION['s_act_id'], $Trier );
				$Total = count($ListeApplications); //$objActivites->RowCount;

				foreach ($ListeApplications as $Occurrence) {
					if ($Occurrence->activites != '') {
						$_Tmp = '';
						foreach(explode('¨¨¨', $Occurrence->activites) as $Activite) {
							if ( $_Tmp != '') {
								$_Tmp .= '<hr class="spacer">';
							}

							$T_Activite = explode('^^^', $Activite);

							$_act_id = $T_Activite[0];
							$_act_nom = $T_Activite[1];
/*							$_acap_palliatif = $T_Activite[2];
							$_acap_hebergement = $T_Activite[3];
							$_acap_niveau_service = $T_Activite[4];*/

							$Donnees = $objActivites->recupererSensibiliteActivite($_SESSION['s_cmp_id'], $_act_id);

							//$_Tmp .= '<i class="bi-house-gear-fill fg_bleu"></i>&nbsp;' . $_act_nom . ($Donnees->ete_nom_code != '' ? ' [<span class="fw-bold" style="background-color: #' . $Donnees->nim_couleur . ';">&nbsp;' . $Donnees->nim_nom_code . '&nbsp;</span> / <span class="fw-bold">' . $Donnees->ete_nom_code . '</span>]' : '');
							$_Tmp .= '<i class="bi-gear fg_bleu"></i>&nbsp;' . $_act_nom . ' [<span class="fw-bold" style="background-color: #' . $Donnees->nim_couleur . ';">&nbsp;' . $Donnees->nim_nom_code . '&nbsp;</span> / <span class="fw-bold">' . $Donnees->ete_nom_code . '</span>]';
						}

						$Occurrence->activites = $_Tmp;
					}

					if ($Occurrence->applications_palliatives != '') {
						$_Tmp = '';
						foreach(explode('¨¨¨', $Occurrence->applications_palliatives) as $Application) {
							if ( $_Tmp != '') {
								$_Tmp .= '<hr class="spacer">';
							}

							$T_Application = explode('^^^', $Application);

							$_appa_id = $T_Application[0];
							$_app_id_secours = $T_Application[1];
							$_app_nom_secours = $T_Application[2];
							$_appa_mode_operatoire = $T_Application[3];

							$_Tmp .= '<i class="bi-window-stack fg_vert"></i>&nbsp;' . $_app_nom_secours;
						}

						$Occurrence->applications_palliatives = $_Tmp;
					}

					$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->app_id, $Occurrence, $Format_Colonnes );
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
		try {
			if ( isset($_POST['liste_PEI_ajouter']) ) {
				$Action = 'ajouterEquipeActivite';

				foreach( $_POST['liste_PEI_ajouter'] as $Occurrence ) {
					if ( ! $PageHTML->verifierActiviteAutorisee( $Occurrence[1] ) ) {
						echo json_encode( array(
							'statut' => 'error',
							'texteMsg' => $L_No_Authorize . ' (act_id=' . $Occurrence[1] . ')'
						) );
						
						exit();
					} else {
						$objIndispoFournisseur->ajouterEquipeActivite( $Occurrence );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_EQUIPE_INTERNE',
							'frn_id="' . $Occurrence[0] . '", ' .
							'act_id="' . $Occurrence[1] . '", ' .
							'ein_id="' . $Occurrence[2] . '", ' .
							'ete_id="' . $Occurrence[3] . '", ' .
							'ppr_id="' . $Occurrence[4] . '", ' .
							'pei_commentaires="' . $Occurrence[5] . '"' );
					}
				}
			}
	
			if ( isset($_POST['liste_PEI_modifier']) ) {
				$Action = 'modifierEquipeActivite';

				foreach( $_POST['liste_PEI_modifier'] as $Occurrence ) {
					if ( ! $PageHTML->verifierActiviteAutorisee( $Occurrence[0] ) ) {
						echo json_encode( array(
							'statut' => 'error',
							'texteMsg' => $L_No_Authorize . ' (act_id=' . $Occurrence[0] . ')'
						) );
						
						exit();
					} else {
						$objIndispoFournisseur->modifierEquipeActivite( $Occurrence );

						$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_EQUIPE_INTERNE',
							'pei_id="' . $Occurrence[0] . '", ' .
							'act_id="' . $Occurrence[1] . '", ' .
							'ein_id="' . $Occurrence[2] . '", ' .
							'ete_id="' . $Occurrence[3] . '", ' .
							'ppr_id="' . $Occurrence[4] . '", ' .
							'pei_commentaires="' . $Occurrence[5] . '"' );
					}
				}
			}
	
			if ( isset($_POST['liste_PEI_supprimer']) ) {
				$Action = 'supprimerEquipeInterneActivite';

				foreach( $_POST['liste_PEI_supprimer'] as $Occurrence ) {
					$objIndispoFournisseur->supprimerEquipeActivite( $Occurrence );

					$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_EQUIPE_INTERNE',
						'ipi_id="' . $Occurrence[0] . '"' );
				}
			}

			// ===========================================
			// [ipp_id, act_id, frn_id, ipp_contact, ete_id, ipp_commentaires]
			// [act_id, frn_id, ipp_contact, ete_id, ipp_commentaires]

			if ( isset($_POST['liste_PAP_ajouter']) ) {
				$Action = 'ajouterPrestataireActivite';

				foreach( $_POST['liste_PAP_ajouter'] as $Occurrence ) {
					// [act_id, frn_id, ipp_contact, ete_id, ipp_commentaires]
					if ( ! $PageHTML->verifierActiviteAutorisee( $Occurrence[0] ) ) {
						echo json_encode( array(
							'statut' => 'error',
							'texteMsg' => $L_No_Authorize . ' (act_id=' . $Occurrence[0] . ')'
						) );

						exit();
					} else {
						$objIndispoFournisseur->ajouterFournisseurActivite( $Occurrence );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_PRESTATAIRE',
							'act_id="' . $Occurrence[0] . '", ' .
							'frn_id="' . $Occurrence[1] . '", ' .
							'frn_id_nouveau="' . $Occurrence[2] . '", ' .
							'ipp_contact="' . $Occurrence[3] . '", ' .
							'ete_id="' . $Occurrence[4] . '", ' .
							'ipp_commentaires="' . $Occurrence[5] . '"' );
					}
				}
			}

			if ( isset($_POST['liste_PAP_modifier']) ) {
				$Action = 'modifierPrestataireActivite';
				
				foreach( $_POST['liste_PAP_modifier'] as $Occurrence ) {
					// [ipp_id, act_id, frn_id, ipp_contact, ete_id, ipp_commentaires]
					if ( ! $PageHTML->verifierActiviteAutorisee( $Occurrence[1] ) ) {
						echo json_encode( array(
							'statut' => 'error',
							'texteMsg' => $L_No_Authorize . ' (act_id=' . $Occurrence[1] . ')'
						) );
						
						exit();
					} else {
						$objIndispoFournisseur->modifierFournisseurActivite( $Occurrence );

						$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_PRESTATAIRE',
							'pap_id="' . $Occurrence[0] . '", ' .
							'act_id="' . $Occurrence[1] . '", ' .
							'frn_id="' . $Occurrence[2] . '", ' .
							'frn_id_nouveau="' . $Occurrence[3] . '", ' .
							'pap_contact="' . $Occurrence[4] . '", ' .
							'ete_id="' . $Occurrence[5] . '", ' .
							'pap_commentaires="' . $Occurrence[6] . '"' );
					}
				}
			}

			if ( isset($_POST['liste_PAP_supprimer']) ) {
				$Action = 'supprimerPrestataireActivite';
				
				foreach( $_POST['liste_PAP_supprimer'] as $Occurrence ) {
					$objIndispoFournisseur->supprimerFournisseurActivite( $Occurrence );
					
					$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_PRESTATAIRE',
						'ipp_id="' . $Occurrence[0] . '"' );
				}
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteMsg' => $PageHTML->getLibelle('__LRI_MODIFICATION_REALISEE')
			) );

			exit();
		} catch (Exception $e) {
			$Statut = 'error';
			$Message = $e->getMessage() . ' (' . $Action . ')';
			
			if ( $e->getCode() == 23505 ) {
				$Message = $L_ERR_DUPL_Activite;
			}

			echo json_encode( array(
				'statut' => $Statut,
				'texteMsg' => $Message
			) );

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
			list($Liste_Societes, $Liste_Campagnes, $Liste_Entites, $Liste_Activites) =
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
			'act_id' => $_SESSION['s_act_id'],
			'L_Societe_Sans_Campagne' => $L_Societe_Sans_Campagne,
			'L_Gestion_Campagnes' => $L_Gestion_Campagnes,
			'L_Campagne_Sans_Entite' => $L_Campagne_Sans_Entite,
			'L_Gestion_Entites' => $L_Gestion_Entites,
			'Liste_Campagnes' => $Liste_Campagnes,
			'Liste_Entites' => $Liste_Entites,
			'Liste_Activites' => $Liste_Activites,
			'L_Activite' => $PageHTML->getLibelle('__LRI_ACTIVITE'),
			'L_Activites' => $PageHTML->getLibelle('__LRI_ACTIVITES')
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
			list($Liste_Societes, $Liste_Campagnes, $Liste_Entites, $Liste_Activites) =
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
			'act_id' => $_SESSION['s_act_id'],
			'Liste_Entites' => $Liste_Entites,
			'Liste_Activites' => $Liste_Activites,
			'L_Activite' => $PageHTML->getLibelle('__LRI_ACTIVITE'),
			'L_Activites' => $PageHTML->getLibelle('__LRI_ACTIVITES')
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

		try {
			list($Liste_Societes, $Liste_Campagnes, $Liste_Entites, $Liste_Activites) =
			actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites, 3);
		} catch ( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
			echo json_encode( $Resultat );
			break;
		}

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Entite_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id'],
			'ent_id' => $_SESSION['s_ent_id'],
			'Liste_Activites' => $Liste_Activites,
			'L_Activite' => $PageHTML->getLibelle('__LRI_ACTIVITE'),
			'L_Activites' => $PageHTML->getLibelle('__LRI_ACTIVITES')
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (ent_id)' );
	}

	echo json_encode( $Resultat );

	break;
	
	
 case 'AJAX_Selectioner_Activite':
	if ( isset($_POST['act_id']) ) {
		$_SESSION['s_act_id'] = $_POST['act_id'];

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Activite_Modifiee,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id'],
			'ent_id' => $_SESSION['s_ent_id'],
			'act_id' => $_SESSION['s_act_id'],
			'L_Activite' => $PageHTML->getLibelle('__LRI_ACTIVITE'),
			'L_Activites' => $PageHTML->getLibelle('__LRI_ACTIVITES')
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (ent_id)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Ajouter_Equipe_Interne':
 	if ( isset($_POST['ein_nom']) && $_POST['ein_nom'] != '' ) {
		try {
			$objIndispoPersonnel->ajouterModifierEquipeInterne( '', $_SESSION['s_sct_id'], $_POST['ein_nom'] );
		} catch ( PDOException $e) {
			if ($e->getCode() == 23505) {
				$Message = $L_Equipe . ' <strong>' . $_POST['ein_nom'] . '</strong> : ' . $PageHTML->getLibelle('__LRI_ERR_SYS_DEJA_EXISTANT');
			} else {
				$Message = $e->getMessage();
			}

			echo json_encode( array( 'statut' => 'error',
				'texteMsg' => $Message ) );

			exit();
		}

		$ein_id = $objIndispoPersonnel->LastInsertId;

		$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_EQUIPE_INTERNE',
			'ein_id="' . $ein_id . '", ' .
			'sct_id="' . $_SESSION['s_sct_id'] . '", ' .
			'ein_nom="' . $_POST['ein_nom'] . '"' );

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $PageHTML->getLibelle('__LRI_EQUIPE_INTERNE_AJOUTEE'),
			'ein_id' => $ein_id,
			'ein_nom' => $_POST['ein_nom']
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (ein_nom)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Ajouter_Responsable_Equipe':
	if ( isset($_POST['ppr_nom']) && isset($_POST['ppr_prenom']) ) {
		try {
			$objPartiesPrenantes->majPartiePrenante( '', $_SESSION['s_sct_id'], $_POST['ppr_nom'], $_POST['ppr_prenom'], $_POST['ppr_interne'] );
		} catch ( PDOException $e) {
			if ($e->getCode() == 23505) {
				$Message = $L_Partie_Prenante . ' <strong>' . $_POST['ppr_nom'] . ' ' . $_POST['ppr_prenom'] . '</strong> : ' . $PageHTML->getLibelle('__LRI_ERR_SYS_DEJA_EXISTANT');
			} else {
				$Message = $e->getMessage();
			}
			
			echo json_encode( array( 'statut' => 'error',
				'texteMsg' => $Message ) );
			
			exit();
		}

		$ppr_id = $objPartiesPrenantes->LastInsertId;

		$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_PARTIE_PRENANTE',
			'ppr_id="' . $ppr_id . '", ' .
			'sct_id="' . $_SESSION['s_sct_id'] . '", ' .
			'ppr_nom="' . $_POST['ppr_nom'] . '", ' .
			'ppr_prenom="' . $_POST['ppr_prenom'] . '"' );
		
		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $PageHTML->getLibelle('__LRI_EQUIPE_INTERNE_AJOUTEE'),
			'ppr_id' => $ppr_id,
			'ppr_nom' => $_POST['ppr_nom'],
			'ppr_prenom' => $_POST['ppr_prenom']
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (ppr_nom ou ppr_prenom)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Ajouter_Prestataire':
		if ( isset($_POST['frn_nom']) && isset($_POST['tfr_id']) ) {
			try {
				$objFournisseurs->majFournisseur( '', $_POST['tfr_id'], $_POST['frn_nom'], $_POST['frn_description'] );
			} catch ( PDOException $e) {
				if ($e->getCode() == 23505) {
					$Message = $L_Fournisseur . ' <strong>' . $_POST['frn_nom'] . '</strong> : ' . $PageHTML->getLibelle('__LRI_ERR_SYS_DEJA_EXISTANT');
				} else {
					$Message = $e->getMessage();
				}
				
				echo json_encode( array( 'statut' => 'error',
					'texteMsg' => $Message ) );
				
				exit();
			}

			$frn_id = $objFournisseurs->LastInsertId;

			$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_PRESTATAIRE',
				'frn_id="' . $frn_id . '", ' .
				'frn_nom="' . $_POST['frn_nom'] . '", ' .
				'frn_description="' . $_POST['frn_description'] . '"' );

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $PageHTML->getLibelle('__LRI_PRESTATAIRE_AJOUTEE'),
				'frn_id' => $frn_id,
				'frn_nom' => $_POST['frn_nom'],
				'frn_description' => $_POST['frn_description']
			);
		} else {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires . ' (frn_nom)' );
		}

		echo json_encode( $Resultat );

		break;
		
		
 case 'AJAX_Ajouter_Application':
	if ( isset($_POST['app_nom']) ) {
		try {
			$objApplications->majApplication( '', $_POST['app_nom'], $_POST['frn_id'], $_POST['app_hebergement'], $_POST['app_niveau_service'],
				$_POST['app_description'] );
		} catch ( PDOException $e) {
			if ($e->getCode() == 23505) {
				$Message = $L_Application . ' <strong>' . $_POST['app_nom'] . '</strong> : ' . $PageHTML->getLibelle('__LRI_ERR_SYS_DEJA_EXISTANT');
			} else {
				$Message = $e->getMessage();
			}

			echo json_encode( array( 'statut' => 'error',
				'texteMsg' => $Message ) );

			exit();
		}

		$app_id = $objApplications->LastInsertId;

		$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_APPLICATION',
			'app_id="' . $app_id . '", ' .
			'app_nom="' . $_POST['app_nom'] . '", ' .
			'frn_id="' . $_POST['frn_id'] . '", ' .
			'app_hebergement="' . $_POST['app_hebergement'] . '", ' .
			'app_niveau_service="' . $_POST['app_niveau_service'] . '", ' .
			'app_description="' . $_POST['app_description'] . '"');

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Application_Cree,
			'app_id' => $app_id,
			'app_nom' => $_POST['app_nom'],
			'app_description' => $_POST['app_description']
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (app_nom)' );
	}

	echo json_encode( $Resultat );

	break;
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
	$Liste_Activites = [];


	switch ( $forcer ) {
		case 1:
			// Comme on vient de change de Société, on efface les variables de Session qui pointaient :
			//   sur une Campagne
			//   et sur une Entité.
			// Ainsi, on forcera le repositionnement sur la première occurrence de Campagne et d'Entité.
			unset($_SESSION['s_cmp_id']);
			unset($_SESSION['s_ent_id']);
			unset($_SESSION['s_act_id']);
			break;

		case 2:
			// Comme on vient de change de Campagne, on efface la variable de Session qui pointait sur une Entité.
			// Ainsi, on forcera le repositionnement sur la première occurrence d'Entité.
			unset($_SESSION['s_ent_id']);
			unset($_SESSION['s_act_id']);
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
			$_SESSION['s_act_id'] = '';

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
		$tmpObj1 = new stdClass();
		$tmpObj1->cmp_id = '';
		$tmpObj1->cmp_date = '---';
		$Liste_Campagnes[0] = $tmpObj1;

		$tmpObj2 = new stdClass();
		$tmpObj2->ent_id = '';
		$tmpObj2->ent_nom = '---';
		$tmpObj2->total_activites = 0;
		$Liste_Entites[0] = $tmpObj2;

		$tmpObj3 = new stdClass();
		$tmpObj3->act_id = '';
		$tmpObj3->act_nom = '---';
		$tmpObj3->total_activites = 0;
		$Liste_Activites[0] = $tmpObj3;

		$_SESSION['s_cmp_id'] = '';
		$_SESSION['s_ent_id'] = '';
		$_SESSION['s_act_id'] = '';
		
		//throw new Exception($L_Pas_Campagne_Pour_Societe, 0);
		return [$Liste_Societes, $Liste_Campagnes, $Liste_Entites];
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
		$tmpObj2 = new stdClass();
		$tmpObj2->ent_id = '';
		$tmpObj2->ent_nom = '---';
		$tmpObj2->total_activites = 0;
		$Liste_Entites[0] = $tmpObj2;

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

	// Récupère les Activités associées à la Campagne et à l'Entité Sélectionnée
	$Liste_Activites = $objActivites->rechercherActivitesEntiteCampagne($_SESSION['s_cmp_id'], $_SESSION['s_ent_id']);
	if ( $Liste_Activites == [] ) {
		$tmpObj3 = new stdClass();
		$tmpObj3->act_id = '';
		$tmpObj3->act_nom = '---';
		$tmpObj3->total_activites = 0;
		$Liste_Activites[0] = $tmpObj3;
		
		$_SESSION['s_act_id'] = '';
	} else {
		if ( ! isset($_SESSION['s_act_id']) or $_SESSION['s_act_id'] == '' ) {
			$_SESSION['s_act_id'] = $Liste_Activites[0]->act_id;
		} else {
			// On contrôle que l'utilisateur a encore accès à cette Société.
			$_Autorise = 0;
			foreach ($Liste_Activites as $_Tmp) {
				if ( $_Tmp->act_id == $_SESSION['s_act_id'] ) {
					$_Autorise = 1;
					break;
				}
			}
			
			if ( $_Autorise == 0 ) {
				$_SESSION['s_act_id'] = $Liste_Activites[0]->act_id;
			}
		}
	}

	return [$Liste_Societes, $Liste_Campagnes, $Liste_Entites, $Liste_Activites];
}

?>