/**
 *
 * @typedef {Object} FormResponse
 * @property {string} code
 * @property {Object} errors
 * @property {string} html
 */

const formDiscussion = document.querySelector('#form_discussion');
const discussionList = document.querySelector('#discussion_list');

const addFormMessage = document.querySelector('#add_form_message');
const listMessage = document.querySelector('#message_list');

formDiscussion.addEventListener('submit', function (e) {
    e.preventDefault();

    fetch(this.action, {
        body: new FormData(e.target),
        method: 'POST'
    })
    .then(response => response.json())
    .then(json => {
        handleResponse(json);
    });
});

/**
 *
 * @param {FormResponse} response
 */
const handleResponse = function (response) {
    switch(response.code) {
        case 'DISCUSSION_ADDED_SUCCESSFULLY':
            discussionList.innerHTML += response.html;
            break;
        case 'DISCUSSION_INVALID_FORM':
            handleErrors(response.errors);
            break;
    }
}

function discussionClick()
{
    var page = event.srcElement.getAttribute('data-page');
    var idDiscussion = event.srcElement.getAttribute('data-idDiscussion');

    console.log('discussionClick',page, idDiscussion);
    message(idDiscussion, page);
}

function paginationClick()
{
    var page = event.srcElement.getAttribute('data-page');
    var idDiscussion = event.srcElement.getAttribute('data-idDiscussion');

    console.log('paginationClick',page, idDiscussion);
    message(idDiscussion, page);
}

function message(idDiscussion, page) {
    var url = urlMessage(idDiscussion, page);
    const options = {
        method: 'GET',
    };

    fetch(url, options)
    .then(response => response.json())
    .then(json => {
        addFormMessage.innerHTML = json.html;

        listMessage.innerHTML = json.messages;

        addMessage(idDiscussion, page);
    });
}

function addMessage(idDiscussion, page)
{
    const formMessage = document.querySelector('#form_message');
    const newMessage = document.querySelector('#new_message');
    
    var url = urlMessage(idDiscussion, page);

    formMessage.addEventListener('submit', function (e) {
        e.preventDefault();

        fetch(url, {
            body: new FormData(e.target),
            method: 'POST'
        })
        .then(response => response.json())
        .then(json => {
            console.log(json);
            //handleResponse(json);
            newMessage.innerHTML += json.html;
        });
    });

    formMessage.scrollIntoView({ behavior: "smooth" });
}

function urlMessage(idDiscussion, page)
{
    var url = '{{ path("app_create_message", {'idDiscussion': 'discussion_id', 'page': 'page_id'}) }}'; 
    url = url.replace("discussion_id", idDiscussion);
    url = url.replace("page_id", page);

    return url;
}