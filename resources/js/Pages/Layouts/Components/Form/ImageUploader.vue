<template>
    <div>
        <!-- File Input -->
        <div class="flex items-left justify-left my-4">
            <label for="image-upload" class="form-button">
                {{ imagePreview ? 'Vervang afbeelding' : 'Wijzig afbeelding' }}
            </label>
            <input id="image-upload" type="file" accept="image/*" class="hidden" @change="handleImageUpload" />
        </div>
        <div v-if="errorMessage" class="mt-4 text-red-500 text-sm">
            {{ errorMessage }}
        </div>
        <!-- Image Preview -->
        <div v-if="imagePreview" class="mb-4">
            <img :src="imagePreview" alt="Preview" class="max-w-full h-auto rounded-lg shadow-md" />
        </div>
    </div>
</template>

<script>
export default {
    name: 'ImageUploader',
    props: {
        modelValue: String,
    },
    data() {
        return {
            imagePreview: this.modelValue,
            imageFile: null,
            errorMessage: '',
        };
    },
    mounted() {
        this.convertImageUrlToFile(this.modelValue);
    },
    methods: {
        async convertImageUrlToFile(imageUrl) {
            try {
                // Download de afbeelding
                const response = await fetch(imageUrl);
                if (!response.ok) {
                    throw new Error('Kon de afbeelding niet downloaden.');
                }

                // Converteer de afbeelding naar een Blob
                const blob = await response.blob();

                // Maak een File object van de Blob
                const fileName = imageUrl.split('/').pop(); // Haal de bestandsnaam uit de URL
                const file = new File([blob], fileName, { type: blob.type });
                this.handleFileChange(file);
                // console.log('File object:', this.imageFile);
            } catch (error) {
                console.error('Fout bij het converteren van de URL naar een File object:', error);
                this.imageFile = null; // Reset het File object bij een fout
            }
        },
        handleImageUpload(event) {
            const file = event.target.files[0]; // Get the uploaded file
            if (!file) {
                this.errorMessage = 'Please select an image file.';
                return;
            }
            if (file && file.type.startsWith('image/')) {
                this.handleFileChange(file);
                // this.imageFile = file; // Store the file
                // this.imagePreview = URL.createObjectURL(file); // Create a preview URL
                // this.$emit('image-uploaded', file); // Emit the file to the parent component
            } else {
                alert('Please upload a valid image file.'); // Show an error for invalid files
            }
        },
        handleFileChange(file) {
            this.imageFile = file; // Store the file
            this.imagePreview = URL.createObjectURL(file); // Create a preview URL
            this.$emit('image-uploaded', file); // Emit the file to the parent component
        },
    },
};
</script>
