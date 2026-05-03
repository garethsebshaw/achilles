<template>
    <div>
        <DefaultLogin />
        <div class="mt-6 text-center">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">Or continue with</span>
                </div>
            </div>
            <div class="flex space-x-4 justify-center mt-4">
                <a href="/auth/google/redirect"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Sign in with Google
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue'
import DefaultLogin from '@/vendor/nova/resources/js/pages/Login.vue'

export default defineComponent({
    components: {
        DefaultLogin
    }
})
</script>
