<?php
// includes/footer.php
?>
        </main>
    </div>
    <script>
        // Auto-expand the appropriate menu based on active submenu item
        const activeLink = document.querySelector('.submenu a.active');
        if (activeLink) {
            let parentSubmenu = activeLink.closest('.submenu');
            let parentLink = parentSubmenu.previousElementSibling;
            parentLink.classList.add('active');
        }


        document.addEventListener("DOMContentLoaded", () => {
            const sidebarLinks = document.querySelectorAll(".sidebar-link");

            sidebarLinks.forEach(link => {
                link.addEventListener("click", (e) => {
                e.preventDefault(); 
                const submenu = link.nextElementSibling;
                if (submenu && submenu.classList.contains("submenu")) {
                    submenu.classList.toggle("open");
                }
                });

                // Auto-open submenu if one of its children is active
                const submenu = link.nextElementSibling;
                if (submenu && submenu.querySelector("a.active")) {
                submenu.classList.add("open");
                }
            });
        });
    </script>
</body>
</html>