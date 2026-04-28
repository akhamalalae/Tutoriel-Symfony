
import Translation from "../Translation/translation.js";

const routes = require('../../routes.json');

import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

class TicketUtils {    
    constructor() {
        Routing.setRoutingData(routes);
        this.locale = new Translation().locale();
    }

    messagesTicket (id) {
        return Routing.generate('get_ticket_messages', {
            '_locale': this.locale,
            'id': id
        });
    }
    equipmentsTicke (id) {
        return Routing.generate('get_form_ticket_equipments', {
            '_locale': this.locale,
            'id': id
        });
    }
    softwareTicke (id) {
        return Routing.generate('get_form_ticket_software', {
            '_locale': this.locale,
            'id': id
        });
    }
    rowEquipmentsTicke () {
        return Routing.generate('get_form_row_ticket_equipments', {
            '_locale': this.locale
        });
    }
    rowSoftwareTicke () {
        return Routing.generate('get_form_row_ticket_software', {
            '_locale': this.locale
        });
    }
    searchTicket () {
        return Routing.generate('ticket_search_ajax', {
            '_locale': this.locale
        });
    }
    createTicket () {
        return Routing.generate('ticket_create', {
            '_locale': this.locale
        });
    }
    updateTicket (id) {
        return Routing.generate('ticket_update', {
            '_locale': this.locale,
            'id': id
        });
    }
    deleteTicket (id) {
        return Routing.generate('ticket_delete', {
            '_locale': this.locale,
            'id': id
        });
    }
    getTicket (id) {
        return Routing.generate('get_ticket', {
            '_locale': this.locale,
            'id': id
        });
    }
    deleteEquipmentRow(id) {
        return Routing.generate('ticket_equipment_delete_row', {
            _locale: this.locale,
            id: id
        });
    }
    deleteSoftwarRow(id) {
        return Routing.generate('ticket_software_delete_row', {
            _locale: this.locale,
            id: id
        });
    }
}
 
export default TicketUtils;