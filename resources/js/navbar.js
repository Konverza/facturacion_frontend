document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    if (!sidebar) {
        return;
    }

    const searchInput = document.getElementById("sidebar-search");
    const panels = Array.from(sidebar.querySelectorAll("[data-menu-panel]"));
    const triggers = Array.from(sidebar.querySelectorAll("[data-accordion-trigger]"));
    const menuItems = Array.from(sidebar.querySelectorAll("[data-menu-item]"));
    const menuLinks = Array.from(sidebar.querySelectorAll("[data-menu-text]"));

    const initialOpenPanels = new Set(
        panels.filter((panel) => !panel.classList.contains("hidden")).map((panel) => panel.id)
    );

    const normalize = (value) =>
        value
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");

    const setPanelState = (panelId, open) => {
        const panel = document.getElementById(panelId);
        if (!panel) {
            return;
        }
        panel.classList.toggle("hidden", !open);
        const trigger = sidebar.querySelector(
            `[data-accordion-trigger="${panelId}"]`
        );
        if (trigger) {
            trigger.setAttribute("aria-expanded", open ? "true" : "false");
        }
    };

    const closeAllPanels = (exceptId = null) => {
        panels.forEach((panel) => {
            const open = exceptId === panel.id;
            setPanelState(panel.id, open);
        });
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener("click", () => {
            const panelId = trigger.getAttribute("data-accordion-trigger");
            if (!panelId) {
                return;
            }
            const panel = document.getElementById(panelId);
            if (!panel) {
                return;
            }
            const isOpen = !panel.classList.contains("hidden");
            if (isOpen) {
                setPanelState(panelId, false);
            } else {
                closeAllPanels(panelId);
                setPanelState(panelId, true);
            }
        });
    });

    const resetSearch = () => {
        menuItems.forEach((item) => item.classList.remove("hidden"));
        panels.forEach((panel) => {
            const open = initialOpenPanels.has(panel.id);
            setPanelState(panel.id, open);
        });
    };

    const applySearch = (value) => {
        const query = normalize(value.trim());
        if (!query) {
            resetSearch();
            return;
        }

        menuItems.forEach((item) => item.classList.add("hidden"));
        panels.forEach((panel) => panel.classList.add("hidden"));

        const panelMatches = new Set();

        menuLinks.forEach((link) => {
            const text = link.dataset.menuText || link.textContent || "";
            const keywords = link.dataset.menuKeywords || "";
            const haystack = normalize(`${text} ${keywords}`);

            if (!haystack.includes(query)) {
                return;
            }

            const item = link.closest("[data-menu-item]");
            if (item) {
                item.classList.remove("hidden");
            }

            const panel = link.closest("[data-menu-panel]");
            if (panel) {
                panelMatches.add(panel.id);
                panel.classList.remove("hidden");
                const parentTrigger = sidebar.querySelector(
                    `[data-panel-id="${panel.id}"]`
                );
                const parentItem = parentTrigger?.closest("[data-menu-item]");
                if (parentItem) {
                    parentItem.classList.remove("hidden");
                }
            }
        });

        panels.forEach((panel) => {
            const open = panelMatches.has(panel.id);
            setPanelState(panel.id, open);
        });
    };

    if (searchInput) {
        searchInput.addEventListener("input", (event) => {
            applySearch(event.target.value);
        });
        searchInput.addEventListener("search", (event) => {
            applySearch(event.target.value);
        });
    }
});
