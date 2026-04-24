@extends('layouts.app')

@section('content')
<div class="glass-panel" style="padding: 2rem; max-width: 600px; margin: 0 auto;">
    <h2 style="margin-top: 0;">Add New Menu</h2>
    
    <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label class="form-label">Menu Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Description (Optional)</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Price (Rp)</label>
            <input type="number" name="price" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Image (Optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">Save Menu</button>
            <a href="{{ route('menus.index') }}" class="btn" style="background: #e5e7eb; color: var(--text-main);">Cancel</a>
        </div>
    </form>
</div>
@endsection
