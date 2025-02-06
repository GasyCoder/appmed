<!-- resources/views/navigation/desktop-logo.blade.php -->
<div class="shrink-0 flex items-center">
    @role('admin')
        <a href="{{ route('adminEspace') }}">
            <x-application-mark class="block h-9 w-auto" />
        </a>
    @endrole
    @role('teacher')
        <a href="{{ route('teacherEspace') }}">
            <x-application-mark class="block h-9 w-auto" />
        </a>
    @endrole
    @role('student')
        <a href="{{ route('studentEspace') }}">
            <x-application-mark class="block h-9 w-auto" />
        </a>
    @endrole
</div>
