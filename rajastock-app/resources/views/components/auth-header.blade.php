@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading accent="true" size="xl" >{{ $title }}</flux:heading>
    <flux:subheading accent="true" class="mb-4" >{{ $description }}</flux:subheading>
</div>
