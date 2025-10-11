# 🌍 Sustainable Travel Agent

A modern Laravel + Inertia.js + Vue 3 web application for a sustainable travel organization promoting **European train journeys** and **short cultural trips**.  
Built for performance, maintainability, and a great user experience — powered by a fully automated CI/CD pipeline via **GitHub Actions** and **SFTP deployment**.

---

## ✨ Project Vision

Our mission is to make **slow travel** the new standard for short trips within Europe.  
By offering curated, culturally rich train-based journeys, we aim to inspire travelers to explore Europe responsibly — with minimal carbon footprint and maximum authenticity.

---

## 🧭 Key Features

- 🧳 **Travel Packages** — Browse curated European train journeys with highlights, duration, and sustainability details.  
- 💌 **Newsletter System** — Subscribe, confirm, or unsubscribe via secure expiring links.  
- 🚆 **Eco-friendly Focus** — All trips are designed around train travel and local culture.  
- 🧾 **Booking Forms with Validation** — Dynamic form handling powered by Inertia.js and Vue 3.  
- 🪄 **Fully Responsive UI** — Tailwind CSS styling with a custom color palette and breakpoints.  
- ⚙️ **Automated Deployment** — GitHub Actions build & deploy pipeline via SFTP (no composer/npm required on server).  
- 📬 **Mailjet Integration** — Transactional and marketing email delivery via Mailjet API.

---

## 🧱 Tech Stack

| Layer | Technology |
|-------|-------------|
| **Backend** | Laravel 12 (PHP 8.4) |
| **Frontend** | Inertia.js + Vue 3 |
| **Styling** | Tailwind CSS v3.4 (with custom theme & breakpoints) |
| **Email** | Mailjet (via `mailjet/laravel-mailjet`) |
| **CI/CD** | GitHub Actions |
| **Deployment** | SFTP (bare-minimum Debian server, no Composer/NPM installed) |
| **Build Tools** | Vite |

---

## 🧩 Tailwind Theme
Custom color palette and breakpoints:
```
// tailwind.config.js
colors: {
  primary: { default: '#2F3E46', dark: '#1B3A4B' },
  secondary: { sage: '#AFCB98', stone: '#A3BCCB' },
  accent: { earth: '#DCC7AA', gold: '#D4A017', terracotta: '#B17C65' },
  neutral: { 50: '#F2F4F3' },
  status: { error: '#C5534A', success: '#6B8E5A', warning: '#D4A017' },
},
screens: {
  phone: '0px',
  tablet: '600px',
  laptop: '900px',
  desktop: '1200px',
  wide: '1800px'
}
```
## 🧠 Architecture Highlights
* Inertia.js Bridge: Seamlessly connects Laravel routes with Vue 3 components.
* Form Requests & DTOs: Typed validation and clean data transformation using Carbon dates.
* Reusable Components: Cards, Buttons, and Layouts follow Tailwind utility patterns.
* Email Templates: Clean HTML templates with inline logos and responsive headers.

## 🔒 Security & Best Practices
* Secure form validation on both client and server sides
* Expiring confirmation/unsubscribe links for newsletters
* Environment-specific configuration separation
* No secrets stored in the repository (all handled via GitHub Secrets)

## 🪪 License
This project is licensed under the MIT License — see the [LICENSE](https://mit-license.org/)

## 💚 Built with passion for sustainable travel and webdevelopment
“The journey matters more than the destination — especially when it’s by train.”
