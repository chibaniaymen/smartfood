// ── Année courante dans le footer ──
function getYear() {
    var el = document.querySelector("#displayYear");
    if (el) el.innerHTML = new Date().getFullYear();
}
getYear();

$(document).ready(function () {

    // ── Filtres articles (sans Isotope) ──
    $('.filters_menu li').on('click', function () {
        $('.filters_menu li').removeClass('active');
        $(this).addClass('active');

        var filter = $(this).attr('data-filter');

        if (filter === '*') {
            $('.grid .all').show(300);
        } else {
            // masquer tout, puis afficher les items correspondants
            $('.grid .all').hide(200);
            $('.grid ' + filter).show(300);
        }
    });

    // ── Carousel Bootstrap auto-start ──
    $('#customCarousel1').carousel({
        interval: 4000,
        ride: 'carousel'
    });

    // ── Navbar : fermer le menu mobile après un clic ──
    $('.navbar-nav .nav-link').on('click', function () {
        var $toggler = $('.navbar-toggler');
        if ($toggler.attr('aria-expanded') === 'true') {
            $toggler.click();
        }
    });

    // ── Smooth scroll vers les ancres ──
    $('a[href^="#"]').on('click', function (e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 600, 'swing');
        }
    });
});