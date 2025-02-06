@session("success")
<div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900"
     x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 3000)">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-200">
            {{ $value }}
        </p>
    </div>
</div>
@endsession

@session("error")
<div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900"
     x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 3000)">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <p class="ml-3 text-sm font-medium text-red-800 dark:text-red-200">
            {{ $value }}
        </p>
    </div>
</div>
@endsession
