const formData = new FormData();

@if(setting('social_media_image_model') === 'openai')
	formData.append("post_type", "ai_image_generator");
	formData.append("openai_id", "36");
	formData.append("custom_template", "0");
	formData.append("image_generator", "openai");
	formData.append("image_style", "");
	formData.append("image_lighting", "");
	formData.append("image_mood", "");
	formData.append("image_number_of_images", "1");
	formData.append("size", "256x256");
	formData.append("quality", "standard");
@endif

@if(setting('social_media_image_model') === 'gpt-image-1')
	formData.append("post_type", "ai_image_generator");
	formData.append("openai_id", "36");
	formData.append("custom_template", "0");
	formData.append("image_generator", "gpt-image-1");
	formData.append("image_style", "");
	formData.append("image_lighting", "");
	formData.append("image_mood", "");
	formData.append("image_number_of_images", "1");
	formData.append("size", "256x256");
	formData.append("quality", "standard");
@endif

@if(setting('social_media_image_model') === 'stable_diffusion')
	formData.append("post_type", "ai_image_generator");
	formData.append("openai_id", "36");
	formData.append("custom_template", "0");
	formData.append("image_generator", "stable_diffusion");
	formData.append("type", "text-to-image");
	formData.append("negative_prompt", "");
	formData.append("style_preset", "");
	formData.append("image_mood", "");
	formData.append("sampler", "");
	formData.append("clip_guidance_preset", "");
	formData.append("image_resolution", "1x1");
	formData.append("image_number_of_images", "1");
@endif

@if(setting('social_media_image_model') === 'midjourney')
	formData.append("post_type", "ai_image_generator");
	formData.append("openai_id", "36");
	formData.append("custom_template", "0");
	formData.append("description", "");
	formData.append("model", "midjourney");
	formData.append("image_number_of_images", "1");
	formData.append("image_generator", "midjourney");
	formData.append("image_mood", "");
	formData.append("size", "");
	formData.append("image_style", "");
	formData.append("image_lighting", "");
	formData.append("quality", "");
	formData.append("type", "");
	formData.append("stable_description", "");
	formData.append("negative_prompt", "");
	formData.append("style_preset", "");
	formData.append("sampler", "");
	formData.append("clip_guidance_preset", "");
	formData.append("image_resolution", "1x1");
@endif

@if(setting('social_media_image_model') === 'flux-pro')
	formData.append("post_type", "ai_image_generator");
	formData.append("openai_id", "36");
	formData.append("custom_template", "0");
	formData.append("description", "");
	formData.append("model", "flux-pro");
	formData.append("image_number_of_images", "1");
	formData.append("image_generator", "flux-pro");
	formData.append("image_mood", "");
	formData.append("size", "");
	formData.append("image_style", "");
	formData.append("image_lighting", "");
	formData.append("quality", "");
	formData.append("type", "");
	formData.append("stable_description", "");
	formData.append("negative_prompt", "");
	formData.append("style_preset", "");
	formData.append("sampler", "");
	formData.append("clip_guidance_preset", "");
	formData.append("image_resolution", "1x1");
@endif

@if(setting('social_media_image_model') === 'ideogram')
	formData.append("post_type", "ai_image_generator");
	formData.append("openai_id", "36");
	formData.append("custom_template", "0");
	formData.append("description", "");
	formData.append("model", "ideogram-v2");
	formData.append("image_number_of_images", "1");
	formData.append("image_generator", "ideogram");
	formData.append("image_mood", "");
	formData.append("size", "");
	formData.append("image_style", "");
	formData.append("image_lighting", "");
	formData.append("quality", "");
	formData.append("type", "");
	formData.append("stable_description", "");
	formData.append("negative_prompt", "");
	formData.append("style_preset", "");
	formData.append("sampler", "");
	formData.append("clip_guidance_preset", "");
	formData.append("image_resolution", "1x1");
@endif

@if(setting('social_media_image_model') === 'flux-pro-kontext')
	formData.append("image_ratio", "");
	formData.append("image_generator", "flux-pro-kontext");
	formData.append("description", "");
	formData.append("template_description", "");
	formData.append("prompt_description", "");
	formData.append("post_type", "ai_image_generator");
	formData.append("image_mood", "");
	formData.append("image_style", "");
	formData.append("quality", "standard");
	formData.append("image_lighting", "");
	formData.append("size", "1024x1024");
	formData.append("image_resolution", "1x1");
	formData.append("type", "text-to-image");
	formData.append("image_number_of_images", "1");
	formData.append("negative_prompt", "");
	formData.append("model", "flux-pro-kontext");
	formData.append("description_flux_pro", "");
	formData.append("description_ideogram", "");
	formData.append("stable_description", "");
	formData.append("style_preset", "");
	formData.append("sampler", "");
	formData.append("clip_guidance_preset", "");
	formData.append("openai_id", "36");
@endif

formData.append('description_ideogram', prompt);
formData.append('description_flux_pro', prompt);
formData.append('description_midjourney', prompt);
formData.append('description', prompt);
formData.append('stable_description', prompt);
