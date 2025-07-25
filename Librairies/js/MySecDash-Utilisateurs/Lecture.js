$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );

	// Active l'écoute du "click" sur les libellés de l'entête du tableau.
	$('.triable').click( function() {
		trier( this, true );
	});

	// Active l'écoute du "select" sur le changement de Société.
	$('#s_sct_id').change( function() {
		var sens_recherche = $( 'div#entete_tableau div.row div:first' ).attr( 'data-sens-tri' );
		var sct_id = $('#s_sct_id').val();

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Selectioner_Societe',
			type: 'POST',
			data: $.param({'trier': sens_recherche, 'sct_id': sct_id}),
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function( reponse ){
				var statut = reponse['statut'];
	
				if( statut == 'success' ){
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );

					trier( $( 'div#entete_tableau div.row div:first'), true );
				} else {
					var texteMsg = reponse['texteMsg'];
	
					afficherMessage( texteMsg, statut );
				}
			}
		});
	});
});


function trier( myElement, changerTri ) {
	// AJAX changeant la valeur du filtre
	var sens_recherche = $( myElement ).attr( 'data-sens-tri' );
	var changer_tri = changerTri || false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Trier',
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
						var sct_nom = $('#s_sct_id option:selected').text();
						var cvl_label = $(this).parent().parent().find('div[data-src="cvl_label"]').find('span').text();
						var idn_login = $(this).parent().parent().find('div[data-src="idn_login"]').find('span').text();

						ModalSupprimer( Id, sct_nom, cvl_label, idn_login );
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
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_CVL_LABEL',
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


function listerSocietesChecks( idn_id ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_SCT',
		type: 'POST',
		dataType: 'json',
		data: $.param({'aucun': '', 'idn_id': idn_id}),
		async: false,
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				$( reponse['texteMsg'] ).appendTo( '#onglets_utilisateur' );
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'] );
			}
		}
	});
}


