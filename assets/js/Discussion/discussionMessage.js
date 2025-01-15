import Menu from '../Menu/menuManagement';

import Discussion from './discussion';

import DiscussionOnClick from './discussionOnClick';

import MessageOnClick from '../Message/MessageOnClick';

import DiscussionUtils from './discussionUtils';

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

clickDiscussion.submitSearchDiscussuionCriteria();

clickDiscussion.paginationDiscussion();

let clickMessage = new MessageOnClick();

clickMessage.discussion();

clickMessage.pagination();
