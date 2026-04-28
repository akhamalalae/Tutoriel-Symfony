import { DOM } from "./dom";
import { loadTickets, getCurrentPage, resetFilters } from "./table";
import { getFormData } from "./form";
import { showErrors, toggleOnboardingFields } from "./ui";
import { createTicketApi, updateTicketApi, deleteTicketApi, getTicketApi, getMessagesTicketApi, getEquipmentsTicketApi, getRowEquipmentsTicketApi } from "./api";
import Notification from '../Components/Notyf/notification';
import DateFormatter from '../Components/dateFormatter';
import { initSelect2Ajax } from '../Components/initSelect2Ajax.js';

const notyf = new Notification();

export function initEvents() {
    // Masquer le formulaire au départ
    DOM.toggleDisplay(DOM.cardeFormTicket, false);

    // =======================
    // CREATE BUTTON
    // =======================
    DOM.createBtn?.addEventListener('click', () => {
        resetForm();
        DOM.toggleDisplay(DOM.cardeFormTicket, true);
        DOM.toggleDisplay(DOM.cardeSearchTicket, false);
        DOM.toggleDisplay(DOM.cardeListTicket, false);
        DOM.toggleDisplay(DOM.statusForm, false);
        showErrors({});
    });

    DOM.type?.addEventListener('change', toggleOnboardingFields);

    // =======================
    // SAVE BUTTON (create ou update)
    // =======================
    DOM.saveBtn?.addEventListener('click', () => {
        const { payload, errors } = getFormData();

        if (Object.keys(errors).length) {
            showErrors(errors);
            return;
        }

        const request = DOM.ticketId?.value
            ? updateTicketApi(DOM.ticketId.value, payload)
            : createTicketApi(payload);

        request
            .then(async res => {
                const data = await res.json().catch(() => ({}));

                if (!res.ok || data.errors) {
                    showErrors(data.errors || { general: 'Erreur serveur' });
                    return;
                }

                DOM.toggleDisplay(DOM.cardeFormTicket, false);
                DOM.toggleDisplay(DOM.cardeListTicket, true);

                setTimeout(() => loadTickets(getCurrentPage()), 500);

                notyf.show('success', data.message);
            })
            .catch(() => showErrors({ general: 'Erreur connexion serveur' }));
    });


    DOM.updateTicketBtn?.addEventListener('click', () => {
        console.log('update ticket : ');
        //document.getElementById('saveTicket').click();
        
    });

    // =======================
    // CLICK GLOBAL (pagination + edit + delete)
    // =======================
    document.addEventListener('click', async (e) => {
        const target = e.target;

        // Pagination
        if (target.classList.contains('page-link')) {
            e.preventDefault();
            if (target.parentElement.classList.contains('disabled')) return;
            loadTickets(target.dataset.page);
        }


        //add
        if (target.classList.contains('create-ticket')) {
            console.log('create ticket :');
                    
            const rowEquipmentsTicketRes = await getRowEquipmentsTicketApi();
            const rowEquipmentsTicketHTML = await rowEquipmentsTicketRes.text();
            document.getElementById('addEquipment').addEventListener('click', () => {
                const row = document.createElement('tr');

                row.innerHTML = rowEquipmentsTicketHTML;

                DOM.equipmentsTableBody.appendChild(row);

                initSelect2Ajax(row.querySelectorAll('.js-select2-ajax'));
            });

            // 5. Suppression ligne
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
        }

        // Edit
        if (target.classList.contains('edit-ticket')) {
            DOM.toggleDisplay(DOM.cardeFormTicket, true);
            DOM.toggleDisplay(DOM.cardeSearchTicket, false);
            DOM.toggleDisplay(DOM.cardeListTicket, false);
            DOM.toggleDisplay(DOM.messagesContainer, true);

            try {
                const data = await getTicketApi(target.dataset.id);
                console.log('update ticket :', data);

                DOM.setValue(DOM.ticketId, data.id);
                DOM.setValue(DOM.title, data.title);
                DOM.setValue(DOM.description, data.description);
                DOM.setValue(DOM.status, data.status);
                DOM.setValue(DOM.type, data.type);
                DOM.setValue(DOM.priority, data.priority);
                DOM.setValue(DOM.company, data.company);
                DOM.setValue(DOM.onboardingEmployee, data.onboardingEmployee);
                DOM.setValue(DOM.onboardingPosition, data.onboardingPosition);
                DOM.setValue(DOM.onboardingStartDate, DateFormatter.toDatetimeLocal(data.onboardingStartDate));

                // Assigned To (Select2)
                if (DOM.assignedTo) {
                    const $assigned = $(DOM.assignedTo);
                    DOM.clearElement(DOM.assignedTo);
                    if (data.assignedTo && data.assignedToName) {
                        const option = new Option(data.assignedToName, data.assignedTo, true, true);
                        $assigned.append(option).trigger('change');
                        $assigned.trigger({
                            type: 'select2:select',
                            params: { data: { id: data.assignedTo, text: data.assignedToName } }
                        });
                    }
                }

                // =========================
                // MESSAGES HTML
                // =========================
                if (DOM.messagesContainer) {
                    const messagesRes = await getMessagesTicketApi(target.dataset.id);
                    const messagesHtml = await messagesRes.text(); // res.text() car c'est du HTML
                    DOM.messagesContainer.innerHTML = messagesHtml;
    
                    // Scroll automatique en bas
                    DOM.messagesContainer.scrollTop = DOM.messagesContainer.scrollHeight;
                }

                // =========================
                // Equipments HTML
                // =========================
                if (DOM.equipmentsTableBody) {

                    // 1. Charger les équipements existants (HTML venant du serveur)
                    const equipmentsRes = await getEquipmentsTicketApi(target.dataset.id);
                    const equipmentsHTML = await equipmentsRes.text();
                    DOM.equipmentsTableBody.innerHTML = equipmentsHTML;

                    // 4. Bouton ajout
                    const rowEquipmentsTicketRes = await getRowEquipmentsTicketApi();
                    const rowEquipmentsTicketHTML = await rowEquipmentsTicketRes.text();
                    document.getElementById('addEquipment').addEventListener('click', () => {
                        const row = document.createElement('tr');

                        row.innerHTML = rowEquipmentsTicketHTML;

                        DOM.equipmentsTableBody.appendChild(row);

                        // INIT SELECT2 SUR LA NOUVELLE LIGNE
                        initSelect2Ajax(row.querySelectorAll('.js-select2-ajax'));
                    });

                    // 5. Suppression ligne
                    document.addEventListener('click', (e) => {
                        if (e.target.classList.contains('remove-row')) {
                            e.target.closest('tr').remove();
                        }
                    });
                }

                DOM.toggleDisplay(DOM.statusForm, true);
                toggleOnboardingFields();
                showErrors({});
            } catch {
                showErrors({ general: 'Erreur chargement ticket' });
            }
        }

        // Delete
        if (target.classList.contains('remove-ticket')) {
            const id = target.dataset.id;
            if (!confirm("Voulez-vous vraiment supprimer ce ticket ?")) return;

            try {
                const res = await deleteTicketApi(id);
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.errors) {
                    alert(data.errors?.general || 'Erreur suppression');
                    return;
                }
                setTimeout(() => loadTickets(getCurrentPage()), 500);
                notyf.show('delete', data.message);
            } catch {
                alert('Erreur serveur');
            }
        }
    });

    // =======================
    // HIDE FORM
    // =======================
    DOM.hiddenFormBtn?.addEventListener('click', () => {
        DOM.toggleDisplay(DOM.cardeFormTicket, false);
        DOM.toggleDisplay(DOM.cardeListTicket, true);
    });

    // =======================
    // SEARCH
    // =======================
    DOM.searchBtn?.addEventListener('click', () => {
        DOM.toggleDisplay(DOM.cardeSearchTicket, true);
        DOM.toggleDisplay(DOM.cardeFormTicket, false);
    });

    DOM.hiddenBtn?.addEventListener('click', () => {
        DOM.toggleDisplay(DOM.cardeSearchTicket, false);
        resetFilters();
    });

    DOM.resetBtn?.addEventListener('click', resetFilters);

    document.getElementById('searchForm')?.addEventListener('input', () => {
        loadTickets(getCurrentPage());
    });

    // =======================
    // RESET FORM FUNCTION
    // =======================
    function resetForm() {
        DOM.setValue(DOM.ticketId, '');
        DOM.setValue(DOM.title, '');
        DOM.setValue(DOM.description, '');
        DOM.setValue(DOM.status, '');
        DOM.setValue(DOM.type, '');
        DOM.setValue(DOM.priority, '');
        DOM.clearElement(DOM.assignedTo);
        DOM.clearElement(DOM.equipments);
        DOM.setValue(DOM.company, '');
        DOM.setValue(DOM.onboardingEmployee, '');
        DOM.setValue(DOM.onboardingPosition, '');
        DOM.setValue(DOM.onboardingStartDate, '');
        DOM.toggleDisplay(DOM.messagesContainer, false);

    }
}