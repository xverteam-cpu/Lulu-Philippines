@extends('layouts.app')

@section('content')
<style>
  .action-shell {
    max-width: 840px;
    margin: 24px auto 90px;
    padding: 18px;
  }

  .action-card {
    background: #fff;
    border-radius: 18px;
    padding: 26px;
    box-shadow: 0 16px 36px rgba(3,7,18,0.08);
    border: 1px solid rgba(3,7,18,0.04);
  }

  .action-title { font-size: 24px; font-weight: 800; margin-bottom: 10px; }
  .action-sub { color: #64748b; margin-bottom: 24px; }

  .input-group { display: grid; gap: 14px; margin-bottom: 20px; }
  .input-label { font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: 0.02em; }
  .input-field { width: 100%; padding: 14px 16px; border-radius: 14px; border: 1px solid #d1d5db; background: #f8fafc; font-size: 15px; }
  .primary-btn { padding: 14px 18px; border-radius: 14px; border: none; background: #166534; color: #fff; font-weight: 700; cursor: pointer; }
</style>

<main class="action-shell">
  <div class="action-card">
    <div class="action-title">Edit Profile</div>
    <div class="action-sub">Update your public account name, email address, and profile details.</div>

    @if (session('status'))
      <div role="status" style="margin-bottom:16px; color:#166534; font-weight:700;">{{ session('status') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="post">
      @csrf
      <div class="input-group">
        <label class="input-label" for="name">Name</label>
        <input class="input-field" id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required maxlength="255">
        @error('name') <div role="alert" style="color:#b91c1c;">{{ $message }}</div> @enderror
      </div>

      <div class="input-group">
        <label class="input-label" for="email">Email</label>
        <input class="input-field" id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required maxlength="255">
        @error('email') <div role="alert" style="color:#b91c1c;">{{ $message }}</div> @enderror
      </div>

      <div class="input-group">
        <label class="input-label" for="region">Region</label>
        <input class="input-field" id="region" name="region" type="text" value="{{ old('region', auth()->user()->region ?: '') }}" maxlength="255">
        @error('region') <div role="alert" style="color:#b91c1c;">{{ $message }}</div> @enderror
      </div>

      <button class="primary-btn" type="submit">Save changes</button>
    </form>
  </div>
</main>
@endsection
