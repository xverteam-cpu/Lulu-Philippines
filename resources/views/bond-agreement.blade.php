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
  .signature-pad { display:block; width:100%; height:150px; margin-top:10px; border:1px solid #94a3b8; border-radius:8px; background:#fff; touch-action:none; cursor:crosshair; }
  .signature-actions { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:8px; color:#64748b; font-size:12px; }
  .signature-clear { border:0; background:transparent; color:#166534; font-weight:900; cursor:pointer; }
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
  <p>This Bond Purchase Agreement (the Agreement) is executed on <strong>{{ $agreement['commencement_date'] }}</strong> by and between:</p>
  <p><strong>LULU HOLDING CORP.</strong>, a duly organized corporation with principal business address at ______________________________, represented herein by its authorized representative, Mr. Jhon Joem Ramirez, hereinafter referred to as the Company; and</p>
  <p><strong>{{ $agreement['bondholder'] }}</strong>, hereinafter referred to as the Bondholder.</p>
  <p><strong>WITNESSETH</strong></p>
  <p>WHEREAS, the Company offers bond purchase packages that allow qualified purchasers to acquire corporate bonds subject to the selected package, contract term, and interest rate;</p>
  <p>WHEREAS, the Bondholder has expressed the intent to purchase the selected Bond Package under the terms and conditions set forth in this Agreement;</p>
  <p>NOW, THEREFORE, for and in consideration of the foregoing premises and mutual covenants, the parties agree as follows:</p>
  <h3>1. BOND PURCHASE PACKAGE</h3>
  <div class="agreement-meta">
    <div><span>Bondholder</span><strong>{{ $agreement['bondholder'] }}</strong></div>
    <div><span>Package Type</span><strong>{{ $agreement['package_name'] }}</strong></div>
    <div><span>Contract Term</span><strong>{{ $agreement['duration_days'] }}</strong></div>
    <div><span>Bond Purchase Amount</span><strong>{{ $agreement['amount'] }}</strong></div>
    <div><span>Daily Interest Rate</span><strong>{{ $agreement['daily_interest_rate'] }}</strong></div>
    <div><span>Daily Interest Income</span><strong>{{ $agreement['daily_interest_income'] }}</strong></div>
    <div><span>Commencement Date</span><strong>{{ $agreement['commencement_date'] }}</strong></div>
    <div><span>Maturity Date</span><strong>{{ $agreement['maturity_date'] }}</strong></div>
  </div>
  <h3>2. TERM AND PAYMENT OF INTEREST</h3>
  <p>The Company shall pay the Bondholder daily interest based on the selected package, bond purchase amount, and rate stated in Section 1 of this Agreement.</p>
  <p>Interest shall be credited monthly to the Bondholder's designated bank account or through another payment method agreed upon in writing by both parties. Account balances reflected on the Company's dashboard shall be withdrawable to the Bondholder's verified bank account, subject to the applicable processing fee disclosed to and accepted by the Bondholder.</p>
  <p>Upon maturity of the contract term stated in Section 1, the full bond principal shall be returned to the Bondholder. The Bondholder may withdraw the principal or purchase a new bond package, subject to availability and a new agreement.</p>
  <h3>3. REDEMPTION AT MATURITY</h3>
  <p>The Bondholder may request redemption of the bond principal upon maturity. The amount shall be released within three (3) banking days from the date of the completed redemption request.</p>
  <h3>4. EARLY REDEMPTION</h3>
  <p>If the Bondholder requests early redemption before the Maturity Date, the applicable early redemption fee disclosed to and accepted by the Bondholder shall apply. The remaining principal shall be processed within three (3) banking days after approval of the request.</p>
  <h3>5. BOND CERTIFICATE</h3>
  <p>After confirmation of payment, the Company shall issue a Bond Purchase Certificate or electronic bond record showing the Bondholder's name, package, principal amount, interest rate, commencement date, and maturity date. The certificate or electronic record forms part of this Agreement and shall be used to verify the Bondholder's ownership of the bond package.</p>
  <h3>6. USE OF FUNDS</h3>
  <p>The Company shall use the bond purchase proceeds for its lawful business operations, expansion, working capital, project development, and other approved corporate purposes.</p>
  <h3>7. CONFIDENTIALITY</h3>
  <p>The Company and the Bondholder shall keep confidential all business information, financial records, account details, strategies, and other private information obtained in connection with this Agreement. Disclosure to third parties is prohibited unless required by law, requested by a competent authority, or authorized in writing by the other party. This obligation shall continue after this Agreement ends.</p>
  <h3>8. NON-TRANSFERABILITY</h3>
  <p>The rights under this Agreement are personal to the Bondholder and may not be assigned, transferred, pledged, or sold without the prior written consent of the Company. Any unauthorized transfer shall be void.</p>
  <h3>9. COMPANY OBLIGATIONS</h3>
  <p>The Company shall maintain accurate records of the bond purchase, interest payments, withdrawals, and principal repayment and shall provide reasonable assistance regarding the Bondholder's account.</p>
  <h3>10. BONDHOLDER OBLIGATIONS</h3>
  <p>The Bondholder shall provide correct personal and banking information, comply with identity-verification requirements, keep account access details secure, and promptly notify the Company of any change in contact or payment information.</p>
  <h3>11. RISK ACKNOWLEDGMENT</h3>
  <p>The Bondholder understands that a corporate bond is an investment obligation of the Company and is not a bank deposit. Payment depends on the Company's ability to perform its obligations. The Bondholder confirms that the selected package has been explained and that the Bondholder had the opportunity to seek independent advice.</p>
  <h3>12. DISPUTE RESOLUTION</h3>
  <p>If a dispute arises, the parties shall first attempt to resolve it through good-faith negotiation within thirty (30) days from written notice. If unresolved, the parties may submit the matter to mediation. If mediation fails, either party may seek relief through the proper courts of Taguig City, Philippines, subject to applicable law.</p>
  <h3>13. GOVERNING LAW</h3>
  <p>This Agreement shall be governed by the laws of the Republic of the Philippines, including applicable corporate and securities laws.</p>
  <h3>14. ENTIRE AGREEMENT</h3>
  <p>This Agreement and the Bond Purchase Certificate constitute the entire understanding between the parties concerning the selected bond package and supersede prior discussions relating to the same transaction.</p>
  <h3>15. AMENDMENTS</h3>
  <p>Any amendment must be in writing and signed by both parties. No verbal change shall be binding.</p>
  <h3>16. REGULATORY COMPLIANCE</h3>
  <p>The issuance and sale of the bond package shall be subject to all registrations, approvals, disclosures, exemptions, and other requirements imposed by the Securities and Exchange Commission and other competent authorities. Nothing in this Agreement shall be interpreted as proof of regulatory approval.</p>
  <h3>IN WITNESS WHEREOF</h3>
  <p>The parties have affixed their signatures on {{ $agreement['commencement_date'] }} at Taguig City, Philippines.</p>
  @if (!empty($isSigning))
    <form class="agreement-form" method="post" action="{{ route('investments.store') }}">
      @csrf
      <input type="hidden" name="package" value="{{ $purchase['package'] }}">
      <input type="hidden" name="amount" value="{{ $purchase['amount'] }}">
      <input type="hidden" name="currency" value="{{ $purchase['currency'] }}">
      <input type="hidden" name="payment_method" value="{{ $purchase['payment_method'] }}">
      <label>Name on contract</label>
      <input type="text" value="{{ $agreement['bondholder'] }}" readonly>
      <input type="hidden" name="agreement_signature_name" value="{{ $agreement['bondholder'] }}">
      <label for="agreementSignaturePad">Draw your signature</label>
      <canvas class="signature-pad" id="agreementSignaturePad" width="900" height="300" aria-label="Draw your signature"></canvas>
      <input type="hidden" name="agreement_signature_data" id="agreementSignatureData" required>
      <div class="signature-actions"><span>Use your finger, mouse, or stylus.</span><button class="signature-clear" type="button" id="clearAgreementSignature">Clear signature</button></div>
      <label class="agreement-check"><input type="checkbox" name="agreement_accepted" value="1" required> <span>I have read and understood this Agreement and voluntarily accept its terms.</span></label>
      <button type="submit">Sign agreement and submit investment</button>
    </form>
  @endif
  <div class="agreement-sign">
    <div>LULU HOLDING CORP.<br>____________________________<br>Jhon Joem Ramirez<br>Authorized Representative</div>
    <div>BONDHOLDER<br>{{ $agreement['bondholder'] }}<br>Date: {{ $agreement['commencement_date'] }}</div>
  </div>
  <h3>ACKNOWLEDGMENT</h3>
  <p>REPUBLIC OF THE PHILIPPINES )<br>CITY OF TAGUIG ) S.S.</p>
  <p>BEFORE ME, a Notary Public for and in the City of Taguig, personally appeared the following persons and acknowledged that this Agreement is their free and voluntary act and deed and, for the Company's representative, the act and deed of the entity represented.</p>
  <div class="agreement-meta">
    <div><span>Company Representative</span><strong>Jhon Joem Ramirez</strong></div>
    <div><span>Bondholder</span><strong>{{ $agreement['bondholder'] }}</strong></div>
    <div><span>Notary Public</span><strong>____________________________</strong></div>
  </div>
  <h3>BOND CERTIFICATE</h3>
  <p>Issued under the Bond Purchase Agreement</p>
  <div class="agreement-meta">
    <div><span>Certificate Number</span><strong>LOT-INV-{{ $agreement['reference'] ?? 'PENDING' }}</strong></div>
    <div><span>Contract Number</span><strong>{{ $agreement['contract_number'] ?? 'PENDING' }}</strong></div>
    <div><span>Bondholder</span><strong>{{ $agreement['bondholder'] }}</strong></div>
    <div><span>Bond Package</span><strong>{{ $agreement['package_name'] }}</strong></div>
    <div><span>Principal Amount</span><strong>{{ $agreement['amount'] }}</strong></div>
    <div><span>Daily Interest Rate</span><strong>{{ $agreement['daily_interest_rate'] }}</strong></div>
  </div>
</main>
<script>
  (function () {
    var pad = document.getElementById('agreementSignaturePad');
    var data = document.getElementById('agreementSignatureData');
    var context = pad.getContext('2d');
    var drawing = false;
    context.lineWidth = 4;
    context.lineCap = 'round';
    context.lineJoin = 'round';
    context.strokeStyle = '#17202a';
    function point(event) {
      var rect = pad.getBoundingClientRect();
      return { x: (event.clientX - rect.left) * pad.width / rect.width, y: (event.clientY - rect.top) * pad.height / rect.height };
    }
    function save() { data.value = pad.toDataURL('image/png'); }
    pad.addEventListener('pointerdown', function (event) { drawing = true; pad.setPointerCapture(event.pointerId); var start = point(event); context.beginPath(); context.moveTo(start.x, start.y); });
    pad.addEventListener('pointermove', function (event) { if (!drawing) return; var current = point(event); context.lineTo(current.x, current.y); context.stroke(); save(); });
    pad.addEventListener('pointerup', function () { drawing = false; save(); });
    document.getElementById('clearAgreementSignature').addEventListener('click', function () { context.clearRect(0, 0, pad.width, pad.height); data.value = ''; });
  })();
</script>
@endsection
