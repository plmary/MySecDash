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
include 'Constants.inc.php';

include DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php';


// Charge les libellés en fonction de la langue sélectionnée.
include HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-Entites.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-PartiesPrenantes.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Campagnes.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Sites.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Applications.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Fournisseurs.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Activites.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Rapports.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-MatriceImpacts.php';


// Charge les classes utiles à cet écran.
include DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_HBL_Identites_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_HBL_Civilites_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_Activites_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_Sites_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_PartiesPrenantes_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_EchellesTemps_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_MatriceImpacts_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_Applications_PDO.inc.php';
include DIR_LIBRAIRIES . '/Class_Fournisseurs_PDO.inc.php';


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
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ent_nom', 'titre' => $L_Entite, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ent_nom', 'modifiable' => 'non' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'act_nom', 'titre' => $L_Nom, 'taille' => '4',
	'maximum' => 100, 'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'act_nom', 'type' => 'input',
	'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'dmia', 'titre' => $L_DMIA,
	'affichage' => 'img', 'taille' => '6' );


// Exécute l'action identifiée
switch( $Action ) {
 default:
	$Liste_Societes = '';
	$Liste_Campagnes = '';

	// Initialise les listes déroulantes : Sociétés, Campagnes et Entités
	try {
		list($Liste_Societes, $Liste_Campagnes) =
		actualiseSocieteCampagneEntite($objSocietes, $objCampagnes);
	} catch( Exception $e ) {
		print('<h1 class="text-urgent">' . $e->getMessage() . '</h1>');
		break;
	}

	$Choix_Campagnes['id'] = 's_cmp_id';
	$Choix_Campagnes['libelle'] = $L_Campagnes;

	if ( $Liste_Campagnes != '' ) {
		foreach( $Liste_Campagnes AS $Campagne ) {
			$Choix_Campagnes['options'][] = array('id' => $Campagne->cmp_id, 'nom' => $Campagne->cmp_date );
		}
	}

	$Fichiers_JavaScript[] = 'MatriceImpacts.js';

	$Boutons_Alternatifs = [
		['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search']
	];

	/*$Champ_Recherche['libelle'] = $L_Activites;
	$Champ_Recherche['id'] = 'rech_activites';*/
	$Champ_Recherche = '';

	print( $PageHTML->construireEnteteHTML( $L_Comparateur_DMIA_Activites, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Comparateur_DMIA_Activites, $Liste_Societes, $Boutons_Alternatifs, 
			$Choix_Campagnes, '', '', $Champ_Recherche )
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
		'L_Titre_Modifier' => $L_Modifier_Niveau_Impact,
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
		'L_Effectifs_En_Nominal' => $L_Effectifs_En_Nominal
	);

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];

		$Total = 0;
		$Texte_HTML = '';

		try {
			$ListeActivites = $objActivites->listerActivitesUtilisateur( $Trier, $_SESSION['s_cmp_id'] );
			$Liste_EchellesTemps = $objEchellesTemps->rechercherEchellesTemps( $_SESSION['s_sct_id'] );

			if ( $ListeActivites != [] ) {
				$_largeur = 100 / count( $Liste_EchellesTemps );

				$_Ligne_1 = '';
				$_Ligne_2 = '';
				$_Ligne_3 = '';

				$Lecture = 0;

				$_act_id = $ListeActivites[0]->act_id;
				$_ent_nom = $ListeActivites[0]->sct_nom . ' > ' . $ListeActivites[0]->ent_nom . ($ListeActivites[0]->ent_nom != '' ? ' (' . $ListeActivites[0]->ent_description . ')' : '' );
				$Occurrence_Precedente = $ListeActivites[0];

				foreach ($ListeActivites as $Occurrence) {
					$Lecture += 1;

					if ( $Occurrence->act_id != $_act_id ) {
						$Total += 1;

						$DMIA = '<table class="table table-bordered">' .
							'<thead class="text-center"><tr>' . $_Ligne_1 . '</tr></thead>' .
							'<tbody class="text-center">' .
							'<tr class="cellule-valeur">' . $_Ligne_2 . '</tr>' .
							'<tr class="cellule-libelle">' . $_Ligne_3 . '</tr>' .
							'</tbody>' .
							'</table>';

						$Occurrence_Precedente->ent_nom = $_ent_nom;
						$Occurrence_Precedente->dmia = $DMIA;

						$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $_act_id, $Occurrence_Precedente, $Format_Colonnes );

						// -------------------------------------------------------
						// Réinitialise les valeurs avec le changement d'Activité
						$_act_id = $Occurrence->act_id;
						$_ent_nom = $Occurrence->sct_nom . ' > ' . $Occurrence->ent_nom . ($Occurrence->ent_nom != '' ? ' (' . $Occurrence->ent_description . ')' : '' ) . ' > ' . $Occurrence->act_nom;

						$_Ligne_1 = '<th class="titre-fond-bleu border border-secondary-subtle">' . $Occurrence->ete_nom_code . '</th>';
						$_Ligne_2 = '<td class="border border-secondary-subtle cellule-echelle" data-act_id="' . $Occurrence->act_id . '" ' .
							'data-ete_id="' . $Occurrence->ete_id . '" data-mim_id="' . $Occurrence->mim_id . '" style="background-color: #' . $Occurrence->nim_couleur . ';">' .
							$Occurrence->nim_numero . '</td>';
						$_Ligne_3 = '<td style="background-color: silver;">' . $Occurrence->tim_nom_code . '</td>';

						$Occurrence_Precedente = $Occurrence;
					} else {
						// Gestion de l'entête du tableau
						$_Ligne_1 .= '<th class="titre-fond-bleu border border-secondary-subtle" width="' . $_largeur . '%">' . $Occurrence->ete_nom_code . '</th>';

						// Affichage du niveau retenu.
						$_Ligne_2 .= '<td class="border border-secondary-subtle cellule-echelle" data-act_id="' . $Occurrence->act_id . '" ' .
							'data-ete_id="' . $Occurrence->ete_id . '" data-mim_id="' . $Occurrence->mim_id . '" style="background-color: #' . $Occurrence->nim_couleur . ';">' .
							$Occurrence->nim_numero . '</td>';

						// Affichage du type d'impact retenu
						$_Ligne_3 .= '<td style="background-color: silver;" data-act_id="' . $Occurrence->act_id . '" data-ete_id="' . $Occurrence->ete_id . '" ' .
							'data-mim_id="' . $Occurrence->mim_id . '">' . $Occurrence->tim_nom_code . '</td>';
					}
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
			list($Liste_Societes, $Liste_Campagnes) =
			actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, 2);
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
			'L_Societe_Sans_Campagne' => $L_Societe_Sans_Campagne,
			'L_Gestion_Campagnes' => $L_Gestion_Campagnes,
			'L_Campagne_Sans_Entite' => $L_Campagne_Sans_Entite,
			'L_Gestion_Entites' => $L_Gestion_Entites,
			'Liste_Campagnes' => $Liste_Campagnes
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
			print json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (cmp_id="' . $_POST['cmp_id'] . '")'.' [' . __LINE__ . ']' ) );

			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );

			exit();
		}

		$_SESSION['s_cmp_id'] = $_POST['cmp_id'];

		try {
			list($Liste_Societes, $Liste_Campagnes) =
			actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, 3);
	} catch ( Exception $e ) {
			$Resultat = array( 'statut' => 'error',
				'texteMsg' => $e->getMessage() );
			echo json_encode( $Resultat );
			break;
		}

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Campagne_Change,
			'sct_id' => $_SESSION['s_sct_id'],
			'cmp_id' => $_SESSION['s_cmp_id']
		);
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires . ' (cmp_id)' );
	}

	echo json_encode( $Resultat );
	
	break;
	
	
 case 'AJAX_Modifier':
	if ( isset( $_POST['act_id'] ) && isset( $_POST['ete_id'] ) && isset( $_POST['mim_id'] ) && isset( $_POST['n_mim_id'] ) ) {
		$_POST['act_id'] = $PageHTML->controlerTypeValeur( $_POST['act_id'], 'NUMERIC' );
		if ( $_POST['act_id'] == -1 ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (act_id)'
			) );
			
			exit();
		}

		$_POST['ete_id'] = $PageHTML->controlerTypeValeur( $_POST['ete_id'], 'NUMERIC' );
		if ( $_POST['ete_id'] == -1 ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (ete_id)'
			) );
			
			exit();
		}

		$_POST['mim_id'] = $PageHTML->controlerTypeValeur( $_POST['mim_id'], 'NUMERIC' );
		if ( $_POST['mim_id'] == -1 ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (mim_id)'
			) );
			
			exit();
		}

		$_POST['n_mim_id'] = $PageHTML->controlerTypeValeur( $_POST['n_mim_id'], 'NUMERIC' );
		if ( $_POST['n_mim_id'] == -1 ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (n_mim_id)'
			) );
			
			exit();
		}

		$objActivites->modifierDMIA($_SESSION['s_cmp_id'], $_POST['act_id'], $_POST['ete_id'], $_POST['mim_id'], $_POST['n_mim_id']);

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Niveau_Impact_Modifie );
	} else {
		$Resultat = array( 'statut' => 'error',
			'texteMsg' => $L_ERR_Champs_Obligatoires );
	}

	echo json_encode( $Resultat );

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
}


function actualiseSocieteCampagneEntite($objSocietes, $objCampagnes, $forcer=0) {
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
	
	//print($_SESSION['s_sct_id'].' - '.$_SESSION['s_cmp_id'].' - '.$_SESSION['s_ent_id'].'<hr>');
	
	return [$Liste_Societes, $Liste_Campagnes];
}

?>