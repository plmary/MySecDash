function ModalConfirmerSuppression( Id ){
	var _Version = $('#SAV_'+Id+' div[data-src="sav_version"] span').text();
	var _Type = $('#SAV_'+Id+' div[data-src="sav_type"] span').text();
	var _Date = $('#SAV_'+Id+' div[data-src="sav_date"] span').text();

    $.ajax({
        url: '../../../Loxense-ExportBase.php?Action=AJAX_Libeller',
        type: 'POST',
        dataType: 'json',
        data: $.param({'version': _Version, 'type': _Type, 'date': _Date}),

        success: function( reponse ) {
            if ( reponse[ 'statut'] != 'success' ) {
                afficherMessage( reponse['texteMsg'], reponse['statut'], 'body' );
                return -1;
            }

            var Corps = 
                '<div id="SAV-SUPR">' +
                reponse['L_Confirmer_Suppression_Base'].replace("%sav_version", _Version)
                    .replace('%sav_type', _Type).replace('%sav_date', _Date) +
                '</div>';


            construireModal( 'idModalSupprimer',
                reponse[ 'L_Suppression_Fichier_Sauvegarde' ],
                Corps,
                'idBoutonSupprimer', reponse[ 'L_Supprimer' ],
                true, reponse[ 'L_Fermer' ],
                'formSupprimer', 'modal-lg' );


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

                supprimer( Id );
            } );
        }
    });
}


function supprimer( Id ) {
	var _Version = $('#SAV_'+Id+' div[data-src="sav_version"] span').text();
	var _Date = $('#SAV_'+Id+' div[data-src="sav_date"] span').text();

	_Version = _Version.replace('.','_').replace('-','_');
	_Date = _Date.replace(/-/g,'_').replace(/:/g,'_').replace(' ','-');


    $.ajax({
        url: '../../../Loxense-ExportBase.php?Action=AJAX_Supprimer',
        type: 'POST',
        data: $.param({'version': _Version, 'date': _Date}),
        dataType: 'json', // le résultat est transmit dans un objet JSON

        success: function( reponse ){
            var statut = reponse['statut'];
            var texteMsg = reponse['texteMsg'];

            if( statut == 'success' ){
			    trier( $( 'div#entete_tableau div.row div:first'), false );
            }

            afficherMessage( texteMsg, statut );
        }
    });

    $('.modal').modal('hide');
}