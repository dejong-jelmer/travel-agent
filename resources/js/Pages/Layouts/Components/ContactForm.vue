<script setup>
import { reactive, ref } from "vue";
import VueHoneypot from "vue-honeypot";
import { Input, Text } from "@/Pages/Layouts/Components/Form";
import axios from "@/axios";
import Button from "@/Pages/Layouts/Components/Button.vue";
import { useToast } from "vue-toastification";

const errors = reactive({});
const form = reactive({
    name: "",
    email: "",
    telephone: "",
    text: "",
});
const honeypot = ref(null);
const toast = useToast();

function resetObject(obj) {
    Object.keys(obj).forEach((key) => delete obj[key]);
}

function submit() {
    try {
        honeypot.value?.validate();
        resetObject(errors);
        axios
            .post(route("contact"), form)
            .then((response) => {
                toast.success("Bedankt! Uw bericht is succesvol verstuurd.");
                resetObject(form);
            })
            .catch((error) => {
                if (error.response && error.response.data && error.response.data.errors) {
                    Object.assign(errors, error.response.data.errors);
                }
            });
    } catch (error) {}
}
</script>

<template>
    <form @submit.prevent="submit" @change="resetObject(errors)">
        <div class="px-0 grid gap-y-6 tablet:gap-y-12">
            <div class="grid tablet:grid-cols-2 gap-6 tablet:gap-12">
                <div
                    class="grid gap-6 tablet:flex tablet:flex-col h-full justify-between"
                >
                    <Input
                        type="text"
                        name="name"
                        label="Naam"
                        :required="false"
                        :show-label="false"
                        v-model="form.name"
                        :feedback="errors?.name"
                    />
                    <Input
                        type="email"
                        name="email"
                        label="Uw Emailadres"
                        :required="false"
                        :show-label="false"
                        v-model="form.email"
                        :feedback="errors?.email"
                    />
                    <Input
                        type="telephone"
                        name="telephone"
                        label="Uw telefoonnummer (optioneel)"
                        :required="false"
                        :show-label="false"
                        v-model="form.telephone"
                        :feedback="errors?.telephone"
                    />
                </div>
                <div>
                    <Text
                        name="text"
                        label="Bericht"
                        :required="false"
                        :show-label="false"
                        v-model="form.text"
                        :feedback="errors?.text"
                    />
                </div>
            </div>
            <VueHoneypot ref="honeypot" />
            <div class="w-full flex justify-center">
                <Button text="Verstuur" />
            </div>
        </div>
    </form>
</template>
