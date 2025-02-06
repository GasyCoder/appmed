<!-- resources/views/navigation/mobile-menu.blade.php -->
<div class="pt-2 pb-3 space-y-1">
    @role('admin')
        <x-responsive-nav-link href="{{ route('adminEspace') }}" :active="request()->routeIs('adminEspace')">
            {{ __('Tableau de bord') }}
        </x-responsive-nav-link>
    @endrole

    @role('teacher')
        <x-responsive-nav-link href="{{ route('teacherEspace') }}" :active="request()->routeIs('teacherEspace')">
            {{ __('Tableau de bord') }}
        </x-responsive-nav-link>
    @endrole

    @role('student')
        <x-responsive-nav-link href="{{ route('studentEspace') }}" :active="request()->routeIs('studentEspace')">
            {{ __('Tableau de bord') }}
        </x-responsive-nav-link>
    @endrole
</div>
