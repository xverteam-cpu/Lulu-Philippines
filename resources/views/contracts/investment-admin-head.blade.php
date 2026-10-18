<script>
  window.LuluContractInitialData = @json($initialData);
  window.LuluContractPrintOnLoad = @json($autoPrint);
</script>
<style>
  body.contract-admin-mode .header-actions,
  body.contract-admin-mode .intro,
  body.contract-admin-mode .form-column { display:none !important; }
  body.contract-admin-mode .main { max-width:1240px; }
  body.contract-admin-mode .workspace { display:block; }
  body.contract-admin-mode .preview-column { position:static; width:100%; max-width:1040px; margin:24px auto 0; }
  body.contract-admin-mode .preview-card { width:100%; }
  body.contract-admin-mode .preview-options,
  body.contract-admin-mode .preview-footer { display:none; }
  .admin-contract-toolbar { display:flex; max-width:1200px; justify-content:space-between; align-items:center; gap:16px; margin:24px auto 0; padding:0 18px; font-family:Inter,system-ui,sans-serif; }
  .admin-contract-toolbar a { color:#166534; font-weight:700; }
  .admin-contract-toolbar button { padding:9px 14px; border:1px solid #cbdacf; border-radius:9px; background:#fff; color:#17623b; font-weight:750; cursor:pointer; }
  .admin-contract-record { width:min(100% - 32px,1200px); margin:14px auto 0; padding:18px; border:1px solid #d9e5dc; border-radius:14px; background:#fff; color:#17202a; font-family:Inter,system-ui,sans-serif; }
  .admin-contract-record h2 { margin:0 0 14px; font-size:18px; }
  .admin-contract-record-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px 20px; }
  .admin-contract-record-item span { display:block; color:#64736b; font-size:12px; }
  .admin-contract-record-item strong { display:block; overflow-wrap:anywhere; margin-top:3px; font-size:14px; }
  @media(max-width:600px) { .admin-contract-record-grid { grid-template-columns:1fr; } }
  @media print {
    .admin-contract-toolbar,.admin-contract-record { display:none !important; }
    body.contract-admin-mode .main { max-width:none; }
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('contract-admin-mode');
    document.getElementById('saveDraft')?.remove();
    document.getElementById('downloadOriginal')?.remove();
  });
</script>
