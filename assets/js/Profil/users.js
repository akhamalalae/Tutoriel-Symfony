import Menu from "../Menu/menuManagement";
import Notification from '../Components/Notyf/notification';
import Loading from '../Components/displayLoading';

import {
    searchAddress,
    fetchUsers,
    getUserForm,
    saveUser,
    deleteUser
} from "./api";

new Menu().selectItem(document.querySelector('#sidebar-nav-users'));

document.addEventListener('DOMContentLoaded', () => {

    const listContainer = document.getElementById('user-list');
    const formContainer = document.getElementById('form-container');
    const searchInput = document.getElementById('user-search');
    const blockList = document.getElementById('block-user-list');

    if (!listContainer || !formContainer) return;

    const notyf = new Notification();
    const loader = new Loading();

    let currentSearch = '';
    let currentPage = 1;
    let debounce = null;

    // =========================
    // SAFE FORM DETECTION
    // =========================
    function getActiveForm() {
        return document.querySelector('#user-form')
            || document.querySelector('#registration-form');
    }

    // =========================
    // LOAD FORM
    // =========================
    async function loadForm(id = null) {
        try {
            loader.display(formContainer);
            const html = await getUserForm(id);
            formContainer.innerHTML = html;
        } catch (err) {
            console.error(err);
            notyf.show('error', 'Form load error');
        }
    }

    // =========================
    // LIST
    // =========================
    async function reloadList(page = 1) {

        currentPage = page;

        const params = new URLSearchParams({
            search: currentSearch,
            page: currentPage
        });

        try {
            loader.display(listContainer);
            const html = await fetchUsers(params.toString());
            updateDom(html);
        } catch (err) {
            console.error(err);
            notyf.show('error', 'List load error');
        }
    }

    function updateDom(html) {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newList = doc.querySelector('#user-list');

        if (newList) {
            listContainer.innerHTML = newList.innerHTML;
        }
    }

    function toggleDisplay(el, show = true) {
        if (el) el.style.display = show ? 'block' : 'none';
    }

    // =========================
    // SEARCH
    // =========================
    if (searchInput) {
        searchInput.addEventListener('keyup', () => {

            clearTimeout(debounce);

            debounce = setTimeout(() => {
                currentSearch = searchInput.value;
                reloadList(1);
            }, 350);
        });
    }

    // =========================
    // AUTOCOMPLETE (FULL SAFE MULTI FORM)
    // =========================
    document.addEventListener('input', async (e) => {
        console.log('input');
        const input = e.target.closest('.address-autocomplete');
        if (!input) return;

        const form = input.closest('form');
        if (!form) return;

        const query = input.value;
        if (query.length <= 3) return;

        const dropdown = form.querySelector('#autocomplete-results');
        if (!dropdown) return;

        try {
            const results = await searchAddress(query);

            dropdown.innerHTML = '';

            results.forEach(result => {

                const option = document.createElement('a');
                option.className = "list-group-item list-group-item-action list-group-item-primary";
                option.textContent = result.display_name;

                option.addEventListener('click', () => {

                    input.value = result.display_name;

                    const cityInput = form.querySelector('[name$="[city]"]');
                    const postalInput = form.querySelector('[name$="[postal_code]"]');
                    const countryInput = form.querySelector('[name$="[country]"]');

                    if (cityInput) cityInput.value = result.address?.city || result.address?.town || '';
                    if (postalInput) postalInput.value = result.address?.postcode || '';
                    if (countryInput) countryInput.value = result.address?.country || '';

                    dropdown.innerHTML = '';
                });

                dropdown.appendChild(option);
            });

        } catch (err) {
            console.error('Autocomplete error', err);
        }
    });

    // =========================
    // SUBMIT FORM (SAFE MULTI FORM)
    // =========================
    document.addEventListener('submit', async (e) => {

        const form = e.target;

        if (!form || !form.matches('#user-form')) return;

        e.preventDefault();

        const id = form.dataset.id || null;

        try {
            const data = await saveUser(form, id);

            if (data.success) {
                await reloadList(currentPage);
                formContainer.innerHTML = '';
                toggleDisplay(blockList, true);
                notyf.show('success', data.message);
            } else {
                notyf.show('error', data.message || 'Error');
            }

        } catch (err) {
            console.error(err);
            notyf.show('error', 'Server error');
        }
    });

    // =========================
    // CLICK EVENTS
    // =========================
    document.addEventListener('click', async (e) => {

        const target = e.target;

        if (e.target.id === 'hidden-user-btn') {
            console.log("hidden");
            toggleDisplay(formContainer, false);
            toggleDisplay(blockList, true);
            await reloadList(currentPage);
        }

        if (target?.id === 'btn-add-user') {
            toggleDisplay(formContainer, true);
            toggleDisplay(blockList, false);
            await loadForm();
        }

        if (target?.classList?.contains('btn-edit')) {
            toggleDisplay(formContainer, true);
            toggleDisplay(blockList, false);
            await loadForm(target.dataset.id);
        }

        if (target?.classList?.contains('btn-delete')) {

            if (!confirm('Delete user ?')) return;

            try {
                const data = await deleteUser(target.dataset.id);

                if (data.success) {
                    await reloadList(currentPage);
                    notyf.show('delete', data.message);
                }

            } catch (err) {
                console.error(err);
                notyf.show('error', 'Delete error');
            }
        }

        const link = target?.closest?.('.pagination a');

        if (link) {
            e.preventDefault();

            const url = new URL(link.href);
            const page = parseInt(url.searchParams.get('page') || 1, 10);

            await reloadList(page);
        }
    });

    // =========================
    // INIT
    // =========================
    reloadList();

});