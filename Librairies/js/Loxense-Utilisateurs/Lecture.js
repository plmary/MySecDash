$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});
});


function trier( myElement, changerTri ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Trier',
		type: 'POST',
		data: $.param({'trier': sens_recherche}),
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ){
			var statut = reponse['statut'];

			if( statut == 'success' ){
				var texteMsg = reponse['texteHTML'];

				$('div#corps_tableau').html( reponse[ 'texteHTML'] );

				if ( changer_tri == true ) {
					var Element = sens_recherche.split('-');
					if ( Element[ Element.length - 1 ] == 'desc' ) {
						sens_recherche = Element[ 0 ];
					} else {
						sens_recherche = Element[ 0 ] + '-desc';
					}
				}

				// Postionne la couleur sur la colonne active sur le tri.
				$('div#entete_tableau div.row div.triable').removeClass('active');
				$(myElement).addClass('active');

				$(myElement).attr( 'data-sens-tri', sens_recherche );

				$('#totalOccurrences').text( ajouterZero(reponse[ 'total' ] ) );

				// Vérifie s'il y a une limitation à la création des Entités.
				gererBoutonAjouter( reponse['total'], reponse['limitation'], reponse['libelle_limitation'] );

				if ( reponse[ 'droit_modifier' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Modification
					$('.btn-modifier').click( function( event ){
						var Id = $(this).attr('data-id');

						ModalAjouterModifier( Id );
					});
				}

				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					// Assigne l'événement "click" sur tous les boutons de Suppression
					$('.btn-supprimer').click(function(){
						var Id = $(this).attr('data-id');
						var ent_libelle = $(this).parent().parent().find('div[data-src="ent_libelle"]').find('span').text();
						var cvl_label = $(this).parent().parent().find('div[data-src="cvl_label"]').find('span').text();
						var idn_login = $(this).parent().parent().find('div[data-src="idn_login"]').find('span').text();

						ModalSupprimer( Id, ent_libelle, cvl_label, idn_login );
					});
				}

				$('[data-toggle="tooltip"]').tooltip();

				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
			}
		}
	});
}


function listerCivilitesOptions( Id_Civilite ) {
	var Id_Civilite = Id_Civilite || '';
	var Aucun;

	if ( Id_Civilite == '' ) Aucun = 'oui';
	else Aucun = 'non';

	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Lister_CVL_LABEL',
		type: 'POST',
		dataType: 'json',
		data: $.param({'aucun': Aucun, 'id': Id_Civilite}),
		async: false,
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$('#cvl_id').html(reponse['texteMsg']);
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function listerEntitesOptions( Id_Entite ) {
	var Id_Entite = Id_Entite || '';
	var Aucun;

	if ( Id_Entite == '' ) Aucun = 'oui';
	else Aucun = 'non';

	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Lister_ENT_LABEL',
		type: 'POST',
		dataType: 'json',
		data: $.param({'aucun': Aucun, 'id': Id_Entite}),
		async: false,
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$('#ent_id').html( reponse['texteMsg'] );
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function listerEntitesChecks( Id_Identite ) {
	var Id_Identite = Id_Identite || '';

	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Lister_ENT_LABEL',
		type: 'POST',
		dataType: 'json',
		async: false,
		data: $.param({'type': 'checkbox', 'id': Id_Identite}),
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$( reponse['texteMsg'] ).appendTo( '#onglets_utilisateur' );
				$('#liste-entites').hide();
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function listerProfilsChecks( Id_Identite ) {
	var Id_Identite = Id_Identite || '';
	
	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Lister_PRF_LABEL',
		type: 'POST',
		dataType: 'json',
		async: false,
		data: $.param({'type': 'checkbox', 'id': Id_Identite}),
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$(reponse['texteMsg']).appendTo( '#onglets_utilisateur' );
				$('#liste-profils').hide();
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function listerGestionnairesChecks( Id_Identite ) {
	var Id_Identite = Id_Identite || '';
	
	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Lister_Gestionnaires',
		type: 'POST',
		dataType: 'json',
		async: false,
		data: $.param({'type': 'checkbox', 'id': Id_Identite}),
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$(reponse['texteMsg']).appendTo( '#onglets_utilisateur' );
				$('#liste-gestionnaires').hide();
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'], '#formAjouterUtilisateur' );
			}
		}
	});
}


function listerEtiquettesChecks( Id_Identite ) {
	var Id_Identite = Id_Identite || '';
	
	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Lister_Etiquettes',
		type: 'POST',
		dataType: 'json',
		async: false,
		data: $.param({'type': 'checkbox', 'id': Id_Identite}),
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$(reponse['texteMsg']).appendTo( '#onglets_utilisateur' );
				$('#liste-etiquettes').hide();
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'], '#formAjouterUtilisateur' );
			}
		}
	});
}


