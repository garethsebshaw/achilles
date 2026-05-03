<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
        <div class="space-y-4">
            <!-- Temperature Chart -->
            <div class="bg-white dark:bg-gray-900 rounded p-4">
                <h3 class="text-xl font-semibold mb-4">Temperature</h3>
                <div ref="temperatureChart" style="height: 300px"></div>
            </div>

            <!-- Precipitation Chart -->
            <div class="bg-white dark:bg-gray-900 rounded p-4">
                <h3 class="text-xl font-semibold mb-4">Precipitation</h3>
                <div ref="precipitationChart" style="height: 300px"></div>
            </div>

            <!-- Wind Chart -->
            <div class="bg-white dark:bg-gray-900 rounded p-4">
                <h3 class="text-xl font-semibold mb-4">Wind</h3>
                <div ref="windChart" style="height: 300px"></div>
            </div>

            <!-- Humidity Chart -->
            <div class="bg-white dark:bg-gray-900 rounded p-4">
                <h3 class="text-xl font-semibold mb-4">Humidity & Clouds</h3>
                <div ref="humidityChart" style="height: 300px"></div>
            </div>
        </div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts'

export default {
    name: 'WeatherDashboard',

    props: ['locationId'],

    data() {
        return {
            weatherData: [],
            charts: {},
            loading: true,
            error: null
        }
    },

    async mounted() {
        await this.fetchData()
        this.initCharts()

        // Refresh data every 5 minutes
        setInterval(async () => {
            await this.fetchData()
            this.updateCharts()
        }, 5 * 60 * 1000)
    },

    methods: {
        async fetchData() {
            try {
                const response = await Nova.request().get(`/nova-api/weather-data/${this.locationId}`)
                this.weatherData = response.data
                this.loading = false
            } catch (error) {
                this.error = 'Failed to load weather data'
                this.loading = false
                console.error('Error fetching weather data:', error)
            }
        },

        initCharts() {
            if (!this.weatherData.length) return

            const options = {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    type: 'datetime'
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy HH:mm'
                    }
                }
            }

            // Temperature Chart
            this.charts.temperature = new ApexCharts(this.$refs.temperatureChart, {
                ...options,
                series: [{
                    name: 'Temperature',
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.temperature_2m])
                }, {
                    name: 'Feels Like',
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.apparent_temperature])
                }],
                yaxis: {
                    title: {
                        text: 'Temperature (°C)'
                    }
                }
            })
            this.charts.temperature.render()

            // Precipitation Chart
            this.charts.precipitation = new ApexCharts(this.$refs.precipitationChart, {
                ...options,
                series: [{
                    name: 'Precipitation',
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.precipitation])
                }, {
                    name: 'Snowfall',
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.snowfall])
                }],
                yaxis: {
                    title: {
                        text: 'Amount (mm)'
                    }
                }
            })
            this.charts.precipitation.render()

            // Wind Chart
            this.charts.wind = new ApexCharts(this.$refs.windChart, {
                ...options,
                series: [{
                    name: 'Wind Speed',
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.wind_speed_10m])
                }, {
                    name: 'Wind Gusts',
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.wind_gusts_10m])
                }],
                yaxis: {
                    title: {
                        text: 'Speed (m/s)'
                    }
                }
            })
            this.charts.wind.render()

            // Humidity Chart
            this.charts.humidity = new ApexCharts(this.$refs.humidityChart, {
                ...options,
                series: [{
                    name: 'Humidity',
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.relative_humidity_2m])
                }, {
                    name: 'Cloud Cover',
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.cloud_cover])
                }],
                yaxis: {
                    title: {
                        text: 'Percentage (%)'
                    }
                }
            })
            this.charts.humidity.render()
        },

        updateCharts() {
            if (!this.weatherData.length) return

            Object.values(this.charts).forEach(chart => {
                chart.updateSeries([{
                    data: this.weatherData.map(d => [new Date(d.forecast_time).getTime(), d.temperature_2m])
                }])
            })
        }
    },

    beforeUnmount() {
        // Clean up charts
        Object.values(this.charts).forEach(chart => {
            chart.destroy()
        })
    }
}
</script>
