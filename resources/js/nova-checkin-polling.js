Nova.booting((Vue) => {
    setInterval(() => {
        Nova.request()
            .get('/nova-api/workout-signups')
            .then(response => {
                if (!response || !response.data || !response.data.resources) {
                    console.warn("Nova API response is empty or malformed:", response);
                    return;
                }

                let signups = response.data.resources;

                signups.forEach(item => {
                    let checkInButton = document.querySelector(`[dusk="action-check-in-${item.id}"]`);
                    let checkOutButton = document.querySelector(`[dusk="action-check-out-${item.id}"]`);

                    if (!item.fields) {
                        console.warn(`No fields found for item ID ${item.id}`);
                        return;
                    }

                    let checkedInField = item.fields.find(field => field.attribute === 'checked_in_at');
                    let checkedOutField = item.fields.find(field => field.attribute === 'checked_out_at');

                    let checkedInAt = checkedInField ? checkedInField.value : null;
                    let checkedOutAt = checkedOutField ? checkedOutField.value : null;

                    if (checkInButton) {
                        if (checkedInAt) {
                            checkInButton.innerHTML = 'Cancel Check In';
                            checkInButton.style.backgroundColor = '#ffcc00';
                        } else {
                            checkInButton.innerHTML = 'Check In';
                            checkInButton.style.backgroundColor = '#28a745';
                        }
                    }

                    if (checkOutButton) {
                        if (checkedInAt && !checkedOutAt) {
                            checkOutButton.style.display = 'block';
                        } else {
                            checkOutButton.style.display = 'none';
                        }
                    }
                });
            })
            .catch(error => {
                console.error("Polling error:", error);
            });
    }, 5000);
});
