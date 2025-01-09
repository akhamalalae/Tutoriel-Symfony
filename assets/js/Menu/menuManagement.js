
class Menu {    
    constructor() {
        //this.nom = nom;
    }
    
    selectItem (element) {
        let getLIitemsObject = document.querySelectorAll('.sidebar-nav li');

        //convert Object to Array type
        let liItems = Object.values(getLIitemsObject);
        
        // Foreach over LI Items
        liItems.forEach(function (data) {
            data.classList.remove('active');
        });

        element.classList.add('active');
    }
}
 
export default Menu;