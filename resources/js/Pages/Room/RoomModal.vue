<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
            <h2 class="text-xl font-semibold mb-4">{{ editing ? 'Editar Habitación' : 'Añadir Nueva Habitación' }}</h2>
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label for="room_number" class="block text-gray-700">Número de Habitación</label>
                    <input
                        type="text"
                        v-model="form.room_number"
                        id="room_number"
                        required
                        class="border rounded-lg w-full p-2"
                    />
                </div>
                <div class="mb-4">
                    <label for="type" class="block text-gray-700">Tipo</label>
                    <select
                        v-model="form.type"
                        id="type"
                        required
                        class="border rounded-lg w-full p-2"
                    >
                        <option value="single">Individual</option>
                        <option value="double">Doble</option>
                        <option value="suite">Suite</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700">Precio</label>
                    <input
                        type="number"
                        v-model="form.price"
                        id="price"
                        required
                        step="0.01"
                        class="border rounded-lg w-full p-2"
                    />
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-gray-700">Estado</label>
                    <select
                        v-model="form.status"
                        id="status"
                        required
                        class="border rounded-lg w-full p-2"
                    >
                        <option value="available">Disponible</option>
                        <option value="reserved">Reservado</option>
                        <option value="occupied">Ocupado</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="capacity" class="block text-gray-700">Capacidad</label>
                    <select
                        v-model="form.capacity"
                        id="capacity"
                        required
                        class="border rounded-lg w-full p-2"
                    >
                        <option value="2">2</option>
                        <option value="4">4</option>
                        <option value="8">8</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="climate_control" class="block text-gray-700">Control Climático</label>
                    <select
                        v-model="form.climate_control"
                        id="climate_control"
                        required
                        class="border rounded-lg w-full p-2"
                    >
                        <option value="fan">Ventilador</option>
                        <option value="air_conditioning">Aire Acondicionado</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" @click="closeModal" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg mr-2">Cancelar</button>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">{{ editing ? 'Actualizar Habitación' : 'Crear Habitación' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
    room: {
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
    room_number: '',
    type: 'single',
    price: 0,
    status: 'available',
});

const editing = ref(false);

watch(() => props.room, (newRoom) => {
    if (newRoom.id) {
        editing.value = true;
        form.value = { ...newRoom };
    } else {
        editing.value = false;
        resetForm();
    }
});

const resetForm = () => {
    form.value = {
        room_number: '',
        type: 'single',
        price: 0,
        status: 'available',
        capacity: '2',
        climate_control: 'fan',
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
