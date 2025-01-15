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
    urlSearchDiscussion (selectedValue) {
        let locale = new Translation().locale();

        return  Routing.generate('app_search_discussion', {
            '_locale': locale, 
            'idSearchDiscussion': selectedValue,
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
        const elementName = document.getElementById('inputName');
        const elementFirstName = document.getElementById('inputFirstName');
        
        return  {
            'saveSearch': saveSearch,
            'description': elementDescription ? elementDescription.value : '',
            'createdThisMonth': createdThisMonth,
            'name': elementName ? elementName.value : '', 
            'firstName': elementFirstName ? elementFirstName.value : ''
        };
    }
    cleanCriteria () {
        document.getElementById('inputName').value = '';
        document.getElementById("createdThisMonth").value = '';
        document.getElementById('inputFirstName').value = '';
    }
}
 
export default DiscussionUtils;