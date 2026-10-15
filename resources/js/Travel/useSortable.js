import { onBeforeUnmount, watch } from 'vue';
import Sortable from 'sortablejs';

export const moveItem = (list, from, to) => list.splice(to, 0, ...list.splice(from, 1));

// Lets the children of the list element be dragged by their handle. Sortable moves the dragged element itself; it is
// put back right away, so Vue stays the only one moving elements, and onMove(from, to) moves the item in the list.
// Nested lists need their own handle class, or dragging a child would drag its parent too.
export function useSortable(listRef, handle, onMove) {
    let sortable = null;

    // The list element can come and go with a v-if, as the lines of a leg do when its type changes.
    watch(listRef, (element) => {
        sortable?.destroy();
        sortable = element
            ? Sortable.create(element, {
                  handle,
                  animation: 150,
                  onEnd: ({ item, from, oldIndex, newIndex }) => {
                      if (oldIndex === newIndex) {
                          return;
                      }

                      // Put it back between its old siblings. Not with appendChild: Vue keeps an empty text node at
                      // the end of the list, and an element after it would be out of Vue's reach.
                      item.remove();
                      const siblings = [...from.children];
                      siblings[oldIndex] ? siblings[oldIndex].before(item) : siblings[siblings.length - 1].after(item);

                      onMove(oldIndex, newIndex);
                  },
              })
            : null;
    }, { flush: 'post' });

    onBeforeUnmount(() => sortable?.destroy());
}
