@extends('layouts.app')

@section('content')
<style>
  .agreement-page { max-width:850px; margin:0 auto; padding:28px 18px 48px; color:#17202a; font-family:Georgia, 'Times New Roman', serif; line-height:1.6; }
  .agreement-page h1 { margin:0; text-align:center; font-size:28px; }
  .agreement-page h2 { margin:4px 0 24px; text-align:center; font-size:20px; }
  .agreement-meta { margin:20px 0; padding:16px; border:1px solid #d9dee5; border-radius:10px; background:#f8fafc; font-family:Inter, sans-serif; }
  .agreement-meta div { display:flex; justify-content:space-between; gap:16px; padding:4px 0; }
  .agreement-meta strong { text-align:right; }
  .agreement-page h3 { margin-top:24px; font-size:17px; }
  .agreement-sign { display:grid; grid-template-columns:1fr 1fr; gap:36px; margin-top:34px; font-family:Inter, sans-serif; }
  .agreement-sign div { border-top:1px solid #17202a; padding-top:8px; }
  .agreement-notice { margin-bottom:18px; padding:12px 14px; border-radius:8px; background:#fff7ed; color:#9a3412; font-family:Inter, sans-serif; font-size:14px; }
  .agreement-form { margin-top:28px; padding:18px; border:1px solid #d9dee5; border-radius:10px; background:#f8fafc; font-family:Inter, sans-serif; }
  .agreement-form label { display:block; margin-bottom:7px; color:#17202a; font-size:13px; font-weight:800; }
  .agreement-form input[type=text] { width:100%; box-sizing:border-box; padding:12px; border:1px solid #cbd5e1; border-radius:8px; font-size:15px; }
  .agreement-form .agreement-check { display:flex; gap:8px; margin:12px 0; font-size:13px; line-height:18px; }
  .agreement-form button { width:100%; min-height:48px; border:0; border-radius:8px; background:#166534; color:#fff; font-size:14px; font-weight:900; cursor:pointer; }
  @media (max-width:600px) { .agreement-meta div { display:block; } .agreement-meta strong { display:block; text-align:left; } .agreement-sign { gap:20px; } }
</style>
<main class="agreement-page">
  @if ($isSample)
    <div class="agreement-notice">Sample agreement. The final agreement is generated with your selected package, amount, dates, and signature.</div>
  @endif
  <h1>LULU HOLDING CORP.</h1>
  <h2>Bond Purchase Agreement</h2>
  <p>This Bond Purchase Agreement (the Agreement) is entered into by and between LULU HOLDING CORP. (the Company) and the registered Lulu account holder (the Bondholder).</p>
  <p>The Bondholder has expressed the intent to purchase the selected bond package under the terms and conditions set forth in this Agreement.</p>
  <h3>1. BOND PURCHASE PACKAGE</h3>
  <div class="agreement-meta">
    <div><span>Bondholder</span><strong>{{ $agreement['bondholder'] }}</strong></div>
    <div><span>Package Type</span><strong>{{ $agreement['package_name'] }}</strong></div>
    <div><span>Bond Purchase Amount</span><strong>{{ $agreement['amount'] }}</strong></div>
    <div><span>Daily Interest Rate</span><strong>{{ $agreement['daily_interest_rate'] }}</strong></div>
    <div><span>Contract Term</span><strong>{{ $agreement['duration_days'] }}</strong></div>
    <div><span>Commencement Date</span><strong>{{ $agreement['commencement_date'] }}</strong></div>
    <div><span>Maturity Date</span><strong>{{ $agreement['maturity_date'] }}</strong></div>
  </div>
  <h3>2. TERM AND PAYMENT OF INTEREST</h3>
  <p>The Company shall pay the Bondholder daily interest based on the selected package, principal amount, and rate stated above. Interest and principal are subject to the terms, verification, redemption, and risk disclosures in this Agreement.</p>
  <h3>3. REDEMPTION AND BOND CERTIFICATE</h3>
  <p>The Bondholder may request redemption at maturity. After payment verification and contract activation, the Company will maintain an electronic bond record showing the Bondholder, package, principal, rate, commencement date, and maturity date.</p>
  <h3>4. RISK AND REGULATORY ACKNOWLEDGMENT</h3>
  <p>The Bondholder understands that a corporate bond is an investment obligation of the Company and is not a bank deposit. The issuance and sale are subject to applicable registrations, approvals, disclosures, exemptions, and other legal requirements.</p>
  <p>The parties confirm that they have read and understood this Agreement and voluntarily accept its terms.</p>
  @if (!empty($isSigning))
    <form class="agreement-form" method="post" action="{{ route('investments.store') }}">
      @csrf
      <input type="hidden" name="package" value="{{ $purchase['package'] }}">
      <input type="hidden" name="amount" value="{{ $purchase['amount'] }}">
      <input type="hidden" name="currency" value="{{ $purchase['currency'] }}">
      <input type="hidden" name="payment_method" value="{{ $purchase['payment_method'] }}">
      <label for="agreementSignature">Electronic signature</label>
      <input id="agreementSignature" type="text" name="agreement_signature_name" maxlength="150" placeholder="Type your legal name" autocomplete="name" required>
      <label class="agreement-check"><input type="checkbox" name="agreement_accepted" value="1" required> <span>I have read and understood this Agreement and voluntarily accept its terms.</span></label>
      <button type="submit">Sign agreement and submit investment</button>
    </form>
  @endif
  <div class="agreement-sign">
    <div>LULU HOLDING CORP.<br>Jhon Joem Ramirez<br>Authorized Representative</div>
    <div>BONDHOLDER<br>{{ $agreement['signature_name'] ?: 'Electronic signature required' }}<br>Date: {{ $agreement['commencement_date'] }}</div>
  </div>
</main>
@endsection
