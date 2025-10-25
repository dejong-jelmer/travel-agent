<script>
import { ref, onMounted, watch } from "vue";
import Sortable from "sortablejs";

export default {
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
        <div v-for="block in localBlocks" :key="block.id" :data-id="block.id">
            <slot :block="block" />
        </div>
    </div>
</template>
