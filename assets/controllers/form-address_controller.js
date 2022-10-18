import {Controller} from '@hotwired/stimulus';
import './../external/pca.css'

require('../external/pca');

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        country: String,
        line1: String,
        line2: String,
        line3: String,
        city: String,
        postalCode: String,
        apiKey: String,
        suggestionsId: String,
        hidden: Boolean
    }

    // noinspection JSUnusedGlobalSymbols
    static debounces = ['change'];

    static targets = ['editManually'];

    connect() {
        const pca = window.pca;

        let fields = [
            {
                element: this.line1Value, field: "Line1", mode: pca.fieldMode.POPULATE | pca.fieldMode.SEARCH
            },
            {
                element: this.line2Value, field: "Line2", mode: pca.fieldMode.POPULATE
            },
            {
                element: this.line3Value, field: "Line3", mode: pca.fieldMode.POPULATE
            },
            {
                element: this.cityValue, field: "City", mode: pca.fieldMode.POPULATE
            },
            {
                element: this.postalCodeValue, field: "PostalCode", mode: pca.fieldMode.POPULATE
            },
            {
                element: this.countryValue, field: "CountryName", mode: pca.fieldMode.COUNTRY
            }
        ];

        let controls = new pca.Address(fields, {
            key: this.apiKeyValue,
        });

        if (this.hiddenValue) {
            controls.listen("populate", (address, variations) => {
                this.showRest();
            });
        }
    }

    showRest() {
        Array.from(this.element.querySelectorAll('.hidden')).forEach(el => {
            el.classList.remove('hidden');
        })

        if (this.hasditManuallyTarget) {
            this.editManuallyTarget.classList.add('d-none');
        }
    }
}
