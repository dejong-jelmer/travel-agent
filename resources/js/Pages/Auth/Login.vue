<script setup>
import { useForm } from "@inertiajs/vue3";
import { reactive } from "vue";

const props = defineProps({
    message: String,
    error: Boolean,
});

const loginMsg = `Omdat we nu eenmaal moeten inloggen` || "Login";

const form = reactive({
    email: '',
    password: '',
});

function handleLogin() {
    const submitForm = useForm(form);
    submitForm.post(route("admin.login"));
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-black">
        <div class="bg-gray-800 p-8 rounded-lg shadow-md w-full max-w-md">
            <div v-if="props.error">
                <div
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                    role="alert"
                >
                    <span class="block sm:inline">{{ props.message }}</span>
                </div>
            </div>
            <h2
                class="text-2xl font-bold mb-6 text-center text-white"
                v-text="`${loginMsg}`"
            ></h2>
            <form @submit.prevent="handleLogin">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-white"
                        >Email</label
                    >
                    <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter your email"
                        required
                    />
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-white"
                        >Password</label
                    >
                    <input
                        type="password"
                        id="password"
                        v-model="form.password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter your password"
                        required
                    />
                </div>
                <button
                    type="submit"
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</template>
