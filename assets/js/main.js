/**
 * Grand TAR ABC Student Hostel - Main JS
 */

document.addEventListener('DOMContentLoaded', function () {
    // Navbar scroll effect
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // Set min date for move-in / move-out to today
    const today = new Date().toISOString().split('T')[0];
    const moveInInputs = document.querySelectorAll('input[name="move_in"], #move_in, #check_in');
    const moveOutInputs = document.querySelectorAll('input[name="move_out"], #move_out, #check_out');

    moveInInputs.forEach(function (el) {
        if (el) el.setAttribute('min', today);
    });

    moveOutInputs.forEach(function (el) {
        if (el) el.setAttribute('min', today);
    });

    // Auto-update move-out min when move-in changes
    moveInInputs.forEach(function (moveIn) {
        moveIn.addEventListener('change', function () {
            moveOutInputs.forEach(function (moveOut) {
                if (moveOut) {
                    moveOut.setAttribute('min', moveIn.value || today);
                    if (moveOut.value && moveOut.value <= moveIn.value) {
                        moveOut.value = '';
                    }
                }
            });
        });
    });

    // Booking form price calculation (if present)
    const roomSelect = document.getElementById('room_id');
    const moveIn = document.getElementById('move_in');
    const moveOut = document.getElementById('move_out');
    const priceDisplay = document.getElementById('estimated_price');

    function updatePrice() {
        if (!roomSelect || !moveIn || !moveOut || !priceDisplay) return;
        const option = roomSelect.options[roomSelect.selectedIndex];
        const price = parseFloat(option.getAttribute('data-price') || 0);
        if (!moveIn.value || !moveOut.value || !price) {
            priceDisplay.textContent = 'RM 0';
            return;
        }
        const start = new Date(moveIn.value);
        const end = new Date(moveOut.value);
        if (end <= start) {
            priceDisplay.textContent = 'RM 0';
            return;
        }
        let months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
        if (end.getDate() > start.getDate()) months += 1;
        months = Math.max(1, months);
        priceDisplay.textContent = 'RM ' + (price * months).toLocaleString('en-MY', { minimumFractionDigits: 0 });
    }

    if (roomSelect) roomSelect.addEventListener('change', updatePrice);
    if (moveIn) moveIn.addEventListener('change', updatePrice);
    if (moveOut) moveOut.addEventListener('change', updatePrice);
});
