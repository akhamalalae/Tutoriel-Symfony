
/*
    import 'bootstrap';
    import 'bootstrap/dist/css/bootstrap.min.css';
    import './theme/css/app.css';
    import './theme/js/app.js';
*/
import './styles/app.css';
import 'select2';
import 'select2/dist/css/select2.css';
import 'bootstrap-datepicker';
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.js';
import $ from 'jquery';
import 'notyf/notyf.min.css';

global.$ = global.jQuery = $;

document.addEventListener('DOMContentLoaded', () => {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });
});

$(document).ready(function() {
    $('.select2').select2();
});

