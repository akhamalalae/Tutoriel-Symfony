import { DOM } from "./dom";

/**
 * Affiche les erreurs sous les champs + erreur globale
 */
export function showErrors(errors) {

    // reset erreurs champs
    const type = DOM.type?.value;

    const fields = [
        'title',
        'description',
        'status',
        'type',
        'priority',
        'assignedTo',
        'company',
        'ticketEquipment',
        'ticketSoftware',
        'targetUser'
    ];

    // Ajouter les champs onboarding uniquement si nécessaire
    if (type === 'onboarding') {
        fields.push('onboardingPosition', 'onboardingStartDate', 'onboardingEmployee');
    }

    if (type === 'offboarding') {
        fields.push('offboardingRevokeAccess', 'offboardingReturnEquipment', 'offboardingEmployee', 'offboardingEndDate');
    }

    fields.forEach(f => {
        const el = document.getElementById(`error_${f}`);
        if (el) el.textContent = '';
    });

    // afficher erreurs
    Object.keys(errors).forEach(f => {
        const el = document.getElementById(`error_${f}`);
        if (el) el.textContent = errors[f];
    });

    // erreur globale (identique à ton code)
    if (errors.general) {
        let gen = document.getElementById('error_general');
        if (!gen) {
            gen = document.createElement('div');
            gen.id = 'error_general';
            gen.className = 'alert alert-danger mb-2';
            DOM.cardeFormTicket.prepend(gen);
        }
        gen.textContent = errors.general;
    } else {
        document.getElementById('error_general')?.remove();
    }
}

/**
 * Toggle onboarding
 */
export function toggleOnboardingFields() {
    const onboardingDiv = DOM.onboardingFields;

    if (!onboardingDiv) return;

    const type = DOM.type?.value;

    onboardingDiv.style.display = (type === 'onboarding') ? 'block' : 'none';
}

/**
 * Toggle offboarding
 */
export function toggleOffboardingFields() {
    const offboardingDiv = DOM.offboardingFields;

    if (!offboardingDiv) return;

    const type = DOM.type?.value;

    offboardingDiv.style.display = (type === 'offboarding') ? 'block' : 'none';
}

export function toggleButtonLoading(button, isLoading) {
    if (!button) return;

    const spinner = button.querySelector('.spinner-border');

    if (isLoading) {
        button.disabled = true;
        spinner?.classList.remove('d-none');
    } else {
        button.disabled = false;
        spinner?.classList.add('d-none');
    }
}