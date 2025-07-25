<?php

/**
* Ce script gère les Matrices des Impacts.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-01-20
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );


// Charge les informations et droits de base d'un écran.
include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Societes.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_MyContinuity-Campagnes.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

include( DIR_LIBRAIRIES . '/Class_Campagnes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include( DIR_LIBRAIRIES . '/Class_MatriceImpacts_PDO.inc.php' );


// Crée l'instance de l'objet Entites.
$objCampagnes = new Campagnes();
$objSocietes = new HBL_Societes();
$objMatriceImpacts = new MatriceImpacts();


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( ! isset( $_SESSION['s_cmp_id'] ) ) {
		$_SESSION['s_cmp_id'] = '';
	}

	$Liste_Societes = $objSocietes->rechercherSocietes();
	if ( isset($_SESSION['s_sct_id']) ) {
		$sct_id = $_SESSION['s_sct_id'];
	} else {
		$_SESSION['s_sct_id'] = $sct_id = $Liste_Societes[0]->sct_id;
	}

	$Liste_Campagnes = $objCampagnes->rechercherCampagnes($sct_id, 'cmp_date-desc');
	if ( $Liste_Campagnes != '' && $Liste_Campagnes != [] ) {
		if ( isset($_SESSION['s_cmp_id']) && $_SESSION['s_cmp_id'] != '---' ) {
			$cmp_id = $_SESSION['s_cmp_id'];
		} else {
			$_SESSION['s_cmp_id'] = $cmp_id = $Liste_Campagnes[0]->cmp_id;
		}
	}

	$Choix_Campagnes['id'] = 's_cmp_id';
	$Choix_Campagnes['libelle'] = $L_Campagnes;

	foreach( $Liste_Campagnes AS $Campagne ) {
		$Choix_Campagnes['options'][] = array('id' => $Campagne->cmp_id, 'nom' => $Campagne->cmp_date );
	}

	$Boutons_Alternatifs = '';
	if ( $Droit_Ajouter === TRUE and $Droit_Supprimer === TRUE ) {
		$Boutons_Alternatifs = [
			['class'=>'btn-initialiser', 'libelle'=>$L_Initialiser, 'glyph'=>'magic']
		];
	}

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Matrice_Impacts, $Fichiers_JavaScript, '3' ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Matrice_Impacts, $Liste_Societes, $Boutons_Alternatifs,
		$Choix_Campagnes ) .
		'<div id="corps_ecran"></div>' .
		$PageHTML->construireFooter( TRUE ) .
		$PageHTML->construirePiedHTML() );

	break;


 /* ========================================================================
 ** Réponses aux appels AJAX
 */

 case 'AJAX_Libeller':
	$Libelles = array(
		'statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'L_Titre_Ajouter' => $L_Ajouter_Campagne,
		'L_Titre_Modifier' => $L_Modifier_Campagne,
		'L_Titre_Supprimer' => $L_Supprimer_Campagne,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modifier,
		'L_Supprimer' => $L_Supprimer,
		'L_Libelle' => $L_Label,
		'L_Nom' => $L_Nom,
		'L_Description' => $L_Description,
		'L_Administrateur' => $L_Administrateur,
		'is_super_admin' => $_SESSION['idn_super_admin'],
		'L_Niveaux_Appreciation' => $L_Niveaux_Appreciation,
		'L_Types_Impact' => $L_Types_Impact,
		'L_Matrice_Impacts' => $L_Matrice_Impacts,
		'L_Entites' => $L_Entites,
		'L_Echelles_Temps' => $L_Echelles_Temps,
		'L_Applications' => $L_Applications,
		'L_Fournisseurs' => $L_Fournisseurs,
		'L_Date' => $L_Date,
		'L_Societe_Sans_Campagne' => $L_Societe_Sans_Campagne,
		'L_Gestion_Campagnes' => $L_Gestion_Campagnes,
		'L_Type' => $L_Type, 
		'L_Niveau' => $L_Niveau,
		'L_Ajouter_Type_Impact' => $L_Ajouter_Type_Impact,
		'L_Modifier_Type_Impact' => $L_Modifier_Type_Impact,
		'L_Supprimer_Type_Impact' => $L_Supprimer_Type_Impact,
		'L_Ajouter_Niveau_Impact' => $L_Ajouter_Niveau_Impact,
		'L_Modifier_Niveau_Impact' => $L_Modifier_Niveau_Impact,
		'L_Supprimer_Niveau_Impact' => $L_Supprimer_Niveau_Impact,
		'L_Ajouter_Description_Impact' => $L_Ajouter_Description_Impact,
		'L_Modifier_Description_Impact' => $L_Modifier_Description_Impact,
		'L_Supprimer_Description_Impact' => $L_Supprimer_Description_Impact,
		'L_Poids' => $L_Poids,
		'L_Couleur' => $L_Couleur,
		'L_Confirmer_Suppression_Niveau_Impact' => $L_Confirmer_Suppression_Niveau_Impact,
		'L_Confirmer_Suppression_Type_Impact' => $L_Confirmer_Suppression_Type_Impact,
		'L_Description' => $L_Description,
		'cmp_id' => $_SESSION['s_cmp_id'],
		'Droit_Ajouter' => $Droit_Ajouter,
		'Droit_Modifier' => $Droit_Modifier,
		'Droit_Supprimer' => $Droit_Supprimer
		);

	if ( $Droit_Lecture === TRUE ) {
		if ( isset( $_SESSION['s_cmp_id'] ) ) {
			if ( $_SESSION['s_cmp_id'] != '' ) {
				$Libelles['objCampagne'] = $objCampagnes->detaillerCampagne( $_SESSION['s_cmp_id'] );
				$Libelles['Liste_Niveaux_Impact'] = $objCampagnes->rechercherNiveauxImpactCampagne( $_SESSION['s_cmp_id'] );
				$Libelles['Liste_Types_Impact'] = $objCampagnes->rechercherTypesImpactCampagne( $_SESSION['s_cmp_id'] );
				$Libelles['Liste_Matrice_Impacts'] = $objMatriceImpacts->rechercherMatriceImpactsParID( $_SESSION['s_cmp_id'] );
				$Libelles['Liste_Entites'] = $objCampagnes->rechercherEntitesAssocieesCampagne( $_SESSION['s_sct_id'], $_SESSION['s_cmp_id'] );
				$Libelles['Liste_Echelle_Temps'] = $objCampagnes->rechercherEchelleTempsCampagne( $_SESSION['s_cmp_id'] );

				$Liste_Campagnes = $objCampagnes->rechercherCampagnes($_SESSION['s_sct_id']);
				$Libelles['Liste_Campagnes'] = $Liste_Campagnes;
			}
		}

		if ( isset( $_POST['nim_id'] ) ) {
			if ( $_POST['nim_id'] != '' ) {
				$Libelles['nim'] = $objMatriceImpacts->rechercherNiveauxImpact( $_SESSION['s_cmp_id'], $_POST['nim_id'] );
			}
		}

		if ( isset( $_POST['tim_id'] ) ) {
			if ( $_POST['tim_id'] != '' ) {
				$Libelles['tim'] = $objMatriceImpacts->rechercherTypesImpact( $_SESSION['s_cmp_id'], $_POST['tim_id'] );
			}
		}

		if ( isset( $_GET['Charger_Cellule'] ) ) {
			if ( $_POST['tim_id'] != '' ) {
				$Libelles['mim'] = $objMatriceImpacts->rechercherMatriceImpacts( $_SESSION['s_cmp_id'], $_POST['nim_id'], $_POST['tim_id'] );
			}
		}
	}

	print( json_encode( $Libelles ) );

	exit();


 case 'AJAX_Ajouter_Niveau':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['nim_poids']) && isset($_POST['nim_numero'])
			&& isset($_POST['nim_nom_code']) && isset($_POST['nim_couleur']) ) {

			$_POST['nim_poids'] = $PageHTML->controlerTypeValeur( $_POST['nim_poids'], 'NUMERIC' );
			if ( $_POST['nim_poids'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_poids)'
				) );
				
				exit();
			}

			$_POST['nim_numero'] = $PageHTML->controlerTypeValeur( $_POST['nim_numero'], 'NUMERIC' );
			if ( $_POST['nim_numero'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_numero)'
				) );
				
				exit();
			}

			$_POST['nim_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['nim_nom_code'], 'ASCII' );
			if ( $_POST['nim_nom_code'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_nom_code)'
				) );
				
				exit();
			}

			$_POST['nim_couleur'] = $PageHTML->controlerTypeValeur( $_POST['nim_couleur'], 'ALPHA-NUMERIC' );
			if ( $_POST['nim_couleur'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_couleur)'
				) );
				
				exit();
			}
			

			try {
				$objMatriceImpacts->MaJNiveauImpact( $_SESSION['s_cmp_id'], '', $_POST['nim_poids'],
					$_POST['nim_numero'], $_POST['nim_nom_code'],
					$_POST['nim_couleur'] );
				$nim_id= $objMatriceImpacts->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_NIVEAU_IMPACT', 'cmp_id="' . $_SESSION['s_cmp_id'] .
					'", nim_id="' . $nim_id .
					'", nim_poids="' . $_POST[ 'nim_poids' ] .
					'", nim_numero="' . $_POST[ 'nim_numero' ] .
					'", nim_nom_code="' . $_POST[ 'nim_nom_code' ] .
					'", nim_couleur="' . $_POST[ 'nim_couleur' ] . '"' );

				$Liste_Types_Impact = $objMatriceImpacts->rechercherTypesImpact( $_SESSION['s_cmp_id'] );
				
				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Niveau_Impact_Cree,
					'nim_id' => $nim_id,
					'Liste_Types_Impact' => $Liste_Types_Impact,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Niveau_Impact;
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


 case 'AJAX_Modifier_Niveau':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['nim_id']) && isset($_POST['nim_poids'])
			&& isset($_POST['nim_numero']) && isset($_POST['nim_nom_code'])
			&& isset($_POST['nim_couleur'])) {

			$_POST['nim_id'] = $PageHTML->controlerTypeValeur( $_POST['nim_id'], 'NUMERIC' );
			if ( $_POST['nim_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_id)'
				) );

				exit();
			}

			$_POST['nim_poids'] = $PageHTML->controlerTypeValeur( $_POST['nim_poids'], 'NUMERIC' );
			if ( $_POST['nim_poids'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_poids)'
				) );

				exit();
			}

			$_POST['nim_numero'] = $PageHTML->controlerTypeValeur( $_POST['nim_numero'], 'NUMERIC' );
			if ( $_POST['nim_numero'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_numero)'
				) );
				
				exit();
			}

			$_POST['nim_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['nim_nom_code'], 'ASCII' );
			if ( $_POST['nim_nom_code'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_nom_code)'
				) );
				
				exit();
			}

			$_POST['nim_couleur'] = $PageHTML->controlerTypeValeur( $_POST['nim_couleur'], 'ALPHA-NUMERIC' );
			if ( $_POST['nim_couleur'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_couleur)'
				) );
				
				exit();
			}

			try {
				$objMatriceImpacts->MaJNiveauImpact( $_SESSION['s_cmp_id'], $_POST['nim_id'], $_POST[ 'nim_poids' ],
					$_POST[ 'nim_numero' ], $_POST[ 'nim_nom_code' ], $_POST[ 'nim_couleur' ] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_NIVEAU_IMPACT', 'nim_id="' . $_POST['nim_id'] .
					'", nim_poids="' . $_POST[ 'nim_poids' ] .
					'", nim_numero="' . $_POST[ 'nim_numero' ] .
					'", nim_nom_code="' . $_POST[ 'nim_nom_code' ] .
					'", nim_couleur="' . $_POST[ 'nim_couleur' ] . '"');
				
				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Niveau_Impact_Modifie,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();
				
				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Niveau_Impact;
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


 case 'AJAX_Supprimer_Niveau':
	if ( $Droit_Supprimer === TRUE ) {
		if ( isset($_POST['nim_id']) ) {
			try  {
				$objMatriceImpacts->supprimerNiveauImpact( $_POST['nim_id'] );

				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_NIVEAU_IMPACT', 'nim_id="' . $_POST['nim_id'] . '", libelle="' . $_POST[ 'nim_libelle' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Niveau_Impact_Supprime,
					'libelle_limitation' => $L_Limitation_Licence
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


 case 'AJAX_Ajouter_Type':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['tim_poids']) && isset($_POST['tim_nom_code']) ) {

				$_POST['tim_poids'] = $PageHTML->controlerTypeValeur( $_POST['tim_poids'], 'NUMERIC' );
				if ( $_POST['tim_poids'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (tim_poids)'
					) );

					exit();
				}

				$_POST['tim_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['tim_nom_code'], 'ASCII' );
				if ( $_POST['tim_nom_code'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (tim_nom_code)'
					) );

					exit();
				}


				try {
					$objMatriceImpacts->MaJTypeImpact( $_SESSION['s_cmp_id'], '', $_POST['tim_poids'], $_POST['tim_nom_code'] );
					$tim_id= $objMatriceImpacts->LastInsertId;

					$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_TYPE_IMPACT', 'cmp_id="' . $_SESSION['s_cmp_id'] . '", tim_id="' . $tim_id .
						'", tim_nom_code="' . $_POST[ 'tim_nom_code' ] . '"' );

					$Liste_Niveaux_Impact = $objMatriceImpacts->rechercherNiveauxImpact( $_SESSION['s_cmp_id'] );

					$Resultat = array( 'statut' => 'success',
						'texteMsg' => $L_Niveau_Impact_Cree,
						'tim_id' => $tim_id,
						'Liste_Niveaux_Impact' => $Liste_Niveaux_Impact,
						'droit_modifier' => $Droit_Modifier,
						'droit_supprimer' => $Droit_Supprimer,
						'L_Administrateur' => $L_Administrateur
					);

				} catch (Exception $e) {
					$Statut = 'error';
					$Message = $e->getMessage();

					if ( $e->getCode() == 23505 ) { // Gestion des doublons
						$Message = $L_ERR_DUPL_Type_Impact;
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


 case 'AJAX_Modifier_Type':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['tim_id']) && isset($_POST['tim_poids'])
			&& isset($_POST['tim_nom_code']) ) {

			$_POST['tim_id'] = $PageHTML->controlerTypeValeur( $_POST['tim_id'], 'NUMERIC' );
			if ( $_POST['tim_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tim_id)'
				) );

				exit();
			}

			$_POST['tim_poids'] = $PageHTML->controlerTypeValeur( $_POST['tim_poids'], 'NUMERIC' );
			if ( $_POST['tim_poids'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tim_poids)'
				) );

				exit();
			}

			$_POST['tim_nom_code'] = $PageHTML->controlerTypeValeur( $_POST['tim_nom_code'], 'ASCII' );
			if ( $_POST['tim_nom_code'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tim_nom_code)'
				) );

				exit();
			}

			try {
				$objMatriceImpacts->MaJTypeImpact( $_SESSION['s_cmp_id'], $_POST['tim_id'], $_POST[ 'tim_poids' ],
					$_POST[ 'tim_nom_code' ] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_TYPE_IMPACT', 'cmp_id="' . $_SESSION['s_cmp_id'] . '", tim_id="' . $_POST['tim_id'] .
					'", tim_poids="' . $_POST[ 'tim_poids' ] .
					'", tim_nom_code="' . $_POST[ 'tim_nom_code' ] . '"');

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Type_Impact_Modifie,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Type_Impact;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
				);
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			);
		}

		echo json_encode( $Resultat );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Supprimer_Type':
	if ( $Droit_Supprimer === TRUE ) {
		if ( isset($_POST['tim_id']) ) {
			try  {
				$objMatriceImpacts->supprimerTypeImpact( $_POST['tim_id'] );

				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_TYPE_IMPACT', 'tim_id="' . $_POST['tim_id'] . '", libelle="' . $_POST[ 'tim_libelle' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Type_Impact_Supprime,
					'libelle_limitation' => $L_Limitation_Licence
				);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $e->getMessage() );
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			);
		}
		
		echo json_encode( $Resultat );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Ajouter_Description':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['tim_id']) && isset($_POST['nim_id']) && isset($_POST['mim_description']) ) {
			
			$_POST['tim_id'] = $PageHTML->controlerTypeValeur( $_POST['tim_id'], 'NUMERIC' );
			if ( $_POST['tim_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (tim_id)'
				) );
				
				exit();
			}

			$_POST['nim_id'] = $PageHTML->controlerTypeValeur( $_POST['nim_id'], 'NUMERIC' );
			if ( $_POST['nim_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (nim_id)'
				) );
				
				exit();
			}
			
			$_POST['mim_description'] = $PageHTML->controlerTypeValeur( $_POST['mim_description'], 'ASCII' );
			if ( $_POST['mim_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (mim_description)'
				) );
				
				exit();
			}

			if ( substr($_POST['mim_description'], 0, 3) == '<p>' ) {
				$_POST['mim_description'] = substr($_POST['mim_description'], 3);
			}
			
			if ( substr($_POST['mim_description'], -4) == '</p>' ) {
				$_POST['mim_description'] = substr($_POST['mim_description'], 0, (strlen($_POST['mim_description']) - 4));
			}


			try {
				$objMatriceImpacts->MaJDescriptionImpact( $_SESSION['s_cmp_id'], '', $_POST['nim_id'], $_POST['tim_id'], $_POST['mim_description'] );
				$mim_id= $objMatriceImpacts->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_DESCRIPTION_IMPACT', 'cmp_id="' . $_SESSION['s_cmp_id'] .
					'", mim_id="' . $mim_id .
					'", nim_id="' . $_POST[ 'nim_id' ] .
					'", tim_id="' . $_POST[ 'tim_id' ] .
					'", mim_description="' . $_POST[ 'mim_description' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Description_Impact_Creee,
					'mim_id' => $mim_id,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Type_Impact;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
				);
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			);
		}
		
		echo json_encode( $Resultat );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Modifier_Description':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['mim_id']) && isset($_POST['mim_description']) ) {

			$_POST['mim_id'] = $PageHTML->controlerTypeValeur( $_POST['mim_id'], 'NUMERIC' );
			if ( $_POST['mim_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (mim_id)'
				) );
				
				exit();
			}

			$_POST['mim_description'] = $PageHTML->controlerTypeValeur( $_POST['mim_description'], 'ASCII' );
			if ( $_POST['mim_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (mim_description)'
				) );

				exit();
			}

			if ( substr($_POST['mim_description'], 0, 3) == '<p>' ) {
				$_POST['mim_description'] = substr($_POST['mim_description'], 3);
			}

			if ( substr($_POST['mim_description'], -4) == '</p>' ) {
				$_POST['mim_description'] = substr($_POST['mim_description'], 0, (strlen($_POST['mim_description']) - 4));
			}


			try {
				$objMatriceImpacts->MaJDescriptionImpact($_SESSION['s_cmp_id'], $_POST['mim_id'], '', '', $_POST['mim_description'] );
				
				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_DESCRIPTION_IMPACT', 'mim_id="' . $_POST['mim_id'] .
					'", mim_description="' . $_POST[ 'mim_description' ] . '"' );
				
				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Description_Impact_Modifiee,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer,
					'L_Administrateur' => $L_Administrateur
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Type_Impact;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
				);
			}
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
			);
		}

		echo json_encode( $Resultat );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Supprimer_Description':
	if ( $Droit_Supprimer === TRUE ) {
		if ( isset($_POST['mim_id']) ) {
			try  {
				$objMatriceImpacts->supprimerDescriptionImpact( $_POST['mim_id'] );
				
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_TYPE_IMPACT', 'mim_id="' . $_POST['mim_id'] . '", libelle="' . $_POST[ 'mim_libelle' ] . '"' );
				
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Type_Description_Supprimee,
					'libelle_limitation' => $L_Limitation_Licence
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
			actualiseSocieteCampagne($objSocietes, $objCampagnes, 2);
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
			print( json_encode( array( 'Statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (cmp_id="' . $_POST['cmp_id'] . '")'.' [' . __LINE__ . ']' ) ) );

			$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (ent_id="' . $_POST['ent_id'] . '")'.' [' . __LINE__ . ']' );

			exit();
		}

		$_SESSION['s_cmp_id'] = $_POST['cmp_id'];

		try {
			list($Liste_Societes, $Liste_Campagnes) =
			actualiseSocieteCampagne($objSocietes, $objCampagnes, 3);
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
	
	
 case 'AJAX_Verifier_Avant_Initialisation':
	if ( $Droit_Lecture === TRUE && isset($_SESSION['s_cmp_id']) && $_SESSION['s_cmp_id'] != '' ) {
		$Resultat = $objMatriceImpacts->controlerSiCampagneAMatriceImpacts( $_SESSION['s_cmp_id'] );
		
		if ( $Resultat[0] === TRUE ) {
			$Message = sprintf( $L_Confirmer_Reinitialiser_Matrice_Impacts, $Resultat[1] );
			$Titre = $L_Reinitialiser_Matrice_Impacts_Campagne;
			$Bouton = $L_Reinitialiser;
		} elseif ( $Resultat[0] === FALSE ) {
			$Message = sprintf( $L_Confirmer_Initialiser_Matrice_Impacts_Defaut, $Resultat[1] );
			$Titre = $L_Initialiser_Matrice_Impacts_Campagne;
			$Bouton = $L_Initialiser;
		} else {
			$Message = $Resultat[1];
		}
		
		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $Message,
			'L_Titre' => $Titre,
			'L_Bouton' => $Bouton,
			'L_Fermer' => $L_Fermer
		);
		
		echo json_encode( $Resultat );
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize,
			'L_Titre' => $L_Error,
			'L_Bouton' => '',
			'L_Fermer' => $L_Fermer
		);
		
		echo json_encode( $Resultat );
		exit();
	}
	
	break;


 case 'AJAX_Initialiser_Matrice_Impacts':
	if ( $Droit_Ajouter === TRUE && $Droit_Supprimer === TRUE ) {
		$cmp_id = $_SESSION['s_cmp_id'];

		// Mise en place des Types d'Impact par défaut
		$objMatriceImpacts->MaJTypeImpact($cmp_id, '', 1, 'Financier');
		$TypesImpact[1] = $objMatriceImpacts->LastInsertId;
	
		$objMatriceImpacts->MaJTypeImpact($cmp_id, '', 2, 'Organisationnel');
		$TypesImpact[2] = $objMatriceImpacts->LastInsertId;
	
		$objMatriceImpacts->MaJTypeImpact($cmp_id, '', 3, 'Juridique / Réglementaire');
		$TypesImpact[3] = $objMatriceImpacts->LastInsertId;
	
		$objMatriceImpacts->MaJTypeImpact($cmp_id, '', 4, 'Image de marque');
		$TypesImpact[4] = $objMatriceImpacts->LastInsertId;


		// Mise en place des Niveaux d'Impact par défaut
		$objMatriceImpacts->MaJNiveauImpact($cmp_id, '', 1, 1, 'Faible', '2ecc71');
		$NiveauxImpact[1] = $objMatriceImpacts->LastInsertId;
		$objMatriceImpacts->MaJNiveauImpact($cmp_id, '', 2, 2, 'Notable', 'f1c40f');
		$NiveauxImpact[2] = $objMatriceImpacts->LastInsertId;
		$objMatriceImpacts->MaJNiveauImpact($cmp_id, '', 3, 3, 'Grave', 'e67e22');
		$NiveauxImpact[3] = $objMatriceImpacts->LastInsertId;
		$objMatriceImpacts->MaJNiveauImpact($cmp_id, '', 4, 4, 'Vitale', 'c0392b');
		$NiveauxImpact[4] = $objMatriceImpacts->LastInsertId;


		// Mise en place de la Grille d'Impact par défaut
		// Mise à jour de la colonne "Organisationnel"
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[1], $TypesImpact[1],
			'<p>- Perte inférieure ou égale à 5% de CA</p>
			<p>Les impacts financiers sont faibles et pourront être facilement rattrapés/compensés ? Oui</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[2], $TypesImpact[1],
			'<p>- Perte 5% et 10% de CA</p>
			<p>Les impacts financier sont significatifs mais n\'entraîneront pas de conséquences sur le long terme ? Oui</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[3], $TypesImpact[1],
			'<p>- Perte 10% et 20% de CA</p>
			<p>Les impacts financier sont importants et des conséquences sur le long terme sont à anticiper ? Oui</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[4], $TypesImpact[1],
			'<p>- Perte supérieure à 30% de CA</p>
			<p>Les impacts financiers peuvent remettre en cause la pérennité de l\'entité ? Oui</p>');

		// Mise à jour de la colonne "Financier"
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[1], $TypesImpact[2],
			'<p>- Faibles nuisances à l\'activité sans impact sur tiers ( clients, financeurs, prestataires, fournisseurs…) ni sur les autres service de l\'Entreprise</p>
			<p>L\'incident peut-il provoquer l\'arrêt du service ? Non</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[2], $TypesImpact[2],
			'<p>- Nuisances organisationnelles internes à l\'activité "Titre" entraînant la perturbation de service pour une ou plusieurs catégories de tiers (clients, financeurs, prestataires, fournisseurs…) mais n\'impactant pas d\'autres services de l\'Entreprise.</p>
			<p>L\'incident peut-il provoquer l\'arrêt du service ? Oui</p>
			<p>Peut-il impacter d\'autres services ? Non</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[3], $TypesImpact[2],
			'<p>- Nuisances organisationnelles internes à l\'activité entraînant la perturbation de service pour une ou plusieurs catégories de tiers (clients, financeurs, prestataires, fournisseurs…). Ces nuisances peuvent entraîner l\'arrêt d\'autres services de l\'Entreprise.</p>
			<p>L\'incident peut-il provoquer l\'arrêt du service ? Oui</p>
			<p>Peut-il impacter d\'autres services ? Oui</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[4], $TypesImpact[2],
			'<p>- Arrêt total de l\'activité</p>
			<p>L\'incident peut-il provoquer l\'arrêt total des activités de l\'Entreprise ? Oui</p>');

		// Mise à jour de la colonne "Juridique / Réglementaire"
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[1], $TypesImpact[3],
			'<p>- Absence d\'éligibilité à une action civile ou pénale ou à une action réglementaire, mais recours amiable (mise en demeure, acte extrajudiciaire etc…) de la part d\'un cocontractant ou d\'un tiers</p>
			<p>L\'incident entraîne-t-il des conséquences juridiques ? Non</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[2], $TypesImpact[3],
			'<p>- Contravention ou Exposition à des poursuites civiles limitées</p>
			<p>- Recommandations des autorités de tutelle</p>
			<p>L\'incident peut impliquer des conséquences juridiques limitées ? Oui</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[3], $TypesImpact[3],
			'<p>- Exposition à une condamnation civile significative, résiliation de contrat non stratégique</p>
			<p>-  Délit ou manquement à une norme juridique faisant encourir des sanctions financière significative</p>
			<p>- Avertissement ou blâme des autorités de tutelle</p>
			<p>L\'incident nécessite-t-il la convocation du représentant légal ? Oui</p>
			<p>La conséquence juridique de l\'incident peut-elle amener à la fermeture de l\'entité ou à la condamnation de ses dirigeants ? Non</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[4], $TypesImpact[3],
			'<p>- Condamnation pénale de l\'entreprise : exclusion des marchés publics, amendes pénales > 1M€</p>
			<p>- Mise en jeu de la responsabilité pénale du Dirigeant avec interdiction d\'exercer certaines activités ou peine d\'emprisonnement<p>
			<p>- Retrait d\'agrément ou habilitation</p>
			<p>La conséquence juridique de l\'incident peut-elle amener à la fermeture de l\'entité ou à la condamnation de ses dirigeants ? Oui</p>');

		// Mise à jour de la colonne "Image de marque"
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[1], $TypesImpact[4],
			'<p>- Pas de médiatisation mais mécontentement possible de parties prenantes internes</p>
			<p>- Pas d\'atteinte à la satisfaction des tiers (clients, financeurs, prestataires, fournisseurs, relations publiques…)</p>
			<p>L\'incident va-t-il circuler en interne ? Nécessite-t-il une communication interne spécifique ? Oui</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[2], $TypesImpact[4],
			'<p>- Exposition à un risque de mention négative ponctuelle sur un média ou réseau social à faible audience, ayant peu d’impact ou éloigné de notre cœur de métier</p>
			<p>- Faible atteinte à la satisfaction des tiers (clients, financeurs, prestataires, fournisseurs, relations publiques…) avec une remédiation rapide et satisfaisante</p>
			<p>L\'incident dépasse-t-il la sphère interne ? Peut-il être relayé sur les réseaux sociaux ? Par la presse locale (faible audience) ? Oui</p>
			<p>A-t-il un impact au niveau de l\'image vis-à-vis de ses partenaires, clients ? Non</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[3], $TypesImpact[4],
			'<p>- Constatation de montée de réclamations  via les réseaux légérement supérieure à la normale et exposition à un risque de mentions fréquentes ou récurrentes et dont le contenu ne valorise pas l\'image de l\'Entreprise, ou le dégrade peu</p>
			<p>- Exposition à un risque de mentions négatives dans la presse spécialisée dans notre coeur d\'activité</p>
			<p>- Volume significatif de réclamations de la part de tiers (clients, financeurs, prestataires, fournisseurs, …) et/ou réclamations récurrentes que l\'on ne parvient pas à remédier</p>');
		$objMatriceImpacts->MaJDescriptionImpact($cmp_id, '', $NiveauxImpact[4], $TypesImpact[4],
			'<p>- Crise médiatique relayée par l\'ensemble des canaux de communication (réseaux sociaux compris) portant durablement atteinte à l\'image ou la réputation de l\'Entreprise</p>
			<p>- Défiance des tiers  (clients, financeurs, prestataires, fournisseurs…), impact sur un large public, au-delà des parties prenantes habituelles</p>
			<p>L\'incident peut êre relayé par la presse nationale ? Oui</p>
			<p>Entraîne-t-il des pertes lecteurs/clients importantes ? Oui</p>
			<p>Nécessite-t-il la mise en place d\'une cellule de communication ? Oui</p>
			<p>Faut-il prévoir des sorties médiatiques des dirigeants ? La publication de communiqués de presse ? Oui</p>');

		$Resultat = array( 'statut' => 'success',
			'texteMsg' => $L_Initialisation_Terminee );
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize,
			'L_Titre' => $L_Error,
			'L_Bouton' => '',
			'L_Fermer' => $L_Fermer
		);
		
		echo json_encode( $Resultat );
		exit();
	}

	echo json_encode( $Resultat );
	
	break;
}



function actualiseSocieteCampagne($objSocietes, $objCampagnes, $forcer=0) {
	/**
	 * Actualise les listes Sociétés et Campagnes à l'entrée dans l'écran et en cas de changement.
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

	return [$Liste_Societes, $Liste_Campagnes];
}

?>