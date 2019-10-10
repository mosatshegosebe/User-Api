<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'ID_Number' => (int) $this->id_no,
            'Firstname' => $this->firstname,
            'Lastname' => $this->lastname,
            'Gender' => strtoupper($this->gender) == 'M' ? 'Male' : 'Female',
        ];
    }
}
