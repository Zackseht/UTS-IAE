@extends('layouts.app')

@section('content')
<div class="glass-panel" style="padding: 2rem; max-width: 600px; margin: 0 auto;">
    <h2 style="margin-top: 0;">Edit Menu: {{ $menu->name }}</h2>
    
    <form action="{{ route('menus.update', $menu) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">Menu Name</label>
            <input type="text" name="name" class="form-control" value="{{ $menu->name }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ $menu->description }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Price (Rp)</label>
            <input type="number" name="price" class="form-control" value="{{ $menu->price }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Current Image</label>
            @if($menu->image)
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('storage/' . $menu->image) }}" alt="Current Image" style="width: 100px; border-radius: 8px;">
                </div>
            @endif
            <label class="form-label">Replace Image (Optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">Update Menu</button>
            <a href="{{ route('menus.index') }}" class="btn" style="background: #e5e7eb; color: var(--text-main);">Cancel</a>
        </div>
    </form>
</div>
@endsection
