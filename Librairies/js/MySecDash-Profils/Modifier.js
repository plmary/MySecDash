function activerBoutonsModification() {
	$('div.row.profils a.modifiable').off( 'click' );

	$('div.row.profils a.modifiable').on( 'click', function() {
		var Id_Profil = $(this).parent().attr('data-id');
		var Libelle = protegerQuotes( $(this).parent().text() );

		ModalModifierProfil( Id_Profil, Libelle );
	});
}


function ModalModifierProfil( Id_Profil, Libelle ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			var Corps =
				'<div class="form-group">' +
				'<label class="col-lg-2 form-label" for="prf_libelle">' + reponse[ 'L_Libelle' ] + '</label>' +
				'<div class="col-lg-10">' +
				'<input id="prf_libelle-' + Id_Profil + '" class="form-control" type="text" value="' + Libelle + '" required>' +
				'</div>' +
				'</div>';

			construireModal( 'idModalProfil',
				reponse[ 'Titre1' ],
				Corps,
				'idBoutonAjouter', reponse[ 'L_Modifier' ],
				true, reponse[ 'L_Fermer' ],
				'formModifierProfil' );

			$('#idModalProfil').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalProfil').on('shown.bs.modal', function() {
				$('#prf_libelle-'+Id_Profil).focus();

				// On place le curseur après le dernier caractère.
				document.getElementById('prf_libelle-'+Id_Profil).selectionStart = Libelle.length;
			});

			// Supprime la modale après l'avoir caché.
			$('#idModalProfil').on('hidden.bs.modal', function() {
				$('#idModalProfil').remove();
			});

			$('#formModifierProfil').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				sauverModificationModal( Id_Profil, $('#prf_libelle-'+Id_Profil).val() );
			} );
		}
	});
}


function ModifierProfil( Id_Profil ) {
	var prf_libelle = $('#prf_libelle').val();
	var prf_description = $('#prf_description').val();


	var Liste_Droits_Ajouter = [];

	$('a.droit').each(function(index, element){
		if ($(element).hasClass('desactive') == false ) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_Droits_Ajouter.push($(element).attr('id'));
			}
		}
	});


	var Liste_Droits_Supprimer = [];

	$('a.droit').each(function(index, element){
		if ($(element).hasClass('desactive') == true ) {
			if ( $(element).attr('data-old_value') == 1) {
				Liste_Droits_Supprimer.push($(element).attr('id'));
			}
		}
	});

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Modifier_Profil',
		type: 'POST',
		async: false,
		dataType: 'json', // le résultat est transmit dans un objet JSON
		data: $.param({'prf_id': Id_Profil, 'prf_libelle': prf_libelle, 'prf_description': prf_description,
			'Liste_Droits_Ajouter': Liste_Droits_Ajouter, 'Liste_Droits_Supprimer': Liste_Droits_Supprimer}),
		success: function( reponse ){
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if( statut == 'success' ){
				$('div#PRF_'+Id_Profil+' div[data-src="prf_libelle"] span').text( prf_libelle );
				$('div#PRF_'+Id_Profil+' div[data-src="prf_description"] span').text( prf_description );

				// Supprime la fenêtre modale.
				$('#idModalProfil').modal('hide');

				afficherMessage( texteMsg, statut, 'body' );
			} else {
				afficherMessage( texteMsg, statut, '#idModalProfil', 0, 'n' );
			}
		}
	});
}
