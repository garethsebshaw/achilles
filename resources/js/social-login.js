Nova.booting((Vue) => {
    // Hook into Nova's login page
    Nova.inertia('Login', {
        setup(props) {
            // Add social login buttons
            const socialButtons = () => {
                return h('div', { class: 'mt-6' }, [
                    h('div', { class: 'text-center text-sm text-gray-600' }, 'Or continue with'),
                    h('div', { class: 'mt-4 flex justify-center space-x-4' }, [
                        h('a', {
                            href: '/auth/google/redirect',
                            class: 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50'
                        }, 'Google'),
                        h('a', {
                            href: '/auth/facebook/redirect',
                            class: 'inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50'
                        }, 'Facebook')
                    ])
                ])
            }

            return {
                socialButtons
            }
        }
    })
})
