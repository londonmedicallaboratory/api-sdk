import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class calender_controller extends Controller {
    connect() {
        this.element.textContent = 'vucicu pederu2';
    }
}
