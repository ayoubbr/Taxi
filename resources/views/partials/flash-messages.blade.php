@if (session('success'))
    <div class="alert alert-success" id="flash-success">
        <div class="alert-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="alert-content">
            <div class="alert-title">Success!</div>
            <div class="alert-text">{{ session('success') }}</div>
        </div>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" id="flash-error">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="alert-content">
            <div class="alert-title">Error!</div>
            <div class="alert-text">{{ session('error') }}</div>
        </div>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning" id="flash-warning">
        <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="alert-content">
            <div class="alert-title">Warning!</div>
            <div class="alert-text">{{ session('warning') }}</div>
        </div>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info" id="flash-info">
        <div class="alert-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="alert-content">
            <div class="alert-title">Information</div>
            <div class="alert-text">{{ session('info') }}</div>
        </div>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" id="flash-validation">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="alert-content">
            <div class="alert-title">Validation Error</div>
            <div class="alert-text">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif
