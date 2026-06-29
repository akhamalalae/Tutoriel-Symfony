import {
    fetchStock,
    getStockForm,
    saveStock,
    deleteStock
} from "./api";
import { initSelect2Ajax } from '../Components/initSelect2Ajax.js';
import Notification from '../Components/Notyf/notification';
import Loading from '../Components/displayLoading';
import Menu from "../Menu/menuManagement";

document.addEventListener('DOMContentLoaded', () => {

    new Menu().selectItem(document.querySelector('#sidebar-nav-stock'));

    const notyf = new Notification();
    const loader = new Loading();

    const formContainer = document.getElementById('form-container');
    const blockFormContainer = document.getElementById('block-form-container');
    const listContainer = document.getElementById('equipment-list');
    const searchInput = document.getElementById('stock-search');
    const blockEquipmentList = document.getElementById('block-equipment-list');
    const hiddenStockBtn = document.getElementById('hidden-stock-btn');

    let currentSearch = '';

    // =========================
    // HELPER LOADING WRAPPER
    // =========================
    async function withLoading(container, callback) {
        loader.display(container);
        try {
            return await callback();
        } catch (err) {
            console.error(err);
            throw err;
        } finally {
            loader.hide?.();
        }
    }

    // =========================
    // LOAD FORM
    // =========================
    async function loadForm(id = null) {
        try {
            await withLoading(formContainer, async () => {
                const html = await getStockForm(id);
                formContainer.innerHTML = html;

                const firstInput = formContainer.querySelector('input, select, textarea');
                if (firstInput) firstInput.focus();

                const element = formContainer.querySelectorAll('.js-select2-ajax');

                initSelect2Ajax(element);
            });
        } catch (err) {
            alert('Erreur chargement formulaire');
        }
    }

    // =========================
    // SUBMIT FORM
    // =========================
    document.addEventListener('submit', async function (e) {

        if (e.target.id !== 'equipment-form') return;

        e.preventDefault();

        try {
            const data = await saveStock(e.target);

            if (data.success) {
                await reloadList();
                formContainer.innerHTML = '';
                toggleDisplay(blockEquipmentList, true);
                notyf.show('success', data.message);
            } else {
                alert(data.errors || 'Erreur');
            }

        } catch (err) {
            console.error(err);
            alert('Erreur serveur');
        }
    });

    // =========================
    // CLICK EVENTS
    // =========================
    document.addEventListener('click', async function (e) {

        // ADD
        if (e.target.id === 'btn-add') {
            toggleDisplay(formContainer, true);
            toggleDisplay(blockFormContainer, true);
            toggleDisplay(blockEquipmentList, false);
            loadForm();
        }

        // EDIT
        if (e.target.classList.contains('btn-edit')) {
            toggleDisplay(formContainer, true);
            toggleDisplay(blockFormContainer, true);
            toggleDisplay(blockEquipmentList, false);
            loadForm(e.target.dataset.id);
        }

        // Hidden
        const btnHidden = e.target.closest('#hidden-stock-btn');
        if (btnHidden) {
            toggleDisplay(formContainer, false);
            toggleDisplay(blockFormContainer, false);
            toggleDisplay(blockEquipmentList, true);
        }
        
        // DELETE
        if (e.target.classList.contains('btn-delete')) {

            if (!confirm('Supprimer ?')) return;

            try {
                const data = await deleteStock(e.target.dataset.id);

                if (data.success) {
                    await reloadList();
                    notyf.show('delete', data.message);
                }

            } catch (err) {
                console.error(err);
                alert('Erreur suppression');
            }
        }

        // PAGINATION
        const link = e.target.closest('.pagination a');

        if (link) {
            e.preventDefault();
            loadPage(link.href);
        }
    });

    // =========================
    // SEARCH LIVE
    // =========================
    let debounceTimer;

    searchInput.addEventListener('keyup', function () {

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            currentSearch = searchInput.value;
            reloadList();
        }, 400);
    });

    // =========================
    // LOAD PAGE
    // =========================
    async function loadPage(url) {
        try {
            await withLoading(listContainer, async () => {
                const res = await fetch(url);
                const html = await res.text();
                updateListDom(html);
            });
        } catch (err) {
            alert('Erreur pagination');
        }
    }

    // =========================
    // RELOAD LIST
    // =========================
    async function reloadList(page = 1) {

        const params = new URLSearchParams({
            search: currentSearch,
            page: page
        });

        try {
            await withLoading(listContainer, async () => {
                const html = await fetchStock(params.toString());
                updateListDom(html);
            });
        } catch (err) {
            alert('Erreur reload');
        }
    }

    // =========================
    // UPDATE DOM
    // =========================
    function updateListDom(html) {

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newList = doc.getElementById('equipment-list');

        if (newList) {
            listContainer.innerHTML = newList.innerHTML;
        }
    }

});

function toggleDisplay(element, show = true) {
    if (element) {
        element.style.display = show ? 'block' : 'none';
    }
}