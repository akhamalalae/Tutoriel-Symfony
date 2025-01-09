import Routing from "fos-router";

import Translation from "../Translation/translation";

class MessageUtils {    
    constructor() {
    }

    url (idDiscussion, page , criteria) {
        let locale = new Translation().locale();

        return Routing.generate('app_message', {
            '_locale': locale, 
            'idDiscussion': idDiscussion, 
            'page': page,
            'criteria': criteria
        });
    }
    urlSearchMessage (idDiscussion, page, selectedValue) {
        let locale = new Translation().locale();

        return Routing.generate('app_search_message', {
            '_locale': locale, 
            'idDiscussion': idDiscussion, 
            'page': page,
            'idSearchDiscussion': selectedValue,
        });
    }
    criteria () {
        return  {
            'saveSearch': document.getElementById('saveSearch').value,
            'description': document.getElementById('inputDescription').value,
            'message': document.getElementById('inputMessage').value,
            'fileName': document.getElementById('inputFile').value, 
            'createdThisMonth': document.getElementById("createdThisMonth").value,
        };
    }
    cleanCriteria () {
        document.getElementById('inputMessage').value = '';
        document.getElementById('inputFile').value  = '';
        document.getElementById("createdThisMonth").value  = '';
        document.getElementById('saveSearch').value = '';
        document.getElementById('inputDescription').value = '';
    }
}
 
export default MessageUtils;