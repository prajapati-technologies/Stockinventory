@extends('layouts.app')

@section('title', 'Edit Condition for Stock Allotment')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Edit Condition for Stock Allotment</h1>
            <a href="{{ route('admin.stock-allotment-conditions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Conditions
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #e67e22, #d35400); width: 50px; height: 50px;">
                    <i class="fas fa-cog"></i>
                </div>
                <h5 class="mb-0">Edit condition for stock allotment - {{ $condition->district->name }}</h5>
            </div>
            
            <form method="POST" action="{{ route('admin.stock-allotment-conditions.update', $condition) }}" id="conditionForm">
                @csrf
                @method('PUT')
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Select District <span class="text-danger">*</span></label>
                        <select name="district_id" id="district_id" required 
                                class="form-select @error('district_id') is-invalid @enderror" disabled>
                            <option value="{{ $condition->district_id }}" selected>
                                {{ $condition->district->name }}
                            </option>
                        </select>
                        <input type="hidden" name="district_id" value="{{ $condition->district_id }}">
                        @error('district_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="conditionsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Ext (From)</th>
                                <th style="width: 15%;">To</th>
                                <th style="width: 18%;">No. of bags</th>
                                <th style="width: 18%;">At a time 'How many'</th>
                                <th style="width: 18%;">Interval time (Days)</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="conditionsBody">
                            @foreach($conditions as $index => $cond)
                            <tr class="condition-row">
                                <td>
                                    <input type="number" 
                                           name="conditions[{{ $index }}][land_extent_from]" 
                                           class="form-control" 
                                           step="0.01" 
                                           min="0" 
                                           value="{{ old("conditions.$index.land_extent_from", $cond->land_extent_from) }}" 
                                           required>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="conditions[{{ $index }}][land_extent_to]" 
                                           class="form-control" 
                                           step="0.01" 
                                           min="0" 
                                           value="{{ old("conditions.$index.land_extent_to", $cond->land_extent_to) }}" 
                                           required>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="conditions[{{ $index }}][number_of_bags]" 
                                           class="form-control" 
                                           min="1" 
                                           value="{{ old("conditions.$index.number_of_bags", $cond->number_of_bags) }}" 
                                           required>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="conditions[{{ $index }}][at_a_time_how_many]" 
                                           class="form-control" 
                                           min="1" 
                                           value="{{ old("conditions.$index.at_a_time_how_many", $cond->at_a_time_how_many) }}" 
                                           required>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="conditions[{{ $index }}][interval_time_days]" 
                                               class="form-control" 
                                               min="0" 
                                               value="{{ old("conditions.$index.interval_time_days", $cond->interval_time_days) }}" 
                                               required>
                                        @if($loop->last)
                                        <button type="button" class="btn btn-outline-primary" onclick="addRow(this)">
                                            <i class="fas fa-plus"></i> Add Row
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($conditions->count() > 1)
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.stock-allotment-conditions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>SAVE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let rowIndex = {{ $conditions->count() }};

function addRow(button) {
    const tbody = document.getElementById('conditionsBody');
    const row = button.closest('tr');
    const newRow = row.cloneNode(true);
    
    // Update input names with new index
    const inputs = newRow.querySelectorAll('input');
    inputs.forEach(input => {
        const name = input.name;
        if (name && !name.includes('district_id')) {
            input.name = name.replace(/\[\d+\]/, `[${rowIndex}]`);
            input.value = '';
        }
    });
    
    // Update values for next range
    const fromInput = newRow.querySelector('input[name*="[land_extent_from]"]');
    const toInput = newRow.querySelector('input[name*="[land_extent_to]"]');
    const prevToInput = row.querySelector('input[name*="[land_extent_to]"]');
    
    if (fromInput && toInput && prevToInput) {
        const prevToValue = parseFloat(prevToInput.value) || 0;
        fromInput.value = (prevToValue + 0.01).toFixed(2);
        toInput.value = (prevToValue + 1.01).toFixed(2);
    }
    
    // Reset other values
    const bagsInput = newRow.querySelector('input[name*="[number_of_bags]"]');
    const atTimeInput = newRow.querySelector('input[name*="[at_a_time_how_many]"]');
    const intervalInput = newRow.querySelector('input[name*="[interval_time_days]"]');
    
    if (bagsInput) bagsInput.value = '';
    if (atTimeInput) atTimeInput.value = '';
    if (intervalInput) intervalInput.value = '0';
    
    // Update button
    const addButton = newRow.querySelector('button[onclick*="addRow"]');
    const removeButton = newRow.querySelector('button[onclick*="removeRow"]');
    if (addButton) {
        addButton.innerHTML = '<i class="fas fa-plus"></i> Add Row';
    }
    if (removeButton) {
        removeButton.style.display = 'block';
    }
    
    // Hide add button on previous row
    const prevAddButton = row.querySelector('button[onclick*="addRow"]');
    if (prevAddButton) {
        prevAddButton.style.display = 'none';
    }
    
    // Show remove button on previous row
    const prevRemoveButton = row.querySelector('button[onclick*="removeRow"]');
    if (prevRemoveButton) {
        prevRemoveButton.style.display = 'block';
    }
    
    tbody.appendChild(newRow);
    rowIndex++;
}

function removeRow(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('conditionsBody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length > 1) {
        row.remove();
        
        // Re-index all rows
        rows.forEach((r, index) => {
            const inputs = r.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.name;
                if (name && !name.includes('district_id')) {
                    input.name = name.replace(/\[\d+\]/, `[${index}]`);
                }
            });
        });
        
        // Show add button on last row
        const lastRow = tbody.querySelector('tr:last-child');
        if (lastRow) {
            const addButton = lastRow.querySelector('button[onclick*="addRow"]');
            if (addButton) {
                addButton.style.display = 'block';
            }
        }
    }
}
</script>
@endsection

