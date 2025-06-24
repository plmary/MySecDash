<?php

use PhpOffice\PhpWord\Element\TextRun;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


/**
* Ce script gère les Campagnes.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2025-04-03
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );


// Charge les informations et droits de base d'un écran.
include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
//include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-Connexion.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Activites.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Applications.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Campagnes.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-EchellesTemps.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Fournisseurs.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-MatriceImpacts.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-PartiesPrenantes.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-RolesPartiesPrenantes.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Sites.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-TypesFournisseur.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Rapports.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );

$L_Organisation = $PageHTML->getLibelle('__LRI_ORGANISATION');
$L_Synthese = $PageHTML->getLibelle('__LRI_SYNTHESE');


include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Sites_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_EchellesTemps_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Applications_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Fournisseurs_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_MatriceImpacts_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Activites_PDO.inc.php' );


// Crée l'instance de l'objet Entites.
$objCampagnes = new Campagnes();
$objSocietes = new HBL_Societes();
$objEntites = new HBL_Entites();
$objSites = new Sites();
$objEchellesTemps = new EchellesTemps();
$objApplications = new Applications();
$objFournisseurs = new Fournisseurs();
$objMatriceImpacts = new MatriceImpacts();
$objActivites = new Activites();


// Définit le format des colonnes du tableau central.
$Trier = 'cmp_date';

$Format_Colonnes[ 'Prefixe' ] = 'CMP';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'cmp_id' );
$Format_Colonnes[ 'Colonnes' ][] = array(
	'nom' => 'cmp_date', 'titre' => $L_Campagne, 'taille' => '6', 'maximum' => '10',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'cmp_date-desc', 'type' => 'input', 'modifiable' => 'non' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'imprimer' => $Droit_Lecture ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Societes = $objSocietes->rechercherSocietes();
	} else {
		$Liste_Societes = $objSocietes->rechercherSocietes('', '', $_SESSION['idn_id'] );
	}

	if ( ! isset( $_SESSION['s_sct_id'] ) ) {
		$_SESSION['s_sct_id'] = $Liste_Societes[0]->sct_id;
	}

	$Boutons_Alternatifs = [
	//['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'] //,
	['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search']
	];

	$Fichiers_JavaScript[] = 'MatriceImpacts.js';

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Editions_BIA, $Fichiers_JavaScript, '3' ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Editions_BIA, $Liste_Societes, $Boutons_Alternatifs )
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
		'L_Edition_BIA' => $L_Edition_BIA,
		'L_Fermer' => $L_Fermer,
		'L_Rechercher' => $L_Rechercher,
		'L_Editer' => $L_Editer,
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
		'L_Source' => $L_Source,
		'L_Cible' => $L_Cible,
		'L_Oui' => $L_Yes,
		'L_Non' => $L_No,
		'L_Type' => $L_Type,
		'L_Niveau' => $L_Niveau,
		'L_Societe' => $L_Societe,
		'L_Campagne' => $L_Campagne,
		'L_Format_Edition' => $L_Format_Edition,
		'L_Tout_Cocher_Decocher' => $L_Tout_Cocher_Decocher,
		'L_Chapitres' => $L_Chapitres,
		'L_Synthese_Manageriale_Globale' => $L_Synthese_Manageriale_Globale,
		'L_Liste_Activites' => $L_Liste_Activites,
		'L_Liste_Applications' => $L_Liste_Applications,
		'L_Liste_Personnes_Cles' => $L_Liste_Personnes_Cles,
		'L_Liste_Fournisseurs' => $L_Liste_Fournisseurs,
		'L_Detail_Activites' => $L_Detail_Activites,
		'L_Entite' => $L_Entite,
		'L_Toutes' => $L_Toutes,
		'L_Planning' => $PageHTML->getLibelle('__LRI_PLANNING'),
		'L_Liste_Personnes_Prioritaires' => $PageHTML->getLibelle('__LRI_LISTE_PERSONNES_PRIORITAIRES'),
		'L_Liste_Interdependances' => $PageHTML->getLibelle('__LRI_LISTE_INTERDEPENDANCES')
		);

	if ( isset( $_POST['cmp_id'] ) ) {
		if ( $_POST['cmp_id'] != '' ) {
			$Libelles['objCampagne'] = $objCampagnes->detaillerCampagne( $_POST['cmp_id'] );
			$Libelles['Liste_Campagnes'] = $objCampagnes->rechercherCampagnes( $_SESSION['s_sct_id'], 'cmp_date-desc' );
			//$Libelles['Liste_Niveaux_Impact'] = $objCampagnes->rechercherNiveauxImpactCampagne( $_POST['cmp_id'] );
			//$Libelles['Liste_Types_Impact'] = $objCampagnes->rechercherTypesImpactCampagne( $_POST['cmp_id'] );
			//$Libelles['Liste_Matrice_Impacts'] = $objCampagnes->rechercherMatriceImpactsCampagne( $_POST['cmp_id'] );
			$Libelles['Liste_Entites'] = $objCampagnes->rechercherEntitesAssocieesCampagne( $_SESSION['s_sct_id'], $_POST['cmp_id'] );
			$Libelles['Liste_Sites'] = $objCampagnes->rechercherSitesCampagne( $_SESSION['s_sct_id'], $_POST['cmp_id'] );
			$Libelles['Liste_Echelle_Temps'] = $objCampagnes->rechercherEchelleTempsCampagne( $_POST['cmp_id'] );
//			$Libelles['Liste_Applications'] = $objCampagnes->rechercherApplicationsCampagne( $_POST['cmp_id'] );
//			$Libelles['Liste_Fournisseurs'] = $objCampagnes->rechercherFournisseursCampagne( $_POST['cmp_id'] );
			$Libelles['Liste_Types_Fournisseur'] = $objFournisseurs->rechercherTypesFournisseur();
			$Libelles['Liste_Niveaux_Impact'] = $objCampagnes->rechercherNiveauxImpactCampagne( $_POST['cmp_id'] );
			$Libelles['Liste_Types_Impact'] = $objCampagnes->rechercherTypesImpactCampagne( $_POST['cmp_id'] );
			$Libelles['Liste_Matrice_Impacts'] = $objMatriceImpacts->rechercherMatriceImpactsParID( $_POST['cmp_id'] );
			$Libelles['cmp_id'] = $_POST['cmp_id'];
		} else {
			$Libelles['Liste_Campagnes'] = $objCampagnes->rechercherCampagnes( $_SESSION['s_sct_id'], 'cmp_date-desc' );
			$Libelles['Liste_Entites'] = $objEntites->rechercherEntites( $_SESSION['s_sct_id'] );
			$Libelles['Liste_Sites'] = $objSites->rechercherSites( $_SESSION['s_sct_id'] );
//			$Libelles['Liste_Applications'] = $objApplications->rechercherApplications();
//			$Libelles['Liste_Fournisseurs'] = $objFournisseurs->rechercherFournisseurs();
			$Libelles['Liste_Types_Fournisseur'] = $objFournisseurs->rechercherTypesFournisseur();
		}
	}
	
	print( json_encode( $Libelles ) );

	exit();


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


 case 'AJAX_Selectioner_Societe':
	if ( isset($_POST['sct_id']) ) {
		$_SESSION['s_sct_id'] = $_POST['sct_id'];
		
		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Societe_Change );
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (sct_id)' );
	}

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Editer_Campagne':
	require_once CHEMIN_APPLICATION . '/vendor/autoload.php';
	//require_once CHEMIN_APPLICATION . '/vendor/dompdf/autoload.inc.php';

	if ( isset($_POST['nom_redacteur']) ) {
		$Nom_Redacteur = $_POST['nom_redacteur'];
	} else {
		$Nom_Redacteur = '';
	}

	if ( $_POST['entite_a_editer'] == '*' ) {
		$_Nom_Entite = $L_Toutes;
		$_ID_Entite = '';
	} else {
		$_Nom_Entite = substr( $_POST['nom_entite_a_editer'], 0, stripos($_POST['nom_entite_a_editer'], ' - ') );
		$_Nom_Entite = str_replace([':', '/', '\\'], '-', $_Nom_Entite);
		$_ID_Entite = $_POST['entite_a_editer'];
	}
	
	$Nom_Fichier = 'Restitution - '.$_POST['sct_nom'].' - '.$_POST['cmp_date'].' - '.$_Nom_Entite.' '.date('[Y-m-d - H\hi\ms\s]');

	$Liste_Entites = $objCampagnes->rechercherEntitesCampagne($_POST['cmp_id'], $_ID_Entite);
	$Liste_EchellesTemps = $objEchellesTemps->rechercherEchellesTemps($_POST['cmp_id']);
	$Liste_Niveaux_Impact = $objCampagnes->rechercherNiveauxImpactCampagne( $_POST['cmp_id'] );
	$Liste_Types_Impact = $objCampagnes->rechercherTypesImpactCampagne( $_POST['cmp_id'] );
	$Liste_Matrice_Impacts = $objMatriceImpacts->rechercherMatriceImpactsParID( $_POST['cmp_id'] );
	$Liste_Sites = $objActivites->rechercherSitesCampagne($_POST['cmp_id']);


	if ( $_POST['format_edition'] == 'docx'
	 || $_POST['format_edition'] == 'odt'
	 || $_POST['format_edition'] == 'html'
	 || $_POST['format_edition'] == 'pdf') {
		// Création du nouveau document
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		//$phpWord = \PhpOffice\PhpWord\IOFactory::load(DIR_TEMPLATES . '/MSD-Template-Rapport.docx');
	
		// Paramétrage du document.
		$phpWord->getSettings()->setDecimalSymbol(',');
		$phpWord->getSettings()->setThemeFontLang(
			new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::FR_FR));
		// Force la mise à jour des champs
		//$phpWord->getSettings()->setUpdateFields(true);
		
		\PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
		
		$properties = $phpWord->getDocInfo();
		$properties->setCreator($PageHTML->Nom_Outil_TXT.' v'.$PageHTML->Version_Outil);
		$properties->setCompany($PageHTML->Nom_Societe);
		$properties->setTitle($L_Dossier_Restitution);
		$properties->setSubject($L_Conclusion_BIAs);
		$properties->setDescription('Automatic report');
		$properties->setCategory('Restitution');
		$properties->setLastModifiedBy($Nom_Redacteur);
		$properties->setCreated(mktime(0, 0, 0, 3, 12, 2014));
		$properties->setModified(mktime(0, 0, 0, 3, 14, 2014));
		$properties->setKeywords('mysecdash, word, bia, conclusion');
	
		// Définition des styles
		$fontStyle10 = ['size' => 10];
		$fontStyle12 = ['size' => 12];
		$fontStyle14 = ['size' => 14];
		$fontStyle16 = ['size' => 16];
		$fontStyle18 = ['size' => 18];
		$fontStyle20 = ['size' => 20];
		$fontStyle22 = ['size' => 22];
		
		$fontStyle10Fort = array_merge($fontStyle10, ['bold' => true]);
		$fontStyle12Fort = array_merge($fontStyle12, ['bold' => true]);
		$fontStyle14Fort = array_merge($fontStyle14, ['bold' => true]);
		$fontStyle16Fort = array_merge($fontStyle16, ['bold' => true]);
		$fontStyle18Fort = array_merge($fontStyle18, ['bold' => true]);
		$fontStyle20Fort = array_merge($fontStyle20, ['bold' => true]);
		$fontStyle22Fort = array_merge($fontStyle22, ['bold' => true]);
		
		$fontStyle10FortRouge = array_merge($fontStyle10Fort, ['color' => 'C34A36']);
		$fontStyle12FortRouge = array_merge($fontStyle12Fort, ['color' => 'C34A36']);
		$fontStyle16FortRouge = array_merge($fontStyle16Fort, ['color' => 'C34A36']);
		$fontStyle18FortRouge = array_merge($fontStyle18Fort, ['color' => 'C34A36']);
		$fontStyle20FortRouge = array_merge($fontStyle20Fort, ['color' => 'C34A36']);
		$fontStyle22FortRouge = array_merge($fontStyle22Fort, ['color' => 'C34A36']);
		
		$fontStyle10FortBleu  = array_merge($fontStyle10Fort, ['color' => '44808A']);
		$fontStyle12FortBleu  = array_merge($fontStyle12Fort, ['color' => '44808A']);
		$fontStyle16FortBleu  = array_merge($fontStyle16Fort, ['color' => '44808A']);
		$fontStyle18FortBleu  = array_merge($fontStyle18Fort, ['color' => '44808A']);
		$fontStyle20FortBleu  = array_merge($fontStyle20Fort, ['color' => '44808A']);
		
		$fontStyle10FortVert  = array_merge($fontStyle10Fort, ['color' => '717D11']);
		$fontStyle12FortVert  = array_merge($fontStyle12Fort, ['color' => '717D11']);
		$fontStyle16FortVert  = array_merge($fontStyle16Fort, ['color' => '717D11']);
		$fontStyle18FortVert  = array_merge($fontStyle18Fort, ['color' => '717D11']);
		$fontStyle20FortVert  = array_merge($fontStyle20Fort, ['color' => '717D11']);
		
		$fontStyle12BgFortRouge = array_merge($fontStyle12Fort, ['bgColor' => 'C34A36', 'color' => 'FFFFFF']);
		$fontStyle12BgFortBleu  = array_merge($fontStyle12Fort, ['bgColor' => '44808A', 'color' => 'FFFFFF']);
		$fontStyle12BgFortVert  = array_merge($fontStyle12Fort, ['bgColor' => '717D11', 'color' => 'FFFFFF']);
	
		$fontStyle16BgFortRouge = array_merge($fontStyle16Fort, ['bgColor' => 'C34A36', 'color' => 'FFFFFF']);
		$fontStyle16BgFortBleu  = array_merge($fontStyle16Fort, ['bgColor' => '44808A', 'color' => 'FFFFFF']);
		$fontStyle16BgFortVert  = array_merge($fontStyle16Fort, ['bgColor' => '717D11', 'color' => 'FFFFFF']);
		$fontStyle16BgFortGris  = array_merge($fontStyle16Fort, ['bgColor' => 'C0C0C0', 'color' => 'FFFFFF']);
		
		$fontTitreTableau = $fontStyle12Fort;
//		$fontTitre2Tableau = array_merge($fontStyle12Fort, );
		$fontTexteTableau = $fontStyle10;
		$fontTexteFortTableau = $fontStyle10Fort;
		$styleParagrapheTableau = ['spaceBefore' => 60, 'spaceAfter' => 60];
		
		$fontTitreDocument = ['spaceBefore' => 160, 'spaceAfter' => 60, 'size' => 30, 'color' => 'C34A36',
			'bold' => true];
		$fontSujetDocument = ['spaceBefore' => 160, 'spaceAfter' => 60, 'size' => 24, 'color' => '44808A',
			'bold' => true];
		
		$phpWord->addTitleStyle(null, ['size' => 24, 'bold' => true]);
		$phpWord->addTitleStyle(1, $fontStyle22FortRouge, ['spaceBefore' => 280, 'spaceAfter' => 60, 'keepNext' => true]);
		$phpWord->addTitleStyle(2, $fontStyle18FortBleu, ['spaceBefore' => 220, 'spaceAfter' => 60, 'keepNext' => true]);
		$phpWord->addTitleStyle(3, $fontStyle16FortVert, ['spaceBefore' => 220, 'spaceAfter' => 60, 'keepNext' => true]);
	
	
		$Donnees = $objCampagnes->syntheseCampagne( $_POST['cmp_id'] );
	
		// ==============================
		// Création de la page de garde.
		$section = $phpWord->addSection(['orientation' => 'landscape']);
		$section->addTextBreak(3);
		$section->addImage(DIR_IMAGES.'/Logo-MyContinuity.png',
			['width' => 250, 'height' => 100, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER], false,
			'MyContinuity');
		$section->addTextBreak(2);
		$section->addText($L_Dossier_Restitution, $fontTitreDocument);
		$section->addText($L_Conclusion_BIAs, $fontSujetDocument);
		$textrun = $section->addTextRun();
		$textrun->addText($L_Societe . ' : ', $fontStyle16Fort);
		$textrun->addText($_POST['sct_nom'], $fontStyle16FortVert);
		$textrun = $section->addTextRun();
		$textrun->addText($L_Campagne . ' : ', $fontStyle16Fort);
		$textrun->addText($Donnees['campagne']->cmp_date, $fontStyle16FortVert);
		
	
		// Ajoute un pied de page
		$footer = $section->addFooter();
		$table = $footer->addTable();
		$table->addRow();
		$table->addCell(5000)->addLink('https://www.loxense.fr', 'Loxense');
		$table->addCell(5000)->addText('C2 - Diffusion Restreinte', $fontStyle12FortRouge, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
		$table->addCell(5000)->addPreserveText('{PAGE} / {NUMPAGES}', null, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
	
	
		// =================================================================
		// Création d'une nouvelle section et ajoute une table des matières
		$section = $phpWord->addSection(['orientation' => 'landscape', 'breakType' => 'nextPage']);
	
	
		// Ajoute un entête de page
		$footer = $section->addHeader();
		$table = $footer->addTable();
		$table->addRow();
		$table->addCell(2500)->addImage(DIR_IMAGES.'/Logo-MyContinuity.png',
			['width' => 75, 'height' => 30, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER], false,
			'MyContinuity');
		$textrun = $table->addCell(7500)->addTextRun();
		$textrun->addText($L_BIAs_Societe . ' ', $fontStyle12);
		$textrun->addText($_POST['sct_nom'], $fontStyle12FortBleu);
		$textrun->addTextBreak(1);
		$textrun->addText($L_Campagne . ' : ', $fontStyle12);
		$textrun->addText($Donnees['campagne']->cmp_date, $fontStyle12FortBleu);
		$textrun = $table->addCell(5000)->addTextRun(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
		$textrun->addText($L_Date . ' : ', $fontStyle12);
		$textrun->addText(date('Y-m-d'), $fontStyle10FortVert);
		
		
		// Ajoute un pied de page
		$footer = $section->addFooter();
		$table = $footer->addTable();
		$table->addRow();
		$table->addCell(5000)->addLink('https://www.loxense.fr', 'Loxense');
		$table->addCell(5000)->addText('C2 - Diffusion Restreinte', $fontStyle12FortRouge, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
		$table->addCell(5000)->addPreserveText('{PAGE} / {NUMPAGES}', null, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END]);
		
		$section->addTitle($L_Sommaire, 0);
		$toc = $section->addTOC($fontStyle12);
	
	
	
		// ====================================
		// Gestion de la synthèse managériale.
		if ( $_POST['flag_synthese_manager'] == 'true' && $_POST['entite_a_editer'] == '*' ) {
			$section->addPageBreak();
	
			$Nombre_BIA_A_Faire = $Donnees['total_bia'] - ( $Donnees['total_bia_valides'] + $Donnees['total_bia_en_cours'] );
	
			// Construit le tableau de synthèse.
			$section->addTitle( $L_Synthese_Manageriale_Globale );
	
			$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699',
				'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160]);
			$table->addRow();
	
			if ($Donnees['total_bia'] > 1) {
				$_BIA = $L_T_BIAs;
				$_Auxiliere = $L_T_Ont_Ete;
				$_Verbe = $L_T_Identifies;
			} else {
				$_BIA = $L_T_BIA;
				$_Auxiliere = $L_T_A_Ete;
				$_Verbe = $L_T_Identifie;
			}
			$L_T_Pour_Cette_Campagne;
			
			$textrun = $table->addCell(15000, ['gridSpan' => 2, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER])->addTextRun($styleParagrapheTableau);
			$textrun->addText($Donnees['total_bia'] . ' '.$_BIA, $fontStyle20FortBleu);
			$textrun->addText(' '.$_Auxiliere.' ', $fontStyle14);
			$textrun->addText($_Verbe, $fontStyle16FortBleu);
			$textrun->addText(' '.$L_T_Pour_Cette_Campagne, $fontStyle14);
	
	
			if ($Donnees['total_bia_valides'] > 1) {
				$_BIA = $L_T_BIAs;
				$_Auxiliere = $L_T_Sont;
				$_Verbe = $L_T_Valides;
			} else {
				$_BIA = $L_T_BIA;
				$_Auxiliere = $L_T_Est;
				$_Verbe = $L_T_Valide;
			}
	
			$textrun->addText(', ', $fontStyle14);
			$textrun->addText($Donnees['total_bia_valides'] . ' '.$_BIA, $fontStyle20FortVert);
			$textrun->addText(' '.$_Auxiliere.' ', $fontStyle14);
			$textrun->addText($_Verbe, $fontStyle16FortVert);
	
	
			if ($Donnees['total_bia_en_cours'] > 1) {
				$_BIA = $L_T_BIAs;
				$_Auxiliere = $L_T_Sont;
				$_Verbe = $L_T_En_Cours;
			} else {
				$_BIA = $L_T_BIA;
				$_Auxiliere = $L_T_Est;
				$_Verbe = $L_T_En_Cours;
			}
	
			$textrun->addText(', ', $fontStyle14);
			$textrun->addText($Donnees['total_bia_en_cours'] . ' '.$_BIA, $fontStyle20FortBleu);
			$textrun->addText(' '.$_Auxiliere.' ', $fontStyle14);
			$textrun->addText($_Verbe, $fontStyle16FortBleu);
	
	
			if ($Nombre_BIA_A_Faire > 1) {
				$_BIA = $L_T_BIAs;
				$_Auxiliere = $L_T_Sont;
				$_Verbe = $L_T_A_Faire;
			} else {
				$_BIA = $L_T_BIA;
				$_Auxiliere = $L_T_Est;
				$_Verbe = $L_T_A_Faire;
			}
	
			$textrun->addText(', ', $fontStyle14);
			$textrun->addText($Nombre_BIA_A_Faire . ' '.$_BIA, $fontStyle20FortRouge);
			$textrun->addText(' '.$_Auxiliere.' ', $fontStyle14);
			$textrun->addText($_Verbe, $fontStyle16FortRouge);
	
	
			// Nouvelle occurrence du tableau
			$table->addRow();
	
			if ($Donnees['total_act_3_4'] > 1) {
				$_Sujet = $L_T_Activites;
				$_Auxiliere = $L_T_Sont;
				$_Type = $L_T_Essentielles;
			} else {
				$_Sujet = $L_T_Activite;
				$_Auxiliere = $L_T_Est;
				$_Type = $L_T_Essentielle;
			}
			$TextRun = $table->addCell(7500)->addTextRun($styleParagrapheTableau);
			$TextRun->addImage(DIR_IMAGES . DIRECTORY_SEPARATOR . 'glyphicons' . DIRECTORY_SEPARATOR .
				'png' . DIRECTORY_SEPARATOR . 'glyphicons_136_cogwheel.png', ['bgColor' => '44808A']);
			$TextRun->addText(' ', $fontStyle14);
			$TextRun->addText($Donnees['total_act_3_4'], $fontStyle20FortBleu);
			$TextRun->addText(' '.$_Sujet, $fontStyle16FortBleu);
			$TextRun->addText(' '.$_Auxiliere.' ', $fontStyle14);
			$TextRun->addText($_Type, $fontStyle16FortBleu);
			$TextRun->addText(' '.$L_T_Def_Activites_Essentielles, $fontStyle14);
	
	
			if ($Donnees['total_act_4'] > 1) {
				$_Sujet = $L_T_Activites;
				$_Auxiliere = $L_T_Sont;
				$_Type = $L_T_Vitales;
			} else {
				$_Sujet = $L_T_Activite;
				$_Auxiliere = $L_T_Est;
				$_Type = $L_T_Vitale;
			}
			$TextRun = $table->addCell(7500)->addTextRun($styleParagrapheTableau);
			$TextRun->addImage(DIR_IMAGES . DIRECTORY_SEPARATOR . 'glyphicons' . DIRECTORY_SEPARATOR .
				'png' . DIRECTORY_SEPARATOR . 'glyphicons_053_alarm.png', ['bgColor' => '44808A']);
			$TextRun->addText(' ', $fontStyle14);
			$TextRun->addText($Donnees['total_act_4'], $fontStyle20FortRouge);
			$TextRun->addText(' '.$_Sujet, $fontStyle16FortRouge);
			$TextRun->addText(' '.$_Auxiliere.' ', $fontStyle14);
			$TextRun->addText($_Type, $fontStyle16FortRouge);
			$TextRun->addText(' '.$L_T_Def_Activites_Vitales, $fontStyle14);
	
	
			// Nouvelle occurrence du tableau
			$table->addRow();
			
			if ($Donnees['total_sts'] > 1) {
				$_Sujet = $L_T_Sites;
				$_Suite = $L_T_Ont_Ete . ' ' . $L_T_Identifies . ' ' . $L_T_Ensemble_Activites_Analysees;
			} else {
				$_Sujet = $L_T_Site;
				$_Suite = $L_T_A_Ete . ' ' . $L_T_Identifie . ' ' . $L_T_Ensemble_Activites_Analysees;
			}
			$TextRun = $table->addCell(7500)->addTextRun($styleParagrapheTableau);
			$TextRun->addImage(DIR_IMAGES . DIRECTORY_SEPARATOR . 'glyphicons' . DIRECTORY_SEPARATOR .
				'png' . DIRECTORY_SEPARATOR . 'glyphicons_089_building.png', ['color' => '44808A']);
			$TextRun->addText(' ', $fontStyle14);
			$TextRun->addText($Donnees['total_sts'], $fontStyle20FortVert);
			$TextRun->addText(' '.$_Sujet, $fontStyle16FortVert);
			$TextRun->addText(' '.$_Suite, $fontStyle14);
	
	
			if ($Donnees['total_app'] > 1) {
				$_Sujet = $L_T_Applications;
				$_Suite = $L_T_Supportant_Activites;
			} else {
				$_Sujet = $L_T_Application;
				$_Suite = $L_T_Supportant_Activites;
			}
			$TextRun = $table->addCell(7500)->addTextRun($styleParagrapheTableau);
			$TextRun->addImage(DIR_IMAGES . DIRECTORY_SEPARATOR . 'glyphicons' . DIRECTORY_SEPARATOR .
				'png' . DIRECTORY_SEPARATOR . 'glyphicons_119_table.png', ['bgColor' => '44808A']);
			$TextRun->addText(' ', $fontStyle14);
			$TextRun->addText($Donnees['total_app'], $fontStyle20FortVert);
			$TextRun->addText(' '.$_Sujet, $fontStyle16FortVert);
			$TextRun->addText(' '.$_Suite, $fontStyle14);
	
	
			// Nouvelle occurrence du tableau
			$table->addRow();
			
			if ($Donnees['total_ppr'] > 1) {
				$_Sujet = $L_T_Personnes_Cles;
			} else {
				$_Sujet = $L_T_Personne_Cle;
			}
			$_Suite = $L_T_Supportant_Activites;
			$TextRun = $table->addCell(7500)->addTextRun($styleParagrapheTableau);
			$TextRun->addImage(DIR_IMAGES . DIRECTORY_SEPARATOR . 'glyphicons' . DIRECTORY_SEPARATOR .
				'png' . DIRECTORY_SEPARATOR . 'glyphicons_043_group.png', ['color' => '44808A']);
			$TextRun->addText(' ', $fontStyle14);
			$TextRun->addText($Donnees['total_ppr'], $fontStyle20FortVert);
			$TextRun->addText(' '.$_Sujet, $fontStyle16FortVert);
			$TextRun->addText(' '.$_Suite, $fontStyle14);
	
	
			if ($Donnees['total_frn'] > 1) {
				$_Sujet = $L_T_Fournisseurs;
				$_Suite = $L_T_Supportant_Activites;
			} else {
				$_Sujet = $L_T_Fournisseurs;
				$_Suite = $L_T_Supportant_Activites;
			}
			$TextRun = $table->addCell(7500)->addTextRun($styleParagrapheTableau);
			$TextRun->addImage(DIR_IMAGES . DIRECTORY_SEPARATOR . 'glyphicons' . DIRECTORY_SEPARATOR .
				'png' . DIRECTORY_SEPARATOR . 'glyphicons_341_briefcase.png', ['bgColor' => '44808A']);
			$TextRun->addText(' ', $fontStyle14);
			$TextRun->addText($Donnees['total_frn'], $fontStyle20FortVert);
			$TextRun->addText(' '.$_Sujet, $fontStyle16FortVert);
			$TextRun->addText(' '.$_Suite, $fontStyle14);
		}
	
	
		$Liste_Niveaux_Impact = $objMatriceImpacts->rechercherNiveauxImpact($_POST['cmp_id']);
		$Liste_Niveaux_Impact_Poids = [];
		foreach ($Liste_Niveaux_Impact as $Element) {
			$Liste_Niveaux_Impact_Poids[$Element->nim_poids] = $Element;
		}
		
		$Liste_Echelles_Temps = $objEchellesTemps->rechercherEchellesTemps($_POST['cmp_id']);
		$Liste_Echelles_Temps_Poids = [];
		foreach ($Liste_Echelles_Temps as $Element) {
			$Liste_Echelles_Temps_Poids[$Element->ete_poids] = $Element;
		}


		// **********************

		if ( $_POST['flag_liste_pln_eff'] == 'true' ) {
			// ====================================================
			// Affichage du planning de réalisation des entretiens
			$Liste_Entites = $objCampagnes->rechercherEntitesAssocieesCampagne( $_SESSION['s_sct_id'], $_POST['cmp_id'], $_POST['entite_a_editer'] );
	
			$section->addPageBreak();
	
			$section->addTitle( $PageHTML->getLibelle('__LRI_PLANNING'), 1 );
	
			$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699',
				'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160, 'afterSpace' => 160]);
	
			$table->addRow(null, ['tblHeader' => true]);
			$table->addCell(3750)->addText($L_Entite, $fontTitreTableau, $styleParagrapheTableau);
			$table->addCell(3750)->addText($PageHTML->getLibelle('__LRI_EFFECTIF'), $fontTitreTableau, $styleParagrapheTableau);
			$table->addCell(3750)->addText($PageHTML->getLibelle('__LRI_CORRESPONDANT_PCA'), $fontTitreTableau, $styleParagrapheTableau);
			$table->addCell(3750)->addText($PageHTML->getLibelle('__LRI_DATE_ENTRETIEN'), $fontTitreTableau, $styleParagrapheTableau);
	
			foreach($Liste_Entites as $Entite) {
				$table->addRow();
				$table->addCell(7000)->addText($Entite->ent_nom . ($Entite->ent_description != '' ? ' (' . $Entite->ent_description . ')' : '' ), $fontTexteTableau, $styleParagrapheTableau);
				$table->addCell(7000)->addText(($Entite->cmen_effectif_total == '' ? '-' : $Entite->cmen_effectif_total), $fontTexteTableau, $styleParagrapheTableau);
				$table->addCell(7000)->addText(($Entite->ppr_prenom . ' ' . $Entite->ppr_nom == ' ' ? '-' : $Entite->ppr_prenom . ' ' . $Entite->ppr_nom), $fontTexteTableau, $styleParagrapheTableau);
				$table->addCell(7000)->addText(($Entite->cmen_date_entretien_cpca == '' ? '-' : $Entite->cmen_date_entretien_cpca), $fontTexteTableau, $styleParagrapheTableau);
			}
		}


		// ========================================
		// Gestion des activités de cette Campagne
		if ( $_POST['flag_liste_act'] == 'true' ) {
			$section->addPageBreak();
	
			// Affichage des Activités à redémarrer par période.
			$Liste_Activites = $objActivites->rechercherSyntheseActivites( $_POST['cmp_id'], $_POST['entite_a_editer'], '', 'ete_poids' );
	
			$Liste_Activites_ID = [];
			foreach( $Liste_Activites as $Occurrence ) {
				$Liste_Activites_ID[$Occurrence->act_id] = $Occurrence;
			}
	
			$section->addTitle( $L_Liste_Activites_Redemarrer_Par_Periode, 1 );
	
			$tmp_ete_poids = 0;
			foreach($Liste_Activites as $Activite) {
				if ( isset($Liste_Echelles_Temps_Poids[$Activite->ete_poids]->ete_nom_code) ) {
					if ($tmp_ete_poids != $Activite->ete_poids) {
						$tmp_ete_poids = $Activite->ete_poids;
	
						$section->addTitle($L_Activites_A_Redemarrer.' '.$Liste_Echelles_Temps_Poids[$Activite->ete_poids]->ete_nom_code, 2);
		
						$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699',
							'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160, 'afterSpace' => 160]);
		
						$table->addRow(null, ['tblHeader' => true]);
						$table->addCell(7000)->addText($L_Activite, $fontTitreTableau, $styleParagrapheTableau);
						$table->addCell(5000)->addText($L_Entite, $fontTitreTableau, $styleParagrapheTableau);
						$table->addCell(3000)->addText($L_Niveau_Impact, $fontTitreTableau, $styleParagrapheTableau);
					}
				}
	
				$table->addRow();
				$table->addCell(7000)->addText($Activite->act_nom, $fontTexteTableau, $styleParagrapheTableau);
				$table->addCell(5000)->addText(($Activite->ent_description != '' ? $Activite->ent_description : $Activite->ent_nom), $fontTexteTableau, $styleParagrapheTableau);
				$table->addCell(3000, ['bgColor' => $Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_couleur])
				->addText($Activite->nim_poids . ' - ' . $Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_nom_code, ['size' => 10, 'bold' => true, 'color' => 'ffffff'],
						array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER]));
			}
		}


		// ===========================================
		// Gestion des Applications de cette Campagne
		if ( $_POST['flag_liste_app'] == 'true' ) {
			$section->addPageBreak();

			// -------------------------------------------------------
			// Affichage des Applications de cette Campagne par DMIA.
			$Liste_Applications = $objCampagnes->rechercherApplicationsCampagne( $_POST['cmp_id'], '*', $_POST['entite_a_editer'] );

			$section->addTitle( $L_Liste_Applications_Redemarrer_Par_Periode, 1 );

			$tmp_poids = 0;
			foreach($Liste_Applications as $Application) {
				if ($tmp_poids != $Application->dmia) {
					$tmp_poids = $Application->dmia;

					$section->addTitle($L_Applications_A_Redemarrer.' '.$Liste_Echelles_Temps_Poids[$Application->dmia]->ete_nom_code, 2);

					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699',
						'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160]);

					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(1500)->addText($L_Nom_G, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(6000)->addText($L_Activites, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(5250)->addText($L_Donnees, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(2250)->addText($L_Palliatif, $fontTitreTableau, $styleParagrapheTableau);
				}

				$table->addRow();
				$table->addCell(1000)->addText($Application->app_nom, $fontTexteTableau, $styleParagrapheTableau);

				$textlines = explode(',<br>', $Application->act_nom);
				$textrun = $table->addCell(6000)->addTextRun();

				$line = explode('###', array_shift($textlines));
				$_act_nom = $line[0];
				$_act_id = $line[1];

				$textrun->addText($_act_nom . ' ', $fontTexteTableau, $styleParagrapheTableau);

				if ( array_key_exists($_act_id, $Liste_Activites_ID) ) {
					$textrun->addText('(', $fontTexteTableau, $styleParagrapheTableau);

					$_nim_poids = $Liste_Activites_ID[$_act_id]->nim_poids;

					$textrun->addText($_nim_poids . ' - ' . $Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_nom_code,
						['size' => 10, 'bold' => true, 'color' => $Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_couleur],
						$styleParagrapheTableau );

					if ( $_nim_poids > 2 ) {
						$_ete_poids = $Liste_Activites_ID[$_act_id]->ete_poids;
						if ($_ete_poids != '') {
							$textrun->addText( ' / ' . $Liste_Echelles_Temps_Poids[$_ete_poids]->ete_nom_code,
								['size' => 10, 'bold' => true], $styleParagrapheTableau );
						}
					}

					$textrun->addText(')', $fontTexteTableau, $styleParagrapheTableau);
				}

				foreach($textlines as $line) {
					$textrun->addTextBreak(2);

					$line = explode('###', $line);
					$_act_nom = $line[0];
					$_act_id = $line[1];

					$textrun->addText($_act_nom . ' ', $fontTexteTableau, $styleParagrapheTableau);

					if ( array_key_exists($_act_id, $Liste_Activites_ID) ) {
						$textrun->addText('(', $fontTexteTableau, $styleParagrapheTableau);

						$_nim_poids = $Liste_Activites_ID[$_act_id]->nim_poids;

						$textrun->addText($_nim_poids . ' - ' . $Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_nom_code,
							['size' => 10, 'bold' => true, 'color' => $Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_couleur],
							$styleParagrapheTableau );
						
						if ( $_nim_poids > 2 ) {
							$_ete_poids = $Liste_Activites_ID[$_act_id]->ete_poids;
							if ($_ete_poids != '') {
								$textrun->addText( ' / ' . $Liste_Echelles_Temps_Poids[$_ete_poids]->ete_nom_code,
									['size' => 10, 'bold' => true], $styleParagrapheTableau );
							}
						}

						$textrun->addText(')', $fontTexteTableau, $styleParagrapheTableau);
					}
				}


				$textlines = explode('##', $Application->acap_donnees);
				$textrun = $table->addCell(2250)->addTextRun();
				if (count($textlines) > 0) {
					$compteur = 0;

					foreach($textlines as $line) {
						if ($line != '') {
							$compteur += 1;

							if ($compteur > 1) $textrun->addTextBreak(2);

							$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
						}
					}

					if ($compteur == 0) {
						$textrun->addText($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
					}
				} else {
					$textrun->addText($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
				}


				$textlines = explode('##', $Application->acap_palliatif);
				$textrun = $table->addCell(2250)->addTextRun();
				if (count($textlines) > 0) {
					$compteur = 0;

					foreach($textlines as $line) {
						if ($line != '') {
							$compteur += 1;

							if ($compteur > 1) $textrun->addTextBreak(2);

							$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
						}
					}

					if ($compteur == 0) {
						$textrun->addText($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
					}
				} else {
					$textrun->addText($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
				}
			}


			$section->addPageBreak();


			// -------------------------------------------------------
			// Affichage des Applications de cette Campagne par PDMA.
			$Liste_Applications = $objCampagnes->rechercherPDMAApplicationsCampagne( $_POST['cmp_id'], '*', $_POST['entite_a_editer'] );

			$section->addTitle( $L_Liste_Applications_Par_PDMA, 1 );

			$tmp_poids = 0;
			foreach($Liste_Applications as $Application) {
				if ($tmp_poids != $Application->pdma) {
					$tmp_poids = $Application->pdma;

					$section->addTitle($L_Applications_PDMA.' '.$Liste_Echelles_Temps_Poids[$Application->pdma]->ete_nom_code, 2);

					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699',
						'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160]);

					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(1500)->addText($L_Nom_G, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(6750)->addText($L_Activites, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(6750)->addText($L_Donnees, $fontTitreTableau, $styleParagrapheTableau);
				}

				$table->addRow();
				$table->addCell(1500)->addText($Application->app_nom, $fontTexteTableau, $styleParagrapheTableau);

				$textlines = explode('//', $Application->act_nom);
				$textrun = $table->addCell(6750)->addTextRun();
				$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);

				foreach($textlines as $line) {
					$textrun->addTextBreak(2);
					$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
				}


				$textlines = explode('##', $Application->acap_donnees);
				$textrun = $table->addCell(6750)->addTextRun();
				if (count($textlines) > 0) {
					$compteur = 0;

					foreach($textlines as $line) {
						if ($line != '') {
							$compteur += 1;

							if ($compteur > 1) $textrun->addTextBreak(2);

							$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
						}
					}

					if ($compteur == 0) {
						$textrun->addText($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
					}
				} else {
					$textrun->addText($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
				}
			}
		}


		if ( $_POST['flag_liste_ppr'] == 'true' ) {
			$section->addPageBreak();

			// Affichage des Personnes Clés de cette Campagne.
			$Liste_Personnes = $objCampagnes->rechercherPersonnesClesCampagne( $_POST['cmp_id'], $_POST['entite_a_editer'] );

			$section->addTitle($L_Liste_Personnes_Cles, 1);

			$tmp_ent_nom = 0;
			foreach($Liste_Personnes as $Personne) {
				if ($tmp_ent_nom != $Personne->ent_nom) {
					$tmp_ent_nom = $Personne->ent_nom;

					$section->addTitle($Personne->ent_nom . ' (' . $Personne->ent_description . ')', 2);

					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699',
						'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160]);

					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(3500)->addText($L_Prenom, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(3500)->addText($L_Nom, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(4000)->addText($L_Activites, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(4000)->addText($L_Description, $fontTitreTableau, $styleParagrapheTableau);
				}

				$table->addRow();
				$table->addCell(3500)->addText($Personne->ppr_prenom, $fontTexteTableau, $styleParagrapheTableau);
				$table->addCell(3500)->addText($Personne->ppr_nom, $fontTexteTableau, $styleParagrapheTableau);

				$textlines = explode('<br>', $Personne->act_nom);
				$textrun = $table->addCell(4000)->addTextRun();
				$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);

				foreach($textlines as $line) {
					$textrun->addTextBreak(2);
					$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
				}

				$textlines = explode('<br>', $Personne->ppac_description);
				$textrun = $table->addCell(4000)->addTextRun();
				$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);

				foreach($textlines as $line) {
					$textrun->addTextBreak(2);
					$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
				}
			}
		}


		if ( $_POST['flag_liste_frn'] == 'true' ) {
			$section->addPageBreak();

			// Affichage des Fournisseurs de cette Campagne.
			$Liste_Fournisseurs = $objCampagnes->rechercherFournisseursCampagne( $_POST['cmp_id'], '*', $_POST['entite_a_editer'] );

			$section->addTitle($L_Liste_Fournisseurs_Utiles_Par_Periode, 1);

			$tmp_ete_poids = 0;
			foreach($Liste_Fournisseurs as $Fournisseur) {
				if ($tmp_ete_poids != $Fournisseur->ete_poids) {
					$tmp_ete_poids = $Fournisseur->ete_poids;

					$section->addTitle($L_DMIA_Fournisseurs.' '.$Fournisseur->ete_nom_code, 2);

					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699',
						'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160]);

					$table->addRow(null, ['tblHeader' => TRUE]);
					$table->addCell(3500)->addText($L_Nom_G, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(3500)->addText($L_Type, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(4000)->addText($L_Entite, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(4000)->addText($L_Activites, $fontTitreTableau, $styleParagrapheTableau);
				}

				$table->addRow();
				$table->addCell(3500)->addText($Fournisseur->frn_nom, $fontTexteTableau, $styleParagrapheTableau);
				$table->addCell(3500)->addText($Fournisseur->tfr_nom_code, $fontTexteTableau, $styleParagrapheTableau);
				$table->addCell(4000)->addText($Fournisseur->ent_nom . ' (' . $Fournisseur->ent_description . ')', $fontTexteTableau, $styleParagrapheTableau);
				$textlines = explode('<br>', $Fournisseur->act_nom);
				$textrun = $table->addCell(4000)->addTextRun();
				$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);

				foreach($textlines as $line) {
					$textrun->addTextBreak(2);
					$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
				}
			}
		}


		// =================================================================
		// *****************************************************************
		// =================================================================

		// Gestion de l'affichage détaillé des activité, entité par entité.
		if ( $_POST['flag_liste_act'] == 'true' ) { //if ( $_POST['flag_liste_dtl_act'] == 'true' ) {
			$section->addPageBreak();

			$Liste_Niveaux_Impact_Poids = [];
			foreach( $Liste_Niveaux_Impact as $Niveau_Impact ) {
				$Liste_Niveaux_Impact_Poids[$Niveau_Impact->nim_poids] = $Niveau_Impact;
			}
	
			$Liste_EchellesTemps_Poids = [];
			foreach( $Liste_EchellesTemps as $EchelleTemps ) {
				$Liste_EchellesTemps_Poids[$EchelleTemps->ete_poids] = $EchelleTemps;
			}
	
			$Liste_Sites_Id = [];
			foreach( $Liste_Sites as $Site ) {
				$Liste_Sites_Id[$Site->sts_id] = $Site;
			}
	
			$section->addTitle($L_Detail_Activites, 1);
	
			foreach( $Liste_Entites as $_Entite ) {
				$Entite = $objEntites->detaillerEntite($_Entite->ent_id);
				$Activites = $objActivites->rechercherActivites($_POST['cmp_id'], $_Entite->ent_id);
	
				$Nom_Entite = $Entite->ent_nom;
				if ($Entite->ent_description != '') $Nom_Entite .= ' (' . $Entite->ent_description . ')';
	
				$section->addTitle($L_Entite.' : '.$Nom_Entite, 2);
	
				$Informations_Validation = $objCampagnes->informationsValidationEntite($_POST['cmp_id'], $_Entite->ent_id);
	
				// ------------------------------------
				// Insertion de la table de validation
				$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'spaceBefore' => 60, 'spaceAfter' => 60,
					'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160]);
				
				$table->addRow(null, ['tblHeader' => true]);
				$table->addCell(15000, array_merge(['gridSpan' => 3], $fontStyle12BgFortRouge))
					->addText($L_Informations_Validation, array_merge($fontTitreTableau, ['color' => 'FFFFFF']), $styleParagrapheTableau);
				
				$table->addRow(null, ['tblHeader' => true]);
				$table->addCell(7500)
					->addText($L_Valideur, $fontTitreTableau, $styleParagrapheTableau);
				$table->addCell(7500)
					->addText($L_Date_Validation, $fontTitreTableau, $styleParagrapheTableau);
				
				if ( (! isset($Informations_Validation->cmen_date_validation)) || $Informations_Validation->cmen_date_validation == NULL ) {
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(15000, ['gridSpan' => 2])
						->addText($L_Neither_f, $fontTexteTableau, $styleParagrapheTableau);
				} else {
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(7500)
						->addText($Informations_Validation->cvl_nom . ' ' . $Informations_Validation->cvl_prenom, $fontTexteTableau, $styleParagrapheTableau);
					$table->addCell(7500)
						->addText($Informations_Validation->cmen_date_validation, $fontTexteTableau, $styleParagrapheTableau);
				}
	
	
				// ======================================
				// Détail de chaque activités pour cette entité
				$Compteur = 0;
	
				foreach ($Activites as $Activite) {
					$Liste_DMIA = $objActivites->recupererDMIA( $_POST['cmp_id'], $Activite->act_id );
					$Liste_Personnes_Cles = $objActivites->rechercherPersonnesClesAssociesActivite( $Activite->act_id );
					$Liste_Applications = $objActivites->rechercherApplicationsAssocieesActivite( $Activite->act_id );
					$Liste_Fournisseurs = $objActivites->rechercherFournisseursAssociesActivite( $Activite->act_id );
					$Liste_Sites = $objActivites->rechercherSitesActivite( $Activite->act_id );
					
					$Compteur += 1;
	
					$section->addTitle($Activite->act_nom, 3);
	
					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'spaceBefore' => 60, 'spaceAfter' => 60,
						'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 'cellMargin' => 160]);
	
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(4500)->addText($L_Responsable_Activite, $fontTexteTableau, $styleParagrapheTableau);
					$table->addCell(10500)->addText($Activite->ppr_nom_resp . ' ' . $Activite->ppr_prenom_resp, $fontTexteFortTableau, $styleParagrapheTableau);
	
					if ($Activite->ppr_nom_supp != '' or $Activite->ppr_prenom_supp != '') {
						$_Nom_Suppleant = $Activite->ppr_nom_supp . ' ' . $Activite->ppr_prenom_supp;
					} else {
						$_Nom_Suppleant = $L_Neither;
					}
	
	
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(4500)->addText($L_Suppleant, $fontTexteTableau, $styleParagrapheTableau);
					$table->addCell(10500)->addText($_Nom_Suppleant, $fontTexteFortTableau, $styleParagrapheTableau);
	
	
					if ( $Activite->act_teletravail == 1 ) {
						$_Activite_Teletravail = $L_Yes;
					} else {
						$_Activite_Teletravail = $L_No;
					}
	
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(4500)->addText($L_Activite_Teletravaillable, $fontTexteTableau, $styleParagrapheTableau);
					$table->addCell(10500)->addText($_Activite_Teletravail, $fontTexteFortTableau, $styleParagrapheTableau);
	
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(4500)->addText($L_Description, $fontTexteTableau, $styleParagrapheTableau);
					if ( $Activite->act_description == NULL ) $Activite->act_description = '';
					$table->addCell(15000)->addText($Activite->act_description, $fontTexteFortTableau, $styleParagrapheTableau);
	
	
					// ====================
					// Sites de l'activité
	
					// Gestion de l'entête du tableau
					$section->addTextBreak(1);
					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'topFromText' => 20,
						'bottomFromText' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
						'cellMargin' => 160]);
					
					$table->addRow();
					$table->addCell(15000, ['gridSpan' => 3, 'bgColor' => 'C0C0C0'])
						->addText($L_Sites, $fontTitreTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 60, 'spaceAfter' => 60]);
	
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(3000)->addText($L_Type, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(4500)->addText($L_Nom_G, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(7500)->addText($L_Description, $fontTitreTableau, $styleParagrapheTableau);
	
	
					// Gestion du corps du tableau
					foreach( $Liste_Sites as $Site ) {
						switch ( $Site->acst_type_site ) {
							default:
								$_Type_Site = $L_Neither;
								break;
								
							case 0:
								$_Type_Site = $L_Site_Nominal;
								break;
								
							case 1:
								$_Type_Site = $L_Site_Secours;
								break;
						}
						$table->addRow();
						$table->addCell(3000)->addText($_Type_Site, $fontTexteTableau, $styleParagrapheTableau);
						$table->addCell(4500)->addText($Site->sts_nom, $fontTexteTableau, $styleParagrapheTableau);
						$table->addCell(7500)->addText($Site->sts_description, $fontTexteTableau, $styleParagrapheTableau);
					}
	
	
					// ====================
					// DMIA de l'activité
					$Nombre_Echelle = count($Liste_EchellesTemps);
					$_largeur_colonne = 15000 / $Nombre_Echelle;
	
					// Gestion de l'entête du tableau
					$section->addTextBreak(1);
					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'topFromText' => 20,
						'bottomFromText' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
						'cellMargin' => 160]);
	
					$table->addRow();
					$table->addCell(15000, ['gridSpan' => $Nombre_Echelle, 'bgColor' => 'C0C0C0'])
						->addText($L_DMIA, $fontTitreTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 60, 'spaceAfter' => 60]);
	
					$table->addRow(null, ['tblHeader' => true]);
	
					foreach ($Liste_EchellesTemps as $_EchelleTemps) {
						$table->addCell($_largeur_colonne)->addText($_EchelleTemps->ete_nom_code, $fontTitreTableau,
							array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]));
					}
	
					$table->addRow(null, ['tblHeader' => true]);
	
					// Affichage des niveaux retenus.
					foreach ($Liste_EchellesTemps as $_EchelleTemps) {
						$Numero_Echelle = '0';
						$Couleur_Echelle = '';
	
						if (isset($Liste_DMIA) and $Liste_DMIA != []) {
							foreach ($Liste_DMIA as $_DetailEchelle) {
								if ($_DetailEchelle->ete_id == $_EchelleTemps->ete_id) {
									$Numero_Echelle = $_DetailEchelle->nim_numero;
									$Couleur_Echelle = $_DetailEchelle->nim_couleur;
								}
							}
						}
	
						$table->addCell($_largeur_colonne, ['bgColor' => $Couleur_Echelle])->addText($Numero_Echelle, array_merge($fontStyle12Fort, ['color' => 'ffffff']), array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER]));
					}
	
					$table->addRow(null, ['tblHeader' => true]);
	
					// Affichage du type d'impact retenu
					foreach ($Liste_EchellesTemps as $_EchelleTemps) {
						if (isset($Liste_DMIA) and $Liste_DMIA != []) {
							foreach ($Liste_DMIA as $_DetailEchelle) {
								if ($_DetailEchelle->ete_id == $_EchelleTemps->ete_id) {
									$Nom_Type_Impact = $_DetailEchelle->tim_nom_code;
								}
							}
						}
	
						$table->addCell($_largeur_colonne)->addText($Nom_Type_Impact, $fontTexteTableau, array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER]));
					}
	
	
					// ==============================
					// Affichage des Personnes Clés.
	
					// Gestion de l'entête du tableau
					$section->addTextBreak(1);
					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'topFromText' => 20,
						'bottomFromText' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
						'cellMargin' => 160]);
	
					$table->addRow();
					$table->addCell(15000, ['gridSpan' => 3, 'bgColor' => 'C0C0C0'])
					->addText($L_Personnes_Cles, $fontTitreTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 60, 'spaceAfter' => 60]);
	
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(5000)->addText($L_Nom, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(5000)->addText($L_Prenom, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(5000)->addText($L_Description, $fontTitreTableau, $styleParagrapheTableau);
					
					if ( $Liste_Personnes_Cles != FALSE ) {
						foreach ($Liste_Personnes_Cles as $Personne) {
							$table->addRow();
							$table->addCell(5000)->addText($Personne->ppr_nom, $fontTexteTableau, $styleParagrapheTableau);
							$table->addCell(5000)->addText($Personne->ppr_prenom, $fontTexteTableau, $styleParagrapheTableau);
							if ( $Personne->ppac_description == NULL ) $Personne->ppac_description = '';
							$table->addCell(5000)->addText($Personne->ppac_description, $fontTexteTableau, $styleParagrapheTableau);
						}
					} else {
						$table->addRow();
						$table->addCell(15000, ['gridSpan' => 3])->addText($L_Neither_f, $fontTexteTableau, array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]));
					}


					// ======================================
					// Affichage des Personnes Prioritaires.
					$Liste_Personnes_Prioritaires = $objActivites->recupererPersonnesPrioritaires( $Activite->act_id );

					// Gestion de l'entête du tableau
					$Nombre_Echelle = count($Liste_EchellesTemps);
					$_largeur_colonne = 15000 / $Nombre_Echelle;

					// Gestion de l'entête du tableau
					$section->addTextBreak(1);
					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'topFromText' => 20,
						'bottomFromText' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
						'cellMargin' => 160]);

					$table->addRow();
					$table->addCell(15000, ['gridSpan' => $Nombre_Echelle, 'bgColor' => 'C0C0C0'])
						->addText($PageHTML->getLibelle('__LRI_PERSONNES_PRIORITAIRES'), $fontTitreTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 60, 'spaceAfter' => 60]);

					$table->addRow(null, ['tblHeader' => true]);

					foreach ($Liste_EchellesTemps as $_EchelleTemps) {
						$table->addCell($_largeur_colonne)->addText($_EchelleTemps->ete_nom_code, $fontTitreTableau,
							array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]));
					}

					$table->addRow(null, ['tblHeader' => true]);

					// Affichage des niveaux retenus.
					if ( $Liste_Personnes_Prioritaires != [] ) {
						foreach( $Liste_EchellesTemps as $EchelleTemps ) {
							$_Utilisateurs_A_Redemarrer = 0;
								foreach( $Liste_Personnes_Prioritaires as $Personne_Prioritaire ) {
									if ($Personne_Prioritaire->ete_id == $EchelleTemps->ete_id) {
										$_Utilisateurs_A_Redemarrer = $Personne_Prioritaire->rut_nbr_utilisateurs_a_redemarrer;
										break;
									}
								}
								$table->addCell($_largeur_colonne)->addText($_Utilisateurs_A_Redemarrer, $fontStyle12, array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER]));
						}
					} else {
						$table->addCell(15000, ['gridSpan' => $Nombre_Echelle])
							->addText($L_Neither_f, $fontTexteTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
					}


					// ==============================
					// Affichage des Interdépendances.

					// Gestion de l'entête du tableau
					$section->addTextBreak(1);
					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'topFromText' => 20,
						'bottomFromText' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
						'cellMargin' => 160]);

					$table->addRow();
					$table->addCell(15000, ['gridSpan' => 2, 'bgColor' => 'C0C0C0'])
					->addText($L_Interdependances, $fontTitreTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 60, 'spaceAfter' => 60]);

					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(7500)->addText($L_Dependances_Internes_Amont, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(7500)->addText($L_Dependances_Internes_Aval, $fontTitreTableau, $styleParagrapheTableau);

					if ( $Liste_Personnes_Cles != FALSE ) {
						foreach ($Liste_Personnes_Cles as $Personne) {
							$table->addRow();
							if ( $Activite->act_dependances_internes_amont != '' ) {
								$textlines = explode(', ', $Activite->act_dependances_internes_amont);
								$textrun = $table->addCell(7500)->addTextRun();
								$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);
								
								foreach($textlines as $line) {
									$textrun->addTextBreak(1);
									$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
								}
							} else {
								$table->addCell(7500)->addText($L_Neither_f, $fontTexteTableau, $styleParagrapheTableau);
							}
	
							if ( $Activite->act_dependances_internes_aval != '' ) {
								$textlines = explode(', ', $Activite->act_dependances_internes_aval);
								$textrun = $table->addCell(7500)->addTextRun();
								$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);
								
								foreach($textlines as $line) {
									$textrun->addTextBreak(1);
									$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
								}
							} else {
								$table->addCell(7500)->addText($L_Neither_f, $fontTexteTableau, $styleParagrapheTableau);
							}
						}
					} else {
						$table->addRow();
						$table->addCell(15000, ['gridSpan' => 3])->addText($L_Neither_f, $fontTexteTableau, array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]));
					}
	
	
					// ==============================
					// Affichage des Applications.
					
					// Gestion de l'entête du tableau
					$section->addTextBreak(1);
					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'topFromText' => 20,
						'bottomFromText' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
						'cellMargin' => 160]);
					
					$table->addRow();
					$table->addCell(15000, ['gridSpan' => 8, 'bgColor' => 'C0C0C0'])
						->addText($L_Applications, $fontTitreTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 60, 'spaceAfter' => 60]);
					
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(1875)->addText($L_Nom, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(1875)->addText($L_Fournisseur, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(1875)->addText($L_Hebergement, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(1875)->addText($L_Niveau_Service, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(1875)->addText($L_DMIA, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(1875)->addText($L_PDMA, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(1875)->addText($L_Donnees, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(1875)->addText($L_Palliatif, $fontTitreTableau, $styleParagrapheTableau);
					
					if ( $Liste_Applications != FALSE ) {
						foreach ($Liste_Applications as $Application) {
							$table->addRow();
	
							if ( $Application->app_nom == NULL ) $Application->app_nom = '';
							$table->addCell(1875)->addText($Application->app_nom, $fontTexteTableau, $styleParagrapheTableau);
	
							if ( $Application->frn_nom == NULL ) $Application->frn_nom = '';
							$table->addCell(1875)->addText($Application->frn_nom, $fontTexteTableau, $styleParagrapheTableau);
	
							if ( $Application->app_hebergement == NULL ) $Application->app_hebergement = '';
							if ( $Application->acap_hebergement == NULL ) $Application->acap_hebergement = '';
							$table->addCell(1875)->addText(
								($Application->acap_hebergement != '' ? $Application->acap_hebergement : $Application->app_hebergement),
								$fontTexteTableau, $styleParagrapheTableau
								);
	
							if ( $Application->app_niveau_service == NULL ) $Application->app_niveau_service = '';
							if ( $Application->acap_niveau_service == NULL ) $Application->acap_niveau_service = '';
							$table->addCell(1875)->addText(
								($Application->acap_niveau_service != '' ? $Application->acap_niveau_service : $Application->app_niveau_service),
								$fontTexteTableau, $styleParagrapheTableau
								);
	
							if ( $Application->ete_id_dima == NULL ) $Application->ete_id_dima = '';
							$table->addCell(1875)->addText($Application->ete_id_dima, $fontTexteTableau, $styleParagrapheTableau);
	
							if ( $Application->ete_id_pdma == NULL ) $Application->ete_id_pdma = '';
							$table->addCell(1875)->addText($Application->ete_id_pdma, $fontTexteTableau, $styleParagrapheTableau);
	
							if ( $Application->acap_donnees == NULL ) {
								$Application->acap_donnees = '';
								$table->addCell(1875)->addText($Application->acap_donnees, $fontTexteTableau, $styleParagrapheTableau);
							} else {
								$textlines = explode("\n", $Application->acap_donnees);
								$textrun = $table->addCell(1875)->addTextRun();
								$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);
	
								foreach($textlines as $line) {
									$textrun->addTextBreak(1);
									$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
								}
							}
	
							if ( $Application->acap_palliatif == NULL ) {
								$Application->acap_palliatif = '';
								$table->addCell(1875)->addText($Application->acap_palliatif, $fontTexteTableau, $styleParagrapheTableau);
							} else {
								$textlines = explode("\n", $Application->acap_palliatif);
								$textrun = $table->addCell(1875)->addTextRun();
								$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);
	
								foreach($textlines as $line) {
									$textrun->addTextBreak(1);
									$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
								}
							}
						}
					} else {
						$table->addRow();
						$table->addCell(15000, ['gridSpan' => 8])->addText($L_Neither_f, $fontTexteTableau, array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]));
					}
	
	
					// ==============================
					// Affichage des Fournisseurs.
					
					// Gestion de l'entête du tableau
					$section->addTextBreak(1);
					$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'topFromText' => 20,
						'bottomFromText' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
						'cellMargin' => 160]);
					
					$table->addRow();
					$table->addCell(15000, ['gridSpan' => 5, 'bgColor' => 'C0C0C0'])
						->addText($L_Fournisseurs, $fontTitreTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 60, 'spaceAfter' => 60]);
					
					$table->addRow(null, ['tblHeader' => true]);
					$table->addCell(2500)->addText($L_Nom, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(2500)->addText($L_Type, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(2500)->addText($L_DMIA, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(2500)->addText($L_Consequence_Indisponibilite, $fontTitreTableau, $styleParagrapheTableau);
					$table->addCell(2500)->addText($L_Palliatif, $fontTitreTableau, $styleParagrapheTableau);
	
					if ( $Liste_Fournisseurs != FALSE ) {
						foreach ($Liste_Fournisseurs as $Fournisseur) {
							$table->addRow();
							
							if ( $Fournisseur->frn_nom == NULL ) $Fournisseur->frn_nom = '';
							$table->addCell(3000)->addText($Fournisseur->frn_nom, $fontTexteTableau, $styleParagrapheTableau);
							
							if ( $Fournisseur->tfr_nom_code == NULL ) $Fournisseur->tfr_nom_code = '';
							$table->addCell(3000)->addText($Fournisseur->tfr_nom_code, $fontTexteTableau, $styleParagrapheTableau);
							
							if ( $Fournisseur->ete_nom_code == NULL ) $Fournisseur->ete_nom_code = '';
							$table->addCell(3000)->addText($Fournisseur->ete_nom_code, $fontTexteTableau, $styleParagrapheTableau);
							
							if ( $Fournisseur->acfr_consequence_indisponibilite == NULL ) $Fournisseur->acfr_consequence_indisponibilite = '';
							$table->addCell(3000)->addText($Fournisseur->acfr_consequence_indisponibilite, $fontTexteTableau, $styleParagrapheTableau);
							
							if ( ! isset($Fournisseur->acfr_palliatif_tiers) or $Fournisseur->acfr_palliatif_tiers == NULL ) {
								$Fournisseur->acfr_palliatif_tiers = '';
								$table->addCell(3000)->addText($Fournisseur->acfr_palliatif_tiers, $fontTexteTableau, $styleParagrapheTableau);
							} else {
								$textlines = explode('<br>', $Fournisseur->acfr_palliatif_tiers);
								$textrun = $table->addCell(3000)->addTextRun();
								$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);
								
								foreach($textlines as $line) {
									$textrun->addTextBreak(1);
									$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
								}
							}
						}
					} else {
						$table->addRow();
						$table->addCell(15000, ['gridSpan' => 5])->addText($L_Neither_f, $fontTexteTableau, array_merge($styleParagrapheTableau, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]));
					}
				}
			}
		}


		$Nombre_Niveaux = count( $Liste_Niveaux_Impact );
		$Nombre_Types = count( $Liste_Types_Impact ) + 1;
		$Taille_Colonne = 15000 / $Nombre_Types;

		$section->addPageBreak();

		$section->addTitle( $L_Annexe, 1 );

		$section->addTextBreak(1);

		$table = $section->addTable(['borderSize' => 6, 'borderColor' => '006699', 'topFromText' => 20,
			'bottomFromText' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
			'cellMargin' => 160]);

		$table->addRow(null, ['tblHeader' => true]);

		$TextRun = $table->addCell($Taille_Colonne, ['bgColor' => 'ECECEC'])->addTextRun(['alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER]);
		$TextRun->addText($L_Type, $fontTitreTableau, $styleParagrapheTableau);
		$TextRun->addTextBreak(1);
		$TextRun->addText('---');
		$TextRun->addTextBreak(1);
		$TextRun->addText($L_Niveau, $fontTitreTableau, $styleParagrapheTableau);

		if ($Nombre_Types > 1) {
			foreach ($Liste_Types_Impact as $Type_Impact) {
				$table->addCell($Taille_Colonne)->addText($Type_Impact->tim_nom_code, $fontTitreTableau, $styleParagrapheTableau);
			}
		}

		foreach ( $Liste_Niveaux_Impact as $Niveau_Impact ) {
			$table->addRow(null, ['tblHeader' => true]);

			$table->addCell($Taille_Colonne)->addText($Niveau_Impact->nim_poids.' - '.$Niveau_Impact->nim_nom_code, $fontTexteFortTableau, $styleParagrapheTableau);

			if ( isset($Liste_Types_Impact) ) {
				foreach ($Liste_Types_Impact as $Type_Impact) {
					if ( isset($Liste_Matrice_Impacts[$Niveau_Impact->nim_id.'-'.$Type_Impact->tim_id]) ) {
						$Description = html_entity_decode($Liste_Matrice_Impacts[$Niveau_Impact->nim_id.'-'.$Type_Impact->tim_id]->mim_description);
					} else {
						$Description = '';
					}
					if ( str_contains($Description, '<ul>') ) {
						$Description = str_replace(['<ul>', '</ul>', '<li>', '<br>'], ['', '', '', ''], $Description);

						$textlines = explode('</li>', $Description);
						$textrun = $table->addCell($Taille_Colonne, ['bgColor' => $Niveau_Impact->nim_couleur])->addTextRun();
						$textrun->addText('- '.array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);

						foreach($textlines as $line) {
							if ($line != '') {
								$textrun->addTextBreak(1);
								$textrun->addText('- '.$line, $fontTexteTableau, $styleParagrapheTableau);
							}
						}
					} else {
						$textlines = explode('<br>', $Description);
						$textrun = $table->addCell($Taille_Colonne, ['bgColor' => $Niveau_Impact->nim_couleur])->addTextRun();
						$textrun->addText(array_shift($textlines), $fontTexteTableau, $styleParagrapheTableau);

						foreach($textlines as $line) {
							if ($line != '') {
								$textrun->addTextBreak(1);
								$textrun->addText($line, $fontTexteTableau, $styleParagrapheTableau);
							}
						}
					}
				}
			}
		}


		switch( $_POST['format_edition'] ) {
		 default:
		 case 'docx':
			// Saving the document as OOXML file...
			$Nom_Fichier = $Nom_Fichier . '.docx';
			$Nom_Fichier_Complet = DIR_RAPPORTS . '/' . $Nom_Fichier;
	
			$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
			$objWriter->save( $Nom_Fichier_Complet );
			break;

		 case 'odt':
			// Saving the document as ODF file...
			$Nom_Fichier = $Nom_Fichier . '.odt';
			$Nom_Fichier_Complet = DIR_RAPPORTS . '/' . $Nom_Fichier;

			$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
			$objWriter->save( $Nom_Fichier_Complet );
			break;

		 case 'html':
			// Saving the document as HTML file...
			$Nom_Fichier = $Nom_Fichier . '.html';
			$Nom_Fichier_Complet = DIR_RAPPORTS . '/' . $Nom_Fichier;
	
			$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
			$objWriter->save( $Nom_Fichier_Complet );
			break;

		 case 'pdf':
			// Saving the document as PDF file...
			/**
			 * Add a meta tag generator
			 */
			function cbEditHTML(string $inputHTML): string
			{
				$beforeBody = '<meta name="generator" content="PHPWord" />';
				$needle = '</head>';

				$pos = strpos($inputHTML, $needle);
				if ($pos !== false) {
					$inputHTML = (string) substr_replace($inputHTML, "$beforeBody\n$needle", $pos, strlen($needle));
				}

				return $inputHTML;
			}

			$Nom_Fichier = $Nom_Fichier . '.pdf';
			$Nom_Fichier_Complet = DIR_RAPPORTS . '/' . $Nom_Fichier;

			\PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_MPDF);
			\PhpOffice\PhpWord\Settings::setPdfRendererPath(CHEMIN_APPLICATION . '/vendor/mpdf/mpdf');
			\PhpOffice\PhpWord\Settings::setPdfRendererOptions(['orientation' => 'landscape']);

			$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
			$objWriter->setEditCallback('cbEditHTML');
			$objWriter->save( $Nom_Fichier_Complet );
			break;
		}
	} elseif ($_POST['format_edition'] == 'xlsx') { // Edition Excel
		// *****************************************************************
		// -----------------------------------------------------------------
		// ********* EXCEL *********
		// -----------------------------------------------------------------
		// *****************************************************************

		// =====================
		// Création du document
		$spreadsheet = new Spreadsheet();

		$Numero_Onglet = 0;

		$Nom_Fichier = $Nom_Fichier . '.xlsx';
		$Nom_Fichier_Complet = DIR_RAPPORTS . '/' . $Nom_Fichier;

		// -----------------------------------------
		// Mise à jour des "Propriétés" du document
		$properties = $spreadsheet->getProperties();
		$properties->setCreator($PageHTML->Nom_Outil_TXT.' v'.$PageHTML->Version_Outil);
		$properties->setCompany($PageHTML->Nom_Societe);
		$properties->setTitle($L_Dossier_Restitution);
		$properties->setSubject($L_Conclusion_BIAs);
		$properties->setDescription('Automatic report');
		$properties->setCategory('Restitution');
		$properties->setLastModifiedBy($Nom_Redacteur);
		$properties->setCreated(mktime(0, 0, 0, 3, 12, 2014));
		$properties->setModified(mktime(0, 0, 0, 3, 14, 2014));
		$properties->setKeywords('mysecdash, word, bia, conclusion');

		// ----------------------------------
		// Définition des styles du document
		$fontStyle10 = ['font' => array('size' => 10)];
		$fontStyle12 = ['font' => array('size' => 12)];
		$fontStyle14 = ['font' => array('size' => 14)];
		$fontStyle16 = ['font' => array('size' => 16)];
		$fontStyle18 = ['font' => array('size' => 18)];
		$fontStyle20 = ['font' => array('size' => 20)];
		$fontStyle22 = ['font' => array('size' => 22)];

		$fontStyle10Fort = array('font' => ['size' => 10, 'bold' => true]);
		$fontStyle12Fort = array('font' => ['size' => 12, 'bold' => true]);
		$fontStyle14Fort = array('font' => ['size' => 14, 'bold' => true]);
		$fontStyle16Fort = array('font' => ['size' => 16, 'bold' => true]);
		$fontStyle18Fort = array('font' => ['size' => 18, 'bold' => true]);
		$fontStyle20Fort = array('font' => ['size' => 20, 'bold' => true]);
		$fontStyle22Fort = array('font' => ['size' => 22, 'bold' => true]);

		$fontStyle10FortRouge = array('font' => ['size' => 10, 'bold' => true, 'color' => array('argb' => 'FFC34A36')]);
		$fontStyle12FortRouge = array('font' => ['size' => 12, 'bold' => true, 'color' => array('argb' => 'FFC34A36')]);
		$fontStyle16FortRouge = array('font' => ['size' => 16, 'bold' => true, 'color' => array('argb' => 'FFC34A36')]);
		$fontStyle18FortRouge = array('font' => ['size' => 18, 'bold' => true, 'color' => array('argb' => 'FFC34A36')]);
		$fontStyle20FortRouge = array('font' => ['size' => 20, 'bold' => true, 'color' => array('argb' => 'FFC34A36')]);
		$fontStyle22FortRouge = array('font' => ['size' => 22, 'bold' => true, 'color' => array('argb' => 'FFC34A36')]);

		$fontStyle10FortBleu  = array('font' => ['size' => 10, 'bold' => true, 'color' => array('argb' => 'FF44808A')]);
		$fontStyle12FortBleu  = array('font' => ['size' => 12, 'bold' => true, 'color' => array('argb' => 'FF44808A')]);
		$fontStyle16FortBleu  = array('font' => ['size' => 16, 'bold' => true, 'color' => array('argb' => 'FF44808A')]);
		$fontStyle18FortBleu  = array('font' => ['size' => 18, 'bold' => true, 'color' => array('argb' => 'FF44808A')]);
		$fontStyle20FortBleu  = array('font' => ['size' => 20, 'bold' => true, 'color' => array('argb' => 'FF44808A')]);
		$fontStyle22FortBleu  = array('font' => ['size' => 22, 'bold' => true, 'color' => array('argb' => 'FF44808A')]);

		$fontStyle10FortVert  = array('font' => ['size' => 10, 'bold' => true, 'color' => array('argb' => 'FF717D11')]);
		$fontStyle12FortVert  = array('font' => ['size' => 12, 'bold' => true, 'color' => array('argb' => 'FF717D11')]);
		$fontStyle16FortVert  = array('font' => ['size' => 16, 'bold' => true, 'color' => array('argb' => 'FF717D11')]);
		$fontStyle18FortVert  = array('font' => ['size' => 18, 'bold' => true, 'color' => array('argb' => 'FF717D11')]);
		$fontStyle20FortVert  = array('font' => ['size' => 20, 'bold' => true, 'color' => array('argb' => 'FF717D11')]);
		$fontStyle22FortVert  = array('font' => ['size' => 22, 'bold' => true, 'color' => array('argb' => 'FF717D11')]);

		$fontStyle10FortBlanc  = array('font' => ['size' => 10, 'bold' => true, 'color' => array('argb' => 'FFFFFFFF')]);
		$fontStyle12FortBlanc  = array('font' => ['size' => 12, 'bold' => true, 'color' => array('argb' => 'FFFFFFFF')]);
		$fontStyle16FortBlanc  = array('font' => ['size' => 16, 'bold' => true, 'color' => array('argb' => 'FFFFFFFF')]);
		$fontStyle18FortBlanc  = array('font' => ['size' => 18, 'bold' => true, 'color' => array('argb' => 'FFFFFFFF')]);
		$fontStyle20FortBlanc  = array('font' => ['size' => 20, 'bold' => true, 'color' => array('argb' => 'FFFFFFFF')]);
		$fontStyle22FortBlanc  = array('font' => ['size' => 22, 'bold' => true, 'color' => array('argb' => 'FFFFFFFF')]);
		
		$fontStyle12BgFortRouge = array_merge($fontStyle12FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FFC34A36'))]);
		$fontStyle12BgFortBleu  = array_merge($fontStyle12FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FF44808A'))]);
		$fontStyle12BgFortVert  = array_merge($fontStyle12FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FF717D11'))]);
		$fontStyle12BgFortGris  = array_merge($fontStyle12FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FFC0C0C0'))]);

		$fontStyle16BgFortRouge = array_merge($fontStyle16FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FFC34A36'))]);
		$fontStyle16BgFortBleu  = array_merge($fontStyle16FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FF44808A'))]);
		$fontStyle16BgFortVert  = array_merge($fontStyle16FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FF717D11'))]);
		$fontStyle16BgFortGris  = array_merge($fontStyle16FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FFC0C0C0'))]);

		$fontStyle20BgFortRouge = array_merge($fontStyle20FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FFC34A36'))]);
		$fontStyle20BgFortBleu  = array_merge($fontStyle20FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FF44808A'))]);
		$fontStyle20BgFortVert  = array_merge($fontStyle20FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FF717D11'))]);
		$fontStyle20BgFortGris  = array_merge($fontStyle20FortBlanc, ['fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => array('argb' => 'FFC0C0C0'))]);


		$fontTitreTableau = array_merge($fontStyle12FortVert,
			['borders' => array('outline' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => array('argb' => '00ACACAC')))]);
		$fontTitre2Tableau = array_merge($fontStyle18,
			['borders' => array('outline' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => array('argb' => '00ACACAC'))), 
				'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);
		$fontTexteTableau = array_merge($fontStyle10,
			['borders' => array('outline' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => array('argb' => '00ACACAC'))),
				'alignment' => array('vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)]);
		$fontTexteTableau2 = array_merge($fontStyle10,
			['borders' => array('outline' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => array('argb' => '00ACACAC'))),
				'fill' => array('fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => array('argb' => 'FFE0E0E0')),
				'alignment' => array('vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)]
			);
		$fontTexteFortTableau = array_merge($fontStyle10Fort,
			['borders' => array('outline' => array('borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => array('argb' => '00ACACAC'))),
				'alignment' => array('vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)]);

		$styleParagrapheTableau = ['spaceBefore' => 60, 'spaceAfter' => 60];

		$fontTitreDocument = array_merge($fontStyle22FortRouge, ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);
		$fontChapitreDocument = array_merge($fontStyle20FortBleu, ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]]);

		$fontSujetDocument = ['spaceBefore' => 160, 'spaceAfter' => 60, 'size' => 24, 'color' => '44808A',
			'bold' => true];

		// ===========================================
		// *******************************************
		// Gestion de l'onglet "Synthèse managériale"
		if ( $_POST['flag_synthese_manager'] == 'true' ) {
			$Donnees = $objCampagnes->syntheseCampagne( $_POST['cmp_id'] );

			$Nombre_BIA_A_Faire = $Donnees['total_bia'] - ( $Donnees['total_bia_valides'] + $Donnees['total_bia_en_cours'] );

			// Construit l'onglet de tableau de synthèse.
			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;
			$activeWorksheet->setTitle($L_Synthese);

			$activeWorksheet->mergeCells('A1:B1');
			$activeWorksheet->setCellValue('A1', $L_Synthese_Manageriale_Globale);
			$activeWorksheet->getStyle('A1:B1')->applyFromArray($fontChapitreDocument);


			// Créer un objet RichText
			$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
			
			if ($Donnees['total_bia'] > 1) {
				$_BIA = $L_T_BIAs;
				$_Auxiliere = $L_T_Ont_Ete;
				$_Verbe = $L_T_Identifies;
			} else {
				$_BIA = $L_T_BIA;
				$_Auxiliere = $L_T_A_Ete;
				$_Verbe = $L_T_Identifie;
			}
			$L_T_Pour_Cette_Campagne;

			$textRun = $richText->createTextRun($Donnees['total_bia'] . ' ' . $_BIA);
			$textRun->getFont()->getColor()->setARGB('FF44808A'); // Bleu
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun(' '.$_Auxiliere.' ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun($_Verbe);
			$textRun->getFont()->getColor()->setARGB('FF44808A');
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun(' '.$L_T_Pour_Cette_Campagne);
			$textRun->getFont()->setSize(16);
			
			if ($Donnees['total_bia_valides'] > 1) {
				$_BIA = $L_T_BIAs;
				$_Auxiliere = $L_T_Sont;
				$_Verbe = $L_T_Valides;
			} else {
				$_BIA = $L_T_BIA;
				$_Auxiliere = $L_T_Est;
				$_Verbe = $L_T_Valide;
			}

			$textRun = $richText->createTextRun(', ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun($Donnees['total_bia_valides']);
			$textRun->getFont()->getColor()->setARGB('FF717D11'); // Vert
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun(' '.$_Auxiliere.' ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun($_Verbe);
			$textRun->getFont()->setSize(16);
			$textRun->getFont()->getColor()->setARGB('FF717D11');
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			
			if ($Donnees['total_bia_en_cours'] > 1) {
				$_BIA = $L_T_BIAs;
				$_Auxiliere = $L_T_Sont;
				$_Verbe = $L_T_En_Cours;
			} else {
				$_BIA = $L_T_BIA;
				$_Auxiliere = $L_T_Est;
				$_Verbe = $L_T_En_Cours;
			}

			$textRun = $richText->createTextRun(', ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun(', ' . $Donnees['total_bia_en_cours']);
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun->getFont()->getColor()->setARGB('FF44808A'); // Bleu
			$textRun = $richText->createTextRun(' '.$_Auxiliere.' ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun($_Verbe);
			$textRun->getFont()->getColor()->setARGB('FF44808A');
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);


			if ($Nombre_BIA_A_Faire > 1) {
				$_BIA = $L_T_BIAs;
				$_Auxiliere = $L_T_Sont;
				$_Verbe = $L_T_A_Faire;
			} else {
				$_BIA = $L_T_BIA;
				$_Auxiliere = $L_T_Est;
				$_Verbe = $L_T_A_Faire;
			}

			$textRun = $richText->createTextRun(', ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun($Nombre_BIA_A_Faire);
			$textRun->getFont()->getColor()->setARGB('FFC34A36'); // Rouge
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun(' '.$_Auxiliere.' ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun($_Verbe);
			$textRun->getFont()->getColor()->setARGB('FFC34A36');
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			
			$activeWorksheet->mergeCells('A3:B3');
			$activeWorksheet->getCell('A3')->setValue($richText);
			$activeWorksheet->getStyle('A3:B3')->applyFromArray($fontTitre2Tableau);


			// Nouvelle occurrence du tableau

			// Nouvelle cellule
			if ($Donnees['total_act_3_4'] > 1) {
				$_Sujet = $L_T_Activites;
				$_Auxiliere = $L_T_Sont;
				$_Type = $L_T_Essentielles;
			} else {
				$_Sujet = $L_T_Activite;
				$_Auxiliere = $L_T_Est;
				$_Type = $L_T_Essentielle;
			}

			$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

			$textRun = $richText->createTextRun($Donnees['total_act_3_4'].' '.$_Sujet);
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun->getFont()->getColor()->setARGB('FF44808A'); // Bleu
			$textRun = $richText->createTextRun(' '.$_Auxiliere.' ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun($_Type);
			$textRun->getFont()->getColor()->setARGB('FF44808A');
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun(' '.$L_T_Def_Activites_Essentielles);
			$textRun->getFont()->setSize(16);
			
			$activeWorksheet->getCell('A4')->setValue($richText);
			$activeWorksheet->getStyle('A4')->applyFromArray($fontTitre2Tableau);
			

			// Nouvelle cellule
			if ($Donnees['total_act_4'] > 1) {
				$_Sujet = $L_T_Activites;
				$_Auxiliere = $L_T_Sont;
				$_Type = $L_T_Vitales;
			} else {
				$_Sujet = $L_T_Activite;
				$_Auxiliere = $L_T_Est;
				$_Type = $L_T_Vitale;
			}

			$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

			$textRun = $richText->createTextRun($Donnees['total_act_3_4'].' '.$_Sujet);
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->getColor()->setARGB('FFC34A36'); // Rouge
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun(' '.$_Auxiliere.' ');
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun($_Type);
			$textRun->getFont()->getColor()->setARGB('FFC34A36');
			$textRun->getFont()->setBold(true);
			$textRun = $richText->createTextRun(' '.$L_T_Def_Activites_Essentielles);
			$textRun->getFont()->setSize(16);
			
			$activeWorksheet->getCell('B4')->setValue($richText);
			$activeWorksheet->getStyle('B4')->applyFromArray($fontTitre2Tableau);


			// Nouvelle occurrence du tableau

			// Nouvelle cellule
			if ($Donnees['total_sts'] > 1) {
				$_Sujet = $L_T_Sites;
				$_Suite = $L_T_Ont_Ete . ' ' . $L_T_Identifies . ' ' . $L_T_Ensemble_Activites_Analysees;
			} else {
				$_Sujet = $L_T_Site;
				$_Suite = $L_T_A_Ete . ' ' . $L_T_Identifie . ' ' . $L_T_Ensemble_Activites_Analysees;
			}

			$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

			$textRun = $richText->createTextRun($Donnees['total_sts'].' '.$_Sujet);
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->getColor()->setARGB('FF717D11'); // Vert
			$textRun->getFont()->setSize(16);
			$textRun = $richText->createTextRun(' '.$_Suite);
			$textRun->getFont()->setSize(16);
			
			$activeWorksheet->getCell('A5')->setValue($richText);
			$activeWorksheet->getStyle('A5')->applyFromArray($fontTitre2Tableau);


			// Nouvelle cellule
			if ($Donnees['total_app'] > 1) {
				$_Sujet = $L_T_Applications;
				$_Suite = $L_T_Supportant_Activites;
			} else {
				$_Sujet = $L_T_Application;
				$_Suite = $L_T_Supportant_Activites;
			}

			$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

			$textRun = $richText->createTextRun($Donnees['total_app'].' '.$_Sujet);
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun->getFont()->getColor()->setARGB('FF717D11'); // Vert
			$textRun = $richText->createTextRun(' '.$_Suite);
			$textRun->getFont()->setSize(16);

			$activeWorksheet->getCell('B5')->setValue($richText);
			$activeWorksheet->getStyle('B5')->applyFromArray($fontTitre2Tableau);


			// Nouvelle occurrence du tableau

			// Nouvelle cellule
			if ($Donnees['total_ppr'] > 1) {
				$_Sujet = $L_T_Personnes_Cles;
			} else {
				$_Sujet = $L_T_Personne_Cle;
			}

			$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

			$textRun = $richText->createTextRun($Donnees['total_ppr'].' '.$_Sujet);
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun->getFont()->getColor()->setARGB('FF717D11'); // Vert
			$textRun = $richText->createTextRun(' '.$_Suite);
			$textRun->getFont()->setSize(16);

			$activeWorksheet->getCell('A6')->setValue($richText);
			$activeWorksheet->getStyle('A6')->applyFromArray($fontTitre2Tableau);


			if ($Donnees['total_frn'] > 1) {
				$_Sujet = $L_T_Fournisseurs;
				$_Suite = $L_T_Supportant_Activites;
			} else {
				$_Sujet = $L_T_Fournisseurs;
				$_Suite = $L_T_Supportant_Activites;
			}

			$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

			$textRun = $richText->createTextRun($Donnees['total_frn'].' '.$_Sujet);
			$textRun->getFont()->setBold(true);
			$textRun->getFont()->setSize(16);
			$textRun->getFont()->getColor()->setARGB('FF717D11'); // Vert
			$textRun = $richText->createTextRun(' '.$_Suite);
			$textRun->getFont()->setSize(16);

			$activeWorksheet->getCell('B6')->setValue($richText);
			$activeWorksheet->getStyle('B6')->applyFromArray($fontTitre2Tableau);


			$activeWorksheet->getColumnDimension('A')->setAutoSize(true);
			$activeWorksheet->getColumnDimension('B')->setAutoSize(true);
		}


		// ===========================================
		// *******************************************
		// Gestion de l'onglet "Planning" et "Effectifs
		if ( $_POST['flag_liste_pln_eff'] == 'true' ) {
			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;
			$activeWorksheet->setTitle( $PageHTML->getLibelle('__LRI_PLANNING') );

			$activeWorksheet->mergeCells('A1:D1');
			$activeWorksheet->setCellValue('A1', $L_Entites);
			$activeWorksheet->getStyle('A1')->applyFromArray($fontChapitreDocument);

			// ------------------
			// Entête du tableau
			$activeWorksheet->setCellValue('A3', $L_Entite);
			$activeWorksheet->getStyle('A3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('B3', $PageHTML->getLibelle('__LRI_EFFECTIF'));
			$activeWorksheet->getStyle('B3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('C3', $L_CPCA);
			$activeWorksheet->getStyle('C3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('D3', $PageHTML->getLibelle('__LRI_DATE_ENTRETIEN'));
			$activeWorksheet->getStyle('D3')->applyFromArray($fontTitreTableau);

			// -----------------
			// Corps du tableau
			$Liste_Entites = $objCampagnes->rechercherEntitesAssocieesCampagne( $_SESSION['s_sct_id'], $_POST['cmp_id'], $_POST['entite_a_editer'] );
			$Ligne = 4;

			foreach( $Liste_Entites as $Entite ) {
				if ( $Entite->associe != null) {
					if (($Ligne % 2) == 0) {
						$fontCourant = $fontTexteTableau2;
					} else {
						$fontCourant = $fontTexteTableau;
					}

					$activeWorksheet->setCellValue('A' . $Ligne, $Entite->ent_nom . ' - ' . $Entite->ent_description);
					$activeWorksheet->getStyle('A'. $Ligne)->applyFromArray($fontCourant);

					$activeWorksheet->setCellValue('B'. $Ligne, ($Entite->cmen_effectif_total == '' ? '-' : $Entite->cmen_effectif_total));
					$activeWorksheet->getStyle('B'. $Ligne)->applyFromArray($fontCourant);

					$activeWorksheet->setCellValue('C'. $Ligne, ($Entite->ppr_nom . ' ' . $Entite->ppr_prenom == ' ' ? '-' : $Entite->ppr_nom . ' ' . $Entite->ppr_prenom));
					$activeWorksheet->getStyle('C'. $Ligne)->applyFromArray($fontCourant);

					$activeWorksheet->setCellValue('D'. $Ligne, ($Entite->cmen_date_entretien_cpca == '' ? '-' : $Entite->cmen_date_entretien_cpca));
					$activeWorksheet->getStyle('D'. $Ligne)->applyFromArray($fontCourant);

					$Ligne += 1;
				}
			}

			// ------------------------------------------------------------------
			// Ajuste la taille des colonnes en fonction du contenu de celles-ci
			$activeWorksheet->getColumnDimension('A')->setAutoSize(true);
			$activeWorksheet->getColumnDimension('B')->setAutoSize(true);
			$activeWorksheet->getColumnDimension('C')->setAutoSize(true);
			$activeWorksheet->getColumnDimension('D')->setAutoSize(true);



			// ************************************
			// ====================================
			// Mise à jour de l'onglet "Effectifs"
			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;
			$activeWorksheet->setTitle($PageHTML->getLibelle('__LRI_EFFECTIFS'));

			// ------------------
			// Entête du tableau
			$activeWorksheet->mergeCells('A1:E1');
			$activeWorksheet->setCellValue('A1', 'Effectifs des départements par activités');
			$activeWorksheet->getStyle('A1')->applyFromArray($fontChapitreDocument);

			$activeWorksheet->setCellValue('A3', $L_Entite);
			$activeWorksheet->getStyle('A3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('B3', $L_Activite);
			$activeWorksheet->getStyle('B3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('C3', $L_Site);
			$activeWorksheet->getStyle('C3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('D3', 'Effectifs en mode nominal');
			$activeWorksheet->getStyle('D3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('E3', 'Effectifs à distance');
			$activeWorksheet->getStyle('E3')->applyFromArray($fontTitreTableau);

			// ------------------
			// Corps du tableau
			$Liste_Activites = $objActivites->rechercherEffectifsActivite($_POST['cmp_id'], $_POST['entite_a_editer']);
			$Ligne = 4;

			foreach( $Liste_Activites as $Activite ) {
				if (($Ligne % 2) == 0) {
					$fontCourant = $fontTexteTableau2;
				} else {
					$fontCourant = $fontTexteTableau;
				}

				$activeWorksheet->setCellValue('A' . $Ligne, $Activite->ent_nom . ' - ' . $Activite->ent_description);
				$activeWorksheet->getStyle('A'. $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('B' . $Ligne, $Activite->act_nom);
				$activeWorksheet->getStyle('B'. $Ligne)->applyFromArray($fontCourant);

				$Sites = '';
				if ( $Activite->sites != [] && $Activite->sites != '' ) {
					foreach(explode('###', $Activite->sites) as $tSite) {
						$Site = explode('---', $tSite);
						if ($Sites != '') $Sites .= '\n';
						$Sites .= ($Site[2] == 0 ? $L_Site_Nominal : $L_Site_Secours).' : ' . $Site[0] . ($Site[1] != '' ? ' ('. $Site[1].')':'');
					}
				}
				$activeWorksheet->setCellValue('C' . $Ligne, $Sites);
				$activeWorksheet->getStyle('C'. $Ligne)->applyFromArray($fontCourant);
				
				$activeWorksheet->setCellValue('D' . $Ligne, ($Activite->act_effectifs_en_nominal == '' ? '-' : $Activite->act_effectifs_en_nominal));
				$activeWorksheet->getStyle('D'. $Ligne)->applyFromArray($fontCourant);
				
				$activeWorksheet->setCellValue('E' . $Ligne, ($Activite->act_effectifs_a_distance == '' ? '-' : $Activite->act_effectifs_a_distance));
				$activeWorksheet->getStyle('E'. $Ligne)->applyFromArray($fontCourant);
				
				$Ligne += 1;
			}


			// ------------------------------------------------------------------
			// Ajuste la taille des colonnes en fonction du contenu de celles-ci
			$activeWorksheet->getColumnDimension('A')->setAutoSize(true);
			$activeWorksheet->getColumnDimension('B')->setAutoSize(true);
			$activeWorksheet->getColumnDimension('C')->setAutoSize(true);
			$activeWorksheet->getColumnDimension('D')->setAutoSize(true);
			$activeWorksheet->getColumnDimension('E')->setAutoSize(true);
		}


		// ***************************************
		// =======================================
		// Mise à jour de l'onglet "Activités"
		if ($_POST['flag_liste_act'] == 'true') {
			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;

			$activeWorksheet->setTitle($L_Activites);

			$Liste_Niveaux_Impact = $objMatriceImpacts->rechercherNiveauxImpact($_POST['cmp_id']);
			$Liste_Niveaux_Impact_Poids = [];
			foreach ($Liste_Niveaux_Impact as $Element) {
				$Liste_Niveaux_Impact_Poids[$Element->nim_poids] = $Element;
			}

			$Liste_Echelles_Temps = $objEchellesTemps->rechercherEchellesTemps($_POST['cmp_id']);
			$Liste_Echelles_Temps_Poids = [];
			foreach ($Liste_Echelles_Temps as $Element) {
				$Liste_Echelles_Temps_Poids[$Element->ete_poids] = $Element;
			}

			$Liste_EchellesTemps_Poids = [];
			foreach( $Liste_EchellesTemps as $EchelleTemps ) {
				$Liste_EchellesTemps_Poids[$EchelleTemps->ete_poids] = $EchelleTemps;
			}

			// ------------------
			// Entête du tableau
			$activeWorksheet->mergeCells('A1:H1');
			$activeWorksheet->setCellValue('A1', 'Description de l\'activité');
			$activeWorksheet->getStyle('A1:H1')->applyFromArray($fontChapitreDocument);

			$activeWorksheet->setCellValue('A3', $L_Entite);
			$activeWorksheet->getStyle('A3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('B3', $L_Activite);
			$activeWorksheet->getStyle('B3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('C3', $L_Responsable_Activite);
			$activeWorksheet->getStyle('C3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('D3', $L_Suppleant);
			$activeWorksheet->getStyle('D3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('E3', $L_Site);
			$activeWorksheet->getStyle('E3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('F3', $L_Niveau_Impact);
			$activeWorksheet->getStyle('F3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('G3', $L_DMIA);
			$activeWorksheet->getStyle('G3')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('H3', $L_Justification_DMIA);
			$activeWorksheet->getStyle('H3')->applyFromArray($fontTitreTableau);

			$PositionIndex = 10;

			// Index de départ
			$PositionIndex = 10;
			$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);
			$activeWorksheet->setCellValue($LettreColonne1.'1', $L_Impacts);
			$activeWorksheet->getStyle($LettreColonne1.'1')->applyFromArray($fontChapitreDocument);
			$EchellesTemps = $objEchellesTemps->rechercherEchellesTemps($_POST['cmp_id']);
			$LettreColonne2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex + ((count($EchellesTemps)-1)*2));
			$activeWorksheet->mergeCells($LettreColonne1.'1:'.$LettreColonne2.'1');
	
			foreach ( $EchellesTemps as $EchelleTemps) {
				$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);
				$PositionIndex += 1;
	
				$LettreColonne2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);
				$PositionIndex += 1;
	
				$activeWorksheet->setCellValue($LettreColonne1 . '3', $EchelleTemps->ete_nom_code);
				$activeWorksheet->getStyle($LettreColonne1 . '3:'.$LettreColonne2.'3')->applyFromArray($fontTitreTableau);
				$activeWorksheet->getStyle($LettreColonne1 . ':'.$LettreColonne2)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
	
				$activeWorksheet->mergeCells($LettreColonne1 . '3:'.$LettreColonne2.'3');
			}


			// ------------------
			// Corps du tableau
			$Liste_Activites = $objActivites->rechercherActivites($_POST['cmp_id'], $_POST['entite_a_editer'], 'ent_nom');
			$Ligne = 4;

			foreach( $Liste_Activites as $Activite ) {
				if (($Ligne % 2) == 0) {
					$fontCourant = $fontTexteTableau2;
				} else {
					$fontCourant = $fontTexteTableau;
				}

				$activeWorksheet->setCellValue('A' . $Ligne, $Activite->ent_nom . ' - ' . $Activite->ent_description);
				$activeWorksheet->getStyle('A'. $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('B' . $Ligne, $Activite->act_nom);
				$activeWorksheet->getStyle('B'. $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('C' . $Ligne, $Activite->ppr_nom_resp . ' ' . $Activite->ppr_prenom_resp);
				$activeWorksheet->getStyle('C'. $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('D' . $Ligne, $Activite->ppr_nom_supp . ' ' . $Activite->ppr_prenom_supp);
				$activeWorksheet->getStyle('D'. $Ligne)->applyFromArray($fontCourant);

				$Sites = '';
				if ( $Activite->sites != [] && $Activite->sites != '' ) {
					foreach(explode('###', $Activite->sites) as $tSite) {
						$Site = explode('---', $tSite);
						if ($Sites != '') $Sites .= '\n';
						$Sites .= ($Site[2] == 0 ? $L_Site_Nominal : $L_Site_Secours).' : ' . $Site[0] . ($Site[1] != '' ? ' ('. $Site[1].')':'');
					}
				}
				$activeWorksheet->setCellValue('E' . $Ligne, $Sites);
				$activeWorksheet->getStyle('E'. $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('F' . $Ligne, $Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_numero.' - '.$Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_nom_code);
				$activeWorksheet->getStyle('F'. $Ligne)->applyFromArray($fontCourant);
				$activeWorksheet->getStyle('F' . $Ligne)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()
					->setARGB('FF'.$Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_couleur);
				$activeWorksheet->getStyle('F' . $Ligne)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$activeWorksheet->setCellValue('G' . $Ligne, ($Activite->ete_poids == '' ? '-' : $Liste_EchellesTemps_Poids[$Activite->ete_poids]->ete_nom_code));
				$activeWorksheet->getStyle('G'. $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('H' . $Ligne, ($Activite->act_justification_dmia == '' ? '-' : $Activite->act_justification_dmia));
				$activeWorksheet->getStyle('H'. $Ligne)->applyFromArray($fontCourant);


				// --------------------------------
				$PositionIndex = 10; // Colonne "J"
				foreach( $objActivites->recupererDMIA($_POST['cmp_id'], $Activite->act_id) as $_DMIA ) {
					$LettreColonne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);

					$activeWorksheet->setCellValue($LettreColonne . $Ligne, $_DMIA->nim_numero.' - '.$_DMIA->nim_nom_code);
					$activeWorksheet->getStyle($LettreColonne . $Ligne)->applyFromArray($fontCourant);
					$activeWorksheet->getStyle($LettreColonne . $Ligne)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()
						->setARGB('FF'.$_DMIA->nim_couleur);
					$activeWorksheet->getStyle($LettreColonne . $Ligne)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

					$PositionIndex += 1;
					$LettreColonne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);
					
					$activeWorksheet->setCellValue($LettreColonne . $Ligne, $_DMIA->tim_nom_code);
					$activeWorksheet->getStyle($LettreColonne . $Ligne)->applyFromArray($fontCourant);
					$activeWorksheet->getStyle($LettreColonne . $Ligne)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
					
					$PositionIndex += 1;
				}

				$Ligne += 1;
			}

			// ------------------------------------------------------------------
			// Ajuste la taille des colonnes en fonction du contenu de celles-ci
			for( $_tmp = 0; $_tmp <= $PositionIndex; $_tmp++) {
				$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_tmp);
				$activeWorksheet->getColumnDimension($LettreColonne1)->setAutoSize(true);
			}
		}


		// ************************************************
		// ================================================
		// Affichage des Personnes Clés de cette Campagne.
		if ( $_POST['flag_liste_ppr'] == 'true' ) {
			$Liste_Personnes = $objCampagnes->rechercherPersonnesClesCampagne( $_POST['cmp_id'], $_POST['entite_a_editer'] );

			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;
			$activeWorksheet->setTitle($L_Personnes_Cles);

			$tmp_ent_nom = 0;
			$Ligne = 2;
			foreach($Liste_Personnes as $Personne) {
				if (($Ligne % 2) == 0) {
					$fontCourant = $fontTexteTableau2;
				} else {
					$fontCourant = $fontTexteTableau;
				}

				if ($tmp_ent_nom != $Personne->ent_nom) {
					$tmp_ent_nom = $Personne->ent_nom;

					$activeWorksheet->setCellValue('A1', $L_Entite);
					$activeWorksheet->getStyle('A1')->applyFromArray($fontTitreTableau);
//					$activeWorksheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

					$activeWorksheet->setCellValue('B1', $L_Prenom);
					$activeWorksheet->getStyle('B1')->applyFromArray($fontTitreTableau);
//					$activeWorksheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

					$activeWorksheet->setCellValue('C1', $L_Nom);
					$activeWorksheet->getStyle('C1')->applyFromArray($fontTitreTableau);
//					$activeWorksheet->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

					$activeWorksheet->setCellValue('D1', $L_Activites);
					$activeWorksheet->getStyle('D1')->applyFromArray($fontTitreTableau);
//					$activeWorksheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

					$activeWorksheet->setCellValue('E1', $L_Description);
					$activeWorksheet->getStyle('E1')->applyFromArray($fontTitreTableau);
//					$activeWorksheet->getStyle('E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				}

				$activeWorksheet->setCellValue('A'.$Ligne, $Personne->ent_nom . ' (' . $Personne->ent_description . ')');
				$activeWorksheet->getStyle('A'.$Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('B'.$Ligne, $Personne->ppr_prenom);
				$activeWorksheet->getStyle('B'.$Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('C'.$Ligne, $Personne->ppr_nom);
				$activeWorksheet->getStyle('C'.$Ligne)->applyFromArray($fontCourant);

				$textlines = explode('<br>', $Personne->act_nom);
				$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
				$textRun = $richText->createTextRun(array_shift($textlines));

				foreach($textlines as $line) {
					$textRun = $richText->createTextRun("\n");
					$textRun = $richText->createTextRun($line);
				}
				$activeWorksheet->setCellValue('D'.$Ligne, $richText);
				$activeWorksheet->getStyle('D'.$Ligne)->applyFromArray($fontCourant);
				$activeWorksheet->getStyle('D'.$Ligne)->getAlignment()->setWrapText(true);
				
				$textlines = explode('<br>', $Personne->ppac_description);
				$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
				$textRun = $richText->createTextRun(array_shift($textlines));

				foreach($textlines as $line) {
					$textRun = $richText->createTextRun("\n");
					$textRun = $richText->createTextRun($line);
				}
				$activeWorksheet->setCellValue('E'.$Ligne, $richText);
				$activeWorksheet->getStyle('E'.$Ligne)->applyFromArray($fontCourant);
				$activeWorksheet->getStyle('E'.$Ligne)->getAlignment()->setWrapText(true);
				
				$Ligne += 1;
			}


			// ------------------------------------------------------------------
			// Ajuste la taille des colonnes en fonction du contenu de celles-ci
			for( $_tmp = 1; $_tmp <= 5; $_tmp++) {
				$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_tmp);
				$activeWorksheet->getColumnDimension($LettreColonne1)->setAutoSize(true);
			}
		}


		// ***************************************
		// =======================================
		// Mise à jour de l'onglet "Interdépendances"
		if ($_POST['flag_liste_int'] == 'true') {
			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;
			
			$activeWorksheet->setTitle($L_Interdependances);

			$Liste_Niveaux_Impact = $objMatriceImpacts->rechercherNiveauxImpact($_POST['cmp_id']);
			$Liste_Niveaux_Impact_Poids = [];
			foreach ($Liste_Niveaux_Impact as $Element) {
				$Liste_Niveaux_Impact_Poids[$Element->nim_poids] = $Element;
			}

			$Liste_Echelles_Temps = $objEchellesTemps->rechercherEchellesTemps($_POST['cmp_id']);
			$Liste_Echelles_Temps_Poids = [];
			foreach ($Liste_Echelles_Temps as $Element) {
				$Liste_Echelles_Temps_Poids[$Element->ete_poids] = $Element;
			}

			$Liste_EchellesTemps_Poids = [];
			foreach( $Liste_EchellesTemps as $EchelleTemps ) {
				$Liste_EchellesTemps_Poids[$EchelleTemps->ete_poids] = $EchelleTemps;
			}

			// ------------------
			// Entête du tableau
			$activeWorksheet->setCellValue('A1', $L_Entite);
			$activeWorksheet->getStyle('A1')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('B1', $L_Activite);
			$activeWorksheet->getStyle('B1')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('C1', $L_Dependances_Internes_Amont);
			$activeWorksheet->getStyle('C1')->applyFromArray($fontTitreTableau);
			$activeWorksheet->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$activeWorksheet->setCellValue('D1', $L_Dependances_Internes_Aval);
			$activeWorksheet->getStyle('D1')->applyFromArray($fontTitreTableau);
			$activeWorksheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


			// ------------------
			// Corps du tableau
			$Liste_Activites = $objActivites->rechercherActivites($_POST['cmp_id'], $_POST['entite_a_editer'], 'ent_nom');
			$Ligne = 2;

			foreach( $Liste_Activites as $Activite ) {
				if (($Ligne % 2) == 0) {
					$fontCourant = $fontTexteTableau2;
				} else {
					$fontCourant = $fontTexteTableau;
				}

				$activeWorksheet->setCellValue('A' . $Ligne, $Activite->ent_nom . ' - ' . $Activite->ent_description);
				$activeWorksheet->getStyle('A'. $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('B' . $Ligne, $Activite->act_nom);
				$activeWorksheet->getStyle('B'. $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('C' . $Ligne, $Activite->act_dependances_internes_amont);
				$activeWorksheet->getStyle('C' . $Ligne)->applyFromArray($fontCourant);

				$activeWorksheet->setCellValue('D' . $Ligne, $Activite->act_dependances_internes_aval);
				$activeWorksheet->getStyle('D' . $Ligne)->applyFromArray($fontCourant);

				$Ligne += 1;
			}

			// ------------------------------------------------------------------
			// Ajuste la taille des colonnes en fonction du contenu de celles-ci
			for( $_tmp = 1; $_tmp <= 4; $_tmp++) {
				$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_tmp);
				$activeWorksheet->getColumnDimension($LettreColonne1)->setAutoSize(true);
			}
		}


		// ===========================================
		// Gestion des Applications de cette Campagne
		if ( $_POST['flag_liste_app'] == 'true' ) {
			// Affichage des Activités à redémarrer par période.
			$Liste_Activites = $objActivites->rechercherSyntheseActivites( $_POST['cmp_id'], $_POST['entite_a_editer'], '', 'ete_poids' );
			
			$Liste_Activites_ID = [];
			foreach( $Liste_Activites as $Occurrence ) {
				$Liste_Activites_ID[$Occurrence->act_id] = $Occurrence;
			}

			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;
			$activeWorksheet->setTitle($L_Applications);

			// -------------------------------------------------------
			// Affichage des Applications de cette Campagne par DMIA.
			$Liste_Applications = $objCampagnes->rechercherApplicationsCampagne( $_POST['cmp_id'], '*', $_POST['entite_a_editer'] );

			$activeWorksheet->setCellValue('A1', $L_Application);
			$activeWorksheet->getStyle('A1')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('B1', $L_Activites);
			$activeWorksheet->getStyle('B1')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('C1', $L_DMIA);
			$activeWorksheet->getStyle('C1')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('D1', $L_PDMA);
			$activeWorksheet->getStyle('D1')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('E1', $L_Donnees);
			$activeWorksheet->getStyle('E1')->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('F1', $L_Palliatif);
			$activeWorksheet->getStyle('F1')->applyFromArray($fontTitreTableau);

			$Ligne = 2;

			foreach($Liste_Applications as $Application) {
				if (($Ligne % 2) == 0) {
					$fontCourant = $fontTexteTableau2;
				} else {
					$fontCourant = $fontTexteTableau;
				}

				$activeWorksheet->getCell('A'.$Ligne)->setValue($Application->app_nom);
				$activeWorksheet->getStyle('A'.$Ligne)->applyFromArray($fontCourant);


				// Affichage des Activités associées à cette Application.
				// Nom de l'Entité, Nom de l'Activité et de sa Sensibilité
				$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

				$textlines = explode(',<br>', $Application->act_nom);

				$line = explode('###', array_shift($textlines));
				$_act_nom = $line[0];
				$_act_id = $line[1];

				$textRun = $richText->createTextRun($_act_nom . ' ');

				if ( array_key_exists($_act_id, $Liste_Activites_ID) ) {
					$textRun = $richText->createTextRun('(');

					$_nim_poids = $Liste_Activites_ID[$_act_id]->nim_poids;

					$textRun = $richText->createTextRun($_nim_poids . ' - ' . $Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_nom_code);
					$textRun->getFont()->setBold(true);
					$textRun->getFont()->getColor()->setARGB('FF'.$Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_couleur);

					if ( $_nim_poids > 2 ) {
						$_ete_poids = $Liste_Activites_ID[$_act_id]->ete_poids;
						if ($_ete_poids != '') {
							$textRun = $richText->createTextRun(' / ' . $Liste_Echelles_Temps_Poids[$_ete_poids]->ete_nom_code);
							$textRun->getFont()->setBold(true);
						}
					}

					$textRun = $richText->createTextRun(')');
				}

				foreach($textlines as $line) {
					$line = explode('###', $line);
					$_act_nom = $line[0];
					$_act_id = $line[1];

					$textRun = $richText->createTextRun("\n");
					$textRun = $richText->createTextRun($_act_nom . ' ');

					if ( array_key_exists($_act_id, $Liste_Activites_ID) ) {
						$textRun = $richText->createTextRun('(');

						$_nim_poids = $Liste_Activites_ID[$_act_id]->nim_poids;

						$textRun = $richText->createTextRun($_nim_poids . ' - ' . $Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_nom_code);
						$textRun->getFont()->setBold(true);
						$textRun->getFont()->getColor()->setARGB('FF'.$Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_couleur);

						if ( $_nim_poids > 2 ) {
							$_ete_poids = $Liste_Activites_ID[$_act_id]->ete_poids;
							if ($_ete_poids != '') {
								$textRun = $richText->createTextRun(' / ' . $Liste_Echelles_Temps_Poids[$_ete_poids]->ete_nom_code);
								$textRun->getFont()->setBold(true);
							}
						}

						$textRun = $richText->createTextRun(')');
					}
				}
				$activeWorksheet->setCellValue('B'.$Ligne, $richText);
				$activeWorksheet->getStyle('B'.$Ligne)->applyFromArray($fontCourant);
				$activeWorksheet->getStyle('B'.$Ligne)->getAlignment()->setWrapText(true);


				$activeWorksheet->setCellValue('C'.$Ligne, $Liste_Echelles_Temps_Poids[$Application->dmia]->ete_nom_code);
				$activeWorksheet->getStyle('C'.$Ligne)->applyFromArray($fontCourant);
				
				$activeWorksheet->setCellValue('D'.$Ligne, $Liste_Echelles_Temps_Poids[$Application->pdma]->ete_nom_code);
				$activeWorksheet->getStyle('D'.$Ligne)->applyFromArray($fontCourant);


				$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

				$textlines = explode('##', $Application->acap_donnees);
				if (count($textlines) > 0) {
					$compteur = 0;

					foreach($textlines as $line) {
						if ($line != '') {
							$compteur += 1;

							if ($compteur > 1) {
								$textrun = $richText->createTextRun("\n");
							}

							$textrun = $richText->createTextRun($line, $fontTexteTableau, $styleParagrapheTableau);
						}
					}
					
					if ($compteur == 0) {
						$textrun = $richText->createTextRun($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
					}
				} else {
					$textrun = $richText->createTextRun($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
				}
				$activeWorksheet->setCellValue('E'.$Ligne, $richText);
				$activeWorksheet->getStyle('E'.$Ligne)->applyFromArray($fontCourant);
				$activeWorksheet->getStyle('E'.$Ligne)->getAlignment()->setWrapText(true);


				$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

				$textlines = explode('##', $Application->acap_palliatif);
				if (count($textlines) > 0) {
					$compteur = 0;

					foreach($textlines as $line) {
						if ($line != '') {
							$compteur += 1;
							
							if ($compteur > 1) {
								$textrun = $richText->createTextRun("\n");
							}

							$textrun = $richText->createTextRun($line, $fontTexteTableau, $styleParagrapheTableau);
						}
					}

					if ($compteur == 0) {
						$textrun = $richText->createTextRun($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
					}
				} else {
					$textrun = $richText->createTextRun($L_Neither, $fontTexteTableau, $styleParagrapheTableau);
				}
				$activeWorksheet->setCellValue('F'.$Ligne, $richText);
				$activeWorksheet->getStyle('F'.$Ligne)->applyFromArray($fontCourant);
				$activeWorksheet->getStyle('F'.$Ligne)->getAlignment()->setWrapText(true);

				$Ligne += 1;
			}


			// ------------------------------------------------------------------
			// Ajuste la taille des colonnes en fonction du contenu de celles-ci
			for( $_tmp = 1; $_tmp <= 6; $_tmp++) {
				$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_tmp);
				$activeWorksheet->getColumnDimension($LettreColonne1)->setAutoSize(true);
			}
		}


		// *************************
		// =========================
		// Gestion des fournisseurs
		if ( $_POST['flag_liste_frn'] == 'true' ) {
			// Affichage des Fournisseurs de cette Campagne.
			$Liste_Fournisseurs = $objCampagnes->rechercherFournisseursCampagne( $_POST['cmp_id'], '*', $_POST['entite_a_editer'] );

			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;
			$activeWorksheet->setTitle($L_Fournisseurs);

			$Ligne = 1;

			$activeWorksheet->setCellValue('A'.$Ligne, $L_Fournisseur);
			$activeWorksheet->getStyle('A'.$Ligne)->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('B'.$Ligne, $L_Type);
			$activeWorksheet->getStyle('B'.$Ligne)->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('C'.$Ligne, $L_Entite);
			$activeWorksheet->getStyle('C'.$Ligne)->applyFromArray($fontTitreTableau);

			$activeWorksheet->setCellValue('D'.$Ligne, $L_Activites);
			$activeWorksheet->getStyle('D'.$Ligne)->applyFromArray($fontTitreTableau);

			$Ligne += 1;

			foreach($Liste_Fournisseurs as $Fournisseur) {
				if (($Ligne % 2) == 0) {
					$fontCourant = $fontTexteTableau2;
				} else {
					$fontCourant = $fontTexteTableau;
				}
				
				$activeWorksheet->setCellValue('A'.$Ligne, $Fournisseur->frn_nom);
				$activeWorksheet->getStyle('A'.$Ligne)->applyFromArray($fontCourant);
				
				$activeWorksheet->setCellValue('B'.$Ligne, $Fournisseur->tfr_nom_code);
				$activeWorksheet->getStyle('B'.$Ligne)->applyFromArray($fontCourant);
				
				$activeWorksheet->setCellValue('C'.$Ligne, $Fournisseur->ent_nom . ' (' . $Fournisseur->ent_description . ')');
				$activeWorksheet->getStyle('C'.$Ligne)->applyFromArray($fontCourant);
				
				$textlines = explode('<br>', $Fournisseur->act_nom);
				$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
				$textrun = $richText->createTextRun(array_shift($textlines));
				
				foreach($textlines as $line) {
					$textrun = $richText->createTextRun("\n");
					$textrun = $richText->createTextRun($line);
				}
				$activeWorksheet->setCellValue('D'.$Ligne, $richText);
				$activeWorksheet->getStyle('D'.$Ligne)->applyFromArray($fontCourant);
				$activeWorksheet->getStyle('D'.$Ligne)->getAlignment()->setWrapText(true);
				
				$Ligne += 1;
			}


			// ------------------------------------------------------------------
			// Ajuste la taille des colonnes en fonction du contenu de celles-ci
			for( $_tmp = 1; $_tmp <= 4; $_tmp++) {
				$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_tmp);
				$activeWorksheet->getColumnDimension($LettreColonne1)->setAutoSize(true);
			}
		}
		
		
		// ***************************************
		// =======================================
		// Mise à jour de l'onglet "Personnes Prioritaires"
		if ($_POST['flag_liste_ppr'] == 'true') {
			if ($Numero_Onglet == 0) {
				$activeWorksheet = $spreadsheet->getActiveSheet();
			} else {
				$activeWorksheet = $spreadsheet->createSheet();
			}
			$Numero_Onglet += 1;
			
			$activeWorksheet->setTitle($PageHTML->getLibelle('__LRI_PERSONNES_PRIORITAIRES'));
			
			$Liste_Niveaux_Impact = $objMatriceImpacts->rechercherNiveauxImpact($_POST['cmp_id']);
			$Liste_Niveaux_Impact_Poids = [];
			foreach ($Liste_Niveaux_Impact as $Element) {
				$Liste_Niveaux_Impact_Poids[$Element->nim_poids] = $Element;
			}
			
			$Liste_Echelles_Temps = $objEchellesTemps->rechercherEchellesTemps($_POST['cmp_id']);
			$Liste_Echelles_Temps_Poids = [];
			foreach ($Liste_Echelles_Temps as $Element) {
				$Liste_Echelles_Temps_Poids[$Element->ete_poids] = $Element;
			}
			
			$Liste_EchellesTemps_Poids = [];
			foreach( $Liste_EchellesTemps as $EchelleTemps ) {
				$Liste_EchellesTemps_Poids[$EchelleTemps->ete_poids] = $EchelleTemps;
			}
			
			// ------------------
			// Entête du tableau
			$activeWorksheet->setCellValue('A1', $L_Entite);
			$activeWorksheet->getStyle('A1')->applyFromArray($fontTitreTableau);
			
			$activeWorksheet->setCellValue('B1', $L_Activite);
			$activeWorksheet->getStyle('B1')->applyFromArray($fontTitreTableau);

			$PositionIndex = 3;

			$EchellesTemps = $objEchellesTemps->rechercherEchellesTemps($_POST['cmp_id']);

			foreach ( $EchellesTemps as $EchelleTemps) {
				$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);
				$PositionIndex += 1;

				$activeWorksheet->setCellValue($LettreColonne1 . '1', $EchelleTemps->ete_nom_code);
				$activeWorksheet->getStyle($LettreColonne1 . '1')->applyFromArray($fontTitreTableau);
				$activeWorksheet->getStyle($LettreColonne1 . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			}


			// ------------------
			// Corps du tableau
			$Liste_Activites = $objActivites->rechercherActivites($_POST['cmp_id'], $_POST['entite_a_editer'], 'ent_nom');
			$Ligne = 2;
			
			foreach( $Liste_Activites as $Activite ) {
				if (($Ligne % 2) == 0) {
					$fontCourant = $fontTexteTableau2;
				} else {
					$fontCourant = $fontTexteTableau;
				}
				
				$activeWorksheet->setCellValue('A' . $Ligne, $Activite->ent_nom . ' - ' . $Activite->ent_description);
				$activeWorksheet->getStyle('A'. $Ligne)->applyFromArray($fontCourant);
				
				$activeWorksheet->setCellValue('B' . $Ligne, $Activite->act_nom);
				$activeWorksheet->getStyle('B'. $Ligne)->applyFromArray($fontCourant);
				
				
				// --------------------------------
				$PositionIndex = 3;

				$Liste_Personnes_Prioritaires = $objActivites->recupererPersonnesPrioritaires( $Activite->act_id );
				$Liste_Personnes_Prioritaires_idx = [];
				foreach($Liste_Personnes_Prioritaires as $_tmp) {
					$Liste_Personnes_Prioritaires_idx[$_tmp->ete_id] = $_tmp->rut_nbr_utilisateurs_a_redemarrer;
				}

				foreach ( $EchellesTemps as $EchelleTemps) {
					$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);
					$PositionIndex += 1;

					if ( array_key_exists($EchelleTemps->ete_id, $Liste_Personnes_Prioritaires_idx) ) {
						$_tmp = $Liste_Personnes_Prioritaires_idx[$EchelleTemps->ete_id];
					} else {
						$_tmp = 0;
					}
					$activeWorksheet->setCellValue($LettreColonne1 . $Ligne, $_tmp);
					$activeWorksheet->getStyle($LettreColonne1 . $Ligne)->applyFromArray($fontCourant);
					$activeWorksheet->getStyle($LettreColonne1 . $Ligne)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				}

				$Ligne += 1;
			}
			
			// ------------------------------------------------------------------
			// Ajuste la taille des colonnes en fonction du contenu de celles-ci
			for( $_tmp = 0; $_tmp <= $PositionIndex; $_tmp++) {
				$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_tmp);
				$activeWorksheet->getColumnDimension($LettreColonne1)->setAutoSize(true);
			}
		}


		// **********************
		// ======================
		// Affichage de l'échelle
		$Liste_Matrice_Impacts = $objMatriceImpacts->rechercherMatriceImpactsParID( $_POST['cmp_id'] );
		$Liste_EchellesTemps = $objEchellesTemps->rechercherEchellesTemps($_POST['cmp_id']);
		$Liste_Niveaux_Impact = $objCampagnes->rechercherNiveauxImpactCampagne( $_POST['cmp_id'] );
		$Liste_Types_Impact = $objCampagnes->rechercherTypesImpactCampagne( $_POST['cmp_id'] );

		$Nombre_Types = count($Liste_Types_Impact);

		if ($Numero_Onglet == 0) {
			$activeWorksheet = $spreadsheet->getActiveSheet();
		} else {
			$activeWorksheet = $spreadsheet->createSheet();
		}
		$Numero_Onglet += 1;
		$activeWorksheet->setTitle($PageHTML->getLibelle('__LRI_ECHELLES'));

		$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

		$textRun = $richText->createTextRun($L_Type);
		$textRun->getFont()->setBold(true);
//		$textRun->getFont()->getColor()->setARGB('FF'.$Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_couleur);
		$textRun = $richText->createTextRun("\n");
		$textRun = $richText->createTextRun('---');
		$textRun->getFont()->setBold(true);
//		$textRun->getFont()->getColor()->setARGB('FF'.$Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_couleur);
		$textRun = $richText->createTextRun("\n");
		$textRun = $richText->createTextRun($L_Niveau);
		$textRun->getFont()->setBold(true);
//		$textRun->getFont()->getColor()->setARGB('FF'.$Liste_Niveaux_Impact_Poids[$_nim_poids]->nim_couleur);
		
		$Ligne = 1;

		$activeWorksheet->setCellValue('A'.$Ligne, $richText);
		$activeWorksheet->getStyle('A'.$Ligne)->getAlignment()->setWrapText(true);
		$activeWorksheet->getStyle('A'.$Ligne)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//		$activeWorksheet->getStyle('A'.$Ligne)->getAlignment()->setTextRotation(10);
		$activeWorksheet->getStyle('A'.$Ligne)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFECECEC');
		$activeWorksheet->getStyle('A'.$Ligne)->applyFromArray($fontTitreTableau);

		if ($Nombre_Types > 1) {
			$PositionIndex = 2;
			foreach ($Liste_Types_Impact as $Type_Impact) {
				$LettreColonne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);

				$activeWorksheet->setCellValue($LettreColonne.$Ligne, $Type_Impact->tim_nom_code);
				$activeWorksheet->getStyle($LettreColonne.$Ligne)->applyFromArray($fontTitreTableau);

				$PositionIndex += 1;
			}
		}

		$Ligne = 2;
		foreach ( $Liste_Niveaux_Impact as $Niveau_Impact ) {
			$PositionIndex = 1;
			$LettreColonne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);

			$activeWorksheet->setCellValue($LettreColonne.$Ligne, $Niveau_Impact->nim_poids.' - '.$Niveau_Impact->nim_nom_code);
			$activeWorksheet->getStyle($LettreColonne.$Ligne)->applyFromArray($fontTexteFortTableau);

			if ( isset($Liste_Types_Impact) ) {
				foreach ($Liste_Types_Impact as $Type_Impact) {
					if ( isset($Liste_Matrice_Impacts[$Niveau_Impact->nim_id.'-'.$Type_Impact->tim_id]) ) {
						$Description = html_entity_decode($Liste_Matrice_Impacts[$Niveau_Impact->nim_id.'-'.$Type_Impact->tim_id]->mim_description);
					} else {
						$Description = '';
					}
					if ( str_contains($Description, '<ul>') ) {
						$Description = str_replace(['<ul>', '</ul>', '<li>', '<br>'], ['', '', '', ''], $Description);

						$textlines = explode('</li>', $Description);

						$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

						$textrun = $richText->createTextRun('- '.array_shift($textlines));

						foreach($textlines as $line) {
							if ($line != '') {
								$textrun = $richText->createTextRun("\n");
								$textrun = $richText->createTextRun('- '.$line);
							}
						}
					} else {
						$textlines = explode('<br>', $Description);

						$richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

						$textrun = $richText->createTextRun('- '.array_shift($textlines));

						foreach($textlines as $line) {
							if ($line != '') {
								$textrun = $richText->createTextRun("\n");
								$textrun = $richText->createTextRun('- '.$line);
							}
						}
					}

					$PositionIndex += 1;

					$LettreColonne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($PositionIndex);

					$activeWorksheet->setCellValue($LettreColonne.$Ligne, $richText);
					$activeWorksheet->getStyle($LettreColonne.$Ligne)->applyFromArray($fontTexteTableau);
					$activeWorksheet->getStyle($LettreColonne.$Ligne)->getAlignment()->setWrapText(true);
					$activeWorksheet->getStyle($LettreColonne.$Ligne)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()
						->setARGB('FF'.$Niveau_Impact->nim_couleur); //$Liste_Niveaux_Impact_Poids[$Niveau_Impact->nim_poids]->nim_couleur);
					
				}

				$Ligne += 1;
			}
		}


		// ------------------------------------------------------------------
		// Ajuste la taille des colonnes en fonction du contenu de celles-ci
		for( $_tmp = 1; $_tmp <= ($Nombre_Types+1); $_tmp++) {
			$LettreColonne1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_tmp);
			$activeWorksheet->getColumnDimension($LettreColonne1)->setAutoSize(true);
		}


		// =============================================
		// Sauvegarde des données dans un fichier Excel

		$writer = new Xlsx($spreadsheet);
		$writer->save($Nom_Fichier_Complet);
	}

	$Resultat = array( 'statut' => 'success',
		'texteMsg' => $L_Edition_Terminee,
		'Nom_Fichier' => $Nom_Fichier,
		'Nom_Fichier_Complet' => $Nom_Fichier_Complet
	);

	echo json_encode( $Resultat );

	break;


 case 'AJAX_Charger_Impression':
	// Envoi le fichier généré à l'appelant.
	header( 'Content-Description: File Transfer' );
	header( 'Content-Type: application/octet-stream' );
	header( 'Content-Disposition: attachment; filename="' . $_GET['Nom_Fichier'] . '"' );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0, public' );
	header( 'Pragma: no-cache' );
	header( 'Content-Length: ' . filesize( $_GET['Nom_Fichier_Complet'] ) );
	ob_clean();
	flush();
	readfile( $_GET['Nom_Fichier_Complet'] );

	unlink( $_GET['Nom_Fichier_Complet'] );

	break;
}
?>