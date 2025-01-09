import Menu from "../Menu/menuManagement";

import Translation from "../Translation/translation";

import ProfilUtils from "./profilUtils";

new Menu().selectItem(document.querySelector('#sidebar-nav-profil'));

let locale = new Translation().locale();

//console.log(new ProfilUtils().url('query'));

document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('.address-autocomplete');
    
    if (input) {
        input.addEventListener('input', async () => {
            const query = input.value;
            
            if (query.length > 3) {
                const response = await fetch(`/${locale}/user/address/search?q=${encodeURIComponent(query)}`);
                const results = await response.json();

                // Affichez les résultats
                const dropdown = document.querySelector('#autocomplete-results');

                dropdown.innerHTML = '';

                results.forEach(result => {
                    const option = document.createElement('a');

                    option.classList.add("list-group-item");

                    option.classList.add("list-group-item-action");

                    option.classList.add("list-group-item-primary");

                    option.textContent = result.display_name;

                    option.addEventListener('click', () => {
                        console.log(result.address);
                        input.value = result.display_name;

                        let city = '';

                        if (result.address.town) {
                            city = result.address.town;
                        } else if (result.address.city) {
                            city = result.address.city;
                        }

                        document.querySelector('[name="registration_form[city]"]').value = city || '';
                        document.querySelector('[name="registration_form[postal_code]"]').value = result.address.postcode || '';
                        document.querySelector('[name="registration_form[country]"]').value = result.address.country || '';
                    });

                    dropdown.appendChild(option);
                });
            }
        });
    }
});