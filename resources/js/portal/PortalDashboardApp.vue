<script setup>
import ApexCharts from 'apexcharts'
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const config = window.__PORTAL_DASHBOARD_CONFIG__ ?? {}
const storageKey = 'achilles.portal.preferences'

const loading = ref(true)
const error = ref(null)
const payload = ref(null)
const charts = ref({})
const preferences = ref({
  textSize: 'default',
  contrast: 'default',
  motion: 'standard',
  density: 'comfortable',
  autoRefresh: Boolean(config.auto_refresh_default),
})

let refreshHandle = null

const summaryCards = computed(() => {
  if (!payload.value) {
    return []
  }

  return [
    { key: 'users', label: config.users, value: payload.value.summary.users, tone: 'primary' },
    { key: 'chapters', label: config.chapters, value: payload.value.summary.chapters, tone: 'info' },
    { key: 'sessions', label: config.sessions, value: payload.value.summary.sessions, tone: 'warning' },
    { key: 'signups', label: config.signups, value: payload.value.summary.signups, tone: 'success' },
  ]
})

const generatedAt = computed(() => {
  if (!payload.value?.generated_at) {
    return '—'
  }

  return new Date(payload.value.generated_at).toLocaleString()
})

const heroStats = computed(() => {
  if (!payload.value) {
    return []
  }

  return [
    { key: 'registrations_last_30_days', label: config.registrations_last_30_days, value: payload.value.summary.registrations_last_30_days },
    { key: 'signups_last_30_days', label: config.signups_last_30_days, value: payload.value.summary.signups_last_30_days },
    { key: 'average_signups_per_session', label: config.average_signups_per_session, value: payload.value.summary.average_signups_per_session },
  ]
})

const chapterActivity = computed(() => payload.value?.chapter_activity ?? [])

const chartTables = computed(() => {
  if (!payload.value?.series) {
    return []
  }

  return [
    {
      key: 'user_registrations',
      title: config.user_registrations,
      labels: payload.value.series.user_registrations.labels.slice(-14),
      values: payload.value.series.user_registrations.values.slice(-14),
    },
    {
      key: 'workout_signups',
      title: config.workout_signups,
      labels: payload.value.series.workout_signups.labels.slice(-14),
      values: payload.value.series.workout_signups.values.slice(-14),
    },
  ]
})

const normalizedNoticeVisible = computed(() => Boolean(
  payload.value?.series?.user_registrations?.normalized || payload.value?.series?.workout_signups?.normalized,
))

const refreshStatus = computed(() => (
  preferences.value.autoRefresh ? config.auto_refresh_on : config.auto_refresh_off
))

const currentAnalyticsImage = computed(() => {
  const images = config.analytics_images ?? []

  if (!images.length) {
    return null
  }

  return images[(new Date().getDate() - 1) % images.length]
})

async function fetchDashboard() {
  try {
    error.value = null

    const response = await fetch(`${config.data_url}?days=${config.default_days ?? 365}`, {
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
      cache: 'no-store',
    })

    const contentType = response.headers.get('content-type') ?? ''

    if (!response.ok || !contentType.includes('application/json')) {
      throw new Error(`Unexpected dashboard response: ${response.status}`)
    }

    payload.value = await response.json()
    loading.value = false
    await renderCharts()
  } catch (err) {
    error.value = err
    loading.value = false
    preferences.value.autoRefresh = false
  }
}

