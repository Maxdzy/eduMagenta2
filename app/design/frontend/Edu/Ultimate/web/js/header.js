document.addEventListener('DOMContentLoaded', function () {

    window.onscroll = function () {
        stickyHeader()
    };

    function stickyHeader() {
        const beginStickRun = 400;
        let screenOffset = window.pageYOffset,
            header = document.getElementsByClassName('page-header')[0],
            sections = document.getElementsByClassName('sections nav-sections')[0],
            logo = document.getElementsByClassName('logo')[0],
            search = document.getElementsByClassName('block block-search')[0],
            minicart = document.getElementsByClassName('minicart-wrapper')[0],
            variablesClass = {
                'sticky_header': header.classList,
                'sticky_sections': sections.classList,
                'sticky_logo': logo.classList,
                'sticky_search': search.classList,
                'sticky_minicart': minicart.classList,
            };
        Object.entries(variablesClass).forEach(([className, classObject]) => classObject.remove(className));

        if (screenOffset > beginStickRun) {
            Object.entries(variablesClass).forEach(([className, classObject]) => classObject.add(className));
        }
    }

});
