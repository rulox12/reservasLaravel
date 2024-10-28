<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
            <h2 class="text-xl font-semibold mb-4">{{ editing ? 'Editar Usuario' : 'Agregar Nuevo Usuario' }}</h2>
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label for="name" class="block mb-1">Nombre</label>
                    <input
                        v-model="form.name"
                        type="text"
                        id="name"
                        required
                        class="border rounded-lg w-full p-2"
                    />
                </div>
                <div class="mb-4">
                    <label for="email" class="block mb-1">Correo Electrónico</label>
                    <input
                        v-model="form.email"
                        type="email"
                        id="email"
                        required
                        class="border rounded-lg w-full p-2"
                    />
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-1">Contraseña</label>
                    <input
                        v-model="form.password"
                        type="password"
                        id="password"
                        :required="!editing"
                        class="border rounded-lg w-full p-2"
                    />
                </div>
                <div class="mb-4">
                    <label for="role" class="block mb-1">Rol</label>
                    <select v-model="form.role" class="border rounded-lg w-full p-2">
                        <option value="client">Cliente</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" @click="closeModal"
                            class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg mr-2">Cancelar
                    </button>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        {{ editing ? 'Actualizar Usuario' : 'Crear Usuario' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import {ref, watch} from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
    user: {
        type: Object,
        default: () => ({}),
    },
    onClose: {
        type: Function,
        required: true,
    },
    onSave: {
        type: Function,
        required: true,
    },
});

const form = ref({
    name: '',
    email: '',
    password: '',
    role: 'client',
});

const editing = ref(false);

watch(() => props.user, (newUser) => {
    if (newUser.id) {
        editing.value = true;
        form.value = {...newUser};
        form.value.password = ''; // No mostrar la contraseña existente
    } else {
        editing.value = false;
        resetForm();
    }
});

const resetForm = () => {
    form.value = {
        name: '',
        email: '',
        password: '',
        role: 'client',
    };
};

const handleSubmit = () => {
    props.onSave(form.value);
    closeModal();
};

const closeModal = () => {
    props.onClose();
    resetForm();
};
</script>
