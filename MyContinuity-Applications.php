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
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_Fournisseurs_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_Applications_PDO.inc.php' );


// Crée une instance de l'objet HTML.
$objCampagnes = new Campagnes();
$objSocietes = new HBL_Societes();
$objApplications = new Applications();
$objFournisseurs = new Fournisseurs();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'APP';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'app_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'app_nom', 'titre' => $L_Nom, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'app_nom', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'frn_id', 'titre' => $L_Fournisseur, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'frn_nom', 'type' => 'select', 'fonction' => 'listerFournisseurs', 'modifiable' => 'oui', );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'app_hebergement', 'titre' => $L_Hebergement, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'app_hebergement', 'type' => 'input', 'modifiable' => 'oui', );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'app_niveau_service', 'titre' => $L_Niveau_Service, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'app_niveau_service', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'app_description', 'titre' => $L_Description, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'app_description', 'type' => 'textarea', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'sct_id', 'titre' => $L_Specifique, 'taille' => '1',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'sct_id', 'type' => 'select', 'liste' => '0='.$L_No.';1='.$L_Yes, 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '1', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );

$Droit_Ajouter_Fournisseurs = $PageHTML->controlerPermission('MyContinuity-Fournisseurs.php', 'RGH_2');


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Societes = $objSocietes->rechercherSocietes();
	} else {
		$Liste_Societes = $objSocietes->rechercherSocietes('', '', $_SESSION['idn_id'] );
	}

	$Choix_Societe['id'] = 's_sct_id';
	$Choix_Societe['libelle'] = $L_Specifique_A;
	
	if ( $Liste_Societes != '' ) {
		foreach( $Liste_Societes AS $Societe ) {
			$Choix_Societe['options'][] = array('id' => $Societe->sct_id, 'nom' => $Societe->sct_nom );
		}
	}

	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print $PageHTML->construireEnteteHTML( $L_Gestion_Applications, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Applications, '', $Boutons_Alternatifs, '', '', $Choix_Societe );


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
		'L_Non' => $L_No
		);

	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['app_id']) and $_POST['app_id'] != '') {
			$Application = $objApplications->rechercherApplications( 'app_nom', $_POST['app_id'], $_SESSION['s_sct_id'] );
			$Libelles['Application'] = $Application[0];
			$Libelles['Liste_Fournisseurs'] = listerFournisseurs($Application[0]->frn_id);
		} else {
			$Libelles['Liste_Fournisseurs'] = listerFournisseurs();
		}
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


			try {
				$objApplications->majApplication( '', $_POST['app_nom'], $_POST['frn_id'], $_POST['app_hebergement'],
					$_POST['app_niveau_service'], $_POST['app_description'] );

				$Id_Application = $objApplications->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_APPLICATION', 'app_id="' . $Id_Application .
					'", app_nom="' . $_POST[ 'app_nom' ] .
					'", frn_nom="' . $_POST[ 'frn_nom' ] .
					'", app_hebergement="' . $_POST['app_hebergement'] .
					'", app_niveau_service="' . $_POST['app_niveau_service'] .
					'", app_description="' . $_POST['app_description'] . '"' );

				$Valeurs = new stdClass();
				$Valeurs->app_nom = $_POST[ 'app_nom' ];
				$Valeurs->frn_id = $_POST[ 'frn_nom' ];
				$Valeurs->app_hebergement = $_POST['app_hebergement'];
				$Valeurs->app_niveau_service = $_POST['app_niveau_service'];
				$Valeurs->app_description = $_POST['app_description'];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_Application, $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Application_Cree,
					'texte' => $Occurrence,
					'id' => $Id_Application,
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
				$objApplications->majApplicationParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

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
			$ListeApplications = $objApplications->rechercherApplications( $Trier, '', $_SESSION['s_sct_id'] );
			$Total = $objApplications->RowCount;

			$Texte_HTML = '';
			
			foreach ($ListeApplications as $Occurrence) {
				$Occurrence->frn_id = $Occurrence->frn_nom;

				if ( $Occurrence->sct_id == NULL ) {
					$Occurrence->sct_id = $L_No;
				} else {
					$Occurrence->sct_id = $L_Yes;
				}
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->app_id, $Occurrence, $Format_Colonnes );
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
		if ( isset($_POST['app_id']) and isset($_POST['app_nom']) and isset($_POST['app_hebergement'])
			and isset($_POST['app_niveau_service']) and isset($_POST['app_description']) ) {

				$_POST['app_nom'] = $PageHTML->controlerTypeValeur( $_POST['app_nom'], 'ASCII' );
				if ( $_POST['app_nom'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (app_nom)'
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

				$_POST['sct_id'] = $PageHTML->controlerTypeValeur( $_POST['sct_id'], 'BOOLEAN' );
				if ( $_POST['sct_id'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (sct_id)'
					) );

					exit();
				} else {
					if ( $_POST['sct_id'] == 1 ) {
						$_POST['sct_id'] = $_SESSION['s_sct_id'];
					} else {
						$_POST['sct_id'] = NULL;
					}
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
				$objApplications->majApplication( $_POST['app_id'], $_POST['app_nom'], $_POST['frn_id'], $_POST['app_hebergement'],
					$_POST['app_niveau_service'], $_POST['app_description']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_APPLICATION', 'app_id="' . $_POST['app_id'] . '", ' .
					'app_nom="' . $_POST[ 'app_nom' ] . '", app_hebergement="' . $_POST[ 'app_hebergement' ] . '", ' .
					'app_niveau_service="' . $_POST[ 'app_niveau_service' ] . '", app_description="' . $_POST[ 'app_description' ] . '"' );

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

?>