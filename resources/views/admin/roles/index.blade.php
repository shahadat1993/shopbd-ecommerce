@extends('layouts.admin.app')
@section('title','Roles')
@section('page_title','Roles & Permissions')
@section('page_actions')
<a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Add Role</a>
@endsection
@section('content')
<div class="row g-4">
@foreach($roles as $role)
<div class="col-md-6 col-xl-4">
  <div class="card h-100">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <h5 class="card-title mb-1 text-capitalize">{{ $role->name }}</h5>
          <p class="text-muted small mb-0">{{ $role->users_count ?? 0 }} users assigned</p>
        </div>
        <div class="d-flex gap-1">
          <a href="{{ route('admin.roles.edit',$role) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-edit"></i></a>
          @if(!in_array($role->name,['super-admin','admin','customer']))
          <form action="{{ route('admin.roles.destroy',$role) }}" method="POST" onsubmit="return confirm('Delete role?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
          </form>
          @endif
        </div>
      </div>
      <div class="d-flex flex-wrap gap-1">
        @forelse($role->permissions->take(6) as $perm)
          <span class="badge bg-label-secondary small">{{ $perm->name }}</span>
        @empty
          <span class="text-muted small">No permissions assigned</span>
        @endforelse
        @if($role->permissions->count() > 6)
          <span class="badge bg-label-primary">+{{ $role->permissions->count()-6 }} more</span>
        @endif
      </div>
    </div>
  </div>
</div>
@endforeach
</div>
@endsection
