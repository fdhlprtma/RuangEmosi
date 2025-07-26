</main>

<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>Tentang RuangEmosi</h3>
                <p>RuangEmosi adalah platform digital yang memberikan ruang aman untuk kesehatan mental anak muda Indonesia.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
            <div class="footer-section links">
                <h3>Link Cepat</h3>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/about.php">Tentang Kami</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/contact.php">Kontak</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/kebijakan_privasi.php">Kebijakan Privasi</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/syarat_ketentuan.php">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div class="footer-section contact">
                <h3>Hubungi Kami</h3>
                <p><i class="fas fa-envelope"></i> hello@ruangemosi.id</p>
                <p><i class="fas fa-phone"></i> +62 812 3456 7890</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> RuangEmosi. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>

<style>
    /* Footer Styles */
    .main-footer {
        background-color: #333;
        color: #fff;
        padding: 40px 0;
        font-family: Arial, sans-serif;
    }

    .main-footer .container {
        width: 80%;
        margin: 0 auto;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .footer-section {
        width: 30%;
        margin-bottom: 30px;
    }

    .footer-section h3 {
        font-size: 1.2em;
        margin-bottom: 15px;
    }

    .footer-section p {
        font-size: 0.9em;
        line-height: 1.5;
    }

    .footer-section ul {
        list-style-type: none;
        padding: 0;
    }

    .footer-section ul li {
        margin: 10px 0;
    }

    .footer-section ul li a {
        color: #fff;
        text-decoration: none;
        font-size: 1em;
        transition: color 0.3s;
    }

    .footer-section ul li a:hover {
        color:rgb(0, 145, 255);
    }

    .social-links a {
        margin-right: 10px;
        color: #fff;
        font-size: 1.5em;
        transition: color 0.3s;
    }

    .social-links a:hover {
        color:rgb(0, 128, 255);
    }

    .footer-bottom {
        text-align: center;
        padding: 10px 0;
        font-size: 0.9em;
        border-top: 1px solid #444;
        margin-top: 30px;
    }

    .footer-bottom p {
        margin: 0;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            align-items: center;
        }

        .footer-section {
            width: 100%;
            text-align: center;
        }

        .footer-bottom p {
            font-size: 0.8em;
        }
    }
</style>

</body>

</html>