import { DOM } from "./dom";
import { loadTickets, getCurrentPage, resetFilters } from "./table";
import { getFormData } from "./form";
import { 
    showErrors,
    toggleOnboardingFields,
    toggleOffboardingFields,
    toggleButtonLoading 
} from "./ui";
import {
    createTicketApi,
    updateTicketApi,
    deleteTicketApi,
    getTicketApi,
    getMessagesTicketApi,
    getEquipmentsTicketApi,
    getRowEquipmentsTicketApi,
    getRowSoftwareTicketApi,
    getTicketSoftwareApi,
    deleteEquipmentRowApi,
    deleteSoftwareRowApi
} from "./api";
import Loading from '../Components/displayLoading';
import { TicketState } from './ticketState';
import Notification from '../Components/Notyf/notification';
import DateFormatter from '../Components/dateFormatter';
import { initSelect2Ajax } from '../Components/initSelect2Ajax.js';

const notyf = new Notification();

export function initEvents() {
    initUI();
    initActions();
    initGlobalClick();
    initSearch();
}

/* ======================
   INIT
====================== */

function initUI() {
    DOM.toggleDisplay(DOM.cardeFormTicket, false);
    DOM.type?.addEventListener('change', toggleOnboardingFields);
    DOM.type?.addEventListener('change', toggleOffboardingFields);
}

/* ======================
   ACTIONS (buttons)
====================== */

function initActions() {
    DOM.createBtn?.addEventListener('click', onCreate);
    DOM.saveBtn?.addEventListener('click', onSave);
    DOM.saveStayBtn?.addEventListener('click', onSaveStay);
    DOM.hiddenFormBtn?.addEventListener('click', onCancelForm);
}

/* ======================
   CREATE
====================== */

function onCreate() {
    resetForm();

    DOM.toggleDisplay(DOM.cardeFormTicket, true);
    DOM.toggleDisplay(DOM.cardeSearchTicket, false);
    DOM.toggleDisplay(DOM.cardeListTicket, false);
    DOM.toggleDisplay(DOM.statusForm, false);

    showErrors({});
}

/* ======================
   SAVE (CREATE / UPDATE)
====================== */

async function onSave() {

    if (DOM.saveBtn) toggleButtonLoading(DOM.saveBtn, true);
    if (DOM.saveStayBtn) toggleButtonLoading(DOM.saveStayBtn, true);

    try {
        const result = await saveTicket();

        if (!result.success) return;

        DOM.toggleDisplay(DOM.cardeFormTicket, false);
        DOM.toggleDisplay(DOM.cardeListTicket, true);

        loadTickets(getCurrentPage());

        notyf.show('success', result.data.message);

    } catch (e) {
        console.error(e);
        notyf.show('error', 'Erreur serveur');

    } finally {
        if (DOM.saveBtn) toggleButtonLoading(DOM.saveBtn, false);
        if (DOM.saveStayBtn) toggleButtonLoading(DOM.saveStayBtn, false);
    }
}

async function onSaveStay() {
    
    if (DOM.saveBtn) toggleButtonLoading(DOM.saveBtn, true);
    if (DOM.saveStayBtn) toggleButtonLoading(DOM.saveStayBtn, true);

    try {
        const result = await saveTicket();

        if (!result.success) return;

        loadTickets(getCurrentPage());

        notyf.show('success', result.data.message);

    } catch (e) {
        console.error(e);
        notyf.show('error', 'Erreur serveur');

    } finally {
        if (DOM.saveBtn) toggleButtonLoading(DOM.saveBtn, false);
        if (DOM.saveStayBtn) toggleButtonLoading(DOM.saveStayBtn, false);
    }
}

