<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Sortable from 'sortablejs';

const props = defineProps({
    modelValue: {
        type: [Object, String, Array],
        required: false
    },
    multiple: {
        type: Boolean,
        default: false
    },
    previewSize: {
        type: String,
        default: 'thumbnail',
        validator: (value) => ['large', 'thumbnail'].includes(value)
    },
    label: {
        type: String,
        required: false
    },
    feedback: {
        type: [String, Array],
        required: false
    }
});

const emit = defineEmits(['update:modelValue']);
const { t } = useI18n();

const imageConfig = computed(() => usePage().props.config?.images || {});

const fileInput = ref(null);
const isDragging = ref(false);
const errorMessages = ref([]);

// Each entry: { source: File|string, url: string, error: boolean }
// `source` is what gets emitted; `url` is what the preview shows.
const images = ref([]);

const isLargePreview = computed(() => !props.multiple && props.previewSize === 'large');
const singleImage = computed(() => (props.multiple ? null : images.value[0] ?? null));
const singleFile = computed(() => (singleImage.value?.source instanceof File ? singleImage.value.source : null));

const urlFor = (source) => {
    if (source instanceof File) return URL.createObjectURL(source);
    if (source.startsWith('/storage/') || source.startsWith('http')) return source;
    return `/storage/${imageConfig.value.directory ?? 'images'}/${source}`;
};

const toImage = (source) => ({ source, url: urlFor(source), error: false });

const revoke = (image) => {
    if (image.source instanceof File) URL.revokeObjectURL(image.url);
};

// Initialise from the current v-model value (existing paths and/or File objects).
const initial = props.multiple
    ? (Array.isArray(props.modelValue) ? props.modelValue : [])
    : [props.modelValue].filter((v) => typeof v === 'string' || v instanceof File);
images.value = initial.map(toImage);

const emitUpdate = () => {
    const sources = images.value.map((image) => image.source);
    emit('update:modelValue', props.multiple ? sources : sources[0] ?? null);
};

// Mirrors the server-side rules (ImageValidationRules): allowed mimes and max size.
const allowedTypes = computed(() =>
    (imageConfig.value.allowed_mimes ?? []).map((ext) => `image/${ext === 'jpg' ? 'jpeg' : ext}`)
);

const rejectionFor = (file) => {
    const isImage = file.type.startsWith('image/');
    const isAllowed = allowedTypes.value.length === 0 || allowedTypes.value.includes(file.type);
    if (!isImage || !isAllowed) {
        return t('image_uploader.errors.invalid_type', {
            filename: file.name,
            types: (imageConfig.value.allowed_mimes ?? []).join(', ')
        });
    }
    if (file.size > maxBytes.value) {
        return t('image_uploader.errors.too_large', { filename: file.name, maxSize: formatBytes(maxBytes.value) });
    }
    return null;
};

const addFiles = (fileList) => {
    errorMessages.value = [];
    const validFiles = [];

    for (const file of Array.from(fileList)) {
        const rejection = rejectionFor(file);
        if (rejection) errorMessages.value.push(rejection);
        else validFiles.push(file);
    }
    if (validFiles.length === 0) return;

    if (props.multiple) {
        images.value.push(...validFiles.map(toImage));
    } else {
        images.value.forEach(revoke);
        images.value = [toImage(validFiles[0])];
    }
    emitUpdate();
};

const triggerFileInput = () => fileInput.value.click();

const handleFiles = (event) => {
    addFiles(event.target.files);
    event.target.value = '';
};

const handleDrop = (event) => {
    isDragging.value = false;
    addFiles(event.dataTransfer.files);
};

const handleDragLeave = (event) => {
    // dragleave also fires when moving over a child element; ignore those.
    if (event.currentTarget.contains(event.relatedTarget)) return;
    isDragging.value = false;
};

const removeImage = (index) => {
    revoke(images.value[index]);
    images.value.splice(index, 1);
    if (images.value.length === 0) errorMessages.value = [];
    emitUpdate();
};

// A broken preview only shows a fallback; the value stays intact so an existing
// image is never silently dropped from the form. Removing is an explicit action.
const handleImageError = (index) => {
    images.value[index].error = true;
};

const handleImageLoad = (index) => {
    images.value[index].error = false;
};

const KB = 1024;

const formatBytes = (bytes, decimals = 2) => {
    if (bytes === 0) return '0 Bytes';
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
    const i = Math.floor(Math.log(bytes) / Math.log(KB));
    return parseFloat((bytes / Math.pow(KB, i)).toFixed(decimals)) + ' ' + sizes[i];
};

// config('images.max_size') is in kilobytes, matching Laravel's `max:` rule.
const maxBytes = computed(() => imageConfig.value.max_size * KB);

// Drag-to-reorder thumbnails (multiple mode). The emitted array order is the display order.
const grid = ref(null);
let sortable = null;

