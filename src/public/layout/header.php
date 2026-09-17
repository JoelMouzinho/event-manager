<header>
    <div class="logo-title">
        <a href="<?= isLoggedIn() ? '../home/index.php' : '../index.php' ?>">
        </a>

        <a href="<?= isLoggedIn() ? '../home/index.php' : '../index.php' ?>">
            <h1>ITKFA Party-Organizer</h1>
        </a>

        <?php if (isLoggedIn()): ?>
            <nav>
                <ul>
                    <li><a href="../ort/index.php">Ort</a></li>
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
            <div class="quick-add-menu">
                <button id="quick-add-toggle" class="quick-add-btn" type="button" aria-label="Neues Event anlegen"
                    aria-expanded="false" title="Neues Event anlegen">
                    +
                </button>

                <div id="quick-add-panel" class="quick-add-panel">
                    <form method="post" action="../events/quick-create.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                        <label for="quick-event-name">Neues Event</label>
                        <input type="text" id="quick-event-name" name="event_name" placeholder="z.B. Geburtstag Lisa"
                            autocomplete="off">
                        <button type="submit" class="save-btn">Anlegen</button>
                    </form>
                </div>
            </div>

            <button id="user-menu-toggle" class="burger-btn" type="button" aria-label="Benutzermenü öffnen"
                aria-expanded="false">
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

                <div class="menu-divider"></div>

                <a href="../events/index.php" class="dropdown-item">
                    <span>📊</span>
                    <span>Mein Dashboard</span>
                </a>

                <?php if (isAdmin($pdo, (int) currentUserId())): ?>
                    <a href="../admin/index.php" class="dropdown-item">
                        <span>🛡️</span>
                        <span>Admin-Dashboard</span>
                    </a>
                <?php endif; ?>

                <div class="menu-divider"></div>

                <a href="../logout/index.php" class="dropdown-item logout-item">
                    <span>↪</span>
                    <span>Logout</span>
                </a>

            </div>
        </div>
    <?php endif; ?>
</header>

<script src="../js/user-menu.js"></script>
<script src="../js/quick-add.js"></script>
<script src="../js/theme-toggle.js"></script>