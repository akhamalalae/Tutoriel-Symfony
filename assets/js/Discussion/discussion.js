import Loading from '../Components/displayLoading';

import Response from '../Components/formResponse';

class Discussion {    
    constructor() {
    }
    
    list (url) {
        const discussionList = document.querySelector('#discussion_list');

        const listMessage = document.querySelector('#message_list');

        const addFormMessage = document.querySelector('#add_form_message');

        new Loading().display(discussionList);

        const options = {
            method: 'GET',
        };
        fetch(url, options)
        .then(response => response.json())
        .then(json => {
            discussionList.innerHTML = json.discussions;

            listMessage.innerHTML = "";

            addFormMessage.innerHTML = "";
        });
    }
    add (e, url) {
        e.preventDefault();

        const ButtonFormDiscussion = document.querySelector('#discussion_form_save');

        const newDiscussion = document.querySelector('#new_discussion');

        ButtonFormDiscussion.classList.add("disabled");

        new Loading().display(newDiscussion);

        fetch(url, {
            body: new FormData(e.target),
            method: 'POST'
        })
        .then(response => response.json())
        .then(json => {
            new Response().handle(json, newDiscussion);
            ButtonFormDiscussion.classList.remove("disabled");
        });
    }
    formSearchDiscussion (url) {
        let searchDiscussion = document.getElementById('search_discussion_with_criteria');

        new Loading().display(searchDiscussion);

        const options = {
            method: 'GET',
        };
        fetch(url, options)
        .then(response => response.json())
        .then(json => {
            searchDiscussion.innerHTML = json.html;
            
            const checkboxes = document.querySelectorAll('input[name="saveSearch"]');
            document.getElementById("DivInputDescription").style.display = "none"; 

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', (event) => {
                    if (event.target.checked) {
                        document.getElementById("DivInputDescription").style.display = "block"; 
                    } else {
                        document.getElementById("DivInputDescription").style.display = "none"; 
                        document.getElementById('inputDescription').value = '';
                    }
                });
            });
        });
    }
}
 
export default Discussion;