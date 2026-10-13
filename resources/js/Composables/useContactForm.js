// resources/js/Composables/useContactForm.js
import { reactive, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { fetchApi } from '@/fetchApi'

export function useContactForm() {
    const { t } = useI18n()
    const toast = useToast()

    // From the page url rather than window.location, which does not exist during server-side rendering. The base only
    // makes the relative url parseable.
    const urlParams = new URL(usePage().url, 'http://localhost').searchParams
    const tripSlug = urlParams.get('reis') || ''
    const period = urlParams.get('periode') || ''

    function buildInitialMessage() {
        if (!tripSlug) return ''
        let msg = t('forms.contact.trip_inquiry_prefix', { trip: tripSlug })
        if (period) {
            msg += ' ' + t('forms.contact.trip_inquiry_period', { period: period })
        }

        return msg
    }

    const errors = reactive({})
    const form = reactive({
        name: '',
        email: '',
        phone: '',
        text: buildInitialMessage(),
    })
    const honeypot = ref(null)

    function resetObject(obj) {
        Object.keys(obj).forEach((key) => delete obj[key])
    }

    function submit() {
        try {
            honeypot.value.validate()
            resetObject(errors)
            fetchApi(route('submit.contact'), { method: 'POST', body: form })
                .then(() => {
                    toast.success(t('forms.contact.success'))
                    resetObject(form)
                })
                .catch((error) => {
                    if (error.response?.errors) {
                        Object.assign(errors, error.response.errors)
                    }
                })
        } catch (error) { }
    }

    return { form, errors, honeypot, submit, resetObject }
}
