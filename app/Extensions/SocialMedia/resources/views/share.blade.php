@extends('panel.layout.app', ['disable_tblr' => true, 'disable_titlebar' => true])
@section('title', __('Create New Post'))

@section('content')
    @dump($platforms)

    @dump($currentPlatform)

    @dump($companies)

    @dump($campaigns)

    <input
        id="image"
        type="hidden"
        nae="image"
        value="{{ old('image') }}"
    >

    <input
        id="upload_image"
        type="file"
        name="upload_image"
    >
@endsection

@push('scripts')
    <script>
        $('#upload_image').on('change', function() {
            uploadImage();
        });

        function uploadImage() {
            // input[type="file"] elemanını seç
            const fileInput = document.getElementById('upload_image');
            // Seçilen dosyayı al
            const file = fileInput.files[0];

            // Dosya seçilmemişse işlem yapma
            if (!file) return;

            // Gönderilecek veriyi hazırlıyoruz
            let formData = new FormData();
            formData.append('upload_image', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Sunucuya isteği atıyoruz
            fetch('{{ route('dashboard.user.social-media.upload.image') }}', {
                    method: 'POST',
                    body: formData,
                    // Eğer Laravel kullanıyorsanız ve CSRF token eklemek isterseniz:
                    // headers: {
                    //   'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    // }
                })
                .then(response => response.json())
                .then(data => {
                    // Örnek olarak, sunucudan 'image_path' veya 'image' vb. dönmesini beklediğimizi varsayıyoruz
                    if (data && data.image_path) {
                        // Gizli alana sunucudan gelen değeri yaz
                        document.getElementById('image').value = data.image_path;
                    } else {
                        // Herhangi bir hata ya da beklenmeyen cevap durumunda işlem
                        console.error('Sunucudan beklenen veri dönmedi', data);
                    }
                })
                .catch(error => {
                    console.error('Görsel yükleme sırasında hata oluştu:', error);
                });
        }
    </script>

    <script>
        function uploadImage() {

        }

        function generateImage(prompt = 'Image prompt #Mohsen') {
            $.ajax({
                url: "/dashboard/user/openai/generate",
                type: 'POST',
                data: {
                    "post_type": "ai_image_generator",
                    "openai_id": "36",
                    "custom_template": "0",
                    "image_generator": "openai",
                    "image_style": "",
                    "image_lighting": "",
                    "image_mood": "",
                    "image_number_of_images": "1",
                    "size": "1024x1024",
                    "quality": "standard",
                    "description": prompt
                },
                beforeSend: function() {},
                success: function(response) {
                    if (response.status === 'success') {
                        let images = response.images;

                        if (images[0]) {
                            let image = images[0];

                            let output = image.output;

                            preview.src = output;

                            preview.classList.remove("hidden");

                            placeholder.classList.add("hidden");

                            imageInput.value = response.nameOfImage;

                            $('#lds-dual-ring1').toggleClass('hidden');
                            $('.generate').toggleClass('hidden');

                        }
                    } else {
                        toastr.error(response.message);

                    }
                },
                error: function(error) {
                    // console.log(error);
                    toastr.error(error.responseJSON.message);

                }
            });
        }
    </script>
@endpush
