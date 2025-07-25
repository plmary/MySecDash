function ModalSupprimer( Id, Libelle ) {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		data: $.param({'id': Id, 'libelle': Libelle, 'action': 'supprimer'}), // les paramètres sont protégés avant envoi
		dataType: 'json',
		success: function( reponse ) {
			var Corps =
				'<div id="PRF-SUPR">' +
				reponse['texteMsg'] +
				'</div>';

			construireModal( 'idModalSupprimer',
				reponse[ 'Titre2' ],
				Corps,
				'idBoutonSupprimer', reponse[ 'L_Supprimer' ],
				true, reponse[ 'L_Fermer' ],
				'formSupprimer' );

			$('#idModalSupprimer').modal('show'); // Affiche la modale qui vient d'être créée

			// Attend que la modale soit affichée avant de donner le focus au champ.
			$('#idModalSupprimer').on('shown.bs.modal', function() {
				$('#idBoutonSupprimer').focus();
			});

			// Supprime la modale après l'avoir caché.
			$('#idModalSupprimer').on('hidden.bs.modal', function() {
				$('#idModalSupprimer').remove();
			});

			$('#formSupprimer').submit( function( event ) { // Gère la soumission du formulaire.
				event.preventDefault(); // Laisse le contrôle au Javascript.

				supprimerProfil( Id, Libelle );
			});
		}
	});
}


function activerBoutonsSuppression() {
	$('.row.profils button.bi-x-circle').off( 'click' );

	$('.row.profils button.bi-x-circle').on( 'click', function() {
		var Id_Profil = $(this).parent().attr('data-id');
		var Libelle = $(this).parent().text();

		ModalSupprimer( Id_Profil, Libelle );
	});
}


function supprimerProfil( Id_Profil, Libelle ) {
	var total = $( '#totalOccurrences' ).text();
	total = Number(total) - 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Supprimer_Profil',
		type: 'POST',
		async: false,
		dataType: 'json', // le résultat est transmit dans un objet JSON
		data: $.param({'id_profil': Id_Profil, 'libelle': Libelle}),
		success: function( reponse ){
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			afficherMessage( texteMsg, statut );

			if( statut == 'success' ){
				// Supprime la fenêtre modale
				$('#idModalSupprimer').modal('hide');

				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Supprime visuellement l'élément
				$('div#PRF_' + Id_Profil).remove();
			}
		}
	});
}