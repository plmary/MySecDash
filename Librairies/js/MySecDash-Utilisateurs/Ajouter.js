// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalAjouterModifier(null);
	});
});



// ============================================
// Fonctions répondant aux événements écoutés.

function ajouterUtilisateur() {
	var idn_login = $('#idn_login').val();
	var idn_courriel = $('#idn_courriel').val();
	var idn_super_admin = $('#idn_super_admin').is(':checked');
	var sct_id = $('#sct_id').val();
	var cvl_id = $('#cvl_id').val();
	var cvl_label = $('#cvl_id option:selected').text();
	var sct_libelle = $('#sct_id option:selected').text();
	var total = $( '#totalOccurrences' ).text();

	var liste_profils = [];
	var liste_entites = [];
	var liste_societes = [];
	var liste_etiquettes = [];
//	var liste_gestionnaires = [];

	// Récupère toutes les sociétés qui ont été cochés.
	$('div#liste-societes input:checked').each(function( Index ) {
		liste_societes[Index] = $(this).attr('id').split('-')[2];
	});

	// Récupère toutes les entités qui ont été cochées.
	$('div#liste-entites input:checked').each(function( Index ) {
		_Id = $(this).attr('id').split('-')[2];

		if ( $(this).attr('id').split('-')[1].search('ADM') == -1 ) {
			liste_entites[Index] = { 'ent_id': _Id, 'admin': false };			
		} else {
			liste_entites[Index-1] = { 'ent_id': _Id, 'admin': true};
		}
	});

	// Récupère tous les profils qui ont été cochés.
	$('div#liste-profils input:checked').each(function( Index ) {
		liste_profils[Index] = $(this).attr('id').split('-')[2];
	});

	// Récupère tous les gestionnaires qui ont été cochés.
	$('div#liste-etiquettes input:checked').each(function( Index ) {
		liste_etiquettes[Index] = $(this).attr('id').split('-')[2];
	});

	// Récupère tous les gestionnaires qui ont été cochés.
/*	$('div#liste-gestionnaires input:checked').each(function( Index ) {
		liste_gestionnaires[Index] = $(this).attr('id').split('-')[2];
	});*/


	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'idn_login': idn_login, 'idn_super_admin': idn_super_admin, 'cvl_id': cvl_id,
			'sct_id': sct_id, 'sct_libelle': sct_libelle, 'cvl_label': cvl_label, 'idn_courriel': idn_courriel,
			'liste_profils': liste_profils, 'liste_entites': liste_entites, 'liste_societes': liste_societes}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalUtilisateur').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Vérifie s'il y a une limitation à la création des Utilisateurs.
				gererBoutonAjouter( total, reponse['limitation'], reponse['libelle_limitation'] );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == 1 ) {
					$('#IDN_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == 1 ) {
					var sct_nom = $('#s_sct_id option:selected').text();

					$('#IDN_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						ModalSupprimer( reponse[ 'id' ], sct_nom, cvl_label, idn_login  );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModalUtilisateur', 0, 'n' );
			}

			$('[data-toggle="tooltip"').tooltip();
		}
	});
}


function afficherZoneCreationCivilite() {
	$.ajax({
		url: Parameters['URL_BASE'] + '/MySecDash-Utilisateurs.php?Action=AJAX_Libeller', //Civilites
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			$('#insert-cvl_id').removeClass('d-none');
			$('#select-cvl_id').addClass('d-none');

			$('#btn-fermer-civilite').on( 'click', function() {
				fermerZoneAjoutCivilite();
			});

			$('#btn-creer-civilite').on( 'click', function() {
				sauverZoneAjoutCivilite(reponse['L_Field_Mandatory']);
			});

			$('#cvl_nom').focus();
		}
	});
}


function fermerZoneAjoutCivilite() {
	$('#insert-cvl_id').addClass('d-none');
	$('#select-cvl_id').removeClass('d-none');

	$('#insert-cvl_id #cvl_nom').val('');
	$('#insert-cvl_id #cvl_prenom').val('');


	$('btn-fermer-civilite').off( 'click' );
}


function sauverZoneAjoutCivilite(L_Field_Mandatory) {
	var Fermer = true;

	$('#insert-cvl_id input').each( function() {
		if ( $(this).val() == '' ) {
			$(this).focus();

			$(this).attr('data-toggle', 'tooltip').attr('data-placement', 'bottom').attr('title', L_Field_Mandatory);
			$(this).tooltip('show');

			Fermer = false;
			return false;
		}
	});


	if ( Fermer == true ) {
		var Last_Name = $('#cvl_nom').val();
		var First_Name = $('#cvl_prenom').val();

		Last_Name = Last_Name.toUpperCase();

		tmp1 = First_Name;
		First_Name = '';
		
		var tmp2 = tmp1.split(' ');
		for( Count = 0; Count < tmp2.length; Count++ ) {
			if (First_Name != '') First_Name += ' ';
			First_Name += tmp2[Count][0].toUpperCase() + tmp2[Count].substring(1).toLowerCase();
		}

		tmp1 = First_Name;
		First_Name = '';

		var tmp2 = tmp1.split('-');
		for( Count = 0; Count < tmp2.length; Count++ ) {
			if (First_Name != '') First_Name += '-';
			First_Name += tmp2[Count][0].toUpperCase() + tmp2[Count].substring(1).toLowerCase();
		}


		$.ajax({
			url: Parameters['URL_BASE'] + '/MySecDash-Civilites.php?Action=AJAX_Ajouter',
			type: 'POST',
			data: $.param({'last_name': Last_Name, 'first_name': First_Name}), // les paramètres sont protégés avant envoi
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
				var statut = reponse['statut'];
				var texteMsg = reponse['texteMsg'];

				if ( statut == 'success' ) {
					fermerZoneAjoutCivilite();

					$('#cvl_id option').removeAttr( 'selected' );

					$('#cvl_id').append(
						'<option value="' + reponse['id'] + '" selected>' + First_Name + ' ' + Last_Name + '</option>'
					);
				} else {
					afficherMessage( reponse['texteMsg'], reponse['statut'], '#zone_ajout_civilite' );
				}
			}
		});
	}
}


