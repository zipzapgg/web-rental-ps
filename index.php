<?php include 'config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violet Playstation - Best Gaming Experience</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --violet-primary: #8a2be2;
            --violet-dark: #0f051d;
            --violet-glow: #bc13fe;
        }
        body {
            background-color: var(--violet-dark);
            color: white;
            font-family: 'Poppins', sans-serif;
        }
        .gaming-font { font-family: 'Orbitron', sans-serif; }
        
        /* Navbar Styling */
        .navbar { background-color: rgba(15, 5, 29, 0.9) !important; border-bottom: 2px solid var(--violet-primary); }
        .navbar-brand img { filter: drop-shadow(0 0 5px var(--violet-glow)); }

        /* Pricing Card */
        .price-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--violet-primary);
            border-radius: 15px;
            transition: 0.3s;
        }
        .price-card:hover { box-shadow: 0 0 20px var(--violet-glow); transform: translateY(-5px); }
        .text-neon { color: var(--violet-glow); text-shadow: 0 0 10px var(--violet-glow); }

        /* Game Card */
        .game-card { background: #1a1a2e; border: none; border-radius: 10px; overflow: hidden; }
        .game-img { height: 250px; object-fit: cover; }
        
        .btn-violet { background: var(--violet-primary); color: white; border: none; }
        .btn-violet:hover { background: var(--violet-glow); color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="assets/images/logo.png" alt="Logo" width="50" class="me-2">
            <span class="gaming-font fw-bold">VIOLET <span class="text-neon">PS</span></span>
        </a>
    </div>
</nav>

<header class="container mt-5 text-center">
    <h1 class="gaming-font display-4">READY TO <span class="text-neon">PLAY?</span></h1>
    <p class="lead">Rental PS4 & PS5 Terpercaya di Jagakarsa</p>
</header>

<section class="container mt-5">
    <h3 class="gaming-font mb-4 text-center">DAFTAR HARGA <span class="small text-muted">(Main di Tempat)</span></h3>
    <div class="row justify-content-center">
        <div class="col-md-5 mb-4">
            <div class="price-card p-4">
                <h4 class="gaming-font text-center mb-3 text-neon">PLAYSTATION 4</h4>
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between"><span>1 Jam</span> <span>Rp 8.000</span></li>
                    <li class="d-flex justify-content-between"><span>2 Jam</span> <span>Rp 15.000</span></li>
                    <li class="d-flex justify-content-between fw-bold text-warning"><span>3 Jam (Best)</span> <span>Rp 20.000</span></li>
                    <li class="d-flex justify-content-between"><span>5 Jam</span> <span>Rp 35.000</span></li>
                </ul>
            </div>
        </div>
        <div class="col-md-5 mb-4">
            <div class="price-card p-4 border-info">
                <h4 class="gaming-font text-center mb-3 text-info">PLAYSTATION 5</h4>
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between"><span>1 Jam</span> <span>Rp 15.000</span></li>
                    <li class="d-flex justify-content-between"><span>2 Jam</span> <span>Rp 28.000</span></li>
                    <li class="d-flex justify-content-between fw-bold text-info"><span>3 Jam (Best)</span> <span>Rp 42.000</span></li>
                    <li class="d-flex justify-content-between"><span>6 Jam</span> <span>Rp 84.000</span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center mt-2">
        <small class="text-danger fw-bold">* NOTES: TIDAK BISA MENYIMPAN WAKTU</small>
    </div>
</section>

<section class="container mt-5 pt-5 border-top border-secondary">
    <h3 class="gaming-font mb-4">KOLEKSI <span class="text-neon">GAME KAMI</span></h3>
    <div class="row">
        <?php
        // Mengambil game unik dari database
        $sql = "SELECT DISTINCT games.judul_game, games.foto_game 
                FROM games 
                JOIN unit_games ON games.id_game = unit_games.id_game";
        $query = mysqli_query($koneksi, $sql);
        while($g = mysqli_fetch_assoc($query)){
        ?>
        <div class="col-6 col-md-3 mb-4">
            <div class="game-card shadow">
                <img src="uploads/games/<?php echo $g['foto_game']; ?>" class="card-img-top game-img" alt="Game">
                <div class="card-body">
                    <h6 class="text-truncate small"><?php echo $g['judul_game']; ?></h6>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>

<footer class="container-fluid py-5 mt-5 bg-black">
    <div class="container text-center">
        <h4 class="gaming-font mb-3">MINAT SEWA BAWA PULANG?</h4>
        <p>Persyaratan: KTP/STNK & Berdomisili Jagakarsa</p>
        <a href="https://wa.me/6285847831078" class="btn btn-violet btn-lg px-5 py-3 gaming-font">BOOKING VIA WHATSAPP</a>
        <div class="mt-4">
            <p class="small text-muted">© 2026 Violet Playstation. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>