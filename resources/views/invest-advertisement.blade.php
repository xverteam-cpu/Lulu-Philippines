@extends('layouts.app')

@section('content')
<style>
  body { background:#f4f6f9 !important; }
  .container { max-width:none !important; margin:0 !important; padding:0 !important; }
  .bond-advertisement-page {
    box-sizing:border-box;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:space-between;
    min-height:100vh;
    min-height:100svh;
    padding:0 0 max(12px, env(safe-area-inset-bottom));
  }
  .bond-advertisement-image {
    display:block;
    width:min(100vw, calc(100svh - 88px));
    max-width:100vw;
    height:auto;
    object-fit:contain;
  }
  .bond-advertisement-next {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:180px;
    min-height:52px;
    margin:12px 16px 0;
    padding:0 28px;
    border:0;
    border-radius:14px;
    background:#087a48;
    color:#fff;
    font:inherit;
    font-size:16px;
    font-weight:800;
    text-decoration:none;
  }
  .bond-advertisement-next:focus-visible {
    outline:3px solid #14532d;
    outline-offset:3px;
  }
</style>

<main class="bond-advertisement-page">
  <img
    class="bond-advertisement-image"
    src="{{ asset('images/lulu-bonds-investment-advertisement.png') }}"
    alt="Invest in Lulu Bonds starting from ₱8,000. Earn interest on your bond investment."
  >
  <a class="bond-advertisement-next" href="{{ route('invest') }}">Next</a>
</main>
@endsection
