import { DOM } from "./dom";
import { TicketState } from './ticketState';

/**
 * Clear erreurs UI
 */
function clearErrors() {
    document.querySelectorAll('[id^="error_"]').forEach(el => {
        el.textContent = '';
    });

    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
}

/**
 * Récupère et valide les données du formulaire
 */
export function getFormData() {
    clearErrors();

    const payload = {
        title: DOM.title?.value.trim(),
        description: DOM.description?.value.trim(),
        status: DOM.status?.value || 'open',
        type: DOM.type?.value || null,
        priority: DOM.priority?.value || null,
        assignedTo: DOM.assignedTo?.value || null,
        targetUser: DOM.targetUser?.value || null,
        company: DOM.company?.value || null,
        onboardingPosition : DOM.onboardingPosition?.value.trim() || null,
        onboardingStartDate : DOM.onboardingStartDate?.value || null,
        offboardingRevokeAccess : DOM.offboardingRevokeAccess?.checked || false,
        offboardingReturnEquipment : DOM.offboardingReturnEquipment?.checked || false,
        offboardingEndDate : DOM.offboardingEndDate?.value || null,
        message: DOM.message?.value.trim() || null,
        ticketEquipment: [],
        ticketSoftware: []
    };

    // ==========================
    // Récupérer les lignes existantes / nouvelles
    // ==========================
    const softwareRows = document.querySelectorAll('#ticketSoftwareTableBody .card');

    softwareRows.forEach(row => {
        const swData = {
            id: row.querySelector('[name$="[id]"]')?.value || null,
            softwareProduct: row.querySelector('[name$="[softwareProduct]"]')?.value || null,
            softwareLicense: {
                licenseKey: row.querySelector('[name$="[softwareLicense][licenseKey]"]')?.value || null,
                expiresAt: row.querySelector('[name$="[softwareLicense][expiresAt]"]')?.value || null,
            },
            comment: row.querySelector('[name$="[comment]"]')?.value || null,
            status: row.querySelector('[name$="[status]"]')?.value || null,
        };

        payload.ticketSoftware.push(swData);
    });

    const rows = document.querySelectorAll('#equipmentsTableBody .card');

    rows.forEach(row => {
        const eqData = {
            id: row.querySelector('[name$="[id]"]')?.value || null, // si existant
            equipmentModel: row.querySelector('[name$="[equipmentModel]"]')?.value || null,
            equipmentAsset: row.querySelector('[name$="[equipmentAsset]"]')?.value || null,
            comment: row.querySelector('[name$="[comment]"]')?.value || null,
            status: row.querySelector('[name$="[status]"]')?.value || null,
        };

        payload.ticketEquipment.push(eqData);
    });

    const errors = {};

    /*

    for (const eq of payload.ticketEquipment) {
        if (!eq.equipmentModel) {
            errors.ticketEquipment = 'Le choix de l’équipement est obligatoire';
            break;
        }
    }

    if (!payload.title) errors.title = 'Le titre est obligatoire';
    if (!payload.description) errors.description = 'La description est obligatoire';
    if (!payload.type) errors.type = 'Le type est obligatoire';
    if (!payload.priority) errors.priority = 'La priorité est obligatoire';
    if (!payload.company) errors.company = 'La société est obligatoire';

    if (payload.status === 'closed' && TicketState.currentStatus !== 'resolved') {
        errors.status = 'Le ticket doit être résolu avant d’être fermé';
    }

    if (payload.type == 'onboarding') {
        if (!payload.onboardingPosition) errors.onboardingPosition = 'La position d\'onboarding est obligatoire';
        if (!payload.onboardingStartDate) errors.onboardingStartDate = 'Date de début d\'onboarding est obligatoire';
        if (!payload.onboardingEmployee) errors.onboardingEmployee = 'L\'employé d\'onboarding est obligatoire';
    }

    if (payload.type == 'offboarding') {
        if (!payload.offboardingEmployee) errors.offboardingEmployee = 'L\'employé d\'offboarding est obligatoire';
        if (!payload.offboardingEndDate) errors.offboardingEndDate = 'Date de fin d\'offboarding est obligatoire';
    }
    */

    return { payload, errors };
}