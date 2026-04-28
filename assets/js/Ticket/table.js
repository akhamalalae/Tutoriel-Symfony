import { DOM } from "./dom";
import { fetchTickets } from "./api";
import Loading from '../Components/displayLoading';

let currentPage = 1;

/**
 * Charge les tickets avec filtres + pagination
 */
export function loadTickets(page = 1) {
    currentPage = page;

    const params = new URLSearchParams({
        title: document.getElementById('searchTitle')?.value,
        description: document.getElementById('searchDescription')?.value,
        status: document.getElementById('searchStatus')?.value,
        page
    });

    new Loading().display(DOM.table);

    fetchTickets(params)
        .then(html => DOM.table.innerHTML = html)
        .catch(() => console.error('Erreur chargement'));
}

export function getCurrentPage() {
    return currentPage;
}

/**
 * Reset filtres
 */
export function resetFilters() {
    document.getElementById('searchTitle').value = '';
    document.getElementById('searchDescription').value = '';
    document.getElementById('searchStatus').value = '';

    loadTickets(currentPage);
}