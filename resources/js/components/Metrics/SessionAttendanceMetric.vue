<template>
    <DefaultCard :loading="loading">
        <CardHeader v-text="title" />

        <LoadingView v-if="loading">
            <LoadingCard />
        </LoadingView>

        <div v-else class="px-6 py-4">
            <!-- Your metric content here -->
            <div class="mb-4">
                <h4 class="text-md font-semibold">Athletes</h4>
                <div>
                    Checked In: {{ athleteStats.checkedIn }} of {{ athleteStats.total }}
                    ({{ athleteStats.checkedInPercent }}%)
                </div>
            </div>

            <div>
                <h4 class="text-md font-semibold">Guides</h4>
                <div>
                    Checked In: {{ guideStats.checkedIn }} of {{ guideStats.total }}
                    ({{ guideStats.checkedInPercent }}%)
                </div>
            </div>
        </div>
    </DefaultCard>
</template>

<script>
export default {
    name: 'SessionAttendanceMetric',

    props: ['card'],  // This receives the metric data

    data() {
        return {
            loading: true,
        }
    },

    computed: {
        title() {
            return this.card.name
        },

        athleteStats() {
            return this.card.value.athleteStats || {
                total: 0,
                checkedIn: 0,
                checkedInPercent: 0
            }
        },

        guideStats() {
            return this.card.value.guideStats || {
                total: 0,
                checkedIn: 0,
                checkedInPercent: 0
            }
        }
    },

    mounted() {
        this.loading = false
    }
}
</script>
