@extends('layouts.app')

@section('content')
<div class="glass-panel" style="padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin: 0;">Menu Management</h2>
        <a href="{{ route('menus.create') }}" class="btn btn-primary">+ Add New Menu</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($menus as $menu)
            <tr>
                <td>
                    @if($menu->image)
                        <img src="/storage/{{ $menu->image }}" alt="{{ $menu->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    @else
                        <div style="width: 60px; height: 60px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">No Img</div>
                    @endif
                </td>
                <td style="font-weight: 600;">{{ $menu->name }}</td>
                <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('menus.edit', $menu) }}" class="btn" style="background: var(--text-main); color: white; padding: 0.5rem 1rem; font-size: 0.875rem;">Edit</a>
                    <form action="{{ route('menus.destroy', $menu) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">No menus found. Click "Add New Menu" to get started.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
