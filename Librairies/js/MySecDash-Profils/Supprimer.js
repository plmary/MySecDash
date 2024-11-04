function ModalSupprimer( Id, Libelle ) {
	var Message = 'zzz';
	var Erreur = false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Verifier_Associer',
		type: 'POST',
		dataType: 'json',
		data: $.param({'id': Id, 'libelle': Libelle}),
		async: false,
		success: function( reponse ) {
			if ( reponse['statut'] == 'success' ) {
				Message = reponse['texteMsg'];
			} else {
				afficherMessage( reponse['texteMsg'], reponse['statut'], 'body', true );
				Erreur = true;
			}
		}
	});

	if ( Erreur === true ) return false;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Libeller',
		type: 'POST',
		dataType: 'json',
		success: function( reponse ) {
			var Corps =
				'<div id="PRF-SUPR">' +
				Message +
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

				// Supprime visuellement l'élément de l'entête
				$('div#entete_tableau div.profils div[data-id="' + Id_Profil + '"]').remove();

				// Supprime visuellement l'élément de toutes les occurrences du corps
				$('div#corps_tableau div.row div[data-prf="' + Id_Profil + '"]').remove();

				Total_Profils = $('div#entete_tableau div.row div.titre').attr('data-total_prf');
				Total_Profils = Number(Total_Profils) - 1;
				$('div#entete_tableau div.row div.titre').attr('data-total_prf', Total_Profils);

				if ( Total_Profils >= reponse['limitation'] ) {
					$('button.btn-ajouter').attr('disabled', 'disabled');
				} else {
					$('button.btn-ajouter').removeAttr('disabled');
				}
			}
		}
	});
}