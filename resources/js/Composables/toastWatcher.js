import { useToast } from "vue-toastification";
import { isRef, ref, watch } from "vue";
const toast = useToast();

export function useToastWatcher(message, type = 'success') {
    const messageRef = isRef(message) ? message : ref(message);
    const typeRef = isRef(type) ? type : ref(type);

    watch(messageRef, (newMessage) => {
      if (newMessage) {
        const toastType = typeRef.value || 'success';
        toast[toastType](newMessage);
      }
    }, { immediate: true });
  }
