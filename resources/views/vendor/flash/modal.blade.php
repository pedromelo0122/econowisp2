<div id="flash-overlay-modal" class="modal fade {{ $modalClass ?? '' }}" tabindex="-1" aria-labelledby="flashOverlayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5 fw-bold" id="flashOverlayModalLabel">{{ $title }}</h4>
    
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ t('Close') }}"></button>
            </div>
            
            <div class="modal-body">
                <p>{!! $body !!}</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ t('Close') }}</button>
            </div>
        </div>
    </div>
</div>
