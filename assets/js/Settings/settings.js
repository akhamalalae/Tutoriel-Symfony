import Menu from "../Menu/menuManagement";

new Menu().selectItem(document.querySelector('#sidebar-nav-settings'));

let myCollapse = document.getElementById('collapseSettings');

new bootstrap.Collapse(myCollapse, {
  toggle: true
})
