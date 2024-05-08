
mix.js('resources/js/app.js', 'public/js').postCss('resources/scss/app.css', 'public/scss');
// import * as bootstrap from 'bootstrap';
$(document).ready(function() {
    // Verifica se il CSS è stato caricato
    var cssLoaded = false;
    
    // Aggiungi un listener per il caricamento del CSS
    $('link[rel="stylesheet"]').on('load', function() {
        cssLoaded = true;
        $('#activeColumn').fadeIn(); // Mostra l'elemento HTML quando il CSS è caricato
    });
    
    // Controlla se il CSS è già stato caricato
    $('link[rel="stylesheet"]').each(function() {
        if (this.sheet && this.sheet.cssRules.length) {
            cssLoaded = true;
        }
    });
    
    // Se il CSS è già stato caricato, mostra direttamente l'elemento HTML
    if (cssLoaded) {
        $('#activeColumn').fadeIn();
    }
});
import.meta.glob([
    '../img/**'
])