async function renderCharts() {
  if (!payload.value?.series) {
    return
  }

  const sparklineUserSeries = payload.value.series.user_registrations.values.slice(-14)
  const sparklineSignupSeries = payload.value.series.workout_signups.values.slice(-14)

  const definitions = [
    {
      key: 'userRegistrations',
      selector: '#portal-user-registrations-chart',
      type: 'area',
      title: config.user_registrations,
      color: '#7367f0',
      series: payload.value.series.user_registrations,
      height: 340,
    },
    {
      key: 'workoutSignups',
      selector: '#portal-workout-signups-chart',
      type: 'area',
      title: config.workout_signups,
      color: '#28c76f',
      series: payload.value.series.workout_signups,
      height: 340,
    },
    {
      key: 'miniRegistrations',
      selector: '#portal-mini-registrations-chart',
      type: 'area',
      title: config.registrations_last_30_days,
      color: '#00cfe8',
      series: {
        labels: payload.value.series.user_registrations.labels.slice(-14),
        values: sparklineUserSeries,
      },
      height: 90,
      sparkline: true,
    },
    {
      key: 'miniSignups',
      selector: '#portal-mini-signups-chart',
      type: 'area',
      title: config.signups_last_30_days,
      color: '#ff9f43',
      series: {
        labels: payload.value.series.workout_signups.labels.slice(-14),
        values: sparklineSignupSeries,
      },
      height: 90,
      sparkline: true,
    },
  ]

  for (const definition of definitions) {
    const target = document.querySelector(definition.selector)

    if (!target) {
      continue
    }

    if (charts.value[definition.key]) {
      charts.value[definition.key].updateOptions(thisChartOptions(definition, preferences.value), false, true)
      charts.value[definition.key].updateSeries([
        { name: definition.title, data: definition.series.values },
      ], true)
      continue
    }

    const chart = new ApexCharts(target, {
      ...thisChartOptions(definition, preferences.value),
      series: [{ name: definition.title, data: definition.series.values }],
    })

    await chart.render()
    charts.value[definition.key] = chart
  }
}

function thisChartOptions(definition, currentPreferences) {
  const isSparkline = Boolean(definition.sparkline)

  return {
    chart: {
      type: definition.type,
      height: definition.height,
      toolbar: { show: false },
      sparkline: { enabled: isSparkline },
      animations: { enabled: currentPreferences.motion !== 'reduced' },
      fontFamily: 'Public Sans, Inter, system-ui, sans-serif',
      foreColor: currentPreferences.contrast === 'high' ? '#111827' : '#6e6b7b',
    },
    colors: [definition.color],
    dataLabels: { enabled: false },
    stroke: {
      curve: 'smooth',
      width: isSparkline ? 2.5 : 3,
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 0.55,
        opacityFrom: isSparkline ? 0.5 : 0.35,
        opacityTo: 0.05,
        stops: [0, 90, 100],
      },
    },
    grid: {
      borderColor: 'rgba(75, 70, 92, 0.08)',
      strokeDashArray: 6,
      padding: { left: 0, right: 0 },
    },
    markers: {
      size: isSparkline ? 0 : 4,
      strokeWidth: 0,
      hover: { size: 5 },
    },
    xaxis: {
      categories: definition.series.labels,
      labels: {
        show: !isSparkline,
        rotate: -35,
      },
      axisBorder: { show: false },
      axisTicks: { show: false },
    },
    yaxis: {
      show: !isSparkline,
      min: 0,
      forceNiceScale: true,
    },
    tooltip: {
      theme: currentPreferences.contrast === 'high' ? 'dark' : 'light',
    },
    legend: { show: false },
  }
}

function loadPreferences() {
  try {
    const saved = JSON.parse(window.localStorage.getItem(storageKey) ?? '{}')
    preferences.value = { ...preferences.value, ...saved }
  } catch {
    // Ignore malformed local preferences and continue with defaults.
  }
}

function applyPreferences() {
  const root = document.documentElement
  root.dataset.textSize = preferences.value.textSize
  root.dataset.contrast = preferences.value.contrast
  root.dataset.motion = preferences.value.motion
  root.dataset.density = preferences.value.density

  window.localStorage.setItem(storageKey, JSON.stringify(preferences.value))
}

function stopRefreshLoop() {
  if (refreshHandle) {
    window.clearInterval(refreshHandle)
    refreshHandle = null
  }
}

function startRefreshLoop() {
  stopRefreshLoop()

  if (!preferences.value.autoRefresh) {
    return
  }

  const interval = Number(config.refresh_seconds ?? 60) * 1000
  refreshHandle = window.setInterval(fetchDashboard, interval)
}

function toggleAutoRefresh() {
  preferences.value.autoRefresh = !preferences.value.autoRefresh
}

