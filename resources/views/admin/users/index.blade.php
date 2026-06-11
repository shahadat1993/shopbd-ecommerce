@extends('layouts.admin.app')
@section('title','Users')
@section('page_title','Users')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb breadcrumb-style1 mb-0">
  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
  <li class="breadcrumb-item active">Users</li>
</ol></nav>
@endsection
@section('page_actions')
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Add User</a>
@endsection

@section('content')
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Name or Email..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select name="role" class="form-select">
          <option value="">All Roles</option>
          @foreach($roles as $role)
            <option value="{{ $role->name }}" {{ request('role')===$role->name?'selected':'' }}>{{ ucfirst($role->name) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">All Status</option>
          <option value="1" {{ request('status')==='1'?'selected':'' }}>Active</option>
          <option value="0" {{ request('status')==='0'?'selected':'' }}>Inactive</option>
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">All Users <span class="badge bg-label-primary ms-1">{{ $users->total() }}</span></h5>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>User</th><th>Role</th><th>Orders</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>
            <div class="d-flex align-items-center gap-3">
              <div class="avatar avatar-sm">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle w-px-40 h-auto">
              </div>
              <div>
                <p class="mb-0 fw-semibold">{{ $user->name }}</p>
                <small class="text-muted">{{ $user->email }}</small>
              </div>
            </div>
          </td>
          <td>
            @foreach($user->roles as $role)
              <span class="badge bg-label-primary me-1">{{ ucfirst($role->name) }}</span>
            @endforeach
          </td>
          <td>{{ $user->orders_count ?? 0 }}</td>
          <td>
            @if($user->is_active)
              <span class="badge bg-label-success">Active</span>
            @else
              <span class="badge bg-label-danger">Inactive</span>
            @endif
          </td>
          <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.users.show',$user) }}" class="btn btn-sm btn-icon btn-outline-info" title="View">
                <i class="bx bx-show"></i>
              </a>
              <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                <i class="bx bx-edit"></i>
              </a>
              <form action="{{ route('admin.users.toggle',$user) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-icon btn-outline-{{ $user->is_active?'warning':'success' }}" title="{{ $user->is_active?'Deactivate':'Activate' }}">
                  <i class="bx bx-{{ $user->is_active?'block':'check' }}"></i>
                </button>
              </form>
              @if($user->id !== auth()->id())
              <form action="{{ route('admin.users.destroy',$user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete">
                  <i class="bx bx-trash"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-5">No users found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
  <div class="card-footer d-flex justify-content-between align-items-center">
    <span class="text-muted small">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</span>
    {{ $users->withQueryString()->links() }}
  </div>
  @endif
</div>
@endsection
