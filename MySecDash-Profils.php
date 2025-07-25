<?php

/**
* Ce script gère les Profils de MySecDash.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2015-10-15
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

// Charge les informations et droits de base d'un écran
include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_referentiels.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include_once( DIR_LIBRAIRIES . '/Class_HBL_ApplicationsInternes_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Profils_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );


$objApplications = new HBL_ApplicationsInternes();
$objProfils = new HBL_Profils();
$objProfiles_Access_Control = new HBL_Profils_Controles_Acces();
$objSocietes = new HBL_Societes();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'PRF';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'prf_id' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'prf_libelle', 'titre' => $L_Nom, 'taille' => '4',
	'triable' => 'oui', 'sens_tri' => 'prf_libelle', 'type' => 'input', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'prf_description', 'titre' => $L_Description, 'taille' => '6',
	'triable' => 'oui', 'sens_tri' => 'prf_description', 'type' => 'textarea', 'modifiable' => 'oui' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '2', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs = [
			['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter_Profil, 'glyph'=>'plus']
		];
	} else {
		$Boutons_Alternatifs = '';
	}

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Profils, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Profils, '', $Boutons_Alternatifs )
	);


	if ( $Droit_Lecture === TRUE ) {
		// Construit le tableau central.
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
		'L_Fermer' => $L_Fermer,
		'Titre' => $L_Ajouter_Profil,
		'Titre1' => $L_Modifier_Profil,
		'Titre2' => $L_Supprimer_Profil,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Libelle' => $L_Label,
		'L_Description' => $L_Description,
		'L_Applications' => $L_Applications,
		'L_Nom' => $L_Nom,
		'L_Droits' => $L_Rights,
		'L_Localisation' => $L_Localisation,
		'L_Lecture' => $L_Right_1,
		'L_Ecriture' => $L_Right_2,
		'L_Modifier' => $L_Right_3,
		'L_Supprimer' => $L_Right_4
		);

	if ( ! isset( $_POST['prf_id'] ) ) {
		$_POST['prf_id'] = 0;
		
		$Libelles['prf_libelle'] = '';
		$Libelles['prf_description'] = '';
	} else {
		if ( $_POST['prf_id'] == 0 ) {
			$Libelles['prf_libelle'] = '';
			$Libelles['prf_description'] = '';
		} else {
			$Profil = $objProfils->detaillerProfil( $_POST['prf_id'] );
	
			$Libelles['prf_libelle'] = $Profil->prf_libelle;
			$Libelles['prf_description'] = $Profil->prf_description;
		}
	}

	$Libelles['Liste_Applications'] = $objProfiles_Access_Control->rechercherApplicationsParProfil( $_POST['prf_id'] );

	if ( isset( $_POST['action'] ) ) {
		if ( $_POST['action'] == 'supprimer' ) {
			if ( isset($_POST['id']) ) {
				try {
					$Compteurs = $objProfils->testerAssociationProfil( $_POST['id'] );
					
					$CodeHTML = '';
					
					if ( $Compteurs->total_idn ) {
						$CodeHTML .= sprintf( $L_Confirmation_Suppression_Profil_Associe, $_POST['libelle'] ) .
						'<ul style="margin-top: 10px;">';
						
						if ( $Compteurs->total_idn > 1 ) $Libelle = $L_Identities;
						else $Libelle = $L_Identity;
						
						$CodeHTML .= '<li><span class="orange_moyen">' . $Compteurs->total_idn . '</span> ' . $Libelle . '</li>' .
							'</ul>' . $L_Cascading_Delete;
					} else {
						$CodeHTML .= sprintf( $L_Confirmation_Suppression_Profil, $_POST['libelle'] );
					}
					
					$Libelles['statut'] = 'success';
					$Libelles['texteMsg'] = $CodeHTML;
				} catch( Exception $e ) {
					$Libelles['statut'] = 'error';
					$Libelles['texteMsg'] = $e->getMessage();
				}
			} else {
				$Libelles['statut'] = 'error';
				$Libelles['texteMsg'] = $L_ERR_Champs_Obligatoires;
			}
		}
	}

	print( json_encode( $Libelles ) );

	exit();


	
	
 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		$Trier = $_POST[ 'trier' ];

		try {
			$Profils = $objProfils->rechercherProfils( $Trier );
			$Total = $objProfils->RowCount;

			$Texte_HTML = '';

			foreach ($Profils as $Occurrence) {
				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->prf_id, $Occurrence, $Format_Colonnes );
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


 case 'AJAX_Lister_Applications':
	if ( $Droit_Lecture === TRUE ) {
		$Liste_Applications = $objApplications->rechercherApplications('ain_libelle');
		$Total_Applications = $objApplications->RowCount;
	
		$Liste_Applications_HTML = '';
		$Lignes = 0;
	
		foreach( $Liste_Applications as $Occurrence ) {
			$Lignes += 1;
			$Liste_Applications_HTML .= "<div class=\"row liste\" data-ligne=\"" . $Lignes . "\" data-id=\"" . $Occurrence->ain_id . "\"><div class=\"col-lg-3\">" . $Occurrence->ain_libelle . "</div></div>";
			$Liste_ID_Applications[] = $Occurrence->ain_id;
		}
	
		echo json_encode( array(
			'statut' => 'success',
			'liste_applications' => $Liste_Applications_HTML,
			'liste_id_applications' => $Liste_ID_Applications,
			'total_applications' => $Total_Applications
			) );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Lister_Profils':
	if ( $Droit_Lecture === TRUE ) {
		$Liste_Profils = $objProfils->rechercherProfils();
		$Total_Profils = $objProfils->RowCount;
	
		$Liste_Profils_HTML = '';
		$Colonnes = 0;
	
		foreach( $Liste_Profils as $Occurrence ) {
			$Colonnes += 1;
			$Liste_Profils_HTML .= '<div class="col-lg-1 text-break" data-id="' . $Occurrence->prf_id . '"><a href="#" class="modifiable">' . $Occurrence->prf_libelle . '</a>';
	
			if ( $Droit_Supprimer == TRUE ) $Liste_Profils_HTML .= '<button class="bi-x-circle btn btn-outline-secondary btn-sm" title="'.$L_Supprimer_Profil.'"></button>';
	
			$Liste_Profils_HTML .= '</div>';
			
			$Liste_ID_Profils[] = $Occurrence->prf_id;
		}
	
		$Limitation = $PageHTML->recupererParametre('limitation_profils');
	
		echo json_encode( array(
			'statut' => 'success',
			'liste_profils' => $Liste_Profils_HTML,
			'liste_id_profils' => $Liste_ID_Profils,
			'total_profils' => $Total_Profils,
			'droit_suppression' => $Droit_Supprimer,
			'droit_modification' => $Droit_Modifier,
			'libelle_limitation' => $L_Limitation_Licence,
			'limitation' => $Limitation
			) );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Lister_Control_Acces':
 	if ( $Droit_Lecture === TRUE ) {
		$Liste_Acces = $objProfiles_Access_Control->rechercherDroitsVersMatrice();
		$Total_Acces = $objProfiles_Access_Control->RowCount;
		$Libelles = $objProfiles_Access_Control->rechercherLibellesDroits();
	
		echo json_encode( array(
			'statut' => 'success',
			'liste_acces' => $Liste_Acces,
			'total_acces' => $Total_Acces,
			'libelles_droits' => $Libelles
			) );
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Activer_Droit':
	if ( $Droit_Modifier === TRUE ) {
		try {
			if ( $_POST['activer'] == 1 ) {
				$Action = $L_Droit_Ajoute;
				$Code_Action = 'ATP_ECRITURE';
				$objProfiles_Access_Control->ajouterControleAcces( $_POST['id_profil'], $_POST['id_application'], $_POST['id_droit'] );
			} else {
				$Action = $L_Droit_Supprime;
				$Code_Action = 'ATP_SUPPRESSION';
				$objProfiles_Access_Control->supprimerControleAcces( $_POST['id_profil'], $_POST['id_application'], $_POST['id_droit'] );
			}

			$PageHTML->ecrireEvenement( $Code_Action, 'OTP_DROIT', 'prf_id="' . $_POST['id_profil'] . '", app_id="' . $_POST[ 'id_application' ] . '", ' .
				'drt_id="' . $_POST['id_droit'] . '"' );

			echo json_encode( array(
				'statut' => 'success',
				'texteMsg' => $Action
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


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if (isset($_POST['prf_libelle'])) {
			if ($_POST['prf_libelle'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );

				echo json_encode( $Resultat );
				exit();
			}

			$_POST['prf_libelle'] = $PageHTML->controlerTypeValeur( $_POST['prf_libelle'], 'ASCII' );
			if ( $_POST['prf_libelle'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (prf_libelle)'
				) );
				
				exit();
			}

			$_POST['prf_description'] = $PageHTML->controlerTypeValeur( $_POST['prf_description'], 'ASCII' );
			if ( $_POST['prf_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (prf_description)'
				) );
				
				exit();
			}

			try {
				$objProfils->majProfil( '', $_POST[ 'prf_libelle' ], $_POST[ 'prf_description' ] );
				$Id_Profil = $objProfils->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_PROFIL', 'prf_id="' . $Id_Profil . '", prf_libelle="' . $_POST[ 'prf_libelle' ] . 
					'", prf_libelle="' . $_POST[ 'prf_libelle' ] . '"' );

				$Limitation = $PageHTML->recupererParametre('limitation_profils');
				//$Libelles = $objProfiles_Access_Control->rechercherLibellesDroits();


				// Ajoute tous les Droits au Profil.
				if ( isset( $_POST['Liste_Droits_Ajouter'] ) ) {
					foreach( $_POST['Liste_Droits_Ajouter'] as $Element ) {
						$Droit = explode( '-', $Element );
						$objProfiles_Access_Control->ajouterControleAcces( $Id_Profil, $Droit[0], $Droit[1] );
					}
				}


				$Occurrence = new stdClass();
				$Occurrence->prf_id = $Id_Profil;
				$Occurrence->prf_libelle = $_POST[ 'prf_libelle' ];
				$Occurrence->prf_description = $_POST[ 'prf_description' ];

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Profil_Cree,
					'id' => $Id_Profil,
					'limitation' => $Limitation,
					'libelle_limitation' => $L_Limitation_Licence,
					//'libelles_droits' => $Libelles,
					'libelle_delete_profil' => $L_Supprimer_Profil,
					'droit_supprimer' => $Droit_Supprimer,
					'droit_modifier' => $Droit_Modifier,
					'texteHTML' => $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->prf_id, $Occurrence, $Format_Colonnes )
					);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Profil;
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
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur']) ) {
			try {
				$objProfils->majProfilParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_PROFIL', $_POST[ 'source' ] . '="' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Profil_Modifie
				);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_Profil;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
				);
			}

			echo json_encode( $Resultat );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Supprimer_Profil':
	if ( $Droit_Supprimer === TRUE ) {
		try {
			$objProfils->supprimerProfil( $_POST[ 'id_profil' ] );

			$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_PROFIL', 'prf_id="' . $_POST['id_profil'] . '", prf_libelle="' . $_POST[ 'libelle' ] . '"' );

			$Limitation = $PageHTML->recupererParametre('limitation_profils');

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Profil_Supprime,
				'limitation' => $Limitation,
				'libelle_limitation' => $L_Limitation_Licence
				);

		} catch (Exception $e) {
			$Statut = 'error';
			$Message = $e->getMessage();

			$Resultat = array(
				'statut' => $Statut,
				'texteMsg' => $Message
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


 case 'AJAX_Modifier_Profil':
	if ( $Droit_Modifier === TRUE ) {
		if (isset($_POST['prf_libelle'])) {
			if ($_POST['prf_libelle'] == '') {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $L_Field_Mandatory );
				
				echo json_encode( $Resultat );
				exit();
			}

			$_POST['prf_id'] = $PageHTML->controlerTypeValeur( $_POST['prf_id'], 'NUMBER' );
			if ( $_POST['prf_id'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (prf_id)'
				) );
				
				exit();
			}

			$_POST['prf_libelle'] = $PageHTML->controlerTypeValeur( $_POST['prf_libelle'], 'ASCII' );
			if ( $_POST['prf_libelle'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (prf_libelle)'
				) );
				
				exit();
			}

			$_POST['prf_description'] = $PageHTML->controlerTypeValeur( $_POST['prf_description'], 'ASCII' );
			if ( $_POST['prf_description'] == -1 ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (prf_description)'
				) );
				
				exit();
			}

			try {
				$objProfils->majProfil( $_POST[ 'prf_id' ], $_POST[ 'prf_libelle' ], $_POST[ 'prf_description' ] );
				
				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_PROFIL', 'prf_id="' . $_POST[ 'prf_id' ] . '", prf_libelle="' . $_POST[ 'prf_libelle' ] .
					'", prf_libelle="' . $_POST[ 'prf_libelle' ] . '"' );

				$Limitation = $PageHTML->recupererParametre('limitation_profils');
				//$Libelles = $objProfiles_Access_Control->rechercherLibellesDroits();


				// Ajoute les Droits au Profil.
				if ( isset( $_POST['Liste_Droits_Ajouter'] ) ) {
					foreach( $_POST['Liste_Droits_Ajouter'] as $Element ) {
						$Droit = explode( '-', $Element );
						$objProfiles_Access_Control->ajouterControleAcces( $_POST[ 'prf_id' ], $Droit[0], $Droit[1] );
					}
				}

				// Supprime les Droits au Profil.
				if ( isset( $_POST['Liste_Droits_Supprimer'] ) ) {
					foreach( $_POST['Liste_Droits_Supprimer'] as $Element ) {
						$Droit = explode( '-', $Element );
						$objProfiles_Access_Control->SupprimerControleAcces( $_POST[ 'prf_id' ], $Droit[0], $Droit[1] );
					}
				}


				$Occurrence = new stdClass();
				$Occurrence->prf_id = $_POST[ 'prf_id' ];
				$Occurrence->prf_libelle = $_POST[ 'prf_libelle' ];
				$Occurrence->prf_description = $_POST[ 'prf_description' ];

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Profil_Modifie,
					'id' => $_POST[ 'prf_id' ],
					'limitation' => $Limitation,
					'libelle_limitation' => $L_Limitation_Licence,
					//'libelles_droits' => $Libelles,
					'libelle_delete_profil' => $L_Supprimer_Profil,
					'droit_supprimer' => $Droit_Supprimer,
					'droit_modifier' => $Droit_Modifier
				);

			} catch (Exception $e) {
				$Statut = 'error';
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Gestion des doublons
					$Message = $L_ERR_DUPL_Profil;
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
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;
}

?>