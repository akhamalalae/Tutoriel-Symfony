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
            'idSearchMessage': selectedValue,
        });
    }
    criteria () {
        const selectedCreatedThisMonth = document.querySelectorAll('input[name="createdThisMonth"]:checked');
        const valuesCreatedThisMonth = Array.from(selectedCreatedThisMonth).map(cb => cb.value);

        const selectedSaveSearch = document.querySelectorAll('input[name="saveSearch"]:checked');
        const valuesSaveSearch = Array.from(selectedSaveSearch).map(cb => cb.value);

        let createdThisMonth = false;
        let saveSearch = false;

        if (valuesCreatedThisMonth.join() == 'on' ) {
            createdThisMonth = true
        }

        if (valuesSaveSearch.join() == 'on' ) {
            saveSearch = true
        }
        
        const elementDescription = document.getElementById('inputDescription');
        const elementMessage = document.getElementById('inputMessage');
        const elementFile = document.getElementById('inputFile');

        return  {
            'saveSearch': saveSearch,
            'description': elementDescription ? elementDescription.value : '',
            'message': elementMessage ? elementMessage.value : '',
            'fileName': elementFile ? elementFile.value : '', 
            'createdThisMonth': createdThisMonth,
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