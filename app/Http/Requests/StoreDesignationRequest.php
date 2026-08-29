<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage_designations') || $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('designation') ? $this->route('designation')->id : null;

        return [
            'name' => 'required|string|max:255',
            'short_code' => 'required|string|max:30|unique:designations,short_code,' . $id,
            'head_of_designation_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,archived',
        ];
    }
}
