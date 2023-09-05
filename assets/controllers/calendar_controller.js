// noinspection JSValidateTypes

import {Controller} from '@hotwired/stimulus';
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'
import '../styles.scss'
import {sprintf} from "printj/printj.mjs";

/* stimulusFetch: 'lazy' */
// noinspection JSUnresolvedVariable,JSUnusedGlobalSymbols
export default class calender_controller extends Controller {
    static values = {
        calendarUrl: String,
        dailySlotsUrl: String,
        formElementId: String,
        autoOpen: Boolean,
        closeOnSelect: Boolean,
    }
    static targets = [
        'dialog',
        'slots',
        'calendar',
        'altTime',
        'prettyTime',
    ]

    fInstance = null;
    selectedDay = 0;
    selectedMonth = 0;
    selectedYear = 0;

    connect() {
        let autoOpenValue = this.autoOpenValue;
        if (autoOpenValue) {
            this.selectDate();
        }
    }

    disconnect = () => {
        this.fInstance?.destroy();
    }

    close() {
        this.fInstance?.destroy();
        this.dialogTarget.classList.add('hidden');
    }

    // noinspection JSUnusedGlobalSymbols
    selectDate = () => {
        this.dialogTarget.classList.remove('hidden');
        this.fInstance?.destroy();
        this._createFlatpickr();
    }

    // noinspection JSUnusedGlobalSymbols
    selectSlot({params: {time, available, humanReadableFormat}}) {
        if (!available) {
            return;
        }
        let instance = this.fInstance;
        let selectedMonth = instance.currentMonth + 1;
        let selectedYear = instance.currentYear;

        let formElementId = this.formElementIdValue;

        let element = document.getElementById(formElementId);
        element.value = sprintf('%04d-%02d-%02dT%02s:00', selectedYear, selectedMonth, this.selectedDay, time);

        this.prettyTimeTarget.innerHTML = humanReadableFormat;
        if (this.closeOnSelectValue) {
            this.close();
        }

        let event = new Event('change');
        element.dispatchEvent(event);
    }

    _createFlatpickr = () => {
        let altTime = this.altTimeTarget;
        let altTimeAsString = altTime.value;

        let date = altTimeAsString ? new Date(altTimeAsString) : new Date();

        this.selectedYear = date.getFullYear();
        this.selectedMonth = date.getMonth() + 1;

        // noinspection JSUnusedGlobalSymbols
        let options = {
            locale: {
                firstDayOfWeek: 1 // start week on Monday
            },
            appendTo: this.calendarTarget,
            minDate: new Date(),
            inline: true,
            enableTime: false,
            noCalendar: false,
            dateFormat: 'Y-m-d',
            onMonthChange: (selectedDates, dateStr, instance) => this._onMonthOrYearChange(instance),
            onYearChange: (selectedDates, dateStr, instance) => this._onMonthOrYearChange(instance),
            onChange: (selectedDates, dateStr) => this._onDateSelect(dateStr),
        }

        this.fInstance = flatpickr(altTime, options);
        this._updateMonthlyAvailability().catch();
        if (altTimeAsString) {
            this._onDateSelect(sprintf('%04d-%02d-%02d', date.getFullYear(), date.getMonth() + 1, date.getDate()));
        }
    }

    /**
     * Update the list of enabled dates
     */
    _onMonthOrYearChange = () => {
        this.slotsTarget.innerHTML = '';

        let instance = this.fInstance;
        this.selectedMonth = instance.currentMonth + 1;
        this.selectedYear = instance.currentYear;

        this._updateMonthlyAvailability().catch();
    }

    _updateMonthlyAvailability() {
        let year = this.selectedYear;
        let month = this.selectedMonth;
        let url = this.calendarUrlValue;
        let queryParams = `year=${year}&month=${month}`;
        let prefix = !url.includes('?') ? '?' : '&';
        url = url + prefix + queryParams;

        return fetch(url)
        .then((r) => r.json())
        .then((json) => this.fInstance.set('enable', [
            (date) => {
                let formattedDate = sprintf('%04d-%02d-%02d', date.getFullYear(), date.getMonth() + 1, date.getDate());

                return formattedDate in json && json[formattedDate] === true;
            }
        ]))
            ;
    }

    /**
     * Date has been selected; fetch available timeslots from backend
     *
     * `dateStr` param is in Y-m-d format
     */
    _onDateSelect = (dateStr) => {
        let date = new Date(dateStr);
        let slotsUrl = this.dailySlotsUrlValue;
        let year = date.getFullYear();
        let month = date.getMonth() + 1;
        let day = date.getDate();
        this.selectedDay = day;

        let queryParams = `year=${year}&month=${month}&day=${day}`;
        let prefix = !slotsUrl.includes('?') ? '?' : '&';
        slotsUrl = slotsUrl + prefix + queryParams;

        fetch(slotsUrl)
        .then((r) => r.json())
        .then((json) => {
            let slots = this.slotsTarget;
            slots.innerHTML = '';

            json.forEach(function (struct) {
                let isAvailable = struct.available;
                let isInPast = struct.is_past;
                let humanReadableFormat = struct['human_readable_format'];
                let preview = struct['preview'];

                slots.innerHTML += `
                        <span class="lml-calendar-widget-slot ${isAvailable ? '' : 'lml-calendar-widget-slot-not-available'} ${isInPast ? 'lml-calendar-widget-slot-past-time' : ''}" 
                            ${isAvailable ? 'data-action="click->londonmedicallaboratory--api-sdk--calendar#selectSlot"' : ''}    
                            data-londonmedicallaboratory--api-sdk--calendar-time-param="${preview}" 
                            data-londonmedicallaboratory--api-sdk--calendar-human_readable_format-param="${humanReadableFormat}" 
                            data-londonmedicallaboratory--api-sdk--calendar-available-param="${isAvailable ? 'true' : 'false'}">${preview}
                        </span>
                    `;
            });
        })
    }
}
