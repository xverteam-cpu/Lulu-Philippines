<script>
  (() => {
    const purchase = @json($purchase);
    const bondholder = @json($bondholder);
    const csrfToken = @json(csrf_token());
    const submitUrl = @json(route('investments.store'));

    document.addEventListener('DOMContentLoaded', () => {
      const contract = document.getElementById('contractForm');
      const nameInput = document.getElementById('fullName');
      const finalStep = document.querySelector('.form-step[data-panel="2"] .form-navigation');
      const privacyNote = document.querySelector('.privacy-note p');

      if (!contract || !nameInput || !finalStep) {
        console.error('The bond agreement form is missing required signing controls.');
        return;
      }

      if (!nameInput.value.trim()) {
        nameInput.value = bondholder;
        nameInput.dispatchEvent(new Event('input', { bubbles: true }));
      }

      const summary = document.createElement('section');
      summary.className = 'purchase-summary';
      summary.setAttribute('aria-label', 'Bond purchase summary');
      summary.style.cssText = 'max-width:920px;margin:0 auto 18px;padding:16px 20px;border:1px solid #dce8df;border-radius:16px;background:#f0f8f2;color:#1b2c24;font:14px/1.5 Arial,Helvetica,sans-serif';
      const heading = document.createElement('strong');
      heading.textContent = 'Bond purchase to complete';
      const details = document.createElement('p');
      details.style.margin = '6px 0 0';
      details.textContent = `${purchase.package_name} · ${new Intl.NumberFormat('en-PH', { style: 'currency', currency: purchase.currency }).format(Number(purchase.amount))}`;
      summary.append(heading, details);
      contract.parentNode.insertBefore(summary, contract);

      const submitButton = document.createElement('button');
      submitButton.id = 'lulu-submit-purchase';
      submitButton.className = 'button primary';
      submitButton.type = 'button';
      submitButton.textContent = 'Submit signed agreement';
      finalStep.insertBefore(submitButton, document.getElementById('downloadFinal'));

      if (privacyNote) {
        privacyNote.textContent = 'Your personal and ID details stay in this browser. Submitting your signed agreement sends your bond selection, account name, and signature to LuLu to record the investment.';
      }

      const error = document.createElement('p');
      error.id = 'lulu-signing-error';
      error.setAttribute('role', 'alert');
      error.style.cssText = 'flex-basis:100%;margin:8px 0 0;color:#a1342e;font:13px/1.5 Arial,Helvetica,sans-serif';
      finalStep.appendChild(error);

      submitButton.addEventListener('click', async () => {
        error.textContent = '';

        const requiredIds = ['fullName', 'civilStatus', 'address', 'signDate', 'idType', 'idNumber'];
        if (document.getElementById('civilStatus')?.value === 'Other') requiredIds.push('civilOther');
        if (document.getElementById('idType')?.value === 'Other') requiredIds.push('idOther');
        if (!document.getElementById('noIssueDate')?.checked) requiredIds.push('issueDate');

        const missing = requiredIds.find((id) => !document.getElementById(id)?.value.trim());
        if (missing || !document.getElementById('confirmed')?.checked) {
          error.textContent = 'Complete the agreement details and confirm the declaration before submitting.';
          document.getElementById(missing || 'confirmed')?.focus();
          return;
        }

        const signaturePad = document.getElementById('signaturePad');
        const upload = document.getElementById('uploadPreview');
        const selectedMode = document.querySelector('[data-mode].selected')?.dataset.mode;
        const signatureSource = selectedMode === 'upload' && upload && !upload.closest('[hidden]')
          ? upload
          : selectedMode === 'draw'
            ? signaturePad
            : null;

        if (!signatureSource || (signatureSource instanceof HTMLCanvasElement && !hasSignature(signatureSource))) {
          error.textContent = 'Draw or upload your signature to submit the signed agreement.';
          return;
        }

        submitButton.disabled = true;
        try {
          const signatureData = await signatureAsPng(signatureSource);
          const form = document.createElement('form');
          form.method = 'post';
          form.action = submitUrl;
          form.hidden = true;

          const fields = {
            _token: csrfToken,
            package: purchase.package,
            investment_id: purchase.investment_id || '',
            amount: String(purchase.amount),
            currency: purchase.currency,
            payment_method: purchase.payment_method,
            agreement_signature_name: document.getElementById('fullName').value.trim(),
            agreement_signature_data: signatureData,
            agreement_accepted: '1',
            'agreement_details[full_name]': document.getElementById('fullName').value.trim(),
            'agreement_details[civil_status]': document.getElementById('civilStatus').value,
            'agreement_details[civil_status_other]': document.getElementById('civilOther').value.trim(),
            'agreement_details[address]': document.getElementById('address').value.trim(),
            'agreement_details[sign_date]': document.getElementById('signDate').value,
            'agreement_details[id_type]': document.getElementById('idType').value,
            'agreement_details[id_type_other]': document.getElementById('idOther').value.trim(),
            'agreement_details[id_number]': document.getElementById('idNumber').value.trim(),
            'agreement_details[issue_date]': document.getElementById('noIssueDate').checked
              ? ''
              : document.getElementById('issueDate').value,
            'agreement_details[no_issue_date]': document.getElementById('noIssueDate').checked ? '1' : '0',
          };

          Object.entries(fields).forEach(([name, value]) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
          });

          document.body.appendChild(form);
          form.submit();
        } catch (submissionError) {
          console.error('Unable to prepare the signed agreement submission.', submissionError);
          error.textContent = 'Your signature could not be prepared. Please try drawing or uploading it again.';
          submitButton.disabled = false;
        }
      });
    });

    function hasSignature(canvas) {
      const pixels = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data;
      for (let index = 3; index < pixels.length; index += 4) {
        if (pixels[index] !== 0) return true;
      }
      return false;
    }

    function signatureAsPng(source) {
      return new Promise((resolve, reject) => {
        const output = document.createElement('canvas');
        output.width = 900;
        output.height = 270;
        const context = output.getContext('2d');
        if (!context) {
          reject(new Error('Canvas is unavailable.'));
          return;
        }

        const render = (image) => {
          context.drawImage(image, 0, 0, output.width, output.height);
          resolve(output.toDataURL('image/png'));
        };

        if (source instanceof HTMLCanvasElement) {
          render(source);
          return;
        }

        if (source.complete && source.naturalWidth) {
          render(source);
          return;
        }

        source.addEventListener('load', () => render(source), { once: true });
        source.addEventListener('error', () => reject(new Error('Signature image could not be loaded.')), { once: true });
      });
    }
  })();
</script>
