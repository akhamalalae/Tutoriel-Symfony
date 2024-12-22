//import CKEDITOR from "ckeditor4";


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

const ButtonFormDiscussion = document.querySelector('#discussion_form_save');

formDiscussion.addEventListener('submit', function (e) {
    e.preventDefault();

    ButtonFormDiscussion.classList.add("disabled");

    fetch(this.action, {
        body: new FormData(e.target),
        method: 'POST'
    })
    .then(response => response.json())
    .then(json => {
        handleResponse(json);
        ButtonFormDiscussion.classList.remove("disabled");
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

window.discussionClick= discussionClick;

function discussionClick(event)
  {
    const className = "bg-dark";

    var idDiscussion = event.target.getAttribute('data-idDiscussion');

    var url = event.target.getAttribute('data-url');

    var elList = document.querySelectorAll('div.discussions-card-click');

    elList.forEach(el => el.classList.remove(className));

    var element = document.querySelector('#discussion-click-'+idDiscussion);

    element.classList.add(className);

    message(url);
  }

  window.paginationClick = paginationClick;

  function paginationClick(event)
    {
      var url = event.target.getAttribute('data-url');

      message(url);
    }

function message(url) {

    displayLoading(listMessage);

    const options = {
        method: 'GET',
    };
    fetch(url, options)
    .then(response => response.json())
    .then(json => {
        addFormMessage.innerHTML = json.html;

        listMessage.innerHTML = json.messages;

        CKEDITOR.replace('message_form_field');

        //var area = document.querySelectorAll('div.cke_notifications_area');

        //area.forEach(el => el.hidden = true);

        document.getElementById("message_form_message").hidden = true;

        addMessage(url);
    });
}

function addMessage(url)
  {
    const formMessage = document.querySelector('#form_message');
    const newMessage = document.querySelector('#new_message');
    const ButtonFormMessage = document.querySelector('#message_form_save');

    formMessage.addEventListener('submit', function (e) {
        e.preventDefault();

        var valueCKEDITOR = CKEDITOR.instances['message_form_field'].getData();

        document.getElementById("message_form_message").value = valueCKEDITOR;

        ButtonFormMessage.classList.add("disabled");

        fetch(url, {
            body: new FormData(e.target),
            method: 'POST'
        })
        .then(response => response.json())
        .then(json => {
            newMessage.innerHTML += json.html;
            ButtonFormMessage.classList.remove("disabled");
        });
    });

    formMessage.scrollIntoView({ behavior: "smooth" });
  }

// showing loading
function displayLoading(element) {
    element.innerHTML= '<div class="d-flex justify-content-center"> <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div></div>';
}