watch(grid, (el) => {
    sortable?.destroy();
    sortable = null;
    if (!el || !props.multiple) return;

    sortable = new Sortable(el, {
        animation: 150,
        // No native drag events, so the drop zone's dragover/drop handlers stay quiet while sorting.
        forceFallback: true,
        fallbackTolerance: 3,
        delay: 150,
        delayOnTouchOnly: true,
        filter: 'button',
        ghostClass: 'opacity-40',
        onEnd: ({ oldIndex, newIndex }) => {
            if (oldIndex === newIndex) return;
            const [moved] = images.value.splice(oldIndex, 1);
            images.value.splice(newIndex, 0, moved);
            emitUpdate();
        }
    });
});

onBeforeUnmount(() => {
    sortable?.destroy();
    images.value.forEach(revoke);
});
</script>

<template>
    <div>
        <!-- Hidden file input -->
        <input ref="fileInput" type="file" :multiple="multiple" accept="image/*" class="hidden" @change="handleFiles" />

        <!-- Drop zone -->
        <div class="border-2 border-dashed rounded-lg p-8 transition-colors cursor-pointer" role="button" tabindex="0"
            :class="isDragging ? 'border-brand-link bg-brand-link/5' : 'border-gray-300 hover:border-gray-400'"
            @click="triggerFileInput" @keydown.enter.prevent="triggerFileInput" @keydown.space.prevent="triggerFileInput"
            @dragover.prevent="isDragging = true" @dragenter.prevent="isDragging = true"
            @dragleave.prevent="handleDragLeave" @drop.prevent="handleDrop">
            <div class="flex flex-col items-center justify-center space-y-2">
                <div class="text-brand-link font-medium">
                    {{ isDragging ?
                        (multiple ? t('image_uploader.drop_zone.drop_multiple') :
                            t('image_uploader.drop_zone.drop_single')) :
                        (label || (multiple ? t('image_uploader.drop_zone.click_or_drag_multiple') :
                            t('image_uploader.drop_zone.click_or_drag_single')))
                    }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ multiple ? t('image_uploader.drop_zone.multiple_allowed') :
                        t('image_uploader.drop_zone.single_allowed') }}
                </div>
            </div>

            <!-- Rejected files (type or size) -->
            <div v-if="errorMessages.length" class="mt-4 text-status-error text-sm text-center space-y-1">
                <p v-for="message in errorMessages" :key="message">{{ message }}</p>
            </div>

            <!-- Error message from form request validation -->
            <FormFeedback v-if="feedback" :message="feedback" />

            <!-- Preview Section -->
            <div v-if="images.length > 0" class="mt-4">
                <!-- Large preview (single mode only) -->
                <div v-if="isLargePreview" class="relative">
                    <img v-if="!singleImage.error" :src="singleImage.url" alt=""
                        class="max-w-full h-auto rounded-lg shadow-md" @error="handleImageError(0)"
                        @load="handleImageLoad(0)" />

                    <!-- Fallback for broken image -->
                    <div v-else class="p-4 bg-gray-100 rounded-lg text-gray-600">
                        <p class="text-sm">{{ t('image_uploader.errors.image_load_error') }}</p>
                    </div>

                    <!-- Remove button -->
                    <button type="button"
                        class="absolute -top-2 -right-2 w-8 h-8 bg-status-error text-white rounded-full flex items-center justify-center text-sm font-bold hover:bg-red-600 hover:scale-110 transition-all shadow-md z-10"
                        @click.stop="removeImage(0)" :aria-label="t('image_uploader.preview.remove_image')">
                        ✕
                    </button>

                    <!-- File info (only in large mode) -->
                    <div v-if="singleFile" class="mt-4 space-y-1 text-sm">
                        <p>{{ t('image_uploader.file_info.filename') }}: {{ singleFile.name }}</p>
                        <p>{{ t('image_uploader.file_info.filesize') }}: {{ formatBytes(singleFile.size) }}</p>
                        <p>{{ t('image_uploader.file_info.filetype') }}: {{ singleFile.type }}</p>
                    </div>
                </div>

                <!-- Thumbnail grid (multiple mode or thumbnail preference) -->
                <div v-else ref="grid" class="flex flex-wrap justify-center gap-2" @click.stop>
                    <div v-for="(image, index) in images" :key="image.url" class="relative w-24 h-24"
                        :class="{ 'cursor-grab active:cursor-grabbing': multiple }">
                        <!-- Normal image -->
                        <img v-if="!image.error" :src="image.url" alt=""
                            class="w-full h-full object-cover rounded-lg shadow" @error="handleImageError(index)"
                            @load="handleImageLoad(index)" />

                        <!-- Fallback for broken image -->
                        <div v-else
                            class="w-full h-full bg-gray-100 rounded-lg shadow flex flex-col items-center justify-center text-gray-500 text-xs p-1">
                            <div class="text-lg">📷</div>
                            <div class="text-center leading-3">{{ t('image_uploader.errors.cannot_load_image') }}</div>
                        </div>

                        <!-- Remove button -->
                        <button type="button"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-status-error text-white rounded-full flex items-center justify-center text-sm font-bold hover:bg-red-600 hover:scale-110 transition-all shadow-md z-10"
                            @click.stop="removeImage(index)" :aria-label="t('image_uploader.preview.remove_image')">
                            ✕
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
