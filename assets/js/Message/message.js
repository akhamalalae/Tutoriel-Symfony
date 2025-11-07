import Loading from '../Components/displayLoading';

import ButtonForm from '../Components/buttonForm';

import LoadMore from '../Components/Pagination/loadMore';

import MessageOnClick from './messageOnClick';

import MessageUtils from './messageUtils';

import Notification from '../Components/Notyf/notification';

import TagsInput from '../Components/tagsInput';

class Message {    
    constructor() {
    }
    
    list (url) {
        const addFormMessage = document.querySelector('#add_form_message');

        const listMessage = document.querySelector('#message_list');

        new Loading().display(listMessage);

        const options = {
            method: 'GET',
        };
        fetch(url, options)
        .then(response => response.json())
        .then(json => {
            addFormMessage.innerHTML = json.html;

            listMessage.innerHTML =  json.messages;

            this.add(url);

            new LoadMore().pagination();

            this.messageItemActionAnswer();

            this.messageItemActionDelete();

            new MessageOnClick().scroll();

            let searchMessage = json.searchMessage;

            if (searchMessage) {
                const elementIdSelectedSearchMessage = document.getElementById('idSelectedSearchMessage');
                elementIdSelectedSearchMessage.value = searchMessage;
            }
        });
    }
    listScrollable (url) {
        const paginationScrollable = document.getElementById("pagination-scrollable");

        const paginationScrollableSpinner = document.getElementById("pagination-scrollable-spinner");
    
        if (paginationScrollable) {
            paginationScrollable.remove();
        }

        const messagesBody = document.querySelector('#messages_body');

        const paginationMessage = document.querySelector('#pagination-message');

        let messagesBodyHtml = messagesBody.innerHTML;

        new Loading().display(paginationScrollableSpinner);

        const options = {
            method: 'GET',
        };
        fetch(url, options)
        .then(response => response.json())
        .then(json => {
            const parser = new DOMParser();

            const doc = parser.parseFromString(json.messages, "text/html");

            const messagesChild = doc.querySelector("#messages_body");

            const paginationChild = doc.querySelector("#pagination-message");

            messagesBody.innerHTML = messagesChild.innerHTML + messagesBodyHtml;

            paginationMessage.innerHTML = paginationChild.innerHTML;

            new LoadMore().pagination();
        });
    }
    add (url) {
        const formMessage = document.querySelector('#form_message');

        const newMessage = document.querySelector('#new_message');
        
        const ButtonFormMessage = document.querySelector('#message_form_save');

        formMessage.addEventListener('submit', function (e) {
            e.preventDefault();

            new ButtonForm().addDisabled(ButtonFormMessage);

            fetch(url, {
                body: new FormData(e.target),
                method: 'POST'
            })
            .then(response => response.json())
            .then(json => {
                newMessage.innerHTML += json.html;
                new ButtonForm().removeDisabled(ButtonFormMessage);
                new MessageUtils().cleanForm();
                const notyf = new Notification();
                notyf.show('success', json.message);
            });
        });

        formMessage.scrollIntoView({ behavior: "smooth" });
    }
    formSearchMessage (url) {
        let searchMessage = document.getElementById('search_message_with_criteria');

        new Loading().display(searchMessage);

        const options = {
            method: 'GET',
        };
        fetch(url, options)
        .then(response => response.json())
        .then(json => {
            searchMessage.innerHTML = json.html;

            const inputDescription = document.getElementById("DivInputDescription");

            const checkboxes = document.querySelectorAll('input[name="saveSearch"]');
            
            inputDescription.style.display = "none"; 

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', (event) => {
                    if (event.target.checked) {
                        inputDescription.style.display = "block"; 
                    } else {
                        inputDescription.style.display = "none"; 
                        inputDescription.value = '';
                    }
                });
            });
            console.log('formSearchMessage');

            // Tags pour le champ Message
            new TagsInput({
                inputId: 'tag-input-message',
                containerId: 'tags-message',
                hiddenId: 'inputMessage'
            });
        
            // Tags pour le champ File Name
            new TagsInput({
                inputId: 'tag-input-file',
                containerId: 'tags-file',
                hiddenId: 'inputFile'
            });
        });
    }
    messageItemActionAnswer () {
        window.messageItemActionAnswerClick = messageItemActionAnswerClick;

        function messageItemActionAnswerClick(event)
        {
            const formMessage = document.querySelector('#form_message');
            
            const toAnswer = document.getElementById('message_form_toAnswer');

            let idMessage = event.target.getAttribute('data-idMessage');

            toAnswer.value = idMessage;

            let idDiscussionMessageUser = event.target.getAttribute('data-idDiscussionMessageUser');
            
            const copiedHTMLMessageBlock = document.getElementById('item-message-block-for-answer-'+idDiscussionMessageUser);
            
            const htmlBlock = document.getElementById('add_message_to_answer');

            htmlBlock.innerHTML = copiedHTMLMessageBlock.innerHTML;
            
            formMessage.scrollIntoView({ behavior: "smooth" });
        }
    }
    messageItemActionDelete () {
        window.messageItemActionDeleteClick = messageItemActionDeleteClick;

        function messageItemActionDeleteClick(event)
        {
            let idDiscussionMessageUser = event.target.getAttribute('data-idDiscussionMessageUser');
            
            const itemMessageBlock = document.getElementById('item-message-block-for-delete-'+idDiscussionMessageUser);
            
            let url = new MessageUtils().urlDeleteMessage(idDiscussionMessageUser);

            const options = {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            };
            fetch(url, options)
            .then(response => response.json())
            .then(json => {
                itemMessageBlock.innerHTML = '';
                const notyf = new Notification();
                notyf.show('delete', json.message);
            });
        }
    }
}
 
export default Message;