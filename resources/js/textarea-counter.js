/**
 * Textarea Counter - Menampilkan indikator jumlah karakter
 * Format: X/MAX
 */

function initTextareaCounters() {
    // Fungsi untuk update counter
    function updateTextareaCounter(textarea) {
        let counter = textarea.nextElementSibling;

        // Jika belum ada counter, buat baru
        if (!counter || !counter.classList.contains("textarea-counter")) {
            counter = document.createElement("div");
            counter.className = "textarea-counter";
            textarea.insertAdjacentElement("afterend", counter);
        }

        const max = parseInt(textarea.getAttribute("maxlength") || "0", 10);
        const current = textarea.value.length;

        // Update text dan styling
        counter.textContent = `${current}/${max}`;

        // Styling berdasarkan status
        if (current === 0) {
            counter.className =
                "textarea-counter text-[11px] text-gray-400 font-semibold text-right mt-1.5 tracking-wide";
        } else if (current < max * 0.75) {
            counter.className =
                "textarea-counter text-[11px] text-green-600 font-semibold text-right mt-1.5 tracking-wide";
        } else if (current < max) {
            counter.className =
                "textarea-counter text-[11px] text-amber-600 font-semibold text-right mt-1.5 tracking-wide";
        } else {
            counter.className =
                "textarea-counter text-[11px] text-red-600 font-semibold text-right mt-1.5 tracking-wide";
        }
    }

    // Inisialisasi semua textarea dengan maxlength
    document.querySelectorAll("textarea[maxlength]").forEach((textarea) => {
        // Create counter if not exists
        let counter = textarea.nextElementSibling;
        if (!counter || !counter.classList.contains("textarea-counter")) {
            counter = document.createElement("div");
            counter.className = "textarea-counter";
            textarea.insertAdjacentElement("afterend", counter);
        }

        // Event listeners
        textarea.addEventListener("input", () =>
            updateTextareaCounter(textarea),
        );
        textarea.addEventListener("change", () =>
            updateTextareaCounter(textarea),
        );
        textarea.addEventListener("textarea-counter:update", () =>
            updateTextareaCounter(textarea),
        );

        // Initial update
        updateTextareaCounter(textarea);
    });

    // Observer untuk textarea yang ditambahkan secara dinamis
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    // Element node
                    const textareas = node.querySelectorAll
                        ? node.querySelectorAll("textarea[maxlength]")
                        : [];
                    if (
                        node.tagName === "TEXTAREA" &&
                        node.hasAttribute("maxlength")
                    ) {
                        textareas.push(node);
                    }
                    textareas.forEach((textarea) => {
                        if (
                            !textarea.nextElementSibling?.classList?.contains(
                                "textarea-counter",
                            )
                        ) {
                            const counter = document.createElement("div");
                            counter.className = "textarea-counter";
                            textarea.insertAdjacentElement("afterend", counter);
                            textarea.addEventListener("input", () =>
                                updateTextareaCounter(textarea),
                            );
                            updateTextareaCounter(textarea);
                        }
                    });
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

// Jalankan saat DOM ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initTextareaCounters);
} else {
    initTextareaCounters();
}
