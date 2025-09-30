@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
@endphp

<div class="card" style="max-width:520px;">
  <div class="card-body">
    <h4 class="mb-3">Verificación de teléfono</h4>

    <div class="mb-2">
      <strong>Teléfono actual:</strong>
      <span id="pv_phone_show">{{ $user?->phone }}</span>
    </div>

    @if($user?->phone_verified_at)
      <div class="alert alert-success py-2">
        ✅ Teléfono verificado el
        {{ optional($user->phone_verified_at)->timezone('America/Santo_Domingo')->format('Y-m-d H:i') }}
      </div>
    @else
      <div class="alert alert-warning py-2">⚠️ Teléfono no verificado.</div>

      <div class="mb-3">
        <label for="pv_phone" class="form-label">Teléfono (E.164)</label>
        <input type="tel" id="pv_phone" class="form-control" value="{{ $user?->phone }}" placeholder="+1809..." />
      </div>

      <div class="d-flex gap-2 mb-3">
        <button id="pv_btn_sms" class="btn btn-primary" type="button" onclick="pvSend('sms')">Enviar código por SMS</button>
        <button id="pv_btn_wa" class="btn btn-outline-success" type="button" onclick="pvSend('whatsapp')">Usar WhatsApp</button>
      </div>

      <div class="mb-3">
        <label for="pv_code" class="form-label">Código recibido</label>
        <input type="text" id="pv_code" class="form-control" placeholder="123456" />
      </div>

      <button id="pv_btn_check" class="btn btn-success" type="button" onclick="pvCheck()">Verificar</button>
    @endif

    <pre id="pv_result" style="white-space:pre-wrap;margin-top:12px;"></pre>
  </div>
</div>

<script>
let pvVE = null;

async function pvSend(channel = 'sms') {
  const btnSms = document.getElementById('pv_btn_sms');
  const btnWa  = document.getElementById('pv_btn_wa');
  const phone  = (document.getElementById('pv_phone')?.value || '').trim();

  btnSms?.setAttribute('disabled', 'disabled');
  btnWa?.setAttribute('disabled', 'disabled');
  pvSetResult('Enviando código...');

  try {
    const res = await fetch('/verify/otp/start', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ phone, channel })
    });
    const data = await res.json();
    pvVE = data.sid || null;

    if (data.already_verified) {
      pvSetResult('✅ Ya estaba verificado. No se envió SMS.');
    } else if (data.status === 'pending') {
      pvSetResult(`Código enviado por ${data.channel}. VE=${pvVE || 'N/A'}`);
    } else {
      pvSetResult('Respuesta: ' + JSON.stringify(data, null, 2));
    }
  } catch (e) {
    pvSetResult('Error: ' + e.message);
  } finally {
    btnSms?.removeAttribute('disabled');
    btnWa?.removeAttribute('disabled');
  }
}

async function pvCheck() {
  const btn = document.getElementById('pv_btn_check');
  const code = (document.getElementById('pv_code')?.value || '').trim();
  const phone = (document.getElementById('pv_phone')?.value || '').trim();

  if (!code) {
    pvSetResult('Ingresa el código recibido.');
    return;
  }

  btn?.setAttribute('disabled', 'disabled');
  pvSetResult('Verificando código...');

  try {
    const payload = pvVE ? { verification_sid: pvVE, code } : { phone, code };
    const res = await fetch('/verify/otp/check', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (data.status === 'approved' && data.valid === true) {
      pvSetResult('✅ Verificado correctamente. Recarga la página para ver el estado actualizado.');
    } else {
      pvSetResult('Respuesta: ' + JSON.stringify(data, null, 2));
    }
  } catch (e) {
    pvSetResult('Error: ' + e.message);
  } finally {
    btn?.removeAttribute('disabled');
  }
}

function pvSetResult(msg) {
  const el = document.getElementById('pv_result');
  if (el) el.textContent = msg;
}
</script>
