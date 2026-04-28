import TicketUtils from "./ticketUtils";

const utils = new TicketUtils();

/**
 * GET liste tickets (HTML)
 */
export function fetchTickets(params) {
    return fetch(utils.searchTicket() + "?" + params.toString())
        .then(res => res.text());
}

/**
 * CREATE ticket
 */
export function createTicketApi(payload) {
    return fetch(utils.createTicket(), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });
}

/**
 * UPDATE ticket
 */
export function updateTicketApi(id, payload) {
    return fetch(utils.updateTicket(id), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });
}

/**
 * DELETE ticket
 */
export function deleteTicketApi(id) {
    return fetch(utils.deleteTicket(id), {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
}

/**
 * GET 1 ticket (JSON)
 */
export function getTicketApi(id) {
    return fetch(utils.getTicket(id)).then(res => res.json());
}

/**
 * GET 1 ticket Messages (JSON)
 */
export function getMessagesTicketApi(id) {
    return fetch(utils.messagesTicket(id));
}

export function getEquipmentsTicketApi(id) {
    return fetch(utils.equipmentsTicke(id));
}

export function getTicketSoftwareApi(id) {
    return fetch(utils.softwareTicke(id));
}

export function getRowEquipmentsTicketApi() {
    return fetch(utils.rowEquipmentsTicke());
}

export function getRowSoftwareTicketApi() {
    return fetch(utils.rowSoftwareTicke());
}

export function deleteEquipmentRowApi(id) {
    return fetch(utils.deleteEquipmentRow(id), {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
}

export function deleteSoftwareRowApi(id) {
    return fetch(utils.deleteSoftwarRow(id), {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
}
