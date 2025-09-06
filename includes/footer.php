<?php
// includes/footer.php
?>
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row g-4">
            <!-- About Column -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-about">
                    <a href="index.php" class="d-flex align-items-center mb-3 text-decoration-none">
                        <span class="fs-4 text-white fw-bold">ShopEase</span>
                    </a>
                    <p class="mb-3">Your one-stop e-commerce destination offering quality products at affordable prices with seamless shopping experience.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-2" aria-label="Facebook"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white me-2" aria-label="Twitter"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white me-2" aria-label="Instagram"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white" aria-label="LinkedIn"><i class="bi bi-linkedin fs-5"></i></a>
                    </div>
                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="col-lg-2 col-md-6">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="index.php" class="nav-link p-0 text-white-50">Home</a></li>
                    <li class="nav-item mb-2"><a href="products.php" class="nav-link p-0 text-white-50">All Products</a></li>
                    <li class="nav-item mb-2"><a href="about.php" class="nav-link p-0 text-white-50">About Us</a></li>
                    <li class="nav-item mb-2"><a href="contact.php" class="nav-link p-0 text-white-50">Contact</a></li>
                    <li class="nav-item mb-2"><a href="faq.php" class="nav-link p-0 text-white-50">FAQs</a></li>
                </ul>
            </div>

            <!-- Categories Column -->
            <div class="col-lg-2 col-md-6">
                <h5 class="mb-3">Categories</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="electronics.php" class="nav-link p-0 text-white-50">Electronics</a></li>
                    <li class="nav-item mb-2"><a href="clothing.php" class="nav-link p-0 text-white-50">Clothing</a></li>
                    <li class="nav-item mb-2"><a href="furniture.php" class="nav-link p-0 text-white-50">Home Goods</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="col-lg-4 col-md-6">
                <h5 class="mb-3">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="https://maps.google.com" class="text-white-50 text-decoration-none">
                            <i class="bi bi-geo-alt-fill me-2"></i> 123 Shop Street, Commerce City
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="tel:+1234567890" class="text-white-50 text-decoration-none">
                            <i class="bi bi-telephone-fill me-2"></i> (123) 456-7890
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="mailto:info@shopease.com" class="text-white-50 text-decoration-none">
                            <i class="bi bi-envelope-fill me-2"></i> info@shopease.com
                        </a>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-clock-fill me-2"></i> Mon-Fri: 9AM - 6PM
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0">
                <p class="mb-0">&copy; <?= date('Y') ?> ShopEase. All rights reserved.</p>
            </div>
            <div class="d-flex">
                <a href="privacy.php" class="text-white-50 me-3 text-decoration-none">Privacy Policy</a>
                <a href="terms.php" class="text-white-50 me-3 text-decoration-none">Terms of Service</a>
                <a href="returns.php" class="text-white-50 text-decoration-none">Return Policy</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<a href="#" class="btn btn-primary btn-lg back-to-top position-fixed bottom-0 end-0 m-4 rounded-circle shadow" style="display: none;">
    <i class="bi bi-arrow-up"></i>
</a>

<script>
// Back to Top Button
window.addEventListener('scroll', function() {
    var backToTop = document.querySelector('.back-to-top');
    if (window.pageYOffset > 300) {
        backToTop.style.display = 'block';
    } else {
        backToTop.style.display = 'none';
    }
});

document.querySelector('.back-to-top').addEventListener('click', function(e) {
    e.preventDefault();
    window.scrollTo({top: 0, behavior: 'smooth'});
});
</script>