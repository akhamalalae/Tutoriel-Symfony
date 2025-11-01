import Notification from '../Components/Notyf/notification';
import SearchDiscussionUtils from './searchDiscussionUtils';

class SearchDiscussion {    
    constructor() {
    }

    removeSearch () {
        window.removeSearchDiscussionClick = function removeSearchDiscussionClick(event)
        {
            const button = event.currentTarget;

            const id = button.getAttribute('data-id');

            if (id) {
                let url = new SearchDiscussionUtils().delete(id);

                const options = {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                };
                fetch(url, options)
                .then(response => response.json())
                .then(json => {
                    const notyf = new Notification();
                    notyf.show('delete', json.message);
                });
                const btn = document.getElementById('btn-refresh-search');
                if (btn) {
                    btn.click();
                }  
            }
        }
    }
}
 
export default SearchDiscussion;