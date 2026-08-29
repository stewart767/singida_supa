<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('campus') ? $this->route('campus') : null;
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:campuses,code,' . $id,
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,archived',
        ];
    }
}