function listerEntitesChecks( idn_id ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_ENT',
		type: 'POST',
		dataType: 'json',
		async: false,
		data: $.param({'type': 'checkbox', 'idn_id': idn_id}),
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
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_PRF_LABEL',
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
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_Gestionnaires',
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
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_Etiquettes',
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

function activeSociete( sct_nom, sct_id ) {
	if ( $('#'+sct_id).is(':checked') == true ) {
		$.each($('[for^="chk-ENT-"]'), function( cle, valeur){
			if ($(valeur).text().search(sct_nom) >= 0) {
				$('#row_'+$(valeur).attr('for')).removeClass('d-none');
			}
		});
	} else {
		$.each($('[for^="chk-ENT-"]'), function( cle, valeur){
			if ($(valeur).text().search(sct_nom) >= 0) {
				$('#row_'+$(valeur).attr('for')).addClass('d-none');
			}
		});
	}
}

function ModalAjouterModifier( Id ){
	var Username, Super_Admin, Id_Civility, sct_id, Attempt, Disable, Email,
		Last_Connection, Expiration_Date, Updated_Authentication, Is_Super_Admin, Liste_Gestionnaires;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
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

				cvl_id = reponse['cvl_id'];
				if ($('#s_sct_id').prop('tagName') == 'SELECT') {
					s_sct_nom = $('#s_sct_id option:selected').text();
				} else {
					s_sct_nom = $('#s_sct_id').val();
				}
				sct_id = reponse['sct_id'];
				Attempt = reponse['attempt'];
				Disable = reponse['disable'];
				Last_Connection = reponse['last_connection'];
				Expiration_Date = reponse['expiration_date'];
				Updated_Authentication = reponse['updated_authentification'];
				Is_Super_Admin = reponse['is_super_admin'];
				Liste_Gestionnaires = reponse['liste_gestionnaires'];
				Email = reponse['email'];
				if ( Email == null ) Email = '';
				SocietesAutorisees = new Map();

				var Corps = '<div id="zone_modifie_identite">' +
					'<div class="row">' +
					'<label class="col-lg-2 col-form-label" for="idn_login">' + reponse[ 'L_Nom_Utilisateur' ] + '</label>' +
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
					'<div class="row" id="select-cvl_id">' +
					'<label class="col-lg-2 col-form-label" for="cvl_id">' + reponse[ 'L_Civilite' ] + '</label>' +
					'<div class="col-lg-9">' +
					'<select id="cvl_id" class="form-select" required>' +
					'<option value="">(' + reponse['L_Aucune'] + ')</option>';

				for (let Civilite of reponse['Civilites']) {
					if (Civilite.cvl_id == cvl_id) {
						Selected = ' selected';
					} else {
						Selected = '';
					}
					Corps += '<option value="' + Civilite.cvl_id + '"' + Selected + '>' + Civilite.cvl_prenom + ' ' + Civilite.cvl_nom + '</option>';
				}

				Corps += '</select>' +
					'</div>';

				for( let Droit of reponse['Droits_Civilites']['MySecDash-Civilites.php']['rights']) {
					if ( Droit == 'RGH_2' ) {
						Corps += '<div class="col-lg-1">' +
							 '<button class="btn btn-outline-secondary" id="btn-ajouter-civilite" title="' + reponse[ 'L_Ajouter' ] + '" type="button">' +
							  '<span class="bi-plus" aria-hidden="true"></span>' +
							 '</button>' +
							'</div>';
					}
				}

				Corps += '</div> <!-- .row #select-cvl_id -->' +
					'<div class="row d-none" id="insert-cvl_id">' +
					'<div class="input-group">' +
					'<span class="input-group-text" id="addon-wrapping">' + reponse[ 'L_Civilite' ] + '</span>' +
					'<input type="text" class="form-control text-uppercase" placeholder="' + reponse[ 'L_Nom' ] + '" id="cvl_nom">' +
					'<input type="text" class="form-control text-capitalize" placeholder="' + reponse[ 'L_Prenom' ] + '" id="cvl_prenom">' +
					'<button class="btn btn-outline-secondary" type="button" id="btn-creer-civilite">' + reponse['L_Creer'] + '</button>' +
					'<button class="btn btn-primary" type="button" id="btn-fermer-civilite">' + reponse['L_Fermer'] + '</button>' +
					'</div> <!-- .input-group -->' +
					'</div> <!-- .row #insert-cvl_id -->' +

					'<div class="row">' +
					 '<label class="col-lg-2 col-form-label" for="sct_id">' + reponse[ 'L_Societe' ] + '</label>' +
					 '<div class="col-lg-9">' +
					  '<input id="sct_id" class="form-control" data-id="' + reponse['s_sct_id'] + '" value="' + s_sct_nom + '" disabled>' +
					 '</div>' +
					'</div>' +
					'<div class="row">' +
					 '<label class="col-lg-2 col-form-label" for="idn_courriel">' + reponse[ 'L_Courriel' ] + '</label>' +
					 '<div class="col-lg-9">' +
					  '<input id="idn_courriel" class="form-control" type="email" value="' + protegerQuotes( Email ) + '">' +
					 '</div>' +
					'</div>' +
					'<ul class="nav nav-tabs">';

				if ( Id != null ) {
					Corps += '<li><a id="lister_actions" class="nav-link active" href="#">' + reponse[ 'L_Actions'] + '</a></li>';

					listerActions( Id, Attempt, Disable, Last_Connection, Expiration_Date, Updated_Authentication );
				}

				Corps += '<li><a id="lister_chk_societes" class="nav-link" href="#">' + reponse[ 'L_Societes'] + '</a></li>' +
					'<li><a id="lister_chk_entites" class="nav-link" href="#">' + reponse[ 'L_Entites'] + '</a></li>' +
					'<li><a id="lister_chk_profils" class="nav-link" href="#">' + reponse[ 'L_Profiles'] + '</a></li>' +
//					'<li><a id="lister_chk_gestionnaires" class="nav-link" href="#">' + reponse[ 'L_Gestionnaires'] + '</a></li>' +
//					'<li><a id="lister_chk_etiquettes" class="nav-link" href="#">' + reponse[ 'L_Etiquettes'] + '</a></li>' +
					'</ul>' +
					'<div id="onglets_utilisateur">';

				Corps += '<div id="liste-societes" class="liste-interne" style="display: none">' +
					'<div class="row liste">' +
					'<div class="col-lg-8">' +
					'<div class="form-check form-check-inline">' +
					'<input class="form-check-input" type="checkbox" id="tout-cocher-sct">' +
					'<label class="form-check-label fw-bold fg_bleu" for="tout-cocher-sct">' + reponse['L_Tout_Cocher_Decocher'] + '</label>' +
					'</div>' +
					'</div>' +
					'</div>';
				for (let Societe of reponse['Societes']) {
					if (Societe.autorise === null || Societe.autorise === undefined) {
						Old_Value = '0';
						Checked = '';
					} else {
						Old_Value = '1';
						Checked = 'checked';
						SocietesAutorisees.set( Societe.sct_id, Societe.sct_nom );
					}

/*					if (Societe.idsc_admin === null || Societe.idsc_admin === false) {
						Checked_Adm = '';
						Old_Value_Adm = '0';
					} else {
						Checked_Adm = 'checked';
						Old_Value_Adm = '1';
					} */
					
					Corps += '<div class="row liste">' +
						'<div class="col-lg-8">' +
						 '<div class="form-check">' +
						  '<input class="form-check-input" type="checkbox" id="chk-SCT-' + Societe.sct_id + '" ' + Checked + ' data-old="' + Old_Value + '" onchange="activeSociete(\'' + Societe.sct_nom + '\', \'chk-SCT-' + Societe.sct_id +'\');">' +
						  '<label class="form-check-label" for="chk-SCT-' + Societe.sct_id + '">' + Societe.sct_nom + '</label>' +
						 '</div>' +
						'</div>' +
/*						'<div class="col-lg-4">' +
						 '<div class="form-check">' +
						  '<input class="form-check-input" type="checkbox" id="chk-SCT-ADM-' + Societe.sct_id + '" ' + Checked_Adm + ' data-old="' + Old_Value_Adm + '" onchange="activeSociete(\'' + Societe.sct_nom + '\', \'chk-SCT-' + Societe.sct_id +'\');">' +
						  '<label class="form-check-label" for="chk-SCT-ADM-' + Societe.sct_id + '">' + reponse['L_Administrateur'] + '</label>' +
						 '</div>' +
						'</div>' + */
						'</div>';
				}
				Corps += '</div>';

				Corps += '<div id="liste-entites" class="liste-interne" style="display: none">' +
					'<div class="row liste">' +
					'<div class="col-lg-8">' +
					'<div class="form-check form-check-inline">' +
					'<input class="form-check-input" type="checkbox" id="tout-cocher-ent">' +
					'<label class="form-check-label fw-bold fg_bleu" for="tout-cocher-ent">' + reponse['L_Tout_Cocher_Decocher'] + '</label>' +
					'</div>' +
					'</div>' +
					'</div>';
				for (let Entite of reponse['Entites']) {
					if (Entite.autorise === null || Entite.autorise === undefined) {
						Old_Value = '0';
						Checked = '';
					} else {
						Old_Value = '1';
						Checked = 'checked';
					}

/*					if (Entite.iden_admin === null || Entite.iden_admin === false) {
						Old_Value_Adm = '0';
						Checked_Adm = '';
					} else {
						Old_Value_Adm = '1';
						Checked_Adm = 'checked';
					} */

					if ( SocietesAutorisees.has(Entite.sct_id) == true ) {
						Visible = '';
					} else {
						Visible = 'd-none';
					}

					Corps += '<div class="row liste ' + Visible + '" id="row_chk-ENT-' + Entite.ent_id + '">' +
						'<div class="col-lg-8">' +
						 '<div class="form-check">' +
						  '<input class="form-check-input" type="checkbox" id="chk-ENT-' + Entite.ent_id + '" ' + Checked + ' data-old="' + Old_Value + '" data-sct_id="' + Entite.sct_id + '">' +
						  '<label class="form-check-label" for="chk-ENT-' + Entite.ent_id + '">' + Entite.sct_nom + ' - ' + Entite.ent_nom + '</label>' +
						 '</div>' +
						'</div>' +
/*						'<div class="col-lg-4">' +
						 '<div class="form-check">' +
						  '<input class="form-check-input" type="checkbox" id="chk-ENT-ADM-' + Entite.ent_id + '" ' + Checked_Adm + ' data-old="' + Old_Value_Adm + '">' +
						  '<label class="form-check-label" for="chk-ENT-ADM-' + Entite.ent_id + '">' + reponse['L_Administrateur'] + '</label>' +
						 '</div>' +
						'</div>' + */
						'</div>';
				}
				Corps += '</div>';

				Corps += '<div id="liste-profils" class="liste-interne" style="display: none">' +
					'<div class="row liste">' +
					'<div class="col-lg-8">' +
					'<div class="form-check form-check-inline">' +
					'<input class="form-check-input" type="checkbox" id="tout-cocher-prf">' +
					'<label class="form-check-label fw-bold fg_bleu" for="tout-cocher-prf">' + reponse['L_Tout_Cocher_Decocher'] + '</label>' +
					'</div>' +
					'</div>' +
					'</div>';
				for (let Profil of reponse['Profils']) {
					if (Profil.autorise === null || Profil.autorise === undefined) {
						Old_Value = '0';
						Checked = '';
					} else {
						Old_Value = '1';
						Checked = 'checked';
					}

					Corps += '<div class="row liste">' +
						'<div class="col-lg-12">' +
						'<div class="form-check">' +
						'<input class="form-check-input" type="checkbox" id="chk-PRF-' + Profil.prf_id + '"' + Checked + ' data-old="' + Old_Value + '">' +
						'<label class="form-check-label" for="chk-PRF-' + Profil.prf_id + '">' + Profil.prf_libelle + '</label>' +
						'</div>' +
						'</div>' +
						'</div>';
				}
				Corps += '</div>';

				Corps += '</div>' +
					'</div>';

				if ( Id != null ) {
					Titre_Modal = reponse[ 'L_Titre_Modifier' ];
					Bouton_Primaire = reponse[ 'L_Modifier' ];
				} else {
					Titre_Modal = reponse[ 'L_Titre_Ajouter' ]
					Bouton_Primaire = reponse[ 'L_Ajouter' ];
				}

				construireModal( 'idModalUtilisateur',
					Titre_Modal,
					Corps,
					'idBoutonModifier', Bouton_Primaire,
					true, reponse[ 'L_Fermer' ],
					'formModifierUtilisateur', 'modal-xxl' );

				$('#idModalUtilisateur').modal('show'); // Affiche la modale qui vient d'être créée

				// Attend que la modale soit affichée avant de donner le focus au champ.
				$('#idModalUtilisateur').on('shown.bs.modal', function() {
					$('.nav-tabs a:first').trigger('click');

					$('#idn_login').focus();

					// On place le curseur après le dernier caractère.
					document.getElementById('idn_login').selectionStart = Username.length;

					$('#tout-cocher-sct').on( 'click', function() {
						var Checked = $('#tout-cocher-sct').is(':checked');
						if ( Checked ) {
							$('input[id^="chk-SCT-"]').prop('checked', true);
						} else {
							$('input[id^="chk-SCT-"]').prop('checked', false);
						}
					} );

					$('#tout-cocher-ent').on( 'click', function() {
						var Checked = $('#tout-cocher-ent').is(':checked');
						if ( Checked ) {
							$('input[id^="chk-ENT-"]').prop('checked', true);
						} else {
							$('input[id^="chk-ENT-"]').prop('checked', false);
						}
					} );
	
					$('#tout-cocher-prf').on( 'click', function() {
						var Checked = $('#tout-cocher-prf').is(':checked');
						if ( Checked ) {
							$('input[id^="chk-PRF-"]').prop('checked', true);
						} else {
							$('input[id^="chk-PRF-"]').prop('checked', false);
						}
					} );

					$("#btn-fermer-civilite").on( 'click', function() {
						
					} );
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

				// Affiche la liste des sociétés pouvant être associées.
				$('#lister_chk_societes').on('click', function() {
					$('.nav-link').removeClass('active');
					$('a#lister_chk_societes').addClass('active');
					$('div[id^=liste-]').hide();
					$('#liste-societes').show();

					$('div#liste-societes input:enabled:first').focus();
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


				// ==================================
				// Gère la soumission du formulaire.
				$('#formModifierUtilisateur').submit( function( event ) {
					event.preventDefault(); // Laisse le contrôle au Javascript.

					// Récupère les Sociétés qui ont été décochées afin de décocher les Entités rattachées.
					var liste_SCT_a_supprimer = [];
				
					$('input[id^="chk-SCT-"]').each( function( index, element ) {
						e_ID = $(element).attr('id');
						e_OLD = $(element).attr('data-old');

						if ( $(element).is(':checked') == false && e_OLD == '1' ) {
							liste_SCT_a_supprimer.push( e_ID.split('-')[2] );
						}
					});
				
				
					// Décoche les Entités pour lesquelles la Société vient d'être décochée.
					var sct_id;
				
					$('div#liste-entites input[id^="chk-ENT-"]').each( function( Index ) {
						sct_id = $(this).attr('data-sct_id');

						for (let t_sct_id of liste_SCT_a_supprimer) {
							if (t_sct_id == sct_id) {
								$(this).attr('checked', false);
								break;
							}
						}
					});


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
