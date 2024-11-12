<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {ref, inject, watch, computed} from 'vue';
import axios from 'axios';
import RoomModal from './Room/RoomModal.vue';

const swal = inject('$swal');

const props = defineProps({
    rooms: {
        type: Array,
        required: true,
    },
});

const filters = ref({
    room_number: '',
    status: '',
    capacity: '',
});


const reactiveRooms = ref([...props.rooms]);

const editRoom = (room) => {
    selectedRoom.value = room;
    isModalOpen.value = true;
};

const deleteRoom = async (roomId) => {
    const confirmed = await swal({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Eliminar",
    });

    if (confirmed.isConfirmed) {
        try {
            await axios.delete(route('room.destroy', roomId));

            swal({
                title: '¡Eliminado!',
                text: '¡Habitación eliminada con éxito!',
                icon: 'success',
                button: 'OK',
            });

            reactiveRooms.value = reactiveRooms.value.filter(room => room.id !== roomId);
        } catch (error) {
            console.log(error);
            swal({
                title: '¡Error!',
                text: 'Hubo un problema al eliminar la habitación.',
                icon: 'error',
                button: 'OK',
            });
        }
    } else {
        swal({
            title: 'Cancelado',
            text: 'La habitación no fue eliminada.',
            icon: 'info',
            button: 'OK',
        });
    }
};

const createRoom = () => {
    selectedRoom.value = {};
    isModalOpen.value = true;
};

const isModalOpen = ref(false);
const selectedRoom = ref({});

const closeModalHandler = () => {
    isModalOpen.value = false;
};

const filteredRooms = computed(() => {
    return reactiveRooms.value.filter((room) => {
        const roomNumberMatches = room.room_number.includes(filters.value.room_number);
        const statusMatches = filters.value.status ? room.status === filters.value.status : true;
        const capacityMatches = filters.value.capacity ? room.capacity === filters.value.capacity : true;

        return roomNumberMatches && statusMatches && capacityMatches;
    });
});

const saveRoom = async (room) => {
    try {
        if (room.id) {
            await axios.put(route('room.update', room.id), room);

            swal({
                title: '¡Éxito!',
                text: '¡Habitación actualizada con éxito!',
                icon: 'success',
                button: 'OK',
            });

            // Actualizar la lista con la habitación editada
            const index = reactiveRooms.value.findIndex(r => r.id === room.id);
            if (index !== -1) {
                reactiveRooms.value[index] = room;
            }
        } else {
            const response = await axios.post(route('room.store'), room);

            if (response.data.success) {
                swal({
                    title: '¡Éxito!',
                    text: response.data.message,
                    icon: 'success',
                    button: 'OK',
                });

                reactiveRooms.value.push(response.data.room);
            }
        }
    } catch (error) {
        swal({
            title: '¡Error!',
            text: 'Hubo un problema al guardar la habitación.',
            icon: 'error',
            button: 'OK',
        });
    }
    isModalOpen.value = false;
};

const translateStatus = (status) => {
    const statusMap = {
        'available': 'Disponible',
        'occupied': 'Ocupada',
        'reserved': 'Reservada',
    };

    return statusMap[status] || status;
}

const getStatusClass = (status) => {
    switch (status) {
        case 'available':
            return 'text-green-500';
        case 'reserved':
            return 'text-yellow-500';
        case 'occupied':
            return 'text-red-500';
        default:
            return 'text-gray-500';
    }
};
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Habitaciones
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="flex justify-between mb-4">
                    <button @click="createRoom" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Añadir Nueva Habitación
                    </button>
                </div>
                <div class="mb-4">
                    <div class="flex gap-6">
                        <input
                            v-model="filters.room_number"
                            type="text"
                            placeholder="Número de Habitación"
                            class="border border-gray-300 px-6 py-3 rounded-lg text-lg w-full max-w-xs"
                        />
                        <select v-model="filters.status"
                                class="border border-gray-300 px-6 py-3 rounded-lg text-lg w-full max-w-xs">
                            <option value="">Estado</option>
                            <option value="available">Disponible</option>
                            <option value="reserved">Reservada</option>
                            <option value="occupied">Ocupada</option>
                        </select>
                        <select v-model="filters.capacity"
                                class="border border-gray-300 px-6 py-3 rounded-lg text-lg w-full max-w-xs">
                            <option value="">Capacidad</option>
                            <option value="2">2</option>
                            <option value="4">4</option>
                            <option value="8">8</option>
                        </select>
                    </div>
                </div>
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <div v-if="reactiveRooms?.length === 0" class="text-center">
                        <p class="text-gray-600">No hay habitaciones disponibles. Por favor, añade una habitación.</p>
                    </div>

                    <div v-else>
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Número de Habitación</th>
                                <th class="px-4 py-2 text-left">Tipo</th>
                                <th class="px-4 py-2 text-left">Precio</th>
                                <th class="px-4 py-2 text-left">Capacidad</th>
                                <th class="px-4 py-2 text-left">Estado</th>
                                <th class="px-4 py-2 text-left">Aire A</th>
                                <th class="px-4 py-2 text-left">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(room, index) in filteredRooms" :key="room.id">
                                <td class="border px-4 py-2">{{ room.room_number }}</td>
                                <td class="border px-4 py-2">{{ room.type }}</td>
                                <td class="border px-4 py-2">{{ room.price }}</td>
                                <td class="border px-4 py-2">{{ room.capacity }}</td>
                                <td class="border px-4 py-2" :class="getStatusClass(room.status)">
                                    {{ translateStatus(room.status) }}
                                </td>
                                <td class="border px-4 py-2">{{ room.climate_control }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="editRoom(room)" class="text-green-500 hover:text-green-600">
                                        <svg class="h-5 w-5 text-green-300" viewBox="0 0 24 24" stroke-width="2"
                                             stroke="currentColor" fill="none" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <path d="M9 7 h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"/>
                                            <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"/>
                                            <line x1="16" y1="5" x2="19" y2="8"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteRoom(room.id)" class="text-red-500 hover:text-red-600 ml-2">
                                        <svg class="h-5 w-5 text-red-300" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <RoomModal
                    v-model:isOpen="isModalOpen"
                    v-model:room="selectedRoom"
                    @close="closeModalHandler"
                    @save="saveRoom"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
