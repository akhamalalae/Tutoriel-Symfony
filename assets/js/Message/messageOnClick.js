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

            var element = document.querySelector('#discussion-click-'+idDiscussion);

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

            let criteria = utils.criteria();

            let url = utils.url(idDiscussion, page, criteria);

            new Message().list(url);
        }
    }
}
 
export default MessageOnClick;