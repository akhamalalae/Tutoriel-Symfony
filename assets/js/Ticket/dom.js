/**
 * Centralise tous les éléments DOM utilisés dans le module Ticket
 * → évite duplication
 * → évite erreurs null partout
 */
export const DOM = {
    table: document.getElementById('ticketTableBody') || null,
    createBtn: document.getElementById('createTicketBtn') || null,
    searchBtn: document.getElementById('searchTicketBtn') || null,
    hiddenBtn: document.getElementById('hiddenTicketBtn') || null,
    resetBtn: document.getElementById('resetTicketBtn') || null,
    saveBtn: document.getElementById('saveTicket') || null,
    saveStayBtn: document.getElementById('saveStayTicket') || null,
    hiddenFormBtn: document.getElementById('hiddenFormBtn') || null,
    cardeFormTicket: document.getElementById('cardeFormTicket') || null,
    cardeSearchTicket: document.getElementById('cardeSearchTicket') || null,
    cardeListTicket: document.getElementById('cardeListTicket') || null,
    messagesContainer: document.getElementById('messagesContainer'),
    ticketId: document.getElementById('ticketId') || null,
    statusForm: document.getElementById('statusForm') || null,
    title: document.getElementById('ticket_form_title') || null,
    description: document.getElementById('ticket_form_description') || null,
    status: document.getElementById('ticket_form_status') || null,
    onboardingFields: document.getElementById('onboardingFields') || null,
    offboardingDiv: document.getElementById('offboardingDiv') || null,
    offboardingFields: document.getElementById('offboardingFields') || null,
    type: document.getElementById('ticket_form_type') || null,
    priority: document.getElementById('ticket_form_priority') || null,
    assignedTo: document.getElementById('ticket_form_assignedTo') || null,
    targetUser: document.getElementById('ticket_form_targetUser') || null,
    company: document.getElementById('ticket_form_company') || null,
    message: document.getElementById('ticket_form_message') || null,
    onboardingPosition: document.getElementById('ticket_form_onboarding_position') || null,
    onboardingStartDate: document.getElementById('ticket_form_onboarding_startDate') || null,
    offboardingRevokeAccess: document.getElementById('ticket_form_offboarding_revokeAccess') || null,
    offboardingReturnEquipment: document.getElementById('ticket_form_offboarding_returnEquipment') || null,
    offboardingEndDate: document.getElementById('ticket_form_offboarding_endDate') || null,
    equipmentsTableBody: document.getElementById('equipmentsTableBody') || null,
    ticketSoftwareTableBody: document.getElementById('ticketSoftwareTableBody') || null,

    /**
     * Helper pour assigner une valeur si l'élément existe
     */
    setValue(element, value) {
        if (element) {
            element.value = value ?? '';
        }
    },

    /**
     * Helper pour vider un select (ou input) si l'élément existe
     */
    clearElement(element) {
        if (!element) return;
        if (element.tagName === 'SELECT') {
            $(element).empty().trigger('change');
        } else {
            element.value = '';
        }
    },

    /**
     * Helper pour cacher/montrer un élément
     */
    toggleDisplay(element, show = true) {
        if (element) {
            element.style.display = show ? 'block' : 'none';
        }
    }
};