async function saveTicket() {
    const { payload, errors } = getFormData();

    console.log('Payload to send :', payload, errors);

    try {
        const res = DOM.ticketId?.value
            ? await updateTicketApi(DOM.ticketId.value, payload)
            : await createTicketApi(payload);

        const data = await res.json().catch(() => ({}));

        console.log('API response :', { res, data });

        if (!res.ok || data.errors) {
            if (Object.keys(data.errors).length) {
                notyf.show('delete', Object.values(data.errors).join('<br>'));
            }
            showErrors(data.errors || { general: 'Erreur serveur' });
            return { success: false };
        }

        // Injecte l'ID si c'est une création
        if (!DOM.ticketId.value && data.id) {
            DOM.ticketId.value = data.id;
        }

        // Refresh live
        await loadMessages(DOM.ticketId.value);
        await loadEquipments(DOM.ticketId.value);
        await loadSoftware(DOM.ticketId.value);

        DOM.setValue(DOM.message, '');

        return { success: true, data };

    } catch {
        showErrors({ general: 'Erreur connexion serveur' });
        return { success: false };
    }
}

/* ======================
   GLOBAL CLICK (DELEGATION)
====================== */

function initGlobalClick() {
    document.addEventListener('click', async (e) => {
        const target = e.target;

        if (target.classList.contains('page-link')) {
            return handlePagination(target, e);
        }

        if (target.classList.contains('create-ticket')) {
            return handleCreateTicket();
        }

        if (target.classList.contains('edit-ticket')) {
            return handleEdit(target);
        }

        if (target.classList.contains('remove-ticket')) {
            return handleDelete(target);
        }

        if (target.classList.contains('remove-row')) {
            const card = target.closest('.card');
            handleDeleteRow(card);
        }

        if (target.id === 'addEquipment') {
            return addEquipmentRow();
        }

        if (target.id === 'addTicketSoftware') {
            return addTicketSoftwareRow();
        }
    });
}

/* ======================
   PAGINATION
====================== */

function handlePagination(target, e) {
    e.preventDefault();
    if (target.parentElement.classList.contains('disabled')) return;

    loadTickets(target.dataset.page);
}

/* ======================
   CREATE TICKET (INIT EQUIPMENTS)
====================== */

async function handleCreateTicket() {
    await loadEquipmentTemplate();
    await loadSoftwareTemplate();
}

/* ======================
   EDIT
====================== */

async function handleEdit(target) {
    DOM.toggleDisplay(DOM.cardeFormTicket, true);
    DOM.toggleDisplay(DOM.cardeSearchTicket, false);
    DOM.toggleDisplay(DOM.cardeListTicket, false);
    DOM.toggleDisplay(DOM.messagesContainer, true);

    try {
        const data = await getTicketApi(target.dataset.id);

        TicketState.currentStatus = data.status;
        TicketState.ticketId = data.id;

        fillForm(data);
        await loadMessages(target.dataset.id);
        await loadEquipments(target.dataset.id);
        await loadSoftware(target.dataset.id);

        DOM.toggleDisplay(DOM.statusForm, true);
        toggleOnboardingFields();
        toggleOffboardingFields();
        showErrors({});

    } catch (error) {
        console.error(error);
    
        showErrors({
            general: error?.message || 'Erreur chargement ticket'
        });
    }
}

/* ======================
   DELETE
====================== */

async function handleDelete(target) {
    const id = target.dataset.id;

    if (!confirm("Voulez-vous vraiment supprimer ce ticket ?")) return;

    try {
        const res = await deleteTicketApi(id);
        const data = await res.json().catch(() => ({}));

        if (!res.ok || data.errors) {
            alert(data.errors?.general || 'Erreur suppression');
            return;
        }

        loadTickets(getCurrentPage());
        notyf.show('delete', data.message);

    } catch {
        alert('Erreur serveur');
    }
}

