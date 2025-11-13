<script>
import { ref, onMounted, watch } from "vue";
import Sortable from "sortablejs";
import { GripVertical } from "lucide-vue-next";

export default {
    components: {
        GripVertical,
    },
    props: {
        blocks: {
            type: Array,
            required: true,
        },
    },
    emits: ["update:order"],
    setup(props, { emit }) {
        const sortableContainer = ref(null);
        const localBlocks = ref([...props.blocks]);

        onMounted(() => {
            new Sortable(sortableContainer.value, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: () => {
                    const sortedIds = Array.from(sortableContainer.value.children).map(el => el.dataset.id);

                    localBlocks.value.sort((a, b) => sortedIds.indexOf(a.id.toString()) - sortedIds.indexOf(b.id.toString()));

                    localBlocks.value.forEach((block, index) => {
                        block.order = index + 1;
                    });
                },
            });
        });

        watch(() => props.blocks, (newBlocks) => {
            localBlocks.value = [...newBlocks];
            updateOrder()
        }, { deep: true });

        const updateOrder = () => {
            emit("update:order", props.blocks);
        };

        return {
            sortableContainer,
            localBlocks,
            updateOrder,
        };
    },
};
</script>

<template>
    <div ref="sortableContainer">
        <div
            v-for="block in localBlocks"
            :key="block.id"
            :data-id="block.id"
            class="sortable-item group/sortable relative ml-6 laptop:ml-0"
        >
            <!-- Drag Handle -->
            <div
                class="drag-handle absolute -left-7 top-1/2 -translate-y-1/2 z-10 cursor-grab active:cursor-grabbing opacity-100 laptop:opacity-0 group-hover/sortable:opacity-100 transition-opacity duration-200"
            >
                <div class="bg-gray-100 hover:bg-gray-200 rounded-lg py-3 px-1 shadow-md border border-gray-300">
                    <GripVertical :size="15" class="text-gray-600" />
                </div>
            </div>

            <!-- Block Content -->
            <div class="transition-transform duration-150">
                <slot :block="block" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.sortable-ghost {
    opacity: 0.4;
}

.sortable-chosen {
    cursor: grabbing !important;
}

.sortable-drag {
    opacity: 0.8;
    transform: rotate(2deg);
}

.sortable-item:hover {
    position: relative;
}
</style>
