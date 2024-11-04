<?php

/**
* Ce script gère les Parties Prenantes.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MyContinuity
* \version 1.0
* \date 2024-02-27
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_PartiesPrenantes_PDO.inc.php' );

// Crée une instance de l'objet HTML.
$objSocietes = new HBL_Societes();
$objPartiesPrenantes = new PartiesPrenantes();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'PPR';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'ppr_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ppr_nom', 'titre' => $L_Nom, 'taille' => '3', 'maximum' => 35,
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ppr_nom', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ppr_prenom', 'titre' => $L_Prenom, 'taille' => '3', 'maximum' => 25,
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ppr_nom', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ppr_interne', 'titre' => $L_Interne, 'taille' => '1',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ppr_interne', 'type' => 'select', 'modifiable' => 'oui', 'liste' => '0='.$L_No.';1='.$L_Yes );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'ppr_description', 'titre' => $L_Description, 'taille' => '3',
	'triable' => 'oui', 'tri_actif' => 'non', 'sens_tri' => 'ppr_description', 'type' => 'textarea', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Societes = $objSocietes->rechercherSocietes();
		if ( isset($_SESSION['s_sct_id']) ) {
			$sct_id = $_SESSION['s_sct_id'];
		} else {
			$_SESSION['s_sct_id'] = $sct_id = $Liste_Societes[0]->sct_id;
		}
	} else {
		$Liste_Societes = $objSocietes->rechercherSocietes('', '', $_SESSION['idn_id']);
		if ( isset($_SESSION['s_sct_id']) ) {
			$sct_id = $_SESSION['s_sct_id'];
		} else {
			$sct_id = $Liste_Societes[0]->sct_id;
		}
	}

	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_PartiesPrenantes, $Fichiers_JavaScript, 3 ) .
		$PageHTML->construireNavbarJson('Logo-MyContinuity.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_PartiesPrenantes, $Liste_Societes, $Boutons_Alternatifs )
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
		'L_Titre_Ajouter' => $L_Ajouter_PartiePrenante,
		'L_Titre_Modifier' => $L_Modifier_PartiePrenante,
		'L_Titre_Supprimer' => $L_Supprimer_PartiePrenante,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Nom' => $L_Nom,
		'L_Prenom' => $L_Prenom,
		'L_Trigramme' => $L_Trigramme,
		'L_Interne' => $L_Interne,
		'L_Libelle' => $L_Label,
		'L_Description' => $L_Description,
		'L_Role' => $L_Role,
		'Liste_Roles_PartiePrenante' => $objPartiesPrenantes->rechercherRolesPartiePrenante(),
		'L_Ajouter_Role_PartiePrenante' => $L_Ajouter_Role_PartiePrenante,
		'L_Role_PartiePrenante_Cree' => $L_Role_PartiePrenante_Cree,
		'L_Oui' => $L_Yes,
		'L_Non' => $L_No
		);

	if ( $Droit_Lecture === TRUE ) {
		if ( isset($_POST['ppr_id']) and $_POST['ppr_id'] != '') {
			$Libelles['PartiePrenante'] = $objPartiesPrenantes->rechercherPartiesPrenantes( $_SESSION['s_sct_id'], '', $_POST['ppr_id'] );
		}
	}

	print( json_encode( $Libelles ) );
		
	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['ppr_nom']) and isset($_POST['ppr_prenom'])
			and isset($_POST['ppr_interne']) and isset($_POST['ppr_description']) ) {
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

			$_POST['ppr_interne'] = $PageHTML->controlerTypeValeur( $_POST['ppr_interne'], 'NUMBER' );
			if ( $_POST['ppr_interne'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_interne)'
				) );
				
				exit();
			}

			$_POST['ppr_description'] = $PageHTML->controlerTypeValeur( $_POST['ppr_description'], 'ASCII' );
			if ( $_POST['ppr_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ppr_description)'
				) );
				
				exit();
			}

			try {
				$objPartiesPrenantes->majPartiePrenante( '', $_SESSION['s_sct_id'], $_POST['ppr_nom'],
					$_POST['ppr_prenom'], $_POST['ppr_interne'], $_POST['ppr_description'] );

				$Id_PartiePrenante = $objPartiesPrenantes->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_PARTIE_PRENANTE', 'ppr_id="' . $Id_PartiePrenante .
					'", sct_id="' . $_SESSION['s_sct_id'] . '", ppr_nom="' . $_POST['ppr_nom'] .
					'", ppr_prenom="' . $_POST['ppr_prenom'] . '", ppr_interne="' . $_POST['ppr_interne'] .
					'", ppr_description="' . $_POST['ppr_description'] . '"' );

				$Valeurs = new stdClass();
				$Valeurs->ppr_nom = $_POST['ppr_nom'];
				$Valeurs->ppr_prenom = $_POST['ppr_prenom'];
				if ($_POST['ppr_interne'] == FALSE) {
					$Valeurs->ppr_interne = $L_No;
				} else {
					$Valeurs->ppr_interne = $L_Yes;
				}
				$Valeurs->ppr_description = $_POST['ppr_description'];

				$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_PartiePrenante, $Valeurs, $Format_Colonnes );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_PartiePrenante_Creee,
					'texte' => $Occurrence,
					'id' => $Id_PartiePrenante,
					'droit_modifier' => $Droit_Modifier,
					'droit_supprimer' => $Droit_Supprimer
					);

			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_PartiePrenante;
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
			try {
				$objPartiesPrenantes->majPartiePrenanteParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_PARTIE_PRENANTE', $_POST[ 'source' ] . ' = "' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_PartiePrenante_Modifiee
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
			$_POST['id'] = $PageHTML->controlerTypeValeur( $_POST['id'], 'NUMBER' );
			if ( $_POST['id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (id)'
				) );
				
				exit();
			}
	
			try {
				$objPartiesPrenantes->supprimerPartiePrenante( $_POST['id'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_PARTIE_PRENANTE', 'ppr_id="' . $_POST['id'] . '", ' .
					'ppr_nom="' . $_POST[ 'libelle' ] . '"' );
	
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_PartiePrenante_Supprimee
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
			$ListePartiesPrenantes = $objPartiesPrenantes->rechercherPartiesPrenantes( $_SESSION['s_sct_id'], $Trier );
			$Total = $objPartiesPrenantes->RowCount;

			$Texte_HTML = '';
			
			foreach ($ListePartiesPrenantes as $Occurrence) {
				if ($Occurrence->ppr_interne == FALSE) {
					$Occurrence->ppr_interne = $L_No;
				} else {
					$Occurrence->ppr_interne = $L_Yes;
				}

				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->ppr_id, $Occurrence, $Format_Colonnes );
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


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['ppr_id']) and isset($_POST['ppr_nom']) and isset($_POST['ppr_prenom'])
			and isset($_POST['ppr_interne']) and isset($_POST['ppr_description']) ) {
				$_POST['ppr_id'] = $PageHTML->controlerTypeValeur( $_POST['ppr_id'], 'NUMBER' );
				if ( $_POST['ppr_id'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (ppr_id)'
					) );

					exit();
				}

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

				$_POST['ppr_interne'] = $PageHTML->controlerTypeValeur( $_POST['ppr_interne'], 'NUMBER' );
				if ( $_POST['ppr_interne'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (ppr_interne)'
					) );

					exit();
				}

				$_POST['ppr_description'] = $PageHTML->controlerTypeValeur( $_POST['ppr_description'], 'ASCII' );
				if ( $_POST['ppr_description'] == -1 ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Invalid_Value . ' (ppr_description)'
					) );

					exit();
				}

			try {
				$objPartiesPrenantes->majPartiePrenante( $_POST['ppr_id'], $_SESSION['s_sct_id'],
					$_POST['ppr_nom'], $_POST['ppr_prenom'], $_POST['ppr_interne'], $_POST['ppr_description'] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_PARTIE_PRENANTE', 'ppr_id="' . $_POST['ppr_id'] . '", sct_id="' . $_SESSION['s_sct_id'] . '", ' .
					'ppr_nom="' . $_POST[ 'ppr_nom' ] . '", ppr_prenom="' . $_POST[ 'ppr_prenom' ] . '", ' .
					'ppr_interne="' . $_POST[ 'ppr_interne' ] . '", ppr_description="' . $_POST[ 'ppr_description' ] . '" ' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_PartiePrenante_Modifiee
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_PartiePrenante;
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
				$Compteurs = $objPartiesPrenantes->controlerAssociationPartiePrenante( $_POST['id'] );
	
				$CodeHTML = '';
	
				if ( $Compteurs->total_act ) {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_PartiePrenante_Associe, $_POST['libelle'] ) .
						'<ul style="margin-top: 10px;">';
	
					if ( $Compteurs->total_act > 1 ) $Libelle = $L_Activites;
					else $Libelle = $L_Activite;
	
					$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_act . '</span> ' . $Libelle . '</li>' .
						'</ul>' . $L_Cascading_Delete;
				} else {
					$CodeHTML .= sprintf( $L_Confirmer_Suppression_PartiePrenante, $_POST['libelle'] );
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


 case 'AJAX_Selectioner_Societe':
	$PageHTML->selectionnerSociete();

	break;


 case 'AJAX_Selectioner_Campagne':
	if ( isset($_POST['cmp_id']) ) {
		if ( $PageHTML->verifierCampagneAutorisee($_POST['cmp_id']) ) {
			echo json_encode( array( 'statut' => 'error',
				'texteMsg' => $L_Pas_Droit_Ressource . ' (cmp_id="' . $_POST['cmp_id'] . '")'.' [' . __LINE__ . ']' ) );
			exit();
		}

		$_SESSION['s_cmp_id'] = $_POST['cmp_id'];

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

 case 'AJAX_Ajouter_Role_PartiePrenante':
	if ( $Droit_Ajouter === TRUE ) {
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
}


function listerRolesPartiePrenante( $Init_Id = '', $Init_Libelle = '' ) {
	$objPartiesPrenantes = new PartiesPrenantes();

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

?>