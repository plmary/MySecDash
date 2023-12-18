$(function() {
    // Assigne l'événement "click" sur tous les boutons de Modification
    $('.btn-modifier').click( function( event ){
        var Id = $(this).attr('data-id');

        ouvrirChamp( event, Id );
    });
});
