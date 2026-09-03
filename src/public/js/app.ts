// ============================================
// TYPE DEFINITIONS
// ============================================

interface EventPlannerData {
    unterhaltung: string[];
    mobilliar: string[];
    energie: string[];
    menue: string;
    date: string;
    time: string;
    endTime: string;
    notes: string;
}

interface Labels {
    [key: string]: string;
}

// ============================================
// INITIALIZATION & UTILITY FUNCTIONS
// ============================================

/**
 * Initialize all event listeners based on current page
 */
function initializeEventListeners(): void {
    // Check which page we're on and initialize accordingly
    const currentPage: string = window.location.pathname;

    if (currentPage.includes('unterhaltung')) {
        initUnterhaltung();
    } else if (currentPage.includes('mobilliar')) {
        initMobilliar();
    } else if (currentPage.includes('menue')) {
        initMenue();
    } else if (currentPage.includes('energieversorgung')) {
        initEnergie();
    } else if (currentPage.includes('termin')) {
        initTermin();
    } else if (currentPage.includes('uebersicht')) {
        displayOverview();
    }
}

/**
 * Save array data to localStorage
 */
function saveToLocalStorage(key: string, value: string[]): void {
    try {
        localStorage.setItem(key, JSON.stringify(value));
        console.log(`Saved to localStorage: ${key}`, value);
    } catch (error) {
        console.error(`Error saving to localStorage: ${key}`, error);
    }
}

/**
 * Load array data from localStorage
 */
function loadFromLocalStorage(key: string): string[] {
    try {
        const data: string | null = localStorage.getItem(key);
        return data ? JSON.parse(data) : [];
    } catch (error) {
        console.error(`Error loading from localStorage: ${key}`, error);
        return [];
    }
}

/**
 * Save simple string data to localStorage
 */
function saveSingleValue(key: string, value: string): void {
    try {
        localStorage.setItem(key, value);
        console.log(`Saved to localStorage: ${key} = ${value}`);
    } catch (error) {
        console.error(`Error saving to localStorage: ${key}`, error);
    }
}

/**
 * Load simple string data from localStorage
 */
function loadSingleValue(key: string): string {
    try {
        return localStorage.getItem(key) || '';
    } catch (error) {
        console.error(`Error loading from localStorage: ${key}`, error);
        return '';
    }
}

// ============================================
// UNTERHALTUNG PAGE
// ============================================

function initUnterhaltung(): void {
    console.log('Initializing Unterhaltung page...');
    
    // Load existing selections
    const savedUnterhaltung: string[] = loadFromLocalStorage('unterhaltung');
    console.log('Loaded from localStorage:', savedUnterhaltung);

    // Get all entertainment checkboxes
    const checkboxes: NodeListOf<HTMLInputElement> = document.querySelectorAll('input[name="entertainment"]');

    // Pre-check saved items
    checkboxes.forEach((checkbox: HTMLInputElement) => {
        if (savedUnterhaltung.includes(checkbox.value)) {
            checkbox.checked = true;
        }

        // Add change listener
        checkbox.addEventListener('change', function (): void {
            updateUnterhaltung();
        });
    });

    // Initial display
    displayUnterhaltungLabels(savedUnterhaltung);
}

function updateUnterhaltung(): void {
    const checkboxes: NodeListOf<HTMLInputElement> = document.querySelectorAll('input[name="entertainment"]:checked');
    const selected: string[] = Array.from(checkboxes).map((cb: HTMLInputElement) => cb.value);
    
    saveToLocalStorage('unterhaltung', selected);
    displayUnterhaltungLabels(selected);
}

function displayUnterhaltungLabels(items: string[]): void {
    // This is optional - shows which items are selected
    const labels: Labels = {
        'sound': '🎵 Sound / Musik',
        'tv': '📺 TV / Filme',
        'karaoke': '🎤 Karaoke',
        'band': '🎸 Live Band',
        'spiele': '🎮 Spiele / Quiz',
        'comedy': '🎭 Comedy / Show'
    };

    console.log('Displaying Unterhaltung:', items);
}

// ============================================
// MOBILLIAR PAGE
// ============================================

function initMobilliar(): void {
    console.log('Initializing Mobilliar page...');
    
    // Load existing selections
    const savedMobilliar: string[] = loadFromLocalStorage('mobilliar');
    console.log('Loaded from localStorage:', savedMobilliar);

    // Get all furniture checkboxes
    const checkboxes: NodeListOf<HTMLInputElement> = document.querySelectorAll('input[name="mobilliar"]');

    // Pre-check saved items
    checkboxes.forEach((checkbox: HTMLInputElement) => {
        if (savedMobilliar.includes(checkbox.value)) {
            checkbox.checked = true;
        }

        // Add change listener
        checkbox.addEventListener('change', function (): void {
            updateMobilliar();
        });
    });

    displayMobilliarLabels(savedMobilliar);
}

