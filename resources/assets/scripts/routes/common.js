export default {
    init() {
        $('.basement .nav-link').on('click', function(e) { move_arrow(e.currentTarget) } );

        function move_arrow(e) {
            let arrow = $('.basement .nav-arrow');
            let point = e.offsetLeft + e.offsetWidth / 2;
            arrow.css('left', point + 'px');
        }
        // JavaScript to be fired on all pages
    },
    finalize() {
        // JavaScript to be fired on all pages, after page specific JS is fired
    },
};
