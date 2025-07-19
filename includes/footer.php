    </div>
    
    <footer class="mt-auto">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> Village Birth and Death Register System. All rights reserved.</p>
            <p>Developed for efficient village record management.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    alert.classList.add('fade');
                    setTimeout(function() {
                        alert.remove();
                    }, 150);
                }
            });
        }, 5000);
    </script>
</body>
</html>
