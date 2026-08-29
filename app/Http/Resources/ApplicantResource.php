<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'nida_number' => $this->nida_number,
            'voter_id_number' => $this->voter_id_number,
            'nida_card_number' => $this->nida_card_number,
            'work_id_number' => $this->work_id_number,
            'whatsapp_number' => $this->whatsapp_number,
            'region' => $this->region,
            'district' => $this->district,
            'ward' => $this->ward,
            'nationality' => $this->nationality,
            'next_of_kin' => [
                'name' => $this->next_of_kin_name,
                'phone' => $this->next_of_kin_phone,
                'relation' => $this->next_of_kin_relation,
            ],
            'passport_photo_url' => $this->passport_photo_path ? asset('storage/' . $this->passport_photo_path) : null,
        ];
    }
}
