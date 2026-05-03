import './bootstrap';
import { createApp } from 'vue'
import SessionAttendanceMetric from './components/Metrics/SessionAttendanceMetric.vue'

Nova.booting((app) => {
    app.component('session-attendance-metric', SessionAttendanceMetric)
})