function afficherZoneCreationEntite() {
	var Corps;

	$.ajax({
		url: Parameters['URL_BASE'] + '/MySecDash-Entites.php?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			$('.modal-footer .btn').attr('disabled', 'disabled');
			$('#zone_ajout_identite .btn').attr('disabled', 'disabled');

			Corps = '<div class="zone_ajout_contextuel" id="zone_ajout_entite">' +
				'<h4 class="titre_contextuel">' + reponse[ 'Titre' ] + '</h4>' +
				'<div class="row">' +
				'<label class="col-lg-3 col-form-label" for="ent_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-9">' +
				'<input id="ent_libelle" class="form-control" type="text" required autofocus>' +
				'</div>' +
				'</div>';

			if ( reponse['is_super_admin'] == true ) {
				Corps += '<div class="row mb-3">' +
					'<label class="col-lg-3 form-check-label" for="ent_admin">' + reponse[ 'L_Administrateur' ] + '</label>' +
					'<div class="col-lg-9">' +
					'<input id="ent_admin" class="form-check-input" type="checkbox" required>' +
					'</div>' +
					'</div>';
			}

			Corps += '<div class="text-right">' +
				'<a class="btn btn-outline-secondary" href="javascript:fermerZoneAjoutEntite();">' + reponse['L_Fermer'] + '</a>&nbsp;&nbsp;' +
				'<a class="btn btn-primary" href="javascript:sauverZoneAjoutEntite(\''+reponse['L_Field_Mandatory']+'\');">' + reponse['L_Ajouter'] + '</a>' +
				'</div>' +
				'</div>' ;

			$('.modal-body').prepend( Corps );
		}
	});
}


function fermerZoneAjoutEntite() {
	$('#zone_ajout_entite').remove();
	$('.modal-footer .btn').removeAttr('disabled');
	$('#zone_ajout_identite .btn').removeAttr('disabled');
}


function sauverZoneAjoutEntite( L_Field_Mandatory ) {
	var Fermer = true;

	$('#zone_ajout_entite input[required]').each( function() {
		if ( $(this).val() == '' ) {
			$(this).focus();

			$(this).attr('data-toggle', 'tooltip').attr('data-placement', 'bottom').attr('title', L_Field_Mandatory);
			$(this).tooltip('show');

			Fermer = false;
			return false;
		}
	});


	if ( Fermer == true ) {
		var Libelle = $('#ent_libelle').val();
		var ent_admin = $('#ent_admin').is(':checked');

		$.ajax({
			url: Parameters['URL_BASE'] + '/MySecDash-Entites.php?Action=AJAX_Ajouter',
			type: 'POST',
			data: $.param({'libelle': Libelle,'ent_admin': ent_admin}), // les paramètres sont protégés avant envoi
			dataType: 'json', // le résultat est transmit dans un objet JSON
			success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
				var statut = reponse['statut'];
				var texteMsg = reponse['texteMsg'];

				if ( statut == 'success' ) {
					$('#zone_ajout_entite').remove();
					$('.modal-footer .btn').removeAttr('disabled');
					$('#zone_ajout_identite .btn').removeAttr('disabled');

					$('#ent_id option').removeAttr( 'selected' );

					$('#ent_id').append(
						'<option value="' + reponse['id'] + '" selected>' + Libelle + '</option>'
					);

					$('#liste-entites').append(
						'<div class="row liste">' +
						' <div class="col-lg-8">' +
						'  <div class="form-check">' +
						'   <input class="form-check-input" id="chk-ENT-' + reponse['id'] + '" type="checkbox" data-old="0">' +
						'   <label class="form-check-label">' + Libelle + '</label>' +
						'  </div>' +
						' </div>' +
						' <div class="col-lg-4">' +
						'  <div class="form-check">' +
						'   <input class="form-check-input" id="chk-ENT_ADM-' + reponse['id'] +
						'" type="checkbox" data-old="0">' +
						'   <label class="form-check-label">' + reponse['L_Administrateur'] + '</label>' +
						'  </div>' +
						' </div>' +
						'</div>'
					);

					//actualiserStatutEntiteSelectionnee( reponse['id'] );
				} else {
					afficherMessage( reponse['texteMsg'], reponse['statut'], '#zone_ajout_entite' );
				}
			}
		});
	}
}
