<template>
    <div class="group border-2 border-dashed border-gray-300 rounded-lg p-4 grid justify-center">
        <label class="p-4 flex flex-col items-center cursor-pointer justify-center space-y-2" @change="handleFiles">
            <input type="file" multiple accept="image/*" class="hidden" />
            <div class="text-gray-500 group-hover:text-custom-secondary">Klik om afbeeldingen te uploaden</div>
        </label>
        <div class="mt-4 grid grid-cols-3 gap-3">
            <div v-for="(image, index) in previewImages" :key="index" class="relative w-24 h-24">
                <img :src="image" class="w-full h-full object-cover rounded-lg shadow" />
                <div role="button"
                    class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-full pl-2 pb-2 p-1 text-xs leading-3 font-bold"
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
        };
    },
    async mounted() {
        if (!this.initialized) {
            const imageUrls = this.modelValue.filter(url => typeof url === 'string');
            if (imageUrls.length > 0) {
                await this.convertImageUrlsToFiles(imageUrls);
            }
        }
    },
    computed: {
        previewImages() {
            return this.uploadedImages.map(file => URL.createObjectURL(file));
        },
    },
    methods: {
        async convertImageUrlsToFiles(imageUrls) {
            let newFiles = [];
            for (const imageUrl of imageUrls) {
                try {
                    const response = await fetch(imageUrl);
                    if (!response.ok) {
                        console.warn(`Kon de afbeelding niet downloaden: ${imageUrl}`);
                        continue;
                    }
                    const blob = await response.blob();
                    const fileName = imageUrl.split('/').pop();
                    const file = new File([blob], fileName, { type: blob.type });
                    newFiles.push(file);
                } catch (error) {
                    console.error("Fout bij het converteren van de URLs naar File objecten:", error);
                }
            }
            if (newFiles.length > 0) {
                this.uploadedImages.push(...newFiles);
                this.emitUpdate();
            }
        },
        handleFiles(event) {
            const files = Array.from(event.target.files);
            this.uploadedImages.push(...files);
            this.emitUpdate();
        },
        removeImage(index) {
            this.uploadedImages.splice(index, 1);
            this.emitUpdate();
        },
        emitUpdate() {
            this.$emit("update:modelValue", [...this.uploadedImages]);
        }
    },
};
</script>
