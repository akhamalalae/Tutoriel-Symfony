import Loading from '../Components/displayLoading';

import ButtonForm from '../Components/buttonForm';

import LoadMore from '../Components/Pagination/loadMore';

import MessageUtils from './messageUtils';
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

            listMessage.innerHTML = json.messages;

            this.add(url);

            new LoadMore().pagination();

            this.messageItemActionAnswer();

            this.messageItemActionDelete();
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
            });
        }
    }
}
 
export default Message;