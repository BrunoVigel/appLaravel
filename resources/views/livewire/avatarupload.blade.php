<form wire:submit="save" action="/manage-avatar" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <input wire:loading.attr="disabled" wire:target="avatar" wire:model="avatar" type="file" name="avatar">
        @error('avatar')
            <div class="alert alert-danger small shadow-sm">{{ $message }}</div>
        @enderror
    </div>
    <button wire:loading.attr="disabled" wire:target="avatar" class="btn btn-primary">Save</button>
</form>