import WeatherDashboard from './components/WeatherDashboard.vue'

Nova.booting((app, store) => {
    app.component('weather-dashboard', WeatherDashboard)
})
