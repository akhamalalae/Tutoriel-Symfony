const routes = require('../../js/routes.json');

import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import Translation from "../Translation/translation";

class ProfilUtils {    
    constructor() {
        Routing.setRoutingData(routes);
        this.locale = new Translation().locale();
    }

    url (query) {
        return Routing.generate('app_search_address', {
            '_locale': this.locale, 
            'q': query
        });
    }
}
 
export default ProfilUtils;