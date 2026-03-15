(function () {
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
