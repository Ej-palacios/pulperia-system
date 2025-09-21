@props([
    'id' => null,
    'title' => '',
    'size' => 'medium',
    'closable' => true,
    'backdrop' => true,
    'keyboard' => true,
    'focus' => true,
    'centered' => true,
    'scrollable' => false,
    'static' => false
])

@php
    $modalId = $id ?? 'modal-' . uniqid();
    $sizes = [
        'small' => 'modal-sm',
        'medium' => '',
        'large' => 'modal-lg',
        'extra-large' => 'modal-xl'
    ];
    
    $modalClass = 'fluent-modal modal fade';
    if ($centered) $modalClass .= ' modal-centered';
    if ($scrollable) $modalClass .= ' modal-scrollable';
    if ($static) $modalClass .= ' modal-static';
    
    $dialogClass = 'modal-dialog ' . ($sizes[$size] ?? '');
@endphp

<div class="{{ $modalClass }}" 
     id="{{ $modalId }}" 
     tabindex="-1" 
     aria-labelledby="{{ $modalId }}-title" 
     aria-hidden="true"
     data-bs-backdrop="{{ $backdrop ? 'true' : 'false' }}"
     data-bs-keyboard="{{ $keyboard ? 'true' : 'false' }}"
     data-bs-focus="{{ $focus ? 'true' : 'false' }}">
    
    <div class="{{ $dialogClass }}">
        <div class="modal-content">
            @if($title || $closable)
                <div class="modal-header">
                    @if($title)
                        <h5 class="modal-title" id="{{ $modalId }}-title">
                            {{ $title }}
                        </h5>
                    @endif
                    
                    @if($closable)
                        <button type="button" 
                                class="modal-close" 
                                data-bs-dismiss="modal" 
                                aria-label="Cerrar">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            @endif
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.fluent-modal {
    z-index: 1055;
}

.fluent-modal .modal-dialog {
    margin: var(--fluent-space-lg);
    max-width: 500px;
}

.fluent-modal.modal-centered .modal-dialog {
    display: flex;
    align-items: center;
    min-height: calc(100% - (var(--fluent-space-lg) * 2));
}

.fluent-modal.modal-scrollable .modal-dialog {
    max-height: calc(100% - (var(--fluent-space-lg) * 2));
}

.fluent-modal.modal-scrollable .modal-content {
    max-height: 100%;
    overflow: hidden;
}

.fluent-modal.modal-scrollable .modal-body {
    overflow-y: auto;
}

.modal-content {
    background-color: var(--fluent-white);
    border: 1px solid var(--fluent-gray-30);
    border-radius: var(--fluent-radius-lg);
    box-shadow: var(--fluent-shadow-xl);
    overflow: hidden;
    position: relative;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--fluent-space-lg);
    border-bottom: 1px solid var(--fluent-gray-30);
    background-color: var(--fluent-gray-10);
}

.modal-title {
    margin: 0;
    font-size: var(--fluent-font-size-lg);
    font-weight: 600;
    color: var(--fluent-gray-120);
    line-height: 1.4;
}

.modal-close {
    background: none;
    border: none;
    color: var(--fluent-gray-80);
    cursor: pointer;
    padding: var(--fluent-space-sm);
    border-radius: var(--fluent-radius-sm);
    transition: all var(--fluent-transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.modal-close:hover {
    background-color: var(--fluent-gray-20);
    color: var(--fluent-gray-120);
}

.modal-close i {
    font-size: var(--fluent-font-size-sm);
}

.modal-body {
    padding: var(--fluent-space-lg);
    color: var(--fluent-gray-120);
    line-height: 1.5;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: var(--fluent-space-sm);
    padding: var(--fluent-space-lg);
    border-top: 1px solid var(--fluent-gray-30);
    background-color: var(--fluent-gray-10);
}

/* Modal sizes */
.modal-sm {
    max-width: 400px;
}

.modal-lg {
    max-width: 800px;
}

.modal-xl {
    max-width: 1200px;
}

/* Modal backdrop */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(2px);
}

.modal-backdrop.show {
    opacity: 1;
}

/* Modal animations */
.modal.fade .modal-dialog {
    transition: transform var(--fluent-transition-normal) ease-out;
    transform: translate(0, -50px);
}

.modal.show .modal-dialog {
    transform: none;
}

.modal.fade .modal-backdrop {
    transition: opacity var(--fluent-transition-normal) ease-out;
}

/* Loading state */
.modal-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.modal-loading .loading-spinner {
    text-align: center;
}

.modal-loading .spinner {
    width: 32px;
    height: 32px;
    border: 3px solid var(--fluent-gray-30);
    border-top: 3px solid var(--fluent-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto var(--fluent-space-md);
}

.modal-loading .loading-text {
    color: var(--fluent-gray-80);
    font-size: var(--fluent-font-size-sm);
    font-weight: 500;
}

/* Form modals */
.modal-body .fluent-form-group:last-child {
    margin-bottom: 0;
}

/* Alert modals */
.modal-body .fluent-alert:last-child {
    margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .fluent-modal .modal-dialog {
        margin: var(--fluent-space-md);
        max-width: none;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding: var(--fluent-space-md);
    }
    
    .modal-title {
        font-size: var(--fluent-font-size-base);
    }
    
    .modal-footer {
        flex-direction: column;
        align-items: stretch;
    }
    
    .modal-footer .fluent-btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .fluent-modal .modal-dialog {
        margin: var(--fluent-space-sm);
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding: var(--fluent-space-sm);
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .modal-content {
        background-color: var(--fluent-gray-20);
        border-color: var(--fluent-gray-40);
    }
    
    .modal-header,
    .modal-footer {
        background-color: var(--fluent-gray-30);
        border-color: var(--fluent-gray-40);
    }
    
    .modal-title {
        color: var(--fluent-gray-140);
    }
    
    .modal-body {
        color: var(--fluent-gray-140);
    }
    
    .modal-loading {
        background-color: rgba(26, 26, 26, 0.8);
    }
}

/* Print styles */
@media print {
    .fluent-modal {
        display: none !important;
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced modal functionality
    const modals = document.querySelectorAll('.fluent-modal');
    
    modals.forEach(modal => {
        // Auto-focus first input when modal opens
        modal.addEventListener('shown.bs.modal', function() {
            const firstInput = this.querySelector('input, select, textarea, button');
            if (firstInput && firstInput.type !== 'hidden') {
                firstInput.focus();
            }
        });
        
        // Clear form validation when modal closes
        modal.addEventListener('hidden.bs.modal', function() {
            const forms = this.querySelectorAll('form');
            forms.forEach(form => {
                const inputs = form.querySelectorAll('.is-invalid, .is-valid');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid', 'is-valid');
                });
                
                const feedbacks = form.querySelectorAll('.invalid-feedback, .valid-feedback');
                feedbacks.forEach(feedback => {
                    feedback.remove();
                });
            });
        });
        
        // Handle escape key
        modal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && this.classList.contains('show')) {
                const modalInstance = bootstrap.Modal.getInstance(this);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        });
    });
    
    // Global modal helpers
    window.showModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }
    };
    
    window.hideModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    };
    
    window.showLoadingModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const content = modal.querySelector('.modal-content');
            if (content) {
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'modal-loading';
                loadingDiv.innerHTML = `
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <div class="loading-text">Procesando...</div>
                    </div>
                `;
                content.appendChild(loadingDiv);
            }
        }
    };
    
    window.hideLoadingModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const loading = modal.querySelector('.modal-loading');
            if (loading) {
                loading.remove();
            }
        }
    };
});
</script>

