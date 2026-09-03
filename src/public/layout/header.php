<header>
    <div class="logo-title">
        <a href="<?= isLoggedIn() ? '../home/index.php' : '../index.php' ?>">
            <img src="../assets/icon32x32.png" alt="Event-Manager">
        </a>

        <a href="<?= isLoggedIn() ? '../home/index.php' : '../index.php' ?>">
            <h1>Event-Manager</h1>
        </a>

        <?php if (isLoggedIn()): ?>
            <nav>
                <ul>
                    <li><a href="../unterhaltung/index.php">Unterhaltung</a></li>
                    <li><a href="../mobilliar/index.php">Mobilliar</a></li>
                    <li><a href="../menue/index.php">Menü</a></li>
                    <li><a href="../energieversorgung/index.php">Energieversorgung</a></li>
                    <li><a href="../termin/index.php">Termin</a></li>
                    <li><a href="../uebersicht/index.php">Übersicht</a></li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <?php if (isLoggedIn()): ?>
        <div class="user-menu">
            <button
                id="user-menu-toggle"
                class="burger-btn"
                type="button"
                aria-label="Benutzermenü öffnen"
                aria-expanded="false"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div id="user-menu-dropdown" class="user-menu-dropdown">

                <div class="user-info">
                    <span class="user-label">Angemeldet als</span>
                    <span class="user-email">
                        <?= htmlspecialchars(currentUserEmail() ?? '') ?>
                    </span>
                </div>

                <div class="menu-divider"></div>

                <button id="theme-toggle" type="button" class="dropdown-item">
                    <span>🌙</span>
                    <span>Theme wechseln</span>
                </button>

                <a href="../logout/index.php" class="dropdown-item logout-item">
                    <span>↪</span>
                    <span>Logout</span>
                </a>

            </div>
        </div>
    <?php endif; ?>
</header>

<script src="../js/user-menu.js"></script>
<script src="../js/theme-toggle.js"></script>