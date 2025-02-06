
<!-- resources/views/navigation/desktop-links.blade.php -->
<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    @role('admin')
        <x-nav-link href="{{ route('adminEspace') }}" :active="request()->routeIs('adminEspace')" wire:navigate>
            {{ __('Tableau de bord') }}
        </x-nav-link>
        <x-nav-link href="{{ route('admin.users') }}" :active="request()->routeIs('admin.users')" wire:navigate>
            {{ __('Utilisateurs') }}
        </x-nav-link>
        <x-nav-link href="{{ route('admin.niveau') }}" :active="request()->routeIs('admin.niveau')" wire:navigate>
            {{ __('Niveau') }}
        </x-nav-link>
        <x-nav-link href="{{ route('admin.parcour') }}" :active="request()->routeIs('admin.parcour')" wire:navigate>
            {{ __('Parcours') }}
        </x-nav-link>
    @endrole

    @role('teacher')
        <x-nav-link href="{{ route('teacherEspace') }}" :active="request()->routeIs('teacherEspace')" wire:navigate>
            {{ __('Tableau de bord') }}
        </x-nav-link>
        <x-nav-link href="{{ route('document.teacher') }}"
            :active="request()->routeIs('document.teacher') || request()->routeIs('document.upload')" wire:navigate>
            {{ __('Mes Documents') }}
        </x-nav-link>
    @endrole

    @role('student')
        <x-nav-link href="{{ route('studentEspace') }}" :active="request()->routeIs('studentEspace')" wire:navigate>
            {{ __('Mes documents') }}
        </x-nav-link>
        <x-nav-link href="{{ route('student.myTeacher')}}" :active="request()->routeIs('student.myTeacher')" wire:navigate>
            {{ __('Enseignants') }}
        </x-nav-link>
    @endrole
</div>
