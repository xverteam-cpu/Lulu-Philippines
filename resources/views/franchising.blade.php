@extends('layouts.app')

@section('content')
<style>
  .franchise-shell {
    width: min(100%, 1040px);
    margin: 0 auto;
    padding: 12px 0 32px;
  }

  .franchise-visual {
    display: block;
    width: 100%;
    height: auto;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
  }

  @media (max-width: 640px) {
    .franchise-shell {
      padding: 8px 0 24px;
    }

    .franchise-visual {
      border-radius: 12px;
    }
  }
</style>

<div class="franchise-shell">
  <img src="{{ asset('Franchise.svg') }}" alt="Lulu franchise page" class="franchise-visual" loading="eager" decoding="async">
</div>
@endsection