async function handleDeleteRow(card) {

    const id = card?.dataset.id;
    const type = card?.dataset.type;

    console.log('Delete row:', { id, type });

    // ligne non persistée
    if (!id) {
        card.remove();
        return;
    }

    let request;

    if (type === 'card_equipment_form') {
        request = deleteEquipmentRowApi(id);
    } else if (type === 'card_software_form') {
        request = deleteSoftwareRowApi(id);
    } else {
        console.warn('Unknown type:', type);
        return;
    }

    try {
        const res = await request;
        const data = await res.json().catch(() => null);

        if (!res.ok) {
            throw new Error(data?.message || 'Erreur suppression');
        }

        // suppression UI
        card.remove();

        // notif
        notyf.show('delete', data?.message || 'Suppression effectuée');

    } catch (err) {
        console.error(err);
        notyf.show('delete', err.message || 'Erreur suppression');
    }
}

/* ======================
   FORM HELPERS
====================== */

function fillForm(data) {
    console.log('Filling form with data :', data);
    DOM.setValue(DOM.ticketId, data.id);
    DOM.setValue(DOM.title, data.title);
    DOM.setValue(DOM.description, data.description);
    DOM.setValue(DOM.status, data.status);
    DOM.setValue(DOM.type, data.type);
    DOM.setValue(DOM.priority, data.priority);
    DOM.setValue(DOM.company, data.company);

    if(DOM.type?.value === 'onboarding') {
        DOM.setValue(DOM.onboardingPosition, data.onboardingPosition);
        DOM.setValue(
            DOM.onboardingStartDate,
            DateFormatter.toDatetimeLocal(data.onboardingStartDate)
        );
    }
    
    if(DOM.type?.value === 'offboarding') {
        DOM.setValue(DOM.offboardingEndDate, DateFormatter.toDatetimeLocal(data.offboardingEndDate));
        DOM.offboardingRevokeAccess.checked = data.offboardingRevokeAccess;
        DOM.offboardingReturnEquipment.checked = data.offboardingReturnEquipment;
    }

    // Select2
    if (DOM.assignedTo) {
        const $assigned = $(DOM.assignedTo);
        DOM.clearElement(DOM.assignedTo);

        if (data.assignedTo && data.assignedToName) {
            const option = new Option(data.assignedToName, data.assignedTo, true, true);
            $assigned.append(option).trigger('change');
        }
    }

    if(DOM.targetUser) {
        const $target = $(DOM.targetUser);
        DOM.clearElement(DOM.targetUser);

        if (data.targetUser && data.targetUserName) {
            const option = new Option(data.targetUserName, data.targetUser, true, true);
            $target.append(option).trigger('change');
        }
    }
}

/* ======================
   MESSAGES
====================== */

async function loadMessages(ticketId) {
    if (!DOM.messagesContainer) return;

    new Loading().display(DOM.messagesContainer);

    const res = await getMessagesTicketApi(ticketId);
    const html = await res.text();

    DOM.messagesContainer.innerHTML = html;
    DOM.messagesContainer.scrollTop = DOM.messagesContainer.scrollHeight;
}

/* ======================
   Software
====================== */
let softwareTemplate = null;
let softwareIndex = 0;

async function loadSoftwareTemplate() {
    if (!softwareTemplate) {
        const res = await getRowSoftwareTicketApi();
        softwareTemplate = await res.text();
    }
}

async function loadSoftware(ticketId) {
    if (!DOM.ticketSoftwareTableBody) return;

    new Loading().display(DOM.ticketSoftwareTableBody);

    const res = await getTicketSoftwareApi(ticketId);
    const html = await res.text();

    DOM.ticketSoftwareTableBody.innerHTML = html;

    await loadSoftwareTemplate();

    const element = DOM.ticketSoftwareTableBody.querySelectorAll('.js-select2-ajax');

    initSelect2Ajax(element);
}

