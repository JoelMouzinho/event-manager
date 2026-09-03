import express from "express";
import path from "path";

const app = express();
const PORT = 3000;

// Absoluter Pfad zum public Ordner
const publicPath = path.join(__dirname, "public");

// Statische Dateien freigeben
app.use(express.static(publicPath));

/*
|--------------------------------------------------------------------------
| ROUTES
|--------------------------------------------------------------------------
*/

// Startseite
app.get("/", (req, res) => {
    res.sendFile(path.join(publicPath, "home", "index.html"));
});

// Events
app.get("/events", (req, res) => {
    res.sendFile(path.join(publicPath, "events", "index.html"));
});

// Favorites
app.get("/favorites", (req, res) => {
    res.sendFile(path.join(publicPath, "favorites", "index.html"));
});

// Calendar
app.get("/calendar", (req, res) => {
    res.sendFile(path.join(publicPath, "calendar", "index.html"));
});

// Tickets
app.get("/tickets", (req, res) => {
    res.sendFile(path.join(publicPath, "tickets", "index.html"));
});

// Profile
app.get("/profile", (req, res) => {
    res.sendFile(path.join(publicPath, "profile", "index.html"));
});

/*
|--------------------------------------------------------------------------
| SERVER START
|--------------------------------------------------------------------------
*/

app.listen(PORT, () => {
    console.log(`✅ Server läuft auf: http://localhost:${PORT}`);
});