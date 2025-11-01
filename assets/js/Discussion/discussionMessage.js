import Menu from '../Menu/menuManagement';

import Discussion from './discussion';

import DiscussionOnClick from './discussionOnClick';

import MessageOnClick from '../Message/messageOnClick';

import DiscussionUtils from './discussionUtils';

import SearchMessage from '../SearchMessage/searchMessage';

import SearchDiscussion from '../SearchDiscussion/searchDiscussion';

const formDiscussion = document.querySelector('#form_discussion');

let url = new DiscussionUtils().url(1, {});

new Menu().selectItem(document.querySelector('#sidebar-nav-discussion'));

let discussion = new Discussion();

discussion.list(url);

formDiscussion.addEventListener('submit', function (e) {
    let url = this.action;

    discussion.add(e, url);
});

let clickDiscussion = new DiscussionOnClick();

clickDiscussion.newDiscussion();

clickDiscussion.searchWithCriteria();

clickDiscussion.removeSearch();

clickDiscussion.submitSearchDiscussuionCriteria();

clickDiscussion.paginationDiscussion();

let clickMessage = new MessageOnClick();

clickMessage.discussion();

clickMessage.pagination();

let searchMessage = new SearchMessage();

searchMessage.removeSearch();

let searchDiscussion = new SearchDiscussion();

searchDiscussion.removeSearch();
