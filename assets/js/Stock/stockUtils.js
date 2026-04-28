import Translation from "../Translation/translation.js";

const routes = require('../../routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

class StockUtils {
    constructor() {
        Routing.setRoutingData(routes);
        this.locale = new Translation().locale();
    }

    // =========================
    // LIST
    // =========================
    list(params = '') {

        let url = Routing.generate('admin_stock', {
            '_locale': this.locale
        });
    
        if (params) {
            url += '?' + params;
        }
    
        return url;
    }

    // =========================
    // FORM (CREATE / EDIT)
    // =========================
    form(id = null) {
        return Routing.generate('admin_stock_form', {
            '_locale': this.locale,
            'id': id
        });
    }

    // =========================
    // DELETE
    // =========================
    delete(id) {
        return Routing.generate('admin_stock_delete', {
            '_locale': this.locale,
            'id': id
        });
    }
}

export default StockUtils;