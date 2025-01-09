import Routing from "fos-router";

import Translation from "../Translation/translation";

class ProfilUtils {    
    constructor() {
    }

    url (query) {
        let locale = new Translation().locale();

        return Routing.generate('app_search_address', {
            '_locale': locale, 
            'q': query
        });
    }
}
 
export default ProfilUtils;