import StockUtils from "./stockUtils";

const utils = new StockUtils();

/**
 * GET liste stock (HTML)
 */
export function fetchStock(params = '') {
    return fetch(utils.list(params))
        .then(res => res.text());
}

/**
 * GET form (HTML)
 */
export function getStockForm(id = null) {
    return fetch(utils.form(id))
        .then(res => res.text());
}

/**
 * CREATE / UPDATE stock
 */
export function saveStock(formElement) {
    return fetch(formElement.action, {
        method: 'POST',
        body: new FormData(formElement),
    }).then(res => res.json());
}

/**
 * DELETE stock
 */
export function deleteStock(id) {
    return fetch(utils.delete(id), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(res => res.json());
}