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
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Activites.php' );
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


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'ACT';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'act_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'act_nom', 'titre' => $L_Nom, 'taille' => '5',
	'maximum' => 100, 'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'act_nom', 'type' => 'input',
	'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'dmia', 'titre' => $L_DMIA,
	'affichage' => 'img', 'taille' => '7' );
//$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
//	'boutons' => array( 'dupliquer' => $Droit_Ajouter, 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifiée
switch( $Action ) {
 default:
	$Fichiers_JavaScript[] = 'MatriceImpacts.js';

	$Boutons_Alternatifs = [
		['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search']
	];

	/*$Champ_Recherche['libelle'] = $L_Activites;
	$Champ_Recherche['id'] = 'rech_activites';*/
	$Champ_Recherche = '';

	print( $PageHTML->construireEnteteHTML( $L_Comparateur_DMIA_Activites, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Comparateur_DMIA_Activites, '', $Boutons_Alternatifs, 
			'', '', '', $Champ_Recherche )
	);
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
		'Liste_Sites' => $objActivites->rechercherSitesCampagne( $_SESSION['s_cmp_id'] ),
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
				$Libelles['Liste_Applications'] = $objActivites->rechercherApplicationsActivites( $_POST['act_id'] );
				$Libelles['Liste_Fournisseurs'] = $objActivites->rechercherFournisseursActivite( $_POST['act_id'] );
			} else {
				$Libelles = array( 'statut' => 'error', 'texteMsg' => $L_No_Authorize );
			}
		}
	}

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];

		$Total = 0;
		$Texte_HTML = '';

		try {
			$ListeActivites = $objActivites->listerActivitesUtilisateur( $Trier );

			$_Ligne_1 = '';
			$_Ligne_2 = '';
			$_Ligne_3 = '';

			$Lecture = 0;

			$_act_id = $ListeActivites[0]->act_id;
			$_act_nom = $ListeActivites[0]->sct_nom . ' > ' . $ListeActivites[0]->ent_nom . ($ListeActivites[0]->ent_nom != '' ? '(' . $ListeActivites[0]->ent_description . ')' : '' ) . ' > ' . $ListeActivites[0]->act_nom;
			$Occurrence_Precedente = $ListeActivites[0];
			
			foreach ($ListeActivites as $Occurrence) {
				$Lecture += 1;

				if ( $Occurrence->act_id != $_act_id ) {
					$Total += 1;

					$DMIA = '<table class="table table-bordered">' .
						'<thead class="text-center"><tr>' . $_Ligne_1 . '</tr></thead>' .
						'<tbody class="text-center">' .
						'<tr>' . $_Ligne_2 . '</tr>' .
						'<tr>' . $_Ligne_3 . '</tr>' .
						'</tbody>' .
						'</table>';

					$Occurrence_Precedente->act_nom = $_act_nom;
					$Occurrence_Precedente->dmia = $DMIA;

					$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $_act_id, $Occurrence_Precedente, $Format_Colonnes );

					// -------------------------------------------------------
					// Réinitialise les valeurs avec le changement d'Activité
					$_act_id = $Occurrence->act_id;
					$_act_nom = $Occurrence->sct_nom . ' > ' . $Occurrence->ent_nom . ($Occurrence->ent_nom != '' ? '(' . $Occurrence->ent_description . ')' : '' ) . ' > ' . $Occurrence->act_nom;

					$_Ligne_1 = '<th class="titre-fond-bleu border border-secondary-subtle">' . $Occurrence->ete_nom_code . '</th>';
					$_Ligne_2 = '<td class="border border-secondary-subtle cellule-echelle" ' .
						'style="background-color: #' . $Occurrence->nim_couleur . ';">' .
						$Occurrence->nim_numero . '</td>';
					$_Ligne_3 = '<td style="background-color: silver;">' . $Occurrence->tim_nom_code . '</td>';

					$Occurrence_Precedente = $Occurrence;
				} else {
					// Gestion de l'entête du tableau
					$_Ligne_1 .= '<th class="titre-fond-bleu border border-secondary-subtle">' . $Occurrence->ete_nom_code . '</th>';
	
					// Affichage du niveau retenu.
					$_Ligne_2 .= '<td class="border border-secondary-subtle cellule-echelle" ' .
						'style="background-color: #' . $Occurrence->nim_couleur . ';">' .
						$Occurrence->nim_numero . '</td>';
	
					// Affichage du type d'impact retenu
					$_Ligne_3 .= '<td style="background-color: silver;">' . $Occurrence->tim_nom_code . '</td>';
				}
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
}

?>