$(function() {
	// Charge les données du tableau.
	trier( $( 'div#entete_tableau div.row div:first'), true );
});

function listeTout(){
	$.ajaxSetup({async: false});
	$("#barrederecherche").val("");
	trier();
	$.ajaxSetup({async: true});
}


function trier( myElement, changerTri ) {
	var Texte = '';

	// Recharge l'écran si on change de langue.
	$('#langue_libelle').change( function(){
		$.ajax({
			url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Changer_Langue',
			type: 'POST',
			//dataType: 'json',
			data: $.param({'langue': $('#langue_libelle').val()}),
			success: function( reponse ){
				trier( $( 'div#entete_tableau div.row div:first'), true );
			}
		});
	});

	$.ajax({
		url: '../../../Loxense-ReferentielsConformite.php?Action=AJAX_Trier',
		type: 'POST',
		dataType: 'json',
		data: $.param({'trier': changerTri}),
		success: function( reponse ){
			var statut = reponse['statut'];

			if ( statut == 'success' ) {
				//$('#corps_tableau').html(reponse);
				Texte += reponse[ 'texteHTML' ];
				$('#corps_tableau').html( Texte ).trigger('defiler');
				$('#totalOccurrences').text( reponse[ 'total' ] );
				
				redimensionnerWindow();
			} else {
				var texteMsg = reponse['texteMsg'];

				afficherMessage( texteMsg, statut );
				return;
			}
		}
	}); 


	$('#corps_tableau').html( Texte );
}


function plierDeplier( Id_Occurrence ) {
	if ( $('#'+Id_Occurrence).attr('data-ouvert') == 'oui' ) { // Ferme les enfants de cette occurrence.
		$('#'+Id_Occurrence).attr('data-ouvert', 'non');
		$('#'+Id_Occurrence).find('.plmTree-spacer i').removeClass('bi-chevron-down').addClass('bi-chevron-right');
		
		$.each( $.find('div[id^="'+Id_Occurrence+'-"]'), function( index, valeur ) {
			$(valeur).hide();
		});
	} else { // Ouvre les enfants de cette occurrence avec le dernier état de ses enfants.
		$('#'+Id_Occurrence).attr('data-ouvert', 'oui');
		$('#'+Id_Occurrence).find('.plmTree-spacer i').removeClass('bi-chevron-right').addClass('bi-chevron-down');

		$('#'+Id_Occurrence).show();

		var Afficher_Enfant = true;
		var Prefixe_Parent = '';

		$.each( $.find('div[id^="'+Id_Occurrence+'"]'), function( index, valeur ) {
			if ( Afficher_Enfant === true ) $(valeur).show();

			if ( $(valeur).attr('data-ouvert') == 'non' ) { // Affiche cette occurrence, mais pas ses enfants.
				Afficher_Enfant = false;
				$(valeur).show();
			}

			if ( $(valeur).attr('data-ouvert') == 'oui' ) { // Affiche cette occurrence et ses enfants.
				Afficher_Enfant = true;
				$(valeur).show();
			}
		});
	}
	
	redimensionnerWindow();
}