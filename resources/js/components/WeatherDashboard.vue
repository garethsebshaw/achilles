<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
        <Tabs :tabs="tabs" v-model:selectedTab="selectedTab">
            <template #default="{ selectedTab }">
                <Panel name="temperature" v-show="selectedTab === 'temperature'">
                    <Card class="px-6 py-4">
                        <div ref="temperatureChart" style="height: 400px"></div>
                    </Card>
                </Panel>

                <Panel name="precipitation" v-show="selectedTab === 'precipitation'">
                    <Card class="px-6 py-4">
                        <div ref="precipitationChart" style="height: 400px"></div>
                    </Card>
                </Panel>

                <Panel name="wind" v-show="selectedTab === 'wind'">
                    <Card class="px-6 py-4">
                        <div ref="windChart" style="height: 400px"></div>
                    </Card>
                </Panel>

                <Panel name="humidity" v-show="selectedTab === 'humidity'">
                    <Card class="px-6 py-4">
                        <div ref="humidityChart" style="height: 400px"></div>
                    </Card>
                </Panel>
            </template>
        </Tabs>
    </div>
</template>

<script>
// import { ref, onMounted, watch } from 'vue'
// import { Card, Panel, Tabs } from '@/components/ui'
// import axios from 'axios'
import ApexCharts from 'apexcharts'

export default {
    name: 'WeatherDashboard',

    components: {
        Card,
        Panel,
        Tabs,
    },

    props: {
        locationId: {
            type: [Number, String],
            required: true
        }
    },

    setup(props) {
        const weatherData = ref([])
        const selectedTab = ref('temperature')
        const temperatureChart = ref(null)
        const precipitationChart = ref(null)
        const windChart = ref(null)
        const humidityChart = ref(null)

        const tabs = [
            { name: 'temperature', label: 'Temperature' },
            { name: 'precipitation', label: 'Precipitation' },
            { name: 'wind', label: 'Wind' },
            { name: 'humidity', label: 'Humidity & Clouds' }
        ]

        const fetchWeatherData = async () => {
            try {
                const response = await axios.get(`/api/nova-api/weather-data/location/${props.locationId}`)
                weatherData.value = response.data
                initCharts()
            } catch (error) {
                console.error('Error fetching weather data:', error)
            }
        }

        const initCharts = () => {
            // Temperature Chart
            new ApexCharts(temperatureChart.value, {
                chart: {
                    type: 'line',
                    height: 350,
                },
                series: [
                    {
                        name: 'Temperature',
                        data: weatherData.value.map(d => ({ x: d.forecast_time, y: d.temperature_2m }))
                    },
                    {
                        name: 'Feels Like',
                        data: weatherData.value.map(d => ({ x: d.forecast_time, y: d.apparent_temperature }))
                    }
                ],
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    title: {
                        text: 'Temperature (°C)'
                    }
                }
            }).render()

            // Precipitation Chart
            new ApexCharts(precipitationChart.value, {
                chart: {
                    type: 'line',
                    height: 350,
                },
                series: [
                    {
                        name: 'Precipitation',
                        data: weatherData.value.map(d => ({ x: d.forecast_time, y: d.precipitation }))
                    },
                    {
                        name: 'Snowfall',
                        data: weatherData.value.map(d => ({ x: d.forecast_time, y: d.snowfall }))
                    }
                ],
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    title: {
                        text: 'Amount (mm)'
                    }
                }
            }).render()

            // Wind Chart
            new ApexCharts(windChart.value, {
                chart: {
                    type: 'line',
                    height: 350,
                },
                series: [
                    {
                        name: 'Wind Speed',
                        data: weatherData.value.map(d => ({ x: d.forecast_time, y: d.wind_speed_10m }))
                    },
                    {
                        name: 'Wind Gusts',
                        data: weatherData.value.map(d => ({ x: d.forecast_time, y: d.wind_gusts_10m }))
                    }
                ],
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    title: {
                        text: 'Speed (m/s)'
                    }
                }
            }).render()

            // Humidity Chart
            new ApexCharts(humidityChart.value, {
                chart: {
                    type: 'line',
                    height: 350,
                },
                series: [
                    {
                        name: 'Humidity',
                        data: weatherData.value.map(d => ({ x: d.forecast_time, y: d.relative_humidity_2m }))
                    },
                    {
                        name: 'Cloud Cover',
                        data: weatherData.value.map(d => ({ x: d.forecast_time, y: d.cloud_cover }))
                    }
                ],
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    title: {
                        text: 'Percentage (%)'
                    }
                }
            }).render()
        }

        onMounted(() => {
            fetchWeatherData()
        })

        return {
            selectedTab,
            tabs,
            temperatureChart,
            precipitationChart,
            windChart,
            humidityChart
        }
    }
}
</script>
