// jQuery
import $ from 'jquery';
global.$ = $;
global.jQuery = $;

// Bootstrap
//import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

// Plugins
import 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';

import 'bootstrap-datepicker';
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.css';

import 'datatables.net';
import 'datatables.net-dt';

import 'notyf/notyf.min.css';

// FontAwesome
import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.js';

// CSS THEME
import './styles/theme.css';
import './styles/app.css';

// JS THEME
import './js/Theme/theme.js';

// ✅ Init datepicker
document.addEventListener('DOMContentLoaded', () => {
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });
});