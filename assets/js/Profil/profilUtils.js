import Translation from "../Translation/translation.js";

const routes = require('../../routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

class ProfilUtils {
    constructor() {
        Routing.setRoutingData(routes);
        this.locale = new Translation().locale();
    }

    addressSearch (query) {
        return Routing.generate('app_search_address', {
            '_locale': this.locale, 
            'q': query
        });
    }

    // =========================
    // LIST
    // =========================
    list(params = '') {

        let url = Routing.generate('users_list', {
            '_locale': this.locale
        });

        if (params) {
            url += '?' + params;
        }

        return url;
    }

    // =========================
    // FORM
    // =========================
    form(id = null) {
        return Routing.generate('users_form', {
            '_locale': this.locale,
            'id': id
        });
    }

    // =========================
    // SAVE
    // =========================
    save(id = null) {
        return Routing.generate('users_save', {
            _locale: this.locale,
            id: id || null
        });
    }

    // =========================
    // DELETE
    // =========================
    delete(id) {
        return Routing.generate('users_delete', {
            '_locale': this.locale,
            'id': id
        });
    }
}

export default ProfilUtils;