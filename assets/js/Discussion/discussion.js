import Loading from '../Components/displayLoading';

import Response from '../Components/formResponse';

import DiscussionUtils from './discussionUtils';

import Notification from '../Components/Notyf/notification';
import TagsInput from '../Components/tagsInput';
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

            this.discussionItemActionDelete();
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
            const notyf = new Notification();
            notyf.show('success', json.message);
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

            new TagsInput({
                containerId: "tags-name",
                inputId: "tag-input-name",
                hiddenId: "inputName"
            });
        
            new TagsInput({
                containerId: "tags-firstname",
                inputId: "tag-input-firstname",
                hiddenId: "inputFirstName"
            });
        });
    }
    discussionItemActionDelete () {
        window.discussionItemActionDeleteClick = discussionItemActionDeleteClick;

        function discussionItemActionDeleteClick(event)
        {
            let idDiscussion = event.target.getAttribute('data-idDiscussion');
            console.log(idDiscussion);
            const itemDiscussionBlock = document.getElementById('item-discussion-block-'+idDiscussion);
            console.log(itemDiscussionBlock);
            let url = new DiscussionUtils().urlDeleteDiscussion(idDiscussion);
            console.log(url);

            const options = {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            };
            fetch(url, options)
            .then(response => response.json())
            .then(json => {
                itemDiscussionBlock.innerHTML = '';
                const notyf = new Notification();
                notyf.show('delete', json.message);
            });
        }
    }
}
 
export default Discussion;