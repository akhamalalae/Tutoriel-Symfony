
class Translation {    
    locale () {
        let location = window.location.href;

        if (location.search('/fr/') != -1) {
            return 'fr';
        } else if (location.search('/en/') != -1) {
            return 'en';
        } else {
            return 'en';
        }
    }
}
 
export default Translation;