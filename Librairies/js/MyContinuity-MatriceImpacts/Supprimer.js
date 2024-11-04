// ============================================
// Fonctions répondant aux événements écoutés.


// Créer ou Modifier le Niveau d'Impact dans la BDD et à l'écran.
function ModalSuppNiveau() {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function(reponse) {
			tmp_poids = $('.niveau-impact').length;
			tmp_texte = $('div.row.niveau-impact[data-poids="'+tmp_poids+'"] button').text();
			nim_id = $('div.row.niveau-impact[data-poids="'+tmp_poids+'"]').attr('data-nim_id');

			Corps = '<p>' +
				reponse['L_Confirmer_Suppression_Niveau_Impact'].replace('%s', '<span class="fg_couleur_3">'+tmp_texte+'</span>') +
				'</p>';

			construireModal('idModalSupprimer',
				reponse['L_Supprimer_Niveau_Impact'],
				Corps,
				'idBoutonSupprimer', reponse['L_Supprimer'],
				true, reponse['L_Fermer'],
				'formSupprimerNiveau', 'modal-lg');

			// Affiche la modale qui vient d'être créée
			$('#idModalSupprimer').modal('show');

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalSupprimer').on('shown.bs.modal', function() {
				// Attend que la modale soit affichée avant de donner le focus au champ.
				$('#idBoutonSupprimer').focus();
			});

			$('#idModalSupprimer').on('hidden.bs.modal', function() {
				$('#idModalSupprimer').remove(); // Supprime la modale d'ajout.
			});

			$('#formSupprimerNiveau').submit(function(event) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				supprimerNiveau( nim_id, tmp_texte );
			});

		}
	});

}


// Supprimer le Niveau d'Impact dans la BDD et à l'écran.
function supprimerNiveau( nim_id, nim_libelle ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Supprimer_Niveau',
		type: 'POST',
		data: $.param({'nim_id': nim_id, 'nim_libelle': nim_libelle}),
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse[ 'statut' ];
			var texteMsg = reponse[ 'texteMsg' ];

			if ( statut == 'success' ) {
				charger();
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});

	$('.modal').modal('hide');
}


// Créer ou Modifier le Type d'Impact dans la BDD et à l'écran.
function ModalSuppType() {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function(reponse) {
			tmp_poids = $('.type-impact').length;
			tmp_texte = $('.type-impact[data-tim_poids="'+tmp_poids+'"] button').text();
			tim_id = $('.type-impact[data-tim_poids="'+tmp_poids+'"]').attr('data-tim_id');

			Corps = '<p>' +
				reponse['L_Confirmer_Suppression_Type_Impact'].replace('%s', '<span class="fg_couleur_3">'+tmp_texte+'</span>') +
				'</p>';

			construireModal('idModalSupprimer',
				reponse['L_Supprimer_Type_Impact'],
				Corps,
				'idBoutonSupprimer', reponse['L_Supprimer'],
				true, reponse['L_Fermer'],
				'formSupprimerType', 'modal-lg');

			// Affiche la modale qui vient d'être créée
			$('#idModalSupprimer').modal('show');

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalSupprimer').on('shown.bs.modal', function() {
				// Attend que la modale soit affichée avant de donner le focus au champ.
				$('#idBoutonSupprimer').focus();
			});

			$('#idModalSupprimer').on('hidden.bs.modal', function() {
				$('#idModalSupprimer').remove(); // Supprime la modale d'ajout.
			});

			$('#formSupprimerType').submit(function(event) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				supprimerType( tim_id, tmp_texte );
			});

		}
	});

}


// Supprimer le Type d'Impact dans la BDD et à l'écran.
function supprimerType( tim_id, tim_libelle ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Supprimer_Type',
		type: 'POST',
		data: $.param({'tim_id': tim_id, 'tim_libelle': tim_libelle}),
		dataType: 'json',
		success: function( reponse ) {
			var statut = reponse[ 'statut' ];
			var texteMsg = reponse[ 'texteMsg' ];

			if ( statut == 'success' ) {
				$('[data-tim_id="'+tim_id+'"]').remove();
			}

			afficherMessage( texteMsg, statut, 'body' );
		}
	});

	$('.modal').modal('hide');
}