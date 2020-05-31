'use strict';

$(function() {
    var url = window.location.href.substr(window.location.href.lastIndexOf("/")+1);
    $("#headerNav ul li a").each(function(){
        if($(this).attr("href") === url || $(this).attr("href") === '') {
            $(this).addClass("active");
        }
        if (url === 'violences.php' || url === 'rapport_au_corps.php' || url === 'gestion_de_la_douleur.php'){
            $('#specialisationsNav > a').addClass("active");
        }
        if (url === 'infos-pratiques.php#tarifs' || url === 'infos-pratiques.php#seances' || url === 'infos-pratiques.php#cabinet'){
            $('#infosPratiques > a').addClass("active");
        }
    });
});