// typeWriter.js
import { ref, onMounted } from 'vue';

export function useTypeWriter(strings = [], speed = 100, pause = 1500) {
  const output = ref('');
  const index = ref(0);
  const charIndex = ref(0);
  const isDeleting = ref(false);
  const isPaused = ref(false);

  const type = () => {
    const current = strings[index.value % strings.length];

    if (isPaused.value) return;

    if (!isDeleting.value) {
      output.value = current.substring(0, charIndex.value + 1);
      charIndex.value++;

      if (charIndex.value === current.length) {
        isPaused.value = true;
        setTimeout(() => {
          isDeleting.value = true;
          isPaused.value = false;
          type();
        }, pause);
        return;
      }
    } else {
      output.value = current.substring(0, charIndex.value - 1);
      charIndex.value--;

      if (charIndex.value === 0) {
        isDeleting.value = false;
        index.value++;
        isPaused.value = true;
        setTimeout(() => {
          isPaused.value = false;
          type();
        }, pause);
        return;
      }
    }
    setTimeout(type, isDeleting.value? speed / 2 : speed);

  };

  onMounted(() => {
    type();
  });

  return {
    output,
  };
}
