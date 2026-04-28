export default class DateFormatter {
    /**
     * Transforme une date en string compatible <input type="datetime-local">
     * @param {string|Date} dateInput - date JS ou string ISO
     * @returns {string} - format 'YYYY-MM-DDTHH:MM:SS'
     */
    static toDatetimeLocal(dateInput) {
        if (!dateInput) return '';

        const date = (dateInput instanceof Date) ? dateInput : new Date(dateInput);
        if (isNaN(date)) return '';

        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        const hh = String(date.getHours()).padStart(2, '0');
        const mi = String(date.getMinutes()).padStart(2, '0');
        const ss = String(date.getSeconds()).padStart(2, '0');

        return `${yyyy}-${mm}-${dd}T${hh}:${mi}:${ss}`;
    }

    /**
     * Transforme une date <input type="datetime-local"> en objet Date JS
     * @param {string} inputValue
     * @returns {Date|null}
     */
    static fromDatetimeLocal(inputValue) {
        if (!inputValue) return null;
        const date = new Date(inputValue);
        return isNaN(date) ? null : date;
    }
}