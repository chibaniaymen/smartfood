document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('locationForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let isValid = true;
        
        const checks = [
            { id: 'name', err: 'nameErr', min: 3, msg: "Name must be at least 3 characters." },
            { id: 'address', err: 'addressErr', min: 5, msg: "Address is too short." },
            { id: 'city', err: 'cityErr', min: 2, msg: "City is required." },
            { id: 'capacity', err: 'capacityErr', isNumeric: true, msg: "Please enter a valid capacity." }
        ];

        checks.forEach(check => {
            const el = document.getElementById(check.id);
            const errEl = document.getElementById(check.err);
            
            if (errEl) errEl.innerText = "";

            if (el) {
                const val = el.value.trim();
                if (check.isNumeric) {
                    if (val !== "" && parseInt(val) < 0) {
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
