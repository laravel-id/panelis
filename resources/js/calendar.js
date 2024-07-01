import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

const csrf = document.querySelector('meta[name="csrf-token"]');
const token = csrf ? csrf.getAttribute('content') : null;

let calendarEl = document.getElementById('calendar');
let calendar = new Calendar(calendarEl, {
    plugins: [ dayGridPlugin, timeGridPlugin, listPlugin ],
    initialView: 'dayGridMonth',
    headerToolbar: {
        start: 'prev today next',
        center: 'title',
        right: 'dayGridMonth,listWeek'
    },
    buttonText: {
        next: 'Next',
        prev: 'Prev',
        month: 'Month',
        list: 'List',
        today: 'Today',
    },
    events: {
        lazyFetching: true,
        url: '/schedules.json',
        method: 'POST',
        extraParams: {
            '_token': token,
        },
    },
    eventClick: info => {

    },
});


calendar.setOption('locale', document.documentElement.lang)

calendar.render();