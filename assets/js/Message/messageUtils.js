
import Translation from "../Translation/translation";

const routes = require('../../js/routes.json');

import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

class MessageUtils {    
    constructor() {
        Routing.setRoutingData(routes);
        this.locale = new Translation().locale();
    }

    url (idDiscussion, page , criteria) {

        return Routing.generate('app_message', {
            '_locale': this.locale , 
            'idDiscussion': idDiscussion, 
            'page': page,
            'criteria': criteria
        });
    }
    urlDeleteMessage (idDiscussionMessageUser) {

        return Routing.generate('app_delete_message', {
            '_locale': this.locale , 
            'id': idDiscussionMessageUser
        });
    }
    urlSearchMessage (idDiscussion, page, selectedValue) {

        return Routing.generate('app_search_message', {
            '_locale': this.locale , 
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

        if (valuesCreatedThisMonth.join() == 'on') {
            createdThisMonth = true
        }

        if (valuesSaveSearch.join() == 'on') {
            saveSearch = true
        }
        
        const elementDescription = document.getElementById('inputDescription');
        const elementMessage = document.getElementById('inputMessage');
        const elementFile = document.getElementById('inputFile');
        const elementIdSelectedSearchMessage = document.getElementById('idSelectedSearchMessage');

        return  {
            'saveSearch': saveSearch,
            'description': elementDescription ? elementDescription.value : '',
            'message': elementMessage ? elementMessage.value : '',
            'fileName': elementFile ? elementFile.value : '', 
            'createdThisMonth': createdThisMonth,
            'IdSelectedSearchMessage': elementIdSelectedSearchMessage ? elementIdSelectedSearchMessage.value : '', 
        };
    }
    cleanCriteria () {
        document.getElementById('inputMessage').value = '';
        document.getElementById('inputFile').value  = '';
        document.getElementById("createdThisMonth").value  = '';
        document.getElementById('saveSearch').value = '';
        document.getElementById('inputDescription').value = '';
    }
    cleanForm () {
        document.getElementById('message_form_message').value = '';
        document.getElementById('message_form_toAnswer').value  = '';
        document.getElementById('message_form_files').value  = '';
        const htmlBlock = document.getElementById('add_message_to_answer');
        if (htmlBlock) {
            htmlBlock.innerHTML = '';
        }
    }
}
 
export default MessageUtils;