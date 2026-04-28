import Translation from "../Translation/translation.js";
const routes = require('../../routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

function autocomplete() {
    return Routing.generate('autocomplete', {
        '_locale': new Translation().locale()
    });
}

/**
 * Initialise un select2 avec AJAX et options personnalisables
 * syntaxe exemple : initSelect2Ajax(row.querySelectorAll('.js-select2-ajax'), { company: DOM.company?.value});
 * @param {string} selector - le sélecteur jQuery
 * @param {object} extraParams - options supplémentaires à passer à l'ajax
 */
export function initSelect2Ajax(selector, extraParams = {}) {
    $(selector).each(function () {
        const $el = $(this);

        // reset si déjà initialisé
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }

        const type = $el.data('type');
        const isMultiple = $el.prop('multiple');

        $el.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '',
            allowClear: !isMultiple,
            multiple: isMultiple,
            minimumInputLength: 1,

            ajax: {
                url: autocomplete(),
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term,
                    type: type,
                    ...extraParams // <-- fusionne les options supplémentaires
                }),
                processResults: data => ({
                    results: data.map(item => ({
                        id: item.id,
                        text: item.name
                    }))
                }),
                cache: true
            }
        });
    });
}