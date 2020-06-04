'use strict';
//FONCTION D'AJOUT DE CLASSE POUR METTRE EN VALEUR L'ONGLET DANS LA NAV CORRESPONDANT À LA PAGE OUVERTE//
$(function() {
    //ON RÉCUPÈRE D'ABORD LE NOM DE L'ONGLET DANS L'URL, SITUÉ APRÈS LE DERNIER "/"//
    var url = window.location.href.substr(window.location.href.lastIndexOf("/")+1);
    //ENSUITE ON REGARDE CHAQUE ELEMENT "<a>" DANS LA NAV//
    $("#headerNav ul li a").each(function(){
        //SI UN DES ELEMENTS A L'ATTRIBUT "href" (DONC LE LIEN VERS LEQUEL IL RENVOIE) QUI CORRESPOND À LA VARIABLE "url", ON LUI AJOUTE LA CLASSE ACTIVE//
        if($(this).attr("href") === url) {
            $(this).addClass("active");
        }
        //ON AJOUTE LA CLASSE ACTIVE À L'ONGLET PRINCIPAL "Mes Spécialisation" EN PLUS DE L'AJOUT SUR L'ONGLET SECONDAIRE//
        if (url === 'violences.php' || url === 'rapport_au_corps.php' || url === 'gestion_de_la_douleur.php'){
            $('#specialisationsNav > a').addClass("active");
        }
        //MEME CHOSE ICI SAUF QU'IL S'AGIT DE DIFFÉRENTES SECTIONS D'UNE MEME PAGE//
        if (url === 'infos-pratiques.php#tarifs' || url === 'infos-pratiques.php#seances'){
            $('#infosPratiques > a').addClass("active");
        }

    });
    //ON ENLÈVE LA CLASSE ACTIVE AUX ONGLETS SECONDAIRES AVANT DE L'AJOUTER À CELUI SUR LEQUEL ON A CLIQUÉ //
    $('.subMenu li a').click(function () {
        $('.subMenu li a').removeClass('active');
        $(this).addClass('active');
    })
});