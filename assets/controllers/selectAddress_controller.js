import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const form = this.element.closest('.form-checkout-address');

        if (form) {
            this.element.querySelectorAll('.dropdown-item').forEach(btn => {
                btn.addEventListener('click', () => {
                    form.querySelector('#address_address').value = btn.dataset.address;
                    form.querySelector('#address_zipCode').value = btn.dataset.zipCode;
                    form.querySelector('#address_city').value = btn.dataset.city;
                });
            });
        }
    }
}