function updateMobilliar(): void {
    const checkboxes: NodeListOf<HTMLInputElement> = document.querySelectorAll('input[name="mobilliar"]:checked');
    const selected: string[] = Array.from(checkboxes).map((cb: HTMLInputElement) => cb.value);
    
    saveToLocalStorage('mobilliar', selected);
    displayMobilliarLabels(selected);
}

function displayMobilliarLabels(items: string[]): void {
    const labels: Labels = {
        'stuehle': '🪑 Stühle',
        'tische': '📋 Tische',
        'grill': '🔥 Grill',
        'bar': '🍹 Bar'
    };

    console.log('Displaying Mobilliar:', items);
}

// ============================================
// MENÜ PAGE
// ============================================

function initMenue(): void {
    console.log('Initializing Menü page...');
    
    // Load existing menu selection
    const savedMenue: string = loadSingleValue('menue');
    console.log('Loaded from localStorage:', savedMenue);

    // Get the input field
    const menuInput: HTMLInputElement | null = document.querySelector('.menu-input');
    
    if (menuInput) {
        // Set existing value
        if (savedMenue) {
            menuInput.value = savedMenue;
        }

        // Add change listener
        menuInput.addEventListener('change', function (): void {
            updateMenue();
        });

        menuInput.addEventListener('input', function (): void {
            updateMenue();
        });
    }
}

function updateMenue(): void {
    const menuInput: HTMLInputElement | null = document.querySelector('.menu-input');
    
    if (menuInput && menuInput.value.trim()) {
        saveSingleValue('menue', menuInput.value);
        console.log('Menu saved:', menuInput.value);
    }
}

// ============================================
// ENERGIEVERSORGUNG PAGE
// ============================================

function initEnergie(): void {
    console.log('Initializing Energieversorgung page...');
    
    // Load existing selections
    const savedEnergie: string[] = loadFromLocalStorage('energie');
    console.log('Loaded from localStorage:', savedEnergie);

    // Get all energy checkboxes
    const checkboxes: NodeListOf<HTMLInputElement> = document.querySelectorAll('input[name="energie"]');

    // Pre-check saved items
    checkboxes.forEach((checkbox: HTMLInputElement) => {
        if (savedEnergie.includes(checkbox.value)) {
            checkbox.checked = true;
        }

        // Add change listener
        checkbox.addEventListener('change', function (): void {
            updateEnergie();
        });
    });

    displayEnergieLabels(savedEnergie);
}

function updateEnergie(): void {
    const checkboxes: NodeListOf<HTMLInputElement> = document.querySelectorAll('input[name="energie"]:checked');
    const selected: string[] = Array.from(checkboxes).map((cb: HTMLInputElement) => cb.value);
    
    saveToLocalStorage('energie', selected);
    displayEnergieLabels(selected);
}

function displayEnergieLabels(items: string[]): void {
    const labels: Labels = {
        'stromanschluss': '⚡ Stromanschluss',
        'generator': '⚙️ Generator',
        'verlaengerung': '🔌 Verlängerungskabel',
        'beleuchtung': '💡 Beleuchtung',
        'notstrom': '🆘 Notstrom',
        'technik': '🎚️ Technik-Anschlüsse'
    };

    console.log('Displaying Energie:', items);
}

// ============================================
// TERMIN PAGE
// ============================================

function initTermin(): void {
    console.log('Initializing Termin page...');
    
    // Load existing values
    const savedDate: string = loadSingleValue('date');
    const savedTime: string = loadSingleValue('time');
    const savedEndTime: string = loadSingleValue('endTime');
    const savedNotes: string = loadSingleValue('notes');

    console.log('Loaded from localStorage:', { savedDate, savedTime, savedEndTime, savedNotes });

    // Get input fields
    const dateInput: HTMLInputElement | null = document.querySelector('.date-input');
    const timeInput: HTMLInputElement | null = document.querySelector('.time-input');
    const endTimeInputs: NodeListOf<HTMLInputElement> = document.querySelectorAll('.time-input');
    const endTimeInput: HTMLInputElement | null = endTimeInputs.length > 1 ? endTimeInputs[1] : null;
    const noteInput: HTMLTextAreaElement | null = document.querySelector('.note-input');

    // Set existing values
    if (dateInput && savedDate) dateInput.value = savedDate;
    if (timeInput && savedTime) timeInput.value = savedTime;
    if (endTimeInput && savedEndTime) endTimeInput.value = savedEndTime;
    if (noteInput && savedNotes) noteInput.value = savedNotes;

    // Add listeners
    if (dateInput) {
        dateInput.addEventListener('change', updateTermin);
    }
    if (timeInput) {
        timeInput.addEventListener('change', updateTermin);
    }
    if (endTimeInput) {
        endTimeInput.addEventListener('change', updateTermin);
    }
    if (noteInput) {
        noteInput.addEventListener('change', updateTermin);
    }
}

