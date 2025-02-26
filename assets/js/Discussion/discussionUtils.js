const routes = require('../../js/routes.json');

import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import Translation from "../Translation/translation";

class DiscussionUtils {    
    constructor() {
        Routing.setRoutingData(routes);
        this.locale = new Translation().locale();
    }

    url (page , criteria) {

        return Routing.generate('app_list_discussion', {
            '_locale': this.locale, 
            'page': page,
            'criteria': criteria
        });
    }
    urlDeleteDiscussion (idDiscussion) {

        return Routing.generate('app_delete_discussion', {
            '_locale': this.locale , 
            'id': idDiscussion
        });
    }
    urlSearchDiscussion (selectedValue) {

        return  Routing.generate('app_search_discussion', {
            '_locale': this.locale, 
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