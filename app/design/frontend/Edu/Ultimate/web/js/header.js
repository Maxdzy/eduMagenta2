document.addEventListener('DOMContentLoaded', function () {

    let currentScreenOffset = 0;
    window.onscroll = function () {
        stickyHeader()
    };
    let header = document.getElementsByClassName('page-header')[0];
    let sections = document.getElementsByClassName('sections nav-sections')[0];
    let logo = document.getElementsByClassName('logo')[0];
    let search = document.getElementsByClassName('block block-search')[0];
    let minicart = document.getElementsByClassName('minicart-wrapper')[0];

    function stickyHeader() {
        let screenOffset = window.pageYOffset;
        const beginStickRun = 400;
        header.classList.remove('sticky_header');
        sections.classList.remove('sticky_sections');
        logo.classList.remove('sticky_logo');
        search.classList.remove('sticky_search');
        minicart.classList.remove('sticky_minicart');
        if (screenOffset > beginStickRun) {
            currentScreenOffset = screenOffset;
            header.classList.add('sticky_header');
            sections.classList.add('sticky_sections');
            logo.classList.add('sticky_logo');
            search.classList.add('sticky_search');
            minicart.classList.add('sticky_minicart');
        }
    }
});
