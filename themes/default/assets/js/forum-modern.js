(function () {
    "use strict";

    // Forum Markdown Toolbar & Shortcode Injector
    window.ForumEditor = {
        wrap: function (textarea, prefix, suffix, defaultText) {
            if (!textarea) return;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const sel = textarea.value.substring(start, end) || (defaultText || "");
            const replacement = prefix + sel + suffix;
            textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
            textarea.focus();
            textarea.setSelectionRange(start + prefix.length, start + prefix.length + sel.length);
            textarea.dispatchEvent(new Event("input", { bubbles: true }));
        },

        insert: function (textarea, text) {
            if (!textarea) return;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            textarea.value = textarea.value.substring(0, start) + text + textarea.value.substring(end);
            textarea.focus();
            textarea.setSelectionRange(start + text.length, start + text.length);
            textarea.dispatchEvent(new Event("input", { bubbles: true }));
        },

        // Fast Client-side Markdown to HTML renderer for instant live preview
        renderMarkdown: function (md) {
            if (!md) return "";
            let html = md
                // Escape HTML tags to prevent XSS
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                // Code blocks ```code```
                .replace(/```([a-z0-9_-]*)\n([\s\S]*?)```/gim, function (match, lang, code) {
                    return '<pre class="forum-modern-code-block"><code class="language-' + (lang || "plaintext") + '">' + code.trim() + "</code></pre>";
                })
                // Inline code `code`
                .replace(/`([^`]+)`/gim, '<code class="forum-modern-inline-code">$1</code>')
                // Headers # H1, ## H2, ### H3
                .replace(/^### (.*$)/gim, "<h3>$1</h3>")
                .replace(/^## (.*$)/gim, "<h2>$1</h2>")
                .replace(/^# (.*$)/gim, "<h1>$1</h1>")
                // Bold **text**
                .replace(/\*\*(.*?)\*\*/gim, "<strong>$1</strong>")
                // Italic *text*
                .replace(/\*(.*?)\*/gim, "<em>$1</em>")
                // Strikethrough ~~text~~
                .replace(/~~(.*?)~~/gim, "<del>$1</del>")
                // Blockquote > text
                .replace(/^\> (.*$)/gim, "<blockquote>$1</blockquote>")
                // Links [text](url)
                .replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>')
                // Images ![alt](url)
                .replace(/!\[([^\]]*)\]\(([^)]+)\)/gim, '<img src="$2" alt="$1" style="max-width:100%;border-radius:10px;">')
                // Unordered lists - item
                .replace(/^\s*\-\s(.*$)/gim, "<ul><li>$1</li></ul>")
                // Fix adjoining ul tags
                .replace(/<\/ul>\s*<ul>/gim, "")
                // Line breaks
                .replace(/\n/gim, "<br>");
            return html;
        }
    };

    // Forum Floating Composer Drawer Management
    window.ForumComposer = {
        drawer: null,

        init: function () {
            this.drawer = document.getElementById("forumComposer");
            if (!this.drawer) return;

            const textarea = document.getElementById("forumComposerText");
            const previewBox = document.getElementById("forumComposerPreview");

            if (textarea && previewBox) {
                textarea.addEventListener("input", function () {
                    if (previewBox.classList.contains("is-active")) {
                        previewBox.innerHTML = window.ForumEditor.renderMarkdown(textarea.value);
                    }
                });
            }
        },

        open: function (type, options) {
            if (!this.drawer) this.init();
            if (!this.drawer) return;

            this.drawer.classList.remove("is-minimized");
            this.drawer.classList.add("is-open");

            if (options && options.title) {
                const titleInput = document.getElementById("forumComposerTitle");
                if (titleInput) titleInput.value = options.title;
            }
            if (options && options.catId) {
                const catSelect = document.getElementById("forumComposerCategory");
                if (catSelect) catSelect.value = options.catId;
            }
            if (options && options.replyToUser) {
                const textarea = document.getElementById("forumComposerText");
                if (textarea) {
                    window.ForumEditor.insert(textarea, "@" + options.replyToUser + " ");
                }
            }

            const inputToFocus = document.getElementById("forumComposerTitle") || document.getElementById("forumComposerText");
            if (inputToFocus) {
                setTimeout(function () { inputToFocus.focus(); }, 150);
            }
        },

        close: function () {
            if (!this.drawer) return;
            this.drawer.classList.remove("is-open", "is-fullscreen", "is-minimized");
        },

        minimize: function () {
            if (!this.drawer) return;
            this.drawer.classList.toggle("is-minimized");
        },

        toggleFullscreen: function () {
            if (!this.drawer) return;
            this.drawer.classList.toggle("is-fullscreen");
        },

        togglePreview: function () {
            const previewBox = document.getElementById("forumComposerPreview");
            const textarea = document.getElementById("forumComposerText");
            if (!previewBox || !textarea) return;

            const isActive = previewBox.classList.toggle("is-active");
            if (isActive) {
                previewBox.innerHTML = window.ForumEditor.renderMarkdown(textarea.value) || '<p class="text-muted fst-italic">Nothing to preview yet...</p>';
            }
        }
    };

    // Initialize on DOM ready
    document.addEventListener("DOMContentLoaded", function () {
        window.ForumComposer.init();

        // Delegate comment & form markdown actions
        document.addEventListener("click", function (e) {
            const btn = e.target.closest("[data-md-action]");
            if (btn) {
                const action = btn.getAttribute("data-md-action");
                const targetId = btn.getAttribute("data-target");
                const textarea = document.getElementById(targetId);
                if (!textarea) return;

                e.preventDefault();
                switch (action) {
                    case "bold":
                        window.ForumEditor.wrap(textarea, "**", "**", "bold text");
                        break;
                    case "italic":
                        window.ForumEditor.wrap(textarea, "*", "*", "italic text");
                        break;
                    case "strike":
                        window.ForumEditor.wrap(textarea, "~~", "~~", "strikethrough");
                        break;
                    case "quote":
                        window.ForumEditor.wrap(textarea, "> ", "", "quote");
                        break;
                    case "code":
                        window.ForumEditor.wrap(textarea, "`", "`", "code");
                        break;
                    case "heading":
                        window.ForumEditor.wrap(textarea, "### ", "", "Heading");
                        break;
                    case "list":
                        window.ForumEditor.wrap(textarea, "- ", "", "list item");
                        break;
                    case "link":
                        const url = prompt(btn.getAttribute("data-link-prompt") || "Enter URL:", "https://");
                        if (url) {
                            const label = prompt(btn.getAttribute("data-link-label-prompt") || "Link text:", btn.getAttribute("data-link-default-label") || "Link");
                            window.ForumEditor.insert(textarea, "[" + (label || url) + "](" + url + ")");
                        }
                        break;
                    case "emoji":
                        const emojis = ["😊", "👍", "❤️", "🔥", "🎉", "💡", "🚀", "😂", "✨", "👏"];
                        const picked = emojis[Math.floor(Math.random() * emojis.length)];
                        window.ForumEditor.insert(textarea, " " + picked + " ");
                        break;
                }
            }

            // Comment Live Preview Toggle
            const previewBtn = e.target.closest(".forum-comment-preview-btn");
            if (previewBtn) {
                e.preventDefault();
                const targetId = previewBtn.getAttribute("data-target");
                const previewId = previewBtn.getAttribute("data-preview");
                const textarea = document.getElementById(targetId);
                const previewDiv = document.getElementById(previewId);

                if (textarea && previewDiv) {
                    const isVisible = previewDiv.style.display !== "none";
                    if (isVisible) {
                        previewDiv.style.display = "none";
                        previewBtn.classList.remove("text-primary");
                    } else {
                        previewDiv.style.display = "block";
                        previewBtn.classList.add("text-primary");
                        previewDiv.innerHTML = window.ForumEditor.renderMarkdown(textarea.value) || '<span class="text-muted fst-italic">Type something to preview...</span>';
                    }

                    if (!textarea.dataset.previewBound) {
                        textarea.dataset.previewBound = "1";
                        textarea.addEventListener("input", function () {
                            if (previewDiv.style.display !== "none") {
                                previewDiv.innerHTML = window.ForumEditor.renderMarkdown(textarea.value) || '<span class="text-muted fst-italic">Type something to preview...</span>';
                            }
                        });
                    }
                }
            }
        });
    });

})();
