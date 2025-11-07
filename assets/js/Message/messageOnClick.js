import Message from "./message";

import MessageUtils from "./messageUtils";

import ButtonForm from "../Components/buttonForm";

class MessageOnClick {    
    constructor() {
    }
    
    discussion () {
        window.discussionClick= discussionClick;

        function discussionClick(event)
        {
            document.getElementById('search_discussion_with_criteria').innerHTML = '';

            document.getElementById('search_message_with_criteria').innerHTML = '';

            document.getElementById("DivNewDiscussion").style.display = "none"; 

            const className = "bg-primary";

            var idDiscussion = event.target.getAttribute('data-idDiscussion');

            var url = event.target.getAttribute('data-url');

            var elList = document.querySelectorAll('div.discussions-card-click');

            elList.forEach(el => el.classList.remove(className));

            var element = document.querySelector('#item-discussion-block-'+idDiscussion);

            element.classList.add(className);

            new Message().list(url);
        }
    }
    submitSearchMessageCriteria () {
        window.submitSearchMessageCriteriaClick = function submitSearchMessageCriteriaClick(event)
        {
            let btn = document.getElementById('btn_submit_search_message_criteria');

            let btnSubmit = new ButtonForm();

            btnSubmit.addDisabled(btn);

            let utils = new MessageUtils();

            let idDiscussion = event.target.getAttribute('data-idDiscussion');

            let page = event.target.getAttribute('data-page');

            let criteria = utils.criteria();

            let url = utils.url(idDiscussion, page, criteria);
        
            new Message().list(url);

            btnSubmit.removeDisabled(btn);
            
            //utils.cleanCriteria();
        }
    }
    pagination () {
        window.paginationClick = function paginationClick(event)
        {
            let utils = new MessageUtils();

            let idDiscussion = event.target.getAttribute('data-idDiscussion');

            let page = event.target.getAttribute('data-page');

            let paginationScrollableMessages = event.target.getAttribute('data-view');

            let criteria = utils.criteria();

            let url = utils.url(idDiscussion, page, criteria);

            if (paginationScrollableMessages == "paginationScrollableMessages") {
                new Message().listScrollable(url);
            } else {
                new Message().list(url);
            }
        }
    }
    scroll () {
        let scrollableElements = document.querySelectorAll(".scrollable");

        // Parcourir chaque élément et ajouter un écouteur d'événement
        scrollableElements.forEach(function(element) {
            element.addEventListener("scroll", function(event) {
                let utils = new MessageUtils();

                let idDiscussion = event.target.getAttribute('data-idDiscussion');

                let criteria = utils.criteria();

                // Vérifier si l'utilisateur est en haut de l'élément
                if (element.scrollTop === 0) {
                    console.log("Un utilisateur est en haut d'un élément !");
                    
                    /*
                    let page = parseInt(event.target.getAttribute('data-page'), 10) + 1;

                    let url = utils.url(idDiscussion, page, criteria);
                    
                    console.log(idDiscussion, page);
                    new Message().list(url);
                    */
                }

                // Vérifier si l'utilisateur est en bas de l'élément
                if (element.scrollTop + element.clientHeight >= element.scrollHeight) {
                    console.log("Un utilisateur est en bas d'un élément !");
                }

            });
        });
    }
}
 
export default MessageOnClick;