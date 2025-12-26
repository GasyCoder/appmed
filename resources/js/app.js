import './bootstrap'
import focus from '@alpinejs/focus'
import collapse from '@alpinejs/collapse'
import './chatbot.js'

// ✅ Livewire v3 ESM: une seule instance Alpine (celle de Livewire)
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm'

window.Alpine = Alpine

function applyTheme() {
  const stored = localStorage.getItem('darkMode')
  const prefersDark =
    window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches

  const isDark = stored === null ? prefersDark : stored === 'true'
  document.documentElement.classList.toggle('dark', isDark)
  return isDark
}

// ✅ Tout déclarer au bon moment
document.addEventListener('alpine:init', () => {
  Alpine.plugin(focus)
  Alpine.plugin(collapse)

  Alpine.store('theme', {
    darkMode: applyTheme(),

    sync() {
      this.darkMode = applyTheme()
    },

    set(value) {
      this.darkMode = !!value
      localStorage.setItem('darkMode', this.darkMode ? 'true' : 'false')
      this.sync()
    },

    toggle() {
      this.set(!this.darkMode)
    },
  })
})

// ✅ Après navigation Livewire (wire:navigate)
document.addEventListener('livewire:navigated', () => {
  Alpine.store('theme')?.sync?.()
})

// ✅ Démarrage unique
Livewire.start()
