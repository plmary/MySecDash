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
        url: '../../../Loxense-Profils.php?Action=AJAX_Libeller',
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


function sauverModificationModal( Id_Profil, Libelle ) {
	$.ajax({
		url: '../../../Loxense-Profils.php?Action=AJAX_Modifier_Profil',
		type: 'POST',
		async: false,
		dataType: 'json', // le résultat est transmit dans un objet JSON
		data: $.param({'id_profil': Id_Profil, 'libelle': Libelle}),
		success: function( reponse ){
			var statut = reponse['statut'];
			var texteMsg = reponse['texteMsg'];

			afficherMessage( texteMsg, statut, '#idModalProfil', 0, 'n' );

			if( statut == 'success' ){
				// Supprime la fenêtre modale.
				$('#idModalProfil').modal('hide');

				// Modifie le libellé.
				$('div.row.profils div.col-lg-1[data-id="' + Id_Profil + '"]').find('a.modifiable').html( Libelle );
			}
		}
	});
}
