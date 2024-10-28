<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
            <h2 class="text-xl font-semibold mb-4">{{ editing ? 'Editar Reserva' : 'Agregar Nueva Reserva' }}</h2>
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label for="user" class="block mb-1">Usuario</label>
                    <select v-model="booking.user_id" class="border rounded-lg w-full p-2">
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="room" class="block mb-1">Habitación</label>
                    <select v-model="booking.room_id" class="border rounded-lg w-full p-2">
                        <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.room_number }}</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="check_in_date" class="block text-gray-700">Fecha de Entrada</label>
                    <input
                        type="date"
                        v-model="form.check_in_date"
                        id="check_in_date"
                        required
                        class="border rounded-lg w-full p-2"
                    />
                </div>
                <div class="mb-4">
                    <label for="check_out_date" class="block text-gray-700">Fecha de Salida</label>
                    <input
                        type="date"
                        v-model="form.check_out_date"
                        id="check_out_date"
                        required
                        class="border rounded-lg w-full p-2"
                    />
                </div>
                <div class="mb-4">
                    <label for="total_price" class="block text-gray-700">Precio Total</label>
                    <input
                        type="number"
                        v-model="form.total_price"
                        id="total_price"
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
                        <option value="pending">Pendiente</option>
                        <option value="confirmed">Confirmado</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" @click="closeModal" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg mr-2">Cancelar</button>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">{{ editing ? 'Actualizar Reserva' : 'Crear Reserva' }}</button>
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
    booking: {
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
    users: Array,
    rooms: Array,
});

const form = ref({
    user_id: '',
    room_id: '',
    check_in_date: '',
    check_out_date: '',
    total_price: 0,
    status: 'pending',
});

const editing = ref(false);

watch(() => props.booking, (newBooking) => {
    if (newBooking.id) {
        editing.value = true;
        form.value = { ...newBooking };
    } else {
        editing.value = false;
        resetForm();
    }
});

const resetForm = () => {
    form.value = {
        user_id: '',
        room_id: '',
        check_in_date: '',
        check_out_date: '',
        total_price: 0,
        status: 'pending',
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
