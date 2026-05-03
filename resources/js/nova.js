import './bootstrap';

Nova.booting((Vue, router, store) => {
    Nova.$on('error', (message) => {
        Nova.$toasted.error(message, {
            duration: false, // 10 seconds (or set to `false` to never disappear)
            keepOnHover: true,
            position: "bottom-right",
            action: {
                text: "Close",
                onClick: (e, toastObject) => {
                    toastObject.goAway(0);
                }
            }
        });
    });
});
