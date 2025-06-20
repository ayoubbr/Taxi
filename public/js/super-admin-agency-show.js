// Variables globales
let deleteModal = null

// Initialisation au chargement de la page
document.addEventListener("DOMContentLoaded", () => {
    initializeTooltips()
    initializeAnimations()
    initializeAutoRefresh()

    // Confirm status toggle
    const statusForms = document.querySelectorAll('.inline-form');
    statusForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const button = this.querySelector('button');
            const action = button.textContent.trim();

            if (confirm(
                `Êtes-vous sûr de vouloir ${action.toLowerCase()} cette agence ?`)) {
                this.submit();
            }
        });
    });
})

// Initialisation des tooltips
function initializeTooltips() {
    const statCards = document.querySelectorAll(".stat-card")

    statCards.forEach((card) => {
        card.addEventListener("mouseenter", function () {
            const icon = this.querySelector(".stat-icon")
            const label = this.querySelector(".stat-label").textContent

            // Ajouter un effet de pulsation
            icon.style.animation = "pulse 1s infinite"
        })

        card.addEventListener("mouseleave", function () {
            const icon = this.querySelector(".stat-icon")
            icon.style.animation = ""
        })
    })
}

// Initialisation des animations
function initializeAnimations() {
    // Animation d'apparition des cartes
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = "1"
                entry.target.style.transform = "translateY(0)"
            }
        })
    }, observerOptions)

    // Observer les éléments à animer
    const elementsToAnimate = document.querySelectorAll(".stat-card, .detail-section")
    elementsToAnimate.forEach((el) => {
        el.style.opacity = "0"
        el.style.transform = "translateY(20px)"
        el.style.transition = "opacity 0.6s ease, transform 0.6s ease"
        observer.observe(el)
    })
}

// Auto-refresh des données
function initializeAutoRefresh() {
    // Refresh toutes les 30 secondes
    setInterval(() => {
        refreshStats()
    }, 30000)
}

// Fonction pour rafraîchir les statistiques
function refreshStats() {
    const currentUrl = window.location.href

    fetch(currentUrl, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((response) => response.text())
        .then((html) => {
            // Parser la réponse HTML
            const parser = new DOMParser()
            const doc = parser.parseFromString(html, "text/html")

            // Mettre à jour les statistiques
            updateStatsFromResponse(doc)

            // Afficher une notification de mise à jour
            showUpdateNotification()
        })
        .catch((error) => {
            console.error("Erreur lors du rafraîchissement:", error)
        })
}

// Mettre à jour les statistiques depuis la réponse
function updateStatsFromResponse(doc) {
    const currentStats = document.querySelectorAll(".stat-number")
    const newStats = doc.querySelectorAll(".stat-number")

    currentStats.forEach((stat, index) => {
        if (newStats[index]) {
            const currentValue = stat.textContent
            const newValue = newStats[index].textContent

            if (currentValue !== newValue) {
                // Animation de changement
                stat.style.transform = "scale(1.1)"
                stat.style.color = "var(--success-color)"

                setTimeout(() => {
                    stat.textContent = newValue
                    stat.style.transform = "scale(1)"
                    stat.style.color = ""
                }, 200)
            }
        }
    })
}

// Afficher une notification de mise à jour
function showUpdateNotification() {
    const notification = document.createElement("div")
    notification.className = "update-notification"
    notification.innerHTML = `
        <i class="fas fa-sync-alt"></i>
        <span>Données mises à jour</span>
    `

    // Styles de la notification
    Object.assign(notification.style, {
        position: "fixed",
        top: "20px",
        right: "20px",
        background: "var(--success-color)",
        color: "white",
        padding: "12px 20px",
        borderRadius: "8px",
        boxShadow: "var(--shadow-lg)",
        zIndex: "1000",
        display: "flex",
        alignItems: "center",
        gap: "8px",
        fontSize: "14px",
        fontWeight: "500",
        opacity: "0",
        transform: "translateX(100%)",
        transition: "all 0.3s ease",
    })

    document.body.appendChild(notification)

    // Animation d'apparition
    setTimeout(() => {
        notification.style.opacity = "1"
        notification.style.transform = "translateX(0)"
    }, 100)

    // Suppression automatique
    setTimeout(() => {
        notification.style.opacity = "0"
        notification.style.transform = "translateX(100%)"

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification)
            }
        }, 300)
    }, 3000)
}

// Fonction pour filtrer les tableaux
function filterTable(input, tableId) {
    const filter = input.value.toLowerCase()
    const table = document.getElementById(tableId)
    const rows = table.getElementsByTagName("tr")

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i]
        const cells = row.getElementsByTagName("td")
        let found = false

        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j]
            if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                found = true
                break
            }
        }

        row.style.display = found ? "" : "none"
    }
}

// Fonction pour exporter les données
function exportData(format) {
    const agencyId = window.location.pathname.split("/").pop()
    const exportUrl = `/super-admin/agencies/${agencyId}/export?format=${format}`

    // Créer un lien de téléchargement
    const link = document.createElement("a")
    link.href = exportUrl
    link.download = `agency-${agencyId}-data.${format}`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

// Gestion des erreurs globales
window.addEventListener("error", (event) => {
    console.error("Erreur JavaScript:", event.error)
})

// Animation de pulsation pour les icônes
const pulseKeyframes = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`

// Ajouter les keyframes au document
const style = document.createElement("style")
style.textContent = pulseKeyframes
document.head.appendChild(style)

// Fonction utilitaire pour formater les nombres
function formatNumber(num) {
    return new Intl.NumberFormat("fr-FR").format(num)
}

// Fonction utilitaire pour formater la monnaie
function formatCurrency(amount) {
    return new Intl.NumberFormat("fr-FR", {
        style: "currency",
        currency: "MAD",
    }).format(amount)
}

// Fonction pour copier les informations de l'agence
function copyAgencyInfo() {
    const agencyName = document.querySelector(".agency-details h1").textContent
    const agencyAddress = document.querySelector(".agency-address span")?.textContent || "Adresse non renseignée"
    const agencyStatus = document.querySelector(".status-badge").textContent.trim()

    const info = `
Agence: ${agencyName}
Statut: ${agencyStatus}
Adresse: ${agencyAddress}
    `.trim()

    navigator.clipboard.writeText(info).then(() => {
        showUpdateNotification("Informations copiées dans le presse-papiers")
    })
}

console.log("Script Super Admin Agency Show chargé avec succès")
