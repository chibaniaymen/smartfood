document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let isValid = true;

        const checks = [
            { id: 'title', err: 'titleErr', min: 3, msg: "Title must be at least 3 characters." },
            { id: 'event_date', err: 'dateErr', required: true, msg: "Please select a date." },
            { id: 'location_id', err: 'locErr', required: true, msg: "Please select a valid location ID." },
            { id: 'price', err: 'priceErr', isNumeric: true, msg: "Please enter a valid price." }
        ];

        checks.forEach(check => {
            const el = document.getElementById(check.id);
            const errEl = document.getElementById(check.err);

            if (errEl) errEl.innerText = "";

            if (el) {
                const val = el.value.trim();
                if (check.isNumeric) {
                    if (val !== "" && parseFloat(val) < 0) {
                        if (errEl) errEl.innerText = check.msg;
                        isValid = false;
                    }
                } else if (check.required) {
                    if (!val || (check.id === 'location_id' && parseInt(val) <= 0)) {
                        if (errEl) errEl.innerText = check.msg;
                        isValid = false;
                    }
                } else if (val.length < check.min) {
                    if (errEl) errEl.innerText = check.msg;
                    isValid = false;
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
});
