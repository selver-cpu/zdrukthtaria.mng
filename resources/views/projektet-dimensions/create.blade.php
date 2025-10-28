@extends('layouts.app')

@section('title', 'Shto Dimension të Ri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-plus"></i> Shto Dimension të Ri</h4>
                    <a href="{{ route('projektet-dimensions.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kthehu</a>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('projektet-dimensions.store') }}" method="POST" id="dimensionForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="projekt_id">Projekti <span class="text-danger">*</span></label>
                                    <select name="projekt_id" id="projekt_id" class="form-control" required>
                                        <option value="">-- Zgjidh Projektin --</option>
                                        @foreach($projektet as $p)
                                            <option value="{{ $p->projekt_id }}" {{ old('projekt_id', request('projekt_id')) == $p->projekt_id ? 'selected' : '' }}>{{ $p->emri_projektit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emri_pjeses">Emri i Pjesës <span class="text-danger">*</span></label>
                                    <input type="text" name="emri_pjeses" id="emri_pjeses" class="form-control" value="{{ old('emri_pjeses') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gjatesia">Gjatësia</label>
                                    <input type="number" step="0.01" name="gjatesia" id="gjatesia" class="form-control" value="{{ old('gjatesia') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gjeresia">Gjerësia</label>
                                    <input type="number" step="0.01" name="gjeresia" id="gjeresia" class="form-control" value="{{ old('gjeresia') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="trashesia">Trashësia</label>
                                    <input type="number" step="0.01" name="trashesia" id="trashesia" class="form-control" value="{{ old('trashesia') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="njesi_matese">Njësia Matëse</label>
                                    <select name="njesi_matese" id="njesi_matese" class="form-control">
                                        <option value="mm" {{ old('njesi_matese', 'mm') == 'mm' ? 'selected' : '' }}>mm</option>
                                        <option value="cm" {{ old('njesi_matese') == 'cm' ? 'selected' : '' }}>cm</option>
                                        <option value="m" {{ old('njesi_matese') == 'm' ? 'selected' : '' }}>m</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sasia">Sasia</label>
                                    <input type="number" min="1" step="1" name="sasia" id="sasia" class="form-control" value="{{ old('sasia', 1) }}" required>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Materiali</label>
                                    <div class="input-group">
                                        <select name="materiali_id" id="materiali_id" class="form-control">
                                            <option value="">-- Zgjidh Materialin --</option>
                                            @foreach($materialet as $m)
                                                <option value="{{ $m->material_id }}" {{ old('materiali_id') == $m->material_id ? 'selected' : '' }}>{{ $m->emri_materialit }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append input-group-prepend">
                                            <span class="input-group-text">ose</span>
                                        </div>
                                        <input type="text" name="materiali_personal" id="materiali_personal" class="form-control" placeholder="Material personal" value="{{ old('materiali_personal') }}">
                                        <div class="input-group-append">
                                            <button type="button" id="btnCheckStock" class="btn btn-outline-info"><i class="fas fa-warehouse"></i> Kontrollo Stokun</button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Zgjidh nga lista ose shkruaj material personal.</small>
                                </div>
                                <div id="stockResult" class="mt-2" style="display:none;"></div>
                                <div id="stockWarning" class="mt-2" style="display:none;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="kantim_needed" name="kantim_needed" value="1" {{ old('kantim_needed') ? 'checked' : '' }}>
                            <label class="form-check-label" for="kantim_needed">Kërkohet Kantim</label>
                        </div>

                        <div id="kantimFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kantim_type">Lloji i Kantimit</label>
                                        <select name="kantim_type" id="kantim_type" class="form-control">
                                            <option value="">-- Zgjidh --</option>
                                            @foreach(['PVC','ABS','Wood Veneer','Aluminum'] as $type)
                                                <option value="{{ $type }}" {{ old('kantim_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kantim_thickness">Trashësia e Kantimit (mm)</label>
                                        <input type="number" step="0.01" name="kantim_thickness" id="kantim_thickness" class="form-control" value="{{ old('kantim_thickness') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kantim_corners">Qoshet</label>
                                        <select name="kantim_corners" id="kantim_corners" class="form-control">
                                            <option value="square" {{ old('kantim_corners','square') == 'square' ? 'selected' : '' }}>Katror</option>
                                            <option value="rounded" {{ old('kantim_corners') == 'rounded' ? 'selected' : '' }}>Rrumbullakët</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kantim_front" id="kantim_front" value="1" {{ old('kantim_front') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kantim_front">Përpara</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kantim_left" id="kantim_left" value="1" {{ old('kantim_left') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kantim_left">Majtas</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kantim_right" id="kantim_right" value="1" {{ old('kantim_right') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kantim_right">Djathtas</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kantim_back" id="kantim_back" value="1" {{ old('kantim_back') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kantim_back">Pas</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="pershkrimi">Përshkrimi / Udhëzime</label>
                            <textarea name="pershkrimi" id="pershkrimi" rows="4" class="form-control">{{ old('pershkrimi') }}</textarea>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ruaj</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleKantimFields() {
    const checked = document.getElementById('kantim_needed').checked;
    document.getElementById('kantimFields').style.display = checked ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    toggleKantimFields();
    document.getElementById('kantim_needed').addEventListener('change', toggleKantimFields);

    // Auto-check stock when material or quantity changes
    const materialSelect = document.getElementById('materiali_id');
    const quantityInput = document.getElementById('sasia');
    const dimensionInputs = ['gjatesia', 'gjeresia', 'trashesia'];

    function checkStock() {
        const materiali_id = materialSelect.value;
        if (!materiali_id) {
            hideStockResults();
            return;
        }

        const gjatesia = document.getElementById('gjatesia').value || 0;
        const gjeresia = document.getElementById('gjeresia').value || 0;
        const trashesia = document.getElementById('trashesia').value || 0;
        const sasia = quantityInput.value || 1;

        fetch(`{{ route('projektet-dimensions.check-stock') }}?materiali_id=${materiali_id}&gjatesia=${gjatesia}&gjeresia=${gjeresia}&trashesia=${trashesia}&sasia=${sasia}`)
            .then(r => r.json())
            .then(data => {
                showStockResults(data, sasia);
            })
            .catch(() => {
                hideStockResults();
            });
    }

    function showStockResults(data, sasia) {
        const resultEl = document.getElementById('stockResult');
        const warningEl = document.getElementById('stockWarning');

        if (data.error) {
            resultEl.style.display = 'block';
            resultEl.className = 'alert alert-danger';
            resultEl.innerText = data.error;
            warningEl.style.display = 'none';
        } else {
            resultEl.style.display = 'block';
            resultEl.className = 'alert ' + (data.ka_stok ? 'alert-success' : 'alert-warning');
            resultEl.innerHTML = `Stok i disponueshëm: <strong>${data.stok_disponueshem}</strong> | Sasia e nevojitur: <strong>${data.sasia_nevojitur}</strong>`;

            // Show warning if insufficient stock
            if (!data.ka_stok) {
                warningEl.style.display = 'block';
                warningEl.className = 'alert alert-danger';
                warningEl.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <strong>Kujdes!</strong> Sasia e nevojitur (${data.sasia_nevojitur}) tejkalon stokun e disponueshëm (${data.stok_disponueshem}).`;
            } else {
                warningEl.style.display = 'none';
            }
        }
    }

    function hideStockResults() {
        document.getElementById('stockResult').style.display = 'none';
        document.getElementById('stockWarning').style.display = 'none';
    }

    // Add event listeners
    materialSelect.addEventListener('change', checkStock);
    quantityInput.addEventListener('input', checkStock);
    dimensionInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) input.addEventListener('input', checkStock);
    });

    document.getElementById('btnCheckStock').addEventListener('click', function() {
        const materiali_id = document.getElementById('materiali_id').value;
        const gjatesia = document.getElementById('gjatesia').value;
        const gjeresia = document.getElementById('gjeresia').value;
        const trashesia = document.getElementById('trashesia').value;
        const sasia = document.getElementById('sasia').value;

        if (!materiali_id) {
            alert('Zgjidh një material nga lista për të kontrolluar stokun.');
            return;
        }

        fetch(`{{ route('projektet-dimensions.check-stock') }}?materiali_id=${materiali_id}&gjatesia=${gjatesia}&gjeresia=${gjeresia}&trashesia=${trashesia}&sasia=${sasia}`)
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('stockResult');
                el.style.display = 'block';
                if (data.error) {
                    el.className = 'alert alert-danger';
                    el.innerText = data.error;
                } else {
                    el.className = 'alert ' + (data.ka_stok ? 'alert-success' : 'alert-warning');
                    el.innerHTML = `Stok i disponueshëm: <strong>${data.stok_disponueshem}</strong> | Sasia e nevojitur: <strong>${data.sasia_nevojitur}</strong>`;
                }
            })
            .catch(() => {
                const el = document.getElementById('stockResult');
                el.style.display = 'block';
                el.className = 'alert alert-danger';
                el.innerText = 'Nuk u arrit të kontrollohet stoku.';
            });
    });
});
</script>
@endpush
