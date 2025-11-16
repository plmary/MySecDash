// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter un nouveau niveau à l'échelle de la Campagne courante dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalAjouterModifier();
	});

	// Initialise ou réinitialise une échelle de temps
	$(".btn-initialiser").on('click', function(){
		ModalInitialiserEchelleTemps();
	});
});



// ============================================
// Fonctions répondant aux événements écoutés.

function AjouterEchelleTemps() {
	var ete_poids = $('#ete_poids').val();
	var ete_nom_code = $('#ete_nom_code').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'ete_poids': ete_poids, 'ete_nom_code': ete_nom_code}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				$( reponse[ 'texte' ] ).prependTo( '#corps_tableau' );
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#ETE_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#ETE_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						var Libelle = $('#ETE_'+reponse['id']).find('div[data-src="ete_nom_code"]').find('span').text();

						ModalSupprimer( reponse['id'], Libelle );
					});
				}
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}


function ModalInitialiserEchelleTemps() {
	var libelleSociete = $('#s_sct_id :selected').text();

	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Verifier_Avant_Initialisation',
		type: 'POST',
		data: $.param({'libelle_societe': libelleSociete}), // les paramètres sont protégés avant envoi
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

				InitialiserEchelleTemps();
			} );
		}
	});
}


function InitialiserEchelleTemps() {
	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Initialiser_Echelle_Temps',
		type: 'POST',
		//data: $.param({'cmp_id': cmp_id}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModal').modal('hide'); // Cache la modale d'ajout.

				afficherMessage( texteMsg, statut, 'body' );

				trier( $( 'div#entete_tableau div.row div:first'), false );
			} else {
				afficherMessage( texteMsg, statut, '#idModal', 0, 'n' );
			}
		}
	});
}
