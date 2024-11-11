<?php

/**
* Ce script gère la page d'accueil de l'outil et donne accès à toutes les fonctions 
* disponibles à l'utilisateur connecté.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2024-01-09
*
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
//include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-Connexion.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-Principal.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-MatriceImpacts.php' );
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


$Nombre_Max_Campagnes_Par_Societe = 2;

// Exécute l'action identifiée
switch( $Action ) {
 default:
	print( $PageHTML->construireEnteteHTML( $L_Dashboard, '', 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') );

	if ( array_key_exists( 'notification', $_GET ) ) {
		if ( isset( $_POST[ 'Message'] ) and isset( $_POST[ 'Type_Message' ] ) ) {
			print( $PageHTML->afficherNotification( $_POST[ 'Message'], $_POST[ 'Type_Message' ] ) );
		}
	}


	print( '<div id="titre_ecran" class="container-fluid" data-admin="' . $PageHTML->estAdministrateur() . '">' .
		"<h1 style=\"margin-top: 0;\" class=\"fg_couleur_2\">" . $L_Bienvenue . "&nbsp;(" . $PageHTML->Nom_Outil_Continuity . ")</h1>\n" .
		'<script src="' . URL_LIBRAIRIES . '/js/MySecDash-Principal.js"></script>' .
		'<ul class="nav nav-tabs">' .
		'<li class="nav-item"><a id="onglet-administrateur" class="nav-link active" href="#">' . $L_Gestion . '</a></li>' .
		'<li class="nav-item"><a id="onglet-utilisateur" class="nav-link" href="#">' . $L_Visualisation . '</a></li>' .
		'</ul>' .
		'</div>' .
		'<div id="corps_tableau" class="container-fluid" style="padding: 19px 0; margin-top: 9px;">' .
		"</div>\n".
		$PageHTML->construireFooter() .
		$PageHTML->construirePiedHTML() );

	break;


 case 'AJAX_Tableau_Bord_Admin':
	$texteHTMLAdmin = '';
	$texteHTMLGestion = '';
	$texteHTMLReferentiel = '';
	
	try {
		$Class = 'bg-vert_normal';

		$texteHTMLAdmin .= "<div class=\"tableau_synthese\">" .
			"<p class=\"titre\">" . $L_Ecrans_Administration . "</p>\n" .
			"<div class=\"corps\">\n";

		$texteHTMLGestion .= "<div class=\"tableau_synthese\">" .
			"<p class=\"titre\">" . $L_Ecrans_Gestion . "</p>\n" .
			"<div class=\"corps\">\n";
		
		$texteHTMLReferentiel .= "<div class=\"tableau_synthese\">" .
			"<p class=\"titre\">" . $L_Ecrans_Referentiel . "</p>\n" .
			"<div class=\"corps\">\n";
		
		
		if ( isset( $Permissions['MySecDash-Societes.php'] ) ) {
			$texteHTMLAdmin .= "<a href=\"MySecDash-Societes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Societes, 39 ) . "</span>";
				
				$texteHTMLAdmin .= "</a>";
		}
		
		
		if ( isset( $Permissions['MySecDash-Entites.php'] ) ) {
			$texteHTMLAdmin .= "<a href=\"MySecDash-Entites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Entites, 39 ) . "</span>";
				
				$texteHTMLAdmin .= "</a>";
		}


		if ( isset( $Permissions['MyContinuity-Campagnes.php'] ) ) {
			$texteHTMLGestion .= "<a href=\"MyContinuity-Campagnes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Campagnes, 39 ) . "</span>";
				
				$texteHTMLGestion .= "</a>";
		}
		
		
		if ( isset( $Permissions['MyContinuity-Activites.php'] ) ) {
			$texteHTMLGestion .= "<a href=\"MyContinuity-Activites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Activites, 39 ) . "</span>";
				
				$texteHTMLGestion .= "</a>";
		}
		
		
		if ( isset( $Permissions['MyContinuity-ComparateurDMIAActivites.php'] ) ) {
			$texteHTMLGestion .= "<a href=\"MyContinuity-ComparateurDMIAActivites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_M_Comparateur_DMIA_Activites, 39 ) . "</span>";
				
				$texteHTMLGestion .= "</a>";
		}
		
		
		if ( isset( $Permissions['MyContinuity-ValiderEntites.php'] ) ) {
			$texteHTMLGestion .= "<a href=\"MyContinuity-ValiderEntites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Valider_BIA, 39 ) . "</span>";
				
				$texteHTMLGestion .= "</a>";
		}
		
		
		if ( isset( $Permissions['MyContinuity-Sites.php'] ) ) {
			$texteHTMLReferentiel .= "<a href=\"MyContinuity-Sites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Sites, 39 ) . "</span>";
				
				$texteHTMLReferentiel .= "</a>";
		}
		
		
		if ( isset( $Permissions['MyContinuity-Applications.php'] ) ) {
			$texteHTMLReferentiel .= "<a href=\"MyContinuity-Applications.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Applications, 39 ) . "</span>";
				
				$texteHTMLReferentiel .= "</a>";
		}
		
		
		if ( isset( $Permissions['MyContinuity-Fournisseurs.php'] ) ) {
			$texteHTMLReferentiel .= "<a href=\"MyContinuity-Fournisseurs.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Fournisseurs, 39 ) . "</span>";
				
				$texteHTMLReferentiel .= "</a>";
		}
		
		
		if ( isset( $Permissions['MyContinuity-PartiesPrenantes.php'] ) ) {
			$texteHTMLReferentiel .= "<a href=\"MyContinuity-PartiesPrenantes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Parties_Prenantes, 39 ) . "</span>";
				
				$texteHTMLReferentiel .= "</a>";
		}
		
		
		if ( isset( $Permissions['MyContinuity-EchellesTemps.php'] ) ) {
			$texteHTMLReferentiel .= "<a href=\"MyContinuity-EchellesTemps.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Echelles_Temps, 39 ) . "</span>";
				
				$texteHTMLReferentiel .= "</a>";
		}
		

		if ( isset( $Permissions['MyContinuity-MatriceImpacts.php'] ) ) {
			$texteHTMLReferentiel .= "<a href=\"MyContinuity-MatriceImpacts.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
				couperLibelle( $L_Gestion_Matrice_Impacts, 39 ) . "</span>";
				
				$texteHTMLReferentiel .= "</a>";
		}


		$texteHTMLAdmin .=  "</div>" .
			"</div>";
		
		$texteHTMLGestion .=  "</div>" .
			"</div>";
		
		$texteHTMLReferentiel .=  "</div>" .
			"</div>";

		print( json_encode( array( 'statut' => 'success', 'texteHTML' => $texteHTMLAdmin . $texteHTMLGestion . $texteHTMLReferentiel ) ) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );
	}
	exit();


 case 'AJAX_Tableau_Bord_Utilisateur':
	$Corps_HTML = '';

	foreach ( $objSocietes->rechercherSocietes() as $Societe ) {
		$Corps_HTML .= '<div class="tableau_synthese">' .
			'<p class="titre fs-5">' . $L_Societe . ' : ' . $Societe->sct_nom . '</p>' .
			'<div class="corps">';

		$Nombre_Campagne_Par_Societe = 0;

		foreach ( $objCampagnes->rechercherCampagnes( $Societe->sct_id, 'cmp_date-desc' ) as $Campagne ) {
			$Nombre_Campagne_Par_Societe += 1;
			
			if ( $Nombre_Campagne_Par_Societe > $Nombre_Max_Campagnes_Par_Societe ) break;

			$Corps_HTML .= '<p class="fw-bold fs-4 mt-3">' .
				$L_Campagne . ' : ' . $Campagne->cmp_date .
				'</p> <!-- .fw-bold -->';
				$Donnees = $objCampagnes->syntheseCampagne( $Campagne->cmp_id, $Societe->sct_id );

			$Nombre_BIA_A_Faire = $Donnees['total_bia'] - ( $Donnees['total_bia_valides'] + $Donnees['total_bia_en_cours'] );
	
			// Construit le corps de l'écran.
			$Corps_HTML .= '<table class="table table-bordered">' .
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
		}
		$Corps_HTML .= '</div> <!-- .corps -->' .
			'</div> <!-- .tableau_synthese -->';
	}

	print( json_encode( array( 'statut' => 'success', 'texteHTML' => $Corps_HTML ) ) );

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

?>