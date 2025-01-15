import Discussion from "./discussion";

import MessageOnClick from "../Message/MessageOnClick";

import DiscussionUtils from "./discussionUtils";

import ButtonForm from "../Components/buttonForm";

import Message from "../Message/message";

import MessageUtils from "../Message/messageUtils";

class DiscussionOnClick {  
    constructor() {
    }

    newDiscussion () {
        window.addNewDiscussion = function addNewDiscussion(event)
        {
            document.getElementById('search_message_with_criteria').innerHTML = '';

            document.getElementById('search_discussion_with_criteria').innerHTML = '';

            document.getElementById("DivNewDiscussion").style.display = "block"; 
        }
    }
    searchWithCriteria () {
        window.searchWithCriteriaClick = function searchWithCriteriaClick(event)
        {
            let view = event.target.getAttribute('data-view');

            document.getElementById('search_message_with_criteria').innerHTML = '';

            document.getElementById('search_discussion_with_criteria').innerHTML = '';

            document.getElementById("DivNewDiscussion").style.display = "none"; 

            if (view == "discussion") {
                let url = new DiscussionUtils().urlSearchDiscussion(null);

                new Discussion().formSearchDiscussion(url);

                window.changeListSavedSearch = function changeListSavedSearch() {
                    var selectBox = document.getElementById("listSavedSearch");

                    var selectedValue = selectBox.options[selectBox.selectedIndex].value;

                    let url = new DiscussionUtils().urlSearchDiscussion(selectedValue);

                    new Discussion().formSearchDiscussion(url);
                }
            }

            if (view == "message") {
                let messageUtils = new MessageUtils();

                let idDiscussion = event.target.getAttribute('data-idDiscussion');

                let page = event.target.getAttribute('data-page');

                let url = messageUtils.urlSearchMessage(idDiscussion, page, '');

                new Message().formSearchMessage(url);

                new MessageOnClick().submitSearchMessageCriteria();

                window.changeListSavedSearch = function changeListSavedSearch() {
                    var selectBox = document.getElementById("listSavedSearch");

                    var selectedValue = selectBox.options[selectBox.selectedIndex].value;

                    let url = messageUtils.urlSearchMessage(idDiscussion, page, selectedValue);

                    new Message().formSearchMessage(url);
                }
            }
        }
    }
    submitSearchDiscussuionCriteria () {
        window.submitSearchDiscussionCriteriaClick = function submitSearchDiscussionCriteriaClick(event)
        {
            let btn = document.getElementById('btn_submit_search_discussion_criteria');

            let btnSubmit = new ButtonForm();

            let utils = new DiscussionUtils();

            let criteria = utils.criteria();

            let url = utils.url(1, criteria);

            btnSubmit.addDisabled(btn);

            new Discussion().list(url);

            //utils.cleanCriteria();

            btnSubmit.removeDisabled(btn);
        }
    }
    paginationDiscussion () {
        window.paginationDiscussionClick = function paginationDiscussionClick(event)
        {
            let utils = new DiscussionUtils();

            let criteria = utils.criteria();

            let page = event.target.getAttribute('data-page');

            let url = utils.url(page, criteria);

            new Discussion().list(url);
        }
    }
}
 
export default DiscussionOnClick;