import Routing from "fos-router";

import Translation from "../Translation/translation";

class DiscussionUtils {    
    constructor() {
    }

    url (page , criteria) {
        let locale = new Translation().locale();

        return Routing.generate('app_list_discussion', {
            '_locale': locale, 
            'page': page,
            'criteria': criteria
        });
    }
    criteria () {
        return  {
            'name': document.getElementById('inputName').value, 
            'createdThisMonth': document.getElementById("createdThisMonth").value,
            'firstName': document.getElementById('inputFirstName').value
        };
    }
    cleanCriteria () {
        document.getElementById('inputName').value = '';
        document.getElementById("createdThisMonth").value = '';
        document.getElementById('inputFirstName').value = '';
    }
}
 
export default DiscussionUtils;