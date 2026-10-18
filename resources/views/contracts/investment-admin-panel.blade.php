<div class="admin-contract-toolbar">
  <a href="{{ route('admin.contracts') }}">Back to Client Contracts</a>
  <button type="button" onclick="window.LuluContractApp?.printDocument()">Print contract</button>
</div>
<section class="admin-contract-record" aria-label="Submitted client contract details">
  <h2>Client-submitted details</h2>
  <div class="admin-contract-record-grid">
    <div class="admin-contract-record-item"><span>Client account</span><strong>{{ $investment->user->name }} · {{ $investment->user->email }}</strong></div>
    <div class="admin-contract-record-item"><span>Bondholder name</span><strong>{{ $agreementDetails['full_name'] }}</strong></div>
    <div class="admin-contract-record-item"><span>Civil status</span><strong>{{ $agreementDetails['civil_status'] === 'Other' ? ($agreementDetails['civil_status_other'] ?? 'Other') : $agreementDetails['civil_status'] }}</strong></div>
    <div class="admin-contract-record-item"><span>Residential address</span><strong>{{ $agreementDetails['address'] }}</strong></div>
    <div class="admin-contract-record-item"><span>Identification</span><strong>{{ $agreementDetails['id_type'] === 'Other' ? ($agreementDetails['id_type_other'] ?: 'Other') : $agreementDetails['id_type'] }} · {{ $agreementDetails['id_number'] }}</strong></div>
    <div class="admin-contract-record-item"><span>ID issue date</span><strong>{{ !empty($agreementDetails['no_issue_date']) ? 'Not shown on ID' : ($agreementDetails['issue_date'] ?: 'Not provided') }}</strong></div>
    <div class="admin-contract-record-item"><span>Contract signature date</span><strong>{{ $agreementDetails['sign_date'] }}</strong></div>
    <div class="admin-contract-record-item"><span>Investment</span><strong>{{ $investment->package_name }} · ${{ number_format((float) $investment->amount, 2) }} · {{ $investment->duration_days }} days</strong></div>
  </div>
</section>
