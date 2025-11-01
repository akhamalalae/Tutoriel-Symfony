import Notification from '../Components/Notyf/notification';
import SearchMessageUtils from './searchMessageUtils';

class SearchMessage {    
    constructor() {
    }

    removeSearch () {
        window.removeSearchMessageClick = function removeSearchMessageClick(event)
        {
            const button = event.currentTarget;

            const id = button.getAttribute('data-id');

            if (id) {
                let url = new SearchMessageUtils().delete(id);

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
 
export default SearchMessage;