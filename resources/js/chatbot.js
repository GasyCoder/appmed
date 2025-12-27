class Chatbot {
    constructor() {
        const isAuth = document.querySelector('meta[name="chatbot-auth"]')?.content === '1';
        if (!isAuth) return;

        if (window.__epircChatbotInitialized) return;
        window.__epircChatbotInitialized = true;

        this.isOpen = false;
        this.sessionId = this.getOrCreateSessionId();
        this.messages = [];
        this.isLoading = false;

        this.init();
    }

    getOrCreateSessionId() {
        let sessionId = localStorage.getItem('chat_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).slice(2, 11);
            localStorage.setItem('chat_session_id', sessionId);
        }
        return sessionId;
    }

    init() {
        this.injectShell();
        this.cacheEls();

        this.applySafeTop();
        this.applyDockBottom();
        this.applyDockRight();

        let raf = null;
        const schedule = () => {
            if (raf) return;
            raf = requestAnimationFrame(() => {
                raf = null;
                this.applySafeTop();
                this.applyDockBottom();
                this.applyDockRight();
            });
        };

        window.addEventListener('resize', schedule, { passive: true });
        window.addEventListener('scroll', schedule, { passive: true });

        this.attachEvents();
        this.loadHistory();
        window.chatbot = this;
    }

    cacheEls() {
        this.el = {
            window: document.getElementById('chat-window'),
            toggleBtn: document.getElementById('chat-toggle-btn'),
            closeBtn: document.getElementById('close-chat-btn'),
            clearBtn: document.getElementById('clear-chat-btn'),
            form: document.getElementById('chat-form'),
            input: document.getElementById('chat-input'),
            sendBtn: document.getElementById('send-btn'),
            messages: document.getElementById('chat-messages'),
        };
    }

    applySafeTop() {
        const root = document.documentElement;

        let headerH = 0;
        const headerEl = document.getElementById('landingHeader');
        if (headerEl) {
            headerH = Math.ceil(headerEl.getBoundingClientRect().height || 0);
        } else {
            const cssVar = getComputedStyle(root).getPropertyValue('--landing-header-h').trim();
            if (cssVar) headerH = parseInt(cssVar.replace('px', ''), 10) || 0;
        }
        if (!headerH) headerH = 64;

        root.style.setProperty('--chat-safe-top', `${headerH + 12}px`);
    }

    applyDockBottom() {
        const root = document.documentElement;

        const safeTop = parseInt(
            getComputedStyle(root).getPropertyValue('--chat-safe-top').replace('px', ''),
            10
        ) || 76;

        const isMobile = window.matchMedia('(max-width: 640px)').matches;
        const baseBottom = isMobile ? 80 : 96;
        const liftCap = isMobile ? 140 : 160;
        const margin = 16;

        let bottom = baseBottom;

        const footer = document.querySelector('footer');
        if (footer) {
            const r = footer.getBoundingClientRect();
            if (r.top < window.innerHeight && r.bottom > 0) {
                const needed = (window.innerHeight - r.top) + margin;
                const limited = Math.min(needed, baseBottom + liftCap);
                bottom = Math.max(bottom, limited);
            }
        }

        const maxBottom = Math.max(baseBottom, window.innerHeight - safeTop - 80);
        bottom = Math.min(bottom, maxBottom);

        root.style.setProperty('--chat-dock-bottom', `${bottom}px`);
    }

    applyDockRight() {
        const root = document.documentElement;
        const isMobile = window.matchMedia('(max-width: 640px)').matches;

        const fallback = isMobile ? 16 : 24;
        let right = fallback;

        const anchor =
            document.querySelector('main .max-w-\\[88rem\\]') ||
            document.querySelector('.max-w-\\[88rem\\]') ||
            document.querySelector('main .max-w-7xl') ||
            document.querySelector('main .mx-auto') ||
            null;

        if (anchor) {
            const rect = anchor.getBoundingClientRect();
            const pr = parseFloat(getComputedStyle(anchor).paddingRight) || 0;

            const desiredX = rect.right - pr;
            right = Math.round(window.innerWidth - desiredX);
            right += isMobile ? 0 : 6;

            right = Math.max(12, right);
            right = Math.min(right, Math.max(12, window.innerWidth - 72));
        }

        root.style.setProperty('--chat-dock-right', `${right}px`);
    }

    injectShell() {
        if (document.getElementById('chatbot-container')) return;

        const html = `
        <div id="chatbot-container"
             style="
               position:fixed;
               right:var(--chat-dock-right, 24px);
               bottom:var(--chat-dock-bottom, 96px);
               top:auto; left:auto;
               z-index:9999;
             ">

            <div id="chat-window"
                 class="hidden"
                 style="
                   position:fixed;
                   right:var(--chat-dock-right, 24px);
                   bottom:calc(var(--chat-dock-bottom, 96px) + 76px);
                   top:auto; left:auto;

                   width:min(30rem, calc(100vw - 2rem));
                   height:min(38rem, calc(100vh - var(--chat-safe-top, 76px) - 140px));

                   border-radius:16px;
                   border:1px solid rgba(31,41,55,.15);
                   overflow:hidden;
                   box-shadow:0 20px 60px rgba(0,0,0,.25);
                   background:transparent;
                 ">

                <div class="flex flex-col h-full bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800">
                    <div class="px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-10 w-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="3.5" r="1" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v2" />
                                        <rect x="5" y="7" width="14" height="12" rx="3" stroke-width="2"></rect>
                                        <circle cx="9" cy="13" r="1" />
                                        <circle cx="15" cy="13" r="1" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 16h4" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-sm truncate">Assistant EPIRC</div>
                                    <div class="text-xs text-white/85">Support, cours, documents</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button id="clear-chat-btn"
                                        class="p-2 hover:bg-white/15 rounded-xl transition"
                                        title="Effacer">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                <button id="close-chat-btn"
                                        class="p-2 hover:bg-white/15 rounded-xl transition"
                                        title="Fermer">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="chat-messages"
                         class="flex-1 overflow-y-auto px-4 py-4 space-y-4 bg-gray-50 dark:bg-gray-950">
                    </div>

                    <div class="p-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
                        <form id="chat-form" class="space-y-2">
                            <div class="flex items-end gap-2">
                                <textarea id="chat-input"
                                          rows="1"
                                          placeholder="Posez votre question..."
                                          maxlength="500"
                                          class="flex-1 px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700
                                                 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white
                                                 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                                 resize-none max-h-40 leading-6"></textarea>

                                <button type="submit" id="send-btn"
                                        class="px-4 py-3 rounded-2xl bg-indigo-600 text-white hover:bg-indigo-700
                                               disabled:opacity-50 disabled:cursor-not-allowed transition shrink-0">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                Entrée pour envoyer • Maj+Entrée pour nouvelle ligne
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <button id="chat-toggle-btn"
                    type="button"
                    class="relative h-16 w-16 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600
                           text-white shadow-2xl hover:shadow-indigo-500/30 transition-all
                           hover:scale-110 active:scale-95 flex items-center justify-center">
                <span class="absolute top-2 right-2 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-gray-950 pointer-events-none"></span>
                <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="3.5" r="1.1" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5.2v1.9" />
                    <rect x="5" y="7" width="14" height="12" rx="3" stroke-width="2"></rect>
                    <circle cx="9" cy="13" r="1.2" />
                    <circle cx="15" cy="13" r="1.2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.8 16h4.4" />
                </svg>
            </button>

        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', html);
    }

    attachEvents() {
        this.el.toggleBtn.addEventListener('click', () => this.toggle());
        this.el.closeBtn.addEventListener('click', () => this.toggle(false));
        this.el.clearBtn.addEventListener('click', () => this.clearChat());

        this.el.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        this.el.input.addEventListener('input', (e) => {
            e.target.style.height = 'auto';
            e.target.style.height = Math.min(e.target.scrollHeight, 160) + 'px';
        });

        this.el.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) this.toggle(false);
        });
    }

    toggle(force = null) {
        this.isOpen = force === null ? !this.isOpen : Boolean(force);

        if (this.isOpen) {
            this.el.window.classList.remove('hidden');
            setTimeout(() => {
                this.scrollToBottom();
                this.el.input.focus();
            }, 80);
        } else {
            this.el.window.classList.add('hidden');
        }
    }

    async loadHistory() {
        try {
            const res = await fetch(`/api/chatbot/history/${encodeURIComponent(this.sessionId)}`);
            const data = await res.json();
            this.messages = (data.messages && data.messages.length) ? data.messages : [this.welcomeMessage()];
            this.renderMessages();
        } catch {
            this.messages = [this.welcomeMessage()];
            this.renderMessages();
        }
    }

    welcomeMessage() {
        return {
            role: 'assistant',
            content:
                "Bonjour. Je suis l’assistant EPIRC.\n\n" +
                "Je peux aider sur :\n" +
                "- UE/EC, crédits, semestre\n" +
                "- Documents/cours\n" +
                "- Connexion / support\n\n" +
                "Posez votre question.",
            timestamp: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
        };
    }

    async sendMessage() {
        if (this.isLoading) return;

        const message = this.el.input.value.trim();
        if (!message) return;

        this.messages.push({
            role: 'user',
            content: message,
            timestamp: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
        });

        this.el.input.value = '';
        this.el.input.style.height = 'auto';
        this.renderMessages();
        this.setLoading(true);

        try {
            const res = await fetch('/api/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ message, session_id: this.sessionId })
            });

            const data = await res.json();

            this.messages.push({
                role: 'assistant',
                content: (res.ok && data.success) ? data.message : (data.message || "Erreur. Réessayez."),
                timestamp: data.timestamp || new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
            });

            this.renderMessages();
        } catch {
            this.messages.push({
                role: 'assistant',
                content: "Erreur de connexion. Vérifiez le serveur et les routes.",
                timestamp: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
            });
            this.renderMessages();
        } finally {
            this.setLoading(false);
        }
    }

    renderMessages() {
        this.el.messages.innerHTML = this.messages.map(m => this.renderMessage(m)).join('');
        this.scrollToBottom();
    }

    renderMessage(msg) {
        const isUser = msg.role === 'user';
        const align = isUser ? 'justify-end' : 'justify-start';
        const bubble = isUser
            ? 'bg-indigo-600 text-white'
            : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-800';
        const maxW = isUser ? 'max-w-[84%]' : 'max-w-[92%]';

        const contentHtml = isUser
            ? `<div class="text-sm leading-6 whitespace-pre-wrap">${this.escapeHtml(msg.content)}</div>`
            : `<div class="text-sm leading-6 space-y-2">${this.formatAssistantHtml(msg.content)}</div>`;

        return `
            <div class="flex ${align}">
                <div class="${maxW}">
                    <div class="rounded-2xl px-4 py-3 shadow-sm ${bubble}">
                        ${contentHtml}
                    </div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 ${isUser ? 'text-right' : 'text-left'}">
                        ${this.escapeHtml(msg.timestamp || '')}
                    </div>
                </div>
            </div>
        `;
    }

    setLoading(loading) {
        this.isLoading = loading;
        this.el.sendBtn.disabled = loading;

        if (loading) {
            const loadingHTML = `
                <div class="flex justify-start" id="loading-indicator">
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl px-4 py-3 shadow-sm">
                        <div class="flex items-center gap-2">
                            <div class="flex gap-1">
                                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce"></span>
                                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:0.1s"></span>
                                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:0.2s"></span>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Réponse en cours...</span>
                        </div>
                    </div>
                </div>
            `;
            this.el.messages.insertAdjacentHTML('beforeend', loadingHTML);
            this.scrollToBottom();
        } else {
            document.getElementById('loading-indicator')?.remove();
        }
    }

    async clearChat() {
        if (!confirm('Voulez-vous effacer la conversation ?')) return;

        try {
            await fetch(`/api/chatbot/clear/${encodeURIComponent(this.sessionId)}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            this.messages = [this.welcomeMessage()];
            this.renderMessages();
        } catch {
            alert("Erreur lors de la suppression.");
        }
    }

    scrollToBottom() {
        this.el.messages.scrollTop = this.el.messages.scrollHeight;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = String(text ?? '');
        return div.innerHTML;
    }

    formatAssistantHtml(text) {
        const raw = String(text ?? '').replace(/\r\n/g, '\n');

        let s = this.escapeHtml(raw);

        s = s.replace(
            /(https?:\/\/[^\s<]+)/g,
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-indigo-600 dark:text-indigo-400 underline underline-offset-2 break-words">$1</a>'
        );

        s = s.replace(
            /([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/g,
            '<a href="mailto:$1" class="text-indigo-600 dark:text-indigo-400 underline underline-offset-2">$1</a>'
        );

        s = s.replace(/\*\*(.+?)\*\*/g, '<strong class="font-semibold">$1</strong>');

        const lines = s.split('\n');

        let html = '';
        let inUl = false;
        let inOl = false;

        const closeUl = () => { if (inUl) { html += '</ul>'; inUl = false; } };
        const closeOl = () => { if (inOl) { html += '</ol>'; inOl = false; } };
        const closeLists = () => { closeUl(); closeOl(); };

        const openUl = () => {
            if (!inUl) {
                closeOl();
                html += '<ul class="list-disc pl-5 space-y-1.5 text-sm leading-6">';
                inUl = true;
            }
        };

        const openOl = () => {
            if (!inOl) {
                closeUl();
                html += '<ol class="list-decimal pl-5 space-y-1.5 text-sm leading-6">';
                inOl = true;
            }
        };

        for (const line of lines) {
            const t = line.trim();

            if (!t) {
                closeLists();
                html += '<div class="h-2"></div>';
                continue;
            }

            let m = t.match(/^(\d+)\.\s+(.*)$/);
            if (m) {
                openOl();
                html += `<li>${m[2]}</li>`;
                continue;
            }

            m = t.match(/^([•-])\s+(.*)$/);
            if (m) {
                openUl();
                html += `<li>${m[2]}</li>`;
                continue;
            }

            closeLists();
            html += `<p class="text-sm leading-6">${t}</p>`;
        }

        closeLists();
        return html.replace(/(<div class="h-2"><\/div>)+$/g, '');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new Chatbot());
} else {
    new Chatbot();
}
