
import Translation from "../Translation/translation";

const routes = require('../../routes.json');

import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

class SearchDiscussionUtils {    
    constructor() {
        Routing.setRoutingData(routes);
        this.locale = new Translation().locale();
    }

    delete (id) {
        return Routing.generate('app_delete_search_discussion', {
            '_locale': this.locale , 
            'id': id
        });
    }
}
 
export default SearchDiscussionUtils;