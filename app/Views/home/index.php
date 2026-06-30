<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'BookNest') ?></title>
    <style>
        :root{
            --bg:#120806;
            --panel:rgba(255,255,255,.06);
            --panel-border:rgba(255,255,255,.1);
            --text:#f5e7d8;
            --muted:#c7a98d;
            --accent:#e09a5d;
            --accent-2:#8f5a35;
        }
        *{box-sizing:border-box}
        body{
            margin:0;
            font-family: Inter, Arial, sans-serif;
            background:
                radial-gradient(circle at top, #2a120e 0%, #140806 45%, #0b0504 100%);
            color:var(--text);
        }
        .topbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:1rem;
            padding:1rem 2rem;
            border-bottom:1px solid rgba(255,255,255,.06);
            backdrop-filter: blur(10px);
            position:sticky;
            top:0;
            background:rgba(18,8,6,.75);
            z-index:10;
        }
        .brand{
            display:flex;
            align-items:center;
            gap:.75rem;
            font-weight:700;
        }
        .brand-badge{
            width:38px;height:38px;border-radius:12px;
            display:grid;place-items:center;
            background:linear-gradient(135deg,#3b1f16,#7a4a2b);
            border:1px solid rgba(255,255,255,.12);
        }
        .search{
            flex:1;
            max-width:520px;
            display:flex;
            align-items:center;
            gap:.75rem;
            padding:.9rem 1rem;
            border-radius:14px;
            background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.08);
            color:#d8c6b7;
        }
        .actions{display:flex;gap:.75rem}
        .btn{
            padding:.8rem 1rem;
            border-radius:12px;
            border:1px solid rgba(255,255,255,.12);
            background:transparent;
            color:var(--text);
            text-decoration:none;
        }
        .btn.primary{
            background:linear-gradient(135deg,var(--accent),#d77d3f);
            color:#24120c;
            font-weight:700;
        }
        .container{
            max-width:1250px;
            margin:0 auto;
            padding:3rem 2rem 4rem;
        }
        .hero{
            display:grid;
            grid-template-columns:1.3fr .9fr;
            gap:2rem;
            align-items:start;
            margin-top:1rem;
        }
        .eyebrow{
            text-transform:uppercase;
            letter-spacing:.18em;
            color:#b98a6d;
            font-size:.8rem;
            margin-bottom:1rem;
        }
        h1{
            font-family: Georgia, 'Times New Roman', serif;
            font-size:clamp(3rem, 5vw, 5.5rem);
            line-height:.95;
            margin:0;
            color:#f7e9d8;
        }
        .subtitle{
            margin-top:1.2rem;
            max-width:620px;
            font-size:1.08rem;
            line-height:1.7;
            color:#ceb39b;
        }
        .stats{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:1rem;
        }
        .stat{
            padding:1.1rem 1.1rem 1rem;
            border-radius:16px;
            background:var(--panel);
            border:1px solid var(--panel-border);
            box-shadow:0 12px 30px rgba(0,0,0,.18);
        }
        .stat small{
            display:block;
            color:#cda98a;
            text-transform:uppercase;
            letter-spacing:.12em;
            margin-bottom:.5rem;
            font-size:.72rem;
        }
        .stat strong{
            font-size:2rem;
            color:#fff1e0;
        }
        .filters{
            display:flex;
            flex-wrap:wrap;
            gap:.75rem;
            margin:2rem 0 1.5rem;
        }
        .chip{
            padding:.55rem .9rem;
            border-radius:999px;
            border:1px solid rgba(255,255,255,.1);
            background:rgba(255,255,255,.04);
            color:#dfc7b1;
            font-size:.92rem;
        }
        .chip.active{
            background:var(--accent);
            color:#2a140d;
            font-weight:700;
            border-color:transparent;
        }
        .shelves{
            display:grid;
            gap:2.5rem;
            margin-top:2rem;
        }
        .shelf-label{
            color:#9b6f52;
            font-size:.75rem;
            letter-spacing:.22em;
            text-transform:uppercase;
            margin-bottom:1rem;
        }
        .shelf{
            height:260px;
            border-radius:20px;
            position:relative;
            padding:1.2rem;
            background:
                linear-gradient(to bottom, rgba(255,255,255,.04), rgba(255,255,255,.02)),
                linear-gradient(to right, #4b2a1c, #2b160f);
            box-shadow:inset 0 1px 0 rgba(255,255,255,.12), 0 20px 40px rgba(0,0,0,.2);
        }
        .books{
            position:absolute;
            left:1.5rem;
            bottom:1.2rem;
            display:flex;
            align-items:flex-end;
            gap:.25rem;
        }
        .book{
            width:42px;
            border-radius:4px 4px 0 0;
            position:relative;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.25);
        }
        .book::after{
            content:attr(data-title);
            position:absolute;
            left:50%;
            bottom:10px;
            transform:translateX(-50%) rotate(-90deg);
            transform-origin:center;
            white-space:nowrap;
            font-size:.68rem;
            color:rgba(255,255,255,.82);
            letter-spacing:.03em;
        }
        .b1{height:180px;background:#db9b5d;}
        .b2{height:150px;background:#8e3b3b;}
        .b3{height:168px;background:#b45353;}
        .b4{height:190px;background:#46484e;}
        .b5{height:158px;background:#c9a47a;}
        .b6{height:176px;background:#2f5d46;}
        .b7{height:164px;background:#6a87b8;}
        .b8{height:210px;background:#c28d4a;}
        .b9{height:145px;background:#5f7f4b;}
        .base{
            position:absolute;
            left:1rem;
            right:1rem;
            bottom:0;
            height:18px;
            border-radius:10px;
            background:linear-gradient(to bottom, #6e4938, #3b2015);
        }
        @media (max-width: 900px){
            .hero{grid-template-columns:1fr}
            .search{display:none}
            .stats{grid-template-columns:1fr 1fr}
        }
        @media (max-width: 640px){
            .topbar{padding:1rem}
            .container{padding:2rem 1rem 3rem}
            .stats{grid-template-columns:1fr}
            h1{font-size:2.7rem}
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">
            <div class="brand-badge">B</div>
            <div>
                <div>BookNest</div>
                <small style="color:#b6947a">Votre collection personnelle</small>
            </div>
        </div>

        <div class="search">Chercher un titre, un auteur...</div>

        <div class="actions">
            <a class="btn" href="#">Connexion</a>
            <a class="btn primary" href="#">+ Ajouter</a>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <div>
                <div class="eyebrow">Rayon principal</div>
                <h1>Chaque livre a sa place.<br>Chaque histoire, son étagère.</h1>
                <p class="subtitle">
                    Parcourez votre collection comme une vraie bibliothèque.
                    Cliquez sur une tranche pour ouvrir le livre, suivez vos lectures et vos prêts.
                </p>
            </div>

            <div class="stats">
                <div class="stat">
                    <small>Livres</small>
                    <strong>27</strong>
                </div>
                <div class="stat">
                    <small>Terminés</small>
                    <strong>11</strong>
                </div>
                <div class="stat">
                    <small>En cours</small>
                    <strong>4</strong>
                </div>
                <div class="stat">
                    <small>Pages lues</small>
                    <strong>3769</strong>
                </div>
            </div>
        </section>

        <div class="filters">
            <span class="chip active">Tous</span>
            <span class="chip">Roman</span>
            <span class="chip">Classique</span>
            <span class="chip">SF</span>
            <span class="chip">Essai</span>
            <span class="chip">Tous statuts</span>
            <span class="chip">À lire</span>
            <span class="chip">En cours</span>
            <span class="chip">Terminé</span>
            <span class="chip">Prêté</span>
        </div>

        <section class="shelves">
            <div>
                <div class="shelf-label">Rayon 01</div>
                <div class="shelf">
                    <div class="books">
                        <div class="book b8" data-title="L'ombre de la nuit"></div>
                        <div class="book b2" data-title="Le carnet"></div>
                        <div class="book b3" data-title="Dune"></div>
                        <div class="book b2" data-title="Les Misérables"></div>
                        <div class="book b4" data-title="Sapiens"></div>
                        <div class="book b5" data-title="L'usage du monde"></div>
                        <div class="book b6" data-title="1984"></div>
                        <div class="book b7" data-title="La boîte de Schrödinger"></div>
                    </div>
                    <div class="base"></div>
                </div>
            </div>

            <div>
                <div class="shelf-label">Rayon 02</div>
                <div class="shelf">
                    <div class="books">
                        <div class="book b3" data-title="Fahrenheit 451"></div>
                        <div class="book b7" data-title="Moby Dick"></div>
                        <div class="book b6" data-title="Beloved"></div>
                        <div class="book b5" data-title="Mémoires d'Hadrien"></div>
                        <div class="book b9" data-title="Frankenstein"></div>
                        <div class="book b6" data-title="Le Parfum"></div>
                        <div class="book b5" data-title="L'étranger"></div>
                    </div>
                    <div class="base"></div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
