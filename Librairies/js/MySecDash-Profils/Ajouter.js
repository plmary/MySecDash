// ======================================================================
// Mise en place des écoutes sur les événements produits par les objets.
$(function() {
	// Ajouter l'entité dans la base.
	$(".btn-ajouter").on('click', function(){
		ModalAjouterModifier( 0 );
	});
});



// ============================================
// Fonctions répondant aux événements écoutés.

function AjouterProfil() {
	var prf_libelle = $('#prf_libelle').val();
	var prf_description = $('#prf_description').val();

	var total = $( '#totalOccurrences' ).text();
	total = Number(total) + 1;

	var Liste_Droits_Ajouter = [];
	var totalDroits = 0;

	$('a.droit').each(function(index, element){
		if ($(element).hasClass('desactive') == false ) {
			if ( $(element).attr('data-old_value') == 0) {
				Liste_Droits_Ajouter.push($(element).attr('id'));
				totalDroits += 1;
			}
		}
	});


	$.ajax({
		url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Ajouter',
		type: 'POST',
		data: $.param({'prf_libelle': prf_libelle, 'prf_description': prf_description, 'Liste_Droits_Ajouter': Liste_Droits_Ajouter}), // les paramètres sont protégés avant envoi
		dataType: 'json', // le résultat est transmit dans un objet JSON
		success: function( reponse ) { // Le serveur n'a pas rencontré de problème lors de l'échange ou de l'exécution.
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			if ( statut == 'success' ) {
				$('#idModalProfil').modal('hide'); // Cache la modale d'ajout.

				$( reponse[ 'texteHTML' ] ).prependTo( '#corps_tableau' );
				$( '#totalOccurrences' ).text( ajouterZero( total ) );

				// Assigne l'événement "click" sur le bouton de Modification
				if ( reponse[ 'droit_modifier' ] == true ) {
					$('#PRF_' + reponse[ 'id' ] + ' .btn-modifier').click( function( event ){
						ModalAjouterModifier( reponse[ 'id' ] );
					});
				}

				// Assigne l'événement "click" sur le bouton de Suppression
				if ( reponse[ 'droit_supprimer' ] == true ) {
					$('#PRF_' + reponse[ 'id' ] + ' .btn-supprimer').click(function(){
						ModalSupprimer( reponse['id'], prf_libelle );
					});
				}

				afficherMessage( texteMsg, statut, 'body' );
			} else {
				afficherMessage( texteMsg, statut, '#idModalProfil', 0, 'n' );
			}
		}
	});
}


function ajouterColonne( Id_Profil ) {
	var Acces = Acces | '';
	var Cellule;
	var Id_Application;

	var Ligne = $('div#corps_tableau div.row');

	Ligne.each( function( Index ) {
		//$('div:last', this).after( 'test' );
		Id_Application = $(this).attr('data-id');
		Id_Bouton = Id_Profil+'-'+Id_Application;

		$.ajax({
			url: Parameters['URL_BASE'] + Parameters['SCRIPT'] + '?Action=AJAX_Lister_Control_Acces',
			type: 'POST',
			async: false,
			dataType: 'json', // le résultat est transmit dans un objet JSON
			data: $.param({'id_profil': Id_Profil, 'id_application': Id_Application}),
			success: function( reponse ){
				var statut = reponse['statut'];

				if( statut == 'success' ){
				}
			}
		}); 

		$(this).children('div:last').after( Cellule );
	});

	// Mise à jour du compteur totalisant le nombre de profils en stock.
	Total_Profils = $('div#entete_tableau div.row div.titre').attr('data-total_prf');

	Total_Profils += 1;
	$('div#entete_tableau div.row div.titre').attr('data-total_prf', Total_Profils);

	if ( Total_Profils >= reponse['limitation'] ) {
		$('button.btn-ajouter').attr('disabled', 'disabled');
	} else {
		$('button.btn-ajouter').removeAttr('disabled');
	}

	activerBoutons();
	activerBoutonsSuppression();
	activerBoutonsModification();
}