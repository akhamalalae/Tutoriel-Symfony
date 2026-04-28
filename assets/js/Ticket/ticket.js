import { loadTickets } from "./table";
import { initEvents } from "./events";
import { toggleOnboardingFields } from "./ui";
import { initSelect2Ajax } from '../Components/initSelect2Ajax.js';
import Menu from "../Menu/menuManagement";

/**
 * Point d'entrée du module Ticket
 */
document.addEventListener('DOMContentLoaded', () => {
    new Menu().selectItem(document.querySelector('#sidebar-nav-ticket'));

     // run au chargement
     window.addEventListener("load", updateFooterPosition);
    
     // run au resize
     window.addEventListener("resize", updateFooterPosition);
     
     // IMPORTANT : quand sidebar toggle
     document.addEventListener("click", function () {
        updateFooterPosition();
     });

    loadTickets();
    initEvents();
    toggleOnboardingFields();

    initSelect2Ajax('.js-select2-ajax');
});

function updateFooterPosition() {
    const footer = document.getElementById("floatingFooter");
    const main = document.querySelector(".main");

    if (!footer || !main) return;

    const rect = main.getBoundingClientRect();

    footer.style.left = rect.left + "px";
    footer.style.width = rect.width + "px";
}