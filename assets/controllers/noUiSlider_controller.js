import { Controller } from '@hotwired/stimulus';
import noUiSlider from 'nouislider'
import 'nouislider/dist/nouislider.css'
import '../styles/_noUiSlider.scss';

export default class extends Controller {
    connect() {
        const slider = this.element.querySelector('#price-slider');
        const min = parseInt(slider.dataset.min);
        const max = parseInt(slider.dataset.max);

        const minInput = this.element.querySelector('#min');
        const maxInput = this.element.querySelector('#max');

        const range = noUiSlider.create(slider, {
            start: [min, max],
            connect: true,
            step: 10,
            range: {
                min: min,
                max: max
            }
        });

        range.on('slide', (values, handle) => {
            if (handle === 0) {
                minInput.value = parseInt(values[0]);
            } else {
                maxInput.value = parseInt(values[1]);
            }
        });

        range.on('end', (values, handle) => {
            if (handle === 0) {
                minInput.dispatchEvent(new Event('keyup'));
            } else {
                maxInput.dispatchEvent(new Event('keyup'));
            }
        });
    }
}
