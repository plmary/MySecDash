<?php

/**
* Ce script gère tous les paramètres internes de l'outil.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \date 2024-10-09
* \note Contrôle sécurité réalisé
*/

// Charge les constantes du projet.
include 'Constants.inc.php';

include DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php';

// Charge les libellés en fonction de la langue sélectionnée.
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';
include HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php';
include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script );


$Trier = 'label';



// Exécute l'action identifie
switch( $Action ) {
 default:
	// Construction des onglets de navigation.
	//$Liste = $PageHTML->rechercherGroupesParametres();

	$Systemes = $PageHTML->rechercherParametres( $Trier, '', '*' );

	$Onglets = '<ul class="nav nav-tabs col-lg-12" style="margin-top: 15px;">';
	
	if ( $_SESSION['idn_super_admin'] == true ||
		( array_key_exists( 'syslog_alert', $Systemes ) &&
		array_key_exists( 'syslog_host', $Systemes ) &&
		array_key_exists( 'syslog_port', $Systemes ) &&
		array_key_exists( 'syslog_template', $Systemes ) &&
		array_key_exists( 'mail_alert', $Systemes ) &&
		array_key_exists( 'mail_sender', $Systemes ) &&
		array_key_exists( 'mail_receiver', $Systemes ) &&
		array_key_exists( 'mail_title', $Systemes ) &&
		array_key_exists( 'mail_body_type', $Systemes ) &&
		array_key_exists( 'mail_template', $Systemes ) ) ) {
		$Onglets .= '<li role="presentation" class="nav-item"><a href="#" class="nav-link active" id="Alerte">' . $L_Alerte . '</a></li>';
	}
	
	$Onglets .= '<li role="presentation" class="nav-item"><a href="#" class="nav-link" id="Connexion">' . $L_Connexion . '</a></li>';

	$Onglets .= '</ul>';


	// Définit le format des colonnes de l'entête du tableau central.
	$Format_Colonnes['Colonnes'][] = array( 'titre' => $L_Parameter, 'taille' => '5', 'sens_tri' => 'comment',
		'triable' => 'non', 'tri_actif' => 'non' );
	$Format_Colonnes['Colonnes'][] = array( 'titre' => $L_Value, 'taille' => '7' );

	print $PageHTML->construireEnteteHTML( $L_Gestion_Parametres_Base, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Parametres_Base,
			'', // Choix de Sociétés possibles
			'', // Boutons supplémentaires accrochés au titre de l'écran
			'', // Liste contextuelle possible
			$Onglets );


	if ( $Droit_Lecture === true ) {
		// Construit un tableau central vide.
		print( $PageHTML->contruireTableauVide( $Format_Colonnes ) );
	}

	print $PageHTML->construireFooter( true ) .
		$PageHTML->construirePiedHTML();

	break;


 /* ========================================================================
 ** Réponses aux appels AJAX
 */

 case 'AJAX_Libeller':
	print json_encode( array(
		'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'Titre' => $L_Ajouter_Entite,
		'L_Ajouter' => $L_Ajouter,
		'L_Libelle' => $L_Label
		) );
	
	exit();


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === true ) {
		if (isset($_POST['id']) && isset($_POST['libelle'])){
			try {
				$PageHTML->majParametreParID($_POST['id'], $_POST['libelle']);

				$_Tmp = $PageHTML->recupererParametreParID($_POST['id']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_PARAMETRE', $_Tmp->prs_groupe . ' : ' . $_Tmp->prs_nom . ' = "' . $_POST[ 'libelle' ] . '"' );

				$Resultat = array( 'statut' => 'success',
					'texteMsg' => $L_Parameter_Modified
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == -10 ) { // Si erreur applicative
					$temp = explode( '=', $Message );
						
					$Statut = $temp[0];
					$Id = $temp[1]; // Afin qu'elle puisse être réactivée dans les étapes d'après.
				} else { // Si erreur infrastructure
					$Statut = 'error';
					$Id = '';
				}

				$Resultat = array( 'statut' => $Statut,
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


 case 'AJAX_Trier':
	if ( $Droit_Lecture === true ) {
		$Trier = $_POST[ 'trier' ];

		if ( ! isset( $_POST[ 'groupe' ] ) ) {
			$Groupe = '';
		} else {
			$Groupe = mb_strtolower( $_POST[ 'groupe' ] );
		}

		try {
			$Systemes = $PageHTML->rechercherParametres( $Trier, '', '*', $Groupe );

			$Total = $PageHTML->RowCount;

			$Texte_HTML = '';

			$PageHTML->ecrireEvenement( 'ATP_LECTURE', 'OTP_PARAMETRE', $Groupe );

			switch( $Groupe ) {
			 case 'alerte':
				$Option_HTML = '';
				$Option_TEXT = '';
				if ( array_key_exists( 'mail_body_type', $Systemes ) ) {
					if ( $Systemes['mail_body_type']->prs_valeur == 'HTML' ) {
						$Option_HTML = ' selected';
					}

					if ( $Systemes['mail_body_type']->prs_valeur == 'TEXT' ) {
						$Option_TEXT = ' selected';
					}
				}

				if ( array_key_exists( 'syslog_alert', $Systemes ) ) {
					$Syslog_Alert = '';

					if ( $Systemes['syslog_alert']->prs_valeur == 1
					 || $Systemes['syslog_alert']->prs_valeur == 'true' ) {
						$Syslog_Alert = 'checked';
					}

					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2 mb-2">' .
						  '<label class="form-label fw-bold">' . $L_Alertes_Syslog . '</label>' .
						 '</div>' .
						 '<div class="col-lg-7 g-2 mb-2">' .
						  '<div class="form-check">' .
						   '<input type="checkbox" class="form-check-input" id="prs_valeur-' . $Systemes['syslog_alert']->prs_id .
						   '" data-old="' . $Systemes['syslog_alert']->prs_valeur . '" ' .
						   'onChange="sauverParametre(' . $Systemes['syslog_alert']->prs_id . ');" ' . $Syslog_Alert . '>' .
						   '<label class="form-check-label" for="prs_valeur-' . $Systemes['syslog_alert']->prs_id . '">' .
						   $L_Service_Active .
						   '</label>' .
						   '</div>' . // .form-check
						 '</div>' . // .col-lg-7
						'</div>'; // .my-row-1
				}


				if ( array_key_exists( 'syslog_host', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-7 g-2 mb-2">' .
						  '<label class="form-label" for="prs_valeur-' . $Systemes['syslog_host']->prs_id . '">' . $L_Replication_Syslog . '</label>' .
						  '<input class="form-control" type="text" maxlength="60" id="prs_valeur-' . $Systemes['syslog_host']->prs_id .
						   '" data-old="' . $Systemes['syslog_host']->prs_valeur . '" value="' . $Systemes['syslog_host']->prs_valeur . '" ' .
						   'onKeyDown="controleSaisieChamp( event, ' . $Systemes['syslog_host']->prs_id . ');" ' .
						   'onBlur="sauverParametre(' . $Systemes['syslog_host']->prs_id . ');">' .
						 '</div>' .
						'</div>';
				}


				if ( array_key_exists( 'syslog_port', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label class="form-label col-lg-8 g-2">' . $L_Replication_Port_Syslog . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-2 mb-2">' .
						  '<input class="form-control" type="number" id="prs_valeur-' . $Systemes['syslog_port']->prs_id .
						    '" data-old="' . $Systemes['syslog_port']->prs_valeur . '" value="' . $Systemes['syslog_port']->prs_valeur . '" ' .
						    'onKeyDown="controleSaisieChamp( event, ' . $Systemes['syslog_port']->prs_id . ');" ' .
						    'onBlur="sauverParametre(' . $Systemes['syslog_port']->prs_id . ');">' .
						 '</div>' . // .col-2
						'</div>'; // .row
				}


				if ( array_key_exists( 'syslog_template', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-6 g-2 mb-2">' .
						  '<label class="form-label">' . $L_Fichier_Squelette . '</label>' .
						  '<div class="input-group">' .
						   '<input class="form-control" type="text" maxlength="60" id="prs_valeur-' . $Systemes['syslog_template']->prs_id .
						    '" data-old="' . $Systemes['syslog_template']->prs_valeur . '" value="' . $Systemes['syslog_template']->prs_valeur . '" ' .
						    'onKeyDown="controleSaisieChamp( event, ' . $Systemes['syslog_template']->prs_id . ');" ' .
						    'onBlur="sauverParametre(' . $Systemes['syslog_template']->prs_id . ');">' .
						   '<span class="input-group-text"><button class="btn btn-outline-secondary btn-sm" id="edit-syslog-body" disabled><i class="bi-pencil-square"></i></button></span>' .
						  '</div>' . // .input-group
						 '</div>' . // .col-6
						'</div>'; // .row
				}


				if ( array_key_exists( 'mail_alert', $Systemes ) ) {
					$Mail_Alert = '';

					if ( $Systemes['mail_alert']->prs_valeur == 1
					 || $Systemes['mail_alert']->prs_valeur == 'true' ) {
							$Mail_Alert = 'checked';
						}

					$Texte_HTML .=
						'<div class="row my-separator">' .
						 '<div class="col-lg-4 g-2 mb-2">' .
						  '<label class="form-label fw-bold">' . $L_Alertes_Courriel . '</label>' .
						 '</div>' .
						 '<div class="col-lg-7 g-2 mb-2">' .
						  '<div class="form-check">' .
						   '<input type="checkbox" class="form-check-input" id="prs_valeur-' . $Systemes['mail_alert']->prs_id .
							'" data-old="' . $Systemes['mail_alert']->prs_valeur . '" ' .
							'onChange="sauverParametre(' . $Systemes['mail_alert']->prs_id . ');" ' . $Mail_Alert . '>' .
						    '<label class="form-check-label" for="prs_valeur-' . $Systemes['mail_alert']->prs_id . '">' . $L_Service_Active . '</label>' .
						  '</div>' .
						 '</div>' .
						'</div>';
				}


				if ( array_key_exists( 'mail_sender', $Systemes ) ) {
					$Texte_HTML .=
					'<div class="row">' .
					 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
					 '<div class="col-lg-7 g-2 mb-2">' .
					  '<label class="form-label">' . $L_De . '</label>' .
					  '<input class="form-control" type="text" maxlength="60" id="prs_valeur-' . $Systemes['mail_sender']->prs_id .
						'" data-old="' . $Systemes['mail_sender']->prs_valeur . '" value="' . $Systemes['mail_sender']->prs_valeur . '" ' .
						'onKeyDown="controleSaisieChamp( event, ' . $Systemes['mail_sender']->prs_id . ');" ' .
						'onBlur="sauverParametre(' . $Systemes['mail_sender']->prs_id . ');">' .
					 '</div>' .
					'</div>';
				}


				if ( array_key_exists( 'mail_receiver', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-7 g-2 mb-2">' .
						  '<label class="form-label">' . $L_A . '</label>' .
						  '<input class="form-control" type="text" maxlength="60" id="prs_valeur-' . $Systemes['mail_receiver']->prs_id .
							'" data-old="' . $Systemes['mail_receiver']->prs_valeur . '" value="' . $Systemes['mail_receiver']->prs_valeur . '" ' .
							'onKeyDown="controleSaisieChamp( event, ' . $Systemes['mail_receiver']->prs_id . ');" ' .
							'onBlur="sauverParametre(' . $Systemes['mail_receiver']->prs_id . ');">' .
						 '</div>' .
						'</div>';
				}


				if ( array_key_exists( 'mail_title', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-7 g-2 mb-2">' .
						  '<label class="form-label">' . $L_Titre . '</label>' .
						  '<input class="form-control" type="text" maxlength="60" id="prs_valeur-' . $Systemes['mail_title']->prs_id .
							'" data-old="' . $Systemes['mail_title']->prs_valeur . '" value="' . $Systemes['mail_title']->prs_valeur . '" ' .
							'onKeyDown="controleSaisieChamp( event, ' . $Systemes['mail_title']->prs_id . ');" ' .
							'onBlur="sauverParametre(' . $Systemes['mail_title']->prs_id . ');">' .
						 '</div>' .
						'</div>';
				}


				if ( array_key_exists( 'mail_body_type', $Systemes ) ) {
					$Texte_HTML .=
					'<div class="row">' .
					 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
					 '<div class="col-lg-4 g-2 mb-2">' .
					  '<label class="form-label">' . $L_Type_Corps_Courriel . '</label>' .
					  '<select class="form-select" id="prs_valeur-' . $Systemes['mail_body_type']->prs_id .
					    '" data-old="' . $Systemes['mail_body_type']->prs_valeur . '" ' .
					    'onChange="sauverParametre(' . $Systemes['mail_body_type']->prs_id . ');">' .
					   '<option value="HTML"' . $Option_HTML . '>HTML</option>' .
					   '<option value="TEXT"' . $Option_TEXT . '>' . $L_Texte . '</option>' .
					  '</select>' .
					 '</div>' .
					'</div>';
				}


				if ( array_key_exists( 'mail_template', $Systemes ) ) {
					$Texte_HTML .=
					'<div class="row">' .
					 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
					 '<div class="col-lg-6 g-2 mb-2">' .
					  '<label class="form-label">' . $L_Fichier_Squelette . '</label>' .
					  '<div class="input-group">' .
					   '<input class="form-control" type="text" maxlength="60" id="prs_valeur-' . $Systemes['mail_template']->prs_id .
					    '" data-old="' . $Systemes['mail_template']->prs_valeur . '" value="' . $Systemes['mail_template']->prs_valeur . '" ' .
					    'onKeyDown="controleSaisieChamp(event, ' . $Systemes['mail_template']->prs_id . ');" ' .
					    'onBlur="sauverParametre(' . $Systemes['mail_template']->prs_id . ');">' .
					   '<span class="input-group-text"><button class="btn btn-outline-secondary btn-sm" id="edit-mail-body" disabled><i class="bi-pencil-square"></i></button></span>' .
					  '</div>' .
					 '</div>' .
					'</div>';
				}
				break;


			 case 'connexion':
				$Option_Base = '';
				$Option_LDAP = '';

				$Base_Selectionnee = ' disabled ';
				$LDAP_Selectionnee = ' disabled ';

				if ( array_key_exists( 'authentification_type', $Systemes ) ) {
					switch( mb_strtoupper( $Systemes['authentification_type']->prs_valeur ) ) {
					 case 'D':
						$Option_Base = ' checked';
						$Base_Selectionnee = '';
						break;

					 case 'L':
						$Option_LDAP = ' checked';
						$LDAP_Selectionnee = '';
						break;
					}
				}


				$Complexite_1 = '';
				$Complexite_2 = '';
				$Complexite_3 = '';
				$Complexite_4 = '';

				if ( array_key_exists( 'password_complexity', $Systemes ) ) {
					switch( $Systemes['password_complexity']->prs_valeur ) {
					 case '1':
						$Complexite_1 = ' selected';
						break;

					 case '2':
						$Complexite_2 = ' selected';
						break;

					 case '3':
						$Complexite_3 = ' selected';
						break;

					 case '4':
						$Complexite_4 = ' selected';
						break;
					}
			 	}


				if ( array_key_exists( 'expiration_time', $Systemes ) ) {
					$Texte_HTML .=
					'<div class="row">' .
					 '<div class="col-lg-4 g-2 mb-2">' .
					  '<label for="prs_valeur-' . $Systemes['expiration_time']->prs_id . '" class="form-label">' . $L_Temps_Avant_Expiration . '</label>' .
					 '</div>' .
					 '<div class="col-lg-2 g-2 mb-2">' .
					  '<input type="number" class="form-control" ' .
					   'id="prs_valeur-' . $Systemes['expiration_time']->prs_id . '" ' .
					   'data-old="' . $Systemes['expiration_time']->prs_valeur . '" ' .
					   'value="' . $Systemes['expiration_time']->prs_valeur . '" ' .
					   'onKeyDown="controleSaisieChamp(event, ' . $Systemes['expiration_time']->prs_id . ');" ' .
					   'onBlur="sauverParametre(' . $Systemes['expiration_time']->prs_id . ');">' .
					 '</div>' .
					'</div>';
			 	}


		 		if ( array_key_exists( 'root_alternative_boot', $Systemes ) ) {
		 			if ( mb_strtoupper( $Systemes['root_alternative_boot']->prs_valeur ) == 'true' ) {
		 				$Connexion_Alertnative_Root = ' checked';
		 			} else {
		 				$Connexion_Alertnative_Root = '';
		 			}


					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-5 g-2 mb-2">' .
						  '<div class="form-check">' .
						   '<input type="checkbox" class="form-check-input" ' .
						    'id="prs_valeur-' . $Systemes['root_alternative_boot']->prs_id . '" ' .
						    'data-old="' . $Systemes['root_alternative_boot']->prs_valeur . '" ' .
						    'data-name="root_alternative_boot" ' .
						    $Connexion_Alertnative_Root . ' ' . //$Base_Selectionnee .
						    'onChange="sauverParametre(' . $Systemes['root_alternative_boot']->prs_id . ');">' .
						   '<label for="prs_valeur-' . $Systemes['root_alternative_boot']->prs_id . '" class="form-check-label">' . $L_Connexion_Alertnative_Root . '</label>' .
						  '</div>' . 
						 '</div>' .
						'</div>';
				}


				if ( $_SESSION['idn_super_admin'] == true || $Systemes['authentification_type']->prs_valeur == 'D' ) {
					if ( array_key_exists( 'authentification_type', $Systemes ) ) {
						$Texte_HTML .=
						'<div class="row my-separator">' .
						 '<div class="col-lg-4 g-2 mb-2">' .
						  '<label for="prs_valeur-' . $Systemes['authentification_type']->prs_id . '" class="form-label fw-bold">' . $L_Authentification_SGBD . '</label>' .
						 '</div>' .
						 '<div class="col-lg-8 g-2 mb-2">' .
						  '<div class="form-check">' .
						   '<input type="radio" id="prs_valeur-' . $Systemes['authentification_type']->prs_id . '" ' .
						    'class="form-check-input" ' .
						    'data-old="' . $Systemes['authentification_type']->prs_valeur . '" ' .
						    'data-name="authentification_type" ' .
						    'value="D" name="authentification_type" ' .
						    'onChange="sauverParametre(' . $Systemes['authentification_type']->prs_id . ');" ' . $Option_Base . '>' .
						    '<label for="prs_valeur-' . $Systemes['authentification_type']->prs_id . '" class="form-check-label">' .
						    $L_Service_Active .
						    '</label>' .
						  '</div>' .
						 '</div>' .
						'</div>';
					}


					if ( array_key_exists( 'min_password_size', $Systemes ) ) {
						$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['min_password_size']->prs_id . '" class="col-lg-8 g-2 form-label">' . $L_Longueur_MdP . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-2 mb-2">' .
						  '<input type="number" class="form-control" ' .
						     'id="prs_valeur-' . $Systemes['min_password_size']->prs_id . '" ' .
						     'data-old="' . $Systemes['min_password_size']->prs_valeur . '" ' .
						     'data-name="min_password_size" ' .
						     'value="' . $Systemes['min_password_size']->prs_valeur . '" ' . $Base_Selectionnee .
						     'onKeyDown="controleSaisieChamp(event, ' . $Systemes['min_password_size']->prs_id . ');" ' .
						     'onBlur="sauverParametre(' . $Systemes['min_password_size']->prs_id . ');">' .
						 '</div>' .
						'</div>';
					}


					if ( array_key_exists( 'password_complexity', $Systemes ) ) {
						$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['password_complexity']->prs_id . '" class="col-lg-8 g-2 form-label">' . $L_Complexite_MdP . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-8 mb-2">' .
						  '<select class="form-select" ' .
						    'id="prs_valeur-' . $Systemes['password_complexity']->prs_id . '" ' .
						    'data-old="' . $Systemes['password_complexity']->prs_valeur . '" ' .
						    'data-name="password_complexity" ' . $Base_Selectionnee .
						    'onChange="sauverParametre(' . $Systemes['password_complexity']->prs_id . ');">' .
						   '<option value="1"' . $Complexite_1 . '>' . $L_Complexite_1 . '</option>' .
						   '<option value="2"' . $Complexite_2 . '>' . $L_Complexite_2 . '</option>' .
						   '<option value="3"' . $Complexite_3 . '>' . $L_Complexite_3 . '</option>' .
						   '<option value="4"' . $Complexite_4 . '>' . $L_Complexite_4 . '</option>' .
						  '</select>' .
						 '</div>' .
						'</div>';
					}
	
					
					if ( array_key_exists( 'account_lifetime', $Systemes ) ) {
						$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['account_lifetime']->prs_id . '" class="form-label col-lg-8 g-2">' . $L_Duree_Vie_Utilisateur . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-2 mb-2">' .
						  '<input type="number" class="form-control" ' .
						   'id="prs_valeur-' . $Systemes['account_lifetime']->prs_id . '" ' .
						   'data-old="' . $Systemes['account_lifetime']->prs_valeur . '" ' .
						   'data-name="account_lifetime" ' .
						   'value="' . $Systemes['account_lifetime']->prs_valeur . '" ' . $Base_Selectionnee .
						   'onKeyDown="controleSaisieChamp(event, ' . $Systemes['account_lifetime']->prs_id . ');" ' .
						   'onBlur="sauverParametre(' . $Systemes['account_lifetime']->prs_id . ');">' .
						 '</div>' .
						'</div>';
					}


					if ( array_key_exists( 'max_attempt', $Systemes ) ) {
						$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['max_attempt']->prs_id . '" class="form-label col-lg-8 g-2">' . $L_Nombre_Maximum_Tentative . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-2 mb-2">' .
						  '<input type="number" class="form-control" ' .
						   'id="prs_valeur-' . $Systemes['max_attempt']->prs_id . '" ' .
						   'data-name="max_attempt" ' .
						   'data-old="' . $Systemes['max_attempt']->prs_valeur . '" ' .
						   'value="' . $Systemes['max_attempt']->prs_valeur . '" ' . $Base_Selectionnee .
						   'onKeyDown="controleSaisieChamp(event, ' . $Systemes['max_attempt']->prs_id . ');" ' .
						   'onBlur="sauverParametre(' . $Systemes['max_attempt']->prs_id . ');">' .
						 '</div>' .
						'</div>';
					}


					if ( array_key_exists( 'default_password', $Systemes ) ) {
						$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['default_password']->prs_id . '" class="form-label col-lg-8 g-2">' . $L_MdP_Defaut . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-8 mb-2">' .
						  '<input type="text" class="form-control" maxlength="60" ' .
						   'id="prs_valeur-' . $Systemes['default_password']->prs_id . '" ' .
						   'data-name="default_password" ' .
						   'data-old="' . $Systemes['default_password']->prs_valeur . '" ' .
						   'value="' . $Systemes['default_password']->prs_valeur . '" ' . $Base_Selectionnee .
						   'onKeyDown="controleSaisieChamp(event, ' . $Systemes['default_password']->prs_id . ');" ' .
						   'onBlur="sauverParametre(' . $Systemes['default_password']->prs_id . ');">' .
						 '</div>' .
						'</div>';
					}
				}


				if ( array_key_exists( 'authentification_type', $Systemes ) && $_SESSION['idn_super_admin'] == true ) {
					$Texte_HTML .=
						'<div class="row my-separator">' .
						 '<div class="col-lg-4 g-2 mb-2">' .
						  '<label for="prs_valeur-' . $Systemes['authentification_type']->prs_id . '" class="form-label fw-bold">' . $L_Authentification_LDAP . '</label>' .
						 '</div>' .
						 '<div class="col-lg-8 g-2 mb-2">' .
						 ' <div class="form-check">' .
						   '<input type="radio" id="prs_valeur-' . $Systemes['authentification_type']->prs_id . '" ' .
						    'class="form-check-input" ' .
						    'data-old="' . $Systemes['authentification_type']->prs_valeur . '" ' .
						    'value="L" name="authentification_type" ' .
						    'onChange="sauverParametre(' . $Systemes['authentification_type']->prs_id . ');" ' . $Option_LDAP . '>' .
						 '<label class="form-check-label">' . $L_Service_Active . '</label></div></div>' .
						'</div>';
				}


				if ( array_key_exists( 'ldap_ip_address', $Systemes ) ) {
					$Texte_HTML .=
					'<div class="row">' .
					 '<div class="col-lg-4 g-2">&nbsp;</div>' .
					 '<label for="prs_valeur-' . $Systemes['ldap_ip_address']->prs_id . '" class="form-label col-lg-8 g-2">' . $L_Adresse_IP_LDAP . '</label>' .
					 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
					 '<div class="col-lg-4 mb-2">' .
					  '<input type="text" class="form-control" maxlength="60" ' .
					   'id="prs_valeur-' . $Systemes['ldap_ip_address']->prs_id . '" ' .
					   'data-name="ldap_ip_address" ' .
					   'data-old="' . $Systemes['ldap_ip_address']->prs_valeur . '" ' .
					   'value="' . $Systemes['ldap_ip_address']->prs_valeur . '" ' . $LDAP_Selectionnee .
					   'onKeyDown="controleSaisieChamp(event, ' . $Systemes['ldap_ip_address']->prs_id . ');" ' .
					   'onBlur="sauverParametre(' . $Systemes['ldap_ip_address']->prs_id . ');">' .
					 '</div>' .
					'</div>';
				}


				if ( array_key_exists( 'ldap_ssl', $Systemes ) ) {
					if ( mb_strtoupper( $Systemes['ldap_ssl']->prs_valeur ) == 'true' ) $Connexion_LDAP_SSL = ' checked';
					else $Connexion_LDAP_SSL = '';
						
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-8 g-2 mb-2">' .
						  '<div class="form-check">' .
						   '<input type="checkbox" ' .
						    'id="prs_valeur-' . $Systemes['ldap_ssl']->prs_id . '" ' .
						    'class="form-check-input" ' .
						    'data-name="ldap_ssl" ' . $LDAP_Selectionnee .
						    'data-old="' . $Systemes['ldap_ssl']->prs_valeur . '" ' . $Connexion_LDAP_SSL . ' ' .
						    'onChange="sauverParametre(' . $Systemes['ldap_ssl']->prs_id . ');">' .
						   '<label for="prs_valeur-' . $Systemes['ldap_ssl']->prs_id . '" class="form-check-label" for="prs_valeur-' . $Systemes['ldap_ssl']->prs_id . '">' . $L_LDAP_SSL . '</label>' .
						  '</div>' .
						 '</div>' .
						'</div>';
				}


				if ( array_key_exists( 'ldap_ip_port', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['ldap_ip_port']->prs_id . '" class="form-label col-lg-8 g-2">' . $L_Port_LDAP . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-6 mb-2">' .
						  '<input type="number" class="form-control" maxlength="60" ' .
						   'id="prs_valeur-' . $Systemes['ldap_ip_port']->prs_id . '" ' .
						   'data-name="ldap_ip_port" ' .
						   'data-old="' . $Systemes['ldap_ip_port']->prs_valeur . '" ' .
						   'value="' . $Systemes['ldap_ip_port']->prs_valeur . '" ' . $LDAP_Selectionnee .
						   'onKeyDown="controleSaisieChamp(event, ' . $Systemes['ldap_ip_port']->prs_id . ');" ' .
						   'onBlur="sauverParametre(' . $Systemes['ldap_ip_port']->prs_id . ');">' .
						 '</div>' .
						'</div>';
				}


				if ( array_key_exists( 'ldap_protocol_version', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['ldap_protocol_version']->prs_id . '" class="form-label col-lg-8 g-2">' . $L_Version_Protocol_LDAP . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-2 mb-2">' .
						   '<input type="number" class="form-control" maxlength="60" ' .
						    'id="prs_valeur-' . $Systemes['ldap_protocol_version']->prs_id . '" ' .
							'data-name="ldap_protocol_version" ' .
							'data-old="' . $Systemes['ldap_protocol_version']->prs_valeur . '" ' .
							'value="' . $Systemes['ldap_protocol_version']->prs_valeur . '" ' . $LDAP_Selectionnee .
						    'onKeyDown="controleSaisieChamp(event, ' . $Systemes['ldap_protocol_version']->prs_id . ');" ' .
							'onBlur="sauverParametre(' . $Systemes['ldap_protocol_version']->prs_id . ');">' .
						 '</div>' .
						'</div>';
				}


				if ( array_key_exists( 'ldap_organization', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['ldap_organization']->prs_id . '" class="form-label col-lg-8 g-2">' . $L_Organisation_LDAP . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-8 mb-2">' .
						  '<input type="text" class="form-control" maxlength="60" ' .
						   'id="prs_valeur-' . $Systemes['ldap_organization']->prs_id . '" ' .
						   'data-name="ldap_organization" ' .
						   'data-old="' . $Systemes['ldap_organization']->prs_valeur . '" ' .
						   'value="' . $Systemes['ldap_organization']->prs_valeur . '" ' . $LDAP_Selectionnee .
						   'onKeyDown="controleSaisieChamp(event, ' . $Systemes['ldap_organization']->prs_id . ');" ' .
						   'onBlur="sauverParametre(' . $Systemes['ldap_organization']->prs_id . ');">' .
						 '</div>' .
						'</div>';
				}


				if ( array_key_exists( 'ldap_rdn_prefix', $Systemes ) ) {
					$Texte_HTML .=
						'<div class="row">' .
						 '<div class="col-lg-4 g-2">&nbsp;</div>' .
						 '<label for="prs_valeur-' . $Systemes['ldap_rdn_prefix']->prs_id . '" class="form-label col-lg-8 g-2" for="prs_valeur-' . $Systemes['ldap_rdn_prefix']->prs_id . '">' . $L_Prefixe_RDN_LDAP . '</label>' .
						 '<div class="col-lg-4 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-8 mb-2">' .
						  '<input type="text" class="form-control" maxlength="60" ' .
						   'id="prs_valeur-' . $Systemes['ldap_rdn_prefix']->prs_id . '" ' .
						   'data-name="ldap_rdn_prefix" ' .
						   'data-old="' . $Systemes['ldap_rdn_prefix']->prs_valeur . '" ' .
						   'value="' . $Systemes['ldap_rdn_prefix']->prs_valeur . '" ' . $LDAP_Selectionnee .
						   'onKeyDown="controleSaisieChamp(event, ' . $Systemes['ldap_rdn_prefix']->prs_id . ');" ' .
						   'onBlur="sauverParametre(' . $Systemes['ldap_rdn_prefix']->prs_id . ');">' .
						 '</div>' .
						'</div>' .

						'<div class="row">' .
						 '<div class="col-lg-4 g-2 mb-2">&nbsp;</div>' .
						 '<div class="col-lg-6 g-2 mb-2">' .
						  '<button class="btn btn-outline-secondary" id="generer_conf_ldap">' .
						   $L_Bouton_Generer_Conf_LDAP .
						  '</button>' .
						 '</div>' .
						'</div>';
				}
				break;

			 case 'limitations':
				foreach ($Systemes as $Occurrence) {
					$Texte_HTML .= '<div class="row" id="SPR_' . $Occurrence->prs_id . '">' .
						'<div class="col-lg-5">' .
						'<span>' . $Occurrence->prs_commentaire . '</span>' .
						'</div>';

					if ( $Droit_Modifier === true ) {
						switch ( $Occurrence->prs_type ) {
							case 0: // Boîte à cocher
								if ( $Occurrence->prs_valeur == 'false' ) {
									$Coche = '';
								} else {
									$Coche = 'checked';
								}

								$Texte_HTML .= '<div class="col-lg-1">' .
									'<input class="form-control" type="checkbox" id="prs_valeur-' . $Occurrence->prs_id .
									'" data-old="' . $Occurrence->prs_valeur . '" ' .
									'onClick="sauverParametre(' . $Occurrence->prs_id . ');" ' . $Coche . '>';
								break;

							case 1: // Saisie d'un numérique
								$Texte_HTML .= '<div class="col-lg-1">' .
									'<input class="col-lg-2 form-control" type="number" id="prs_valeur-' . $Occurrence->prs_id .
									'" data-old="' . $Occurrence->prs_valeur . '" value="' . $Occurrence->prs_valeur . '" ' .
									'onKeyDown="controleSaisieChamp( event, ' . $Occurrence->prs_id . ');" ' .
									'onBlur="sauverParametre(' . $Occurrence->prs_id . ');">';
								break;

							case 2: // Saisie d'une chaine.
							default:
								$Texte_HTML .= '<div class="col-lg-7">' .
									'<input class="form-control" type="text" maxlength="60" id="prs_valeur-' . $Occurrence->prs_id .
									'" data-old="' . $Occurrence->prs_valeur . '" value="' . $Occurrence->prs_valeur . '" ' .
									'onKeyDown="controleSaisieChamp( event, ' . $Occurrence->prs_id . ');" ' .
									'onBlur="sauverParametre(' . $Occurrence->prs_id . ');">';
								break;
						}
					} else {
						if ( $Occurrence->prs_valeur == '' ) $Occurrence->prs_valeur = '&nbsp;';
						$Texte_HTML .= '<span>' . $Occurrence->prs_valeur . '</span>';
					}

					$Texte_HTML .= '</div>' .
						'</div>';
				}

				break;
			}

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
				'ctrl_mdp' => 0
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


 case 'AJAX_Generer_Conf_LDAP':
	if ( $Droit_Ajouter === true ) {
		if ( ! isset( $_POST['ldap_ip_address'] )
			|| ! isset( $_POST['ldap_ip_port'] )
			|| ! isset( $_POST['ldap_protocol_version'] )
			|| ! isset( $_POST['ldap_organization'] )
			|| ! isset( $_POST['ldap_rdn_prefix'] )
			|| ! isset( $_POST['ldap_ssl'] )
		) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Field_Mandatory
				) );
	
			exit();
		}
	
	
		if ( $_POST['ldap_ssl'] == 'true' ) {
			$_POST['ldap_ssl'] = 'true';
		} else {
			$_POST['ldap_ssl'] = 'false';
		}
	
	
		try {
			$P_Fichier_Conf = fopen( DIR_RESTREINT . '/Config_LDAP.inc.php', 'w' );
	
			fwrite( $P_Fichier_Conf,
				"<?php\n" .
				"\n" .
				"/**\n" .
				"* Définit les variables permettant de gérer les authentifications via LDAP.\n" .
				"*\n" .
				"* @license Copyright Loxense\n" .
				"* @author Pierre-Luc MARY\n" .
				"* @date " . date( 'Y-m-d') . "\n" .
				"*\n" .
				"*/\n" .
				"\n" .
				"\$_LDAP_Server = '" . $_POST['ldap_ip_address'] . "'; // IP address server or server name\n" .
				"\$_LDAP_Port = '" . $_POST['ldap_ip_port'] . "'; // IP port server\n" .
				"\$_LDAP_Protocol_Version = '" . $_POST['ldap_ip_address'] . "'; // Protocol version\n" .
				"\$_LDAP_Organization = '" . $_POST['ldap_organization'] . "'; // Organization tree\n" .
				"\$_LDAP_RDN_Prefix = '" . $_POST['ldap_rdn_prefix'] . "'; // RDN prefix\n" .
				"\$_LDAP_SSL = '" . $_POST['ldap_ssl'] . "'; // SSL protocol\n" .
				"\n" .
				"?>\n"
		 	);
	
		 	fclose( $P_Fichier_Conf );
	
			$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_PARAMETRE', $L_Fichier_Conf_LDAP_Genere );
	
			echo json_encode( array(
				'statut' => 'success',
				'texteMsg' => $L_Fichier_Conf_LDAP_Genere
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


 case 'AJAX_Controler_MdP':
	if ( $Droit_Ajouter === true) {
		$Texte_Statut = $L_Mot_Passe_Invalide;
		$Statut = 'error';
		$Salt = 'loxense_2016_loxense';
	
		if ( isset( $_POST['mdp_limitation_tmp'] ) ) {
			if ( file_exists( $MdP_Limitation ) ) {
				$PF_MdP = fopen( $MdP_Limitation, 'r' );
				$Tmp_MdP = fread( $PF_MdP, 100 );
				fclose( $PF_MdP );
	
				$Tmp_MdP = str_replace( array("\r","\n"), array('',''), $Tmp_MdP );
	
				if ( hash( 'sha256', $_POST['mdp_limitation_tmp'] . $Salt ) == $Tmp_MdP ) {
					$_SESSION['MdP_Limitation'] = true;
					$Statut = 'success';
					$Texte_Statut = '';
				} else {
					$_SESSION['MdP_Limitation'] = false;
				}
			}
		}

		echo json_encode( array(
			'statut' => $Statut,
			'texteMsg' => $Texte_Statut,
			'total' => 0,
			'droit_modifier' => $Droit_Modifier,
			'droit_supprimer' => $Droit_Supprimer
			) );
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