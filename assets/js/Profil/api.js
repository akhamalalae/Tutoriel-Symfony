import ProfilUtils from "./profilUtils";

const utils = new ProfilUtils();

/**
 * ADDRESS AUTOCOMPLETE
 */
export function searchAddress(query) {
    return fetch(utils.addressSearch(query))
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        });
}

/**
 * LIST (HTML)
 */
export function fetchUsers(params = '') {
    return fetch(utils.list(params))
        .then(res => res.text());
}

/**
 * FORM (HTML)
 */
export function getUserForm(id = null) {
    return fetch(utils.form(id))
        .then(res => res.text());
}

/**
 * SAVE (create/update)
 */
export function saveUser(formElement, id = null) {

    const url = utils.save(id);

    return fetch(url, {
        method: 'POST',
        body: new FormData(formElement),
    }).then(res => res.json());
}

/**
 * DELETE
 */
export function deleteUser(id) {
    return fetch(utils.delete(id), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(res => res.json());
}