<header>
    <?php
    $currentPage = basename(dirname($_SERVER['SCRIPT_FILENAME']));
    $navItems = [
        ['path' => '../events/index.php', 'label' => 'Meine Events', 'page' => 'events'],
        ['path' => '../unterhaltung/index.php', 'label' => 'Unterhaltung', 'page' => 'unterhaltung'],
        ['path' => '../mobilliar/index.php', 'label' => 'Mobilliar', 'page' => 'mobilliar'],
        ['path' => '../menue/index.php', 'label' => 'Menü', 'page' => 'menue'],
        ['path' => '../energieversorgung/index.php', 'label' => 'Energieversorgung', 'page' => 'energieversorgung'],
        ['path' => '../termin/index.php', 'label' => 'Termin', 'page' => 'termin'],
        ['path' => '../uebersicht/index.php', 'label' => 'Übersicht', 'page' => 'uebersicht'],
    ];
    ?>
    <div class="logo-title">
        <a href="<?= isLoggedIn() ? '../home/index.php' : '../index.php' ?>">
            <img src="../assets/icon32x32.png" alt="Event-Manager">
        </a>

        <a href="<?= isLoggedIn() ? '../home/index.php' : '../index.php' ?>">
            <h1>Event-Manager</h1>
        </a>

        <?php if (isLoggedIn()): ?>
            <nav aria-label="Hauptnavigation">
                <ul>
                    <?php foreach ($navItems as $item): ?>
                        <li>
                            <a href="<?= $item['path'] ?>" class="<?= $currentPage === $item['page'] ? 'active' : '' ?>"
                                <?= $currentPage === $item['page'] ? 'aria-current="page"' : '' ?>>
                                <?= htmlspecialchars($item['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <?php if (isLoggedIn()): ?>
        <div class="user-menu">
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

                <?php if (isAdmin($pdo, (int) currentUserId())): ?>
                    <div class="menu-divider"></div>
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
<script src="../js/theme-toggle.js"></script>