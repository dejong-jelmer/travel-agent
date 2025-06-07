<template>
    <div class="group border-2 border-dashed border-gray-300 rounded-lg p-4 grid justify-center">
        <label class="p-4 flex flex-col items-center cursor-pointer justify-center space-y-2" @change="handleFiles">
            <input type="file" multiple accept="image/*" class="hidden" />
            <div class="text-gray-500 group-hover:text-custom-secondary">Klik om afbeeldingen te uploaden</div>
        </label>
        
        <!-- Error bericht -->
        <div v-if="errorMessage" class="mt-4 text-red-500 text-sm text-center">
            {{ errorMessage }}
        </div>
        
        <div class="mt-4 grid grid-cols-3 gap-3">
            <div v-for="(imageData, index) in previewImagesData" :key="index" class="relative w-24 h-24">
                <!-- Normale afbeelding -->
                <img 
                    v-if="!imageData.error && imageData.url" 
                    :src="imageData.url" 
                    class="w-full h-full object-cover rounded-lg shadow"
                    @error="handleImageError(index)"
                    @load="handleImageLoad(index)"
                />
                
                <!-- Fallback voor kapotte afbeeldingen -->
                <div 
                    v-else-if="imageData.error"
                    class="w-full h-full bg-gray-100 rounded-lg shadow flex flex-col items-center justify-center text-gray-500 text-xs p-1"
                >
                    <div class="text-lg">📷</div>
                    <div class="text-center leading-3">Kan niet laden</div>
                </div>
                
                <!-- Loading state -->
                <div 
                    v-else-if="imageData.loading"
                    class="w-full h-full bg-gray-200 rounded-lg shadow flex items-center justify-center text-gray-500 text-xs animate-pulse"
                >
                    Laden...
                </div>
                
                <!-- Remove button -->
                <div role="button"
                    class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-full pl-2 pb-2 p-1 text-xs leading-3 font-bold hover:bg-red-600 z-10"
                    @click="removeImage(index)">
                    ✕
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "MultiImageUploader",
    props: {
        modelValue: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            uploadedImages: [],
            imageStates: [], // Track loading/error states per image
            errorMessage: '',
            initialized: false,
        };
    },
    async mounted() {
        if (!this.initialized) {
            this.initialized = true;
            const imageUrls = this.modelValue.filter(url => typeof url === 'string');
            if (imageUrls.length > 0) {
                await this.convertImageUrlsToFiles(imageUrls);
            }
        }
    },
    computed: {
        previewImagesData() {
            return this.uploadedImages.map((file, index) => {
                const state = this.imageStates[index] || { loading: false, error: false };
                let url = null;
                
                try {
                    if (file instanceof File) {
                        url = URL.createObjectURL(file);
                    }
                } catch (error) {
                    console.warn('Kon preview URL niet maken:', error);
                    return { url: null, error: true, loading: false };
                }
                
                return {
                    url,
                    error: state.error,
                    loading: state.loading
                };
            });
        },
    },
    methods: {
        async convertImageUrlsToFiles(imageUrls) {
            try {
                this.errorMessage = '';
                let newFiles = [];
                
                for (let i = 0; i < imageUrls.length; i++) {
                    const imageUrl = imageUrls[i];
                    
                    // Set loading state
                    this.setImageState(this.uploadedImages.length + newFiles.length, { loading: true, error: false });
                    
                    try {
                        const response = await fetch(imageUrl);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        
                        const blob = await response.blob();
                        
                        // Controleer of het een afbeelding is
                        if (!blob.type.startsWith('image/')) {
                            throw new Error('Het bestand is geen geldige afbeelding');
                        }
                        
                        const fileName = imageUrl.split('/').pop() || `image-${Date.now()}.jpg`;
                        const file = new File([blob], fileName, { type: blob.type });
                        
                        newFiles.push(file);
                        
                        // Clear loading state
                        this.setImageState(this.uploadedImages.length + newFiles.length - 1, { loading: false, error: false });
                        
                    } catch (error) {
                        console.warn(`Kon afbeelding niet laden: ${imageUrl}`, error.message);
                        
                        // Maak een placeholder file voor de error state
                        const fileName = imageUrl.split('/').pop() || 'unknown.jpg';
                        const placeholderFile = new File([new Blob()], fileName, { type: 'image/jpeg' });
                        newFiles.push(placeholderFile);
                        
                        // Set error state
                        this.setImageState(this.uploadedImages.length + newFiles.length - 1, { loading: false, error: true });
                    }
                }
                
                if (newFiles.length > 0) {
                    this.uploadedImages.push(...newFiles);
                    this.emitUpdate();
                }
                
            } catch (error) {
                console.error("Algemene fout bij het converteren van URLs:", error);
                this.errorMessage = 'Er is een fout opgetreden bij het laden van de afbeeldingen.';
            }
        },
        handleFiles(event) {
            try {
                this.errorMessage = '';
                const files = Array.from(event.target.files);
                
                // Valideer bestanden
                const validFiles = files.filter(file => {
                    if (!file.type.startsWith('image/')) {
                        console.warn(`Bestand ${file.name} is geen afbeelding`);
                        return false;
                    }
                    return true;
                });
                
                if (validFiles.length !== files.length) {
                    this.errorMessage = 'Sommige bestanden zijn geen geldige afbeeldingen en zijn overgeslagen.';
                }
                
                if (validFiles.length > 0) {
                    // Initialize states for new images
                    const startIndex = this.uploadedImages.length;
                    validFiles.forEach((_, index) => {
                        this.setImageState(startIndex + index, { loading: false, error: false });
                    });
                    
                    this.uploadedImages.push(...validFiles);
                    this.emitUpdate();
                }
                
                // Reset input
                event.target.value = '';
                
            } catch (error) {
                console.error('Fout bij verwerken van bestanden:', error);
                this.errorMessage = 'Er is een fout opgetreden bij het uploaden van de bestanden.';
            }
        },
        removeImage(index) {
            try {
                // Cleanup object URL om memory leaks te voorkomen
                const imageData = this.previewImagesData[index];
                if (imageData && imageData.url && imageData.url.startsWith('blob:')) {
                    URL.revokeObjectURL(imageData.url);
                }
                
                this.uploadedImages.splice(index, 1);
                this.imageStates.splice(index, 1);
                this.emitUpdate();
                
                // Clear error message als er geen afbeeldingen meer zijn
                if (this.uploadedImages.length === 0) {
                    this.errorMessage = '';
                }
                
            } catch (error) {
                console.error('Fout bij verwijderen van afbeelding:', error);
            }
        },
        handleImageError(index) {
            console.warn(`Afbeelding ${index} kon niet worden geladen`);
            this.setImageState(index, { loading: false, error: true });
        },
        handleImageLoad(index) {
            this.setImageState(index, { loading: false, error: false });
        },
        setImageState(index, state) {
            // Ensure imageStates array is large enough
            while (this.imageStates.length <= index) {
                this.imageStates.push({ loading: false, error: false });
            }
            
            // Update the state reactively (Vue 3 compatible)
            this.imageStates[index] = { ...this.imageStates[index], ...state };
        },
        emitUpdate() {
            try {
                // Filter out error files voor de emit
                const validFiles = this.uploadedImages.filter((file, index) => {
                    const state = this.imageStates[index];
                    return !state || !state.error;
                });
                
                this.$emit("update:modelValue", [...validFiles]);
            } catch (error) {
                console.error('Fout bij emit update:', error);
            }
        }
    },
    // Cleanup object URLs om memory leaks te voorkomen
    beforeUnmount() {
        try {
            this.previewImagesData.forEach(imageData => {
                if (imageData.url && imageData.url.startsWith('blob:')) {
                    URL.revokeObjectURL(imageData.url);
                }
            });
        } catch (error) {
            console.error('Fout bij cleanup:', error);
        }
    }
};
</script>