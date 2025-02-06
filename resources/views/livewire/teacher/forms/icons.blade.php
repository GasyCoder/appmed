<div class="flex-shrink-0">
    <!-- PDF -->
    <template x-if="['pdf'].includes(file.extension)">
        <svg class="h-8 w-8 text-red-400" fill="currentColor" viewBox="0 0 384 512">
            <path d="M181.9 256.1c-5-16-4.9-46.9-2-46.9 8.4 0 7.6 36.9 2 46.9zm-1.7 47.2c-7.7 20.2-17.3 43.3-28.4 62.7 18.3-7 39-17.2 62.9-21.9-12.7-9.6-24.9-23.4-34.5-40.8zM86.1 428.1c0 .8 13.2-5.4 34.9-40.2-6.7 6.3-29.1 24.5-34.9 40.2zM248 160h136v328c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V24C0 10.7 10.7 0 24 0h200v136c0 13.2 10.8 24 24 24zm-8 171.8c-20-12.2-33.3-29-42.7-53.8 4.5-18.5 11.6-46.6 6.2-64.2-4.7-29.4-42.4-26.5-47.8-6.8-5 18.3-.4 44.1 8.1 77-11.6 27.6-28.7 64.6-40.8 85.8-.1 0-.1.1-.2.1-27.1 13.9-73.6 44.5-54.5 68 5.6 6.9 16 10 21.5 10 17.9 0 35.7-18 61.1-61.8 25.8-8.5 54.1-19.1 79-23.2 21.7 11.8 47.1 19.5 64 19.5 29.2 0 31.2-32 19.7-43.4-13.9-13.6-54.3-9.7-73.6-7.2zM377 105L279 7c-4.5-4.5-10.6-7-17-7h-6v128h128v-6.1c0-6.3-2.5-12.4-7-16.9zm-74.1 255.3c4.1-2.7-2.5-11.9-42.8-9 37.1 15.8 42.8 9 42.8 9z"/>
        </svg>
    </template>
    <!-- PowerPoint -->
    <template x-if="['ppt', 'pptx'].includes(file.extension)">
        <svg class="h-8 w-8 text-orange-400" fill="currentColor" viewBox="-0.13 0 32.15 32">
            <path d="M18 2A14.041 14.041 0 0 0 4 16l17.737 3.737z" fill="#ed6c47"/>
            <path d="M18 2a14.041 14.041 0 0 1 14 14l-7 4.758L18 16z" fill="#ff8f6b"/>
            <path d="M18 30a14.041 14.041 0 0 0 14-14H4a14.041 14.041 0 0 0 14 14z" fill="#d35230"/>
            <path d="M16.666 7h-9.36a13.914 13.914 0 0 0 .93 19h8.43A1.337 1.337 0 0 0 18 24.667V8.333A1.337 1.337 0 0 0 16.666 7z" opacity=".1"/>
            <path d="M15.666 8H6.54a13.906 13.906 0 0 0 2.845 19h6.282A1.337 1.337 0 0 0 17 25.667V9.333A1.337 1.337 0 0 0 15.666 8z" opacity=".2"/>
            <path d="M15.666 8H6.54a13.89 13.89 0 0 0 .766 17h8.361A1.337 1.337 0 0 0 17 23.667V9.333A1.337 1.337 0 0 0 15.666 8z" opacity=".2"/>
            <path d="M14.666 8H6.54a13.89 13.89 0 0 0 .766 17h7.361A1.337 1.337 0 0 0 16 23.667V9.333A1.337 1.337 0 0 0 14.666 8z" opacity=".2"/>
            <path d="M1.333 8h13.334A1.333 1.333 0 0 1 16 9.333v13.334A1.333 1.333 0 0 1 14.667 24H1.333A1.333 1.333 0 0 1 0 22.667V9.333A1.333 1.333 0 0 1 1.333 8z" fill="#c43e1c"/>
            <path d="M7.997 11a4.168 4.168 0 0 1 2.755.805 2.878 2.878 0 0 1 .956 2.331 2.726 2.726 0 0 1-.473 1.588 3.164 3.164 0 0 1-1.344 1.186 4.57 4.57 0 0 1-2.02.424h-1.91V21H4V11zM5.96 15.683h1.687a2.194 2.194 0 0 0 1.492-.444 1.107 1.107 0 0 0 .504-1.026q0-1.659-1.933-1.659H5.96z" fill="#f9f7f7"/>
            <path d="M0 0h32v32H0z" fill="none"/>
        </svg>
    </template>
    <!-- Excel -->
    <template x-if="['xls', 'xlsx'].includes(file.extension)">
        <svg class="h-8 w-8 text-green-400" fill="currentColor" viewBox="0 0 384 512">
            <path d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm212-240h-28.8c-4.4 0-8.4 2.4-10.5 6.3-18 33.1-22.2 42.4-28.6 57.7-13.9-29.1-6.9-17.3-28.6-57.7-2.1-3.9-6.2-6.3-10.6-6.3H124c-9.3 0-15 10-10.4 18l46.3 78-46.3 78c-4.7 8 1.1 18 10.4 18h28.9c4.4 0 8.4-2.4 10.5-6.3 21.7-40 23-45 28.6-57.7 14.9 30.2 5.9 15.9 28.6 57.7 2.1 3.9 6.2 6.3 10.6 6.3H260c9.3 0 15-10 10.4-18L224 320c.7-1.1 30.3-50.5 46.3-78 4.7-8-1.1-18-10.3-18z"/>
        </svg>
    </template>
    <!-- Images -->
    <template x-if="['jpg', 'jpeg', 'png', 'gif'].includes(file.extension)">
        <svg class="h-8 w-8 text-blue-400" fill="currentColor" viewBox="0 0 384 512">
            <path d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm32-48h224V288l-23.5-23.5c-4.7-4.7-12.3-4.7-17 0L176 352l-39.5-39.5c-4.7-4.7-12.3-4.7-17 0L80 352v64zm48-240c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z"/>
        </svg>
    </template>
    <!-- Word -->
    <template x-if="['doc', 'docx', 'dotx', 'dot'].includes(file.extension)">
        <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="-0.13 0 32.13 32">
            <path d="M30.667 2H9.333A1.333 1.333 0 0 0 8 3.333V9l12 3.5L32 9V3.333A1.333 1.333 0 0 0 30.667 2z" fill="#41a5ee"/>
            <path d="M32 9H8v7l12 3.5L32 16z" fill="#2b7cd3"/>
            <path d="M32 16H8v7l12 3.5L32 23z" fill="#185abd"/>
            <path d="M32 23H8v5.667A1.333 1.333 0 0 0 9.333 30h21.334A1.333 1.333 0 0 0 32 28.667z" fill="#103f91"/>
            <path d="M16.667 7H8v19h8.667A1.337 1.337 0 0 0 18 24.667V8.333A1.337 1.337 0 0 0 16.667 7z" opacity=".1"/>
            <path d="M15.667 8H8v19h7.667A1.337 1.337 0 0 0 17 25.667V9.333A1.337 1.337 0 0 0 15.667 8z" opacity=".2"/>
            <path d="M15.667 8H8v17h7.667A1.337 1.337 0 0 0 17 23.667V9.333A1.337 1.337 0 0 0 15.667 8z" opacity=".2"/>
            <path d="M14.667 8H8v17h6.667A1.337 1.337 0 0 0 16 23.667V9.333A1.337 1.337 0 0 0 14.667 8z" opacity=".2"/>
            <path d="M1.333 8h13.334A1.333 1.333 0 0 1 16 9.333v13.334A1.333 1.333 0 0 1 14.667 24H1.333A1.333 1.333 0 0 1 0 22.667V9.333A1.333 1.333 0 0 1 1.333 8z" fill="#185abd"/>
            <path d="M11.95 21h-1.8l-2.1-6.9-2.2 6.9h-1.8l-2-10h1.8l1.4 7 2.1-6.8h1.5l2 6.8 1.4-7h1.7z" fill="#fff"/>
            <path d="M0 0h32v32H0z" fill="none"/>
        </svg>
    </template>
    <!-- Autres fichiers -->
    <template x-if="!['pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'dotx', 'dot'].includes(file.extension)">
        <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 384 512">
            <path d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48z"/>
        </svg>
    </template>
</div>