function actualiserStatutEntiteSelectionnee( Id ) {
	$('#liste-entites label').removeClass('disabled');
	$('#liste-entites input').removeAttr('disabled');

	$('#chk-ENT-'+Id).parent().addClass('disabled');
	$('#chk-ENT-'+Id).attr('disabled','disabled');
	$('#chk-ENT_ADM-'+Id).parent().addClass('disabled');
	$('#chk-ENT_ADM-'+Id).attr('disabled','disabled');
}


function ModalAjouterModifier( Id ){
	var Username, Super_Admin, Id_Civility, Id_Entity, Attempt, Disable, Email,
		Last_Connection, Expiration_Date, Updated_Authentication, Is_Super_Admin, Liste_Gestionnaires;

	$.ajax({
		url: '../../../Loxense-Utilisateurs.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		data: $.param({'charger': Id}), // les paramètres sont protégés avant envoi
		success: function( reponse ) {
			if ( reponse['Statut'] == 'success' ) {
				Username = reponse['username'] || '';

				if ( reponse['super_admin'] === true ) {
					Super_Admin = ' checked';
				} else {
					Super_Admin = '';
				}

				Id_Civility = reponse['id_civility'];
				Id_Entity = reponse['id_entity'];
				Attempt = reponse['attempt'];
				Disable = reponse['disable'];
				Last_Connection = reponse['last_connection'];
				Expiration_Date = reponse['expiration_date'];
				Updated_Authentication = reponse['updated_authentification'];
				Is_Super_Admin = reponse['is_super_admin'];
				Liste_Gestionnaires = reponse['liste_gestionnaires'];
				Email = reponse['email'];
				if ( Email == null ) Email = '';

				var Corps = '<div id="zone_modifie_identite">' +
					'<div class="row">' +
					'<label class="col-lg-3 col-form-label" for="idn_login">' + reponse[ 'L_Username' ] + '</label>' +
					'<div class="col-lg-4">' +
					'<input id="idn_login" class="form-control" type="text" value="' + protegerQuotes( Username ) + '" required>' +
					'</div>';
				
				if ( Is_Super_Admin == true ) {
					Corps += '<div class="col-lg-5 form-check col-form-label">' +
						'<input  class="form-check-input" id="idn_super_admin" type="checkbox"' + Super_Admin + '>' +
						'<label class="form-check-label">' + reponse[ 'L_Super_Admin' ] + '</label>' +
						'</div>' ;
				}
				
				Corps +='</div>' +
					'<div class="row">' +
					'<label class="col-lg-2 col-form-label" for="cvl_id">' + reponse[ 'L_Civility' ] + '</label>' +
					'<div class="col-lg-9">' +
					'<select id="cvl_id" class="form-select" required>' +
					'</select>' +
					'</div>' +
					'<div class="col-lg-1">' +
					'<button class="btn btn-outline-secondary" id="btn-ajouter-civilite" title="' + reponse[ 'L_Ajouter' ] + '" type="button">' +
					'<span class="bi-plus" aria-hidden="true"></span>' +
					'</button>' +
					'</div>' +
					'</div>' +
					'<div class="row">' +
					'<label class="col-lg-2 col-form-label" for="ent_id">' + reponse[ 'L_Entity' ] + '</label>' +
					'<div class="col-lg-9">' +
					'<select id="ent_id" class="form-select" required>' +
					'</select>' +
					'</div>' +
					'<div class="col-lg-1">' +
					'<button class="btn btn-outline-secondary" id="btn-ajouter-entite" title="' + reponse[ 'L_Ajouter' ] + '" type="button">' +
					'<span class="bi-plus" aria-hidden="true"></span>' +
					'</button>' +
					'</div>' +
					'</div>' +
					'<div class="row">' +
					'<label class="col-lg-2 col-form-label" for="idn_courriel">' + reponse[ 'L_Email' ] + '</label>' +
					'<div class="col-lg-9">' +
					'<input id="idn_courriel" class="form-control" type="email" value="' + protegerQuotes( Email ) + '">' +
					'</div>' +
					'</div>' +
					'<ul class="nav nav-tabs">';

				if ( Id != null ) {
					Corps += '<li><a id="lister_actions" class="nav-link active" href="#">' + reponse[ 'L_Actions'] + '</a></li>';
				}

				Corps += '<li><a id="lister_chk_entites" class="nav-link" href="#">' + reponse[ 'L_Entities'] + '</a></li>' +
					'<li><a id="lister_chk_profils" class="nav-link" href="#">' + reponse[ 'L_Profiles'] + '</a></li>' +
					'<li><a id="lister_chk_gestionnaires" class="nav-link" href="#">' + reponse[ 'L_Gestionnaires'] + '</a></li>' +
					'<li><a id="lister_chk_etiquettes" class="nav-link" href="#">' + reponse[ 'L_Etiquettes'] + '</a></li>' +
					'</ul>' +
					'<div id="onglets_utilisateur">' +
					'</div>' +
					'</div>';

				if ( Id != null ) {
					listerActions( Id, Attempt, Disable, Last_Connection, Expiration_Date, Updated_Authentication );
				}

				if ( Id != null ) {
					Titre_Modal = reponse[ 'Titre1' ];
					Bouton_Primaire = reponse[ 'L_Modifier' ];
				} else {
					Titre_Modal = reponse[ 'Titre' ]
					Bouton_Primaire = reponse[ 'L_Ajouter' ];
				}

				construireModal( 'idModalUtilisateur',
					Titre_Modal,
					Corps,
					'idBoutonModifier', Bouton_Primaire,
					true, reponse[ 'L_Fermer' ],
					'formModifierUtilisateur', 'modal-lg' );

				$('#idModalUtilisateur').modal('show'); // Affiche la modale qui vient d'être créée

				// Attend que la modale soit affichée avant de donner le focus au champ.
				$('#idModalUtilisateur').on('shown.bs.modal', function() {
//					listerActions( Id, Attempt, Disable, Last_Connection, Expiration_Date, Updated_Authentication );
					listerCivilitesOptions( Id_Civility );
					listerEntitesOptions( Id_Entity );
					listerProfilsChecks( Id );
					listerEntitesChecks( Id );
					listerGestionnairesChecks( Id );
					listerEtiquettesChecks( Id );


					$('.nav-tabs a:first').trigger('click');

					$('#idn_login').focus();

					// On place le curseur après le dernier caractère.
					document.getElementById('idn_login').selectionStart = Username.length;
				});

				// Supprime la modale après l'avoir caché.
				$('#idModalUtilisateur').on('hidden.bs.modal', function() {
					$('#idModalUtilisateur').remove();
				});

				// Affiche la liste des actions pouvant être associées.
				$('#lister_actions').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#lister_actions').addClass('active');
					$('div[id^=liste-]').hide();
					$('#liste-actions').show();
					$('div#liste-actions input:first').focus();
				});

				// Affiche la liste des entités pouvant être associées.
				$('#lister_chk_entites').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#lister_chk_entites').addClass('active');
					$('div[id^=liste-]').hide();
					$('#liste-entites').show();

					$('div#liste-entites input:enabled:first').focus();
				});

				// Affiche la liste des profils pouvant être associés.
				$('#lister_chk_profils').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#lister_chk_profils').addClass('active');
					$('div[id^=liste-]').hide();
					$('#liste-profils').show();
					$('div#liste-profils  input:first').focus();
				});

				// Affiche la liste des gestionnaires pouvant être associés.
				$('#lister_chk_gestionnaires').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#lister_chk_gestionnaires').addClass('active');
					$('div[id^=liste-]').hide();
					$('#liste-gestionnaires').show();
					$('div#liste-gestionnaires input:first').focus();
				});

				// Affiche la liste des étiquettes pouvant être associés.
				$('#lister_chk_etiquettes').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#lister_chk_etiquettes').addClass('active');
					$('div[id^=liste-]').hide();
					$('#liste-etiquettes').show();
					$('div#liste-etiquettes input:first').focus();
				});

				// Gère la soumission du formulaire.
				$('#formModifierUtilisateur').submit( function( event ) {
					event.preventDefault(); // Laisse le contrôle au Javascript.

					if ( Id == null ) {
						ajouterUtilisateur();
					} else {
						modifierUtilisateur( Id );
					}
				} );

				// Gère le bouton de création des Entités.
				$('#btn-ajouter-entite').on('click', function() {
					afficherZoneCreationEntite();
				});

				// Gère le bouton de création des Civilités.
				$('#btn-ajouter-civilite').on('click', function() {
					afficherZoneCreationCivilite();
				});

				// Gère le changement d'Entités.
/*				$('#ent_id').on('change', function() {
					var Id = $(this).val();

					actualiserStatutEntiteSelectionnee( Id );
				});*/
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
				return;
			}
		}
	});
}
