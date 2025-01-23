
class LoadMore {    
    constructor() {
    }
    
    pagination () { 
        const loadMore = document.getElementById('load-more');

        if (loadMore) {
            loadMore.addEventListener('click', function() {
                const hiddenPages = document.querySelectorAll('.more-pages.d-none');
                for (let i = 0; i < 5; i++) {
                    if (hiddenPages[i]) {
                        hiddenPages[i].classList.remove('d-none');
                    }
                }
                if (document.querySelectorAll('.more-pages.d-none').length === 0) {
                    this.style.display = 'none';
                }
            });
        }
    }
}
 
export default LoadMore;