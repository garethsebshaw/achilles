import { createApp } from 'vue'
import PortalDashboardApp from './PortalDashboardApp.vue'

const app = createApp(PortalDashboardApp)
app.mount('#portal-dashboard')

document.addEventListener('click', event => {
  const toggle = event.target.closest('[data-portal-menu-toggle]')

  if (!toggle) {
    return
  }

  document.body.classList.toggle('portal-menu-open')
})
