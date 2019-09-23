document.addEventListener('DOMContentLoaded', function () {

    let current_screen_offset = 0;
    window.onscroll = function () {
        myFunction()
    };
    let header = document.getElementsByClassName('page-header')[0];
    let sections = document.getElementsByClassName('sections nav-sections')[0];

    let logo = document.getElementsByClassName("logo")[0];
    let search = document.getElementsByClassName("block block-search")[0];
    let minicart = document.getElementsByClassName("minicart-wrapper")[0];

    function myFunction() {
        let screen_offset = window.pageYOffset;
        if (screen_offset >= current_screen_offset && screen_offset > 400) {
            current_screen_offset = screen_offset;
            header.classList.add('sticky_header');
            sections.classList.add('sticky_sections');
            logo.classList.add('sticky_logo');
            search.classList.add('sticky_search');
            minicart.classList.add('sticky_minicart');
        } else if (screen_offset < current_screen_offset) {
            header.classList.remove('sticky_header');
            sections.classList.remove('sticky_sections');
            logo.classList.remove('sticky_logo');
            search.classList.remove('sticky_search');
            minicart.classList.remove('sticky_minicart');
            current_screen_offset = screen_offset;
        }
    }
});
