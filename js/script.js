'use strict';
var phoneChecker;
var emailChecker;
var request;
var violencesArticlesArray = {
    'btnTrauma01': '#defTrauma',
    'btnTrauma02': '#consequencesESPT',
    'btnTrauma03': '#sophroEtESPT',
    'btnTrauma04': '#priseEnCharge'
};
var speList = {
    'liViolences': '#imgViolences',
    'liCorps': '#imgCorps',
    'liDouleur': '#imgDouleur'
};

//ON CRÉE UNE FONCTION DE REGEX POUR VÉRIFIER LA VALIDITÉ DU FORMAT DE L'EMAIL//
function regEx(email) {
    var checkEmail = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;

    return checkEmail.test(email);
}

//ON CRÉE UNE FONCTION POUR VÉRIFIER LA VALIDITÉ DU FORMAT DU NUMERO DE TELEPHONE, COMPORTANT BIEN UNIQUEMENT DES CHIFFRES, D'UNE LONGUEUR DE 10 CHIFFRES ET COMMENÇANT PAR 0//
function phoneFormat(phoneNumber) {
    if (isNaN(phoneNumber) || phoneNumber.length !== 10 || phoneNumber.charAt(0) !== '0') {
        return false;
    } else {
        return true;
    }
}

$(function () {
    //CHANGE L'ASPECT DU HEADER SELON LE SCROLL//
    $(window).scroll(function () {
        //SI ON DESCEND DE PLUS DE 135PX LE HEADER PASSE EN FIXED ET LE LOGO CHANGE POUR S'ADAPTER//
        if ($(document).scrollTop() > 135) {
            $('#header').attr('id', 'headerToFixed');
            $('#headerNav').attr('id', 'headerNavToFixed');
            $('#logo').attr('id', 'logoToFixed');
            $('.headerNavLi').attr('class', 'headerNavLiToFixed');
            $('#ipNav').css('margin-top', '0.5rem');
            $('#apNav').css('margin-top', '0.5rem');

            $('#smallHeaderUl').attr('id', 'smallHeaderUlToFixed');
            $('#logoSmallMenu').attr('id', 'logoSmallToFixed');
            $('#smallMenuBtn').css('padding', '0');
            $('#smallMenuNav').css('top', '4.6rem');

            $('html main').css("padding", '22rem 0 5rem');

        } else {
            //SI ON REMONTE, ON RÉINITIALISE LE HEADER//
            $('#headerToFixed').attr('id', 'header');
            $('#headerNavToFixed').attr('id', 'headerNav');
            $('#logoToFixed').attr('id', 'logo');
            $('.headerNavLiToFixed').attr('class', 'headerNavLi');
            $('#ipNavToFixed').css('margin-top', '1rem');
            $('#apNavToFixed').css('margin-top', '1rem');

            $('#smallHeaderUlToFixed').attr('id', 'smallHeaderUl');
            $('#logoSmallToFixed').attr('id', 'logoSmallMenu');
            $('#smallMenuBtn').css('padding', '1rem 0 2rem');
            $('#smallMenuNav').css('top', '23.6rem');

            $('html main').css("padding", '2rem 0 5rem');
        }
        if ($(document).scrollTop() > 200 && $(document).width() > 980) {
            $('#espnArt').attr('id', 'espnArtToFixed');
        } else {
            $('#espnArtToFixed').attr('id', 'espnArt');
        }

        if ($(document).scrollTop() > 1050 && $(document).width() > 980) {
            $('#espnArtToFixed').attr('id', 'espnArtToFixedDown');
        } else {
            $('#espnArtToFixedDown').attr('id', 'espnArt');
        }

    });

    //SI ON CLIQUE SUR UN LIEN AVEC ANCRAGE QUI NOUS DIRIGE AILLEURS QU'EN HAUT DE LA PAGE (PAR EXEMPLE ON CLIQUE DANS LA NAV SUR LA SOUS CATÉGORIE "SÉANCES" D'"INFOS PRATIQUES") , ON FAIT EN SORTE QUE LA NAV APPARAISSE BIEN//
    $("#headerNav ul li a").each(function () {
        if ($(this).attr('href').indexOf('#') >= 0 && $(document).scrollTop() > 0) {
            $('#header').attr('id', 'headerToFixed');
            $('#headerNav').attr('id', 'headerNavToFixed');
            $('#logo').attr('id', 'logoToFixed');
            $('.headerNavLi').attr('class', 'headerNavLiToFixed');
            $('#ipNav').css('margin-top', '0.5rem');
            $('#apNav').css('margin-top', '0.5rem');

            $('#smallHeaderUl').attr('id', 'smallHeaderUlToFixed');
            $('#logoSmallMenu').attr('id', 'logoSmallToFixed');
            $('#smallMenuBtn').css('padding', '0');
            $('#smallMenuNav').css('top', '4.6rem');

            $('html main').css("padding", '22rem 0 5rem');
        }
    });

    //AJOUTE UN FADE OUT SUR LES MESSAGES DE SUCCES OU D'ERREUR LORS DES ENVOIS DE MESSAGES DANS 'CONTACT' OU DES PRISES DE RDV//
    function requestFadeOut() {
        $('.requestSent').animate({
            'opacity': 0
        });
    }

    //ACTIVE LA FONCTION CLICK LORSQUE L'ON PRESSE 'ENTER'//
    function pressEnter(elem, event) {
        $(elem).keydown(function (e) {
            if (e.key === "Enter") {
                $(event).click();

                return false;
            }
        });
    }

    //DETERMINE DANS QUELS CAS LA FONCTION PRESSENTER MARCHE//
    pressEnter('.admin', "#loginBtn");
    pressEnter('#alertWindow', "#alertBtn");
    pressEnter('.userInfo', '#rdvBtn');

    //ACTIVE LA VERIFICATION DU FORMAT DU NUMERO DE TELEPHONE LORSQUE L'ON QUITTE L'INPUT//
    $('.phone').blur(function () {
        var phone = $(".phone").val();
        if (phone !== '') {
            //SI LE CHAMPS EST REMPLI ON VÉRIFIE LE FORMAT ET ON RETOURNE TRUE OU FALSE EN FONCTION DU RÉSULTAT//
            if (phoneFormat(phone) === false) {
                phoneChecker = false;
                $('.phone').addClass("errorInput").removeClass("okInput");
            } else {
                phoneChecker = true;
                $('.phone').addClass("okInput").removeClass("errorInput");
            }
            return phoneChecker;
        }
    });

    //ACTIVE LA VERIFICATION DU FORMAT DU MAIL LORSQUE L'ON QUITTE L'INPUT//
    $('.email').blur(function () {
        var email = $(".email").val();
        //SI LE CHAMPS EST REMPLI ON VÉRIFIE LE FORMAT ET ON RETOURNE TRUE OU FALSE EN FONCTION DU RÉSULTAT//
        if (email !== "") {
            if (regEx(email) === false) {
                emailChecker = false;
                $('.email').addClass("errorInput").removeClass("okInput");
            } else {
                emailChecker = true;
                $('.email').addClass("okInput").removeClass("errorInput");
            }
            return emailChecker;
        }
    });

    //ACTIVE LA VERIFICATION ET L'ENVOIE DES DONNÉES EN AJAX LORS DE L'ENVOI D'UN MESSAGE VIA LE FORMULAIRE PAGE 'CONTACT'//
    $('#contactBtn').click(function (e) {
        e.preventDefault();

        //CONTACTDATA SERVIRA DE VERIFICATEUR PRINCIPAL LORS DE L'ENVOI EN AJAX//
        var contactData = $("#contactForm").serializeArray();
        var firstName = $("#contactFirstName").val();
        var lastName = $("#contactLastName").val();
        var phone = $("#contactPhone").val();
        var email = $("#contactEmail").val();
        var message = $("#contactMessage").val();
        var error = 0;

        //SI LES CHAMPS SONT REMPLIS ET AU BON FORMAT ON AJOUTE UNE BORDURE VERTE, SINON UNE BORDURE ROUGE ET ON INCRÉMENTE ERROR//
        $('input[type=text]').each(function () {
            if ($(this).val() === '') {
                $(this).addClass('errorInput').removeClass('okInput');
                error++;
            } else {
                $(this).removeClass('errorInput').addClass('okInput');
            }
        });

        if (phoneChecker === false || phone === "") {
            $("#contactPhone").addClass("errorInput").removeClass("okInput");
            error++;
        } else {
            $("#contactPhone").addClass("okInput").removeClass("errorInput");
        }

        if (emailChecker === false || email === "") {
            $("#contactEmail").addClass("errorInput").removeClass("okInput");
            error++;
        } else {
            $("#contactEmail").addClass("okInput").removeClass("errorInput");
        }

        if (message === "") {
            $("#contactMessage").removeClass("okInput").addClass("errorInput");
            error++;
        } else {
            $("#contactMessage").removeClass("errorInput").addClass("okInput");
        }
        //SI TOUT EST OK, ALORS ENVOI AJAX///
        if (error === 0) {
            $.ajax({
                type: "POST",
                url: "contact.php",
                data: {
                    'contactData': contactData,
                    'firstName': firstName,
                    'lastName': lastName,
                    'phone': phone,
                    'email': email,
                    'message': message
                },
                success: function () {

                    //ON RÉINITIALISE LES CHAMPS DU FORMULAIRE ET ON FAIT APPARAÎTRE UN MESSAGE DE SUCCES//
                    $("#contactFirstName").val('');
                    $("#contactLastName").val('');
                    $("#contactPhone").val('');
                    $("#contactEmail").val('');
                    $("#contactMessage").val('');
                    $('#contactForm input').removeClass('okInput');
                    $('#contactForm textarea').removeClass('okInput');
                    $('.requestSent').addClass('successRequest').removeClass('errorRequest');
                    $('.requestSent').html('Votre message a bien été envoyé, merci beaucoup !<br>Vous allez recevoir un email récapitulatif.');
                    $('.requestSent').animate({
                        'opacity': 1
                    }, setTimeout(requestFadeOut, 5000));
                    $('html, body').animate({
                        scrollTop: $("#contactSect").offset().top
                    }, 500);

                }
            });
        } else {
            //ON FAIT APPARAÎTRE UN MESSAGE D'ERREUR AVEC UNE BORDURE ROUGE SUR LES CHAMPS À PROBLÈME//
            $('.requestSent').removeClass('successRequest').addClass('errorRequest');
            $('.requestSent').text("Une erreur s'est produite, veuillez vérifier les informations demandées, merci. ");
            $('.requestSent').animate({
                'opacity': 1
            }, setTimeout(requestFadeOut, 5000));
            $('html, body').animate({
                scrollTop: $("#contactSect").offset().top
            }, 500);
        }
    });

    //ACTIVE LA SELECTION / DESELECTION D'UN CRÉNEAU HORAIRE LORS DE LA PRISE DE RDV ET CENTRE LA FENÊTRE SUR LES CHAMPS À REMPLIR //
    $('.timeSlot').click(function (e) {
        e.preventDefault();

        if ($(this).hasClass('selectedTime')) {
            $(this).removeClass('selectedTime');
            $(this).addClass('avTime');
        } else if ($(this).hasClass('avTime')) {
            $('.timeSlot').removeClass('selectedTime').addClass('avTime');
            $('.unavTime').removeClass('avTime');
            $(this).addClass('selectedTime');
            $(this).removeClass('avTime');
        }
        //SI LE CRENEAU SELECTIONNÉ EXISTE ON RÉCUPÈRE SON NAME QUE L'ON SPLIT EN UN TABLEAU POUR SÉPARER LE NOM DU JOUR, SON NOMBRE ET LE MOIS, ON RÉCUPÈRE AUSSI SA VALEUR QUI EST L'HEURE//
        if ($('.selectedTime').length) {
            var timeSlotDataArray = $('.selectedTime').attr('name').split(' ');
            var timeSlotDayName = timeSlotDataArray[0];
            var timeSlotDayNumber = timeSlotDataArray[1];
            var timeSlotMonth = timeSlotDataArray[2];
            var timeSlotTime = $('.selectedTime').val();

            //ON AFFICHE UNE PHRASE RÉCAPITULATRICE EN Y INTÉGRANT LES INFORMATIONS DU CRÉNEAU AVEC UN MISE EN FORME//
            $('#selectedTimeSlotInfo').html('Vous avez choisi le <span class="bold" style ="color: #1b76d1">' + timeSlotDayName + ' ' + timeSlotDayNumber + ' ' + timeSlotMonth + '</span> à <span class="bold" style ="color: #1b76d1">' + timeSlotTime + '</span>.').css('color', 'black')
        }
        //SI AUCUN CRÉNEAU N'EST SÉLECTIONNÉ (OU LE CRÉNEAU A ÉTÉ DÉSELECTIONNÉ) ON DEMANDE DE SELECTIONNER UN CRÉNEAU (ON NE L'AFFICHE PAS SI ON CLIQUE SUR UN CRÉNEAU INDISPONIBLE EN PREMIER LIEU)//
        else if ($('.selectedTime').length === 0 && !$(this).hasClass('unavTime')) {
            $('#selectedTimeSlotInfo').html('Veuillez selectionner un créneau.').css('color', 'red');
        }

        //SI UN BOUTON VALIDE EST SELECTIONNÉ, ON AFFICHE LE FORMULAIRE DE RENDEZ VOUS ET ON SCROLL DESSUS//
        if ($(this).hasClass('selectedTime')) {
            $('#userInfoForm').show();
            $('html, body').animate({
                scrollTop: $("#rdvForm").offset().top
            }, 500);
        }
    });

    //ACTIVE LA VERIFICATION ET L'ENVOIE DES DONNÉES EN AJAX LORS DE LA PRISE DE RDV VIA LE FORMULAIRE PAGE 'PRENDRE RDV'//
    $('#rdvBtn').click(function (e) {
        e.preventDefault();

        //RDVDATA SERVIRA DE VERIFICATEUR PRINCIPAL LORS DE L'ENVOI EN AJAX//
        var rdvData = $("#rdvForm").serializeArray();
        var firstName = $("#rdvFirstName").val();
        var lastName = $("#rdvLastName").val();
        var phone = $("#rdvPhone").val();
        var email = $("#rdvEmail").val();
        var motif = $("#rdvMotif").val();
        var timeSlotDateTime = $(".selectedTime").attr('id');
        var timeSlotFull = $(".selectedTime").attr('name');
        var message = $("#rdvMessage").val();
        var error = 0;

        //SI LES CHAMPS SONT REMPLIS ET AU BON FORMAT ON AJOUTE UNE BORDURE VERTE, SINON UNE BORDURE ROUGE ET ON INCRÉMENTE ERROR//
        $('input[type=text]').each(function () {
            if ($(this).val() === '') {
                $(this).addClass('errorInput').removeClass('okInput');
                error++;
            } else {
                $(this).removeClass('errorInput').addClass('okInput');
            }
        });

        if (phoneChecker === false || phone === "") {
            $("#rdvPhone").addClass("errorInput").removeClass("okInput");
            error++;
        } else {
            $("#rdvPhone").addClass("okInput").removeClass("errorInput");
        }

        if (emailChecker === false || email === "") {
            $("#rdvEmail").addClass("errorInput").removeClass("okInput");
            error++;
        } else {
            $("#rdvEmail").addClass("okInput").removeClass("errorInput");
        }

        if (motif === "0") {
            $("#rdvMotif").addClass("errorInput").removeClass("okInput");
            error++;
        } else {
            $("#rdvMotif").addClass("okInput").removeClass("errorInput");
        }

        //SI TOUT EST OK, ALORS ENVOI AJAX///
        if (error === 0) {
            $.ajax({
                type: "POST",
                url: "prendre-rdv.php",
                data: {
                    'rdvData': rdvData,
                    'firstName': firstName,
                    'lastName': lastName,
                    'phone': phone,
                    'email': email,
                    'motif': motif,
                    'timeSlotDateTime': timeSlotDateTime,
                    'timeSlotFull': timeSlotFull,
                    'message': message
                },
                success: function () {

                    //ON CHANGE L'ASPECT DU CRÉNEAU EN INDISPONIBLE, ON RÉINITIALISE LES CHAMPS DU FORMULAIRE ET ON FAIT APPARAÎTRE UN MESSAGE DE SUCCÈS//
                    $(".selectedTime").addClass('unavTime').removeClass('selectedTime');
                    $("#rdvFirstName").val('');
                    $("#rdvLastName").val('');
                    $("#rdvPhone").val('');
                    $("#rdvEmail").val('');
                    $("#rdvMessage").val('');
                    $("#rdvMotif").val('0');
                    $('#rdvForm input, #rdvForm select').removeClass('okInput');
                    $('.requestSent').addClass('successRequest').removeClass('errorRequest');
                    $('.requestSent').html('Votre rendez-vous a bien été pris en compte, merci beaucoup !<br>Vous allez recevoir un e-mail de confirmation');
                    $('.requestSent').animate({
                        'opacity': 1
                    }, setTimeout(requestFadeOut, 5000));
                    $('html, body').animate({
                        scrollTop: $("#rdvSect").offset().top
                    }, 500);
                    $('#userInfoForm').hide();
                    $('.rdvMessage').hide();

                }

            });
        } else {
            //ON FAIT APPARAÎTRE UN MESSAGE D'ERREUR AVEC UNE BORDURE ROUGE SUR LES CHAMPS À PROBLÈME//
            $('.requestSent').removeClass('successRequest').addClass('errorRequest');
            $('.requestSent').text("Une erreur s'est produite, veuillez vérifier les informations demandées, merci. ")
            $('.requestSent').animate({
                'opacity': 1
            }, setTimeout(requestFadeOut, 5000));
            $('html, body').animate({
                scrollTop: $("#rdvSect").offset().top
            }, 500);
        }
    });


    //ON ENLÈVE LA BORDURE ROUGE DU CHAMPS DE TEXTE À PROBLÈME LORSQUE L'ON CLIQUE À L'INTERIEUR//
    $('input[type=text]').keydown(function () {
        if ($(this).hasClass('errorInput')) {
            $(this).removeClass('errorInput');
        }
    });

    //ON ENLÈVE LA BORDURE ROUGE DU CHAMPS SELECT À PROBLÈME LORSQUE L'ON CLIQUE À L'INTERIEUR//
    $('#rdvMotif').change(function () {
        if ($(this).hasClass('errorInput')) {
            $(this).removeClass('errorInput');
        }
    });

    //SUR LE CALENDRIER HEBDOMADAIRE, ON EMPÊCHE L'UTILISATEUR D'ALLER À UNE SEMAINE ANTÉRIEURE À L'ACTUELLE //
    $('#unavPreviousWeek').click(function (e) {
        e.preventDefault();
    });

    //SUR LE CALENDRIER HEBDOMADAIRE, ON EMPÊCHE L'UTILISATEUR D'ALLER À UNE SEMAINE POSTÉRIEURE À CELLE DÉFINIE EN LIMITE //
    $('#unavNextWeek').click(function (e) {
        e.preventDefault();
    });


    //FAIT APPARAÎTRE / DISPARAÎTRE L'OPTION D'AJOUT DE MESSAGE LORS DE LA PRISE DE RDV ET CENTRE LA FENÊTRE DESSUS//
    $('#rdvMessageButton').click(function (e) {
        e.preventDefault();
        $('.rdvMessage').toggle();
        $('html, body').animate({
            scrollTop: $("#userInfoForm").offset().top
        }, 500);
    });


    //FAIT APPARAÎTRE LE MENU DE NAVIGATION 'INFOS PRATIQUES'//
    $('#infosPratiques').hover(function () {
            $('#ipNav').fadeIn(100);
        },
        function () {
            $('#ipNav').fadeOut(100);
        });

    //FAIT APPARAÎTRE LE MENU DE NAVIGATION 'SPECIALISATIONS'//
    $('#specialisationsNav').hover(function () {
            $('#speNav').fadeIn(100);
        },
        function () {
            $('#speNav').fadeOut(100);
        });

    //FAIT APPARAÎTRE LE MENU PRINCIPAL DANS LE CAS OÙ LA LARGEUR EST BASSE(SUR SMARTPHONE OU TABLETTE)//
    $('#smallMenuBtn').click(function () {
        $('#smallMenuNav').fadeToggle(200);
    });

    //ACTIVE LA VERIFICATION ET L'ENVOIE DES DONNÉES EN AJAX LORS DE LA TENTATIVE DE CONNEXION UTILISATEUR//
    $('#loginBtn').click(function (e) {
        e.preventDefault();
        var loginData = $("#loginForm").serializeArray();
        var login = $("#login").val();
        var password = $("#password").val();
        var error = 0;

        if (login === "") {
            error++;
        }
        if (password === "") {
            error++;
        }

        //SI TOUT EST OK, ALORS ENVOI AJAX///
        if (error === 0) {

            $.ajax({
                type: "POST",
                url: "admin-office.php",
                data: {
                    'loginData': loginData,
                    'login': login,
                    'password': password
                },
                success: function (data) {
                    var status = JSON.parse(data);
                    //ON EFFACE LES CHAMPS//
                    $("#login").val('');
                    $("#password").val('');
                    //SI 'ADMIN' OU 'MOT DE PASSE' ERRONNÉ, ON AFFICHE UN MESSAGE D'ERREUR, SINON ON RECHARGE LA PAGE EN VUE D'UNE REDIRECTION//
                    if (status.status === 'KO') {
                        $('#loginStatus').html('identifiant ou mot de passe incorrect.');
                        $('#loginStatus').addClass('errorRequest').css({
                            'margin': '1rem auto',
                            'display': 'inline-block'
                        });
                        $('.requestSent').animate({
                            'opacity': 1
                        }, setTimeout(requestFadeOut, 5000));
                        $('html, body').animate({
                            scrollTop: $("html header").offset().top
                        }, 500);
                    } else {
                        window.location.reload();
                    }
                },
                error: function (data) {
                    var status = JSON.parse(data);
                    //ON AFFICHE UN MESSAGE D'ERREUR SI LA REQUETE N'ABOUTIT PAS //
                    if (status.status === 'KO') {
                        $('#loginStatus').html('Une erreur interne est survenue.');
                        $('.requestSent').show();
                    }
                }
            });
        }
        //SINON CELA SIGNIFIE QUE TOUT LES CHAMPS NE SONT PAS REMPLIS, ON ALERTE DONC AVEC UN MESSAGE D'ERREUR//
        else {
            $('#loginStatus').html('Veuillez remplir tous les champs.');
            $('#loginStatus').addClass('errorRequest').css({
                'margin': '1rem auto',
                'display': 'inline-block'
            });
            $('.requestSent').animate({
                'opacity': 1
            }, setTimeout(requestFadeOut, 5000));
            $('html, body').animate({
                scrollTop: $("html header").offset().top
            }, 500);
        }
    });

    //REDIRIGE VERS LA PAGE DE DÉCONNEXION//
    $('#logoutBtn').click(function () {
        window.location.replace("logout.php");
    });

    //RÉCUPÈRE LES DONNÉES LIÉES AU CRÉNEAU RÉSERVÉ (FONCTION DISPONIBLE DANS L'ESPACE ADMIN)//
    $('.bookedTime').click(function (e) {
        e.preventDefault();
        var rdvTimeSlot = $(this).attr('id');
        //ON SÉPARE LES INFOS DU NAME EN TABLEAU POUR RÉCUPÉRER LE JOUR, SON NUMBRE ET LE MOIS//
        var rdvDayArray = $(this).attr('name').split(' ');
        var dayName = rdvDayArray[0];
        var dayNumber = rdvDayArray[1];
        var month = rdvDayArray[2];
        var rdvTime = $(this).val();
        //ON ENVOIE EN AJAX//
        $.ajax({
            type: "POST",
            url: "admin-office.php",
            data: {
                'rdvTimeSlot': rdvTimeSlot
            },
            success: function (data) {
                //ON RÉCUPÈRE ET RÉPARTIT LES DONNÉES AVEC UNE MISE EN FORME DANS LA VARIABLE INFOS//
                var rdv = JSON.parse(data);
                var infos = "<span class='bold' style='text-transform: capitalize'>" + dayName + "</span> <span class='bold'>" + dayNumber + "</span> <span class='bold'>" + month + "</span> <br> <span class='bold'>" + rdvTime + "</span><br><span class='bold block'>" + rdv.firstName + " " + rdv.lastName + "</span>" + rdv.motif + "<br>" + "<a href=tel:'" + rdv.phone + "'>" + "<i class='fas fa-phone-alt'></i>" + rdv.phone + "</a>" + "<br>" + "<a href=mailto:'" + rdv.email + "'>" + "<i class='fas fa-envelope'></i>" + rdv.email + "</a>";
                //SI L'UTILISATEUR A AJOUTÉ UN MESSAGE ON LE RÉCUPÈRE ET L'AJOUTE À LA SUITE//
                if (rdv.message) {
                    infos += "<span class='bold block' style='margin-top: 1rem'>Message :</span>" + rdv.message + "<br>";
                }
                //ON AFFICHE LES INFOS DANS "ALERTWINDOWCONTENT" ET ON AFFICHE "ALERTWINDOWBG"//
                $('#alertWindowContent').prepend(infos);
                $('#alertWindowBg').css({
                    "width": '100%',
                    'height': '100%',
                    "display": 'flex'
                });
                //ON ÔTE LE FOCUS AU CRÉNEAU SELECTIONNÉ ET ON L'AJOUTE AU BOUTON DU MODAL POUR QUE LA FONCTION PRESSENTER() SOIT POSSIBLE SUR #ALERTBTN//
                $('.bookedTime').blur();
                $('#alertBtn').focus();
            },
            error: function () {
                //SI ERREUR D'ENVOI IL Y A, ON AFFICHE UN MESSAGE//
                $('#adminCalStatus').html("Une erreur interne s'est produite.")

            }
        })
    });

    //ON CACHE #ALERTWINDOWBG ET ON EFFACE LE CONTENU DE #AERTWINDOWCONTENT//
    $('#alertBtn').click(function (e) {
        e.preventDefault();
        $('#alertWindowBg').css("display", 'none');
        $('#alertWindowContent').html('');
    });

    //L'ADMIN ACTIVE OU DESACTIVE LA DISPONIBILITÉ DES CRÉNEAUX D'UNE JOURNÉE//
    $('.adminDaySwitch').click(function (e) {
        e.preventDefault();
        //NE FONCTIONNE QUE SUR LES JOURS QUI NE SONT PAS PASSÉS//
        if (!$(this).hasClass('dayPast')) {
            //ON RÉCUPÈRE LE NAME DU JOUR DANS DAYTOSWITCH ET ON ENREGISTRE LE BOUTON SUR LEQUEL ON VIENT DE CLIQUER//
            var dayToSwitch = $(this).attr('name');
            var dayBtn = $(this);
            var error = 0;
            //SI L'UN DES CRÉNEAUX DU JOUR EST RÉSERVÉ, UN CONFIRM APPARAÎT POUR, OUI OU NON, CONTINUER LE SWITCH//
            if ($("." + dayToSwitch).hasClass('bookedTime')) {
                request = confirm("Des rendez-vous sont enregistrés, ils seront conservés, voulez-vous continuer ?");
                if (request === false) {
                    error++
                }
            }
            //SI PAS D'ERREUR ON ENVOI EN AJAX//
            if (error === 0) {
                $.ajax({
                    type: "POST",
                    url: "admin-office.php",
                    data: {
                        'dayToSwitch': dayToSwitch
                    },
                    success: function (data) {
                        //ON RECUPERE LES DONNÉES JSON//
                        var day = JSON.parse(data);
                        //ON AJOUTE ET RETIRE LES CLASSES AFFÉRENTES AU SWITCH, AU BOUTON SÉLECTIONNÉ EN FONCTION DES CLASSES QU'IL CONTIENT//
                        if (day.status === "unavDay") {
                            $(dayBtn).removeClass('dayOn').addClass('dayOff');
                        } else {
                            $(dayBtn).removeClass('dayOff').addClass('dayOn');
                        }

                        //POUR CHAQUE CRÉNEAU DU JOUR QUI NE SONT PAS RÉSERVÉS, ON AJOUTE ET RETIRE LES CLASSES AFFÉRENTES AU SWITCH//
                        //AUSSI ON N'EFFACE PAS LES RENDEZ VOUS ENREGISTRÉS//
                        $.each($("." + dayToSwitch), function (key) {
                            var timeSlot = $("." + dayToSwitch)[key];
                            if (!$(timeSlot).hasClass('bookedTime')) {
                                if (day.status === "avDay") {
                                    $(timeSlot).addClass('adminAvTime').removeClass('deactivated');
                                } else {
                                    $(timeSlot).addClass('deactivated').removeClass('adminAvTime');
                                }
                            }
                        });
                    },
                    error: function () {
                        //SI PROBLÈME D'ENVOI, ON AFFICHE N MESSAGE D'ERREUR//
                        $('#adminCalStatus').html("Une erreur interne s'est produite.")

                    }
                })
            }
        }
    });

    //L'ADMIN ACTIVE OU DESACTIVE LA DISPONIBILITÉ D'UN CRÉNEAU EN PARTICULIER//
    $('.adminTimeSlot').click(function (e) {
        e.preventDefault();
        //NE FONCTIONNE PAS SUR LES CRÉNEAUX DES JOURS PASSÉS OU RÉSERVÉS//
        if (!$(this).hasClass('unavTime') && !$(this).hasClass('bookedTime')) {
            //ON RÉCUPÈRE LES INFOS DE L'ID DU CRÉNEAU ET ON ENREGISTRE LE BOUTON SÉLECTIONNÉ//
            var timeSlotToSwitch = $(this).attr('id');
            var fullTimeSlot = $(this);
            //ON ENVOIE EN AJAX//
            $.ajax({
                type: "POST",
                url: "admin-office.php",
                data: {
                    'timeSlotToSwitch': timeSlotToSwitch
                },
                success: function (data) {
                    //ON RÉCUPÈRE LES DONNÉES ET ON AJOUTE ET RETIRE LES CLASSES AFFÉRENTES AU SWITCH //
                    var timeSlot = JSON.parse(data);
                    if (timeSlot.status === "unavTime") {
                        $(fullTimeSlot).addClass('deactivated').removeClass('adminAvTime');
                    } else {
                        $(fullTimeSlot).addClass('adminAvTime').removeClass('deactivated');
                    }
                },
                error: function () {
                    //SI PROBLÈME D'ENVOI, ON AFFICHE N MESSAGE D'ERREUR//
                    $('#adminCalStatus').html("Une erreur interne s'est produite.")
                }
            })
        }
    });

    //ON SUPPRIME LE RENDEZ-VOUS LIÉ AU BOUTON//
    $('.rdvDelete').click(function (e) {
        e.preventDefault();
        //ON RÉCUPÈRE LE NAME ET ON ENREGISTRE LE BOUTON SUR LEQUEL ON VIENT DE CLIQUER//
        var rdvToDelete = $(this).attr('name');
        var deleteBtn = $(this);
        //UN CONFIRM APPARAÎT//
        request = confirm("Êtes-vous sûre de supprimer ce rendez-vous ?");

        //SI ON CONFIRME ON ENVOIE EN AJAX//
        if (request === true) {
            $.ajax({
                type: "POST",
                url: "admin-office.php",
                data: {
                    'rdvToDelete': rdvToDelete
                },
                success: function (data) {
                    //ON RÉCUPÈRE LES DONNÉES JSON//
                    var rdv = JSON.parse(data);
                    //SI LE RDV A ÉTÉ SUPPRIMMÉ ON CACHE LE BOUTON DELETE ET ON RÉINITIALISE LE CRÉNEAU COMME DISPONIBLE//
                    if (rdv.status === 'success') {
                        $(deleteBtn).hide();
                        $(deleteBtn).siblings('input').addClass('adminAvTime').removeClass('bookedTime');
                    }
                },
                error: function () {
                    //SI PROBLÈME D'ENVOI, ON AFFICHE N MESSAGE D'ERREUR//
                    $('#adminCalStatus').html("Une erreur interne s'est produite.")

                }
            })
        }
    });
    //QUAND ON CLIQUE SUR UN LIEN "EN SAVOIR PLUS" ON AFFICHE L'ARTICLE CORREPONDANT ET SI UN ARTICLE ETAIT DÉJÀ AFFICHÉ, ON CACHE CE DERNIER ET ON SCROLL JUSQU'À L'ARTICLE//
    $('.enSavoirPlusNav').click(function () {
        var key;
        var value;

        for (key in violencesArticlesArray) {
            value = violencesArticlesArray[key];
            $(value).hide();
        }

        key = $(this).attr('id');
        value = violencesArticlesArray[key];

        $('#backToEspn').show();
        $(value).toggle();
        $('html, body').animate({
            scrollTop: $("#speDiv").offset().top
        }, 500);
    });

    //ON SCROLL VERS LE HAUT, AU NIVEAU DU MENU "EN SAVOIR PLUS"//
    $('#backToEspn').click(function () {
        if ($(document).width() > 980) {
            $('html, body').animate({
                scrollTop: 150
            }, 500);
        } else {
            $('html, body').animate({
                scrollTop: $("#enSavoirPlusNav").offset().top
            }, 500);
        }

    });

    //LORSQU'ON CLIQUE SUR UN ÉLÉMENT DE LA CLASSE "CLICKABLE" ON L'AFFICHE DANS UN MODAL EN LARGEUR 100%//
    $('.clickable').click(function () {
        var source = $(this).attr('src');
        var srcId = $(this).attr('id');
        var srcAlt = $(this).attr('alt');

        $('.alertWindowContentArt').prepend('<img src="' + source + '" id="' + srcId + '" alt="' + srcAlt + '" style="width: 100%; cursor: default;">');
        $('.alertWindowBgArt').css({
            "width": '100%',
            'height': '100%',
            "display": 'flex'
        });
    });

    //ON CACHE #ALERTWINDOWBGART ET ON EFFACE LE CONTENU DE #AERTWINDOWCONTENTART LORSQU'ON CLIQUE SUR LE "X" ROUGE//
    $('.alertBtnArt').click(function (e) {
        e.preventDefault();
        $('.alertWindowBgArt').css("display", 'none');
        $('.alertWindowContentArt').html('');
    });

    var key = $(this).attr('id');
    var value = speList[key];


    //LORSQU'ON PASSE LA SOURIS SUR UNE LI DE L'UL "SPECIFICATIONS", LES AUTRES LI PERDENT EN OPACITÉ, ELLES LA RÉCUPÈRENT TOUTES LORSQUE LA SOURIS SORT//
    $('#specialisations li').mouseenter(function () {
        for (key in speList) {
            if (key !== $(this).attr('id')) {
                value = speList[key];
                $('#' + key).css('opacity', '70%');
            }
        }
    }).mouseleave(function () {
        for (key in speList) {
            value = speList[key];
            $('#' + key).css('opacity', '100%');
        }
    });


});