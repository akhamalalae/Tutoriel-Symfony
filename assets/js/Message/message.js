import Loading from '../Components/displayLoading';

import ButtonForm from '../Components/buttonForm';

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
}
 
export default Message;