<?php

namespace App\Extensions\SocialMedia\System\Http\Requests;

use App\Extensions\SocialMedia\System\Enums\PlatformEnum;
use App\Extensions\SocialMedia\System\Enums\StatusEnum;
use App\Extensions\SocialMedia\System\Models\SocialMediaPlatform;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class SocialMediaPostUpdateRequest extends FormRequest
{
    public string $platform;

    public function rules(): array
    {
        $limit = config('social-media.' . $this->platform . '.requirements.text.limit');

        $imageRule = $this->platform === 'instagram' ? 'required' : 'sometimes';

        $videoRule = $this->get('social_media_platform') === PlatformEnum::tiktok->value ? 'required' : 'sometimes';

        return [
            'user_id'                  => 'required',
            'company_id'               => 'sometimes|nullable|numeric',
            'campaign_id'              => 'sometimes|nullable|numeric',
            'scheduled_at'             => 'sometimes|',
            'is_repeated'              => 'sometimes',
            'repeat_period'            => 'required_if:is_repeated,1|sometimes',
            'repeat_start_date'        => 'required_if:post_now,0|sometimes',
            'repeat_time'              => 'required_if:post_now,0|sometimes',
            'social_media_platform_id' => 'required',
            'social_media_platform'    => 'required',
            'link'                     => 'sometimes',
            'is_personalized_content'  => 'sometimes',
            'platform'                 => 'required',
            'tone'                     => 'required',
            'content'                  => 'required|min:1|max:' . $limit,
            'image'                    => $imageRule,
            'video'                    => $videoRule,
            'status'                   => 'sometimes',
        ];
    }

    public function messages(): array
    {
        return [
            'social_media_platform_id.required' => 'Please select a platform',
            'content.required'                  => 'Please enter post content',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['user_id' => Auth::id()]);

        if ($this->request->get('post_now')) {
            $this->merge([
                'post_now'          => true,
                'scheduled_at'      => now()->format('Y-m-d H:i'),
                'repeat_start_date' => now()->format('Y-m-d'),
                'repeat_time'       => now()->format('H:i'),
                'is_repeated'       => false,
            ]);
        }

        if ($this->request->get('post_now') === '0') {
            $this->merge([
                'scheduled_at'      => Carbon::createFromFormat('m/d/Y', $this->request->get('scheduled_at'))->format('Y-m-d') . ' ' . $this->request->get('repeat_time') . ':00',
                'repeat_start_date' => Carbon::createFromFormat('m/d/Y', $this->request->get('repeat_start_date'))->format('Y-m-d'),
                'is_repeated'       => $this->request->get('is_repeated') === 'true' ? '1' : '0',
            ]);
        }

        $platform = SocialMediaPlatform::query()->find($this->request->get('social_media_platform_id'));

        if (! $platform) {
            throw new RuntimeException(__('Platform not found'));
        }

        $this->platform = $platform?->platform;

        $this->merge([
            'platform'                => $this->platform,
            'social_media_platform'   => $this->platform,
            'status'                  => StatusEnum::scheduled->value,
            'is_personalized_content' => $this->request->has('is_personalized_content'),
        ]);
    }
}
