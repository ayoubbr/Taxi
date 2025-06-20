@extends('super-admin.layout')

@section('title', 'Créer une Agence')

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.agencies.index') }}">Agences</a>
    <i class="fas fa-chevron-right"></i>
    <span>Créer une Agence</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-forms.css') }}">
@endsection


@section('content')
    <div class="create-agency-page">
        <!-- Page Header -->
        <div class="page-header-card">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="header-info">
                        <h1>Créer une Nouvelle Agence</h1>
                        <p>Ajoutez une nouvelle agence de taxi au système</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('super-admin.agencies.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <!-- Create Form -->
        <div class="form-container">
            <form action="{{ route('super-admin.agencies.store') }}" method="POST" enctype="multipart/form-data"
                class="agency-form">
                @csrf

                <div class="form-grid">
                    <!-- Basic Information Card -->
                    <div class="form-card">
                        <div class="card-header">
                            <h3><i class="fas fa-info-circle"></i> Informations de Base</h3>
                            <p>Informations principales de l'agence</p>
                        </div>
                        <div class="card-content">
                            <div class="form-group">
                                <label for="name" class="form-label required">
                                    <i class="fas fa-building"></i>
                                    Nom de l'Agence
                                </label>
                                <input type="text" id="name" name="name"
                                    class="form-input @error('name') error @enderror" value="{{ old('name') }}"
                                    placeholder="Ex: Taxi Express Casablanca" maxlength="150" required>
                                @error('name')
                                    <span class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <div class="input-help">
                                    <span class="char-count" data-target="name">0/150 caractères</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Adresse Complète
                                </label>
                                <textarea id="address" name="address" class="form-textarea @error('address') error @enderror" rows="4"
                                    placeholder="Adresse complète de l'agence...">{{ old('address') }}</textarea>
                                @error('address')
                                    <span class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label required">
                                    <i class="fas fa-toggle-on"></i>
                                    Statut Initial
                                </label>
                                <select id="status" name="status" class="form-select @error('status') error @enderror"
                                    required>
                                    <option value="">Sélectionner un statut</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>
                                        <i class="fas fa-check-circle"></i> Actif
                                    </option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                                        <i class="fas fa-pause-circle"></i> Inactif
                                    </option>
                                    <option value="suspendu" {{ old('status') === 'suspendu' ? 'selected' : '' }}>
                                        <i class="fas fa-ban"></i> Suspendu
                                    </option>
                                </select>
                                @error('status')
                                    <span class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <div class="input-help">
                                    <div class="status-help">
                                        <div class="status-item">
                                            <span class="status-badge status-active">Actif</span>
                                            <span>L'agence peut opérer normalement</span>
                                        </div>
                                        <div class="status-item">
                                            <span class="status-badge status-inactive">Inactif</span>
                                            <span>L'agence est temporairement désactivée</span>
                                        </div>
                                        <div class="status-item">
                                            <span class="status-badge status-banned">Banni</span>
                                            <span>L'agence est bannie du système</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo Upload Card -->
                    <div class="form-card">
                        <div class="card-header">
                            <h3><i class="fas fa-image"></i> Logo de l'Agence</h3>
                        </div>
                        <div class="card-content">
                            <div class="form-group">
                                <label for="logo" class="form-label required">
                                    <i class="fas fa-upload"></i>
                                    Logo de l'Agence
                                </label>
                                <div class="file-upload-area" id="fileUploadArea">
                                    <input type="file" id="logo" name="logo"
                                        class="file-input @error('logo') error @enderror"
                                        accept="image/jpeg,image/png,image/jpg" required>
                                    <div class="upload-placeholder" id="uploadPlaceholder">
                                        <div class="upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <div class="upload-text">
                                            <h4>Glissez votre logo ici</h4>
                                            <p>ou <span class="upload-link">cliquez pour parcourir</span></p>
                                            <small>PNG, JPG, JPEG jusqu'à 2MB</small>
                                        </div>
                                    </div>
                                    <div class="upload-preview" id="uploadPreview" style="display: none;">
                                        <img id="previewImage" src="/placeholder.svg" alt="Aperçu du logo">
                                        <div class="preview-overlay">
                                            <button type="button" class="remove-image" id="removeImage">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="preview-info">
                                            <span id="fileName"></span>
                                            <span id="fileSize"></span>
                                        </div>
                                    </div>
                                </div>
                                @error('logo')
                                    <span class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <div class="input-help">
                                    <ul class="upload-requirements">
                                        <li><i class="fas fa-check"></i> Format: PNG, JPG, JPEG</li>
                                        <li><i class="fas fa-check"></i> Taille maximale: 2MB</li>
                                        <li><i class="fas fa-check"></i> Dimensions recommandées: 200x200px</li>
                                        <li><i class="fas fa-check"></i> Fond transparent de préférence</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <div class="actions-left">
                        <button type="button" class="btn btn-outline" onclick="resetForm()">
                            <i class="fas fa-undo"></i>
                            Réinitialiser
                        </button>
                    </div>
                    <div class="actions-right">
                        <a href="{{ route('super-admin.agencies.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span class="btn-text">Créer l'Agence</span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                                Création...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="help-section">
            <div class="help-card">
                <div class="help-header">
                    <i class="fas fa-question-circle"></i>
                    <h3>Aide pour la Création d'Agence</h3>
                </div>
                <div class="help-content">
                    <div class="help-item">
                        <h4><i class="fas fa-building"></i> Nom de l'Agence</h4>
                        <p>Choisissez un nom unique et descriptif pour l'agence. Ce nom sera visible par les clients.</p>
                    </div>
                    <div class="help-item">
                        <h4><i class="fas fa-image"></i> Logo</h4>
                        <p>Le logo doit être professionnel et représentatif de l'agence. Il sera affiché dans l'application.
                        </p>
                    </div>
                    <div class="help-item">
                        <h4><i class="fas fa-toggle-on"></i> Statut</h4>
                        <p>Le statut détermine si l'agence peut opérer. Vous pouvez le modifier plus tard.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/super-admin-forms.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counter
            const nameInput = document.getElementById('name');
            const charCount = document.querySelector('[data-target="name"]');

            nameInput.addEventListener('input', function() {
                const count = this.value.length;
                charCount.textContent = `${count}/150 caractères`;

                if (count > 140) {
                    charCount.style.color = 'var(--warning-color)';
                } else if (count > 120) {
                    charCount.style.color = 'var(--info-color)';
                } else {
                    charCount.style.color = 'var(--text-light)';
                }
            });

            // File upload handling
            const fileInput = document.getElementById('logo');
            const uploadArea = document.getElementById('fileUploadArea');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const uploadPreview = document.getElementById('uploadPreview');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const removeImage = document.getElementById('removeImage');

            // Click to upload
            uploadPlaceholder.addEventListener('click', () => fileInput.click());

            // Drag and drop
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('drag-over');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('drag-over');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('drag-over');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            // Remove image
            removeImage.addEventListener('click', () => {
                fileInput.value = '';
                uploadPlaceholder.style.display = 'block';
                uploadPreview.style.display = 'none';
                uploadArea.classList.remove('has-file');
            });

            function handleFileSelect(file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format de fichier non supporté. Utilisez PNG, JPG ou JPEG.');
                    return;
                }

                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux. Taille maximale: 2MB.');
                    return;
                }

                // Create file reader
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);

                    uploadPlaceholder.style.display = 'none';
                    uploadPreview.style.display = 'block';
                    uploadArea.classList.add('has-file');
                };
                reader.readAsDataURL(file);
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Form submission
            const form = document.querySelector('.agency-form');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-flex';
            });
        });

        function resetForm() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')) {
                document.querySelector('.agency-form').reset();
                document.getElementById('uploadPlaceholder').style.display = 'block';
                document.getElementById('uploadPreview').style.display = 'none';
                document.getElementById('fileUploadArea').classList.remove('has-file');
                document.querySelector('[data-target="name"]').textContent = '0/150 caractères';
            }
        }
    </script>
@endsection