function updateTermin(): void {
    const dateInput: HTMLInputElement | null = document.querySelector('.date-input');
    const timeInputs: NodeListOf<HTMLInputElement> = document.querySelectorAll('.time-input');
    const noteInput: HTMLTextAreaElement | null = document.querySelector('.note-input');

    if (dateInput) {
        saveSingleValue('date', dateInput.value);
    }
    if (timeInputs[0]) {
        saveSingleValue('time', timeInputs[0].value);
    }
    if (timeInputs[1]) {
        saveSingleValue('endTime', timeInputs[1].value);
    }
    if (noteInput) {
        saveSingleValue('notes', noteInput.value);
    }

    console.log('Termin updated');
}

// ============================================
// OVERVIEW PAGE
// ============================================

function displayOverview(): void {
    console.log('Loading overview...');

    // Load all data from localStorage
    const unterhaltung: string[] = loadFromLocalStorage('unterhaltung');
    const mobilliar: string[] = loadFromLocalStorage('mobilliar');
    const energie: string[] = loadFromLocalStorage('energie');
    const menue: string = loadSingleValue('menue');
    const date: string = loadSingleValue('date');
    const time: string = loadSingleValue('time');
    const endTime: string = loadSingleValue('endTime');
    const notes: string = loadSingleValue('notes');

    // Labels for display
    const unterhaltungLabels: Labels = {
        'sound': '🎵 Sound / Musik',
        'tv': '📺 TV / Filme',
        'karaoke': '🎤 Karaoke',
        'band': '🎸 Live Band',
        'spiele': '🎮 Spiele / Quiz',
        'comedy': '🎭 Comedy / Show'
    };

    const mobilliarLabels: Labels = {
        'stuehle': '🪑 Stühle',
        'tische': '📋 Tische',
        'grill': '🔥 Grill',
        'bar': '🍹 Bar'
    };

    const energieLabels: Labels = {
        'stromanschluss': '⚡ Stromanschluss',
        'generator': '⚙️ Generator',
        'verlaengerung': '🔌 Verlängerungskabel',
        'beleuchtung': '💡 Beleuchtung',
        'notstrom': '🆘 Notstrom',
        'technik': '🎚️ Technik-Anschlüsse'
    };

    // Display Unterhaltung
    const unterhaltungList: HTMLUListElement | null = document.getElementById('overview-unterhaltung') as HTMLUListElement;
    if (unterhaltungList) {
        unterhaltungList.innerHTML = '';
        if (unterhaltung.length > 0) {
            unterhaltung.forEach((item: string) => {
                const li: HTMLLIElement = document.createElement('li');
                li.textContent = unterhaltungLabels[item] || item;
                unterhaltungList.appendChild(li);
            });
        } else {
            unterhaltungList.innerHTML = '<li style="color: #999;">Keine Auswahl getroffen</li>';
        }
    }

    // Display Mobilliar
    const mobilliarList: HTMLUListElement | null = document.getElementById('overview-mobilliar') as HTMLUListElement;
    if (mobilliarList) {
        mobilliarList.innerHTML = '';
        if (mobilliar.length > 0) {
            mobilliar.forEach((item: string) => {
                const li: HTMLLIElement = document.createElement('li');
                li.textContent = mobilliarLabels[item] || item;
                mobilliarList.appendChild(li);
            });
        } else {
            mobilliarList.innerHTML = '<li style="color: #999;">Keine Auswahl getroffen</li>';
        }
    }

    // Display Energie
    const energieList: HTMLUListElement | null = document.getElementById('overview-energie') as HTMLUListElement;
    if (energieList) {
        energieList.innerHTML = '';
        if (energie.length > 0) {
            energie.forEach((item: string) => {
                const li: HTMLLIElement = document.createElement('li');
                li.textContent = energieLabels[item] || item;
                energieList.appendChild(li);
            });
        } else {
            energieList.innerHTML = '<li style="color: #999;">Keine Auswahl getroffen</li>';
        }
    }

    // Display Menü
    const menuText: HTMLParagraphElement | null = document.getElementById('overview-menue') as HTMLParagraphElement;
    if (menuText) {
        menuText.textContent = menue ? '📄 Menü Nummer: ' + menue : '❌ Kein Menü gewählt';
    }

    // Display Termin
    const terminText: HTMLParagraphElement | null = document.getElementById('overview-termin') as HTMLParagraphElement;
    if (terminText) {
        let terminDisplay: string = '';
        
        if (date && time) {
            terminDisplay = `📅 ${date} um ${time}`;
            if (endTime) {
                terminDisplay += ` - ${endTime}`;
            }
        } else {
            terminDisplay = '❌ Kein Termin festgelegt';
        }
        
        if (notes) {
            terminDisplay += `\n📝 Notizen: ${notes}`;
        }

        terminText.innerHTML = terminDisplay.replace(/\n/g, '<br>');
    }

    console.log('Overview displayed with data:', {
        unterhaltung,
        mobilliar,
        energie,
        menue,
        date,
        time,
        endTime,
        notes
    });
}

// ============================================
// INITIALIZE ON PAGE LOAD
// ============================================

document.addEventListener('DOMContentLoaded', function (): void {
    console.log('DOM loaded, initializing...');
    initializeEventListeners();
});

// Also run on page load in case DOMContentLoaded already fired
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeEventListeners);
} else {
    initializeEventListeners();
}