watch(preferences, async () => {
  applyPreferences()
  startRefreshLoop()
  await renderCharts()
}, { deep: true })

onMounted(async () => {
  loadPreferences()
  applyPreferences()
  await fetchDashboard()
  startRefreshLoop()
})

onBeforeUnmount(() => {
  stopRefreshLoop()
  Object.values(charts.value).forEach(chart => chart?.destroy?.())
})
</script>

<template>
  <div class="portal-shell">
    <a class="portal-skip-link" href="#portal-main">{{ config.skip_to_main }}</a>

    <main id="portal-main" class="portal-container">
      <header class="portal-header" role="banner">
        <div>
          <p class="portal-eyebrow">Achilles Portal</p>
          <h1>{{ config.title }}</h1>
          <p class="portal-subtitle">{{ config.subtitle }}</p>
        </div>

        <div class="portal-toolbar">
          <div class="portal-toolbar__status">
            <span class="portal-toolbar__label">{{ config.auto_refresh }}</span>
            <button
              type="button"
              class="portal-toggle"
              :aria-pressed="preferences.autoRefresh ? 'true' : 'false'"
              @click="toggleAutoRefresh"
            >
              <span>{{ refreshStatus }}</span>
            </button>
          </div>

          <button type="button" class="portal-button" @click="fetchDashboard">
            {{ config.refresh_now }}
          </button>
        </div>
      </header>

      <section class="portal-panel portal-panel--preferences" aria-labelledby="display-preferences-title">
        <div class="portal-panel__header">
          <div>
            <h2 id="display-preferences-title">{{ config.display_preferences }}</h2>
            <p>{{ config.summary_copy }}</p>
          </div>
          <div class="portal-meta" aria-live="polite">
            <span>{{ config.last_updated }}: {{ generatedAt }}</span>
            <span>{{ config.refreshing }}</span>
          </div>
        </div>

        <div class="portal-preferences">
          <label>
            <span>{{ config.text_size }}</span>
            <select v-model="preferences.textSize">
              <option value="default">{{ config.default }}</option>
              <option value="large">{{ config.large }}</option>
              <option value="extra-large">{{ config.extra_large }}</option>
            </select>
          </label>

          <label>
            <span>{{ config.contrast }}</span>
            <select v-model="preferences.contrast">
              <option value="default">{{ config.default }}</option>
              <option value="high">{{ config.high }}</option>
            </select>
          </label>

          <label>
            <span>{{ config.motion }}</span>
            <select v-model="preferences.motion">
              <option value="standard">{{ config.standard }}</option>
              <option value="reduced">{{ config.reduced }}</option>
            </select>
          </label>

          <label>
            <span>{{ config.layout_mode }}</span>
            <select v-model="preferences.density">
              <option value="comfortable">{{ config.comfortable }}</option>
              <option value="compact">{{ config.compact }}</option>
            </select>
          </label>
        </div>
      </section>

      <section v-if="loading" class="portal-panel portal-state">
        <p>{{ config.loading }}</p>
      </section>

      <section v-else-if="error" class="portal-panel portal-state portal-state--error">
        <p>{{ config.error }}</p>
        <button type="button" class="portal-button" @click="fetchDashboard">
          {{ config.retry }}
        </button>
      </section>

      <template v-else>
        <section v-if="normalizedNoticeVisible" class="portal-banner" role="status">
          {{ config.trend_distribution }}
        </section>

        <section class="portal-dashboard-grid">
          <article class="portal-card portal-card--hero">
            <div class="portal-card__content">
              <div>
                <p class="portal-card__eyebrow">{{ config.dashboard_overview }}</p>
                <h2>{{ config.summary_title }}</h2>
                <p class="portal-card__copy">{{ config.growth_window }}</p>
              </div>

              <div class="portal-chip-grid">
                <div v-for="stat in heroStats" :key="stat.key" class="portal-chip-stat">
                  <span class="portal-chip-stat__value">{{ Number(stat.value).toLocaleString() }}</span>
                  <span class="portal-chip-stat__label">{{ stat.label }}</span>
                </div>
              </div>
            </div>

            <img
              v-if="currentAnalyticsImage"
              :src="currentAnalyticsImage"
              alt=""
              class="portal-card__illustration"
            >
          </article>

          <article class="portal-card portal-card--mini">
            <div class="portal-mini-card__header">
              <div>
                <span class="portal-mini-card__label">{{ config.registrations_last_30_days }}</span>
                <strong class="portal-mini-card__value">{{ payload.summary.registrations_last_30_days.toLocaleString() }}</strong>
              </div>
            </div>
            <div id="portal-mini-registrations-chart" class="portal-mini-chart" aria-hidden="true"></div>
          </article>

          <article class="portal-card portal-card--mini">
            <div class="portal-mini-card__header">
              <div>
                <span class="portal-mini-card__label">{{ config.signups_last_30_days }}</span>
                <strong class="portal-mini-card__value">{{ payload.summary.signups_last_30_days.toLocaleString() }}</strong>
              </div>
            </div>
            <div id="portal-mini-signups-chart" class="portal-mini-chart" aria-hidden="true"></div>
          </article>
        </section>

        <section class="portal-stat-grid">
          <article v-for="card in summaryCards" :key="card.key" class="portal-stat-card">
            <span class="portal-stat-card__label">{{ card.label }}</span>
            <strong class="portal-stat-card__value">{{ card.value.toLocaleString() }}</strong>
          </article>
        </section>

        <section class="portal-chart-grid">
          <article class="portal-card portal-card--chart" aria-labelledby="user-registration-chart-title">
            <div class="portal-card__header">
              <div>
                <h2 id="user-registration-chart-title">{{ config.user_registrations }}</h2>
                <p>{{ config.growth_window }}</p>
              </div>
            </div>
            <div id="portal-user-registrations-chart" class="portal-chart" aria-hidden="true"></div>
          </article>

          <article class="portal-card portal-card--chart" aria-labelledby="workout-signup-chart-title">
            <div class="portal-card__header">
              <div>
                <h2 id="workout-signup-chart-title">{{ config.workout_signups }}</h2>
                <p>{{ config.growth_window }}</p>
              </div>
            </div>
            <div id="portal-workout-signups-chart" class="portal-chart" aria-hidden="true"></div>
          </article>
        </section>

        <section class="portal-card portal-card--table" aria-labelledby="chapter-activity-title">
          <div class="portal-card__header">
            <div>
              <h2 id="chapter-activity-title">{{ config.chapter_activity }}</h2>
              <p>{{ config.summary_copy }}</p>
            </div>
          </div>

          <div class="portal-table-wrap">
            <table class="portal-table">
              <thead>
                <tr>
                  <th scope="col">{{ config.chapter }}</th>
                  <th scope="col">{{ config.locations }}</th>
                  <th scope="col">{{ config.sessions }}</th>
                  <th scope="col">{{ config.signups }}</th>
                  <th scope="col">{{ config.athletes }}</th>
                  <th scope="col">{{ config.guides }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in chapterActivity" :key="row.id">
                  <td>{{ row.name }}</td>
                  <td>{{ row.locations_count.toLocaleString() }}</td>
                  <td>{{ row.sessions_count.toLocaleString() }}</td>
                  <td>{{ row.signups_count.toLocaleString() }}</td>
                  <td>{{ row.athlete_signups_count.toLocaleString() }}</td>
                  <td>{{ row.guide_signups_count.toLocaleString() }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="portal-accessible-grid">
          <details
            v-for="table in chartTables"
            :key="`${table.key}-table`"
            class="portal-card portal-card--details"
          >
            <summary>{{ table.title }} · {{ config.table_view }}</summary>
            <div class="portal-table-wrap">
              <table class="portal-table">
                <thead>
                  <tr>
                    <th scope="col">{{ config.date }}</th>
                    <th scope="col">{{ config.value }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(label, index) in table.labels" :key="`${table.key}-${label}`">
                    <td>{{ label }}</td>
                    <td>{{ table.values[index].toLocaleString() }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </details>
        </section>
      </template>
    </main>
  </div>
</template>
