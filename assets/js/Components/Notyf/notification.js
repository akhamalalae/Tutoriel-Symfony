import { Notyf } from 'notyf';

class Notification {   
    constructor() {
    }
    
    show (type, body) {
        const notyf = new Notyf({
            position: {
                x: 'center', // ou 'right' ou 'left'
                y: 'top'     // Position en haut
            },
            duration: 8000, // Durée d'affichage en millisecondes
            dismissible: true, // Permet de fermer la notification
        });

        if (type == 'success') {
            notyf.success(body);
        }

        if (type == 'delete') {
            notyf.error(body);
        }
    }
}
 
export default Notification;