(function () {
    window.toggleImageUpload = function (type) {
        const imageRow = document.getElementById("image-upload-row");
        if (!imageRow) return;

        const shouldShow = String(type) === "4";
        imageRow.hidden = !shouldShow;
        imageRow.style.display = shouldShow ? "block" : "none";
        if (!shouldShow) {
            const input = imageRow.querySelector("input[type='file']");
            if (input) input.value = "";
        }
    };

    function showFormMessage(form, message, isError) {
        const status = form.querySelector("[data-forum-form-status]");
        if (!status) return;

        status.textContent = message;
        status.hidden = false;
        status.classList.toggle("is-error", Boolean(isError));
        status.classList.toggle("is-success", !isError);
        status.setAttribute("role", isError ? "alert" : "status");
    }

    function clearFormErrors(form) {
        form.querySelectorAll(".is-invalid").forEach(function (field) {
            field.classList.remove("is-invalid");
            field.removeAttribute("aria-invalid");
        });
        form.querySelectorAll("[data-forum-field-error]").forEach(function (error) {
            error.textContent = "";
            error.hidden = true;
        });

        const status = form.querySelector("[data-forum-form-status]");
        if (status) status.hidden = true;
    }

    function renderFormErrors(form, response) {
        const errors = response && response.errors && typeof response.errors === "object"
            ? response.errors
            : {};
        let firstInvalidField = null;

        Object.keys(errors).forEach(function (name) {
            const field = Array.from(form.elements).find(function (element) {
                return element.name === name || element.name === name.replace(/\.\d+$/, "[]");
            });
            if (!field) return;

            const message = Array.isArray(errors[name]) ? errors[name][0] : errors[name];
            const visibleField = field.id === "editor1"
                ? (form.querySelector(".ql-editor[contenteditable='true']") || field)
                : field;
            visibleField.classList.add("is-invalid");
            visibleField.setAttribute("aria-invalid", "true");
            firstInvalidField = firstInvalidField || visibleField;

            let error = field.closest(".superdesign-field-group")?.querySelector("[data-forum-field-error]");
            if (!error) {
                error = document.createElement("span");
                error.className = "forum-ajax-field-error";
                error.setAttribute("data-forum-field-error", "");
                error.setAttribute("role", "alert");
                field.closest(".superdesign-field-group")?.appendChild(error);
            }
            if (error) {
                error.textContent = String(message || "");
                error.hidden = !message;
            }
        });

        if (firstInvalidField) {
            firstInvalidField.focus({ preventScroll: true });
            firstInvalidField.scrollIntoView({ behavior: "smooth", block: "center" });
        }

        const firstMessage = Object.values(errors).flat()[0] || response?.message || form.dataset.requestError;
        showFormMessage(form, String(firstMessage), true);
    }

    document.addEventListener("submit", async function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute("data-forum-ajax")) return;

        event.preventDefault();
        if (form.dataset.saving === "1") return;

        clearFormErrors(form);
        form.dataset.saving = "1";
        form.setAttribute("aria-busy", "true");

        const submitButtons = Array.from(form.querySelectorAll("[type='submit']"));
        const previousLabels = submitButtons.map(function (button) {
            return button.textContent;
        });
        submitButtons.forEach(function (button) {
            button.disabled = true;
            button.classList.add("is-loading");
        });

        try {
            const response = await fetch(form.action, {
                method: (form.method || "POST").toUpperCase(),
                body: new FormData(form),
                credentials: "same-origin",
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": form.querySelector("input[name='_token']")?.value || ""
                }
            });
            const payload = await response.json().catch(function () { return {}; });

            if (!response.ok) {
                renderFormErrors(form, payload);
                return;
            }

            showFormMessage(form, payload.message || form.dataset.successMessage, false);
            const destination = payload.url || form.dataset.successUrl;
            if (destination) window.setTimeout(function () { window.location.assign(destination); }, 350);
        } catch (error) {
            showFormMessage(form, form.dataset.requestError, true);
        } finally {
            form.dataset.saving = "0";
            form.removeAttribute("aria-busy");
            submitButtons.forEach(function (button, index) {
                button.disabled = false;
                button.classList.remove("is-loading");
                if (!button.textContent.trim()) button.textContent = previousLabels[index];
            });
        }
    });

    document.addEventListener("input", function (event) {
        const search = event.target.closest("[data-forum-category-search]");
        if (!search) return;

        const query = search.value.trim().toLocaleLowerCase();
        const cards = Array.from(document.querySelectorAll("[data-forum-category-item]"));
        let visibleCount = 0;

        cards.forEach(function (card) {
            const matches = !query || (card.dataset.searchText || "").toLocaleLowerCase().includes(query);
            card.hidden = !matches;
            visibleCount += matches ? 1 : 0;
        });

        const empty = document.querySelector("[data-forum-category-empty]");
        if (empty) empty.hidden = visibleCount > 0 || cards.length === 0;
    });

    document.addEventListener("change", function (event) {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !input.matches("[data-forum-attachments]")) return;

        const label = input.closest(".superdesign-field-group")?.querySelector("[data-forum-attachment-name]");
        if (!label) return;

        if (input.files.length) {
            label.textContent = label.dataset.selectedFiles.replace(":count", String(input.files.length));
        } else {
            label.textContent = label.dataset.emptyLabel;
        }
    });

    function dispatchInput(textarea) {
        textarea.dispatchEvent(new Event("input", { bubbles: true }));
    }

    function autoResize(textarea) {
        textarea.style.height = "auto";
        textarea.style.height = Math.min(textarea.scrollHeight, 280) + "px";
    }

    function wrapSelection(textarea, prefix, suffix, placeholder) {
        const start = textarea.selectionStart ?? 0;
        const end = textarea.selectionEnd ?? 0;
        const selected = textarea.value.slice(start, end);
        const content = selected || placeholder;
        const replacement = prefix + content + suffix;

        textarea.setRangeText(replacement, start, end, "end");

        if (!selected) {
            const cursorStart = start + prefix.length;
            const cursorEnd = cursorStart + placeholder.length;
            textarea.setSelectionRange(cursorStart, cursorEnd);
        }

        dispatchInput(textarea);
        textarea.focus();
    }

    function insertText(textarea, text) {
        const start = textarea.selectionStart ?? 0;
        const end = textarea.selectionEnd ?? 0;
        textarea.setRangeText(text, start, end, "end");
        dispatchInput(textarea);
        textarea.focus();
    }

    function resolveEditor(button) {
        const targetId = button.getAttribute("data-target");
        if (!targetId) {
            return null;
        }

        return document.getElementById(targetId);
    }

    function handleToolbarAction(button) {
        const textarea = resolveEditor(button);
        if (!textarea) {
            return;
        }

        const action = button.getAttribute("data-md-action");
        switch (action) {
            case "bold":
                wrapSelection(textarea, "**", "**", "bold text");
                break;
            case "italic":
                wrapSelection(textarea, "_", "_", "italic text");
                break;
            case "quote":
                wrapSelection(textarea, "\n> ", "", "quoted text");
                break;
            case "code":
                wrapSelection(textarea, "`", "`", "code");
                break;
            case "link": {
                const url = window.prompt(button.getAttribute("data-link-prompt") || "Paste URL");
                if (!url) {
                    return;
                }

                const label = window.prompt(
                    button.getAttribute("data-link-label-prompt") || "Link text",
                    button.getAttribute("data-link-default-label") || "link"
                ) || (button.getAttribute("data-link-default-label") || "link");

                insertText(textarea, "[" + label + "](" + url + ")");
                break;
            }
            case "emoji":
                insertText(textarea, " 😊 ");
                break;
            default:
                break;
        }
    }

    function setCommentLoadingState(button) {
        button.classList.add("is-loading");
        button.disabled = true;

        window.setTimeout(function () {
            if (document.body.contains(button)) {
                button.classList.remove("is-loading");
                button.disabled = false;
            }
        }, 5000);
    }

    document.addEventListener("click", function (event) {
        const toolButton = event.target.closest("[data-md-action]");
        if (toolButton) {
            event.preventDefault();
            handleToolbarAction(toolButton);
            return;
        }

        const submitButton = event.target.closest("[data-comment-submit]");
        if (submitButton) {
            setCommentLoadingState(submitButton);
        }
    });

    document.addEventListener("input", function (event) {
        const textarea = event.target;
        if (!(textarea instanceof HTMLTextAreaElement)) {
            return;
        }

        if (textarea.matches("textarea[data-md-editor='1']")) {
            autoResize(textarea);
        }
    });

    document.addEventListener("focusin", function (event) {
        const textarea = event.target;
        if (!(textarea instanceof HTMLTextAreaElement)) {
            return;
        }

        if (textarea.matches("textarea[data-md-editor='1']")) {
            autoResize(textarea);
        }
    });
})();
