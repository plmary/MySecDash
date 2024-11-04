<?php

//use PhpOffice\PhpSpreadsheet\Reader\Html;
//use PhpOffice\PhpSpreadsheet\IOFactory;

/**
* Ce script gère la visualisation des BIA.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-09-05
*
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

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


// Exécute l'action identifiée
switch( $Action ) {
 default:
	$Liste_Societes = '';
	$Liste_Campagnes = '';
	$Liste_Entites = '';
	
	try {
		list($Liste_Societes, $Liste_Campagnes, $Liste_Entites) =
			actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites);
	} catch ( Exception $e ) {
		print('<h1 class="text-urgent">' . $e->getMessage() . '</h1>');
		break;
	}
	

	$Boutons_Alternatifs = [/*
		['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'],
		['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search']*/
	];


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
		$Choix_Entites['options'][] = array('id' => '*', 'nom' => $L_Toutes );
		foreach( $Liste_Entites AS $Entite ) {
			$Choix_Entites['options'][] = array('id' => $Entite->ent_id, 'nom' => $Entite->ent_nom );
		}
	}

	$Fichiers_JavaScript[] = 'MatriceImpacts.js';

	print( $PageHTML->construireEnteteHTML( $L_Visualiser_BIA, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Visualiser_BIA, $Liste_Societes, $Boutons_Alternatifs,
			$Choix_Campagnes, '', $Choix_Entites )
		);

	print('<div id="corps_tableau" class="container-fluid mt-4"></div>');

	print( $PageHTML->construireFooter( TRUE ) .
		$PageHTML->construirePiedHTML() );

	break;


 case 'AJAX_Selectioner_Societe':
	if ( isset($_POST['sct_id']) ) {
		$_SESSION['s_sct_id'] = $_POST['sct_id'];

		try {
			list($Liste_Societes, $Liste_Campagnes, $Liste_Entites) =
				actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites, 1);
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
		$_SESSION['s_cmp_id'] = $_POST['cmp_id'];

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
			'texteMsg' => $L_Campagne_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id'],
			'ent_id' => $_SESSION['s_ent_id'],
			'L_Toutes' => $L_Toutes,
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
		$_SESSION['s_ent_id'] = $_POST['ent_id'];

/*		try {
			list($Liste_Societes, $Liste_Campagnes, $Liste_Entites) =
				actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $objActivites, 3);
		} catch ( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
			echo json_encode( $Resultat );
			break;
		} */

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Entite_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id'],
			'ent_id' => $_SESSION['s_ent_id'],
			'L_Toutes' => $L_Toutes
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (ent_id)' );
	}

	echo json_encode( $Resultat );
	
	break;
	
	
 case 'AJAX_Synthese_Gloable':
	if ( $Droit_Lecture === TRUE ) {
		$Donnees = $objCampagnes->syntheseCampagne( $_SESSION['s_cmp_id'] );

		$Nombre_BIA_A_Faire = $Donnees['total_bia'] - ( $Donnees['total_bia_valides'] + $Donnees['total_bia_en_cours'] );

		// Construit le corps de l'écran.
		$Corps_HTML = '<h1 class="text-center">' . $L_Synthese_Manageriale_Globale . '</h1>' .
			'<table class="table table-bordered">' .
			'<caption>' . $L_Activites_Non_Critiques_Non_Abordees . '</caption>' .
			'<thead>' .
			'<tr><th colspan="2" class="text-center">';

		if ($Donnees['total_bia'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_BIAs_Identifies_Campagne . ', ', $Donnees['total_bia']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_BIA_Identifie_Campagne, $Donnees['total_bia']);
		}

		if ($Donnees['total_bia_valides'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_BIAs_Valides_Campagne . ', ', $Donnees['total_bia_valides']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_BIA_Valide_Campagne . ', ', $Donnees['total_bia_valides']);
		}

		if ($Donnees['total_bia_en_cours'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_BIAs_En_Cours_Campagne . ', ', $Donnees['total_bia_en_cours']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_BIA_En_Cours_Campagne . ', ', $Donnees['total_bia_en_cours']);
		}

		$Corps_HTML .= ' ' . $L_Et . ' ';

		if ($Nombre_BIA_A_Faire > 1) {
			$Corps_HTML .= sprintf($L_Nombre_BIAs_A_Faire_Campagne . '</th>', $Nombre_BIA_A_Faire);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_BIA_A_Faire_Campagne . '</th>', $Nombre_BIA_A_Faire);
		}

		$Corps_HTML .= '</thead>' .
			'<tbody class="fw-bold">' .
			'<tr>' .
			'<td class="col-6"><i class="bi-gear fg_couleur_2 m-3" style="font-size: 3rem;"></i> ';

		if ($Donnees['total_act_3_4'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_Activites_Essentielles . '</td>', $Donnees['total_act_3_4']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_Activite_Essentielle . '</td>', $Donnees['total_act_3_4']);
		}

		$Corps_HTML .= '<td><i class="bi-alarm fg_couleur_3 m-3" style="font-size: 3rem;"></i> ';

		if ($Donnees['total_act_4'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_Activites_Critiques . '</td></tr>', $Donnees['total_act_4']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_Activite_Critique . '</td></tr>', $Donnees['total_act_4']);
		}

		$Corps_HTML .= '<tr><td><i class="bi-buildings fg_couleur_1 m-3" style="font-size: 3rem;"></i> ';

		if ($Donnees['total_sts'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_Sites . '</td>', $Donnees['total_sts']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_Site . '</td>', $Donnees['total_sts']);
		}

		$Corps_HTML .= '<td><i class="bi-window-stack fg_couleur_1 m-3" style="font-size: 3rem;"></i> ';

		if ($Donnees['total_app'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_Applications . '</td></tr>', $Donnees['total_app']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_Application . '</td></tr>', $Donnees['total_app']);
		}

		$Corps_HTML .= '<tr><td><i class="bi-people fg_couleur_1 m-3" style="font-size: 3rem;"></i> ';

		if ($Donnees['total_ppr'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_Personnes_Cles . '</td>', $Donnees['total_ppr']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_Personne_Cle . '</td>', $Donnees['total_ppr']);
		}

		$Corps_HTML .= '<td><i class="bi-briefcase fg_couleur_1 m-3" style="font-size: 3rem;"></i> ';

		if ($Donnees['total_frn'] > 1) {
			$Corps_HTML .= sprintf($L_Nombre_Fournisseurs . '</td>', $Donnees['total_frn']);
		} else {
			$Corps_HTML .= sprintf($L_Nombre_Fournisseur . '</td>', $Donnees['total_frn']);
		}

		$Corps_HTML .= '</tbody>' .
			'</table>';


		$Liste_Niveaux_Impact = $objMatriceImpacts->rechercherNiveauxImpact($_SESSION['s_cmp_id']);
		$Liste_Niveaux_Impact_Poids = [];
		foreach ($Liste_Niveaux_Impact as $Element) {
			$Liste_Niveaux_Impact_Poids[$Element->nim_poids] = $Element;
		}
		
		$Liste_Echelles_Temps = $objEchellesTemps->rechercherEchellesTemps($_SESSION['s_cmp_id']);
		$Liste_Echelles_Temps_Poids = [];
		foreach ($Liste_Echelles_Temps as $Element) {
			$Liste_Echelles_Temps_Poids[$Element->ete_poids] = $Element;
		}


		// Affichage des Activités à redémarrer par période.
		$Liste_Activites = $objActivites->rechercherSyntheseActivites( $_SESSION['s_cmp_id'], '*', '', 'ete_poids' );

		$Corps_HTML .= '<h1 class="text-center">'.$L_Liste_Activites_Redemarrer_Par_Periode.'</h1>';

		$Corps_HTML .= '<table class="table table-bordered">';
		$tmp_ete_poids = 0;
		foreach($Liste_Activites as $Activite) {
			if ($tmp_ete_poids != $Activite->ete_poids) {
				$tmp_ete_poids = $Activite->ete_poids;
				$Corps_HTML .= '<tr>' .
					'<th colspan="3" style="background-color: silver">' .
					$L_Activites_A_Redemarrer . '&nbsp;<span class="fs-5">' . $Liste_Echelles_Temps_Poids[$Activite->ete_poids]->ete_nom_code . '</span></th>' .
					'</tr>' .
					'<tr>' .
					'<th>' . $L_Activite . '</th>' .
					'<th>' . $L_Entite . '</th>' .
					'<th>' . $L_Niveau_Impact . '</th>' .
					'</tr>';
			}

			$Corps_HTML .= '<tr>' .
				'<td>' . $Activite->act_nom . '</td>' .
				'<td>' . ($Activite->ent_description != '' ? $Activite->ent_description : $Activite->ent_nom) . '</td>' .
				'<td style="color: white; background-color: #'.$Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_couleur.'">' . $Activite->nim_poids . ' - ' . $Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_nom_code . '</td>' .
				'</tr>';
		}
		$Corps_HTML .= '</table>';


		// Affichage des Applications de cette Campagne.
		$Liste_Applications = $objCampagnes->rechercherApplicationsCampagne( $_SESSION['s_cmp_id'] );

		$Corps_HTML .= '<h1 class="text-center">'.$L_Liste_Applications_Redemarrer_Par_Periode.'</h1>';

		$Corps_HTML .= '<table class="table table-bordered">';
		$tmp_poids = 0;
		foreach($Liste_Applications as $Application) {
			if ($tmp_poids != $Application->dmia) {
				$tmp_poids = $Application->dmia;
				$Corps_HTML .= '<tr>' .
					'<th colspan="3" style="background-color: silver">' .
					$L_Applications_A_Redemarrer . '&nbsp;<span class="fs-5">' . $Liste_Echelles_Temps_Poids[$Application->dmia]->ete_nom_code . '</span></th>' .
					'</tr>' .
					'<tr>' .
					'<th>' . $L_Nom_G . '</th>' .
					'<th>' . $L_Activites . '</th>' .
					'<th>' . $L_Palliatif . '</th>' .
					'</tr>';
			}

			$Corps_HTML .= '<tr>' .
				'<td>' . $Application->app_nom . '</td>' .
				'<td>' . $Application->act_nom . '</td>' .
				'<td>';

			$textlines = explode('##', $Application->acap_palliatif);
			if (count($textlines) > 0) {
				$compteur = 0;
				
				foreach($textlines as $line) {
					if ($line != '') {
						$compteur += 1;
						
						if ($compteur > 1) $Corps_HTML .= '<br>';
						
						$Corps_HTML .= $line;
					}
				}
				
				if ($compteur == 0) {
					$Corps_HTML .= $L_Neither;
				}
			} else {
				$Corps_HTML .= $L_Neither;
			}

			$Corps_HTML .= '</td>' .
				'</tr>';
		}
		$Corps_HTML .= '</table>';


		// Affichage des Personnes Clés de cette Campagne.
		$Liste_Personnes = $objCampagnes->rechercherPersonnesClesCampagne( $_SESSION['s_cmp_id'] );

		$Corps_HTML .= '<h1 class="text-center">'.$L_Liste_Personnes_Cles.'</h1>';

		$Corps_HTML .= '<table class="table table-bordered">';
		$tmp_ent_nom = 0;
		foreach($Liste_Personnes as $Personne) {
			if ($tmp_ent_nom != $Personne->ent_nom) {
				$tmp_ent_nom = $Personne->ent_nom;
				$Corps_HTML .= '<tr>' .
					'<th colspan="4" style="background-color: silver">' .
					$Personne->ent_nom . ' (' . $Personne->ent_description . ')</th>' .
					'</tr>' .
					'<tr>' .
					 '<th>' . $L_Prenom . '</th>' .
					 '<th>' . $L_Nom . '</th>' .
					 '<th>' . $L_Activites . '</th>' .
					 '<th>' . $L_Description . '</th>' .
					'</tr>';
			}

			$Corps_HTML .= '<tr>' .
				'<td>' . $Personne->ppr_prenom . '</td>' .
				'<td>' . $Personne->ppr_nom . '</td>' .
				'<td>' . $Personne->act_nom . '</td>' .
				'<td>';

			$textlines = explode('<br>', $Personne->ppac_description);
			$Corps_HTML .= array_shift($textlines);

			foreach($textlines as $line) {
				$Corps_HTML .= '<br>' . $line;
			}

			$Corps_HTML .= '</td>' .
				'</tr>';
		}
		$Corps_HTML .= '</table>';


		// Affichage des Fournisseurs de cette Campagne.
		$Liste_Fournisseurs = $objCampagnes->rechercherFournisseursCampagne( $_SESSION['s_cmp_id'] );

		$Corps_HTML .= '<h1 class="text-center">'.$L_Liste_Fournisseurs_Utiles_Par_Periode.'</h1>';

		$Corps_HTML .= '<table class="table table-bordered">';
		$tmp_ete_poids = 0;
		foreach($Liste_Fournisseurs as $Fournisseur) {
			if ($tmp_ete_poids != $Fournisseur->ete_poids) {
				$tmp_ete_poids = $Fournisseur->ete_poids;
				$Corps_HTML .= '<tr>' .
					'<th colspan="4" style="background-color: silver">' .
					$L_DMIA_Fournisseurs . '&nbsp;' . $Fournisseur->ete_nom_code . '</th>' .
					'</tr>' .
					'<tr>' .
					'<th>' . $L_Nom_G . '</th>' .
					'<th>' . $L_Type . '</th>' .
					'<th>' . $L_Entite . '</th>' .
					'<th>' . $L_Activites . '</th>' .
					'</tr>';
			}

			$Corps_HTML .= '<tr>' .
				'<td>' . $Fournisseur->frn_nom . '</td>' .
				'<td>' . $Fournisseur->tfr_nom_code . '</td>' .
				'<td>' . $Fournisseur->ent_nom . ' (' . $Fournisseur->ent_description . ')</td>' .
				'<td>' . $Fournisseur->act_nom . '</td>' .
				'</tr>';
		}
		$Corps_HTML .= '</table>';

		$Statut = 'success';
	} else {
		$Statut = 'error';
		$Corps_HTML = $L_No_Authorize;
	}

	print( json_encode( array(
		'statut' => $Statut,
		'texteHTML' => $Corps_HTML ) ) );
	
	break;


 case 'AJAX_Synthese_Specifique':
	if ( $Droit_Lecture === TRUE ) {
		$Entite = $objEntites->detaillerEntite($_SESSION['s_ent_id']);
		$Activites = $objActivites->rechercherActivites($_SESSION['s_cmp_id'], $_SESSION['s_ent_id']);
		$Liste_EchellesTemps = $objEchellesTemps->rechercherEchellesTemps($_SESSION['s_cmp_id']);
		$Liste_Niveaux_Impact = $objCampagnes->rechercherNiveauxImpactCampagne( $_SESSION['s_cmp_id'] );
		$Liste_Types_Impact = $objCampagnes->rechercherTypesImpactCampagne( $_SESSION['s_cmp_id'] );
		$Liste_Matrice_Impacts = $objMatriceImpacts->rechercherMatriceImpactsParID( $_SESSION['s_cmp_id'] );
		$Liste_Sites = $objActivites->rechercherSitesCampagne($_SESSION['s_cmp_id']);
		$Informations_Validation = $objCampagnes->informationsValidationEntite($_SESSION['s_cmp_id'], $_SESSION['s_ent_id']);

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

		$Nom_Entite = $Entite->ent_nom;
		if ($Entite->ent_description != '') $Nom_Entite .= ' (' . $Entite->ent_description . ')';

		$Corps_HTML = '<h1 class="text-center">'.$L_BIA_Entite.' : '.$Nom_Entite.'</h1>';

		// ======================================
		// Liste des activités pour cette entité
		$Corps_HTML .= '<h2 style="margin-top: 36px; padding: 6px 9px;" class="bg_couleur_1">' . $L_Liste_Activites . '</h2>' .
			'<table class="table table-bordered">' .
			'<tr>' .
			 '<th width="70%">' . $L_Nom . '</th>' .
			 '<th width="15%">' . $L_Niveau_Impact . '</th>' .
			 '<th width="15%">' . $L_DMIA . '</th>' .
			 '</tr>';

		$Compteur = 0;

		foreach ($Activites as $Activite) {
			$Compteur += 1;

			$Corps_HTML .= '<tr>' .
			//'<td><a href="#ACT_' . $Compteur . '">' . $Activite->act_nom . '</a></td>' .
			'<td>' . $Activite->act_nom . '</td>' .
			'<td class="text-center" style="color: white; background-color: #'.$Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_couleur.'">' . $Activite->nim_poids . ' - ' . $Liste_Niveaux_Impact_Poids[$Activite->nim_poids]->nim_nom_code . '</td>' .
			'<td>' . $Liste_EchellesTemps_Poids[$Activite->ete_poids]->ete_nom_code . '</td>' .
			'</tr>';
		}

		$Corps_HTML .= '</table>';


		$Corps_HTML .= '<table class="table-visu table-bordered mt-3">' .
			'<theader>' .
			'<tr>' .
			'<th class="bg_couleur_3 fs-5" colspan="2">' . $L_Informations_Validation . '</th>' .
			'<tr>' .
			'<th>' . $L_Valideur . '</th>' .
			'<th>' . $L_Date_Validation . '</th>' .
			'</tr>' .
			'</theader>';
		
		if ( $Informations_Validation->cmen_date_validation == NULL ) {
			$Corps_HTML .= '<tbody>' .
				'<tr>' .
				'<td class="text-center" colspan="2"><span class="fs-5 fw-bold fg_couleur_3">' . $Neither_f . '</span></td>' .
				'</tr>' .
				'</tbody>' .
				'</table>';
		} else {
			$Corps_HTML .= '<tbody>' .
				'<tr>' .
				'<td class="fg_couleur_3">' . $Informations_Validation->cvl_nom . ' ' . $Informations_Validation->cvl_prenom . '</td>' .
				'<td class="fg_couleur_3">' . $Informations_Validation->cmen_date_validation . '</td>' .
				'</tr>' .
				'</tbody>' .
				'</table>';
		}


		// ======================================
		// Détail de chaque activités pour cette entité
		$Corps_HTML .= '<h2 style="margin-top: 48px; padding: 6px 9px;" class="bg_couleur_1">' . $L_Detail_Activites . '</h2>';

		$Compteur = 0;

		foreach ($Activites as $Activite) {
			//$Infos['Activite'] = $objActivites->rechercherActivites( $_SESSION['s_cmp_id'], $_SESSION['s_ent_id'], '', $Activite->act_id );
			$Infos['Liste_DMIA'] = $objActivites->recupererDMIA( $_SESSION['s_cmp_id'], $Activite->act_id );
			$Infos['Liste_Personnes_Cles'] = $objActivites->rechercherPersonnesClesAssociesActivite( $Activite->act_id );
			$Infos['Liste_Applications'] = $objActivites->rechercherApplicationsAssocieesActivite( $Activite->act_id );
			$Infos['Liste_Fournisseurs'] = $objActivites->rechercherFournisseursAssociesActivite( $Activite->act_id );

			$Compteur += 1;

			$Corps_HTML .= '<table class="table-visu-cartouche table-bordered">' .
				'<tr>' .
				'<th colspan="2" class="titre-visu-cartouche">' . $Activite->act_nom . '</th>' .
				'</tr>' .
				'<tbody>' .
				'<tr>' .
				 '<td width="30%">' . $L_Responsable_Activite . '</td>' .
				 '<td width="70%">' . $Activite->ppr_nom_resp . ' ' . $Activite->ppr_prenom_resp . '</td>' .
				'</tr>' .
				'<tr>' .
				 '<td>' . $L_Suppleant . '</td>';

			if ($Activite->ppr_nom_supp != '' or $Activite->ppr_prenom_supp != '') {
				$_Nom_Suppleant = $Activite->ppr_nom_supp . ' ' . $Activite->ppr_prenom_supp;
			} else {
				$_Nom_Suppleant = $L_Neither;
			}

			$Corps_HTML .= '<td>' . $_Nom_Suppleant . '</td>' .
				'</tr>' .
				'<tr>' .
				 '<td>' . $L_Activite_Teletravaillable . '</td>';

			if ( $Activite->act_teletravail == 1 ) {
				$_Activite_Teletravail = $L_Yes;
			} else {
				$_Activite_Teletravail = $L_No;
			}

			$Corps_HTML .= '<td>' . $_Activite_Teletravail . '</td>' .
				'</tr>' .
				'<tr>' .
				 '<td>' . $L_Site_Nominal . '</td>' .
				 '<td>' . $Liste_Sites_Id[$Activite->sts_id_nominal]->sts_nom . ' ('.$Liste_Sites_Id[$Activite->sts_id_nominal]->sts_description .')</td>' .
				'</tr>' .
				'<tr>' .
				 '<td>' . $L_Site_Secours . '</td>';

			if ($Activite->sts_id_secours != NULL) {
				$_Nom_Site_Secours = $Liste_Sites_Id[$Activite->sts_id_secours]->sts_nom . ' ('.$Liste_Sites_Id[$Activite->sts_id_secours]->sts_description .')</td>';
			} else {
				$_Nom_Site_Secours = $L_Neither;
			}

			$Corps_HTML .= '<td>' . $_Nom_Site_Secours . '</td>' .
				'</tr>' .
				'<tr>' .
				 '<td>' . $L_Description . '</td>' .
				 '<td>' . $Activite->act_description . '</td>' .
				'</tr>'.
				'</tbody>' .
				'</table>';

			// DMIA de l'activité
			$_Row_1 = '';
			$_Row_2 = '';
			$_Row_3 = '';
			$Nombre_Echelle = count($Liste_EchellesTemps);
			$_largeur_colonne = 100 / $Nombre_Echelle;
			foreach ($Liste_EchellesTemps as $_EchelleTemps) {
				// Gestion de l'entête du tableau
				$_Row_1 .= '<th class="border border-secondary-subtle" ' .
					'width="'.$_largeur_colonne.'%" id="t_ete_id_'.$_EchelleTemps->ete_id.'">'.$_EchelleTemps->ete_nom_code.'</th>';
				$Numero_Echelle = '0';
				$Couleur_Echelle = '';
				$Association_Matrice = '';
				$Poids_Niveau_Impact = '';
				$Nom_Type_Impact = '';

				if (isset($Infos['Liste_DMIA']) and $Infos['Liste_DMIA'] != []) {
					foreach ($Infos['Liste_DMIA'] as $_DetailEchelle) {
						if ($_DetailEchelle->ete_id == $_EchelleTemps->ete_id) {
							$Numero_Echelle = $_DetailEchelle->nim_numero;
							$Couleur_Echelle = 'style="background-color: #'.$_DetailEchelle->nim_couleur.';" ';
							$Association_Matrice = 'data-mim_id="'.$_DetailEchelle->mim_id.'" data-mim_id-old="'.$_DetailEchelle->mim_id.'" ';
							$Poids_Niveau_Impact = 'data-nim_poids="'.$_DetailEchelle->nim_poids.'" ';
							$Nom_Type_Impact = $_DetailEchelle->tim_nom_code;
						}
					}
				}

				// Affichage du niveau retenu.
				$_Row_2 .= '<td class="border border-secondary-subtle cellule-echelle" id="echelle-1-'.$_EchelleTemps->ete_id.'" ' .
				'data-ete_id="'.$_EchelleTemps->ete_id.'" ' .
				'data-cmp_id="'.$_EchelleTemps->cmp_id.'" ' .
				'data-ete_poids="'.$_EchelleTemps->ete_poids.'" ' .
				$Poids_Niveau_Impact .
				$Couleur_Echelle .
				$Association_Matrice .
				'>'.$Numero_Echelle.'</td>';
				// Affichage du type d'impact retenu
				$_Row_3 .= '<td id="echelle-2-'.$_EchelleTemps->ete_id.'">'.$Nom_Type_Impact.'</td>';
				// Affichage du type d'impact retenu
			}

			$Corps_HTML .= '<table class="table-visu table-bordered">' .
				'<thead class="text-center">' .
				'<tr><th colspan="' . $Nombre_Echelle . '" class="bg-gris-normal">' . $L_DMIA . '</th></tr>' .
				'<tr>' . $_Row_1 . '</tr></thead>' .
				'<tbody class="text-center">' .
				'<tr>' . $_Row_2 . '</tr>' .
				'<tr>' . $_Row_3 . '</tr>' .
				'</tbody>' .
				'</table>';


			// Affichage des personnes clés
			$Corps_HTML .= '<table class="table-visu table-bordered">' .
				'<thead>' .
				 '<tr><th colspan="2" class="text-center bg-gris-normal">' . $L_Personnes_Cles . '</th></tr>' .
				'<tr>' .
				 '<th>' . $L_Nom . '</th>' .
				 '<th>' . $L_Prenom . '</th>' .
				'</tr>' .
				'</thead>' .
				'<tbody>';

			if ( $Infos['Liste_Personnes_Cles'] != FALSE ) {
				foreach ($Infos['Liste_Personnes_Cles'] as $Personne) {
					$Corps_HTML .= '<tr>' .
						'<td>' . $Personne->ppr_nom . '</td>' .
						'<td>' . $Personne->ppr_prenom . '</td>' .
						'</tr>';
				}
			} else {
				$Corps_HTML .= '<tr><td colspan="2"class="text-center">' . $L_Neither_f . '</td></tr>';
			}

			$Corps_HTML .= '</tbody>' .
				'</table>';


			// Affichage des interdépendances
			$Corps_HTML .= '<table class="table-visu table-bordered">' .
				'<thead>' .
				'<tr><th colspan="2" class="text-center bg-gris-normal">' . $L_Interdependances . '</th></tr>' .
				'<tr>' .
				'<th>' . $L_Dependances_Internes_Amont . '</th>' .
				'<th>' . $L_Dependances_Internes_Aval . '</th>' .
				'</tr>' .
				'</thead>' .
				'<tbody>';

			if ( $Activite->act_dependances_internes_amont != '' ) {
				$_Dependances_Internes_Amont = str_replace(', ', ',<br>', $Activite->act_dependances_internes_amont);
			} else {
				$_Dependances_Internes_Amont = $L_Neither_f;
			}

			if ( $Activite->act_dependances_internes_aval != '' ) {
				$_Dependances_Internes_Aval = str_replace(', ', ',<br>', $Activite->act_dependances_internes_aval);
			} else {
				$_Dependances_Internes_Aval = $L_Neither_f;
			}
			
			$Corps_HTML .= '<tr>' .
				'<td>' . $_Dependances_Internes_Amont . '</td>' .
				'<td>' . $_Dependances_Internes_Aval . '</td>' .
				'</tr>'.
				'</tbody>' .
				'</table>';


			// Affichage des Applications
			$Corps_HTML .= '<table class="table-visu table-bordered">' .
			'<thead>' .
			'<tr><th colspan="6" class="text-center bg-gris-normal">' . $L_Applications . '</th></tr>' .
			'<tr>' .
			'<th>' . $L_Nom . '</th>' .
			'<th>' . $L_Hebergement . '</th>' .
			'<th>' . $L_DMIA . '</th>' .
			'<th>' . $L_PDMA . '</th>' .
			'<th>' . $L_Donnees . '</th>' .
			'<th>' . $L_Palliatif . '</th>' .
			'</tr>' .
			'</thead>' .
			'<tbody>';
			
			if ( $Infos['Liste_Applications'] != FALSE ) {
				foreach ($Infos['Liste_Applications'] as $Application) {
					$Application->acap_donnees = str_replace("\n", '<br>', $Application->acap_donnees);
					$Application->acap_palliatif = str_replace("\n", '<br>', $Application->acap_palliatif);
					
					$Corps_HTML .= '<tr>' .
						'<td>' . $Application->app_nom . '</td>' .
						'<td>' . $Application->app_hebergement . '</td>' .
						'<td>' . $Application->ete_id_dima . '</td>' .
						'<td>' . $Application->ete_id_pdma . '</td>' .
						'<td>' . $Application->acap_donnees . '</td>' .
						'<td>' . $Application->acap_palliatif . '</td>' .
						'</tr>';
				}
			} else {
				$Corps_HTML .= '<tr><td colspan="6"class="text-center">' . $L_Neither_f . '</td></tr>';
			}
			
			$Corps_HTML .= '</tbody>' .
				'</table>';


			// Affichage des Fournisseurs
			$Corps_HTML .= '<table class="table-visu table-bordered">' .
				'<thead>' .
				'<tr><th colspan="5" class="text-center bg-gris-normal">' . $L_Fournisseurs . '</th></tr>' .
				'<tr>' .
				'<th>' . $L_Nom . '</th>' .
				'<th>' . $L_Type . '</th>' .
				'<th>' . $L_DMIA . '</th>' .
				'<th>' . $L_Consequence_Indisponibilite . '</th>' .
				'<th>' . $L_Palliatif . '</th>' .
				'</tr>' .
				'</thead>' .
				'<tbody>';
			
			if ( $Infos['Liste_Fournisseurs'] != FALSE ) {
				foreach ($Infos['Liste_Fournisseurs'] as $Fournisseur) {
					$Corps_HTML .= '<tr>' .
						'<td>' . $Fournisseur->frn_nom . '</td>' .
						'<td>' . $Fournisseur->tfr_nom_code . '</td>' .
						'<td>' . $Fournisseur->ete_nom_code . '</td>' .
						'<td>' . $Fournisseur->acfr_consequence_indisponibilite . '</td>' .
						'<td>' . (isset($Fournisseur->acfr_palliatif) ? $Fournisseur->acfr_palliatif : '') . '</td>' .
						'</tr>';
				}
			} else {
				$Corps_HTML .= '<tr><td colspan="5"class="text-center">' . $L_Neither . '</td></tr>';
			}
			
			$Corps_HTML .= '</tbody>' .
				'</table>';
		}
		
		print( json_encode( array(
			'statut' => 'success',
			'texteHTML' => $Corps_HTML ) ) );
	}
	
	break;
}


function couperLibelle( $Libelle, $Limite = 38 ) {
	$Taille = mb_strlen( $Libelle );

	if ( $Taille > $Limite ) {
		$Texte = mb_substr( $Libelle, 0, $Limite );
		$Texte = '<span title="' . $Libelle . '">' . $Texte . '&hellip;</span>';
	} else {
		$Texte = $Libelle;
	}

	return $Texte;
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
	if ( $Liste_Entites == [] and $_SESSION['s_ent_id'] != '*' ) {
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