function addTicketSoftwareRow() {
    if (!DOM.ticketSoftwareTableBody || !softwareTemplate) return;

    softwareIndex = DOM.ticketSoftwareTableBody.querySelectorAll('.card').length;

    const row = document.createElement('div');
    row.classList.add('card');

    const html = softwareTemplate.replace(/__name__/g, softwareIndex);

    row.innerHTML = html;

    DOM.ticketSoftwareTableBody.appendChild(row);

    softwareIndex++;

    const element = DOM.ticketSoftwareTableBody.querySelectorAll('.js-select2-ajax');

    initSelect2Ajax(element);
}

/* ======================
   EQUIPMENTS
====================== */

let equipmentTemplate = null;
let equipmentIndex = 0;

async function loadEquipmentTemplate() {
    if (!equipmentTemplate) {
        const res = await getRowEquipmentsTicketApi();
        equipmentTemplate = await res.text();
    }
}

async function loadEquipments(ticketId) {
    if (!DOM.equipmentsTableBody) return;

    new Loading().display(DOM.equipmentsTableBody);

    const res = await getEquipmentsTicketApi(ticketId);
    const html = await res.text();

    DOM.equipmentsTableBody.innerHTML = html;

    await loadEquipmentTemplate();

    const element = DOM.equipmentsTableBody.querySelectorAll('.js-select2-ajax');

    initSelect2Ajax(element, { company: DOM.company?.value});

    // Même logique pour détecter changement de company
    if (DOM.company) {
        DOM.company.addEventListener('change', () => {
            initSelect2Ajax(element, { company: DOM.company?.value});
        });
    }
}

function addEquipmentRow() {
    if (!DOM.equipmentsTableBody || !equipmentTemplate) return;

    equipmentIndex = DOM.equipmentsTableBody.querySelectorAll('.card').length;

    const row = document.createElement('div');
    row.classList.add('card');

    const html = equipmentTemplate.replace(/__name__/g, equipmentIndex);

    row.innerHTML = html;

    DOM.equipmentsTableBody.appendChild(row);

    equipmentIndex++;

    const element = DOM.equipmentsTableBody.querySelectorAll('.js-select2-ajax');

    initSelect2Ajax(element, { company: DOM.company?.value});

    // Même logique pour détecter changement de company
    if (DOM.company) {
        DOM.company.addEventListener('change', () => {
            initSelect2Ajax(element, { company: DOM.company?.value});
        });
    }
}

/* ======================
   SEARCH
====================== */

function initSearch() {
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
}

/* ======================
   RESET FORM
====================== */

function resetForm() {
    DOM.setValue(DOM.ticketId, '');
    DOM.setValue(DOM.title, '');
    DOM.setValue(DOM.description, '');
    DOM.setValue(DOM.status, 'open');
    TicketState.currentStatus = 'open';
    TicketState.ticketId = null;
    DOM.setValue(DOM.type, 'incident');
    DOM.setValue(DOM.priority, 'low');
    DOM.clearElement(DOM.assignedTo);
    DOM.clearElement(DOM.targetUser);
    DOM.setValue(DOM.company, '');
    DOM.setValue(DOM.onboardingPosition, '');
    DOM.setValue(DOM.onboardingStartDate, '');
    DOM.setValue(DOM.offboardingEndDate, '');
    if (DOM.offboardingRevokeAccess) {
        DOM.offboardingRevokeAccess.checked = false;
    }
    if (DOM.offboardingReturnEquipment) {
        DOM.offboardingReturnEquipment.checked = false;
    }
    DOM.setValue(DOM.message, '');

    if (DOM.messagesContainer) DOM.messagesContainer.innerHTML = '';
    if (DOM.equipmentsTableBody) DOM.equipmentsTableBody.innerHTML = '';
    if (DOM.ticketSoftwareTableBody) DOM.ticketSoftwareTableBody.innerHTML = '';
}

/* ======================
   CANCEL
====================== */
function onCancelForm() {
    DOM.toggleDisplay(DOM.cardeFormTicket, false);
    DOM.toggleDisplay(DOM.cardeListTicket, true);
    loadTickets(getCurrentPage());
}