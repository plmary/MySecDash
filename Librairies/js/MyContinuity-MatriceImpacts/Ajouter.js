// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
/*	$(".btn-ajouter").on('click', function(){
		ModalMAJCampagne();
	});*/

	// Initialise ou réinitialise une échelle de temps
	$(".btn-initialiser").on('click', function(){
		ModalInitialiserMatriceImpacts();
	});
});



function AjouterNiveau() {
	nim_poids = $('#nim_poids').val();
	nim_numero = $('#nim_numero').val();
	nim_nom_code = $('#nim_nom_code').val();
	nim_couleur = $('#nim_couleur option:selected').attr('data-color').substring(1);
	Occurrence = '';

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Niveau',
		type: 'POST',
		data: $.param({'nim_poids': nim_poids,
			'nim_numero': nim_numero, 'nim_nom_code': nim_nom_code,
			'nim_couleur': nim_couleur}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalMaJNiveau').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				charger();
			} else {
				afficherMessage( texteMsg, statut, '#idModalMaJNiveau', 0, 'n' );
			}
		}
	});
}


function AjouterType() {
	tim_nom_code = $('#tim_nom_code').val();
	tim_poids = $('#tim_poids').val();
	Occurrence = '';

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Type',
		type: 'POST',
		data: $.param({'tim_nom_code': tim_nom_code, 'tim_poids': tim_poids}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {

				$('#idModalMaJType').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				charger();
			} else {
				afficherMessage( texteMsg, statut, '#idModalMaJType', 0, 'n' );
			}
		}
	});
}


function AjouterDescription(nim_id, tim_id) {
	mim_description = $('#mim_description').summernote('code');

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter_Description',
		type: 'POST',
		data: $.param({'nim_id': nim_id, 'tim_id': tim_id, 'mim_description': mim_description}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];
			if ( statut == 'success' ) {
				$('#idModalMaJDescription').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$('#description-'+nim_id+'-'+tim_id).html(mim_description);
			} else {
				afficherMessage( texteMsg, statut, '#idModalMaJDescription', 0, 'n' );
			}
		}
	});
}


function ModalInitialiserMatriceImpacts() {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Verifier_Avant_Initialisation',
		type: 'POST',
		//data: $.param({'ete_id': ete_id}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			Titre = reponse['L_Titre'];
			IdBouton = '';
			Bouton = reponse[ 'L_Bouton' ];
			Statut = reponse[ 'statut']
			Message = reponse[ 'texteMsg'];

			var Corps =
			'<div id="ETE-INIT">' +
			Message +
			'</div>';

			if (Statut == 'success') {
				IdBouton = 'idBoutonAjouter';
			}

			construireModal( 'idModal',
				Titre,
				Corps,
				IdBouton, Bouton,
				true, reponse[ 'L_Fermer' ],
				'formMAJ', 'modal-lg' );

			$('#idModal').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModal').on('shown.bs.modal', function() {
			});

			// Supprime la modale après l'avoir caché.
			$('#idModal').on('hidden.bs.modal', function() {
				$('#idModal').remove();
			});

			$('#formMAJ').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				InitialiserMatriceImpacts();
			} );
		}
	});
}


function InitialiserMatriceImpacts() {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Initialiser_Matrice_Impacts',
		type: 'POST',
		//data: $.param({'cmp_id': cmp_id}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				charger();
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}
