<?php
// Common footer template
$user = $_SESSION['user'] ?? ['logged_in' => false];
echo generateFooter($user);
?>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            menu.classList.add('animate-slide-down');
        } else {
            menu.classList.add('animate-slide-up');
            setTimeout(() => {
                menu.classList.add('hidden');
                menu.classList.remove('animate-slide-up', 'animate-slide-down');
            }, 300);
        }
    }
</script>
<script src="assets/js/animations.js"></script>
</body>
</html>