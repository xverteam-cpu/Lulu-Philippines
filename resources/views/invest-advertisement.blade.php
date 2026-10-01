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
    min-height:100vh;
    padding:24px 16px;
  }
  .bond-advertisement-image {
    display:block;
    width:min(100%, 900px);
    height:auto;
    border-radius:16px;
    box-shadow:0 16px 40px rgba(15, 23, 42, .16);
  }
  .bond-advertisement-next {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:180px;
    min-height:52px;
    margin-top:auto;
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
